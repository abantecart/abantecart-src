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



//purge _GET
$get = array('mode'=> isset($_GET['mode']) ? $_GET['mode'] : '');
if(!in_array($get['mode'],array('run', 'query'))){ // can be 'query' or 'run'
	$get['mode'] = 'run';
}
// if task details needed for ajax step-by-step run
if($get['mode']=='query'){
	$get['task_name'] = $_GET['task_name'];
}
$_GET = $get;
unset($get);

$_GET['s'] = ADMIN_PATH; // sign of admin side for controllers run from dispatcher
// Load all initial set up
require_once(DIR_ROOT . '/core/init.php');
unset($_GET['s']);// not needed anymore

ADebug::checkpoint('init end');


// Currency
$registry->set('currency', new ACurrency($registry));


//ok... let's start tasks
$tm = new ATaskManager();

if($_GET['mode'] == 'query'){

	//$output = array();
	$tm->getTask();
//TODO: in the future need to add ability json response for task result


}elseif( $_GET['mode'] == 'run' ){
	//try to remove execution time limitation (can not work on some hosts!)
	ini_set("max_execution_time","0");

	//start do tasks one by one
	$tm->runTasks();
}


ADebug::checkpoint('app end');

//display debug info
ADebug::display();

