<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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


class AError {

    /**
     * error code
     *
     * @var int
     */
    public $code;

    /**
     *  error message
     *
     * @var string
     */
    public $msg;

    /**
     * registry to provide access to cart objects
     *
     * @var object Registry
     */
    protected $registry = null;

    /**
     * array of error descriptions by code
     *
     * @var array
     */
    protected $error_descriptions;

    /**
     * error constructor.
     *
     * @param  $msg - error message
     * @param  $code - error code
     */
	public function __construct( $msg, $code = AC_ERR_USER_ERROR )
    {

        $backtrace = debug_backtrace();

        $this->code = $code;
        $this->msg = $msg . ' in ' . $backtrace[0]['file'] . ' on line ' . $backtrace[0]['line'];

        if (class_exists('Registry') ) {
            $this->registry = Registry::getInstance();
        }
        //TODO: use registry object instead?? what if registry not accessible?
        $this->error_descriptions = $GLOBALS['error_descriptions'];

	}

    /**
     * add error message to debug log
     *
     * @return void
     */
    public function toDebug()
    {
        ADebug::error($this->error_descriptions[$this->code], $this->code, $this->msg);
        return $this;
    }

    /**
     * write error message to log file
     *
     * @return void
     */
    public function toLog()
    {
        if (!is_object($this->registry) || !$this->registry->has('log') ) {
            $log = new ALog('system/logs/error.txt');
        } else {
            $log = $this->registry->get('log');
        }
        $log->write($this->error_descriptions[$this->code] . ':  ' . $this->msg);
        return $this;
    }

    /**
     * add error message to messages
     *
     * @return void
     */
    public function toMessages()
    {
        if (is_object($this->registry) && $this->registry->has('messages') ) {
            $messages = $this->registry->get('messages');
            $messages->saveError($this->error_descriptions[$this->code], $this->msg);
        }
        return $this;
    }

    /**
     * send error message to mail
     *
     * @return void
     */
    public function toMail()
    {

        return $this;
    }


}