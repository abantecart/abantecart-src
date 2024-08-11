<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
// Error Reporting
error_reporting(E_ALL);

const DS = DIRECTORY_SEPARATOR;

// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

if (defined('IS_WINDOWS')) {
    $root_path = str_replace('\\', '/', $root_path);
}
define('DIR_ROOT', $root_path);
// Detect https
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1')) {
    define('HTTPS', true);
} elseif (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && ($_SERVER['HTTP_X_FORWARDED_SERVER'] == 'secure' || $_SERVER['HTTP_X_FORWARDED_SERVER'] == 'ssl')) {
    define('HTTPS', true);
} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    define('HTTPS', true);
} elseif (isset($_SERVER['SCRIPT_URI']) && (substr($_SERVER['SCRIPT_URI'], 0, 5) == 'https')) {
    define('HTTPS', true);
} elseif (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], ':443') !== false)) {
    define('HTTPS', true);
} else {
    define('HTTPS', false);
}

// HTTP
define('HTTP_SERVER', (HTTPS === true ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/.\\').'/');
define('HTTP_ABANTECART', (HTTPS === true ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].rtrim(rtrim(dirname($_SERVER['PHP_SELF']), 'install'), '/.\\').'/');

// DIR
define('DIR_APP_SECTION', str_replace('\'', '/', realpath(dirname(__FILE__))).'/');
define('DIR_CORE', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/core/');
define('DIR_SYSTEM', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/system/');
define('DIR_CACHE', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/system/cache/');
define('DIR_LOGS', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/system/logs/');
define('DIR_ABANTECART', str_replace('\'', '/', realpath(DIR_APP_SECTION.'../')).'/');
const DIR_STOREFRONT = DIR_ABANTECART . DS . 'storefront' . DS;
const DIR_DATABASE = DIR_CORE . 'database' . DS;
const DIR_TEMPLATE = DIR_APP_SECTION . 'view' . DS . 'template' . DS;
const INSTALL = true;
// Relative paths and directories
const RDIR_TEMPLATE = 'view' . DS;

//Check if cart is already installed
if (file_exists(DIR_SYSTEM.'config.php')) {
    require_once(DIR_SYSTEM.'config.php');
}

// Startup with local init
require_once('init.php');

if (isset($session->data['finish']) && $session->data['finish'] == 'true') {
    $request->get['rt'] = 'finish';
}

try {

// Document
    $document = new ADocument();
    $document->setBase(HTTP_SERVER);
    $registry->set('document', $document);

// Page Controller 
    $page_controller = new APage($registry);

// Router
    if (!empty($request->get['rt'])) {
        $dispatch = $request->get['rt'];
    } else {
        $dispatch = 'license';
    }

    $page_controller->build('pages/'.$dispatch);

// Output
    $response->output();
} catch (AException $e) {
    ac_exception_handler($e);
}

//display debug info
ADebug::display();
