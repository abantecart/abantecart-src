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

	/*
	* MD5 based encoding used for passwords 
	*/
	static function getHash($keyword){	
		return md5($keyword.SALT);
	}

	/*
	* Encoding of URL for marketplace access
	*/
	static function addEncoded_stid($url){
		$text = UNIQUE_ID.'***'.$_SERVER ['SERVER_ADDR'];
		$encrypt = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'tracetnaba', $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));

		$url = str_replace('&amp;','&',$url);
		$url = $url.(strpos($url,'?')===false? '?' : '&amp;').'stid='.rawurlencode($encrypt);
	return $url;
	}

}

// SSL Based encryption class PHP 5.3 >
// Manual Configuration might berequired
/* 
Requirement: PHP => 5.3 and openSSL enabled

Configuration: 
Add key storage location path. 
Add below line to /system/config.php file. Change path to your specific path on your server
define('SSL_KEYS_DIR', '/path/to/keys/');

NOTES: 
1. Keep Key in secure location with restricted file permissions for root and apache (webserver)
2. There is no key expiration mamagement. 
These needs to be acounted for in key management precedures


Examples:

1. Generate Keys

$password = "123456";
$conf = array (
    'digest_alg'       => 'sha512',
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    'private_key_bits' => 2048,
    'encrypt_key'      => true
);
$enc = new ASSLEncryption ();
$keys = $enc->generate_ssl_key_pair($conf, $password);
$enc->save_ssl_key_pair($keys, 'key_with_pass');
echo_array($keys);	

2. Encrypt

$enc = new ASSLEncryption ('key_with_pass');
$enc_str = $enc->encrypt('test text');
echo $enc_str;

3. Decrypt

$enc = new ASSLEncryption ('', 'key_with_pass', $password);
echo $enc->decrypt($enc_str);


Need help configuring, supporting or extending functionality, 
contact www.abantecart.com for forum or paid support
*/


final class ASSLEncryption {
	private $pubkey;
	private $prkey;
	private $key_path;
	private $failed_str = "*****";
	private $error;
	
	//To generate new keys, class can be instintiated with no data passed
	function __construct($pubkey_name = '', $prkey_name = '', $passphrase = null) {
		$this->registry = Registry::getInstance();
		$this->log = $this->registry->get('log');
		$this->message = $this->registry->get('messages');
		
		//Validate if SSL PHP support is installed
		if ( !function_exists( 'openssl_pkey_get_public' ) ){
		    $error = "Error: PHP OpenSSL is not available on your server! Check if OpenSSL installed for PHP and enabled";
		    $this->log->write($error);
		    $this->message->saveError('OpenSSL Error',$error);
		    return NULL;
		}

        //construct key storage path 
        //NOTE: SSL_KEYS_DIR needs to be added into configuration file
        //Suggested:  Directory to be secured for read a write ONLY for users root and apache (web server).    
        if ( defined('SSL_KEYS_DIR') ) {
        	$this->key_path = SSL_KEYS_DIR;	
        } else {
        	$this->key_path = DIR_SYSTEM . 'keys/';
        }
		
		if ($pubkey_name) {
			$this->pubkey = $this->getPublicKey($pubkey_name.'.pub');
		}
        if ($prkey_name) {
        	$this->loadPrivateKey($prkey_name.'.prv', $passphrase);
        }
	}

	/*
	* Generate new Key Private/Public keys pair. 
	* Incput is $config array with standard openssl_csr_new configargs 
	* $passphrase is set if want to have a passphrase to access private key
	* @return array
	*/
  	public function generate_ssl_key_pair($config = array(), $passphrase = null) {
  	  $default_length = 2048;
      if (!isset($config['private_key_bits'])) {
        $config['private_key_bits'] = $default_length;     
      } 
      $res = openssl_pkey_new($config);

	  //# Do we need to use passphrase for the key?
	  $privatekey = '';
      if ((isset($config['encrypt_key'])) && ($config['encrypt_key'] == true)) {
		openssl_pkey_export($res, $privatekey, $passphrase);      
      } else {
        openssl_pkey_export($res, $privatekey);
      }

      $publickey = openssl_pkey_get_details($res);
      $publickey = $publickey["key"];

      return array('public' => $publickey, 'private' => $privatekey);
    }

	/*
	* Save Private/Public keys pair to set key_path location
	* Incput: Private/Public keys pair array
	* 		  keyname
	* 
	* @return mixed
	*/
  	public function save_ssl_key_pair($keys = array(), $key_name) {
		if(!file_exists( $this->key_path )){
			$result = mkdir($this->key_path,0700,TRUE); // create dir with nested folders
		}else{
			$result = true;
		}	
		if(!$result){
		    $error = "Error: Can't create directory ". $this->key_path . " for saving SSL keys!";
		    $this->log->write($error);
		    $this->message->saveError('Create SSL Key Error',$error);
		    return $error;
		}
		if (empty($key_name)) {
			$key_name = 'default_key';
		}
					
		foreach ( $keys as $type => $key ) {
			if ( $type == 'private') {
				$ext = '.prv';			
			} else if ( $type == 'public') {
				$ext = '.pub';			
			}
			$file = $this->key_path . '/' . $key_name . $ext;				
			if ( file_exists($file) ) {
		        $error = "Error: Can't create key ". $key_name . "! Already Exists";
		        $this->log->write($error);
		        $this->message->saveError('Create SSL Key Error',$error);
		        return $error;		
			}
			$handle = fopen( $file, 'w');
			fwrite($handle, $key . "\n");
			fclose($handle); 
		}
	}
	
	/*
	* Get public key based on key name provided. It is loaded if not yet loaded
	* Key's are stored in the path based on the configuration
	* @return string 
	*/
	public function getPublicKey( $key_name ) {
		if ( empty($this->pubkey) ) {
	    	$this->pubkey = openssl_pkey_get_public("file://".$this->key_path.$key_name);
		}
		return $this->pubkey;
	}

	/*
	* Load private key based on key name provided. 
	* Input : Key name and passphrase (if used)
	* Key's are stored in the path based on the configuration
	* NOTE: Private key value never returned back 
	* @return bool 
	*/
	public function loadPrivateKey($key_name, $passphrase = '') {	
	  $this->prkey = openssl_pkey_get_private("file://".$this->key_path.$key_name, $passphrase);
	  if ($this->prkey){
	  	return true;
	  } else {
	  	return false;
	  } 
	}
	
	/*
	* Dectript value based on private key ONLY
	* @return string
	*/
  	public function decrypt( $crypttext ) {
  	   if (empty($crypttext)) {
			return '';
  	   }
  	   $cleartext = '';
	   if ( empty($this->prkey) ) {
			$error = "Error: SSL Decryption failed! Missing private key";
			$this->log->write($error);
			return $this->failed_str;	   
	   }

       if ((openssl_private_decrypt(base64_decode($crypttext), $cleartext, $this->prkey)) === true) {
			return $cleartext;  
       } else {
			$error = "Error: SSL Decryption based on private key has failed! Possibly wrong key!";
			$this->log->write($error);
			return $this->failed_str;	          
       }
    }

	/*
	* Encrypt value based on public key ONLY
	* @return string
	*/
  	public function encrypt( $cleartext) {
  	   if (empty($cleartext)) {
			return '';
  	   }
  	   $crypttext = '';
	   if ( empty($this->pubkey) ) {
			$error = "Error: SSL Encryption failed! Missing public key";
			$this->log->write($error);
			return '';	   
	   }

       if ((openssl_public_encrypt($cleartext, $crypttext, $this->pubkey)) === true) {
			return base64_encode($crypttext);  
       } else {
			$error = "Error: SSL Encryption based on public key has failed! Possibly wrong key!";
			$this->log->write($error);
			return '';	          
       }
    }
		
}

?>