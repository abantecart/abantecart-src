<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class AException extends Exception
{

    public $registry = null;
    protected $error;

    public function __construct($errno = 0, $errstr = '', $file = '', $line = '')
    {
        parent::__construct();
        $this->code = $errno ? $errno : $this->code;
        $this->message = $errstr ? $errstr : $this->message;
        $this->file = $file ? $file : $this->file;
        $this->line = $line ? $line : $this->line;
        if (class_exists('Registry')) {
            $this->registry = Registry::getInstance();
        }

        $this->error = new AError($this->message, $this->code);
        //update message
        ob_start();
        echo $this->message.' in <b>'.$this->file.'</b> on line <b>'.$this->line.'</b>';
        //echo "\r\n".'<pre>'.$this->getTraceAsString().'</pre>';
        $this->error->msg = ob_get_clean();
    }

    public function errorCode()
    {
        return $this->code;
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
        //error reporting levels based on settings.
        // see admin menu-> system->settings->system -> debugging
        $config = $this->registry->get('config');
        if ($config->get('config_debug')) {
            switch ($config->get('config_debug_level')) {
                // no logs , only exception errors
                case 0:
                    if ($this->getCode() > E_ERROR) {
                        return;
                    }
                    break;
                // errors and warnings
                case 1:
                    if ($this->getCode() > (E_ERROR | E_WARNING | E_DEPRECATED)) {
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
        $url .= (IS_ADMIN === true) ? '?mode=admin' : '';
        header("Location: $url");
        exit();
    }
}