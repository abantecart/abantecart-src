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

// AbanteCart Version
require_once(DIR_CORE.'version.php');
const VERSION = MASTER_VERSION.'.'.MINOR_VERSION.'.'.VERSION_BUILT;
// Required PHP Version
const MIN_PHP_VERSION = '8.2.0';
// EMAIL REGEXP PATTERN
const EMAIL_REGEX_PATTERN = '/^[A-Z0-9._%-]+@[A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,16}$/i';

// Detect if localhost is used.
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// Detect http host
define('REAL_HOST', $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST']);

require_once(DIR_CORE.'lib/debug.php');
require_once(DIR_CORE.'lib/exceptions.php');
require_once(DIR_CORE.'lib/error.php');
require_once(DIR_CORE.'lib/warning.php');

// relative paths for extensions
const DIR_EXT = DIR_ABANTECART . 'extensions' . DS;
const DIR_EXT_CORE = DS . 'core' . DS;
const DIR_EXT_STORE = DS . 'storefront' . DS;
const DIR_EXT_ADMIN = DS . 'admin' . DS;
const DIR_EXT_LANGUAGE = 'language' . DS;
const DIR_EXT_TEMPLATE = 'view' . DS;

//postfixes for template override
const POSTFIX_OVERRIDE = '.override';
const POSTFIX_PRE = '.pre';
const POSTFIX_POST = '.post';

const IS_ADMIN = true;
const SESSION_ID = 'PHPSESSID_AC';

define('DIR_RESOURCE', dirname(DIR_ROOT).DS.'resources'.DS);

try {

// Check Version
    if (version_compare(phpversion(), MIN_PHP_VERSION, '<')) {
        exit(
            MIN_PHP_VERSION.'+ Required for AbanteCart to work properly! '
            .'Please contact your system administrator or host service provider.'
        );
    }

//set ini parameters for session
    ini_set('session.use_trans_sid', 'Off');
    ini_set('session.use_cookies', 'On');
    ini_set('session.cookie_httponly', 'On');


// Magic Quotes
    if (ini_get('magic_quotes_gpc')) {
        function clean($data)
        {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $data[clean($key)] = clean($value);
                }
            } else {
                $data = stripslashes($data);
            }

            return $data;
        }

        $_GET = clean($_GET);
        $_POST = clean($_POST);
        $_COOKIE = clean($_COOKIE);
    }

    if (!ini_get('date.timezone')) {
        date_default_timezone_set('UTC');
    }

    if (!isset($_SERVER['DOCUMENT_ROOT'])) {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace(
                '\\',
                '/',
                substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF']))
            );
        }
    }

    if (!isset($_SERVER['DOCUMENT_ROOT'])) {
        if (isset($_SERVER['PATH_TRANSLATED'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace(
                '\\',
                '/',
                substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF']))
            );
        }
    }

    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

        if (isset($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
        }
    }

// Include Engine
    require_once(DIR_CORE.'engine'.DS.'router.php');
    require_once(DIR_CORE.'engine'.DS.'page.php');
    require_once(DIR_CORE.'engine'.DS.'response.php');
    require_once(DIR_CORE.'engine'.DS.'dispatcher.php');
    require_once(DIR_CORE.'engine'.DS.'controller.php');
    require_once(DIR_CORE.'engine'.DS.'view.php');
    require_once(DIR_CORE.'engine'.DS.'loader.php');
    require_once(DIR_CORE.'engine'.DS.'model.php');
    require_once(DIR_CORE.'engine'.DS.'registry.php');
    require_once(DIR_CORE.'engine'.DS.'html.php');
    require_once(DIR_CORE.'engine'.DS.'layout.php');
    require_once(DIR_CORE.'engine'.DS.'form.php');
    require_once(DIR_CORE.'engine'.DS.'extensions.php');
    require_once(DIR_CORE.'engine'.DS.'language.php');
    require_once(DIR_CORE.'engine'.DS.'resources.php');

    require_once(DIR_CORE.'helper'.DS.'html.php');
    require_once(DIR_CORE.'helper'.DS.'utils.php');
    require_once(DIR_CORE.'helper'.DS.'system_check.php');


// Include library files
    require_once(DIR_CORE.'lib'.DS.'cache.php');
    require_once(DIR_CORE.'lib'.DS.'config.php');
    require_once(DIR_CORE.'lib'.DS.'db.php');
    require_once(DIR_CORE.'lib'.DS.'connect.php');
    require_once(DIR_CORE.'lib'.DS.'document.php');
    require_once(DIR_CORE.'lib'.DS.'image.php');
    require_once(DIR_CORE.'lib'.DS.'language_manager.php');
    require_once(DIR_CORE.'lib'.DS.'log.php');
    require_once(DIR_CORE.'lib'.DS.'mail.php');
    require_once(DIR_CORE.'lib'.DS.'message.php');
    require_once(DIR_CORE.'lib'.DS.'request.php');
    require_once(DIR_CORE.'lib'.DS.'response.php');
    require_once(DIR_CORE.'lib'.DS.'session.php');
    require_once(DIR_CORE.'lib'.DS.'template.php');
    require_once(DIR_CORE.'lib'.DS.'xml2array.php');
    require_once(DIR_CORE.'lib'.DS.'json.php');
    require_once(DIR_CORE.'lib'.DS.'layout_manager.php');
    require_once(DIR_CORE.'lib'.DS.'package_manager.php');
    require_once(DIR_CORE.'lib'.DS.'extension_manager.php');
    require_once(DIR_CORE.'lib'.DS.'resource_manager.php');
    require_once(DIR_CORE.'lib'.DS.'language_manager.php');
    require_once(DIR_CORE.'lib'.DS.'menu_control.php');

//plugins api

// Application Classes
    require_once(DIR_CORE.'lib'.DS.'customer.php');
    require_once(DIR_CORE.'lib'.DS.'currency.php');
    require_once(DIR_CORE.'lib'.DS.'tax.php');
    require_once(DIR_CORE.'lib'.DS.'weight.php');
    require_once(DIR_CORE.'lib'.DS.'length.php');
    require_once(DIR_CORE.'lib'.DS.'cart.php');
    require_once(DIR_CORE.'lib'.DS.'user.php');
    require_once(DIR_CORE.'lib'.DS.'dataset.php');
    require_once(DIR_CORE.'lib'.DS.'encryption.php');
// Registry
    $registry = Registry::getInstance();

// Loader
    $loader = new ALoader($registry);
    $registry->set('load', $loader);

// Request
    $request = new ARequest();
    $registry->set('request', $request);

    $session = new ASession(SESSION_ID);
    $registry->set('session', $session);

// Response
    $response = new AResponse();
    $response->addHeader('Content-Type: text/html; charset=utf-8');
    $registry->set('response', $response);

// URL Class
    $html = new AHtml($registry);
    $registry->set('html', $html);

    $extensions = new ExtensionsApi();
    $extensions->loadEnabledExtensions();
    $registry->set('extensions', $extensions);

    if (defined('DB_HOSTNAME') && DB_HOSTNAME) {
        $registry = Registry::getInstance();
        $db = new ADB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $registry->set('db', $db);
        // Cache
        $registry->set('cache', new ACache());

        $config = new AConfig($registry);
        $registry->set('config', $config);

// Session
        $registry->set('session', new ASession(SESSION_ID));
        $config->set('current_store_id', 0);

        // Load language
        $language = new ALanguageManager($registry);
        $registry->set('language', $language);

        $extensions = new ExtensionsApi();
        $extensions->loadEnabledExtensions();
        $registry->set('extensions', $extensions);

        $r = $db->query("SELECT * FROM ".DB_PREFIX."settings");
        $data_exist = $r->num_rows;
        if ($data_exist && !isset($session->data['finish'])) {
            session_destroy();
            header('Location: ../');
        }

    }else {
        unset($session->data['finish']);
    }

} catch (AException $e) {
    ac_exception_handler($e);
}