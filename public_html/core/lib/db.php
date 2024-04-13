<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * do the phpdoc-trick with meta-class for query result
 * @property array $row
 * @property array $rows
 * @property int $num_rows
 */
class db_result_meta extends  stdClass {}


final class ADB
{
    /**
     * @var MySql|AMySQLi
     */
    private $driver;
    public $error = '';
    public $registry;

    /**
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     *
     * @throws AException
     */
    public function __construct($driver, $hostname, $username, $password, $database)
    {
        $filename = DIR_DATABASE.$driver.'.php';
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            require_once($filename);
        } else {
            throw new AException(AC_ERR_MYSQL, 'Error: Could not load database file '.$driver.'!');
        }

        try {
            $this->driver = new $driver($hostname, $username, $password, $database);
        }catch(Exception|Error $e){
            $err = new AError(
                'Cannot establish database connection to '.$database.' using '.$username.'@'.$hostname
                ."\n".$e->getMessage()
            );
            $err->toLog();
            exit('Cannot establish connection to database');
        }
        $this->registry = Registry::getInstance();
    }

    /**
     * @param string $sql
     * @param bool $noexcept
     *
     * @return bool|db_result_meta
     * @throws AException
     */
    public function query($sql, $noexcept = false)
    {

        if ($this->registry->has('extensions')) {
            $result = $this->registry->get('extensions')->hk_query($this, $sql, $noexcept);
        } else {
            $result = $this->_query($sql, $noexcept);
        }
        if ($noexcept && $result === false) {
            $this->error = $this->driver->error;
        }
        return $result;
    }

    /**
     * @param string $table_name
     *
     * @return string
     */
    public function table($table_name)
    {
        //detect if encryption is enabled
        $postfix = '';
        if (is_object($this->registry->get('dcrypt'))) {
            $postfix = $this->registry->get('dcrypt')->postfix($table_name);
        }
        return DB_PREFIX.$table_name.$postfix;
    }

    /**
     * @param string $sql
     * @param bool $noexcept
     *
     * @return bool|db_result_meta
     * @throws AException
     */
    public function _query($sql, $noexcept = false)
    {
        return $this->driver->query($sql, $noexcept);
    }

    public function escape($value, $with_special_chars = false)
    {
        return $this->driver->escape($value, $with_special_chars);
    }

    /**
     * @return int
     */
    public function countAffected()
    {
        return $this->driver->countAffected();
    }

    /**
     * @return int
     */
    public function getLastId()
    {
        return $this->driver->getLastId();
    }

    /**
     * @return string
     */
    public function getSqlCalcTotalRows()
    {
        return $this->driver->getSqlCalcTotalRows();
    }
    /**
     * @return int|false
     */
    public function getTotalNumRows()
    {
        return $this->driver->getTotalNumRows();
    }

    /**
     * @param $file
     *
     * @return null
     * @throws AException
     */
    public function performSql($file)
    {

        if ($sql = file($file)) {
            $query = '';
            foreach ($sql as $line) {
                $tsl = trim($line);
                if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
                    $query .= $line;
                    if (preg_match('/;\s*$/', $line)) {
                        $query = str_replace("`ac_", "`".DB_PREFIX, $query);
                        $result = $this->_query($query, true);
                        if(!$query){
                            continue;
                        }
                        if (!$result) {
                            $err = $this->driver->getDBError();
                            $this->error = var_export($err, true);
                            return null;
                        }
                        $query = '';
                    }
                }
            }
        }
        return true;
    }
}
