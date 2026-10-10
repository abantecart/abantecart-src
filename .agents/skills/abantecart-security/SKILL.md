---
name: abantecart-security
description: AbanteCart input validation, output escaping, and CSRF protection for admin and storefront forms. Use when building or reviewing forms, controllers that accept POST, templates that print request data, or any user input.
---

# Input validation, escaping, CSRF

## Request input

`ARequest` (`core/lib/request.php`) HTML-escapes **all** GET/POST/COOKIE/FILES/SERVER values in the constructor via `htmlspecialchars(..., ENT_COMPAT, 'UTF-8')`.

```php
$this->request->get['product_id'];
$this->request->post['name'];
$this->request->get_or_post('store_id');   // GET wins
$this->request->post_or_get('name');       // POST wins
```

`rt` values that contain characters other than `A-Za-z0-9_/` are rejected with HTTP 403.

Still treat data as untrusted:

```php
$id = (int)$this->request->get['product_id'];
$ids = filterIntegerIdList($this->request->post['selected']);
$email = $this->request->post['email'];
if (!preg_match(EMAIL_REGEX_PATTERN, $email)) { /* error */ }
```

Passwords: `passwordHash()`, never a custom hash.

There is no HTMLPurifier. Rich HTML from WYSIWYG is stored escaped and decoded when rendering trusted catalog HTML:

```php
html_entity_decode($this->config->get('config_description_'.$language_id), ENT_QUOTES, 'UTF-8');
```

Do **not** `html_entity_decode` untrusted customer input (reviews, addresses, form fields) unless you have sanitized it.

## SQL

Use `$this->db->escape()` for strings and `(int)` for ids. Table names via `$this->db->table()`. See [abantecart-database](../abantecart-database/SKILL.md).

## Output

Helpers in `core/helper/utils.php`:

| Helper | Use |
| --- | --- |
| `html2view($html)` / `echo_html2view($html)` | `htmlspecialchars` ENT_QUOTES UTF-8 for templates |
| `js_encode($value)` / `js_echo($value)` | JSON for inline JS (`JSON_HEX_TAG` etc.) |

```php
<?php echo_html2view($customer_name); ?>
<script>var cfg = <?php js_echo($payload); ?>;</script>
```

ARequest already escaped POST/GET, so many core templates echo assigned values directly. New code should still `echo_html2view` for anything that did not come through ARequest (files, APIs, decoded HTML, DB that stored raw values).

## CSRF

`CSRFToken` (`core/lib/csrf_token.php`) is `$this->csrftoken`. Tokens are one-time, stored in `session['csrftoken'][$instance]`.

### Emit token (forms)

`$this->html->buildElement()` / `FormHtmlElement` with `'csrf' => true` injects hidden `csrfinstance` + `csrftoken` (`form/form_csrf.tpl`).

```php
$form = $this->html->buildElement([
    'type'   => 'form',
    'name'   => 'AccountFrm',
    'action' => $this->html->getSecureURL('account/edit'),
    'csrf'   => true,
]);
```

AJAX/JSON endpoints can mint a fresh pair:

```php
$output['csrfinstance'] = $this->csrftoken->setInstance();
$output['csrftoken'] = $this->csrftoken->setToken();
```

### Validate (controllers)

```php
if ($this->request->is_POST()) {
    if (!$this->csrftoken->isTokenValid()) {
        $this->error['warning'] = $this->language->get('error_unknown');
        // do not persist data
    }
}
```

`isTokenValid()` reads `csrfinstance` + `csrftoken` from GET/POST and **unsets** the session slot (single use). After a successful POST, re-render forms so a new token is issued.

Required for login, register, account edit, password, checkout pay, payment confirmations, and any state-changing **storefront** form. **Admin** mainly authenticates via session `token` on `$this->html->getSecureURL()`; add CSRF on new admin POST forms that are not behind that token/listing-grid flow.

Strip `csrftoken` / `csrfinstance` from `$post` before saving models.

## Validation pattern

```php
protected function validate(array $post): bool
{
    $this->extensions->hk_ValidateData($this, [__FUNCTION__]);
    if (!$this->csrftoken->isTokenValid()) {
        $this->error['warning'] = $this->language->get('error_unknown');
    }
    if (mb_strlen($post['firstname']) < 1) {
        $this->error['firstname'] = $this->language->get('error_firstname');
    }
    $this->extensions->hk_ValidateData($this);
    return !$this->error;
}
```

Call `hk_ValidateData` so extensions can append `$this->error`.

Captcha: `CaptchaHtmlElement` / ReCaptcha (`AForm` + `ReCaptcha\ReCaptcha`) on public forms when configured.

## Session

`session.cookie_httponly` is on. Admin and storefront use different `SESSION_ID` (`AC_CP_*` vs `AC_SF_*`). Do not mix admin auth into storefront session except via `startStorefrontSession()` after admin login for maintenance preview.

## Checklist for new forms

- [ ] `'csrf' => true` on the form element
- [ ] `isTokenValid()` before save
- [ ] Cast ids; `escape()` strings in SQL
- [ ] `$this->error[]` + language keys, not raw exception text to the browser
- [ ] `echo_html2view` / `js_echo` in templates
- [ ] No `html_entity_decode` on customer-supplied fields
- [ ] Admin controllers abort if `!IS_ADMIN`; mutating admin URLs include session `token`
- [ ] Passwords via `passwordHash()`
