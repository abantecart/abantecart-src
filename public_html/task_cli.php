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
// Required PHP Version
define('MIN_PHP_VERSION', '5.6.0');
if (version_compare(phpversion(), MIN_PHP_VERSION, '<') == true) {
    die(MIN_PHP_VERSION.'+ Required for AbanteCart to work properly! Please contact your system administrator or host service provider.');
}

if (substr(php_sapi_name(), 0, 3) != 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    //not command line!!
    echo "Not implemented! <br> \n";
    exit;
}

// Load Configuration
// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

// Windows IIS Compatibility  
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('IS_WINDOWS', true);
    $root_path = str_replace('\\', '/', $root_path);
}

define('DIR_ROOT', $root_path);
define('DIR_CORE', DIR_ROOT.'/core/');

require_once(DIR_ROOT.'/system/config.php');
//set server name for correct email sending
if (defined('SERVER_NAME') && SERVER_NAME != '') {
    putenv("SERVER_NAME=".SERVER_NAME);
}
// sign of admin side for controllers run from dispatcher
$_GET['s'] = ADMIN_PATH;
// Load all initial set up
require_once(DIR_ROOT.'/core/init.php');
// not needed anymore
unset($_GET['s']);
// Currency for processes that require currency
$registry->set('currency', new ACurrency($registry));

//process command
$args = $argv;
$script = array_shift($args);
$command = array_shift($args);
$options = getOptionValues();

switch ($command) {
    case "run":
        //try to remove execution time limitation (can not work on some hosts!)
        ini_set("max_execution_time", "0");
        if ($options['task_id'] && $options['step_id']) {
            processTaskStep($options);
        } else {
            if ($options['task_id']) {
                processTask($options);
            } else {
                processTasks($options);
            }
        }
        break;
    case "get_task":
        echo "Task(s) details:\n";
        queryTasks($options);
        break;
    case "usage":
    case "help":
    case "--help":
    case "/h":
    default:
        echo help();
}

/**
 * @param array $options
 *
 * @return none
 */
function queryTasks($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    if ($options && $options['task_id']) {
        $td = $tm->getTaskById($options['task_id']);
        foreach ($td as $key => $det) {
            echo "\t $key: ";
            if ($key == 'steps') {
                echo "\n\t Number of steps: ".count($det)."\n";
                foreach ($det as $id => $step) {
                    echo "\t\t Step ID: ".$id."\n";
                }
            } else {
                echo "$det \n";
            }
        }
    } else {
        foreach ($tm->getTasks() as $task) {
            echo "Task: {$task['task_id']} / {$task['name']}, Created: {$task['date_added']} / {$task['created_by']}  \n";
        }
    }
}

/**
 * @param array $options
 *
 * @return none
 */
function processTasks($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    echo "Run all ready tasks! \n";
    $tm->runTasks();

    if ($options['show_log']) {
        $run_log = $tm->getRunLog();
        $run_log_text = implode("\n", $run_log);
        echo "{$run_log_text}\n";
    }
    echo "Finished all ready tasks! \n";
}

/**
 * @param array $options
 *
 * @return none
 */
function processTask($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    if ($options['force']) {
        echo "Force starting task! \n";
        if (
            !$tm->updateTask($options['task_id'], array('status' => $tm::STATUS_READY))
            || !($steps = $tm->getTaskSteps($options['task_id']))
        ) {
            echo "Error: Task ID {$options['task_id']} can not be re-started! \n";
            exit(1);
        }

        foreach ($steps as $step) {
            $tm->updateStep($step['step_id'], array('status' => $tm::STATUS_READY));
        }
    }

    echo "Running: Task ID {$options['task_id']}: \n";
    if (!$tm->runTask($options['task_id'])) {
        //error
        echo "Error: Task ID ".$options['task_id']." has failed! \n";
    }
    if ($options['show_log']) {
        $run_log = $tm->getRunLog();
        $run_log_text = implode("\n", $run_log);
        echo "{$run_log_text}\n";
    }
    echo "Finished running: Task ID ".$options['task_id'].": \n";
}

/**
 * @param array $options
 *
 * @return none
 */
function processTaskStep($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    echo "Force starting step! \n";
    if (
        !$tm->updateTask($options['task_id'], array('status' => $tm::STATUS_READY))
        || !$tm->getTaskSteps($options['task_id'])
    ) {
        echo "Error: Task ID ".$options['task_id']." can not be re-started! \n";
        exit(1);
    }
    $tm->updateStep($options['step_id'], array('status' => $tm::STATUS_READY));

    echo "Running: Task ID ".$options['task_id']." Step ID ".$options['step_id'].": \n";
    //start step
    if ($tm->canStepRun($options['task_id'], $options['step_id'])) {
        $step_details = $tm->getTaskStep($options['task_id'], $options['step_id']);
        $step_result = $tm->runStep($step_details);
    }

    if ($options['show_log']) {
        $run_log = $tm->getRunLog();
        $run_log_text = implode("\n", $run_log);
        echo "{$run_log_text}\n";
    }

    if (!$step_result) {
        echo "Error: Step ID ".$options['step_id']." has failed! \n";
    } else {
        echo "Finished running: Task ID ".$options['task_id']." Step ID ".$options['step_id'].": \n";
    }
}

/**
 * @return array
 */
function getOptionList()
{
    return array(
        '--task_id'  => 'Task ID for task to be ran',
        '--step_id'  => 'Step ID for task to be ran',
        '--show_log' => 'Details of the task process from the log',
    );
}

/**
 * @return string
 */
function help()
{
    $output = "Usage:"."\n";
    $output .= "------------------------------------------------"."\n";
    $output .= "\n";
    $output .= "Commands:"."\n";
    $output .= "\t"."usage - get help"."\n\n";
    $output .= "\t"."get_task - tasks ready to be processed or --task_id # to get specific task details"."\n";
    $output .= "\t"."run - process all ready tasks, specified task or specific step in the task with --task_id=# --step_id=# --force"."\n\n";

    $output .= "Required Parameters:"."\n";
    $options = getOptionList();

    foreach ($options as $opt => $ex) {
        $output .= "\t".$opt;
        $output .= "=<value>"."\t"."$ex";
        $output .= "\n";
    }

    $output .= "\nExample:\n";
    $output .= 'php task_cli.php run ';
    foreach ($options as $opt => $ex) {
        $output .= $opt."=<value> ";
    }
    $output .= "\n\n";

    return $output;
}

/**
 * @param string $opt_name
 *
 * @return array|string
 */
function getOptionValues($opt_name = '')
{
    global $args;
    $args = !$args ? $_SERVER['argv'] : $args;
    $options = array();
    foreach ($args as $v) {
        $is_flag = preg_match('/^--(.*)$/', $v, $match);
        //skip commands
        if (!$is_flag) {
            continue;
        }

        $arg = $match[1];
        $array = explode('=', $arg);
        if (sizeof($array) > 1) {
            list($name, $value) = $array;
        } else {
            $name = $arg;
            $value = true;
        }

        if ($name == 'http_server') {
            $value = rtrim($value, '/.\\').'/';
            //put server name into environment based on url.
            // it will add into config.php
            $server_name = parse_url($value, PHP_URL_HOST);
            putenv("SERVER_NAME=".$server_name);
        }

        $options[$name] = $value;
    }

    if ($opt_name) {
        return isset($options[$opt_name]) ? $options[$opt_name] : null;
    }

    return $options;
}