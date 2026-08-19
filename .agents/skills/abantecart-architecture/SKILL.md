---
name: abantecart-architecture
description: AbanteCart architecture, bootstrap, Registry, routing (`rt=`), page vs response controllers, dispatcher, and request lifecycle. Use when tracing a request, adding a route, writing AJAX/JSON/jqGrid responses, working with pages/responses/API/tasks, or explaining how admin vs storefront loads.
---

# AbanteCart architecture and request lifecycle

## Layout

```
public_html/
  index.php              # sole HTTP front controller (admin via ?s=ADMIN_PATH)
  admin/index.php        # redirects to ../index.php
  storefront/index.php   # redirects to ../index.php
  task.php               # HTTP task runner (requires task_api_key)
  task_cli.php           # CLI: php task_cli.php run [--task_id=N] [--step_id=N]
  install/index.php      # web installer; install/cli_install.php for CLI
  core/init.php          # bootstrap
  core/engine/           # router, dispatcher, controller, view, model, extensions, hook, language, form
  core/lib/              # AConfig, ADB, ACache, ATaskManager, CSRFToken, ...
  core/database/         # AMySQLi / APDOMySQL drivers
  core/cache/            # cache drivers (file, redis, memcached, apcu, ...)
  admin/                 # admin MVC (controller, model, view, language)
  storefront/            # storefront MVC
  extensions/            # one directory per extension
  system/config.php      # DB_*, CACHE_DRIVER, ADMIN_PATH, UNIQUE_ID (not in git)
  resources/             # resource library files
  image/                 # generated thumbs + static images
  download/              # digital downloads
  install/               # installer + abantecart_database_upgrade.sql
```

Do not edit `core/` or core admin/storefront files to customize. Use an extension.

## Request lifecycle

1. `public_html/index.php` defines `DIR_ROOT`, loads `system/config.php`. If `DB_DATABASE` is missing, redirects to `install/`.
2. `core/init.php` sets `ROUTE` from `$_GET['rt']` / `$_POST['rt']` (default `index/home`).
3. Section detection:
   - Admin if `s` equals `ADMIN_PATH` → `IS_ADMIN=true`, `DIR_APP_SECTION=admin/`
   - Else storefront → `IS_ADMIN=false`, `DIR_APP_SECTION=storefront/`
   - `rt` starting with `a/` → `IS_API=true`
4. Registry is created. Core services are registered (load, request, response, html, db, cache, config, session, csrftoken, language, extensions, layout, …).
5. `$hook->hk_IndexProcess()` then `ARouter::processRoute(ROUTE)`.
6. `$registry->get('response')->output()`.
7. `$hook->hk_IndexEnd()`.

Storefront-only after init: `ACustomer`, `ATax`, `AWeight`, `ALength`, `ACart`, `AShoppingData`.
Admin-only: `AUser`, `AExtensionManager`, plus admin manager libs.

## Registry

Singleton `Registry` (`core/engine/registry.php`). Controllers and models expose it via `__get`:

```php
$this->db; $this->config; $this->cache; $this->language;
$this->load; $this->html; $this->request; $this->response;
$this->session; $this->extensions; $this->document; $this->layout;
```

Do not `new` these services. Read/write extra keys with `$this->registry->get/set`.

## Routing (`rt`)

`ARouter` (`core/engine/router.php`) classifies the request:

| Prefix | Type | Controller root |
| --- | --- | --- |
| none / `p/` | page | `controller/pages/` |
| `r/` | response (AJAX/JSON/HTML fragment) | `controller/responses/` |
| `a/` | REST API | `controller/api/` |
| `task/` | background task | `controller/task/` (via `ATypeTask`) |

Implicit lookup order if no prefix: pages → responses → api → task.

Examples:

- Storefront home: `index.php?rt=index/home`
- Admin products: `index.php?s=ADMIN_PATH&rt=catalog/product`
- AJAX grid: `rt=listing_grid/product` (responses)
- API: `rt=a/product/product`

SEO: `.htaccess` sets `_route_`; storefront predispatches `common/seo_url` (`ControllerCommonSeoUrl`), which looks up `url_aliases` and `$this->router->resetController($rt)`.

### Predispatch

| Type | Storefront | Admin |
| --- | --- | --- |
| page | `common/maintenance`, `common/seo_url` | `common/home/login`, `common/ant`, `common/home/permission` |
| response | `common/maintenance/response` | `responses/common/access/login`, `.../permission` |
| api | `api/common/preflight`, `api/common/access` | + login/permission |
| task | blocked (404) | access login/permission |

## Dispatcher

`ADispatcher` (`core/engine/dispatcher.php`) maps `rt` to file/class/method:

- File: `{admin|storefront}/controller/{pages|responses|api|blocks}/path.php`
- Class: `Controller` + alphanumeric path (`pages/catalog/product` → `ControllerPagesCatalogProduct`)
- Method: last path segment if not a file; default `main`

**Core controller files win.** If a core controller exists, an extension cannot replace it by shipping the same path. Extend via hooks. Extension controllers only add **new** routes.

Dispatcher also runs optional `.pre` / `.post` controllers (`POSTFIX_PRE` / `POSTFIX_POST`).

`APage::build()` runs predispatches, `ALayout::buildPageData()`, then dispatches **`common/page`** as the root shell (`ControllerCommonPage` → head, columns, `common/page.tpl`). The requested page RT is a child (`content`).

Only **page** and **response** controllers may be the first parent loaded from the browser. Blocks, `common/head`, and other controllers are children only — they cannot be requested as `rt=` (security). See [Architecture Overview](https://abantecart.atlassian.net/wiki/spaces/DOC/pages/655439/Architecture+Overview).

## Response controllers

A **response controller** is a main parent with **no layout**. It returns data (JSON, HTML fragment, XML, file download, embed JS) instead of a full page. Engine: `ATypeResponse` (`core/engine/response.php`).

### How a response request runs

1. Router sets type `response` if `rt` starts with `r/`, or if no `pages/` controller matched and a `responses/` file exists.
2. Predispatches run (admin: `responses/common/access/login` + `permission` — require session `token`).
3. `ATypeResponse::build($rt)` strips a leading `responses/`, then dispatches `responses/{rt}` with `instance_id = 0`.
4. No `ALayout`, no `common/page`. Output is whatever the controller puts on `$this->response`.
5. Missing RT → `error/ajaxerror/not_found`.

URLs (admin HTML helpers omit the `pages/` prefix; `r/` is optional when the pages lookup fails):

```
index.php?s=ADMIN_PATH&rt=listing_grid/stock_status&token=...
index.php?s=ADMIN_PATH&rt=r/common/zone&country_id=223&token=...
index.php?rt=r/checkout/cart
```

File / class:

| File | Class | RT |
| --- | --- | --- |
| `admin/controller/responses/listing_grid/stock_status.php` | `ControllerResponsesListingGridStockStatus` | `listing_grid/stock_status` |
| `admin/controller/responses/common/zone.php` | `ControllerResponsesCommonZone` | `common/zone` |
| `storefront/controller/responses/checkout/cart.php` | `ControllerResponsesCheckoutCart` | `checkout/cart` (or `r/checkout/cart`) |

### Admin `controller/responses/`

Typical folders:

| Folder | Role |
| --- | --- |
| `listing_grid/` | jqGrid JSON: list (`main`), bulk `update`, inline `update_field` |
| `catalog/`, `sale/`, `localisation/`, `design/`, `tool/` | HTML fragments / quick forms / previews |
| `common/` | shared AJAX: zones, captcha, resource library, viewport, access |
| `error/` | `ajaxerror` login / permission / not_found JSON |

Admin list pages are a **pair**: the **page** controller paints jqGrid and points `url` / `editurl` at a **response** controller.

```php
// pages/localisation/stock_status.php — UI shell
$grid_settings = [
    'table_id'     => 'stock_grid',
    'url'          => $this->html->getSecureURL('listing_grid/stock_status'),
    'editurl'      => $this->html->getSecureURL('listing_grid/stock_status/update'),
    'update_field' => $this->html->getSecureURL('listing_grid/stock_status/update_field'),
];

// responses/listing_grid/stock_status.php — JSON
$this->load->library('json');
$this->response->setOutput(AJson::encode($this->data['response']));
```

jqGrid payload shape: `{ page, total, records, rows: [ { id, cell: [...] } ] }`. Check `$this->user->canModify('listing_grid/...')` before writes; on failure use `AError::toJSONResponse(...)`.

### Output patterns

```php
// JSON
$this->response->addJSONHeader();
$this->response->setOutput(AJson::encode($json));

// HTML fragment (quick form, modal, options list)
$this->view->batchAssign($this->data);
$this->processTemplate('responses/common/zone.tpl'); // or setOutput($html)

// Redirect the response pipeline to another RT (predispatch login does this)
return $this->dispatch('responses/error/ajaxerror/login');
```

Children still work (`addChild`, `dispatch` + `dispatchGetOutput()`), but they render into the fragment — they do not pull storefront/admin page layout.

Storefront responses: AJAX cart, checkout steps, embed widgets, reviews, zone lists. Payment extensions typically add `storefront/controller/responses/extension/{id}.php`.

### Adding a response controller (extension)

1. `extensions/<id>/admin/controller/responses/<group>/<name>.php` — class `ControllerResponses{Group}{Name}`
2. Register the RT (without `responses/` prefix) in `main.php` `$controllers['admin']`
3. Link from a page with `$this->html->getSecureURL('<group>/<name>')` (include `token` via `getSecureURL`)
4. Do not put listing JSON in a page controller

## URL helpers

```php
$this->html->getSecureURL('catalog/product/update', '&product_id=1');  // admin / HTTPS
$this->html->getSEOURL('product/product', '&product_id=1');            // storefront SEO
$this->html->getNonSecureURL('product/special');
```

## Constants (set in init)

| Constant | Meaning |
| --- | --- |
| `DIR_ROOT`, `DIR_CORE`, `DIR_SYSTEM`, `DIR_CACHE`, `DIR_LOGS` | paths |
| `DIR_EXT`, `DIR_EXTENSIONS` | extensions |
| `DIR_RESOURCE` | resource library files |
| `DIR_APP_SECTION`, `DIR_LANGUAGE`, `DIR_TEMPLATE` | current section |
| `IS_ADMIN`, `IS_API`, `HTTPS`, `VERSION` | flags / version |
| `SESSION_ID` | `AC_CP_*` admin / `AC_SF_*` storefront |
| `POSTFIX_OVERRIDE`, `POSTFIX_PRE`, `POSTFIX_POST` | template override suffixes |

`system/config.php` defines `DB_*`, `DB_PREFIX`, `ADMIN_PATH`, `CACHE_DRIVER`, `UNIQUE_ID`.

## Related

- MVC details: [abantecart-mvc](../abantecart-mvc/SKILL.md)
- Hooks and extensions: [abantecart-extensions](../abantecart-extensions/SKILL.md)
