<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/

// Required PHP Version
define('MIN_PHP_VERSION', '5.3.0');
if (version_compare(phpversion(), MIN_PHP_VERSION, '<') == TRUE) {
    die( MIN_PHP_VERSION . '+ Required for AbanteCart to work properly! Please contact your system administrator or host service provider.');
}

// Load Configuration
// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

// Windows IIS Compatibility  
if (stristr(PHP_OS, 'WIN')) {
	define('IS_WINDOWS', true);
	$root_path = str_replace('\\', '/', $root_path);
}

define('DIR_ROOT', $root_path);
define('DIR_CORE', DIR_ROOT . '/core/');

require_once(DIR_ROOT . '/system/config.php');
   
// New Installation
if (!defined('DB_DATABASE')) {
	header('Location: install/index.php');
	exit;
}

// Load all initial set up
require_once(DIR_ROOT . '/core/init.php');

ADebug::checkpoint('init end');

if (!defined('IS_ADMIN') || !IS_ADMIN ) { // storefront load

	// Relative paths and directories
	define('RDIR_TEMPLATE',  'storefront/view/' . $config->get('config_storefront_template') . '/');

	// Customer
	$registry->set('customer', new ACustomer($registry));
	
	// Tax
	$registry->set('tax', new ATax($registry));

	// Weight
	$registry->set('weight', new AWeight($registry));

	// Length
	$registry->set('length', new ALength($registry));

	// Cart
	$registry->set('cart', new ACart($registry));

} else {// Admin load

	// Relative paths and directories
	define('RDIR_TEMPLATE',  'admin/view/default/');
	
	// User
	$registry->set('user', new AUser($registry));
					
}// end admin load

// Currency
$registry->set('currency', new ACurrency($registry));


//Route to request process
$router = new ARouter($registry);
$registry->set('router', $router);
$router->processRoute(ROUTE);

// Output
$registry->get('response')->output();


ADebug::checkpoint('app end');

//display debug info
if ( $router->getRequestType() == 'page' ) {
    ADebug::display();
}
