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

final class AEncryption {
	private $key;
	
	function __construct($key) {
        $this->key = $key;
	}
	
	function encrypt($value) {
		if (!$this->key) { 
			return $value;
		}
		
		$stdout = '';
		
		for ($i = 0; $i < strlen($value); $i++) {
			$char = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			
			$stdout .= $char;
		} 
		
        return base64_encode($stdout); 
	}
	
	function decrypt($value) {
		if (!$this->key) { 
			return $value;
		}
		
		$stdout = '';
		
		$value = base64_decode($value);
		
		for ($i = 0; $i < strlen($value); $i++) {
			$char = substr($value, $i, 1);
			$keychar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			
			$stdout .= $char;
		}
		
		return $stdout;
	}

	static function getHash($keyword){	
		return md5($keyword.SALT);
	}

	static function addEncoded_stid($url){
		$text = UNIQUE_ID.'***'.$_SERVER ['SERVER_ADDR'];
		$encrypt = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'tracetnaba', $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));

		$url = str_replace('&amp;','&',$url);
		$url = $url.(strpos($url,'?')===false? '?' : '&amp;').'stid='.rawurlencode($encrypt);
	return $url;
	}

	

}

?>