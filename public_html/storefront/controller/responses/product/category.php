<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesProductCategory extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        try {
            $this->config->set('embed_mode', true);
            //fix urls for url that contains fake seo folders
            if (isset($this->request->get['store_id'])) {
                $seoPrefix = '';
                $storeId = (int) $this->request->get['store_id'];
                /** @var ModelSettingStore $mdl */
                $mdl = $this->loadModel('setting/store');
                $store_info = $mdl->getStore($storeId);
                if($store_info){
                    $settings = $mdl->getStoreSettings($storeId);
                    if(HTTPS === true && $settings['config_ssl_url'] && $this->config->get('config_ssl_url')){
                        $seoPrefix = str_replace($this->config->get('config_ssl_url'),'',$settings['config_ssl_url']);
                    }elseif(HTTPS !== true && $settings['config_url'] && $this->config->get('config_url')){
                        $seoPrefix = str_replace($this->config->get('config_url'),'',$settings['config_url']);
                    }
                    if($seoPrefix) {
                        $this->config->set('seo_prefix', $seoPrefix);
                    }
                    $this->config->set('config_store_id', $storeId);
                }
            }

            $cntr = $this->dispatch('pages/product/category');
            $this->data['html_out'] = $cntr->dispatchGetOutput();
        } catch (AException $e) {
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->response->setOutput($this->data['html_out']);
    }

}