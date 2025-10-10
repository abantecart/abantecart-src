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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

require_once(DIR_CORE . DS . 'lib' . DS . 'exceptions' . DS . 'exception_codes.php');
require_once(DIR_CORE . DS . 'lib' . DS . 'exceptions' . DS . 'exception.php');

/**
 * called for php errors
 *
 * @param int $errNo
 * @param string $errStr
 * @param string $errFile
 * @param string $errLine
 *
 * @throws AException
 */
function ac_error_handler($errNo, $errStr, $errFile, $errLine)
{
    if (error_reporting() == 0) {
        // Error reporting is currently turned off or suppressed with @
        return;
    }

    if (class_exists('Registry')) {
        $registry = Registry::getInstance();
        if ($registry->get('force_skip_errors')) {
            return;
        }
    }

    //skip notice
    if ($errNo == E_NOTICE) {
        return;
    }

    try {
        throw new AException($errNo, $errStr, $errFile, $errLine);
    } catch (AException $e) {
        ac_exception_handler($e);
    }
}

/**
 * called for caught exceptions
 *
 * @param AException $e
 *
 * @throws AException
 */
function ac_exception_handler($e)
{
    if (class_exists('Registry')) {
        $registry = Registry::getInstance();
        if ($registry->get('force_skip_errors')) {
            return;
        }
    }

    //fix for default PHP handler call in third party PHP libraries
    if (!method_exists($e, 'logError')) {
        $e = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), null, $e);
    }

    if (class_exists('Registry')) {
        $registry = Registry::getInstance();
        $config = $registry->get('config');

        if (!$config || (!$config->has('config_error_log') && !$config->has('config_error_display'))) {
            //we have no config or both settings are missing. 
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
        //do we have a fatal error and need to end?
        if (in_array($e->getCode(), AException::$criticalErrors)
            && !defined('INSTALL')
        ) {
            $e->showErrorPage();
        } else {
            //nothing critical
            return;
        }
    }

    //no registry, something totally wrong
    $e->logError();
    $e->displayError();
    if (in_array($e->getCode(), AException::$criticalErrors)) {
        $e->showErrorPage();
    }
}

/**
 * called on application shutdown
 * check if shutdown was caused by an error and write it to log
 */
function ac_shutdown_handler()
{
    $error = error_get_last();
    if (!is_array($error) || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        return;
    }
    $exception = new AException($error['type'], $error['message'], $error['file'], $error['line']);
    $exception->logError();
}

set_error_handler('ac_error_handler');
register_shutdown_function("ac_shutdown_handler");
set_exception_handler('ac_exception_handler');