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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class AException extends Exception {

    protected $error;

    public function __construct($errno = 0, $errstr = '', $file = '', $line = '')
    {
        parent::__construct();
        $this->code = $errno ? $errno : $this->code;
        $this->message = $errstr ? $errstr : $this->message;
        $this->file = $file ? $file : $this->file;
        $this->line = $line ? $line : $this->line;

        $this->error = new AError( $this->message, $this->code );
        //update message
        ob_start();
        echo $this->message . ' in <b>' . $this->file . '</b> on line <b>' . $this->line . '</b>';
        //echo "\r\n".'<pre>'.$this->getTraceAsString().'</pre>';
        $this->error->msg = ob_get_clean();
    }

    public function displayError()
    {
        $this->error->toDebug();
        //Fatal error
        if ( $this->code >= 10000 && !defined('INSTALL') ) {
            $_SESSION['exception_msg'] = $this->error->msg;
            $this->_redirect();
        }
    }

    public function logError()
    {
        $this->error->toLog();
        //Fatal error
        if ( $this->code >= 10000 && !defined('INSTALL') ) {
            $this->_redirect();
        }
    }

    public function mailError()
    {
        $this->error->toMail();
    }

    private function  _redirect() {
        $registry = Registry::getInstance();
        if ( $registry->has('router') && $registry->get('router')->getRequestType() != 'page' ) {
            $router = new ARouter($registry);
		    $router->processRoute('error/ajaxerror');
            $registry->get('response')->output();
            exit();
        }
        header('Location: static_pages/index.php');
        exit();
    }

}