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
 * Main driver for running system check
 *
 * @param Registry $registry
 * @param string $mode ('log', 'return')
 *
 * @return array
 *
 * Note: This is English text only. Can be call before database and languages are loaded
 * @throws AException
 * @since 1.2.4
 *
 */

function run_system_check($registry, $mode = 'log')
{
    $mlog = $counts = [];
    //run anyway
    $mlog[] = check_install_directory();

    if ( //run on admin side
        (IS_ADMIN === true && (!$registry->get('config')->get('config_system_check') || $registry->get('config')->get('config_system_check') == 1))
        || //run on storefront side
        (IS_ADMIN !== true && (!$registry->get('config')->get('config_system_check') || $registry->get('config')->get('config_system_check') == 2))
    ) {

        $mlog = array_merge($mlog, check_file_permissions($registry));
        $mlog = array_merge($mlog, checkPhpConfiguration());
        $mlog = array_merge($mlog, check_server_configuration($registry));
        $mlog = array_merge($mlog, check_order_statuses($registry));
        $mlog = array_merge($mlog, check_web_access());
    }

    $counts['error_count'] = $counts['warning_count'] = $counts['notice_count'] = 0;
    foreach ($mlog as $message) {
        if ($message['type'] == 'E') {
            if ($mode == 'log') {
                //only save errors to the log
                $error = new AError($message['body']);
                $error->toLog()->toDebug();
                $registry->get('messages')->saveError($message['title'], $message['body']);
            }
            $counts['error_count']++;
        } else {
            if ($message['type'] == 'W') {
                if ($mode == 'log') {
                    $registry->get('messages')->saveWarning($message['title'], $message['body']);
                }
                $counts['warning_count']++;
            } else {
                if ($message['type'] == 'N') {
                    if ($mode == 'log') {
                        $registry->get('messages')->saveNotice($message['title'], $message['body']);
                    }
                    $counts['notice_count']++;
                }
            }
        }
    }

    return [$mlog, $counts];
}

function check_install_directory()
{
    //check if install dir existing. warn
    if (file_exists(DIR_ROOT.'/install')) {
        return [
            'title' => 'Security warning',
            'body'  => 'You still have install directory present in your AbanteCart main directory. It is highly recommended to delete install directory.',
            'type'  => 'W',
        ];
    }
    return [];
}

/**
 * @param Registry $registry
 *
 * @return array
 * @throws AException
 */
function check_file_permissions($registry)
{
    //check file permissions.
    $ret_array = [];
    $index = DIR_ROOT.'/index.php';
    if (is_writable($index) || substr(sprintf("%o", fileperms($index)), -3) == '777') {
        $ret_array[] = [
            'title' => 'Incorrect index.php file permissions',
            'body'  => $index.' file is writable. It is recommended to set read and execute modes for this file to keep it secured and running properly!',
            'type'  => 'W',
        ];
    }

    if (is_writable(DIR_SYSTEM.'config.php')) {
        $ret_array[] = [
            'title' => 'Incorrect config.php file permissions',
            'body'  => DIR_SYSTEM.'config.php'.' file needs to be set to read and execute modes to keep it secured from editing!',
            'type'  => 'W',
        ];
    }

    //if cache is enabled
    if ($registry->get('config')->get('config_cache_enable') && CACHE_DRIVER == 'file') {
        $cache_files = get_all_files_dirs(DIR_SYSTEM.'cache/');
        $cache_message = '';
        foreach ($cache_files as $file) {
            if (!is_file($file)) {
                continue;
            }
            $cache_message = '';
            if (in_array(basename($file), ['index.html', 'index.html', '.', '', '..'])) {
                continue;
            }
            if (!is_writable($file)) {
                $cache_message .= $file."<br/>";
            }
        }
        if ($cache_message) {
            $ret_array[] = [
                'title' => 'Incorrect cache files permissions',
                'body'  => "Following files do not have write permissions. AbanteCart will not function properly. <br/>".$cache_message,
                'type'  => 'E',
            ];
        }
    }

    if (!is_writable(DIR_SYSTEM.'logs') || !is_writable(DIR_SYSTEM.'logs/error.txt')) {
        $ret_array[] = [
            'title' => 'Incorrect log dir/file permissions',
            'body'  => DIR_SYSTEM.'logs'.' directory or error.txt file needs to be set to full permissions(777)! Error logs can not be saved',
            'type'  => 'W',
        ];
    }
    //check resource directories
    $resource_files = get_all_files_dirs(DIR_ROOT.'/resources/');
    $resource_message = '';
    foreach ($resource_files as $file) {
        if (in_array(basename($file), ['.htaccess', 'index.php', 'index.html', '.', '', '..'])) {
            continue;
        }
        if (!is_writable($file)) {
            $resource_message .= $file."<br/>";
        }
    }
    if ($resource_message) {
        $ret_array[] = [
            'title' => 'Incorrect resource files permissions',
            'body'  => "Following files(folders) do not have write permissions. AbanteCart Media Manager will not function properly. <br/>".$resource_message,
            'type'  => 'W',
        ];
    }

    $image_files = get_all_files_dirs(DIR_ROOT.'/image/thumbnails/');
    $image_message = '';
    foreach ($image_files as $file) {
        if (in_array(basename($file), ['index.php', 'index.html', '.', '', '..'])) {
            continue;
        }
        if (!is_writable($file)) {
            $image_message .= $file."<br/>";
        }
    }
    if ($image_message) {
        $ret_array[] = [
            'title' => 'Incorrect image files permissions',
            'body'  => "Following files do not have write permissions. AbanteCart thumbnail images will not function properly. <br/>".$image_message,
            'type'  => 'W',
        ];
    }

    if (!is_writable(DIR_ROOT.'/admin/system')) {
        $ret_array[] = [
            'title' => 'Incorrect directory permission',
            'body'  => DIR_ROOT.'/admin/system'.' directory needs to be set to full permissions(777)! AbanteCart backups and upgrade will not work.',
            'type'  => 'W',
        ];
    }

    if (is_dir(DIR_ROOT.'/admin/system/backup') && !is_writable(DIR_ROOT.'/admin/system/backup')) {
        $ret_array[] = [
            'title' => 'Incorrect backup directory permission',
            'body'  => DIR_ROOT.'/admin/system/backup'.' directory needs to be set to full permissions(777)! AbanteCart backups and upgrade will not work.',
            'type'  => 'W',
        ];
    }

    if (is_dir(DIR_ROOT.'/admin/system/temp') && !is_writable(DIR_ROOT.'/admin/system/temp')) {
        $ret_array[] = [
            'title' => 'Incorrect temp directory permission',
            'body'  => DIR_ROOT.'/admin/system/temp'.' directory needs to be set to full permissions(777)!',
            'type'  => 'W',
        ];
    }

    if (is_dir(DIR_ROOT.'/admin/system/uploads') && !is_writable(DIR_ROOT.'/admin/system/uploads')) {
        $ret_array[] = [
            'title' => 'Incorrect "uploads" directory permission',
            'body'  => DIR_ROOT.'/admin/system/uploads'.' directory needs to be set to full permissions(777)! Probably AbanteCart file uploads will not work.',
            'type'  => 'W',
        ];
    }

    return $ret_array;
}

/**
 * @param array $modules - list of specific modules needs to be installed on host
 * @param string|null $phpMinVersion - minimal required version of PHP. If not set - take current
 *
 * @return array
 */
function checkPhpConfiguration( $modules = [], $phpMinVersion = null)
{
    $output = [];
    $phpMinVersion = $phpMinVersion ?: MIN_PHP_VERSION;
    if (version_compare(phpversion(), $phpMinVersion, '<') == true) {
        $output['php_version'] = [
            'title' => 'Incompatible PHP version',
            'body'  => 'You need to use PHP '.$phpMinVersion.' or above!',
            'type'  => 'E',
        ];
    }

    //if needs to check specific php-extensions
    if($modules){
        foreach($modules as $module){
            $module = strtolower($module);
            if (!extension_loaded($module)) {
                $output[$module] = [
                    'title' => ucfirst($module).' extension is missing',
                    'body'  => ucfirst($module).' extension needs to be enabled on PHP!',
                    'type'  => 'E',
                ];
            }
        }
    }
    //check if all modules and settings on PHP side are OK.
    if (!extension_loaded('mysql') && !extension_loaded('mysqli') && !extension_loaded('pdo_mysql')) {
        $output['mysql'] = [
            'title' => 'MySQL extension is missing',
            'body'  => 'MySQL extension needs to be enabled on PHP for AbanteCart to work!',
            'type'  => 'E',
        ];
    }
    if (!ini_get('file_uploads')) {
        $output['file_uploads'] = [
            'title' => 'File Upload Warning',
            'body'  => 'PHP file_uploads option is disabled. File uploading will not function properly',
            'type'  => 'W',
        ];
    }
    if (ini_get('session.auto_start')) {
        $output['session.auto_start'] = [
            'title' => 'Issue with session.auto_start',
            'body'  => 'AbanteCart will not work with session.auto_start enabled!',
            'type'  => 'E',
        ];
    }
    if (!function_exists('simplexml_load_file')) {
        $output['simplexml_load_file'] = [
            'title' => 'SimpleXML Warning',
            'body'  => 'SimpleXML functions needs to be available in PHP!',
            'type'  => 'W',
        ];
    }

    if (!extension_loaded('gd')) {
        $output['gd'] = [
            'title' => 'GD extension is missing',
            'body'  => 'GD extension needs to be enabled in PHP for AbanteCart to work! Images will not display properly',
            'type'  => 'E',
        ];
    }
    if (!extension_loaded('curl')) {
        $output['curl'] = [
            'title' => 'CURL extension is missing',
            'body'  => 'CURL extension needs to be enabled in PHP for AbanteCart to work!',
            'type'  => 'E',
        ];
    }

    if (!extension_loaded('mbstring') || !function_exists('mb_internal_encoding')) {
        $output['mbstring'] = [
            'title' => 'mbstring extension is missing',
            'body'  => 'MultiByte String extension needs to be loaded in PHP for AbanteCart to work!',
            'type'  => 'E',
        ];
    }
    if (!extension_loaded('fileinfo')) {
        $output['fileinfo'] = [
            'title' => 'fileinfo extension is missing',
            'body'  => 'FileInfo extension needs to be loaded in PHP for AbanteCart to work!',
            'type'  => 'E',
        ];
    }
    if (!extension_loaded('zlib')) {
        $output['zlib'] = [
            'title' => 'ZLIB extension is missing',
            'body'  => 'ZLIB extension needs to be loaded in PHP for backups to work!',
            'type'  => 'W',
        ];
    }
    //check memory limit

    $memory_limit = trim(ini_get('memory_limit'));
    $last = strtolower($memory_limit[strlen($memory_limit) - 1]);

    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $memory_limit *= (1024 * 1024 * 1024);
            break;
        case 'm':
            $memory_limit *= (1024 * 1024);
            break;
        case 'k':
            $memory_limit *= 1024;
            break;
    }

    //Recommended minimal PHP memory size is 64mb
    if ($memory_limit > 0 && $memory_limit < (64 * 1024 * 1024)) {
        $output['memory_limit'] = [
            'title' => 'Memory limitation',
            'body'  => 'Low PHP memory setting. Some Abantecart features will not work with memory limit less than 64Mb! '
                .'Check <a href="https://php.net/manual/en/ini.core.php#ini.memory-limit" target="_help_doc">PHP memory-limit setting</a>',
            'type'  => 'W',
        ];
    }

    return $output;
}

/**
 * @param Registry $registry
 *
 * @return array
 * @throws AException
 */
function check_server_configuration($registry)
{
    //check server configurations.
    $output = [];

    $size = disk_size(DIR_ROOT);
    //check for size to drop below 10mb
    if (isset($size['bytes']) && $size['bytes'] < 1024 * 10000) {
        $output[] = [
            'title' => 'Critically low disk space',
            'body'  => 'AbanteCart is running on critically low disk space of '.$size['human'].'! Increase disk size to prevent failure.',
            'type'  => 'E',
        ];
    }

    //if SEO is enabled
    if ($registry->get('config')->get('enable_seo_url') && IS_ADMIN) {
        $curl_handle=curl_init();

        $storeUrl = $registry->get('config')->get('config_url');

        $options = [
            CURLOPT_URL            => $storeUrl.(substr($storeUrl, -1) !== '/' ? '/' : '' ).'check_seo_url',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 10,
        ];
        curl_setopt_array( $curl_handle, $options );
        curl_exec($curl_handle);
        $httpCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        curl_close($curl_handle);

        if ($httpCode !== 200) {
            $output[] = [
                'title' => 'SEO URLs does not work',
                'body'  => 'SEO URL functionality will not work. Check the <a href="https://docs.abantecart.com/pages/tips/enable_seo.html" target="_help_doc">manual for SEO URL setting</a> ',
                'type'  => 'W',
            ];
        }
    }

    return $output;
}

/**
 * @param $start_dir
 *
 * @return array
 */
function get_all_files_dirs($start_dir)
{
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($start_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
    );

    $paths = [$start_dir];
    foreach ($iter as $path => $dir) {
        $paths[] = $path;
    }
    return $paths;
}

/**
 * @param string $path
 *
 * @return array
 */
function disk_size($path)
{
    //check if this is supported by server
    if (function_exists('disk_free_space')) {
        try {
            $bytes = disk_free_space($path);
            $si_prefix = ['B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB'];
            $base = 1024;
            $class = min((int)log($bytes, $base), count($si_prefix) - 1);
            return [
                'bytes' => $bytes,
                'human' => sprintf('%1.2f', $bytes / pow($base, $class)).' '.$si_prefix[$class],
            ];
        } catch (Exception $e) {
            return [];
        }
    } else {
        return [];
    }
}

/**
 * @param Registry $registry
 *
 * @return array
 * @throws AException
 */
function check_order_statuses($registry)
{

    $db = $registry->get('db');

    $order_statuses = $registry->get('order_status')->getStatuses();
    $language_id = (int)$registry->get('language')->getDefaultLanguageID();

    $query = $db->query(
        "SELECT osi.order_status_id, osi.status_text_id
        FROM ".$db->table('order_statuses')." os
        INNER JOIN ".$db->table('order_status_ids')." osi
            ON osi.order_status_id = os.order_status_id
        WHERE os.language_id = '".$language_id."'"
    );
    $db_statuses = [];
    foreach ($query->rows as $row) {
        $db_statuses[(int)$row['order_status_id']] = $row['status_text_id'];
    }

    $ret_array = [];
    foreach ($order_statuses as $id => $text_id) {
        if ($text_id != $db_statuses[$id]) {
            $ret_array[] = [
                'title' => 'Incorrect order status with id '.$id,
                'body'  => 'Incorrect status text id for order status #'.$id.'. Value must be "'.$text_id
                    .'" ('.$db_statuses[$id].'). Please check data of tables '
                    .$db->table('order_status_ids').' and '.$db->table('order_statuses'),
                'type'  => 'W',
            ];
        }
    }

    return $ret_array;
}

/**
 * function checks restricted areas
 */
function check_web_access()
{

    $areas = [
        'system'             => ['.htaccess', 'index.php'],
        'resources/download' => ['.htaccess'],
        'download'           => ['index.html'],
        'admin'              => ['.htaccess', 'index.php'],
        'admin/system'       => ['.htaccess', 'index.html'],
    ];

    $ret_array = [];

    foreach ($areas as $subfolder => $rules) {
        $dirname = DIR_ROOT.'/'.$subfolder;
        if (!is_dir($dirname)) {
            continue;
        }

        foreach ($rules as $rule) {
            $message = '';
            switch ($rule) {
                case '.htaccess':
                    if (!is_file($dirname.'/.htaccess')) {
                        $message = 'Restricted directory '.$dirname.' have public access. It is highly recommended to create .htaccess file and forbid access. ';
                    }
                    break;
                case 'index.php':
                    if (!is_file($dirname.'/index.php')) {
                        $message = 'Restricted directory '.$dirname.' does not contain index.php file. It is highly recommended to create it.';
                    }
                    break;
                case 'index.html':
                    if (!is_file($dirname.'/index.html')) {
                        $message = 'Restricted directory '.$dirname.' does not contain empty index.html file. It is highly recommended to create it.';
                    }

                    break;
                default:
                    break;
            }
            if ($message) {
                $ret_array[] = [
                    'title' => 'Security warning ('.$subfolder.', '.$rule.')',
                    'body'  => $message,
                    'type'  => 'W',
                ];
            }
        }
    }
    return $ret_array;
}

/**
 * @param Registry $registry
 * @param string $mode
 *
 * @return array
 * @throws AException
 */
function run_critical_system_check($registry, $mode = 'log')
{

    $mlog = [];
    $mlog[] = check_session_save_path();

    $output = [];

    foreach ($mlog as $message) {
        if ($message['body']) {
            if ($mode == 'log') {
                //only save errors to the log
                $error = new AError($message['body']);
                $error->toLog()->toDebug();
                $registry->get('messages')->saveError($message['title'], $message['body']);
            }
            $output[] = $message;
        }
    }

    return $output;
}

/**
 * @return array
 */
function check_session_save_path()
{
    $save_path = ini_get('session.save_path');
    //check for non-empty path (it can be on some fast-cgi php)
    if ($save_path) {
        $parts = explode(';', $save_path);
        $path = array_pop($parts);
        if (!is_writable($path)) {
            return [
                'title' => 'Session save path is not writable! ',
                'body'  => 'Your server is unable to create a session necessary for AbanteCart functionality. Check logs for exact error details and contact your hosting support administrator to resolve this error.',
            ];
        }
    }
    return [];
}

/**
 * Function seek an extension which made layout changes during it's installation process
 * Template-extension ignores
 *
 * @param string $excludeExtension
 *
 * @throws AException
 */
function findExtensionsLayouts($excludeExtension = ''){

    $output = [];
    $registry = Registry::getInstance();
    $config = $registry->get('config');
    $exts = $registry->get('extensions');
    $allExtensions = $exts->getExtensionInfo();
    if($excludeExtension) {
        $lm = new ALayoutManager($config->get('config_storefront_template'));
        $currentTemplatePages = $lm->getAllPages();
        $currentTemplatePages = array_column($currentTemplatePages,'layout_name');
    }
    foreach($allExtensions as $ext){
        if(
            $excludeExtension == $ext['key']
            //or not installed
            || !$config->has($ext['key'].'_status')
            // or extension is template
            || $ext['category'] == 'template'
            || !is_dir(DIR_EXT.$ext['key'])
        ){
            continue;
        }

        $xmlLayouts = glob(DIR_EXT.$ext['key'].'/*{layout}*.xml',GLOB_BRACE);
        if(!$xmlLayouts){
            continue;
        }
        if($excludeExtension){
            $absent = false;
            foreach($xmlLayouts as $xmlFile) {

                $extensionLayout = @simplexml_load_file($xmlFile);
                if(!$extensionLayout){
                    continue;
                }
                foreach($extensionLayout->layout as $l ){
                        if(!in_array((string)$l->name,$currentTemplatePages)){
                            $abs[] = (string)$l->name;
                            $absent = true;
                            break;
                        }
                }
            }
            //skip extension if it's layout already in the current template's layout list
            if(!$absent){
                continue;
            }
        }

        $output[$ext['key']] = array_map('basename',$xmlLayouts);
    }
    return $output;
}