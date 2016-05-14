<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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
class ControllerCommonHTMLCache extends AController {
    public function main() {
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if($this->config->get('config_html_cache')) {
			//HTML cache is only for non-customer as customer pages are dynamic
			if(!$this->customer->isLogged() && !$this->customer->isUnauthCustomer()){
				$rt_controller = $this->router->getController();
				//Check if requested controller allows HTML caching
				$cache_keys = $this->getCacheKeyValues($rt_controller);
				if(is_array($cache_keys)){
					//all good, see if we can load cache with the key 			
            		$this->buildHTMLCacheKey($cache_keys, $this->request->get, $rt_controller);			
					if($this->html_cache()){
						//return complete status to dispatcher
						return 'completed';
					}
				}
			}
		}

        $this->extensions->hk_UpdateData($this,__FUNCTION__);
    }
}
