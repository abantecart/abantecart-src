---
name: abantecart-extensions
description: AbanteCart extension construction (config.xml, main.php, hook class), install/uninstall/upgrade, and hook method naming. Use when creating or modifying an extension, writing core/template hooks, or using novator/forms_manager/banner_manager as a pattern.
---

# Extensions and hooks

Customize AbanteCart **only** with extensions under `public_html/extensions/<id>/`. Do not edit core.

Official guide: [Extension’s Developer Guide](https://abantecart.atlassian.net/wiki/spaces/DOC/pages/17727622/Extension+s+Developer+Guide) ([Hooks](https://abantecart.atlassian.net/wiki/spaces/DOC/pages/17793156/Hooks), [Process overview](https://abantecart.atlassian.net/wiki/spaces/DOC/pages/17694951/Extension+Process+Overview)).

Worked examples: **`novator`** (template + hook file), `forms_manager`, `banner_manager`, `paypal_commerce`.

## How an extension is constructed

An extension is one directory. Enable it in admin; `init.php` then loads every **enabled** extension’s `main.php` and instantiates its hook class.

| Piece | Required | Role |
| --- | --- | --- |
| `config.xml` | yes | id, version, type, cartversions, settings, install/uninstall |
| `main.php` | yes | `require` hook file; register `$controllers`, `$models`, `$languages`, `$templates` |
| `core/*_hook.php` | for hooks | `class Extension{Id} extends Extension` — core hooks |
| `install.php` / `.sql` | optional | menus, layouts, resources, schema |
| `admin/` `storefront/` | as needed | extra MVC + template overrides |
| `layout.xml` | templates | imported by `ALayoutManager` in `install.php` |

`<id>` = folder name = `<id>` in XML. Hook class: `Extension` + alphanumeric id (`novator` → `ExtensionNovator`, `forms_manager` → `ExtensionFormsManager`).

### Worked example: `novator` (type `template`)

```
extensions/novator/
  config.xml                 # type=template, <id>novator</id>
  main.php                   # require hook file + register blocks/templates
  install.php                # load layout.xml, logo resource, appearance settings
  uninstall.php / uninstall.sql
  layout.xml                 # storefront placeholders/blocks
  core/novator_hook.php      # ExtensionNovator
  core/helper.php            # template helper functions (menus, ratings)
  storefront/controller/blocks/{mega_menu,category_slides}.php
  storefront/view/novator/   # full theme (tpl, css, js)
  storefront/language/english/novator/novator.xml
  admin/language/english/novator/novator.xml
```

`config.xml` is `type=template`. `additional_settings` points admins at Appearance. `install.php` runs `ALayoutManager('default')->loadXml(['file' => .../layout.xml])`.

`main.php` **must** load the hook class before the engine looks for `ExtensionNovator`:

```php
require_once('core/novator_hook.php');

$controllers = [
    'storefront' => ['blocks/category_slides', 'blocks/mega_menu'],
    'admin'      => [],
];
$templates = [
    'storefront' => [
        'blocks/product_cell_grid.tpl',
        'blocks/mega_menu_header.tpl',
        // ...
    ],
    'admin' => [],
];
$languages = [
    'storefront' => [],
    'admin'      => ['english/novator/novator'],
];
$models = ['storefront' => [], 'admin' => []];
```

Unlisted controllers/templates are invisible. Theme files under `storefront/view/novator/` override core `default` when `config_storefront_template = novator`.

## Hook file — how it is built and used

Two kinds ([glossary](https://abantecart.atlassian.net/wiki/spaces/DOC/pages/17727622/Extension+s+Developer+Guide)):

| Kind | Connects to | Typical API |
| --- | --- | --- |
| **Core hook** | controller/model/engine method | methods on `Extension{Id}` |
| **Template hook** | a `.tpl` placeholder | `$that->view->addHookVar('name', $html)` ↔ `<?php echo $this->getHookVar('name'); ?>` |

Hooks are **not** listed in `config.xml`. They are PHP methods. Loaded when `main.php` is included and `class_exists('Extension{Id}')`.

### Load path

1. Extension enabled (`novator_status = 1`).
2. `ExtensionsApi::loadEnabledExtensions()` includes `main.php`.
3. Instantiates `new ExtensionNovator` into `ExtensionCollection` (order = `<priority>`).
4. Core later calls `$this->extensions->hk_UpdateData($this, __FUNCTION__)` (or `$hook->hk_InitEnd()`).
5. `ExtensionsApi` maps that call onto methods on every loaded `Extension*` class.

### Method naming

Core call:

```php
$this->extensions->hk_InitData($this, __FUNCTION__);
$this->extensions->hk_UpdateData($this, __FUNCTION__);
$this->extensions->hk_ProcessData($this, $point_name, $data);
$this->extensions->hk_ValidateData($this, $args);
```

`hk` is stripped. The hooked object’s class name is prefixed. Four phases ([Hooks](https://abantecart.atlassian.net/wiki/spaces/DOC/pages/17793156/Hooks)):

| Phase | Method | When |
| --- | --- | --- |
| before | `before{Class}{Hook}` | mutate args |
| override | `override{Class}{Hook}` | replace the target; first match wins; return `false` to skip |
| on | `on{Class}{Hook}` | after successful run |
| after | `after{Class}{Hook}` | always after |

`{Class}` = PHP class of `$this` (`ControllerPagesCatalogCategory`, `AHook`, `ADB`, …). `{Hook}` for `hk_UpdateData` is `_UpdateData`.

```
$this->extensions->hk_UpdateData($this, __FUNCTION__);
  → onControllerPagesCatalogCategory_UpdateData   (if $this is that controller)

$hook->hk_InitEnd();   // AHook in init.php
  → afterAHook_InitEnd
```

Inside the method:

- `$this->baseObject` — the controller/model being hooked (must use **public** properties)
- `$this->baseObject_method` — method that fired the hook (`main`, `update`, …)
- `$that->config`, `$that->view`, `$that->language`, `$that->loadModel()` via the base object
- `$this->ExtensionsApi` — to fire further `hk*` calls

Prefer **`on*`**. Use **`override*`** only to skip core. Gate on `$that->config->get('{id}_status')` and, for themes, `config_storefront_template`.

### Updating `$this->data` from a hook

`AController::$data` is **public**. In `on*` / `after*` the hooked controller is `$this->baseObject`, so **`$that->data` is that controller’s `$this->data`**. Change it there instead of editing the core controller.

```php
public function onControllerPagesProductProduct_UpdateData()
{
    $that = $this->baseObject;
    $that->data['my_key'] = 'value';           // mutates the controller array
    $that->view->assign('my_key', 'value');    // also push to the view when needed
}
```

When which is enough:

| Core order | What the hook must do |
| --- | --- |
| `hk_UpdateData` then `$this->view->batchAssign($this->data)` | `$that->data['…'] = …` is enough |
| `batchAssign` / `processTemplate` then `hk_UpdateData` | `$that->data` is too late for the tpl — `$that->view->assign(…)` or `addHookVar` |

Read existing view vars with `$that->view->getData()`. Template-only HTML: `$that->view->addHookVar('point', $html)` — the `.tpl` must already `echo $this->getHookVar('point')`.

Flags: `$overloadHooks` (route unknown hooks through `__call`), `$hookAll` (every hook — `ExtensionPageBuilder`).

Other core points: `hk_query` (ADB), `hk_load` (language), `hk_confirm` / `hk_update` / `hk_create` (orders), `hk_IndexProcess` / `hk_IndexEnd` (`AHook`).

### `novator/core/novator_hook.php`

```php
require_once(__DIR__ . DS . 'helper.php');

class ExtensionNovator extends Extension
{
    public function afterAHook_InitEnd() { /* load novator language on storefront */ }

    public function onControllerPagesCatalogCategory_UpdateData() { /* admin form alert */ }

    public function onControllerCommonHeader_UpdateData() { /* mobile menu data */ }

    public function onControllerBlocksContent_UpdateData() { /* stash block data */ }

    public function onControllerBlocksCart_UpdateData() { /* stash cart data */ }
}
```

What each method does:

1. **`afterAHook_InitEnd`** — `$hook->hk_InitEnd()` at end of `init.php`. Loads `novator/novator` language on the storefront so theme strings exist before pages run.

2. **`onControllerPagesCatalogCategory_UpdateData`** — admin category **update**. If the active storefront template is `novator`, injects a notice via **template hook** `category_form_hook_before_resources`.

3. **`onControllerBlocksContent_UpdateData` / `onControllerBlocksCart_UpdateData`** — when those blocks finish, copy `$that->data` into Registry key `novator_scratch` (blocks cannot easily pass data to header otherwise).

4. **`onControllerCommonHeader_UpdateData`** — header `hk_UpdateData`. Re-dispatches `blocks/content`, `blocks/currency`, `blocks/language`, `blocks/cart`, reads `novator_scratch`, assigns `mobile_menu_*` onto the header view. Pattern: **hook + `ADispatcher` + view assign**, not a core file edit.

Always return early unless `config_storefront_template == 'novator'`.

```php
public function onControllerPagesCatalogCategory_UpdateData()
{
    $that = $this->baseObject;
    if ($that->config->get('config_storefront_template') != 'novator'
        || $this->baseObject_method != 'update') {
        return;
    }
    $that->loadLanguage('novator/novator');
    $that->view->addHookVar(
        'category_form_hook_before_resources',
        $that->language->get('novator_category_form_info_alert')
    );
}
```

`helper.php` is **not** a hook class. It holds render helpers the theme tpl/controllers call (`renderSFMenuNv`, …).

## config.xml

```xml
<extension>
  <id>novator</id>
  <version>1.4.5</version>
  <type>template</type>            <!-- payment|shipping|extensions|template|language|... -->
  <category>template</category>
  <cartversions><item>1.4.0</item></cartversions>
  <priority>0</priority>
  <settings>
    <item id="novator_status">
      <type>checkbox</type>
      <default_value>0</default_value>
    </item>
  </settings>
  <install><trigger>install.php</trigger></install>
  <uninstall><trigger>uninstall.php</trigger></uninstall>
</extension>
```

Status key is always `<id>_status`. Enable: `editSetting($id, [$id.'_status' => 1, 'store_id' => N])`. Type defaults merge from `core/extension/{payment|shipping|default}/config_{top,bottom}.xml`. Optional: `<dependencies>`, `<phpmodules>`, `<phpminversion>`, `<upgrade>`.

## Install / update lifecycle

`AExtensionManager::install()`:

1. Validate `cartversions` vs `VERSION`
2. Default settings (`<id>_status` starts `0`)
3. `install_upgrade_history`
4. Required `<dependencies>`
5. `<install><sql>` via `performSql()` (rewrites `` `ac_ `` → `DB_PREFIX`)
6. `include` `<install><trigger>`
7. Settings for **every store**

Uninstall: SQL/PHP, then delete settings/language defs. Blocked if dependants exist, or an **enabled payment** is still on.

Upgrade (`APackageManager`, `install_mode=upgrade`): `<upgrade><sql>` then `<trigger>`, then stored `version`. Deltas go in `upgrade.sql`, not a reinstall.

## What extensions can and cannot override

| Asset | Override core? |
| --- | --- |
| New controller RT | Yes (register in `main.php`) |
| Existing core controller file | **No** — use hooks |
| Templates listed in `$templates` | Yes |
| Models listed in `$models` | Yes, by RT under the extension |
| Language XML | Yes, merged |
| `.pre.tpl` / `.post.tpl` | Yes (wrap core tpl; all matching extensions render by priority) |

## Recommended development

- One concern per extension; set `<priority>` if hook order matters
- Prefix settings, tables, language keys, cache groups with the extension id
- Use `$this->db->table()` in PHP; `` `ac_ `` in SQL files
- After install: `$this->cache->remove(['settings','extensions','layout','storefront_menu'])`
- Do not ship credentials in `config.xml`

## Related

- MVC: [abantecart-mvc](../abantecart-mvc/SKILL.md)
- SQL/prefix: [abantecart-database](../abantecart-database/SKILL.md)
