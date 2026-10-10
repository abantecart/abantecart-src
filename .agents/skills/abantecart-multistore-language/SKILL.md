---
name: abantecart-multistore-language
description: AbanteCart multi-store (store_id, *_to_stores, store settings) and multi-language (language_id, *_descriptions, content language). Use when writing catalog queries, saving translations, scoping settings, or working with more than one store or language.
---

# Multi-store and multi-language

## Multi-store

Table `stores`. Default store is `store_id = 0` (and/or the row whose URL matches `config_url`). Additional stores have their own URL and settings.

`AConfig` resolves the storefront store from `HTTP_HOST` + path against `settings` keys `config_url` / `config_ssl_url`. Result: `$this->config->get('config_store_id')`.

Admin does **not** switch the whole app to that store. Editors pick a store; session key `current_store_id` / request `store_id` is the store being edited. `$this->config->get('current_store_id')` is set in init for admin.

### What is store-scoped

| Data | How |
| --- | --- |
| Settings | `settings.store_id` |
| Products, categories, manufacturers, collections, contents | `*_to_stores` junction tables |
| Customers, orders | `customers.store_id`, order/cart keyed by store |
| Email templates | `(text_id, language_id, store_id)` |
| Extension settings | written per store on install (`editSetting` loop) |

Layouts (`layouts.template_id`) are **not** store-scoped; the store picks `config_storefront_template`. Cache keys still include `store_id`.

### What is global

Resource library files, tax/length/weight classes, language **definition** XML (UI strings). Product **existence** is global; **visibility** is per store via `*_to_stores`. Descriptions are language-scoped, not store-scoped.

### Queries

Storefront:

```php
$store_id = (int)$this->config->get('config_store_id');
$sql .= " INNER JOIN " . $this->db->table('products_to_stores') . " p2s
          ON (p.product_id = p2s.product_id
          AND p2s.store_id = '" . $store_id . "') ";
```

Admin listing of a store's catalog: filter with `current_store_id`, not `config_store_id`.

Saving settings:

```php
$this->model_setting_setting->editSetting('details', $data, (int)$store_id);
```

Payment/shipping extensions that read their own keys must re-load settings for the current store (see `ExtensionPaypalCommerce::applyStorePaypalSettings`).

## Multi-language

Table `languages` (`language_id`, `code`, `filename`, `status`, `sort_order`).

UI strings: XML → `language_definitions` (`section` 0 storefront / 1 admin, `block`, `language_key`, `language_value`).

Catalog/content text: `*_descriptions` tables (`product_descriptions`, `category_descriptions`, `manufacturer_descriptions`, `content_descriptions`, `resource_descriptions`, …) keyed by entity id + `language_id`.

### Which language id

| Context | API |
| --- | --- |
| Storefront UI + catalog | `$this->language->getLanguageID()` (session `language`, else store default / browser) |
| Admin UI chrome | Admin `$this->language` (admin section) |
| Admin **editing** product names, etc. | `$this->language->getContentLanguageID()` (session `content_language_id`) |

Never save product descriptions under the admin UI language. Always `getContentLanguageID()`.

Storefront language selection (`ALanguage::setCurrentLanguage()`): request `language` → session → cookie → browser detect → `config_storefront_language`. Sets `storefront_language_id`.

Per-language settings exist for store title/meta: `config_title_{language_id}`, `config_description_{language_id}`, …

### Queries

```php
$language_id = (int)$this->language->getLanguageID();
$sql .= " LEFT JOIN " . $this->db->table('product_descriptions') . " pd
          ON (p.product_id = pd.product_id
          AND pd.language_id = '" . $language_id . "') ";
```

When copying a product or installing sample data, insert a descriptions row for **each** active language (or at least the default + content language).

### Adding translations

1. UI strings: add keys to `language/<code>/<block>.xml`. Load with `$this->loadLanguage('group/file')`.
2. Content (admin): `$this->language->replaceDescriptions('product_descriptions', ['product_id' => $id], [$language_id => ['name' => '...', 'description' => '...']]);` also `addDescriptions`, `updateDescriptions`, `saveTags`.
3. Admin UI: Localisation → Language Definitions (`ALanguageManager`).
4. Language extensions (`type=language`) ship full `admin/language/<code>/` and `storefront/language/<code>/` trees.

`$this->language->get('key')` returns the key itself (and logs) if missing, unless silent.

## Combined pitfall

A correct storefront product query filters **both** `p2s.store_id` and `pd.language_id`. Omitting either leaks the wrong catalog or empty names.

Cache keys must include both ids: `product.{store_id}.{language_id}....`

## Related

- SQL helpers: [abantecart-database](../abantecart-database/SKILL.md)
- Settings load order: [abantecart-config-cache-tasks](../abantecart-config-cache-tasks/SKILL.md)
