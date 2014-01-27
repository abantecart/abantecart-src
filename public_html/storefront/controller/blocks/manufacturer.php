<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerBlocksManufacturer extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('heading_title', $this->language->get('heading_title') );
        $this->view->assign('text_select', $this->language->get('text_select') );

		//For product page show only brand icon
		if (isset($this->request->get['product_id']) && is_int($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
			$this->view->assign('product_id', $product_id );
			$result = $this->model_catalog_manufacturer->getManufacturerByProductId($product_id);
			$manuf_detls = $result[0];
			$resource = new AResource('image');
			$thumbnail = $resource->getMainThumb('manufacturers',
				$manuf_detls['manufacturer_id'],
				(int)$this->config->get('config_image_grid_width'),
				(int)$this->config->get('config_image_grid_height'),
				true);			
			$manufacturer = array(
				'manufacturer_id' => $manuf_detls['manufacturer_id'],
				'name'            => $manuf_detls['name'],
				'href'            => $this->html->getSEOURL('product/manufacturer', '&manufacturer_id=' . $manuf_detls['manufacturer_id'], '&encode'),
				'icon'			  => $thumbnail['thumb_url']
			);
        	$this->view->assign('manufacturer', $manufacturer );

		} else {

			if (isset($this->request->get['manufacturer_id']) && is_int($this->request->get['manufacturer_id']) ) {
				$manufacturer_id = $this->request->get['manufacturer_id'];
			} else {
				$manufacturer_id = 0;
			}
	        $this->view->assign('manufacturer_id', $manufacturer_id );
			
			$this->loadModel('catalog/manufacturer');
			 
			$manufacturers = array();
			
			$results = $this->model_catalog_manufacturer->getManufacturers();

			$thumbnail_list = $this->cache->get('manufacturer.block.thumbnals','',(int)$this->config->get('config_store_id'));
			$is_cache_exists = $this->cache->exists('manufacturer.block.thumbnals','',(int)$this->config->get('config_store_id'));

			$resource = new AResource('image');
			foreach ($results as $result) {
				if(!$is_cache_exists){
					$thumbnail = $resource->getMainThumb('manufacturers',
															$result['manufacturer_id'],
															(int)$this->config->get('config_image_grid_width'),
															(int)$this->config->get('config_image_grid_height'),
															true);
					$thumbnails_cache[$result['manufacturer_id']] = $thumbnail;
				}else if(has_value($thumbnail_list[$result['manufacturer_id']])) {
					$thumbnail = $thumbnail_list[$result['manufacturer_id']];
				}
			
				$manufacturers[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => $result['name'],
					'href'            => $this->html->getSEOURL('product/manufacturer', '&manufacturer_id=' . $result['manufacturer_id'], '&encode'),
					'icon'			  => $thumbnail				
				);
			}

			if(!$is_cache_exists){
				$this->cache->set('manufacturer.block.thumbnals',$thumbnails_cache, '',(int)$this->config->get('config_store_id'));
			}
	
	        $this->view->assign('manufacturers', $manufacturers );
		}
				// framed needs to show frames for generic block.
		//If tpl used by listing block framed was set by listing block settings
		$this->view->assign('block_framed',true);

		$this->processTemplate('blocks/manufacturer.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
