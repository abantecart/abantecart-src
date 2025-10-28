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
$root_path = dirname(__DIR__,2);

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
    exit('Fatal Error: configuration not found!');
}

if (!defined('DB_DATABASE')) {
    exit('Fatal Error: configuration not found!');
}

//set server name for correct email sending
if (defined('SERVER_NAME') && SERVER_NAME != '') {
    putenv("SERVER_NAME=" . SERVER_NAME);
}

// Load all initial set-up
require_once(DIR_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'init.php');
