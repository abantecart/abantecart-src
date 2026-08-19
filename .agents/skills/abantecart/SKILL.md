---
name: abantecart
description: Index of AbanteCart development skills. Use when working in this AbanteCart repo, writing PHP for admin or storefront, building extensions, or when unsure which AbanteCart skill to load.
---

# AbanteCart skills

Load the topical skill that matches the task. Do not guess AbanteCart APIs from generic PHP MVC knowledge.

| Task | Skill |
| --- | --- |
| Bootstrap, `rt=`, Registry, dispatcher, pages/responses/api/tasks | [abantecart-architecture](../abantecart-architecture/SKILL.md) |
| Controllers, models, `.tpl` views, language XML | [abantecart-mvc](../abantecart-mvc/SKILL.md) |
| New extension, hooks, install/uninstall/upgrade | [abantecart-extensions](../abantecart-extensions/SKILL.md) |
| SQL, `DB_PREFIX`, schema, upgrade scripts | [abantecart-database](../abantecart-database/SKILL.md) |
| `$this->config`, settings, cache, hooks-as-events, scheduled tasks | [abantecart-config-cache-tasks](../abantecart-config-cache-tasks/SKILL.md) |
| Images/files on products, categories, contents | [abantecart-resource-library](../abantecart-resource-library/SKILL.md) |
| `store_id`, `language_id`, translations, `*_to_stores` | [abantecart-multistore-language](../abantecart-multistore-language/SKILL.md) |
| Request cleaning, escaping, CSRF tokens | [abantecart-security](../abantecart-security/SKILL.md) |

## Defaults for every change

1. Prefer an **extension** under `public_html/extensions/<id>/`. Do not patch core.
2. Access services through Registry: `$this->db`, `$this->config`, `$this->cache`, `$this->language`, `$this->load`, `$this->html`, `$this->request`, `$this->session`.
3. Table names: `$this->db->table('products')` — never hardcode `ac_` in PHP.
4. Wrap controller work with `$this->extensions->hk_InitData($this, __FUNCTION__);` and `hk_UpdateData`. Extensions update `$this->data` from the hook class (`$that->data` / `$that->view->assign`).
5. After writes, `$this->cache->remove('group')` for the affected cache group.

App root is `public_html/`. Version is `MASTER_VERSION.MINOR_VERSION.VERSION_BUILT` from `public_html/core/version.php` (currently 1.4.x).
