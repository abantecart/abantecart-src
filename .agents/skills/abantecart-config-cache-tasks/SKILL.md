---
name: abantecart-config-cache-tasks
description: AbanteCart configuration and settings, ACache, extension hooks as events, and ATaskManager background tasks. Use when reading/writing settings, cache keys, scheduled jobs, task.php, or when the user mentions events, cache, config, or tasks.
---

# Config, settings, cache, events, tasks

## Configuration vs settings

`system/config.php` (not in git): `DB_*`, `DB_PREFIX`, `ADMIN_PATH`, `CACHE_DRIVER`, `UNIQUE_ID`. Loaded before Registry.

Runtime settings live in table `settings` and are loaded by `AConfig` (`core/lib/config.php`) into `$this->config`.

```php
$this->config->get('config_store_id');
$this->config->set('embed_mode', true);   // request-scoped only
$this->config->has('config_ssl');
```

Load order:

1. Cache key `settings` (store_id 0, excluding extension groups) or DB
2. Detect current store from request host/path vs `config_url` / `config_ssl_url`
3. Merge that store's settings; set `config_store_id`
4. Admin: `current_store_id` from request `store_id` / session; `reloadSettings()` for that store
5. Extension settings cache `settings.extension.{admin|store_id}` (`<ext>_status`, etc.)

Groups used by core: `details`, `general`, `checkout`, `appearance`, `mail`, `im`, `api`, `system`. Each extension uses its id as `group`.

### Writing settings (admin)

```php
/** @var ModelSettingSetting $mdl */
$mdl = $this->load->model('setting/setting');
$mdl->getSetting('appearance', $store_id);
$mdl->editSetting('appearance', $data, $store_id);
$mdl->deleteSetting('my_extension', $store_id);
```

Always pass `store_id`. `$this->config->set()` is **request-local** and does not persist — use `editSetting`. After edits, `ModelSettingSetting` clears `settings` (and often `extensions`, `stores`, menus). If you write `settings` rows yourself, `$this->cache->remove('settings')`. Toggling `config_cache_enable` may flush `*`.

## Cache (`ACache`)

`$this->cache` (`core/lib/cache.php`). Drivers in `core/cache/` (`file` default; `CACHE_DRIVER` can be redis/memcached/apcu/…). Storage: `system/cache/<group>/`.

```php
$data = $this->cache->pull($key);     // false on miss
if ($data === false) {
    $data = /* query */;
    $this->cache->push($key, $data);
}
$this->cache->remove('product');      // whole group
$this->cache->remove(['product','category']);
```

The **group** is the substring before the first `.` in the key (`product.123.1` → group `product`). `remove('product')` drops that group.

Include `store_id` and `language_id` in keys:

```php
$key = 'product.listing.' . $store_id . '.' . $language_id . '.' . md5($sql);
```

Invalidate on write. Common groups: `settings`, `extensions`, `product`, `category`, `manufacturer`, `collection`, `resources`, `layout`, `localization`, `storefront_menu`.

Cache is skipped when `config_cache_enable` is off, but still call `remove()` so production caches stay coherent.

## Events

AbanteCart has **no** application-level event dispatcher (Symfony EventDispatcher in vendor is for mailers only).

Use **extension hooks** as events:

```php
$this->extensions->hk_InitData($this, __FUNCTION__);
$this->extensions->hk_UpdateData($this, __FUNCTION__);
$this->extensions->hk_ProcessData($this, 'my_point', $payload);
$this->extensions->hk_ValidateData($this);
```

Listeners are methods on `Extension<Id>`: `on{Class}_{Hook}`, `before*`, `after*`, `override*`. See [abantecart-extensions](../abantecart-extensions/SKILL.md).

Admin inbox notifications: `AMessage` (`$this->messages`). Instant messaging: `AIM` / `AIMManager`.

When adding a new extension point in code you **do** maintain, call `hk_ProcessData` / `hk_InitData` so other extensions can subscribe. Do not invent a parallel event class.

## Tasks

`ATaskManager` (`core/lib/task_manager.php`) runs scheduled/background work. Tables: `tasks`, `task_details`, `task_steps`.

Statuses: `0` disabled, `1` ready, `2` running, `3` failed, `4` scheduled, `5` completed, `6` incomplete.

`starter`: `1` admin, `0` storefront, `2` both.

```php
$tm = new ATaskManager('ajax');
$task_id = $tm->addTask([
    'name'               => 'my_extension_job',
    'starter'            => 1,
    'status'             => ATaskManager::STATUS_READY,
    'run_interval'       => 0,      // seconds; 0 = run once when ready
    'max_execution_time' => 30,
    'settings'           => ['foo' => 'bar'],
]);
$tm->addStep([
    'task_id'            => $task_id,
    'sort_order'         => 1,
    'status'             => ATaskManager::STATUS_READY,
    'controller'         => 'task/my_extension/process',  // admin controller RT
    'max_execution_time' => 30,
    'settings'           => ['chunk' => 50],
]);
```

Step `controller` is dispatched as an admin controller. Implement `admin/controller/task/...`.

### Running tasks

HTTP: `public_html/task.php` (admin context, requires GET `task_api_key` matching setting `task_api_key`). Query: `mode=html|ajax|cli`, `task_id`, `step_id`.

CLI (preferred for cron):

```bash
php public_html/task_cli.php run
php public_html/task_cli.php run --task_id=123 --step_id=456
php public_html/task_cli.php run --force-all
php public_html/task_cli.php get_task --task_id=123
```

Log: `system/logs/task_log.txt`. Admin UI: `rt=tool/task`. Same-named `addTask()` **replaces** the existing task. Step workers live under `admin/controller/task/` (e.g. `ControllerTaskToolBackup`).

## Related

- Store-specific settings: [abantecart-multistore-language](../abantecart-multistore-language/SKILL.md)
- Hooks: [abantecart-extensions](../abantecart-extensions/SKILL.md)
