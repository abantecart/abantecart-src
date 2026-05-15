<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

// Load Configuration
// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

// Windows IIS Compatibility  
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('IS_WINDOWS', true);
    $root_path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $root_path);
}

define('DIR_ROOT', $root_path);
const DIR_CORE = DIR_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;

if (is_file(DIR_ROOT . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'config.php')) {
    require_once(DIR_ROOT . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'config.php');
} else {
    exit('Fatal Error: web-application configuration not found!');
}

// New Installation
if (!defined('DB_DATABASE')) {
    if (is_dir(DIR_ROOT . DIRECTORY_SEPARATOR . 'install')) {
        header('Location: install/index.php');
    }
    exit('Fatal Error: web-application configuration not found!');
}

//set server name for correct email sending
if (defined('SERVER_NAME') && SERVER_NAME != '') {
    putenv("SERVER_NAME=" . SERVER_NAME);
}
// sign of admin side for controllers run from dispatcher
$_GET['s'] = ADMIN_PATH;
// Load all initial set-up
require_once(DIR_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'init.php');
// not needed anymore
unset($_GET['s']);
const RDIR_TEMPLATE = 'admin/view/default/';
// Currency for processes that require currency
$registry = Registry::getInstance();
$registry->set('currency', new ACurrency($registry));

//process command
$args = $argv;
$script = array_shift($args);
$command = array_shift($args);
$options = getOptionValues();

switch ($command) {
    case "run":
        //try to remove execution time limitation (cannot work on some hosts!)
        ini_set("max_execution_time", "0");
        if ($options['task_id'] && $options['step_id']) {
            $options['task_id'] = (int)$options['task_id'];
            $options['step_id'] = (int)$options['step_id'];
            processTaskStep($options);
        } else {
            if ($options['task_id']) {
                $options['task_id'] = (int)$options['task_id'];
                processTask($options);
            } else {
                processTasks($options);
            }
        }
        break;
    case "get_task":
        echo "Task(s) details:" . PHP_EOL;
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
 * @void
 * @throws AException
 */
function queryTasks($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    if ($options && $options['task_id']) {
        $td = $tm->getTaskById((int)$options['task_id']);
        foreach ($td as $key => $det) {
            echo "\t $key: ";
            if ($key == 'steps') {
                echo "\n\t Number of steps: " . count($det) . PHP_EOL;
                foreach ($det as $id => $step) {
                    echo "\t\t Step ID: " . $id . PHP_EOL;
                }
            } else {
                echo $det . PHP_EOL;
            }
        }
    } else {
        foreach ($tm->getTasks() as $task) {
            echo "Task: " . $task['task_id'] . " / " . $task['name']
                . ", Created: " . $task['date_added'] . " / " . $task['created_by'] . PHP_EOL;
        }
    }
}

/**
 * @param array $options
 *
 * @void
 * @throws AException
 */
function processTasks($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    echo "Starting..." . PHP_EOL;
    $tm->runTasks($options);

    if ($options['show_log']) {
        $run_log = $tm->getRunLog();
        $run_log_text = implode(PHP_EOL, $run_log);
        echo $run_log_text . PHP_EOL;
    }
    echo "Finished" . PHP_EOL;
}

/**
 * @param array $options
 *
 * @void
 * @throws AException
 */
function processTask($options)
{
    $tm_mode = 'cli';
    $tm = new ATaskManager($tm_mode);
    if ($options['force']) {
        $task = $tm->getTaskById((int)$options['task_id']);
        if ($task['status'] == $tm::STATUS_RUNNING) {
            echo "Error: Task ID " . $options['task_id'] . " already running!" . PHP_EOL;
            exit(1);
        }
        echo "Force starting task!" . PHP_EOL;
        if (
            !$tm->updateTask((int)$options['task_id'], ['status' => $tm::STATUS_READY])
            || !($steps = $tm->getTaskSteps((int)$options['task_id']))
        ) {
            echo "Error: Task ID " . $options['task_id'] . " can not be re-started!" . PHP_EOL;
            exit(1);
        }

        foreach ($steps as $step) {
            $tm->updateStep((int)$step['step_id'], ['status' => $tm::STATUS_READY]);
        }
    }

    echo "Running: Task ID " . $options['task_id'] . ":" . PHP_EOL;
    if (!$tm->runTask((int)$options['task_id'])) {
        //error
        echo "Error: Task ID " . $options['task_id'] . " has failed!" . PHP_EOL;
    }
    if ($options['show_log']) {
        $run_log = $tm->getRunLog();
        $run_log_text = implode(PHP_EOL, $run_log);
        echo $run_log_text . PHP_EOL;
    }
    echo "Finished running: Task ID " . $options['task_id'] . ": " . PHP_EOL;
}

/**
 * @param array $options
 *
 * @void
 * @throws AException
 */
function processTaskStep($options)
{
    $tm_mode = 'cli';
    $step_result = false;
    $tm = new ATaskManager($tm_mode);
    echo "Force starting step!" . PHP_EOL;
    if (
        !$tm->updateTask((int)$options['task_id'], ['status' => $tm::STATUS_READY])
        || !$tm->getTaskSteps((int)$options['task_id'])
    ) {
        echo "Error: Task ID " . $options['task_id'] . " can not be re-started!" . PHP_EOL;
        exit(1);
    }
    $tm->updateStep((int)$options['step_id'], ['status' => $tm::STATUS_READY]);

    echo "Running: Task ID " . $options['task_id'] . " Step ID " . $options['step_id'] . ":" . PHP_EOL;
    //start step
    if ($tm->canStepRun((int)$options['task_id'], (int)$options['step_id'])) {
        $step_details = $tm->getTaskStep((int)$options['task_id'], (int)$options['step_id']);
        $step_result = $tm->runStep($step_details);
    }

    if ($options['show_log']) {
        $run_log = $tm->getRunLog();
        $run_log_text = implode(PHP_EOL, $run_log);
        echo $run_log_text . PHP_EOL;
    }

    if (!$step_result) {
        echo "Error: Step ID " . $options['step_id'] . " has failed!" . PHP_EOL;
    } else {
        echo "Finished running: Task ID " . $options['task_id'] . " Step ID " . $options['step_id'] . ":" . PHP_EOL;
    }
}

/**
 * @return array
 */
function getOptionList()
{
    return [
        '--task_id'  => 'Task ID for task to be ran',
        '--step_id'  => 'Step ID for task to be ran',
        '--show_log' => 'Details of the task process from the log',
    ];
}

/**
 * @return string
 */
function help()
{
    $output = "Usage:" . PHP_EOL;
    $output .= "------------------------------------------------" . PHP_EOL;
    $output .= PHP_EOL;
    $output .= "Commands:" . PHP_EOL;
    $output .= "\t" . "usage - get help" . PHP_EOL . PHP_EOL;
    $output .= "\t" . "get_task - tasks ready to be processed or --task_id # to get specific task details" . PHP_EOL;
    $output .= "\t" . "run - process all ready tasks, specified task or specific step in the task with --task_id=# --step_id=# --force" . PHP_EOL;
    $output .= "\t" . "run - process all tasks except already running --force-all" . PHP_EOL . PHP_EOL;

    $output .= "Required Parameters:" . PHP_EOL;
    $options = getOptionList();

    foreach ($options as $opt => $ex) {
        $output .= "\t" . $opt;
        $output .= "=<value>" . "\t" . "$ex";
        $output .= PHP_EOL;
    }

    $output .= PHP_EOL . "Example:" . PHP_EOL;
    $output .= 'php task_cli.php run ';
    foreach ($options as $opt => $ex) {
        $output .= $opt . "=<value> ";
    }
    $output .= PHP_EOL . PHP_EOL;

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
    $options = [];
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
            $value = rtrim($value, '/.\\') . '/';
            //put server name into environment based on url.
            // it will add into config.php
            $server_name = parse_url($value, PHP_URL_HOST);
            putenv("SERVER_NAME=" . $server_name);
        }

        $options[$name] = $value;
    }

    if ($opt_name) {
        return $options[$opt_name] ?? null;
    }

    return $options;
}
