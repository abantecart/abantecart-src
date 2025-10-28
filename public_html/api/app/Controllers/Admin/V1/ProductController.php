<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

namespace app\Controllers\Admin\V1;

use Psr\Http\Message\ServerRequestInterface as Request;
use Nyholm\Psr7\Factory\Psr17Factory;

class ProductController
{
    /** @var \Registry $registry */
    public function __construct(private \Registry $registry)
    {
        $this->registry = $registry;
    }

    public function list(Request $req, array $vars, Psr17Factory $psr17)
    {
        $this->registry->get('load')->model('catalog/product');
        /** @var \ModelCatalogProduct $model */
        $model = $this->registry->get('model_catalog_product');

        $page = max(1, (int)($req->getQueryParams()['page'] ?? 1));
        $limit = min(100, max(1, (int)($req->getQueryParams()['limit'] ?? 20)));
        $start = ($page - 1) * $limit;

        $products = $model->getProducts(['start' => $start, 'limit' => $limit]);

        $items = array_map(fn($p) => [
            'id'     => (int)$p['product_id'],
            'sku'    => $p['sku'] ?? null,
            'name'   => $p['name'] ?? $p['model'],
            'price'  => (float)$p['price'],
            'status' => (int)$p['status'],
        ], $products ?? []);

        $data = ['data' => $items, 'meta' => ['page' => $page, 'limit' => $limit]];
        $res = $psr17->createResponse(200);
        $res->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $req, array $vars, Psr17Factory $psr17)
    {
        $id = (int)$vars['id'];
        $this->registry->get('load')->model('catalog/product');
        $model = $this->registry->get('model_catalog_product');

        $p = $model->getProduct($id);
        if (!$p) {
            $res = $psr17->createResponse(404);
            $res->getBody()->write(json_encode(['error' => ['code' => 'not_found', 'message' => 'Product not found']]));
            return $res->withHeader('Content-Type', 'application/json');
        }
        $data = [
            'data' => [
                'id'     => $id,
                'sku'    => $p['sku'] ?? null,
                'name'   => $p['name'] ?? $p['model'],
                'price'  => (float)$p['price'],
                'status' => (int)$p['status'],
            ]
        ];
        $res = $psr17->createResponse(200);
        $res->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $res->withHeader('Content-Type', 'application/json');
    }
}

