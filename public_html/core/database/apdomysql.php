<?php
/** @noinspection PhpComposerExtensionStubsInspection */
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

final class APDOMySQL
{
    /**
     * @var PDO
     */
    private $connection = null;
    /**
     * @var PDOStatement
     */
    private $statement;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var string
     */
    public $error;

    public function __construct($hostname, $username, $password, $database, $port = 3306)
    {
        $port = (int)$port ?: 3306;
        try {
            $this->connection = new PDO(
                "mysql:host=" . $hostname . ";port=" . $port . ";dbname=" . $database,
                $username,
                $password,
                [PDO::ATTR_PERSISTENT => true]
            );
            $this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        } catch (Exception|Error $e) {
            $err = new AError(
                'Cannot establish database connection to '
                . $database . ' using ' . $username . '@' . $hostname.PHP_EOL
                . 'Error: ' . $e->getMessage()
            );
            $err->toLog();
            throw new AException(AC_ERR_MYSQL, 'Cannot establish database connection. Check your database connection settings.');
        }
        $this->registry = Registry::getInstance();

        //check minimal requirements
        $serverVersion = array_map(
            'strtolower',
            explode("-", $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION))
        );

        if (!$serverVersion[1]) {
            $serverVersion[1] = 'mysql';
        }

        if (version_compare(MIN_DB_VERSIONS[$serverVersion[1]], $serverVersion[0], '>')) {
            throw new Exception(
                MIN_DB_VERSIONS[$serverVersion[1]] . '+ of ' . $serverVersion[1] . '-server required for '
                . 'AbanteCart to work properly! Please contact your system administrator or host service provider.'
            );
        }

        $this->connection->exec("SET NAMES 'utf8mb4'");
        $this->connection->exec("SET CHARACTER SET utf8mb4");
        $this->connection->exec("SET CHARACTER_SET_CONNECTION=utf8mb4");
        $this->connection->exec("SET SQL_MODE = ''");
        $this->connection->exec("SET session wait_timeout=60;");
        $this->connection->exec("SET SESSION SQL_BIG_SELECTS=1;");
        try {
            $timezone = date_default_timezone_get();
            if ($timezone) {
                $timezone = $timezone == 'UTC' ? 'Europe/London' : $timezone;
                $this->connection->query("SET time_zone='" . $timezone . "';");
            }
        } catch (Exception|Error $e) {
            error_log(__METHOD__ . ": " . $e->getMessage());
        }
    }

    public function prepare($sql)
    {
        $this->statement = $this->connection->prepare($sql);
    }

    public function bindParam($parameter, $variable, $data_type = PDO::PARAM_STR, $length = 0)
    {
        if ($length) {
            $this->statement->bindParam($parameter, $variable, $data_type, $length);
        } else {
            $this->statement->bindParam($parameter, $variable, $data_type);
        }
    }

    public function query($sql, $noexcept = false, $params = [])
    {
        if (!$noexcept) {
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        $this->statement = $this->connection->prepare($sql);
        $result = false;

        $time_start = microtime(true);
        try {
            if ($this->statement && $this->statement->execute($params)) {
                $data = [];
                if ($this->statement->columnCount()) {
                    while ($row = $this->statement->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = $row;
                    }

                    $result = new db_result_meta();
                    $result->row = $data[0] ?? [];
                    $result->rows = $data;
                    $result->num_rows = $this->statement->rowCount();
                }
            }
        } catch (PDOException $e) {
            $this->error = 'SQL Error: ' . $e->getMessage() . '<br />Error No: ' . $e->getCode() . '<br />SQL:' . $sql;
            if ($noexcept) {
                return false;
            } else {
                $dbg = debug_backtrace();
                $this->error .= "PHP call stack:\n";
                foreach ($dbg as $k => $d) {
                    $this->error .= "#" . $k . " " . $d['file'] . ':' . $d['line'] . "\n";
                }
                throw new AException(AC_ERR_MYSQL, $this->error);
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

        if (!$result) {
            $result = new stdClass();
            $result->row = [];
            $result->rows = [];
            $result->num_rows = 0;
        }
        return $result;
    }

    public function escape($value, $with_special_chars = false)
    {
        if (is_array($value)) {
            $dump = var_export($value, true);
            $backtrace = debug_backtrace();
            $dump .= ' (file: ' . $backtrace[1]['file'] . ' line ' . $backtrace[1]['line'] . ')';
            $message = 'aPDOMySQL class error: Try to escape non-string value: ' . $dump;
            $error = new AError($message);
            $error->toLog()->toDebug();
            return false;
        }

        $search = ["\\", "\0", "\n", "\r", "\x1a", "'", '"'];
        $replace = ["\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'];
        $output = str_replace($search, $replace, $value);
        if ($with_special_chars) {
            $output = str_replace('%', '\%', $output);
        }
        return $output;
    }

    public function countAffected()
    {
        if ($this->statement) {
            return $this->statement->rowCount();
        } else {
            return 0;
        }
    }

    public function getLastId()
    {
        return $this->connection->lastInsertId();
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
        $statement = $this->connection->prepare('select found_rows() as total;');
        if (!$statement) {
            return false;
        }
        $statement->execute();
        $row = (array)$statement->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

    public function __destruct()
    {
        $this->connection = null;
    }

    public function getTextDBError()
    {
        return [
            'error_text' => '',
            'errno'      => '',
        ];
    }
}
