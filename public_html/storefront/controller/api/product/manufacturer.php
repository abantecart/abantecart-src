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

class ControllerApiProductManufacturer extends AControllerAPI
{

    public function get()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $manufacturer_id = $this->request->get['manufacturer_id'];

        $this->loadModel('catalog/manufacturer');

        if ($manufacturer_id) {
            $data = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
        } else {
            $data = $this->model_catalog_manufacturer->getManufacturers();
        }

        $this->loadModel('tool/seo_url');
        $keyword = $this->model_tool_seo_url->getSEOKeyword('manufacturer', 'manufacturer_id', $manufacturer_id, $this->config->get('storefront_language_id'));
        if ($keyword) {
            $url = defined('HTTP_SERVER') ? HTTP_SERVER : 'http://'.REAL_HOST.get_url_path($_SERVER['PHP_SELF']);
            $data['seo_url'] = $url.'/'.$keyword;
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->rest->setResponseData($data);
        $this->rest->sendResponse(200);
    }

}