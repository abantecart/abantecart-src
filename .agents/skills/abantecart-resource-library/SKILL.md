---
name: abantecart-resource-library
description: AbanteCart resource library for images and files mapped to products, categories, manufacturers, contents, and other objects. Use when attaching media, resolving image URLs/thumbs, or working with AResource / AResourceManager.
---

# Resource library

Media is not stored as a filename column on products. Files live in `public_html/resources/` and are mapped through the resource library.

## Classes

| Class | File | Role |
| --- | --- | --- |
| `AResource` | `core/engine/resources.php` | Read: get/map/thumb URLs (storefront + admin) |
| `AResourceManager` | `core/lib/resource_manager.php` | Write: add/map/unmap (admin only) |

`AResourceManager` throws if `IS_ADMIN` is false.

## Object types

`AResource::$obj_list` includes: `products`, `categories`, `manufacturers`, `contents`, `collections`, `product_option_value`, `storefront_menu_item`, `field`. Extensions may map custom `object_name` values (e.g. `banners`).

Resource **types** (table `resource_types`): typically `image`, plus downloadable files, etc. Directory under `DIR_RESOURCE` comes from the type's `default_directory`.

## Tables

- `resource_types`
- `resource_library` — file metadata
- `resource_descriptions` — name, title, description, **resource_path**, resource_code per `language_id`
- `resource_map` — (`resource_id`, `object_name`, `object_id`, `sort_order`, `default`)

On disk: `DIR_RESOURCE/{type_dir}/{hex_path}.{ext}` via `AResource::getHexPath($resource_id)` (id `18733` → `18/73/3.png`). Thumbs: `DIR_IMAGE` via `check_resize_image()` / `AImage`.

## Read (storefront or admin)

```php
$rl = new AResource('image');

$thumb = $rl->getMainThumb('products', $product_id, $width, $height);
$main  = $rl->getMainImage('products', $product_id, $width, $height);
$url   = $rl->getResizedImageURL($resourceInfo, $width, $height);

$all = $rl->getResourceAllObjects('products', $product_id, [
    'main'  => ['width' => 500, 'height' => 500],
    'thumb' => ['width' => 90, 'height' => 90],
], 0, true);

$resources = $rl->getResources('products', $product_id, $language_id);
$info = $rl->getResource($resource_id, $language_id);
$url = $rl->getResourceThumb($resource_id, $width, $height, $language_id);
```

Thumbs are generated under `image/thumbnails/` (or via `ModelToolImage`). `$noimage = true` substitutes the configured no-image asset.

URLs use `HTTPS_DIR_RESOURCE` / `HTTP_DIR_RESOURCE` (`resources/` on the store).

## Write (admin / install.php)

```php
$rm = new AResourceManager();
$rm->setType('image');

$resource_id = $rm->addResource([
    'resource_path' => 'tmp_upload.jpg', // under DIR_RESOURCE/{type}/ — or use resource_code for HTML/icon
    'resource_code' => '',
    'name'          => [$language_id => 'Menu Icon'],
    'title'         => [$language_id => ''],
    'description'   => [$language_id => ''],
]);
// descriptions also go through $this->language->replaceDescriptions('resource_descriptions', ...)

$rm->mapResource('products', $product_id, $resource_id);
$rm->unmapResource('products', $product_id, $resource_id);
$rm->mapResources($ids, 'products', $product_id);
```

`addResource()` copies files into `DIR_RESOURCE . $type_dir` using `buildResourcePath()`. Directories must be writable.

After changes: `$this->cache->remove('resources')` (manager methods usually do this).

## Admin UI

Product images tab: `rt=catalog/product_images`. Shared RL picker is used across catalog, content, banners, menus.

## Pitfalls

- Do not store only a path on the product row; map a resource
- Include `language_id` when reading descriptions
- Custom objects: use a unique `object_name` and clean `resource_map` on uninstall
- `resources/` is public; do not put secrets there (use `download/` + `ADownload` for paid files)
