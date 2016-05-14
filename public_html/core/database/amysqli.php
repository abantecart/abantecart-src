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
final class AMySQLi {
    /**
     * @var resource
     */
    protected $connection;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var string
     */
    public $error;

	/**
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 * @param bool $new_link
	 * @throws AException
	 */
    public function __construct($hostname, $username, $password, $database, $new_link=false) {
		$connection = new mysqli($hostname, $username, $password, $database);
		if ($connection->connect_error) {
            throw new AException(AC_ERR_MYSQL, 'Error: Could not make a database connection to database ' . $database.' using ' . $username . '@' . $hostname);
    	}

	    $connection->query("SET NAMES 'utf8'");
	    $connection->query("SET CHARACTER SET utf8");
	    $connection->query("SET CHARACTER_SET_CONNECTION=utf8");
	    $connection->query("SET SQL_MODE = ''");
	    $connection->query("SET session wait_timeout=60;");
	    $connection->query("SET SESSION SQL_BIG_SELECTS=1;");

        $this->registry = Registry::getInstance();
		$this->connection = $connection;
  	}

    /**
     * @param string $sql
     * @param bool $noexcept
     * @return bool|stdClass
     * @throws AException
     */
    public function query($sql, $noexcept = false) {
		//echo $this->database_name;
        $time_start = microtime(true);
        $result = $this->connection->query($sql);
        $time_exec = microtime(true) - $time_start;

        // to avoid debug class init while setting was not yet loaded
		if($this->registry->get('config')){
			if ( $this->registry->get('config')->has('config_debug') ) {
				$backtrace = debug_backtrace();
				ADebug::set_query($sql, $time_exec, $backtrace[2] );
			}
		}
		if ($result) {
			if (!is_bool($result)) {
				$i = 0;
				$data = array();
				while ($row = $result->fetch_object()) {
					$data[$i] = (array)$row;
					$i++;
				}

				$query = new stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = (int)$result->num_rows;
				
				unset($data);

				return $query;	
    		} else {
				return TRUE;
			}
		} else {
			$this->error = 'SQL Error: '.mysqli_error($this->connection).'<br />Error No: '.mysqli_errno($this->connection).'<br />SQL: '.$sql;
			if($noexcept){
				return FALSE;
			}else{
				throw new AException(AC_ERR_MYSQL, $this->error);
			}
    	}
  	}

    /**
     * @param string $value
     * @return string
     */
    public function escape($value) {
	    if(is_array($value)){
		    $dump = var_export($value,true);
		    $backtrace = debug_backtrace();
		    $dump .= ' (file: '.$backtrace[1]['file'] .' line '.$backtrace[1]['line'].')';
		    $message = 'aMySQLi class error: Try to escape non-string value: '.$dump;
		    $error = new AError($message);
		    $error->toLog()->toDebug()->toMessages();
		    return false;
	    }
		return $this->connection->real_escape_string((string)$value);
	}

    /**
     * @return int
     */
    public function countAffected() {
    	return $this->connection->affected_rows;
  	}

    /**
     * @return int
     */
    public function getLastId() {
    	return $this->connection->insert_id;
  	}

    public function __destruct() {
	    $this->connection->close();
	}

	public function getDBError(){
		return array(
				'error_text' => mysqli_error($this->connection),
				'errno'      => mysqli_errno($this->connection)
		);
	}
}
