<?php

/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------  
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

        if (is_int(strpos($data['db_password'],'\\'))) {
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
                    $data['db_name']
                );
            } catch (Exception $exception) {
                $this->errors['warning'] = $exception->getMessage();
            }
        }

        if (!is_writable(DIR_ABANTECART.'system/config.php')) {
            $this->errors['warning'] = 'Error: Could not write to config.php please check you have '
                .'set the correct permissions on: '.DIR_ABANTECART.'system/config.php!';
        }

        if ($data['with-sample-data']) {
            $sampleDataFile = '';
            if(!is_file($data['with-sample-data'])){
                if(is_file(DIR_APP_SECTION.$data['with-sample-data'])){
                    $sampleDataFile = DIR_APP_SECTION.$data['with-sample-data'];
                }
            }else{
                $sampleDataFile = $data['with-sample-data'];
            }
            if(!$sampleDataFile) {
                $this->errors['with-sample-data'] = 'Sample data file not found!';
            }
        }

        return (!$this->errors);
    }

    /**
     * @return bool
     */
    public function validateRequirements()
    {
        $result = checkPhpConfiguration();
        foreach($result as $name => $r){
            $this->errors[$name] = 'Warning: '.$r['body'];
        }

        if (!extension_loaded('openssl')) {
            $this->errors['openssl'] = 'Warning: OpenSSL extension needs to be loaded for AbanteCart to work!';
        }
        if (!extension_loaded('phar')) {
            $this->errors['phar'] = 'Warning: PHAR extension needs to be loaded for AbanteCart to work!';
        }

        $f = fopen(DIR_ABANTECART.'system/config.php','w');
        fclose($f);
        if (!is_writable(DIR_ABANTECART.'system/config.php')) {
            $this->errors['warning'] = 'Warning: config.php needs to be writable for AbanteCart to be installed!';
        }

        if (!is_writable(DIR_SYSTEM)) {
            $this->errors['warning'] = 'Warning: System directory and all its children files/directories'
                                    .' need to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_SYSTEM.'cache')) {
            $this->errors['warning'] = 'Warning: Cache directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_SYSTEM.'logs')) {
            $this->errors['warning'] = 'Warning: Logs directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'image')) {
            $this->errors['warning'] =
                'Warning: Image directory and all its children files/directories need to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'image/thumbnails')) {
            if (file_exists(DIR_ABANTECART.'image/thumbnails') && is_dir(DIR_ABANTECART.'image/thumbnails')) {
                $this->errors['warning'] =
                    'Warning: image/thumbnails directory needs to be writable for AbanteCart to work!';
            } else {
                $result = mkdir(DIR_ABANTECART.'image/thumbnails', 0777, true);
                if ($result) {
                    chmod(DIR_ABANTECART.'image/thumbnails', 0777);
                    chmod(DIR_ABANTECART.'image', 0777);
                } else {
                    $this->errors['warning'] = 'Warning: image/thumbnails does not exists!';
                }
            }
        }

        if (!is_writable(DIR_ABANTECART.'download')) {
            $this->errors['warning'] = 'Warning: Download directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'extensions')) {
            $this->errors['warning'] = 'Warning: Extensions directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'resources')) {
            $this->errors['warning'] = 'Warning: Resources directory needs to be writable for AbanteCart to work!';
        }

        if (!is_writable(DIR_ABANTECART.'admin/system')) {
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

        $content = "<?php\n";
        $content .= "/**\n";
        $content .= "   AbanteCart, Ideal OpenSource Ecommerce Solution\n";
        $content .= "   https://www.AbanteCart.com\n";
        $content .= "   Copyright © 2011-".date('Y')." Belavier Commerce LLC\n\n";
        $content .= "   Released under the Open Software License (OSL 3.0)\n";
        $content .= "*/\n\n";
        $content .= "const SERVER_NAME = '".getenv('SERVER_NAME')."';\n";
        $content .= "// Admin Section Configuration. You can change this value to any name. Will use ?s=name to access the admin\n";
        $content .= "const ADMIN_PATH = '".$data['admin_path']."';\n\n";
        $content .= "// Database Configuration\n";
        $content .= "const DB_DRIVER = '".$data['db_driver']."';\n";
        $content .= "const DB_HOSTNAME = '".$data['db_host']."';\n";
        $content .= "const DB_USERNAME = '".$data['db_user']."';\n";
        $content .= "const DB_PASSWORD = '".$data['db_password']."';\n";
        $content .= "const DB_DATABASE = '".$data['db_name']."';\n";
        $content .= "const DB_PREFIX = '".DB_PREFIX."';\n";
        $content .= "\n";
        $content .= "const CACHE_DRIVER = 'file';\n";
        $content .= "// Unique AbanteCart store ID\n";
        $content .= "const UNIQUE_ID = '".md5(time())."';\n";
        $content .= "// Encryption key for protecting sensitive information. NOTE: Change of this key will cause a loss of all existing encrypted information!\n";
        $content .= "const ENCRYPTION_KEY = '".randomWord(6)."';\n";
        $content .= "
// details about allowed DSN settings  https://symfony.com/doc/6.0/mailer.html#transport-setup
/*
const MAILER = [
    //'dsn' => null,
    // OR
    'protocol' => 'smtp', // or ses+smtp, gmail+smtp, mandrill+smtp, mailgun+smtp, mailjet+smtp, postmark+smtp, sendgrid+smtp, sendinblue+smtp, ohmysmtp+smtp
    //we use \"username\" also as ID, KEY, API_TOKEN, ACCESS_KEY
    'username' => 'merchant@yourdomain.com',
    'password' => '****super-secret-password****',
    'host'     => 'your-hostname',
    'port'     => 465 //or 587 etc
];\n*/";

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
                        $query = str_replace(
                            "DROP TABLE IF EXISTS `ac_", "DROP TABLE IF EXISTS `".$data['db_prefix'], $query
                        );
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
                    date_added = NOW();"
            );

            $db->query(
                "UPDATE `".$data['db_prefix']."settings` 
                SET value = '".$db->escape($data['email']) ."' 
                WHERE `key` = 'store_main_email'; "
            );
            $db->query(
                "UPDATE `".$data['db_prefix']."settings` 
                SET value = '".$db->escape(HTTP_ABANTECART)."' 
                WHERE `key` = 'config_url'; "
            );
            if (defined('HTTPS') && HTTPS === true) {
                $db->query(
                    "UPDATE `".$data['db_prefix']."settings` 
                    SET value = '".$db->escape(HTTP_ABANTECART)."' 
                    WHERE `key` = 'config_ssl_url'; "
                );
                $db->query(
                    "UPDATE `".$data['db_prefix']."settings` 
                    SET value = '2' 
                    WHERE `key` = 'config_ssl'; "
                );
            }
            $db->query(
                "UPDATE `".$data['db_prefix']."settings` 
                SET value = '".$db->escape(genToken(16))."' 
                WHERE `key` = 'task_api_key'; "
            );
            $db->query(
                "INSERT INTO `".$data['db_prefix']."settings` 
                SET `group` = 'config', 
                    `key` = 'install_date', 
                    value = NOW(); "
            );

            $db->query(
                "UPDATE `".$data['db_prefix']."products` 
                SET `viewed` = '0';"
            );

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
     * @param Registry $registry
     *
     * @return null
     * @throws AException
     */
    public function loadDemoData($registry, $file = '')
    {
        /** @var ADB $db */
        $db = $registry->get('db');
        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARACTER SET utf8");

        $file = DIR_APP_SECTION.($file ? : 'abantecart_sample_data.sql');
        if(!is_file($file)){
            return;
        }else{
            $sql = file($file);
        }
        $query = '';

        foreach ($sql as $line) {
            $tsl = trim($line);

            if (($line != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
                $query .= $line;
                if (preg_match('/;\s*$/', $line)) {
                    $query = str_replace("DROP TABLE IF EXISTS `ac_", "DROP TABLE IF EXISTS `".DB_PREFIX, $query);
                    $query = str_replace("CREATE TABLE `ac_", "CREATE TABLE `".DB_PREFIX, $query);
                    $query = str_replace("INSERT INTO `ac_", "INSERT INTO `".DB_PREFIX, $query);
                    $result = $db->query($query);

                    if (!$result || $db->error) {
                        exit($db->error.'<br>'.$query);
                    }

                    $query = '';
                }
            }
        }
        $db->query("SET CHARACTER SET utf8");
        //clear earlier created cache by AConfig and ALanguage classes in previous step
        $cache = new ACache();
        $cache->setCacheStorageDriver('file');
        $cache->enableCache();
        $cache->remove('*');
        return null;
    }

    public function getLanguages()
    {
        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("languages")."
            ORDER BY `sort_order`, `name`"
        );
        $language_data = [];

        foreach ($query->rows as $result) {
            $language_data[$result['code']] = [
                'language_id' => $result['language_id'],
                'name'        => $result['name'],
                'code'        => $result['code'],
                'locale'      => $result['locale'],
                'directory'   => $result['directory'],
                'filename'    => $result['filename'],
                'sort_order'  => $result['sort_order'],
                'status'      => $result['status'],
            ];
        }

        return $language_data;
    }
}
