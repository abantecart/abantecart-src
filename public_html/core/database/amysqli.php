<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
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

    private $hostname = '', $username = '', $password = '', $database = '';
    private $reconnect_cnt = 0;
    const MAX_RECONNECT_CNT = 3;

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param bool   $new_link
     *
     * @throws AException
     */
    public function __construct($hostname, $username, $password, $database, $new_link = false)
    {
        if (!$this->reconnect_cnt) {
            $this->hostname = $hostname;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;
        }

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
        try {
            $timezone = date_default_timezone_get();
            if ($timezone) {
                $connection->query("SET time_zone='".$timezone."';");
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $this->registry = Registry::getInstance();
        $this->connection = $connection;
    }

    /**
     * @param string $sql
     * @param bool   $noexcept
     *
     * @return bool|db_result_meta
     * @throws AException
     */
    public function query($sql, $noexcept = false)
    {
        $time_start = microtime(true);
        try {
            $result = $this->connection->query($sql);
        } catch (Exception|mysqli_sql_exception $e) {
            // reconnect if connection lost and not exceeded max reconnect count "Mysql Gone Away issue"
            if ($e->getCode() == 2006 && $this->reconnect_cnt < self::MAX_RECONNECT_CNT) {
                try {
                    $this->__construct($this->hostname, $this->username, $this->password, $this->database);
                    $result = $this->query($sql, $noexcept);
                    $message = "Reconnected to database {$this->database} after Mysql connection has dropped";
                    $error = new AError($message);
                    $error->toDebug();
                } catch (Exception $exc) {
                    $this->reconnect_cnt++;
                    $result = false;
                    if (!$noexcept) {
                        $this->processException($exc, $sql);
                    }
                }
            } else {
                $result = false;
                if (!$noexcept) {
                    $this->processException($e, $sql);
                }
            }
        }

        if ($result) {
            $this->reconnect_cnt = 0;
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
                    $data[$i] = (array)$row;
                    $i++;
                }

                $query = new db_result_meta();
                $query->row = $data[0] ?? [];
                $query->rows = $data;
                $query->num_rows = (int)$result->num_rows;
                unset($data);
                return $query;
            } else {
                return true;
            }
        } else {
            $this->error = 'SQL Error: '.mysqli_error($this->connection)
                .'\nError No: '.mysqli_errno($this->connection)
                .'\nSQL: '.$sql;
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

    protected function processException($e, $sql)
    {
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

    /**
     * @param string $value
     * @param bool   $with_special_chars
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
        $output = $this->connection->real_escape_string((string)$value);
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
        if ($this->connection) {
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

    /**
     * @return string
     */
    public function getSqlCalcTotalRows()
    {
        return 'SQL_CALC_FOUND_ROWS';
    }

    /**
     * @return false|int
     */
    public function getTotalNumRows()
    {
        $result = $this->connection->query('select found_rows() as total;');
        if ($result !== false) {
            $row = (array)$result->fetch_object();
            return (int)$row['total'];
        }
        return false;
    }

    public function getDBError()
    {
        return [
            'error_text' => mysqli_error($this->connection),
            'errno'      => mysqli_errno($this->connection),
        ];
    }
}
