<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

require_once(DIR_CORE . '/lib/exceptions/exception_codes.php');
require_once(DIR_CORE . '/lib/exceptions/exception.php');
require_once(DIR_CORE . '/lib/exceptions/php_exception.php');

/**
 * called for php errors
 */
function ac_error_handler($errno, $errstr, $errfile, $errline) {

	//skip notice
	if ( $errno == E_NOTICE )
		return;

    try {
        throw new APhpException($errno, $errstr, $errfile, $errline);
    }
    catch (APhpException $e) {
        ac_exception_handler($e);
    }
}

/**
 * called for caught exceptions
 */
function ac_exception_handler($e)
{
    if (class_exists('Registry') ) {
        $registry = Registry::getInstance();
        $config = $registry->get('config');
        if (!$config || ( !$config->has('config_error_log') && !$config->has('config_error_display') ) ) {
            $e->logError();
            $e->displayError();
        } else {
            if ($config->has('config_error_log') && $config->get('config_error_log')) {
                $e->logError();
            }
            if ($config->has('config_error_display') && $config->get('config_error_display')) {
                $e->displayError();
            }
        }
        return;
    }

    $e->logError();
    $e->displayError();

}

/**
 * called on application shutdown
 * check if shutdown was caused by error and write it to log
 */
function ac_shutdown_handler()
{
    $error = error_get_last();
    if ( !is_array($error) || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        return;
    }
    $exception = new APhpException($error['type'], $error['message'], $error['file'], $error['line']);
    $exception->logError();
} 

set_error_handler('ac_error_handler');
register_shutdown_function("ac_shutdown_handler");
set_exception_handler('ac_exception_handler');