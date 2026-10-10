---
name: abantecart-mvc
description: AbanteCart admin and storefront controllers, models, views (.tpl), layouts, and language definitions. Use when adding or editing a page, block, model, template, or language XML in admin, storefront, or an extension.
---

# Controllers, models, views, languages

## Naming

| Piece | Path | Class |
| --- | --- | --- |
| Admin page | `admin/controller/pages/catalog/product.php` | `ControllerPagesCatalogProduct` |
| Admin response | `admin/controller/responses/listing_grid/product.php` | `ControllerResponsesListingGridProduct` |
| Storefront page | `storefront/controller/pages/index/home.php` | `ControllerPagesIndexHome` |
| Storefront block | `storefront/controller/blocks/cart.php` | `ControllerBlocksCart` |
| Admin model | `admin/model/catalog/product.php` | `ModelCatalogProduct` |
| Storefront model | `storefront/model/catalog/product.php` | `ModelCatalogProduct` |
| Language | `{admin\|storefront}/language/english/catalog/product.xml` | keys via `$this->language->get()` |
| Template | `{admin\|storefront}/view/{template}/template/pages/catalog/product_list.tpl` | |

`rt=catalog/product/update` → class `ControllerPagesCatalogProduct`, method `update`.

Controllers extend `AController`. Models extend `Model`. Both get Registry via `__get`.

## Controller pattern

Keep controllers thin; put SQL and business logic in models. Do not `echo` from controllers. Always wrap with hooks. No inline UI strings — language XML path matches the controller. After `processTemplate()`, add `/** @see public_html/.../file.tpl */`.

```php
class ControllerPagesIndexHome extends AController
{
    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->view->assign('heading_title', $this->language->get('heading_title'));

        $this->processTemplate('pages/index/home.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
```

`$this->data` is public. Extensions mutate it from a hook file via `$this->baseObject->data` (see [abantecart-extensions](../abantecart-extensions/SKILL.md)). If this method `batchAssign`s **before** `hk_UpdateData`, the hook must also `$that->view->assign(...)`.

`AController` constructor already:

- `$this->loadLanguage($this->controller, 'silent')` (strips `pages/` / `responses/`)
- `$this->loadModel($this->controller, 'silent')`
- Sets template from layout block if a layout exists

Extra loads:

```php
$this->loadLanguage('catalog/product');
$this->loadModel('catalog/product');
// property becomes $this->model_catalog_product
$product = $this->model_catalog_product->getProduct($id);
```

### Admin pages

Typical methods: `main()` (list + jqGrid settings), `insert()`, `update()`, `_getForm()`, `_validateForm()`. Grid JSON lives in `responses/listing_grid/*` (`ControllerResponsesListingGrid{Name}`). Forms use `AForm`:

```php
$form = new AForm('ST'); // insert: Standard. Update: 'HS' (highlight+save)
$form->setForm(['form_name' => 'editFrm', 'update' => $ajaxUrl]);
$this->data['form']['form_open'] = $form->getFieldHtml(['type' => 'form', ...]);
$this->data['form']['fields']['name'] = $form->getFieldHtml([
    'type' => 'input', 'name' => 'name', 'value' => ..., 'required' => true,
]);
```

Modes: `ST` standard, `HS` highlight+save, `HT` highlight. Or `$this->html->buildElement()` / `buildInput()`.

```php
$this->document->initBreadcrumb([...]);
$this->document->addBreadcrumb([...]);
$this->view->batchAssign($this->data);
$this->processTemplate('pages/catalog/product_form.tpl');
```

Protect admin files:

```php
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
```

### Storefront pages

Same hook + `processTemplate()`. Children/blocks come from layout. Hardcoded children:

```php
$this->addChild('blocks/listing_block', 'listing_block', 'blocks/listing_block.tpl');
```

Embed mode swaps `pages/*.tpl` for `embed/*.tpl` inside `processTemplate()`.

### Responses

Main parent **without layout**. JSON, HTML fragments, files. Admin jqGrid lives in `responses/listing_grid/`. Full pipeline: [abantecart-architecture](../abantecart-architecture/SKILL.md#response-controllers).

## Models

```php
class ModelCatalogProduct extends Model
{
    public function getProduct($product_id)
    {
        $sql = "SELECT * FROM " . $this->db->table('products') . "
                WHERE product_id = " . (int)$product_id;
        return $this->db->query($sql)->row;
    }
}
```

Admin and storefront models are **separate files** (same class name is OK because only one section loads). Do not call admin models from storefront. From admin, load a storefront model with `$this->load->model('catalog/product', 'storefront')`. Extensions can override models via `isExtensionResource('M', ...)`.

## Views

`AView`: `assign()`, `batchAssign()`, `setTemplate()`, `fetch()`.

Templates are PHP `.tpl` files (`extract($this->data)`). HTML attributes: `echo_html2view()` / `html2view()`. JS values: `js_echo()`. Do not put JavaScript in storefront templates except `common/*.tpl`; define vars in `common/head.tpl`.

Hook injection in TPL: `echo $this->getHookVar('my_point');` filled by `$that->view->addHookVar('my_point', $html)`.

Language keys loaded by the controller are assigned to the view automatically (`loadLanguage()` → `batchAssign`).

### Template override order (storefront)

1. Extension template for the **active** storefront template
2. Extension template for `default`
3. Core `{template}/` then core `default/`
4. `.pre.tpl` / `.post.tpl` wrappers (`POSTFIX_PRE` / `POSTFIX_POST`)
5. `.override.tpl` (`POSTFIX_OVERRIDE`)

Extensions must list templates in `main.php` `$templates`.

## Language definitions

XML files (`admin/language/english/catalog/product.xml`):

```xml
<?xml version="1.0"?>
<definitions>
  <definition>
    <key>heading_title</key>
    <value><![CDATA[Products]]></value>
  </definition>
</definitions>
```

- Directory name = language (english, spanish, …). Main file is `{language}.xml`.
- Load order for a block: DB `language_definitions` → if empty, XML → `_save_to_db`. Admin UI edits DB without changing files.
- `$this->language->get('heading_title')` or `get($key, 'blocks/cart')` for an explicit block.
- `$this->language->getAndReplace('text_hello', '', ['name' => $name])` for placeholders.
- Extension languages: `extensions/<id>/{admin|storefront}/language/<lang>/<id>/<file>.xml`; prefix keys with the extension id. List files in `main.php` `$languages`.

Admin **content** language (product names, etc.) is `getContentLanguageID()`, not the admin UI language. See [abantecart-multistore-language](../abantecart-multistore-language/SKILL.md).

## Layouts and blocks

`ALayout` maps a page RT (plus optional `key_param` like `product_id`) to block instances. Runtime tables: `pages`, `layouts`, `blocks`, `block_layouts`. Install/import XML: `storefront/view/default/layout.xml` (themes ship their own, e.g. `extensions/novator/layout.xml`). Admin editor: `ALayoutManager`. Storefront `processTemplate()` with no argument uses the layout template.

## Adding a new admin page (extension)

1. `extensions/<id>/admin/controller/pages/<group>/<name>.php`
2. Matching model, `view/default/template/pages/<group>/<name>.tpl`, language XML
3. Register arrays in `main.php` (`$controllers`, `$models`, `$languages`, `$templates`)
4. Add menu in `install.php` via `AMenu`
5. Link with `$this->html->getSecureURL('<group>/<name>')`

## Adding a storefront page (extension)

Same, under `storefront/`. Create layout in `install.php` if it is a full page. Route: `rt=<group>/<name>`.

## Related

- Hooks: [abantecart-extensions](../abantecart-extensions/SKILL.md)
- Forms/CSRF: [abantecart-security](../abantecart-security/SKILL.md)
