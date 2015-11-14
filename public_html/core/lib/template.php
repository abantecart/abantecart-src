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

final class ATemplate {
	public $data = array();
	private $config;

	public function fetch($filename) {

        $registry = Registry::getInstance();
        $this->config = $registry->get('config');

		//#PR Build the path to the template file
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_storefront_template') . '/template/'.  $filename)) {
            $filename = $this->config->get('config_storefront_template') . '/template/'.  $filename;
        } else {
            $filename = 'default/template/'.  $filename;
        }
	
		$file = DIR_TEMPLATE . $filename;
    
		if (file_exists($file)) {
			extract($this->data);
			
      		ob_start();
      
	  		include($file);
      
	  		$content = ob_get_contents();

      		ob_end_clean();

      		return $content;
    	} else {
            throw new AException(AC_ERR_LOAD, 'Error: Could not load template ' . $file . '!');
    	}	
	}
}
?>