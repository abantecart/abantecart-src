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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class AWarning extends AError {

	/**
	 * warning constructor.
	 *
	 * @param  $msg - warning message
	 * @param  $code - warning code
	 */
	public function __construct($msg, $code = AC_ERR_USER_WARNING) {
		parent::__construct($msg, $code);
		$backtrace = debug_backtrace();
		$this->msg = $msg . ' in ' . $backtrace[ 0 ][ 'file' ] . ' on line ' . $backtrace[ 0 ][ 'line' ];
	}

	/**
	 * add warning message to debug log
	 *
	 * @return ADebug
	 */
	public function toDebug() {
		ADebug::warning($this->error_descriptions[ $this->code ], $this->code, $this->msg);
		return $this;
	}

	/**
	 * add warning message to messages
	 * @return ADebug
	 */
	public function toMessages() {
		if (is_object($this->registry) && $this->registry->has('messages')) {
			/**
			 * @var $messages AMessage
			 */
			$messages = $this->registry->get('messages');
			$messages->saveWarning($this->error_descriptions[ $this->code ], $this->msg);
		}
		return $this;
	}


}