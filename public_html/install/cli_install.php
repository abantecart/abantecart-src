<?php
/*
------------------------------------------------------------------------------
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
------------------------------------------------------------------------------

 * Command line tool for installing AbanteCart
 *
 * Usage:
 * cd install
 *
 *    php cli_install.php install 
 *                               --db_hostname=localhost
 *                                --db_username=root
 *                                --db_password=pass
 *                                --db_database=abantecart
 *                                --db_driver=mysqli
 *                                --db_port=3306
 *                                --username=admin
 *                                --password=admin
 *                                --email=youremail@example.com
 *                               --http_server=http://localhost/abantecart
 */

ini_set('register_argc_argv', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

//list of arguments
$args = $argv;

// Real path (operating system web root) to the directory where abantecart is installed
$root_path = dirname(__FILE__);

if (defined('IS_WINDOWS')) {
    $root_path = str_replace('\\', '/', $root_path);
}
define('DIR_ROOT', $root_path);

// HTTP

//define('HTTP_SERVER', 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/');
//define('HTTP_ABANTECART', 'http://' . $_SERVER['HTTP_HOST'] . rtrim(rtrim(dirname($_SERVER['PHP_SELF']), 'install'), '/.\\'). '/');

// DIR
define('DIR_APP_SECTION', str_replace('\'', '/', realpath(dirname(__FILE__))).'/');
define('DIR_CORE', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/core/');
define('DIR_SYSTEM', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/system/');
define('DIR_CACHE', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/system/cache/');
define('DIR_LOGS', str_replace('\'', '/', realpath(dirname(__FILE__).'/../')).'/system/logs/');
define('DIR_ABANTECART', str_replace('\'', '/', realpath(DIR_APP_SECTION.'../')).'/');
define('DIR_STOREFRONT', DIR_ABANTECART.'/storefront/');
define('DIR_DATABASE', DIR_CORE.'database/');
define('DIR_TEMPLATE', DIR_APP_SECTION.'view/template/');
define('INSTALL', 'true');
// Relative paths and directories
define('RDIR_TEMPLATE', 'view/');

// Startup with local init
require_once('init.php');

//Check if cart is already installed
if (file_exists(DIR_SYSTEM.'config.php')) {
    require_once(DIR_SYSTEM.'config.php');
}

$installed = false;
if (defined('DB_HOSTNAME') && DB_HOSTNAME) {
    $installed = true;
}

//process command

$script = array_shift($args);
$command = array_shift($args);

switch ($command) {

    case "install":

        if ($installed) {
            echo "\n\n"."AbanteCart is already installed!"."\n\n";
            exit(1);
        }

        try {
            $options = getOptionValues();
            $validateOptions = validateOptions($options);
            if (!$validateOptions[0]) {
                echo "\n\n";
                echo "FAILED! Following inputs were missing or invalid: ";
                echo implode(', ', $validateOptions[1])."\n\n";
                exit(1);
            }
            define('HTTP_ABANTECART', $options['http_server']);
            install($options);
            echo "\n";
            echo "SUCCESS! AbanteCart successfully installed on your server\n\n";
            echo "\t"."Store link: ".$options['http_server']."\n\n";
            echo "\t"."Admin link: ".$options['http_server']."?s=".$options['admin_path']."\n\n";
        } catch (ErrorException $e) {
            echo 'FAILED!: '.$e->getMessage().". File: ".$e->getFile()." Line ".$e->getLine()."\n";
            exit(1);
        }
        break;
    case "usage":
    case "help":
    case "--help":
    case "/h":
    default:
        echo help();
}

/*
 *
 * FUNCTIONS
 *
 */

/**
 * @param       $errno
 * @param       $errstr
 * @param       $errfile
 * @param       $errline
 * @param array $errcontext
 *
 * @return bool
 * @throws ErrorException
 */
function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler('handleError');

/**
 * @return array
 */
function getOptionList()
{
    return array(
        '--db_host'          => 'localhost',
        '--db_user'          => 'root',
        '--db_password'      => 'pass',
        '--db_name'          => 'abantecart',
        '--db_driver'        => 'amysqli',
        '--db_prefix'        => 'ac_',
        '--admin_path'       => 'your_admin',
        '--username'         => 'admin',
        '--password'         => 'admin',
        '--email'            => 'your_email@example.com',
        '--http_server'      => 'http://localhost/abantecart',
        '--with-sample-data' => '',
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
    $output .= "\t"."usage - get help"."\n";
    $output .= "\t"."install - run installation process"."\n\n";

    $output .= "Required Parameters:"."\n";
    $options = getOptionList();

    foreach ($options as $opt => $ex) {
        $output .= "\t".$opt;
        if ($ex) {
            $output .= "=<value>"."\t"."[required]";
        } else {
            $output .= "\t"."[optional]";
        }
        $output .= "\n\n";

    }

    $output .= "\n\nExample:\n";

    $output .= 'php cli_install.php install ';
    foreach ($options as $opt => $ex) {
        $output .= $opt.($ex ? "=".$ex : '')."  ";
    }
    $output .= "\n\n";

    return $output;
}

/**
 * @param string $opt_name
 *
 * @return array|string
 * @throws Exception
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

/**
 * @param array $options
 *
 * @return array
 */
function validateOptions($options)
{
    $required = array(
        'db_host',
        'db_user',
        'db_password',
        'db_name',
        'db_prefix',
        'admin_path',
        'username',
        'password',
        'email',
        'http_server',
    );
    $missing = array();
    foreach ($required as $r) {
        if (!array_key_exists($r, $options)) {
            $missing[] = $r;
        }
    }

    $valid = count($missing) === 0;
    return array($valid, $missing);
}

/**
 * @param $options
 */
function install($options)
{
    $errors = checkRequirements($options);
    if (!$errors) {
        writeConfigFile($options);
        if (file_exists(DIR_SYSTEM.'config.php')) {
            require_once(DIR_SYSTEM.'config.php');
        }
        setupDB($options);
        $cache = new ACache();
        $cache->setCacheStorageDriver('file');
        $cache->enableCache();
        $cache->remove('*');
    } else {
        echo 'FAILED! Pre-installation check failed: '.implode("\n\t", $errors)."\n\n";
        exit(1);
    }
}

function checkRequirements($options)
{
    $options['password_confirm'] = $options['password'];
    $registry = Registry::getInstance();
    $registry->get('load')->model('install');
    $registry->get('model_install')->validateRequirements();
    $errors = $registry->get('model_install')->error;
    if (!$errors) {
        $registry->get('model_install')->validateSettings($options);
        $errors = $registry->get('model_install')->error;
    }
    return $errors;
}

function setupDB($data)
{

    $registry = Registry::getInstance();
    $registry->get('load')->model('install');
    $registry->get('model_install')->RunSQL($data);

    $load_data = getOptionValues('with-sample-data');

    if ($load_data) {
        $db = new ADB(
            $data['db_driver'],
            htmlspecialchars_decode($data['db_host']),
            htmlspecialchars_decode($data['db_user']),
            htmlspecialchars_decode($data['db_password']),
            htmlspecialchars_decode($data['db_name'])
        );
        $registry->set('db', $db);
        define('DIR_LANGUAGE', DIR_ABANTECART.'admin/language/');

        $registry->get('model_install')->loadDemoData($registry);
    }

}

/**
 * @param $options
 */
function writeConfigFile($options)
{
    $registry = Registry::getInstance();
    $registry->get('load')->model('install');
    $registry->get('model_install')->configure($options);

}