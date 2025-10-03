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
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

class ModelInstall extends Model
{
    public $errors;

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validateSettings($data)
    {
        if (!$data['admin_path']) {
            $this->errors['admin_path'] = 'Admin unique name is required!';
        } else {
            if (preg_match('/[^A-Za-z0-9_]/', $data['admin_path'])) {
                $this->errors['admin_path'] = 'Admin unique name contains non-alphanumeric characters!';
            }
        }

        if (!$data['db_driver']) {
            $this->errors['db_driver'] = 'Driver required!';
        }
        if (!$data['db_host']) {
            $this->errors['db_host'] = 'Host required!';
        }

        if (!$data['db_user']) {
            $this->errors['db_user'] = 'User required!';
        }

        if (is_int(strpos($data['db_password'], '\\'))) {
            $this->errors['db_password'] = 'Database password cannot contains forward slashes!';
        }

        if (!$data['db_name']) {
            $this->errors['db_name'] = 'Database Name required!';
        }

        if (!$data['username']) {
            $this->errors['username'] = 'Username required!';
        }

        if (!$data['password']) {
            $this->errors['password'] = 'Password required!';
        }
        if ($data['password'] != $data['password_confirm']) {
            $this->errors['password_confirm'] = 'Password does not match the confirm password!';
        }

        if (!preg_match(EMAIL_REGEX_PATTERN, $data['email'])) {
            $this->errors['email'] = 'Invalid E-Mail!';
        }

        if (!empty($data['db_prefix']) && preg_match('/[^A-Za-z0-9_]/', $data['db_prefix'])) {
            $this->errors['db_prefix'] = 'DB prefix contains non-alphanumeric characters!';
        }

        if ($data['db_driver']
            && $data['db_host']
            && $data['db_user']
            && $data['db_password']
            && $data['db_name']
        ) {
            try {
                new ADB(
                    $data['db_driver'],
                    $data['db_host'],
                    $data['db_user'],
                    $data['db_password'],
                    $data['db_name'],
                    (int)$data['db_port'] ?: NULL,
                    ['table_prefix' => $data['db_prefix']]
                );
            } catch (Exception|Error $exception) {
                $this->errors['warning'] = $exception->getMessage();
            }
        }

        if (!is_writable(DIR_ABANTECART . 'system/config.php')) {
            $this->errors['warning'] = 'Error: Could not write to config.php please check you have '
                . 'set the correct permissions on: ' . DIR_ABANTECART . 'system/config.php!';
        }

        if ($data['with-sample-data']) {
            $sampleDataFile = '';
            if (!is_file($data['with-sample-data'])) {
                if (is_file(DIR_APP_SECTION . $data['with-sample-data'])) {
                    $sampleDataFile = DIR_APP_SECTION . $data['with-sample-data'];
                }
            } else {
                $sampleDataFile = $data['with-sample-data'];
            }
            if (!$sampleDataFile) {
                $this->errors['with-sample-data'] = 'Sample data file not found!';
            }
        }

        $extDirs = $this->extensions->getExtensionsDir();
        if ($data['template'] != 'default' && !in_array($data['template'], $extDirs)) {
            $this->errors['template'] = 'Invalid template!';
        }

        return (!$this->errors);
    }

    /**
     * @return bool
     */
    public function validateRequirements()
    {
        $result = checkPhpConfiguration();
        foreach ($result as $name => $r) {
            $this->errors[$name] = 'Warning: ' . $r['body'];
        }

        if (!extension_loaded('openssl')) {
            $this->errors['openssl'] = 'Warning: OpenSSL extension needs to be loaded for AbanteCart to work!';
        }
        if (!extension_loaded('phar')) {
            $this->errors['phar'] = 'Warning: PHAR extension needs to be loaded for AbanteCart to work!';
        }

        $f = fopen(DIR_ABANTECART . 'system/config.php', 'w');
        if ($f) {
            fclose($f);
        }
        if (!is_writable(DIR_ABANTECART . 'system/config.php')) {
            $this->errors['warning'] = 'Warning: config.php needs to be writable for AbanteCart to be installed!';
        }

        if (!is_writable(DIR_SYSTEM)) {
            $this->errors['warning'] = 'Warning: System directory and all its children files/directories'
                . ' need to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_SYSTEM . 'cache')) {
            $this->errors['warning'] = 'Warning: Cache directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_SYSTEM . 'logs')) {
            $this->errors['warning'] = 'Warning: Logs directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART . 'image')) {
            $this->errors['warning'] =
                'Warning: Image directory and all its children files/directories need to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART . 'image/thumbnails')) {
            if (file_exists(DIR_ABANTECART . 'image/thumbnails') && is_dir(DIR_ABANTECART . 'image/thumbnails')) {
                $this->errors['warning'] =
                    'Warning: image/thumbnails directory needs to be writable for AbanteCart to work!';
            } else {
                $result = mkdir(DIR_ABANTECART . 'image/thumbnails', 0777, true);
                if ($result) {
                    chmod(DIR_ABANTECART . 'image/thumbnails', 0777);
                    chmod(DIR_ABANTECART . 'image', 0777);
                } else {
                    $this->errors['warning'] = 'Warning: image/thumbnails does not exists!';
                }
            }
        }

        if (!is_writable(DIR_ABANTECART . 'download')) {
            $this->errors['warning'] = 'Warning: Download directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART . 'extensions')) {
            $this->errors['warning'] = 'Warning: Extensions directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART . 'resources')) {
            $this->errors['warning'] = 'Warning: Resources directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART . 'admin/system')) {
            $this->errors['warning'] = 'Warning: Admin/system directory needs to be writable for AbanteCart to work!';
        }

        return (!$this->errors);
    }

    public function configure($data)
    {
        if (!$data) {
            return false;
        }
        if (!defined('DB_PREFIX')) {
            define('DB_PREFIX', $data['db_prefix']);
        }

        $content = "<?php" . PHP_EOL;
        $content .= "/**" . PHP_EOL;
        $content .= "   AbanteCart, Ideal OpenSource Ecommerce Solution" . PHP_EOL;
        $content .= "   https://www.AbanteCart.com" . PHP_EOL;
        $content .= "   Copyright © 2011-" . date('Y') . " Belavier Commerce LLC" . PHP_EOL . PHP_EOL;
        $content .= "   Released under the Open Software License (OSL 3.0)" . PHP_EOL;
        $content .= "*/" . PHP_EOL . PHP_EOL;
        $content .= "const SERVER_NAME = '" . getenv('SERVER_NAME') . "';" . PHP_EOL;
        $content .= "// Admin Section Configuration. You can change this value to any name. Will use ?s=name to access the admin" . PHP_EOL;
        $content .= "const ADMIN_PATH = '" . $data['admin_path'] . "';" . PHP_EOL . PHP_EOL;
        $content .= "// Database Configuration" . PHP_EOL;
        $content .= "const DB_DRIVER = '" . $data['db_driver'] . "';" . PHP_EOL;
        $content .= "const DB_HOSTNAME = '" . $data['db_host'] . "';" . PHP_EOL;
        if ($data['db_port']) {
            $content .= "const DB_PORT = " . (int)$data['db_port'] . ";" . PHP_EOL;
        }
        $content .= "const DB_USERNAME = '" . $data['db_user'] . "';" . PHP_EOL;
        $content .= "const DB_PASSWORD = '" . $data['db_password'] . "';" . PHP_EOL;
        $content .= "const DB_DATABASE = '" . $data['db_name'] . "';" . PHP_EOL;
        $content .= "const DB_PREFIX = '" . DB_PREFIX . "';" . PHP_EOL . PHP_EOL;
        $content .= "const CACHE_DRIVER = 'file';" . PHP_EOL;
        $content .= "// Unique AbanteCart store ID" . PHP_EOL;
        $content .= "const UNIQUE_ID = '" . md5(time()) . "';" . PHP_EOL;
        $content .= "// Encryption key for protecting sensitive information. NOTE: Change of this key will cause a loss of all existing encrypted information!" . PHP_EOL;
        $content .= "const ENCRYPTION_KEY = '" . randomWord(6) . "';" . PHP_EOL;
        $content .= PHP_EOL;
        $content .= "// details about allowed DSN settings  https://symfony.com/doc/6.0/mailer.html#transport-setup" . PHP_EOL;
        $content .= "/*" . PHP_EOL;
        $content .= "const MAILER = [" . PHP_EOL;
        $content .= "    //'dsn' => null," . PHP_EOL;
        $content .= "    // OR" . PHP_EOL;
        $content .= "    'protocol' => 'smtp', // or ses+smtp, gmail+smtp, mandrill+smtp, mailgun+smtp, mailjet+smtp, postmark+smtp, sendgrid+smtp, sendinblue+smtp, ohmysmtp+smtp" . PHP_EOL;
        $content .= "    //we use \"username\" also as ID, KEY, API_TOKEN, ACCESS_KEY" . PHP_EOL;
        $content .= "    'username' => 'merchant@yourdomain.com'," . PHP_EOL;
        $content .= "    'password' => '****super-secret-password****'," . PHP_EOL;
        $content .= "    'host'     => 'your-hostname'," . PHP_EOL;
        $content .= "    'port'     => 465 //or 587 etc" . PHP_EOL;
        $content .= "];" . PHP_EOL;
        $content .= "*/" . PHP_EOL;

        $file = fopen(DIR_ABANTECART . 'system/config.php', 'w');
        fwrite($file, $content);
        fclose($file);
        return null;
    }

    public function RunSQL($data)
    {
        $db = new ADB(
            $data['db_driver'],
            $data['db_host'],
            $data['db_user'],
            $data['db_password'],
            $data['db_name'],
            $data['db_port']?:null,
            ['table_prefix' => $data['db_prefix']]
        );

        $file = DIR_APP_SECTION . 'abantecart_database.sql';
        if ($sql = file($file)) {
            try {
                //and check is InnoDb supported
                $engines = $db->query("SHOW ENGINES");
                $engines = array_map('strtolower', array_column($engines->rows, 'Engine'));
                if (!in_array('innodb', $engines)) {
                    throw new Exception(
                        'InnoDB Engine of your database-server required for '
                        . 'AbanteCart to work properly! Please contact your system administrator or host service provider.'
                    );
                }

                $query = '';
                foreach ($sql as $line) {
                    $tsl = trim($line);

                    if (!str_starts_with($tsl, "--") && !str_starts_with($tsl, '#')) {
                        $query .= $line;

                        if (preg_match('/;\s*$/', $line)) {
                            $query = str_replace("`ac_", "`" . $data['db_prefix'], $query);
                            $db->query($query); //no silence mode! if error - will throw to exception
                            $query = '';
                        }
                    }
                }

                $db->query("SET CHARACTER SET utf8mb4;");
                $salt_key = genToken(8);
                $db->query(
                    "INSERT INTO `" . $data['db_prefix'] . "users`
                    SET user_id = '1',
                        user_group_id = '1',
                        email = '" . $db->escape($data['email']) . "',
                        username = '" . $db->escape($data['username']) . "',
                        salt = '" . $db->escape($salt_key) . "', 
                        password = '" . $db->escape(passwordHash($data['password'], $salt_key)) . "',
                        status = '1',
                        date_added = NOW();"
                );

                $this->session->data['username'] = $data['username'];

                $db->query(
                    "UPDATE `" . $data['db_prefix'] . "settings` 
                    SET value = '" . $db->escape($data['email']) . "' 
                    WHERE `key` = 'store_main_email'; "
                );
                $db->query(
                    "UPDATE `" . $data['db_prefix'] . "settings` 
                    SET value = '" . $db->escape(HTTP_ABANTECART) . "' 
                    WHERE `key` = 'config_url'; "
                );
                if (defined('HTTPS') && HTTPS === true) {
                    $db->query(
                        "UPDATE `" . $data['db_prefix'] . "settings` 
                        SET value = '" . $db->escape(HTTP_ABANTECART) . "' 
                        WHERE `key` = 'config_ssl_url'; "
                    );
                    $db->query(
                        "UPDATE `" . $data['db_prefix'] . "settings` 
                        SET value = '2' 
                        WHERE `key` = 'config_ssl'; "
                    );
                }
                $db->query(
                    "UPDATE `" . $data['db_prefix'] . "settings` 
                    SET value = '" . $db->escape(genToken(16)) . "' 
                    WHERE `key` = 'task_api_key'; "
                );
                $db->query(
                    "INSERT INTO `" . $data['db_prefix'] . "settings` 
                    SET `group` = 'config', 
                        `key` = 'install_date', 
                        value = NOW(); "
                );

                $db->query("UPDATE `" . $data['db_prefix'] . "products` SET `viewed` = '0';");

                //run destructor and close db-connection
                unset($db);
            } catch (Exception $e) {
                exit($e->getMessage());
            }
        }

        //clear cache dir in case of reinstall
        $cache = new ACache();
        $cache->setCacheStorageDriver('file');
        $cache->enableCache();
        $cache->remove('*');
    }

    /**
     * @param Registry $registry
     *
     * @return null
     */
    public function loadDemoData($registry, $file = '')
    {
        /** @var ADB $db */
        $db = $registry->get('db');
        try {
            $db->query("SET NAMES 'utf8mb4';");
            $db->query("SET CHARACTER SET utf8mb4;");
            $file = $file ?: DIR_APP_SECTION . 'abantecart_sample_data.sql';
            if (!is_file($file)) {
                return;
            } else {
                $sql = file($file);
            }
            $query = '';

            foreach ($sql as $line) {
                $tsl = trim($line);
                if (($line != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
                    $query .= $line;
                    if (preg_match('/;\s*$/', $line)) {
                        $query = str_replace("`ac_", "`" . DB_PREFIX, $query);
                        $db->query($query);
                        $query = '';
                    }
                }
            }
            $db->query("SET CHARACTER SET utf8mb4;");

            //clear earlier created cache by AConfig and ALanguage classes in previous step
            $cache = new ACache();
            $cache->setCacheStorageDriver('file');
            $cache->enableCache();
            $cache->remove('*');
        } catch (Exception $e) {
            exit(nl2br($e->getMessage()));
        }
        return null;
    }

    /**
     * @param array|null $options
     * @return void
     * @throws AException
     * @throws DOMException
     */
    public function preInstallExtensions(?array $options = [])
    {
        $db = Registry::getInstance()->get('db');
        //install default template anyway
        $layout = new ALayoutManager('default');
        $file = DIR_ABANTECART . DS . 'storefront' . DS . 'view' . DS . 'default' . DS . 'layout.xml';
        $layout->loadXml(['file' => $file]);
        unset($layout);

        $ext = trim($options['install_step_data']['template']);
        if ($ext && $ext != 'default') {
            $template = new ExtensionUtils($ext);
            $em = new AExtensionManager();
            $em->install($ext, $template->getConfig());
            if ($em->errors) {
                throw new Exception(implode("\n", $em->errors));
            }
            $em->editSetting($ext, [$ext . '_status' => 1]);
            $db->query(
                "UPDATE " . $db->table("settings") . " 
                SET `value` = '" . $db->escape($ext) . "' 
                WHERE `key` = 'config_storefront_template'"
            );
        }

        //preinstall extensions for example PageBuilder
        $preinstall = $options['install_step_data']['install_extensions'] ?: ['page_builder'];
        foreach ($preinstall as $pre) {
            $installSql = DIR_ABANTECART . DS . 'extensions' . DS . $pre . DS . 'install.sql';
            if (is_file($installSql) && is_readable($installSql)) {
                if ($sql = file($installSql)) {
                    $query = '';
                    foreach ($sql as $line) {
                        $tsl = trim($line);
                        if (!str_starts_with($tsl, "--") && !str_starts_with($tsl, '#')) {
                            $query .= $line;
                            if (preg_match('/;\s*$/', $line)) {
                                $query = str_replace("`ac_", "`" . $db->tablePrefix(), $query);
                                $db->query($query); //no silence mode! if error - will throw to exception
                                $query = '';
                            }
                        }
                    }
                }
            }
            $installPhp = DIR_ABANTECART . DS . 'extensions' . DS . $pre . DS . 'install.php';
            if (is_file($installPhp) && is_readable($installPhp)) {
                require_once $installPhp;
            }
        }
    }

    public function getLanguages()
    {
        $result = $this->db?->query(
            "SELECT *
            FROM " . $this->db->table("languages") . "
            ORDER BY `sort_order`, `name`"
        );
        $language_data = [];
        if ($result) {
            foreach ($result->rows as $row) {
                $language_data[$row['code']] = [
                    'language_id' => $row['language_id'],
                    'name'        => $row['name'],
                    'code'        => $row['code'],
                    'locale'      => $row['locale'],
                    'directory'   => $row['directory'],
                    'filename'    => $row['filename'],
                    'sort_order'  => $row['sort_order'],
                    'status'      => $row['status'],
                ];
            }
        }

        return $language_data;
    }
}
