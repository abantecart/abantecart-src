<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2022 Belavier Commerce LLC

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

final class AMySQLi
{
    /** @var resource */
    protected $connection;
    /** @var Registry */
    private $registry;
    /** @var string */
    public $error;

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param bool $new_link
     *
     * @throws AException
     */
    public function __construct($hostname, $username, $password, $database, $new_link = false)
    {
        $connection = new mysqli($hostname, $username, $password, $database);
        if ($connection->connect_error) {
            $err = new AError('Cannot establish database connection to '.$database.' using '.$username.'@'.$hostname);
            $err->toLog();
            throw new AException(
                AC_ERR_MYSQL,
                'Cannot establish database connection. Check your database connection settings.'
            );
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
     *
     * @return bool|stdClass
     * @throws AException
     */
    public function query($sql, $noexcept = false)
    {
        //echo $this->database_name;
        $time_start = microtime(true);
        try {
            $result = $this->connection->query($sql);
        } /** @since php8.1 */
        catch (Exception|mysqli_sql_exception $e) {
            $result = false;
            if (!$noexcept) {
                $log = Registry::getInstance()->get('log');
                if (!$log) {
                    $log = new ALog(DIR_LOGS.'error.txt');
                    Registry::getInstance()->set('log', $log);
                }
                $errorText = $e->getCode().': '.$e->getMessage()."\n\n".$sql;
                $log->write($errorText);
                if (php_sapi_name() == 'cli') {
                    echo $errorText.' - sql: '.$sql."\n";
                }
            }
        }

        $time_exec = microtime(true) - $time_start;

        // to avoid debug class init while setting was not yet loaded
        if ($this->registry->get('config')) {
            if ($this->registry->get('config')->has('config_debug')) {
                $backtrace = debug_backtrace();
                ADebug::set_query($sql, $time_exec, $backtrace[2]);
            }
        }
        if ($result) {
            if (!is_bool($result)) {
                $i = 0;
                $data = [];
                while ($row = $result->fetch_object()) {
                    $data[$i] = (array) $row;
                    $i++;
                }

                $query = new stdClass();
                $query->row = $data[0] ?? [];
                $query->rows = $data;
                $query->num_rows = (int) $result->num_rows;

                unset($data);

                return $query;
            } else {
                return true;
            }
        } else {
            $this->error = 'SQL Error: '.mysqli_error($this->connection)
                .'<br />Error No: '.mysqli_errno($this->connection)
                .'<br />SQL: '.$sql."\n";
            if ($noexcept) {
                return false;
            } else {
                $dbg = debug_backtrace();
                $this->error .= "PHP call stack:\n";
                foreach ($dbg as $k => $d) {
                    $this->error .= "#".$k." ".$d['file'].':'.$d['line']."\n";
                }
                throw new AException(AC_ERR_MYSQL, $this->error);
            }
        }
    }

    /**
     * @param string $value
     * @param bool $with_special_chars
     *
     * @return string
     * @throws AException
     */
    public function escape($value, $with_special_chars = false)
    {
        if (is_array($value)) {
            $dump = var_export($value, true);
            $backtrace = debug_backtrace();
            $dump .= ' (file: '.$backtrace[1]['file'].' line '.$backtrace[1]['line'].')';
            $message = 'aMySQLi class error: Try to escape non-string value: '.$dump;
            $error = new AError($message);
            $error->toLog()->toDebug()->toMessages();
            return false;
        }
        $output = $this->connection->real_escape_string((string) $value);
        if ($with_special_chars) {
            $output = str_replace('%', '\%', $output);
        }
        return $output;
    }

    /**
     * @return int
     */
    public function countAffected()
    {
        if($this->connection) {
            return $this->connection->affected_rows;
        }
    }

    /**
     * @return int
     */
    public function getLastId()
    {
        return $this->connection->insert_id;
    }

    public function getDBError()
    {
        return [
            'error_text' => mysqli_error($this->connection),
            'errno'      => mysqli_errno($this->connection),
        ];
    }
}
