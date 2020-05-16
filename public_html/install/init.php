<?php
/*------------------------------------------------------------------------------
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
------------------------------------------------------------------------------*/

// AbanteCart Version
define('VERSION', '1.2');
// Required PHP Version
define('MIN_PHP_VERSION', '5.6.0');

// Detect if localhost is used.
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// Detect http host
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    define('REAL_HOST', $_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
    define('REAL_HOST', $_SERVER['HTTP_HOST']);
}

// Error Reporting
error_reporting(E_ALL);

require_once(DIR_CORE.'lib/debug.php');
require_once(DIR_CORE.'lib/exceptions.php');
require_once(DIR_CORE.'lib/error.php');
require_once(DIR_CORE.'lib/warning.php');

// relative paths for extensions
define('DIR_EXT', DIR_ABANTECART.'extensions/');
define('DIR_EXT_CORE', '/core/');
define('DIR_EXT_STORE', '/storefront/');
define('DIR_EXT_ADMIN', '/admin/');
define('DIR_EXT_LANGUAGE', 'language/');
define('DIR_EXT_TEMPLATE', 'view/');

//postfixes for template override
define('POSTFIX_OVERRIDE', '.override');
define('POSTFIX_PRE', '.pre');
define('POSTFIX_POST', '.post');

define('IS_ADMIN', true);
define('SESSION_ID', 'PHPSESSID_AC');

try {

// Check Version
    if (version_compare(phpversion(), MIN_PHP_VERSION, '<') == true) {
        throw new AException(AC_ERR_REQUIREMENTS, MIN_PHP_VERSION.'+ Required for AbanteCart to work properly! Please contact your system administrator or host service provider.');
    }

//set ini parameters for session
    ini_set('session.use_trans_sid', 'Off');
    ini_set('session.use_cookies', 'On');
    ini_set('session.cookie_httponly', 'On');
// Process Global data if Register Globals enabled
    if (ini_get('register_globals')) {

        session_set_cookie_params(0, '/');
        session_start();

        $globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

        foreach ($globals as $global) {
            foreach (array_keys($global) as $key) {
                unset($$key);
            }
        }
    }

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
            $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
        }
    }

    if (!isset($_SERVER['DOCUMENT_ROOT'])) {
        if (isset($_SERVER['PATH_TRANSLATED'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
        }
    }

    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

        if (isset($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
        }
    }

// Include Engine
    require_once(DIR_CORE.'engine/router.php');
    require_once(DIR_CORE.'engine/page.php');
    require_once(DIR_CORE.'engine/response.php');
    require_once(DIR_CORE.'engine/dispatcher.php');
    require_once(DIR_CORE.'engine/controller.php');
    require_once(DIR_CORE.'engine/view.php');
    require_once(DIR_CORE.'engine/loader.php');
    require_once(DIR_CORE.'engine/model.php');
    require_once(DIR_CORE.'engine/registry.php');
    require_once(DIR_CORE.'engine/html.php');
    require_once(DIR_CORE.'engine/layout.php');
    require_once(DIR_CORE.'engine/form.php');
    require_once(DIR_CORE.'engine/extensions.php');
    require_once(DIR_CORE.'engine/language.php');

    require_once(DIR_CORE.'helper/html.php');
    require_once(DIR_CORE.'helper/utils.php');

// Include library files
    require_once(DIR_CORE.'lib/cache.php');
    require_once(DIR_CORE.'lib/config.php');
    require_once(DIR_CORE.'lib/db.php');
    require_once(DIR_CORE.'lib/connect.php');
    require_once(DIR_CORE.'lib/document.php');
    require_once(DIR_CORE.'lib/image.php');
    require_once(DIR_CORE.'lib/language_manager.php');
    require_once(DIR_CORE.'lib/log.php');
    require_once(DIR_CORE.'lib/mail.php');
    require_once(DIR_CORE.'lib/message.php');
    require_once(DIR_CORE.'lib/request.php');
    require_once(DIR_CORE.'lib/response.php');
    require_once(DIR_CORE.'lib/session.php');
    require_once(DIR_CORE.'lib/template.php');
    require_once(DIR_CORE.'lib/xml2array.php');
    require_once(DIR_CORE.'lib/json.php');

//plugins api

// Application Classes
    require_once(DIR_CORE.'lib/customer.php');
    require_once(DIR_CORE.'lib/currency.php');
    require_once(DIR_CORE.'lib/tax.php');
    require_once(DIR_CORE.'lib/weight.php');
    require_once(DIR_CORE.'lib/length.php');
    require_once(DIR_CORE.'lib/cart.php');
    require_once(DIR_CORE.'lib/user.php');
    require_once(DIR_CORE.'lib/dataset.php');
    require_once(DIR_CORE.'lib/encryption.php');
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

} catch (AException $e) {
    ac_exception_handler($e);
}