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

/**
 * do the phpdoc-trick with meta-class for query result
 * @property array $row
 * @property array $rows
 * @property int $num_rows
 */
class db_result_meta extends stdClass
{
}


final class ADB
{
    // property for hook calls
    /** @see ExtensionsApi::__ExtensionsApiCall() */
    public $ExtensionsApi;
    /**
     * @var AMySQLi
     */
    private $driver;
    private $database;
    private $table_prefix;
    public $error = '';
    public $registry;

    /**
     * @param string $driver
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int|null $port
     * @param array|null $options
     * @throws AException
     */
    public function __construct(
        string $driver,
        string $hostname,
        string $username,
        string $password,
        string $database,
        ?int   $port = 3306,
        ?array $options = []
    )
    {
        $driverDir = $options['driver_dir'] ?: DIR_DATABASE;
        $this->table_prefix = $options['table_prefix'] ?: (defined('DB_PREFIX') ? DB_PREFIX : '');

        $filename = $driverDir . $driver . '.php';
        if (file_exists($filename)) {
            /** @noinspection PhpIncludeInspection */
            require_once($filename);
        } else {
            throw new AException(AC_ERR_MYSQL, 'Error: Could not load database driver file ' . $filename . '!');
        }

        try {
            $this->driver = new $driver($hostname, $username, $password, $database, $port);
        } catch (Exception|Error $e) {
            $err = new AError(
                'Cannot establish database connection to ' . $database . ' using ' . $username . '@' . $hostname
                . "\n" . $e->getMessage()
            );
            $err->toLog();
            throw $e;
        }
        $this->database = $database;
        $this->registry = Registry::getInstance();
    }

    /**
     * @param string $sql
     * @param bool $noexcept
     *
     * @return bool|db_result_meta
     * @throws AException
     */
    public function query(string $sql, ?bool $noexcept = false)
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
    public function table(string $table_name)
    {
        //detect if encryption is enabled
        $postfix = '';
        if (is_object($this->registry->get('dcrypt'))) {
            $postfix = $this->registry->get('dcrypt')->postfix($table_name);
        }
        return $this->table_prefix . $table_name . $postfix;
    }

    /**
     * @param string $sql
     * @param bool $noexcept
     *
     * @return bool|db_result_meta
     * @throws AException
     */
    public function _query(string $sql, ?bool $noexcept = false)
    {
        return $this->driver->query($sql, $noexcept);
    }

    /**
     * @param mixed $value
     * @param bool $with_special_chars
     * @return false|string
     * @throws AException
     */
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
     * @param string $filename
     *
     * @return null
     * @throws AException
     */
    public function performSql(string $filename)
    {

        if ($sql = file($filename)) {
            $query = '';
            foreach ($sql as $line) {
                $tsl = trim($line);
                if ($sql != '' && !str_starts_with($tsl, "--") && !str_starts_with($tsl, '#')) {
                    $query .= $line;
                    if (preg_match('/;\s*$/', $line)) {
                        $query = str_replace("`ac_", "`" . $this->table_prefix, $query);
                        if (!$query) {
                            continue;
                        }
                        $result = $this->_query($query, true);
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

    public function stringOrNull($value)
    {
        return (string)$value !== '' ? "'" . $this->escape($value) . "'" : "NULL";
    }

    public function intOrNull($value)
    {
        $value = (int)$value;
        return $value ?: "NULL";
    }

    /**
     * @return string
     */
    public function dbName()
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function tablePrefix()
    {
        return $this->table_prefix;
    }
}
