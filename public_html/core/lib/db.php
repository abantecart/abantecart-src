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
final class ADB {
	private $driver;
	public $error='';
	public $registry;
	
	public function __construct($driver, $hostname, $username, $password, $database) {		
		if (file_exists(DIR_DATABASE . $driver . '.php')) {
			require_once(DIR_DATABASE . $driver . '.php');
		} else {
			throw new AException(AC_ERR_MYSQL, 'Error: Could not load database file ' . $driver . '!');
		}
				
		$this->driver = new $driver($hostname, $username, $password, $database);
		
		$this->registry = Registry::getInstance();
	}
		
  	public function query($sql,$noexcept=false) {
  		
        if ( $this->registry->has('extensions') ) {
	        $result = $this->registry->get('extensions')->hk_query($this, $sql,$noexcept);
        } else {
        	$result = $this->_query($sql,$noexcept);
        }
		if($noexcept && $result===false){
			$this->error = $this->driver->error;
		}
		return $result;
    }

	public function table($table_name){
		//detect if encryption is enabled
		$postfix = '';
		if ( is_object($this->registry->get('dcrypt')) ) {
			$postfix = $this->registry->get('dcrypt')->posfix($table_name);
		}
		return DB_PREFIX . $table_name . $postfix;
	}	
  	
	public function _query($sql, $noexcept=false) {
		return $this->driver->query($sql,$noexcept);
  	}
  	
	public function escape($value) {
		return $this->driver->escape($value);
	}
	
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	public function getLastId() {
		return $this->driver->getLastId();
  	}

	public function performSql($file){

        if ($sql = file($file)) {
			$query = '';
			foreach($sql as $line) {
				$tsl = trim($line);
				if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
					$query .= $line;
					if (preg_match('/;\s*$/', $line)) {
						$query = str_replace("`ac_", "`" . DB_PREFIX, $query);
						$result = $this->_query($query);
						if (!$result) {
							$this->error(mysql_error());
                            return;
						}
						$query = '';
					}
				}
			}
        }
    }
}
?>