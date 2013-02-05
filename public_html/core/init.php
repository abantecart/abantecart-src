<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

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
include('version.php');
define('VERSION', MASTER_VERSION . '.' . MINOR_VERSION . '.' . VERSION_BUILT);

// Detect if localhost is used.
if (!isset($_SERVER[ 'HTTP_HOST' ])) {
	$_SERVER[ 'HTTP_HOST' ] = 'localhost';
}

// Detect https
if (isset($_SERVER[ 'HTTPS' ]) && ($_SERVER[ 'HTTPS' ] == 'on' || $_SERVER[ 'HTTPS' ] == '1')) {
	define('HTTPS', true);
} elseif (isset($_SERVER[ 'HTTP_X_FORWARDED_SERVER' ]) && ($_SERVER[ 'HTTP_X_FORWARDED_SERVER' ] == 'secure' || $_SERVER[ 'HTTP_X_FORWARDED_SERVER' ] == 'ssl')) {
	define('HTTPS', true);
} elseif (isset($_SERVER[ 'SCRIPT_URI' ]) && (substr($_SERVER[ 'SCRIPT_URI' ], 0, 5) == 'https')) {
	define('HTTPS', true);
} elseif (isset($_SERVER[ 'HTTP_HOST' ]) && (strpos($_SERVER[ 'HTTP_HOST' ], ':443') !== false)) {
	define('HTTPS', true);
}

// Detect http host
if (isset($_SERVER[ 'HTTP_X_FORWARDED_HOST' ])) {
	define('REAL_HOST', $_SERVER[ 'HTTP_X_FORWARDED_HOST' ]);
} else {
	define('REAL_HOST', $_SERVER[ 'HTTP_HOST' ]);
}

//Set up common paths
define('DIR_SYSTEM', DIR_ROOT . '/system/');
define('DIR_IMAGE', DIR_ROOT . '/image/');
define('DIR_DOWNLOAD', DIR_ROOT . '/download/');
define('DIR_DATABASE', DIR_ROOT . '/core/database/');
define('DIR_CONFIG', DIR_ROOT . '/core/config/');
define('DIR_CACHE', DIR_ROOT . '/system/cache/');
define('DIR_LOGS', DIR_ROOT . '/system/logs/');

// Error Reporting
error_reporting(E_ALL);
require_once(DIR_CORE . '/lib/debug.php');
require_once(DIR_CORE . '/lib/exceptions.php');
require_once(DIR_CORE . '/lib/error.php');
require_once(DIR_CORE . '/lib/warning.php');

//Detect the section of the cart to access and build the path definitions
// s=admin or s=storefront (default nothing)
define('INDEX_FILE', 'index.php');

if (defined('ADMIN_PATH') && (isset($_GET[ 's' ]) || isset($_POST[ 's' ])) && ($_GET[ 's' ] == ADMIN_PATH || $_POST[ 's' ] == ADMIN_PATH)) {
	define('IS_ADMIN', true);
	define('DIR_APP_SECTION', DIR_ROOT . '/admin/');
	define('DIR_LANGUAGE', DIR_ROOT . '/admin/language/');
	define('DIR_TEMPLATE', DIR_ROOT . '/admin/view/');
	define('DIR_STOREFRONT', DIR_ROOT . '/storefront/');
	define('DIR_BACKUP', DIR_ROOT . '/admin/system/backup/');
	// Admin
	// HTTP
	define('HTTP_SERVER', 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/');
	define('HTTP_CATALOG', 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/');
	define('HTTP_IMAGE', 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/image/');
	define('HTTP_EXT', 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/extensions/');
	// HTTPS
	if (defined('HTTPS') && HTTPS) {
		define('HTTPS_SERVER', 'https://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/');
		define('HTTPS_CATALOG', 'https://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/');
		define('HTTPS_IMAGE', 'https://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/image/');
		define('HTTPS_EXT', 'https://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/extensions/');
	} else {
		define('HTTPS_SERVER', HTTP_SERVER);
		define('HTTPS_CATALOG', HTTP_CATALOG);
		define('HTTPS_IMAGE', HTTP_IMAGE);
		define('HTTPS_EXT', HTTP_EXT);
	}
	define('SESSION_ID', 'PHPSESSID_AC_CP');
} else {
	define('IS_ADMIN', false);
	define('DIR_APP_SECTION', DIR_ROOT . '/storefront/');
	define('DIR_LANGUAGE', DIR_ROOT . '/storefront/language/');
	define('DIR_TEMPLATE', DIR_ROOT . '/storefront/view/');
	define('SESSION_ID', 'PHPSESSID_AC_SF');
}

try {

// Register Globals
	if (ini_get('register_globals')) {
	        ini_set('session.use_cookies', 'On');
	        ini_set('session.use_trans_sid', 'Off');
	        $path = dirname($_SERVER[ 'PHP_SELF' ]);
	        session_set_cookie_params(0,
	                $path,
	                null,
	                (defined('HTTPS') && HTTPS),
	                true);
	        unset($path);
	        session_name(SESSION_ID);
	        session_start();
	
	        $globals = array( $_REQUEST, $_SESSION, $_SERVER, $_FILES );
	
	        foreach ($globals as $global) {
	                foreach (array_keys($global) as $key) {
	                        unset($$key);
	                }
	        }
	}

// Magic Quotes
	if (ini_get('magic_quotes_gpc')) {
		function clean($data) {
			if (is_array($data)) {
				foreach ($data as $key => $value) {
					$data[ clean($key) ] = clean($value);
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

	if (!isset($_SERVER[ 'DOCUMENT_ROOT' ])) {
		if (isset($_SERVER[ 'SCRIPT_FILENAME' ])) {
			$_SERVER[ 'DOCUMENT_ROOT' ] = str_replace('\\', '/', substr($_SERVER[ 'SCRIPT_FILENAME' ], 0, 0 - strlen($_SERVER[ 'PHP_SELF' ])));
		}
	}

	if (!isset($_SERVER[ 'DOCUMENT_ROOT' ])) {
		if (isset($_SERVER[ 'PATH_TRANSLATED' ])) {
			$_SERVER[ 'DOCUMENT_ROOT' ] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER[ 'PATH_TRANSLATED' ]), 0, 0 - strlen($_SERVER[ 'PHP_SELF' ])));
		}
	}

	if (!isset($_SERVER[ 'REQUEST_URI' ])) {
		$_SERVER[ 'REQUEST_URI' ] = substr($_SERVER[ 'PHP_SELF' ], 1);

		if (isset($_SERVER[ 'QUERY_STRING' ])) {
			$_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
		}
	}

// relative paths for extensions
	define('DIR_EXTENSIONS', 'extensions/');
	define('DIR_EXT', DIR_ROOT . '/' . DIR_EXTENSIONS);
	define('DIR_EXT_CORE', '/core/');
	define('DIR_EXT_STORE', '/storefront/');
	define('DIR_EXT_ADMIN', '/admin/');
	define('DIR_EXT_IMAGE', '/image/');
	define('DIR_EXT_LANGUAGE', 'language/');
	define('DIR_EXT_TEMPLATE', 'view/');

//resources
	define('DIR_RESOURCE', DIR_ROOT . '/resources/');
	define('HTTP_DIR_RESOURCE', 'http://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/resources/');
	define('HTTPS_DIR_RESOURCE', 'https://' . REAL_HOST . rtrim(dirname($_SERVER[ 'PHP_SELF' ]), '/.\\') . '/resources/');

//postfixes for template override
	define('POSTFIX_OVERRIDE', '.override');
	define('POSTFIX_PRE', '.pre');
	define('POSTFIX_POST', '.post');

// Include Engine
	require_once(DIR_CORE . 'engine/router.php');
	require_once(DIR_CORE . 'engine/page.php');
	require_once(DIR_CORE . 'engine/response.php');
	require_once(DIR_CORE . 'engine/api.php');
	require_once(DIR_CORE . 'engine/dispatcher.php');
	require_once(DIR_CORE . 'engine/controller.php');
	require_once(DIR_CORE . 'engine/controller_api.php');
	require_once(DIR_CORE . 'engine/view.php');
	require_once(DIR_CORE . 'engine/loader.php');
	require_once(DIR_CORE . 'engine/model.php');
	require_once(DIR_CORE . 'engine/registry.php');
	require_once(DIR_CORE . 'engine/resources.php');
	require_once(DIR_CORE . 'engine/html.php');
	require_once(DIR_CORE . 'engine/layout.php');
	require_once(DIR_CORE . 'engine/form.php');
	require_once(DIR_CORE . 'engine/extensions.php');
	require_once(DIR_CORE . 'engine/hook.php');
	require_once(DIR_CORE . 'engine/attribute.php');
	require_once(DIR_CORE . 'engine/promotion.php');
	require_once(DIR_CORE . 'engine/language.php');

	require_once(DIR_CORE . 'helper/html.php');
	require_once(DIR_CORE . 'helper/utils.php');

// Include library files
	require_once(DIR_CORE . 'lib/cache.php');
	require_once(DIR_CORE . 'lib/config.php');
	require_once(DIR_CORE . 'lib/db.php');
	require_once(DIR_CORE . 'lib/connect.php');
	require_once(DIR_CORE . 'lib/document.php');
	require_once(DIR_CORE . 'lib/image.php');
	require_once(DIR_CORE . 'lib/log.php');
	require_once(DIR_CORE . 'lib/mail.php');
	require_once(DIR_CORE . 'lib/message.php');
	require_once(DIR_CORE . 'lib/pagination.php');
	require_once(DIR_CORE . 'lib/request.php');
	require_once(DIR_CORE . 'lib/response.php');
	require_once(DIR_CORE . 'lib/session.php');
	require_once(DIR_CORE . 'lib/template.php');
	require_once(DIR_CORE . 'lib/xml2array.php');
	require_once(DIR_CORE . 'lib/data.php');

// Application Classes
	require_once(DIR_CORE . 'lib/customer.php');
	require_once(DIR_CORE . 'lib/order.php');
	require_once(DIR_CORE . 'lib/currency.php');
	require_once(DIR_CORE . 'lib/tax.php');
	require_once(DIR_CORE . 'lib/weight.php');
	require_once(DIR_CORE . 'lib/length.php');
	require_once(DIR_CORE . 'lib/cart.php');
	require_once(DIR_CORE . 'lib/user.php');
	require_once(DIR_CORE . 'lib/dataset.php');
	require_once(DIR_CORE . 'lib/encryption.php');
	require_once(DIR_CORE . 'lib/menu_control.php');
	require_once(DIR_CORE . 'lib/menu_control_storefront.php');
	require_once(DIR_CORE . 'lib/rest.php');
	require_once(DIR_CORE . 'lib/filter.php');
	require_once(DIR_CORE . 'lib/listing.php');

//Admin manager classes
	if (IS_ADMIN) {
		require_once(DIR_CORE . 'lib/layout_manager.php');
		require_once(DIR_CORE . 'lib/content_manager.php');
		require_once(DIR_CORE . 'lib/package_manager.php');
		require_once(DIR_CORE . 'lib/form_manager.php');
		require_once(DIR_CORE . 'lib/extension_manager.php');
		require_once(DIR_CORE . 'lib/resource_manager.php');
		require_once(DIR_CORE . 'lib/resource_upload.php');
		require_once(DIR_CORE . 'lib/listing_manager.php');
		require_once(DIR_CORE . 'lib/attribute_manager.php');
		require_once(DIR_CORE . 'lib/language_manager.php');
		require_once(DIR_CORE . 'lib/backup.php');
	}

// Registry
	$registry = Registry::getInstance();

// Loader
	$loader = new ALoader($registry);
	$registry->set('load', $loader);

// Request
	$request = new ARequest();
	$registry->set('request', $request);

// Response
	$response = new AResponse();
	$response->addHeader('Content-Type: text/html; charset=utf-8');
	$registry->set('response', $response);

// URL Class
	$html = new AHtml($registry);
	$registry->set('html', $html);

//Hook class
	$hook = new AHook($registry);

// Database 
	$db = new ADB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$registry->set('db', $db);

// Cache
	$cache = new ACache();
	$registry->set('cache', $cache);

// Config
	$config = new AConfig($registry);
	$registry->set('config', $config);

	if (IS_ADMIN) {
		//Admin specific loads
		$extension_manager = new AExtensionManager();
		$registry->set('extension_manager', $extension_manager);
	}

//Messages
	$messages = new AMessage();
	$registry->set('messages', $messages);

// Log 
	$log = new ALog(DIR_LOGS . $config->get('config_error_filename'));
	$registry->set('log', $log);

// Session
	$session = new ASession();
	$registry->set('session', $session);


// Document
	$registry->set('document', new ADocument());

// AbanteCart Snapshot details
	$registry->set('snapshot', 'AbanteCart/' . VERSION . ' ' . $_SERVER[ 'SERVER_SOFTWARE' ] . ' (' . $_SERVER[ 'SERVER_NAME' ] . ')');
//Non-apache fix for REQUEST_URI
	if (!isset($_SERVER[ 'REQUEST_URI' ])) {
		$_SERVER[ 'REQUEST_URI' ] = substr($_SERVER[ 'PHP_SELF' ], 1);
		if (isset($_SERVER[ 'QUERY_STRING' ])) {
			$_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
		}
	}
	$registry->set('uri', $_SERVER[ 'REQUEST_URI' ]);

//main instance of data encryption 
	$data_encryption = new ADataEncryption( );
	$registry->set('dcrypt', $data_encryption);

// Extensions api
	$extensions = new ExtensionsApi();
	$extensions->loadEnabledExtensions();
	$registry->set('extensions', $extensions);

//validate template
	$is_valid = false;
	$enabled_extensions = $extensions->getEnabledExtensions();

//check if we specify template directly
	if (!IS_ADMIN && !empty($request->get[ 'sf' ])) {
		$template = preg_replace('/[^A-Za-z0-9_]+/', '', $request->get[ 'sf' ]);
		$dir = $template . DIR_EXT_STORE . DIR_EXT_TEMPLATE . $template;
		if (in_array($template, $enabled_extensions) && is_dir(DIR_EXT . $dir)) {
			$is_valid = true;
		} else {
			$is_valid = false;
		}
	}

	if (!$is_valid) {
		//check template defined in settings
		if (IS_ADMIN) {
			$template = $config->get('admin_template');
			$dir = $template . DIR_EXT_ADMIN . DIR_EXT_TEMPLATE . $template;
		} else {
			$template = $config->get('config_storefront_template');
			$dir = $template . DIR_EXT_STORE . DIR_EXT_TEMPLATE . $template;
		}

		if (in_array($template, $enabled_extensions) && is_dir(DIR_EXT . $dir)) {
			$is_valid = true;
		} else {
			$is_valid = false;
		}
		//check if this is default template
		if (!$is_valid && is_dir(DIR_TEMPLATE . $template)) {
			$is_valid = true;
		}
	}

	if (!$is_valid) {
		$error = new AError ('Template ' . $template . ' is not found - roll back to default');
		$error->toMessages()->toLog()->toDebug();
		$template = 'default';
	}

	if (IS_ADMIN) {
		$config->set('original_admin_template', $config->get('admin_template'));
		$config->set('admin_template', $template);
		// Load language
		$language = new ALanguageManager($registry);
	} else {
		$config->set('original_config_storefront_template', $config->get('config_storefront_template'));
		$config->set('config_storefront_template', $template);
		// Load language
		$language = new ALanguage($registry);
	}

// Create Global Layout Instance
	$registry->set('layout', new ALayout($registry, $template));

//load main language section
	$registry->set('language', $language);
	$registry->get('language')->load();
	$hook->hk_InitEnd();

} //eof try
catch (AException $e) {
	ac_exception_handler($e);
}