<?php

/*
------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------  
*/

class ModelInstall extends Model
{
    public $error;

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validateSettings($data)
    {
        if (!$data['admin_path']) {
            $this->error['admin_path'] = 'Admin unique name is required!';
        } else {
            if (preg_match('/[^A-Za-z0-9_]/', $data['admin_path'])) {
                $this->error['admin_path'] = 'Admin unique name contains non-alphanumeric characters!';
            }
        }

        if (!$data['db_driver']) {
            $this->error['db_driver'] = 'Driver required!';
        }
        if (!$data['db_host']) {
            $this->error['db_host'] = 'Host required!';
        }

        if (!$data['db_user']) {
            $this->error['db_user'] = 'User required!';
        }

        if (!$data['db_name']) {
            $this->error['db_name'] = 'Database Name required!';
        }

        if (!$data['username']) {
            $this->error['username'] = 'Username required!';
        }

        if (!$data['password']) {
            $this->error['password'] = 'Password required!';
        }
        if ($data['password'] != $data['password_confirm']) {
            $this->error['password_confirm'] = 'Password does not match the confirm password!';
        }

        $pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';

        if (!preg_match($pattern, $data['email'])) {
            $this->error['email'] = 'Invalid E-Mail!';
        }

        if (!empty($data['db_prefix']) && preg_match('/[^A-Za-z0-9_]/', $data['db_prefix'])) {
            $this->error['db_prefix'] = 'DB prefix contains non-alphanumeric characters!';
        }

        if ($data['db_driver']
            && $data['db_host']
            && $data['db_user']
            && $data['db_password']
            && $data['db_name']
        ) {
            try {
                new ADB($data['db_driver'],
                    $data['db_host'],
                    $data['db_user'],
                    $data['db_password'],
                    $data['db_name']);
            } catch (AException $exception) {
                $this->error['warning'] = $exception->getMessage();
            }
        }

        if (!is_writable(DIR_ABANTECART.'system/config.php')) {
            $this->error['warning'] = 'Error: Could not write to config.php please check you have set the correct permissions on: '.DIR_ABANTECART.'system/config.php!';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function validateRequirements()
    {
        if (version_compare(phpversion(), MIN_PHP_VERSION, '<') == true) {
            $this->error['warning'] = 'Warning: You need to use PHP '.MIN_PHP_VERSION.' or above for AbanteCart to work!';
        }

        if (!ini_get('file_uploads')) {
            $this->error['warning'] = 'Warning: file_uploads needs to be enabled in PHP!';
        }

        if (ini_get('session.auto_start')) {
            $this->error['warning'] = 'Warning: AbanteCart will not work with session.auto_start enabled!';
        }

        if (!extension_loaded('mysql') && !extension_loaded('mysqli') && !extension_loaded('pdo_mysql')) {
            $this->error['warning'] = 'Warning: MySQL extension needs to be loaded for AbanteCart to work!';
        }

        if (!function_exists('simplexml_load_file')) {
            $this->error['warning'] = 'Warning: SimpleXML functions needs to be available in PHP!';
        }

        if (!extension_loaded('gd')) {
            $this->error['warning'] = 'Warning: GD extension needs to be loaded for AbanteCart to work!';
        }

        if (!extension_loaded('mbstring') || !function_exists('mb_internal_encoding')) {
            $this->error['warning'] = 'Warning: MultiByte String extension needs to be loaded for AbanteCart to work!';
        }
        if (!extension_loaded('zlib')) {
            $this->error['warning'] = 'Warning: ZLIB extension needs to be loaded for AbanteCart to work!';
        }
        if (!extension_loaded('openssl')) {
            $this->error['warning'] = 'Warning: OpenSSL extension needs to be loaded for AbanteCart to work!';
        }
        if (!extension_loaded('phar')) {
            $this->error['warning'] = 'Warning: PHAR extension needs to be loaded for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'system/config.php')) {
            $this->error['warning'] = 'Warning: config.php needs to be writable for AbanteCart to be installed!';
        }

        if (!is_writable(DIR_SYSTEM)) {
            $this->error['warning'] = 'Warning: System directory and all its children files/directories need to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_SYSTEM.'cache')) {
            $this->error['warning'] = 'Warning: Cache directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_SYSTEM.'logs')) {
            $this->error['warning'] = 'Warning: Logs directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'image')) {
            $this->error['warning'] = 'Warning: Image directory and all its children files/directories need to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'image/thumbnails')) {
            if (file_exists(DIR_ABANTECART.'image/thumbnails') && is_dir(DIR_ABANTECART.'image/thumbnails')) {
                $this->error['warning'] = 'Warning: image/thumbnails directory needs to be writable for AbanteCart to work!';
            } else {
                $result = mkdir(DIR_ABANTECART.'image/thumbnails', 0777, true);
                if ($result) {
                    chmod(DIR_ABANTECART.'image/thumbnails', 0777);
                    chmod(DIR_ABANTECART.'image', 0777);
                } else {
                    $this->error['warning'] = 'Warning: image/thumbnails does not exists!';
                }
            }
        }

        if (!is_writable(DIR_ABANTECART.'download')) {
            $this->error['warning'] = 'Warning: Download directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'extensions')) {
            $this->error['warning'] = 'Warning: Extensions directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'resources')) {
            $this->error['warning'] = 'Warning: Resources directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'admin/system')) {
            $this->error['warning'] = 'Warning: Admin/system directory needs to be writable for AbanteCart to work!';
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function configure($data)
    {
        if (!$data) {
            return false;
        }

        define('DB_PREFIX', $data['db_prefix']);

        $content = "<?php\n";
        $content .= "/**\n";
        $content .= "	AbanteCart, Ideal OpenSource Ecommerce Solution\n";
        $content .= "	http://www.AbanteCart.com\n";
        $content .= "	Copyright © 2011-".date('Y')." Belavier Commerce LLC\n\n";
        $content .= "	Released under the Open Software License (OSL 3.0)\n";
        $content .= "*/\n\n";
        $content .= "define('SERVER_NAME', '".getenv('SERVER_NAME')."');\n";
        $content .= "// Admin Section Configuration. You can change this value to any name. Will use ?s=name to access the admin\n";
        $content .= "define('ADMIN_PATH', '".$data['admin_path']."');\n\n";
        $content .= "// Database Configuration\n";
        $content .= "define('DB_DRIVER', '".$data['db_driver']."');\n";
        $content .= "define('DB_HOSTNAME', '".$data['db_host']."');\n";
        $content .= "define('DB_USERNAME', '".$data['db_user']."');\n";
        $content .= "define('DB_PASSWORD', '".$data['db_password']."');\n";
        $content .= "define('DB_DATABASE', '".$data['db_name']."');\n";
        $content .= "define('DB_PREFIX', '".DB_PREFIX."');\n";
        $content .= "\n";
        $content .= "define('CACHE_DRIVER', 'file');\n";
        $content .= "// Unique AbanteCart store ID\n";
        $content .= "define('UNIQUE_ID', '".md5(time())."');\n";
        $content .= "// Encryption key for protecting sensitive information. NOTE: Change of this key will cause a loss of all existing encrypted information!\n";
        $content .= "define('ENCRYPTION_KEY', '".randomWord(6)."');\n";

        $file = fopen(DIR_ABANTECART.'system/config.php', 'w');
        fwrite($file, $content);
        fclose($file);
        return null;
    }

    public function RunSQL($data)
    {
        $db = new ADB($data['db_driver'], $data['db_host'], $data['db_user'], $data['db_password'], $data['db_name']);

        $file = DIR_APP_SECTION.'abantecart_database.sql';
        if ($sql = file($file)) {
            $query = '';

            foreach ($sql as $line) {
                $tsl = trim($line);

                if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
                    $query .= $line;

                    if (preg_match('/;\s*$/', $line)) {
                        $query = str_replace("DROP TABLE IF EXISTS `ac_", "DROP TABLE IF EXISTS `".$data['db_prefix'], $query);
                        $query = str_replace("CREATE TABLE `ac_", "CREATE TABLE `".$data['db_prefix'], $query);
                        $query = str_replace("INSERT INTO `ac_", "INSERT INTO `".$data['db_prefix'], $query);
                        $query = str_replace("ON `ac_", "ON `".$data['db_prefix'], $query);

                        $db->query($query); //no silence mode! if error - will throw to exception
                        $query = '';
                    }
                }
            }

            $db->query("SET CHARACTER SET utf8;");
            $salt_key = genToken(8);
            $db->query(
                "INSERT INTO `".$data['db_prefix']."users`
				SET user_id = '1',
					user_group_id = '1',
					email = '".$db->escape($data['email'])."',
				    username = '".$db->escape($data['username'])."',
					salt = '".$db->escape($salt_key)."', 
					password = '".$db->escape(sha1($salt_key.sha1($salt_key.sha1($data['password']))))."',
				    status = '1',
				    date_added = NOW();");

            $db->query("UPDATE `".$data['db_prefix']."settings` SET value = '".$db->escape($data['email'])."' WHERE `key` = 'store_main_email'; ");
            $db->query("UPDATE `".$data['db_prefix']."settings` SET value = '".$db->escape(HTTP_ABANTECART)."' WHERE `key` = 'config_url'; ");
            if (defined('HTTPS') &&  HTTPS === true) {
                $db->query("UPDATE `".$data['db_prefix']."settings` SET value = '".$db->escape(HTTP_ABANTECART)."' WHERE `key` = 'config_ssl_url'; ");
                $db->query("UPDATE `".$data['db_prefix']."settings` SET value = '2' WHERE `key` = 'config_ssl'; ");
            }
            $db->query("UPDATE `".$data['db_prefix']."settings` SET value = '".$db->escape(genToken(16))."' WHERE `key` = 'task_api_key'; ");
            $db->query("INSERT INTO `".$data['db_prefix']."settings` SET `group` = 'config', `key` = 'install_date', value = NOW(); ");

            $db->query("UPDATE `".$data['db_prefix']."products` SET `viewed` = '0';");

            //process triggers
            //$this->create_triggers($db, $data['db_name']);

            //run destructor and close db-connection
            unset($db);
        }

        //clear cache dir in case of reinstall
        $cache = new ACache();
        $cache->setCacheStorageDriver('file');
        $cache->enableCache();
        $cache->remove('*');
    }

    /**
     * @param ADB    $db
     * @param string $database_name
     */
    private function create_triggers($db, $database_name)
    {
        $tables_sql = "
			SELECT DISTINCT TABLE_NAME 
		    FROM INFORMATION_SCHEMA.COLUMNS
		    WHERE COLUMN_NAME IN ('date_added')
		    AND TABLE_SCHEMA='".$db->escape($database_name)."'";

        $query = $db->query($tables_sql);
        foreach ($query->rows as $t) {
            $table_name = $t['TABLE_NAME'];
            $trigger_name = $table_name."_date_add_trg";

            $trigger_checker = $db->query("SELECT TRIGGER_NAME
								FROM information_schema.triggers
								WHERE TRIGGER_SCHEMA = '".$db->escape($database_name)."' AND TRIGGER_NAME = '".$db->escape($trigger_name)."'");
            if (!$query->row[0]) {
                //create trigger
                $sql = "
				CREATE TRIGGER `".$db->escape($trigger_name)."` BEFORE INSERT ON `".$db->escape($table_name)."` FOR EACH ROW
				BEGIN
		    		SET NEW.date_added = NOW();
				END;
				";
                $db->query($sql);
            }
        }
    }

    /**
     * @param Registry $registry
     *
     * @return null
     */
    public function loadDemoData($registry)
    {
        $db = $registry->get('db');
        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARACTER SET utf8");

        $file = DIR_APP_SECTION.'abantecart_sample_data.sql';

        if ($sql = file($file)) {
            $query = '';

            foreach ($sql as $line) {
                $tsl = trim($line);

                if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
                    $query .= $line;

                    if (preg_match('/;\s*$/', $line)) {
                        $query = str_replace("DROP TABLE IF EXISTS `ac_", "DROP TABLE IF EXISTS `".DB_PREFIX, $query);
                        $query = str_replace("CREATE TABLE `ac_", "CREATE TABLE `".DB_PREFIX, $query);
                        $query = str_replace("INSERT INTO `ac_", "INSERT INTO `".DB_PREFIX, $query);

                        $result = $db->query($query);

                        if (!$result || $db->error) {
                            die($db->error.'<br>'.$query);
                        }

                        $query = '';
                    }
                }
            }
            $db->query("SET CHARACTER SET utf8");
        }
        //clear earlier created cache by AConfig and ALanguage classes in previous step
        $cache = new ACache();
        $cache->setCacheStorageDriver('file');
        $cache->enableCache();
        $cache->remove('*');
        return null;
    }

    public function getLanguages()
    {
        $query = $this->db->query("SELECT *
                                    FROM ".DB_PREFIX."languages
                                    ORDER BY sort_order, name");
        $language_data = array();

        foreach ($query->rows as $result) {
            $language_data[$result['code']] = array(
                'language_id' => $result['language_id'],
                'name'        => $result['name'],
                'code'        => $result['code'],
                'locale'      => $result['locale'],
                'directory'   => $result['directory'],
                'filename'    => $result['filename'],
                'sort_order'  => $result['sort_order'],
                'status'      => $result['status'],
            );
        }

        return $language_data;
    }
}
