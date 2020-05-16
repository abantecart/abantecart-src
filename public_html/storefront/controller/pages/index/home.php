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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesIndexHome extends AController
{

    /**
     * Check if HTML Cache is enabled for the method
     *
     * @return array - array of data keys to be used for cache key building
     */
    public static function main_cache_keys()
    {
        return array();
    }

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $language_id = $this->config->get('storefront_language_id');

        $this->document->setTitle($this->config->get('config_title_'.$language_id));
        $this->document->setDescription($this->config->get('config_meta_description_'.$language_id));
        $this->document->setKeywords($this->config->get('config_meta_keywords_'.$language_id));

        $this->view->assign('heading_title', sprintf($this->language->get('heading_title'), $this->config->get('store_name')));

        $this->loadModel('setting/store');

        if (!$this->config->get('config_store_id')) {
            $this->view->assign('welcome', html_entity_decode($this->config->get('config_description_'.$language_id), ENT_QUOTES, 'UTF-8'));
        } else {
            $store_info = $this->model_setting_store->getStore($this->config->get('config_store_id'));

            if ($store_info) {
                $this->view->assign('welcome', html_entity_decode($store_info['description'], ENT_QUOTES, 'UTF-8'));
            } else {
                $this->view->assign('welcome', '');
            }
        }

        $this->view->assign('special', $this->html->getNonSecureURL('product/special'));

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
