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

class ControllerBlocksManufacturer extends AController
{
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->data['empty_render_text'] =
            'To view content of block you should be logged in and prices must be without taxes';
    }

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('blocks/manufacturer');

        $this->view->assign('heading_title', $this->language->get('heading_title', 'blocks/manufacturer'));
        $this->view->assign('text_select', $this->language->get('text_select'));

        $resource = new AResource('image');

        //For product page show only brand icon
        if (isset($this->request->get['product_id']) && is_int($this->request->get['product_id'])) {
            $product_id = $this->request->get['product_id'];
            $this->view->assign('product_id', $product_id);
            $result = $this->model_catalog_manufacturer->getManufacturerByProductId($product_id);
            $manDetails = $result[0];

            $thumbnail = $resource->getMainThumb(
                'manufacturers',
                $manDetails['manufacturer_id'],
                (int) $this->config->get('config_image_grid_width'),
                (int) $this->config->get('config_image_grid_height')
            );
            $manufacturer = [
                'manufacturer_id' => $manDetails['manufacturer_id'],
                'name'            => $manDetails['name'],
                'href'            => $this->html->getSEOURL(
                    'product/manufacturer',
                    '&manufacturer_id='.$manDetails['manufacturer_id'],
                    '&encode'
                ),
                'icon'            => $thumbnail['thumb_url'],
            ];
            $this->view->assign('manufacturer', $manufacturer);
        } else {
            if (isset($this->request->get['manufacturer_id']) && is_int($this->request->get['manufacturer_id'])) {
                $manufacturer_id = $this->request->get['manufacturer_id'];
            } else {
                $manufacturer_id = 0;
            }
            $this->view->assign('manufacturer_id', $manufacturer_id);
            $this->loadModel('catalog/manufacturer');

            $manufacturers = [];
            $results = $this->model_catalog_manufacturer->getManufacturers();
            $manufacturer_ids = array_column($results, 'manufacturer_id');

            $thumbnails = $manufacturer_ids
                ? $resource->getMainThumbList(
                    'manufacturers',
                    $manufacturer_ids,
                    $this->config->get('config_image_grid_width'),
                    $this->config->get('config_image_grid_height')
                )
                : [];
            foreach ($results as $result) {
                $thumbnail = $thumbnails[$result['manufacturer_id']];
                $manufacturers[] = [
                    'manufacturer_id' => $result['manufacturer_id'],
                    'name'            => $result['name'],
                    'href'            => $this->html->getSEOURL(
                        'product/manufacturer',
                        '&manufacturer_id='.$result['manufacturer_id'],
                        '&encode'
                    ),
                    'icon'            => $thumbnail,
                ];
            }

            $this->view->assign('manufacturers', $manufacturers);
        }
        // framed needs to show frames for generic block.
        //If tpl used by listing block framed was set by listing block settings
        $this->view->assign('block_framed', true);

        $this->processTemplate('blocks/manufacturer.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
