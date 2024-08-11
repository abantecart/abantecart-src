<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesCatalogProductSummary extends AController
{
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->loadModel('catalog/product');
        $product_id = (int)$this->request->get['product_id'];
        $this->data['product'] = $product_info = $mdl->getProduct($product_id);
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
            $this->data['product']['preview'] = $this->html->getCatalogURL('product/product', '&product_id='.$product_id);
        }else{
            /** @var ModelSettingSetting $mdl */
            $mdl = $this->loadModel('setting/setting');
            $settings = $mdl->getSetting('details',current($stores));
            $this->data['product']['preview'] = $settings['config_url'].INDEX_FILE.'?'.'rt=product/product&product_id='.$product_id;
        }

        $this->loadModel('sale/order');
        $this->data['product']['orders'] = $this->model_sale_order->getOrderTotalWithProduct($product_id);
        $this->data['product']['orders_url'] = $this->html->getSecureURL('sale/order', '&product_id='.$product_id);

        $this->view->assign('help_url', $this->gen_help_url('product_summary'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_summary.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}