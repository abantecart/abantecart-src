<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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
class ControllerApiProductResources extends AControllerAPI {
	
	public function get() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$product_id = $this->request->get['product_id'];
		$resource_type = $this->request->get['resource_type'];
	
		if ( !$product_id ) {
			$this->rest->setResponseData( array('Error' => 'Missing product ID as a required parameter') );
			$this->rest->sendResponse( 200);
			return null;
		}
		
		$resources = array();
		$resource = new AResource('image');
		if ( $resource_type == 'image') {
			$images = array();

			$results = $resource->getResources( 'products', $product_id,  $this->config->get('storefront_language_id'));

			foreach ($results as $result) {
				$thumbnail = $resource->getResourceThumb($result['resource_id'],
														$this->config->get('config_image_additional_width'),
						                                $this->config->get('config_image_additional_height'));
				if($thumbnail){
					$images[] = array(
						'origial' => HTTPS_DIR_RESOURCE.'image/'.$result['resource_path'],
						'thumb' => $thumbnail );
				}
			}
            $resources = $images;
		} else if ( $resource_type == 'pdf') {
			// TODO Add support to other types to return files or codes
		} else {
			$resource = new AResource('image');
			//Getting all available types. NOTE there is no easy way, yet, to tell what resources are available in what type for given product. 
			//This is possible only in admin for now
			$resources = $resource->getAllResourceTypes();
		}
		

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->rest->setResponseData( array( 'total' => count($resources), 'resources' => $resources) );
		$this->rest->sendResponse(200);

	}
}