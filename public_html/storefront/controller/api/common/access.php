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
class ControllerApiCommonAccess extends AControllerAPI {
	
	public function main() {
		//validate if API enabled and KEY matches. 
		if ( $this->config->get('config_storefront_api_status')	) {
			if ($this->config->get('config_storefront_api_key') && 
					( $this->config->get('config_storefront_api_key') == $this->request->post['api_key'] ||
					  $this->config->get('config_storefront_api_key') == $this->request->get['api_key'] )
				) {
				return null;
			} else if ( !$this->config->get('config_storefront_api_key') ) {
				return null;
			}
		}	
		return $this->dispatch('api/error/no_access');
	}	
}



