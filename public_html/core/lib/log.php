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

final class ALog {
	private $filename;
	private $mode=true;
	public function __construct($filename) {
		if(is_dir($filename)){
			$filename .= (substr($filename,-1)!='/' ? '/' : '').'error.txt';
		}
		$this->filename = $filename;
		if(class_exists('Registry')){// for disabling via settings
			$registry = Registry::getInstance();
			if(is_object($registry->get('config'))){
				$this->mode = $registry->get('config')->get('config_error_log') ? true : false;
			}
		}
	}
	
	public function write($message) {
		if(!$this->mode) return;
		$file = $this->filename;
		$handle = fopen($file, 'a+');
		fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");
		fclose($handle); 
	}
}
?>