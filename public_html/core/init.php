<?php
/**
*  AbanteCart 2011-2017
*
* AbanteCart, Ideal OpenSource Ecommerce Solution
*
* http://www.AbanteCart.com
*
*  NOTICE OF LICENSE
*
* Copyright 2011-2017 Belavier Commerce LLC
* This source file is subject to Open Software License (OSL 3.0)
* License details is bundled with this package in the file LICENSE.txt.
* It is also available at this URL:
*
* http://www.opensource.org/licenses/OSL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade AbanteCart to newer
* versions in the future. If you wish to customize AbanteCart for your
* needs please refer to http://www.AbanteCart.com for more information.
*/

/* set default encoding for multibyte php mod */
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'utf-8');

/* AbanteCart Version */
include('version.php');
define('VERSION', MASTER_VERSION.'.'.MINOR_VERSION.'.'.VERSION_BUILT);

// Detect if localhost is used.
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// Detect https
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1')) {
    define('HTTPS', true);
} elseif (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) &&
    ($_SERVER['HTTP_X_FORWARDED_SERVER'] == 'secure' ||
    $_SERVER['HTTP_X_FORWARDED_SERVER'] == 'ssl')) {
    define('HTTPS', true);
} elseif (isset($_SERVER['SCRIPT_URI']) && (substr($_SERVER['SCRIPT_URI'], 0, 5) == 'https')) {
    define('HTTPS', true);
} elseif (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], ':443') !== false)) {
    define('HTTPS', true);
}

// Detect http host
if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    define('REAL_HOST', $_SERVER['HTTP_X_FORWARDED_HOST']);
} else {
    define('REAL_HOST', $_SERVER['HTTP_HOST']);
}

//Set up common paths
define('DIR_SYSTEM', DIR_ROOT.'/system/');
define('DIR_IMAGE', DIR_ROOT.'/image/');
define('DIR_DOWNLOAD', DIR_ROOT.'/download/');

define('DIR_CACHE', DIR_SYSTEM.'cache/');
define('DIR_LOGS', DIR_SYSTEM.'logs/');

/**
* DEPRECATED
* CORE DIR ALL
*/
define('DIR_VENDORS', DIR_CORE.'vendors/');
define('DIR_DATABASE', DIR_CORE.'database/');
/*
*
* NOTE there is no "/core/config" directory in that location
*/
define('DIR_CONFIG', DIR_CORE.'config/');

/**
* UPGRADE DIR CORE ALL
* @Cali-gula
*/
define('DIR_CORE_CACHE', DIR_CORE.'cahe/');
define('DIR_CORE_DATABASE', DIR_CORE.'database/');
define('DIR_CORE_ENGINE', DIR_CORE.'engine/');
define('DIR_CORE_EXTENSION', DIR_CORE.'extension/');

/* DIR_CORE_EXTENSION SUB DIR */
define('DIR_CORE_EXTENSION_DEFAULT', DIR_CORE_EXTENSION.'default/');
define('DIR_CORE_EXTENSION_PAYMENT', DIR_CORE_EXTENSION.'payment/');
define('DIR_CORE_EXTENSION_SHIPPING', DIR_CORE_EXTENSION.'shipping/');

define('DIR_CORE_HELPER', DIR_CORE.'helper/');
define('DIR_CORE_LIB', DIR_CORE.'lib/');
define('DIR_CORE_VENDORS', DIR_CORE.'vendors/');

/* SEO URL Keyword separator */
define('SEO_URL_SEPARATOR', '-');
define('USE_IN_CLASS_SEPARATOR', '::');

// EMAIL REGEXP PATTERN
define('EMAIL_REGEX_PATTERN','/^[A-Z0-9._%-]+@[A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,16}$/i');

/* Error Reporting */
error_reporting(E_ALL);
require_once(DIR_CORE_LIB.'debug.php');
require_once(DIR_CORE_LIB.'exceptions.php');
require_once(DIR_CORE_LIB.'error.php');
require_once(DIR_CORE_LIB.'warning.php');

/* define rt - route for application controller */
if($_GET['rt']) {
    define('ROUTE', $_GET['rt']);
} else if($_POST['rt']){
    define('ROUTE', $_POST['rt']);
} else {
    define('ROUTE', 'index/home');
}

/* detect API call */
$path_nodes = explode('/', ROUTE);
if($path_nodes[0] == 'a') {
    define('IS_API', true);
} else {
    define('IS_API', false);
}

/**
* Detect the section of the cart to access and build the path definitions
* s=admin or s=storefront (default nothing)
*/
define('INDEX_FILE', 'index.php');

define('DIR_STOREFRONT', DIR_ROOT.'/storefront/');

if (defined('ADMIN_PATH') && (isset($_GET['s']) ||
    isset($_POST['s'])) && ($_GET['s'] == ADMIN_PATH ||
    $_POST['s'] == ADMIN_PATH)) {
    define('IS_ADMIN', true);
    define('DIR_APP_SECTION', DIR_ROOT.'/admin/');
    define('DIR_LANGUAGE', DIR_APP_SECTION.'language/');
    define('DIR_TEMPLATE', DIR_APP_SECTION.'view/');
    define('DIR_BACKUP', DIR_APP_SECTION.'system/backup/');
    define('DIR_DATA', DIR_APP_SECTION.'system/data/');

    /**
     * generate unique session name.
     * NOTE: This is a session name not to confuse with actual session id. Candidate to renaming
     * define(
     * 'SESSION_ID',
     * defined('UNIQUE_ID'
     * ) ? 'AC_CP_'.strtoupper(substr(UNIQUE_ID, 0, 10)) : 'AC_CP_PHPSESSID');
     *
     *
    */
} else {
    define('IS_ADMIN', false);
    define('DIR_APP_SECTION', DIR_STOREFRONT);
    define('DIR_LANGUAGE', DIR_APP_SECTION.'language/');
    define('DIR_TEMPLATE', DIR_APP_SECTION.'view/');
    define('SESSION_ID', defined('UNIQUE_ID') ? 'AC_SF_'.strtoupper(substr(UNIQUE_ID, 0, 10)) : 'AC_SF_PHPSESSID');
    define('EMBED_TOKEN_NAME', 'ABC_TOKEN');
}

try {
    /* set ini parameters for session */
    ini_set('session.use_trans_sid', 'Off');
    ini_set('session.use_cookies', 'On');
    ini_set('session.cookie_httponly', 'On');

    /* Process Global data if Register Globals enabled */
    if (ini_get('register_globals')) {
        $path = dirname($_SERVER['PHP_SELF']);
        session_set_cookie_params(
            0,
            $path,
            null,
            false,
            true
        );
        unset($path);
        session_name(SESSION_ID);
        session_start();
        $globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);
        foreach ($globals as $global) {
            foreach (array_keys($global) as $key) {
                unset($$key);
            }
        }
    }

    /* Magic Quotes */
    if (ini_get('magic_quotes_gpc')) {
        function clean($data) {
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
                substr(
                    $_SERVER['SCRIPT_FILENAME'],
                    0,
                    0 - strlen($_SERVER['PHP_SELF'])
                )
            );
        }
    }

    if (!isset($_SERVER['DOCUMENT_ROOT'])) {
        if (isset($_SERVER['PATH_TRANSLATED'])) {
            $_SERVER['DOCUMENT_ROOT'] = str_replace(
                '\\',
                '/',
                substr(
                    str_replace(
                        '\\\\',
                        '\\',
                        $_SERVER['PATH_TRANSLATED']
                    ),
                    0,
                    0 - strlen($_SERVER['PHP_SELF'])
                )
            );
        }
    }

    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);
        if (isset($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
        }
    }

    /**
    * relative paths for extensions
    * Directory definition not understandable!
    * What to do with this?
    * @Cali-gula
    */
    define('DIR_EXTENSIONS', DIR_ROOT .'/extensions/');
    define('DIR_EXT', DIR_EXTENSIONS);
    define('DIR_EXT_CORE', '/core/');
    define('DIR_EXT_STORE', '/storefront/');
    define('DIR_EXT_ADMIN', '/admin/');
    define('DIR_EXT_IMAGE', '/image/');
    define('DIR_EXT_LANGUAGE', 'language/');
    define('DIR_EXT_TEMPLATE', 'view/');

    //resources
    define('DIR_RESOURCE', DIR_ROOT.'/resources/');

    /* postfixes for template override */
    define('POSTFIX_OVERRIDE', '.override');
    define('POSTFIX_PRE', '.pre');
    define('POSTFIX_POST', '.post');

    // Include Engine
    require_once(DIR_CORE_ENGINE.'router.php');
    require_once(DIR_CORE_ENGINE.'page.php');
    require_once(DIR_CORE_ENGINE.'response.php');
    require_once(DIR_CORE_ENGINE.'api.php');
    require_once(DIR_CORE_ENGINE.'task.php');
    require_once(DIR_CORE_ENGINE.'dispatcher.php');
    require_once(DIR_CORE_ENGINE.'controller.php');
    require_once(DIR_CORE_ENGINE.'controller_api.php');
    require_once(DIR_CORE_ENGINE.'view.php');
    require_once(DIR_CORE_ENGINE.'loader.php');
    require_once(DIR_CORE_ENGINE.'model.php');
    require_once(DIR_CORE_ENGINE.'registry.php');
    require_once(DIR_CORE_ENGINE.'resources.php');
    require_once(DIR_CORE_ENGINE.'html.php');
    require_once(DIR_CORE_ENGINE.'layout.php');
    require_once(DIR_CORE_ENGINE.'form.php');
    require_once(DIR_CORE_ENGINE.'extensions.php');
    require_once(DIR_CORE_ENGINE.'hook.php');
    require_once(DIR_CORE_ENGINE.'attribute.php');
    require_once(DIR_CORE_ENGINE.'promotion.php');
    require_once(DIR_CORE_ENGINE.'language.php');

    require_once(DIR_CORE_HELPER.'html.php');
    require_once(DIR_CORE_HELPER.'utils.php');
    require_once(DIR_CORE_HELPER.'system_check.php');

    /* Include library files */
    require_once(DIR_CORE_LIB.'cache.php');
    require_once(DIR_CORE_LIB.'config.php');
    require_once(DIR_CORE_LIB.'db.php');
    require_once(DIR_CORE_LIB.'connect.php');
    require_once(DIR_CORE_LIB.'document.php');
    require_once(DIR_CORE_LIB.'image.php');
    require_once(DIR_CORE_LIB.'log.php');
    require_once(DIR_CORE_LIB.'mail.php');
    require_once(DIR_CORE_LIB.'message.php');
    require_once(DIR_CORE_LIB.'pagination.php');
    require_once(DIR_CORE_LIB.'request.php');
    require_once(DIR_CORE_LIB.'response.php');
    require_once(DIR_CORE_LIB.'session.php');
    require_once(DIR_CORE_LIB.'template.php');
    require_once(DIR_CORE_LIB.'xml2array.php');
    require_once(DIR_CORE_LIB.'data.php');
    require_once(DIR_CORE_LIB.'file.php');
    require_once(DIR_CORE_LIB.'download.php');

    /* Application Classes */
    require_once(DIR_CORE_LIB.'customer.php');
    require_once(DIR_CORE_LIB.'order.php');
    require_once(DIR_CORE_LIB.'order_status.php');
    require_once(DIR_CORE_LIB.'currency.php');
    require_once(DIR_CORE_LIB.'tax.php');
    require_once(DIR_CORE_LIB.'weight.php');
    require_once(DIR_CORE_LIB.'length.php');
    require_once(DIR_CORE_LIB.'cart.php');
    require_once(DIR_CORE_LIB.'user.php');
    require_once(DIR_CORE_LIB.'dataset.php');
    require_once(DIR_CORE_LIB.'encryption.php');
    require_once(DIR_CORE_LIB.'menu_control.php');
    require_once(DIR_CORE_LIB.'menu_control_storefront.php');
    require_once(DIR_CORE_LIB.'rest.php');
    require_once(DIR_CORE_LIB.'filter.php');
    require_once(DIR_CORE_LIB.'listing.php');
    require_once(DIR_CORE_LIB.'task_manager.php');
    require_once(DIR_CORE_LIB.'im.php');
    require_once(DIR_CORE_LIB.'csrf_token.php');

    /* Admin manager classes */
    if (IS_ADMIN === true) {
        require_once(DIR_CORE_LIB.'order_manager.php');
        require_once(DIR_CORE_LIB.'layout_manager.php');
        require_once(DIR_CORE_LIB.'content_manager.php');
        require_once(DIR_CORE_LIB.'package_manager.php');
        require_once(DIR_CORE_LIB.'form_manager.php');
        require_once(DIR_CORE_LIB.'extension_manager.php');
        require_once(DIR_CORE_LIB.'resource_manager.php');
        require_once(DIR_CORE_LIB.'resource_upload.php');
        require_once(DIR_CORE_LIB.'listing_manager.php');
        require_once(DIR_CORE_LIB.'attribute_manager.php');
        require_once(DIR_CORE_LIB.'language_manager.php');
        require_once(DIR_CORE_LIB.'backup.php');
        require_once(DIR_CORE_LIB.'file_uploads_manager.php');
        require_once(DIR_CORE_LIB.'admin_commands.php');
        require_once(DIR_CORE_LIB.'im_manager.php');
    }

    // Registry
    $registry = Registry::getInstance();

    // Loader
    $registry->set('load', new ALoader($registry));

    // Request
    $request = new ARequest();
    $registry->set('request', $request);

    // Response
    $response = new AResponse();
    $response->addHeader('Content-Type: text/html; charset=utf-8');
    $registry->set('response', $response);
    unset($response);

    // URL Class
    $registry->set('html', new AHtml($registry));

    //Hook class
    $hook = new AHook($registry);

    // Database
    $registry->set('db', new ADB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE));

    // Cache
    $registry->set('cache', new ACache());

    // Config
    $config = new AConfig($registry);
    $registry->set('config', $config);

    // Session
    $registry->set('session', new ASession(SESSION_ID) );
    if ($config->has('current_store_id')) {
        $registry->get('session')->data['current_store_id'] = $config->get('current_store_id');
    }

    // CSRF Token Class
    $registry->set('csrftoken', new CSRFToken());

    // Set up HTTP and HTTPS based automatic and based on config
    if (IS_ADMIN === true) {
        define('HTTP_DIR_NAME', rtrim(dirname($_SERVER['PHP_SELF']), '/.\\'));
        // Admin HTTP
        define('AUTO_SERVER', '//'.REAL_HOST.HTTP_DIR_NAME.'/');
        define('HTTP_SERVER', 'http:'.AUTO_SERVER);
        define('HTTP_CATALOG', HTTP_SERVER);
        define('HTTP_EXT', HTTP_SERVER.'extensions/');
        define('HTTP_IMAGE', HTTP_SERVER.'image/');
        define('HTTP_DIR_RESOURCE', HTTP_SERVER.'resources/');
        //we use Protocol-relative URLs here
        define('HTTPS_IMAGE', AUTO_SERVER.'image/');
        define('HTTPS_DIR_RESOURCE', AUTO_SERVER.'resources/');
        //Admin HTTPS
        if ( HTTPS === true) {
            define('HTTPS_SERVER', 'https:' . AUTO_SERVER);
            define('HTTPS_CATALOG', HTTPS_SERVER);
            define('HTTPS_EXT', HTTPS_SERVER.'extensions/');
        } else {
            define('HTTPS_SERVER', HTTP_SERVER);
            define('HTTPS_CATALOG', HTTP_CATALOG);
            define('HTTPS_EXT', HTTP_EXT);
        }
        /* Admin specific loads */
        $registry->set('extension_manager', new AExtensionManager());

        /* Now we have session, reload config for store if provided or set in session */
        $session = $registry->get('session');
        if (has_value($request->get['store_id']) || has_value($session->data['current_store_id']) ) {
            $config = new AConfig($registry);
            $registry->set('config', $config);
        }
    } else {
        // Storefront HTTP
        $store_url = $config->get('config_url');
        define('HTTP_SERVER', $store_url);
        define('HTTP_IMAGE', HTTP_SERVER.'image/');
        define('HTTP_EXT', HTTP_SERVER.'extensions/');
        define('HTTP_DIR_RESOURCE', HTTP_SERVER.'resources/');
        // Storefront HTTPS
        if ($config->get('config_ssl') || HTTPS === true) {
            if ( $config->get('config_ssl_url') ) {
                $store_url = $config->get('config_ssl_url');
            }
            define('AUTO_SERVER', '//'.preg_replace('/\w+:\/\//', '', $store_url));
            define('HTTPS_SERVER', 'https:'.AUTO_SERVER);
            define('HTTPS_EXT', HTTPS_SERVER.'extensions/');
        } else {
            define('AUTO_SERVER', '//'.preg_replace('/\w+:\/\//', '', $store_url));
            define('HTTPS_SERVER', HTTP_SERVER);
            define('HTTPS_EXT', HTTP_EXT);
        }
        //we use Protocol-relative URLs here
        define('HTTPS_DIR_RESOURCE', AUTO_SERVER.'resources/');
        define('HTTPS_IMAGE', AUTO_SERVER.'image/');
        //set internal sign of shared ssl domains
        if (preg_replace('/\w+:\/\//', '', HTTPS_SERVER) != preg_replace('/\w+:\/\//', '', HTTP_SERVER)) {
            $registry->get('config')->set('config_shared_session',true);
        }
    }

    //Messages
    $registry->set('messages', new AMessage());

    // Log
    $registry->set('log', new ALog(DIR_LOGS.$config->get('config_error_filename')));

    // Document
    $registry->set('document', new ADocument());

    // AbanteCart Snapshot details
    $registry->set('snapshot', 'AbanteCart/'.VERSION .' '.$_SERVER['SERVER_SOFTWARE'].'('.$_SERVER['SERVER_NAME'].')');

    /* Non-apache fix for REQUEST_URI */
    if (!isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);
        if (isset($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    $registry->set('uri', $_SERVER['REQUEST_URI']);

    //main instance of data encryption 
    $registry->set('dcrypt', new ADataEncryption());

    // Extensions api
    $extensions = new ExtensionsApi();
    if (IS_ADMIN === true) {
        //for admin we load all available(installed) extensions. 
        //This is a solution to make controllers and hooks available for extensions that are in the status off. 
        $extensions->loadAvailableExtensions();
    } else {
        $extensions->loadEnabledExtensions();
    }
    $registry->set('extensions', $extensions);

    //validate template
    $is_valid = false;
    $enabled_extensions = $extensions->getEnabledExtensions();
    unset($extensions);

    //check if we specify template directly
    $template = 'default';
    if (IS_ADMIN !== true && !empty($request->get['sf'])) {
        $template = preg_replace('/[^A-Za-z0-9_]+/', '', $request->get['sf']);
        $dir = $template . DIR_EXT_STORE . DIR_EXT_TEMPLATE . $template;
        if (in_array($template, $enabled_extensions) && is_dir(DIR_EXT . $dir)) {
            $is_valid = true;
        } else {
            $is_valid = false;
        }
    }

    if (!$is_valid) {
        //check template defined in settings
        if (IS_ADMIN===true) {
            $template = $config->get('admin_template');
            $dir = $template.DIR_EXT_ADMIN.DIR_EXT_TEMPLATE.$template;
        } else {
            $template = $config->get('config_storefront_template');
            $dir = $template.DIR_EXT_STORE.DIR_EXT_TEMPLATE.$template;
        }

        if (in_array($template, $enabled_extensions) && is_dir(DIR_EXT.$dir)) {
            $is_valid = true;
        } else {
            $is_valid = false;
        }
        //check if this is default template
        if (!$is_valid && is_dir(DIR_TEMPLATE.$template)) {
            $is_valid = true;
        }
    }

    if (!$is_valid) {
        $error = new AError ('Template '.$template.' is not found - roll back to default');
        $error->toLog()->toDebug();
        $template = 'default';
    }

    if (IS_ADMIN === true) {
        $config->set('original_admin_template', $config->get('admin_template'));
        $config->set('admin_template', $template);
        // Load language
        $lang_obj = new ALanguageManager($registry);
    } else {
        $config->set('original_config_storefront_template', $config->get('config_storefront_template'));
        $config->set('config_storefront_template', $template);
        // Load language
        $lang_obj = new ALanguage($registry);
    }

    // Create Global Layout Instance
    $registry->set('layout', new ALayout($registry, $template));

    // load download class
    $registry->set('download',new ADownload());

    //load main language section
    $registry->set('language', $lang_obj);
    unset($lang_obj);
    $registry->get('language')->load();
    $hook->hk_InitEnd();

    //load order status class
    $registry->set('order_status',new AOrderStatus($registry));

    //IM
    if(IS_ADMIN===true){
        $registry->set('im', new AIMManager());
    }else{
        $registry->set('im', new AIM());
    }
}
catch (AException $e) {
    ac_exception_handler($e);
}
