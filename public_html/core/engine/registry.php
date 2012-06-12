<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

final class Registry {
	private $data = array();    
    static private $instance = NULL;
    
    static function getInstance()
    {
        if (self::$instance == NULL)
        {
          self::$instance = new Registry();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

	public function get($key)
    {
		return (isset($this->data[$key]) ? $this->data[$key] : NULL);
	}

	public function set($key, $value)
                {
		$this->data[$key] = $value;
	}

	public function has($key)
    {
    	return isset($this->data[$key]);
  	}	
}