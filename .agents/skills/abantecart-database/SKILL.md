---
name: abantecart-database
description: AbanteCart database access via ADB, dynamic DB_PREFIX, schema changes, install/upgrade SQL, and query conventions. Use when writing models, SQL, migrations, extension install.sql/upgrade.sql, or anything that touches tables.
---

# Database, DB_PREFIX, schema, upgrades

## ADB

`$this->db` is `ADB` (`core/lib/db.php`). Driver comes from `DB_DRIVER` in `system/config.php` (`amysqli` or `apdomysql`).

```php
$query = $this->db->query($sql);          // throws unless $noexcept
$query->row;                              // first row or []
$query->rows;                             // all rows
$query->num_rows;

$this->db->escape($value);                // strings
$this->db->escape($value, true);          // extra special-char handling (LIKE)
$this->db->stringOrNull($value);          // quoted string or NULL
$this->db->intOrNull($value);             // int or NULL
$this->db->countAffected();
$this->db->getLastId();
$this->db->getSqlCalcTotalRows();         // driver helper
$this->db->getTotalNumRows();
```

Cast IDs with `(int)`. Never concatenate raw request values.

`query()` is hooked (`hk_query`). The real driver call is `_query()`.

## Dynamic DB_PREFIX

`DB_PREFIX` is defined in `system/config.php` and can be any string (default often `ac_`). **Never** hardcode `ac_` in PHP.

```php
// correct
FROM " . $this->db->table('products') . " p

// wrong
FROM ac_products p
FROM " . DB_PREFIX . "products p   // works but skip; table() also handles encryption postfix
```

`$this->db->table('products')` returns `DB_PREFIX + name` and may append an encryption postfix via `ADataEncryption`.

## SQL files (install/upgrade)

`ADB::performSql($file)` reads the file and **rewrites** `` `ac_ `` → `` `{DB_PREFIX} ``.

```sql
CREATE TABLE IF NOT EXISTS `ac_banner_manager` (
  `banner_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

If you write `CREATE TABLE banners` with no `ac_` prefix, it will **not** get prefixed. Always use `` `ac_tablename` `` in extension SQL.

## Schema conventions

- InnoDB, `utf8mb4` / `utf8mb4_unicode_ci`
- Integer PKs, `date_added` / `date_modified` timestamps
- Translatable text lives in `*_descriptions` tables with `language_id` (see [abantecart-multistore-language](../abantecart-multistore-language/SKILL.md))
- Store assignment is `*_to_stores` (`product_id, store_id`), not a `store_id` column on the main product row
- Settings: table `settings` (`group`, `key`, `value`, `store_id`)

Core schema:

- Full DDL + seed: `public_html/install/abantecart_database.sql`
- Sample catalog: `install/abantecart_sample_data.sql`
- Incremental deltas: `install/abantecart_database_upgrade.sql`

`core/database/` is **drivers only**, not migrations. Core upgrades run through `APackageManager::upgradeCore()` (`<upgrade><sql>` + `<trigger>` in the package `config.xml`, then `updateCoreVersion()` writes `core/version.php`). Setting `core_version` must match `VERSION` or startup exits.

`ADB` has **no** `begin`/`commit`/`rollback`. Use single statements.

## Extension schema

| File | When |
| --- | --- |
| `install.sql` | First install (`config.xml` `<install><sql>`) |
| `upgrade.sql` | Package upgrade (`<upgrade><sql>`) |
| `uninstall.sql` | Uninstall |

Bump `<version>` in `config.xml` when shipping `upgrade.sql`. Upgrades do **not** re-run `install.sql`.

PHP install/upgrade scripts use `$this->db->query()` / `table()` like models. `$this` in those triggers is the extension manager context.

## Query patterns

```php
$store_id = (int)$this->config->get('config_store_id');
$language_id = (int)$this->language->getLanguageID(); // storefront
// admin content language:
$language_id = (int)$this->language->getContentLanguageID();

$sql = "SELECT p.product_id, pd.name
        FROM " . $this->db->table('products') . " p
        INNER JOIN " . $this->db->table('products_to_stores') . " p2s
            ON (p.product_id = p2s.product_id AND p2s.store_id = '" . $store_id . "')
        INNER JOIN " . $this->db->table('product_descriptions') . " pd
            ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')
        WHERE p.product_id = " . (int)$product_id . "
            AND p.status = 1";
$result = $this->db->query($sql);
```

Storefront catalog queries must include `products_to_stores` (or the equivalent `*_to_stores`) and `language_id`.

## Cache after writes

```php
$this->cache->remove('product');
$this->cache->remove(['product', 'category', 'collection']);
```

Group name is the key prefix before the first dot. See [abantecart-config-cache-tasks](../abantecart-config-cache-tasks/SKILL.md).

## Pitfalls

- Forgetting `$this->db->table()` → queries miss the merchant's prefix
- SQL file without `` `ac_ `` prefix → tables created unprefixed
- Filtering by `language_id` but not `store_id` (or the reverse)
- Using storefront `getLanguageID()` in admin content saves — use `getContentLanguageID()`
- Not invalidating cache after INSERT/UPDATE/DELETE
