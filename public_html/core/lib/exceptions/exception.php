<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class AException extends Exception
{
    public $registry = null;
    protected $error;
    protected $extraData;
    static $criticalErrors = [
        0, // unknown fatal errors from php-core or errors without error code will be fatal
        E_ERROR,
        E_PARSE,
        E_CORE_ERROR,
        E_COMPILE_ERROR,
        AC_ERR_CLASS_CLASS_NOT_EXIST,
        AC_ERR_CLASS_METHOD_NOT_EXIST,
        AC_ERR_CLASS_PROPERTY_NOT_EXIST,
        AC_ERR_USER_ERROR,
        AC_ERR_MYSQL,
        AC_ERR_REQUIREMENTS,
        AC_ERR_LOAD,
        AC_ERR_CONNECT_METHOD,
        AC_ERR_CONNECT,
        AC_ERR_LOAD_LAYOUT,
    ];

    /**
     * @param int $errno
     * @param string|null $errstr
     * @param string|null $file
     * @param string|null $line
     * @param array|null $extra
     * @param Exception|null $initiator
     */
    public function __construct($errno = 0, $errstr = '', $file = '', $line = '', $extra = null, $initiator = null)
    {
        parent::__construct();
        $this->code = $errno ?: $this->code;
        $this->message = $errstr ?: $this->message;
        $this->file = $file ?: $this->file;
        $this->line = $line ?: $this->line;
        $this->extraData = $extra;
        if (class_exists('Registry')) {
            $this->registry = Registry::getInstance();
        }

        //pass trace from initiator
        if ($initiator && method_exists($initiator, 'getTraceAsString') && !str_contains($this->message, 'Trace')) {
            $this->message .= "\nTrace: \n" . $initiator->getTraceAsString();
        }

        $this->error = new AError($this->message, $this->code);
        ob_start();
        echo $this->message . ' in ' . $this->file . ' on line ' . $this->line;
        $this->error->msg = ob_get_clean();
    }

    public function errorCode()
    {
        return $this->code;
    }

    public function getExtraData()
    {
        return $this->extraData;
    }

    public function errorMessage()
    {
        return $this->error->msg;
    }

    public function displayError()
    {
        $this->error->toDebug();
        //Fatal error
        if ($this->code >= 10000 && !defined('INSTALL')) {
            if ($this->registry && $this->registry->get('session')) {
                $this->registry->get('session')->data['exception_msg'] = $this->error->msg;
            } else {
                //Fatal error happened before session is started, show to the screen and exit
                echo $this->error->msg;
                exit;
            }
        }
    }

    public function logError()
    {
        $criticalErrors = static::$criticalErrors;

        //error reporting levels based on settings.
        // see admin menu-> system->settings->system -> debugging
        if ($this->registry) {
            $config = $this->registry->get('config');
            if ($config) {
                switch ($config->get('config_debug_level')) {
                    // no logs , only exception errors
                    case 0:
                        if (in_array($this->getCode(), $criticalErrors)) {
                            $this->error->toLog();
                            return;
                        }
                        break;
                    // errors and warnings
                    case 1:
                        if (in_array(
                            $this->getCode(),
                            ($criticalErrors + [
                                    //warnings
                                    E_WARNING,
                                    E_CORE_WARNING,
                                    E_COMPILE_WARNING,
                                    E_USER_WARNING,
                                    AC_ERR_USER_WARNING,
                                ])
                        )
                        ) {
                            $this->error->toLog();
                            return;
                        }
                        break;
                    // basic logs and stack of execution
                    case 2:
                    case 3:
                        if ($this->getCode() > (E_ERROR | E_WARNING | E_PARSE | E_DEPRECATED)) {
                            return;
                        }
                        break;
                    // all errors except notices
                    case 4:
                        if ($this->getCode() > (E_ALL & ~E_NOTICE)) {
                            return;
                        }
                        break;
                    // all errors
                    default:
                        $this->error->toLog();
                        break;
                }
            }
        } else {
            if (in_array($this->getCode(), $criticalErrors)) {
                $this->error->toLog();
                return;
            }
        }
        //if no settings for debug level - write only php and linter errors into the log
        if ($this->getCode() <= 1) {
            $this->error->toLog();
        }
    }

    /**
     * @return AError
     */
    public function mailError()
    {
        return $this->error->toMail();
    }

    /**
     * @throws AException
     */
    public function showErrorPage()
    {
        if ($this->registry && $this->registry->has('router')
            && $this->registry->get('router')->getRequestType() != 'page') {
            $router = new ARouter($this->registry);
            $router->processRoute('error/ajaxerror');
            $this->registry->get('response')->output();
            exit();
        }
        $url = "static_pages/index.php";
        $url .= (defined('IS_ADMIN') && IS_ADMIN === true) ? '?mode=admin' : '';
        header("Location: $url");
        exit();
    }
}