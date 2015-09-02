<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

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
class ControllerCommonSeoUrl extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			//Possible area for improvment. Only need to check last node in the path			
			foreach ($parts as $part) {
				$query = $this->db->query("SELECT *
											FROM " . DB_PREFIX . "url_aliases
											WHERE keyword = '" . $this->db->escape($part) . "'");
				//Add cacheing of the result. 
				
				if ($query->num_rows) {
					//Note: query is a field contaning area=id to identify location  
					$url = explode('=', $query->row['query']);
					
					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}
					
					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}	
					
					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}
					
					if ($url[0] == 'content_id') {
						$this->request->get['content_id'] = $url[1];
					}	
				} else {
					$this->request->get['rt'] = 'pages/error/not_found';
				}
			}
			
			if (isset($this->request->get['product_id'])) {
				$this->request->get['rt'] = 'pages/product/product';
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['rt'] = 'pages/product/category';
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['rt'] = 'pages/product/manufacturer';
			} elseif (isset($this->request->get['content_id'])) {
				$this->request->get['rt'] = 'pages/content/content';
			}

			if (isset($this->request->get['rt'])) {
				$rt = $this->request->get['rt'];
				//remove pages prefix from rt for use in new generated urls
				if(substr($this->request->get['rt'],0,6) == 'pages/'){
					$this->request->get['rt'] = substr($this->request->get['rt'],6);
				}
				unset($this->request->get['_route_']);
				//Update router with new RT 
				$this->router->resetController($rt);
				return $this->dispatch($rt,$this->request->get);
			}

		}

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
