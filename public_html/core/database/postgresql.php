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
final class PostgreSQL {
    /**
     * @var resource
     */
    protected $connection;
	/**
     * @var resource
     */
    protected $result;
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
		$connection = pg_connect("host=".$hostname." user=".$username." password=".$password." dbname=".$database."  options='--client_encoding=UTF8'");
		if (!$connection) {
            throw new AException(AC_ERR_MYSQL, 'Error: Could not make a database connection using ' . $username . '@' . $hostname);
    	}

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
	    $sql = $this->_sql_prepare($sql);
        $resource = pg_query($this->connection, $sql);
	    $this->result = $resource;
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
				//get last id for inserts
				if(is_int(strpos($sql, 'INSERT INTO'))){
					$insert_query = pg_query("SELECT lastval();");
					$insert_row = pg_fetch_row($insert_query);
					$this->last_id = $insert_row[0];
				}


				$i = 0;
    	
				$data = array();
		
				while ($result = pg_fetch_assoc($resource)) {
					$data[$i] = $result;
    	
					$i++;
				}
				
				pg_free_result($resource);
				
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
				$this->error = 'AbanteCart Error: ' . pg_result_error($resource) . '<br />' . $sql;
				return FALSE;
			}else{
				throw new AException(AC_ERR_MYSQL, 'Error: ' . pg_result_error($resource) . '<br />' . $sql);
			}
    	}
  	}

	private function _sql_prepare($sql){
		$sql = trim($sql);
		$sql = str_replace('`',"", $sql); //TODO: replace or wrap this mysql quote.
		$sql = str_replace('"',"'", $sql); //TODO: need catch cases when string values prapped by double quotes(probably with audit_log extension). Note: Whwn table name inside double quotes it's mean case-insensitive call in Pgsql
		$sql = str_replace("'0000-00-00 00:00:00'","NULL", $sql); // TODO: pgsql does not understand this date. Need to research INSERT calls and to solve this for cases when date column value cannot be NULL
		$sql = str_replace("= '0000-00-00'"," IS NULL", $sql);
		$sql = str_ireplace("LCASE","LOWER", $sql); //TODO: probably solution in collation of DB

		/*
		 * TODO: probably need to rewrite sql-queries to long "LIMIT-OFFSET"
		 * */
		preg_match('/(limit)\s\d(((\,\s)|(\s\,\s)|(\,))\d)?$/i', $sql, $matches);
		if($matches){
			$orig_text = $limit_text = $matches[0];
			$limit_text = strtolower($limit_text);
			$limit_text = str_replace('limit ','',$limit_text);
			$limit_text = trim($limit_text);
			$lo = explode(',',$limit_text);
			foreach($lo as &$t){
				$t = trim($t);
			}

			$sql = str_replace($orig_text, 'LIMIT '.$lo[0].' '.($lo[1] ? 'OFFSET '.$lo[1] : ''), $sql);

			//$this->registry->get('log')->write($sql);
		}


		$sql = rtrim($sql,';').';';
		/**
		 * TODO: we need to rewrite queries with GROUPing by columns such as
		 * SELECT DISTINCT ps.product_id, p.*, pd.name, pd.description,
                 (SELECT AVG(rating)
                 FROM abc_reviews r1
                 WHERE r1.product_id = ps.product_id
                     AND r1.status = '1'
                 GROUP BY r1.product_id) AS rating
             FROM abc_product_specials ps
             LEFT JOIN abc_products p ON (ps.product_id = p.product_id)
             LEFT JOIN abc_product_descriptions pd ON (p.product_id = pd.product_id AND language_id=1)
             LEFT JOIN abc_products_to_stores p2s ON (p.product_id = p2s.product_id)
             WHERE p.status = '1'
                 AND p.date_available <= NOW()
                 AND p2s.store_id = '0'
                 AND ps.customer_group_id = '1'
                 AND ((ps.date_start  IS NULL OR ps.date_start < NOW())
                 AND (ps.date_end  IS NULL OR ps.date_end > NOW()))
             GROUP BY ps.product_id
		 ORDER BY LOWER(pd.name) ASC LIMIT 0 OFFSET 4;
		 * TODO: WE cannot use p.* here. Need to set all columns list from SELECT clause after GROUP word!
		 */


		return $sql;
	}


    /**
     * @param string $value
     * @return string
     */
    public function escape($value) {

	    if(is_array($value)){
		    $dump = var_export($value,true);
		    $message = 'PostreSQL class error: Try to escape non-string value: '.$dump;
		    $error = new AError($message);
		    $error->toLog()->toDebug()->toMessages();
		    return false;
	    }
		return pg_escape_string($this->connection, (string)$value);
	}

    /**
     * @return int
     */
    public function countAffected() {
    	return pg_affected_rows($this->result);
  	}

    /**
     * @return int
     */
    public function getLastId() {
	    $insert_query = pg_query($this->connection, "SELECT lastval();");
	    $insert_row = pg_fetch_row($insert_query);
	    return $insert_row[0];
  	}

    public function __destruct() {
		if(is_resource($this->connection)){
			pg_close($this->connection);
		}
	}
}
