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

	protected $version;

	/**
	 * error constructor.
	 *
	 * @param  $msg - error message
	 * @param  $code - error code
	 */
	public function __construct($msg, $code = AC_ERR_USER_ERROR) {

		$backtrace = debug_backtrace();

		$this->code = $code;
		$this->msg = $msg . ' in ' . $backtrace[ 0 ][ 'file' ] . ' on line ' . $backtrace[ 0 ][ 'line' ];

		if (class_exists('Registry')) {
			$this->registry = Registry::getInstance();
		}
		//TODO: use registry object instead?? what if registry not accessible?
		$this->error_descriptions = $GLOBALS[ 'error_descriptions' ];

		$this->version = 'AbanteCart core v.' . VERSION;
	}

	/**
	 * add error message to debug log
	 *
	 * @return AError
	 */
	public function toDebug() {
		ADebug::error($this->error_descriptions[ $this->code ], $this->code, $this->msg);
		return $this;
	}

	/**
	 * write error message to log file
	 * @return AError
	 */
	public function toLog() {
		if (!is_object($this->registry) || !$this->registry->has('log')) {
			if (class_exists('ALog')) {
				$log = new ALog(DIR_SYSTEM . 'logs/error.txt');
			} else {
				//we have error way a head of system start
				echo $this->error_descriptions[ $this->code ] . ':  ' . $this->msg;
				return $this;
			}
		} else {
			$log = $this->registry->get('log');
		}
		$log->write($this->error_descriptions[ $this->code ] . ':  ' . $this->version . ' ' . $this->msg);
		return $this;
	}

	/**
	 * add error message to messages     *
	 * @return AError
	 */
	public function toMessages() {
		if (is_object($this->registry) && $this->registry->has('messages')) {
			/**
			 * @var $messages AMessage
			 */
			$messages = $this->registry->get('messages');
			$messages->saveError($this->error_descriptions[ $this->code ], $this->msg);
		}
		return $this;
	}

	/**
	 * send error message to mail
	 *
	 * @return AError
	 */
	public function toMail() {
		//This is for future development
		return $this;
	}

	/**
	 * add error message to JSON output
	 *
	 * $status_text_and_code -> any human readable text string with 3 digit at the end to represent HTTP responce code
	 * $err_data -> array with error text and params to control ajax
	 *            error_code -> HTTP error code if missing in $status_text_and_code
	 *            error_title -> Title for error dialog and header (error constant used be default)
	 *            error_text -> Error message ( Class construct used by default )
	 *            show_dialog -> true to show dialog with error
	 *            reset_value -> true to reset values in a field (if applicable)
	 *            reload_page -> true to reload page after dialog close
	 *            TODO: Add redirect_url on dialog close
	 * @param $status_text_and_code
	 * @param array $err_data
	 * @return mixed
	 */
	public function toJSONResponse($status_text_and_code, $err_data = array()) {
		//detect HTTP responce status code based on readable text status
		preg_match('/(\d+)$/', $status_text_and_code, $match);
		if (!$match[ 0 ]) {
			if (empty($err_data[ 'error_code' ])) {
				$err_data[ 'error_code' ] = 400;
			}
		} else {
			$err_data[ 'error_code' ] = (int)$match[ 0 ];
		}

		if (empty($err_data[ 'error_title' ])) {
			$err_data[ 'error_title' ] = $this->error_descriptions[ $this->code ];
		}
		if (empty($err_data[ 'error_text' ])) {
			$err_data[ 'error_text' ] = $this->msg;
		}
		$http_header_txt = 'HTTP/1.1 ' . (int)$err_data[ 'error_code' ] . ' ' . $err_data[ 'error_title' ];

		if (is_object($this->registry) && $this->registry->has('response')) {
			/**
			 * @var $response AResponse
			 */
			$response = $this->registry->get('response');
			/**
			 * @var $load ALoader
			 */
			$load = $this->registry->get('load');
			$response->addheader($http_header_txt);
			$response->addJSONHeader();
			$load->library('json');
			return $response->setOutput(AJson::encode($err_data));
		} else {
			//for some reason we do not have reqistery. do direct output and exit
			header($http_header_txt);
			header('Content-Type: application/json');
			include_once(DIR_CORE . 'lib/json.php');
			echo AJson::encode($err_data);
			exit;
		}
	}

}