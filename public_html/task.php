<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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
if (version_compare(phpversion(), MIN_PHP_VERSION, '<') == true){
	die(MIN_PHP_VERSION . '+ Required for AbanteCart to work properly! Please contact your system administrator or host service provider.');
}

// Load Configuration
// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

// Windows IIS Compatibility  
if (stristr(PHP_OS, 'WIN')){
	define('IS_WINDOWS', true);
	$root_path = str_replace('\\', '/', $root_path);
}

define('DIR_ROOT', $root_path);
define('DIR_CORE', DIR_ROOT . '/core/');

require_once(DIR_ROOT . '/system/config.php');

//set server name for correct email sending
if (defined('SERVER_NAME') && SERVER_NAME != ''){
	putenv("SERVER_NAME=" . SERVER_NAME);
}

// New Installation
if (!defined('DB_DATABASE')){
	header('Location: install/index.php');
	exit;
}

// sign of admin side for controllers run from dispatcher
$_GET['s'] = ADMIN_PATH;
// Load all initial set up
require_once(DIR_ROOT . '/core/init.php');
// not needed anymore
unset($_GET['s']);

//detect run mode
$command_line = false;
if (php_sapi_name() == "cli"){
	//command line
	echo "Running command line \n";
	$command_line = true;
	$mode = 'start';
	$task_id = $argv[1];
	$step_id = $argv[2];
}else{
	$mode = (string)$_GET['mode'];
	$task_id = (int)$_GET['task_id'];
	$step_id = (int)$_GET['step_id'];
}

if(!$mode && !$command_line){
	exit("Error: Unknown mode!");
}


ADebug::checkpoint('init end');

// Currency
$registry->set('currency', new ACurrency($registry));

//ok... let's start tasks
$tm = new ATaskManager();

//if task_id is not presents
if($mode == 'start' && !$task_id){
	//try to remove execution time limitation (can not work on some hosts!)
	ini_set("max_execution_time", "0");
	//start all scheduled tasks one by one
	$tm->runTasks();

}elseif ($mode == 'start' && $task_id && !$step_id){
	$task = $tm->getTaskById($task_id);

	foreach($task['steps'] as $step){
		$tm->updateStep($step['step_id'], array('status'=> $tm::STATUS_READY));
	}

	$tm->updateTask($task_id, array(
			'status' => $tm::STATUS_READY,
			'start_time' => date('Y-m-d H:i:s')));
	//run all steps of task and change it's status after
	$tm->runTask($task_id);

}elseif ($mode == 'start' && $task_id && $step_id){
	if($tm->canStepRun($task_id, $step_id)){
		$step_details = $tm->getTaskStep($task_id, $step_id);
		$tm->runStep($step_details);
	}
}

//get log for each task ans steps
$run_log = $command_line ? $tm->run_log : nl2br($tm->run_log);
echo $run_log;

ADebug::checkpoint('app end');

//display debug info
ADebug::display();
