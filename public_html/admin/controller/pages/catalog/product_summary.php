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
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesCatalogProductSummary extends AController
{
    /** @var string|null - base URL of other store (used for storefront links) */
    protected $storeUrl = null;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('catalog/product');
        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');
        $product_id = (int)$this->request->get['product_id'];
        $this->data['product'] = $mdl->getProduct($product_id);
        $stores = $mdl->getProductStores($product_id);
        $this->data['product']['product_id'] = '#'.$product_id;
        $this->data['product']['price'] = $this->currency->format($this->data['product']['price']);
        $this->data['product']['condition'] = $mdl->getProductCondition($product_id);

        $this->data['text_product_condition'] = $this->language->get('text_product_condition');
        $this->data['text_product_available'] = $this->language->get('text_product_available');

        $resource = new AResource('image');
        $thumbnail = $resource->getMainThumb('products',
            $this->request->get['product_id'],
            $this->config->get('config_image_grid_width'),
            $this->config->get('config_image_grid_height'), true);
        $this->data['product']['image'] = $thumbnail;
        $currStoreId = (int)$this->session->data['current_store_id'];

        if($this->config->get('store_id') == $currStoreId) {
            $this->data['product']['preview'] = $this->html->getSEOURL('product/product', '&product_id='.$product_id);
        }else{
            /** @var ModelSettingSetting $mdl */
            $mdl = $this->loadModel('setting/setting');
            $settings = $mdl->getSetting('details',current($stores));
            $this->data['product']['preview'] = $settings['config_url'].INDEX_FILE.'?'.'rt=product/product&product_id='.$product_id;
        }

        $this->data['product']['mentions'] = $this->getProductMentions($product_id);

        $this->loadModel('sale/order');
        $this->data['product']['orders'] = $this->model_sale_order->getOrderTotalWithProduct($product_id);
        $this->data['product']['orders_url'] = $this->html->getSecureURL('sale/order', '&product_id='.$product_id);

        $this->view->assign('help_url', $this->gen_help_url('product_summary'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_summary.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
    
    /**
     * Returns list of links to the pages where product is mentioned
     *
     * @param int $product_id
     *
     * @return array
     * @throws AException
     */
    protected function getProductMentions($product_id)
    {
        $product_id = (int) $product_id;
        $output = ['storefront' => [], 'admin' => []];
        if (!$product_id) {
            return $output;
        }

        /** @var ModelCatalogProduct $productMdl */
        $productMdl = $this->loadModel('catalog/product');
        $product = $productMdl->getProduct($product_id);
        if (!$product) {
            return $output;
        }
        $storeId = (int) $this->session->data['current_store_id'];
        $keywords = array_values(
            array_filter(
                array_map(
                    'trim', 
                    (array) $productMdl->getProductSEOKeywords($product_id)
                )
            )
        );

        // Storefront
        $output['storefront']['product'][] = $this->getStorefrontLink(
            $product['name'],
            'product/product',
            '&product_id=' . $product_id
        );

        /** @var ModelCatalogCategory $categoryMdl */
        $categoryMdl = $this->loadModel('catalog/category');
        foreach ($productMdl->getProductCategories($product_id) as $categoryId) {
            $path = $this->getCategoryPathIds((int) $categoryId);
            if (!$path) {
                continue;
            }
            $categoryName = $categoryMdl->getPath($categoryId);
            $output['storefront']['product_category'][] = $this->getStorefrontLink(
                $categoryName . ' > ' . $product['name'],
                'product/product',
                '&path=' . $path . '&product_id=' . $product_id
            );
            $output['storefront']['categories'][] = $this->getStorefrontLink(
                $categoryName,
                'product/category',
                '&path=' . $path
            );
        }

        if ($product['manufacturer_id']) {
            /** @var ModelCatalogManufacturer $mdl */
            $mdl = $this->loadModel('catalog/manufacturer');
            $manufacturer = $mdl->getManufacturer((int) $product['manufacturer_id']);
            if ($manufacturer) {
                $output['storefront']['manufacturer'][] = $this->getStorefrontLink(
                    $manufacturer['name'],
                    'product/manufacturer',
                    '&manufacturer_id=' . (int) $product['manufacturer_id']
                );
            }
        }

        /** @var ModelCatalogCollection $mdl */
        $mdl = $this->loadModel('catalog/collection');
        $collections = $mdl->getCollectionsWithProduct($product_id, $storeId);
        foreach ($collections as $collection) {
            $output['storefront']['collections'][] = $this->getStorefrontLink(
                $collection['name'],
                'product/collection',
                '&collection_id=' . $collection['id']
            );
        }

        /** @var ModelCatalogContent $mdl */
        $mdl = $this->loadModel('catalog/content');
        foreach ($mdl->getContentsMentioningProduct($product_id, $keywords, $storeId) as $content) {
            $output['storefront']['contents'][] = $this->getStorefrontLink(
                $content['title'],
                'content/content',
                '&content_id=' . $content['content_id']
                . ($content['parent_content_id'] ? '&parent_id=' . $content['parent_content_id'] : '')
            );
        }

        // Admin
        $blocks = $productMdl->getCustomBlocksMentioningProduct(
            $product_id,
            $keywords,
            array_column($collections, 'id')
        );
        foreach ($blocks as $block) {
            $output['admin']['listing_blocks'][] = [
                'title' => $block['name'] . ' (' . $block['block_txt_id'] . ')',
                'url'   => $this->html->getSecureURL(
                    'design/blocks/edit',
                    '&custom_block_id=' . $block['custom_block_id']
                ),
            ];
        }

        $banners = $productMdl->getBannersMentioningProduct($product_id, $keywords);
        foreach ($banners['blocks'] as $block) {
            $output['admin']['banner_blocks'][] = [
                'title' => $block['name'],
                'url'   => $this->html->getSecureURL(
                    'extension/banner_manager/edit_block',
                    '&custom_block_id=' . $block['custom_block_id']
                ),
            ];
        }
        foreach ($banners['banners'] as $banner) {
            $output['admin']['banners'][] = [
                'title' => $banner['name'] . ($banner['banner_group_name'] ? ' (' . $banner['banner_group_name'] . ')' : ''),
                'url'   => $this->html->getSecureURL(
                    'extension/banner_manager/edit',
                    '&banner_id=' . $banner['banner_id']
                ),
            ];
        }

        /** @var ModelCatalogReview $mdl */
        $mdl = $this->loadModel('catalog/review');
        $reviews = $mdl->getReviews(
            [
                'filter' => ['product_id' => $product_id],
                'sort'   => 'date_added',
                'order'  => 'DESC',
            ]
        );
        foreach ((array) $reviews as $review) {
            $output['admin']['reviews'][] = [
                'title' => $review['author'] . ' (' . (int) $review['rating'] . '/5, '
                    . dateISO2Display($review['date_added'], $this->language->get('date_format_short')) . ')',
                'url'   => $this->html->getSecureURL(
                    'catalog/review/update',
                    '&review_id=' . $review['review_id']
                ),
            ];
        }

        return $output;
    }

    /**
     * @param string $title
     * @param string $rt
     * @param string $params
     *
     * @return array
     * @throws AException
     */
    protected function getStorefrontLink($title, $rt, $params)
    {
        $storeId = (int) $this->session->data['current_store_id'];
        if ($this->config->get('store_id') == $storeId) {
            $url = $this->html->getNonSecureURL($rt, $params, '', ['mode' => 'storefront']);
            $seoUrl = $this->html->getSEOURL($rt, $params);
        } else {
            if ($this->storeUrl === null) {
                /** @var ModelSettingSetting $mdl */
                $mdl = $this->loadModel('setting/setting');
                $settings = $mdl->getSetting('details', $storeId);
                $this->storeUrl = (string) $settings['config_url'];
            }
            $url = $this->storeUrl . INDEX_FILE . '?rt=' . $rt . $params;
            $seoUrl = '';
        }
        return [
            'title'   => $title,
            'url'     => $url,
            'seo_url' => $seoUrl != $url ? $seoUrl : '',
        ];
    }

    /**
     * Returns category path like "1_5_12"
     *
     * @param int $categoryId
     *
     * @return string
     * @throws AException
     */
    protected function getCategoryPathIds(int $categoryId)
    {
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $path = [];
        while ($categoryId && !in_array($categoryId, $path)) {
            $category = $mdl->getCategory($categoryId);
            if (!$category) {
                break;
            }
            array_unshift($path, $categoryId);
            $categoryId = (int) $category['parent_id'];
        }
        return implode('_', $path);
    }
}
