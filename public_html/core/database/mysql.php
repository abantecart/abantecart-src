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
final class MySQL {
	private $connection;
    private $registry;
	
	public function __construct($hostname, $username, $password, $database) {
		if (!$this->connection = mysql_connect($hostname, $username, $password)) {
            throw new AException(AC_ERR_MYSQL, 'Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

    	if (!mysql_select_db($database, $this->connection)) {
      		throw new AException(AC_ERR_MYSQL, 'Error: Could not connect to database ' . $database);
    	}
		
		mysql_query("SET NAMES 'utf8'", $this->connection);
		mysql_query("SET CHARACTER SET utf8", $this->connection);
		mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->connection);
		mysql_query("SET SQL_MODE = ''", $this->connection);
        $this->registry = Registry::getInstance();
  	}
		
  	public function query($sql, $noexcept = false) {

        $time_start = microtime(true);
        $resource = mysql_query($sql, $this->connection);
        $time_exec = microtime(true) - $time_start;

        // to avoid debug class init while setting was not yet loaded
		if($this->registry->get('config')){
			if ( $this->registry->get('config')->has('config_debug') ) {
				$backtrace = debug_backtrace();
				ADebug::set_query($sql, $time_exec, $backtrace[2] );
			}
		}
		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;
    	
				$data = array();
		
				while ($result = mysql_fetch_assoc($resource)) {
					$data[$i] = $result;
    	
					$i++;
				}
				
				mysql_free_result($resource);
				
				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;
				
				unset($data);

				return $query;	
    		} else {
				return TRUE;
			}
		} else {
			if($noexcept){
				$this->error = 'AbanteCart Error: ' . mysql_error($this->connection) . '<br />Error No: ' . mysql_errno($this->connection) . '<br />' . $sql;
				return FALSE;
			}else{
				throw new AException(AC_ERR_MYSQL, 'Error: ' . mysql_error($this->connection) . '<br />Error No: ' . mysql_errno($this->connection) . '<br />' . $sql);
			}
    	}
  	}
	
	public function escape($value) {
		return mysql_real_escape_string($value, $this->connection);
	}
	
  	public function countAffected() {
    	return mysql_affected_rows($this->connection);
  	}

  	public function getLastId() {
    	return mysql_insert_id($this->connection);
  	}	
	
	public function __destruct() {
		mysql_close($this->connection);
	}
}
?>