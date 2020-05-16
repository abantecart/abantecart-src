<?php
/*------------------------------------------------------------------------------
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
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class AError
{

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
     * @param  $msg  - error message
     * @param  $code - error code
     */
    public function __construct($msg, $code = AC_ERR_USER_ERROR)
    {

        $backtrace = debug_backtrace();

        $this->code = $code;
        $this->msg = $msg;
        $trace_limit = 5;
        $this->msg .= "\nTrace:\n";
        foreach ($backtrace as $k => $b) {
            if ($k >= $trace_limit) {
                break;
            }
            $file = isset($b['file']) ? $b['file'] : '';
            $line = isset($b['line']) ? $b['line'] : '';
            $this->msg .= "\t#".$k." ".$file.' on line '.$line."\n";
        }

        if (class_exists('Registry')) {
            $this->registry = Registry::getInstance();
        }
        //TODO: use registry object instead?? what if registry not accessible?
        $this->error_descriptions = $GLOBALS['error_descriptions'];

        $this->version = 'AbanteCart core v.'.VERSION;
    }

    /**
     * add error message to debug log
     *
     * @return AError
     */
    public function toDebug()
    {
        ADebug::error($this->error_descriptions[$this->code], $this->code, $this->msg);

        return $this;
    }

    /**
     * write error message to log file
     *
     * @return AError
     */
    public function toLog()
    {
        if (!is_object($this->registry) || !$this->registry->has('log')) {
            if (class_exists('ALog')) {
                $log = new ALog(DIR_SYSTEM.'logs/error.txt');
            } else {
                //we have error way a head of system start
                echo $this->error_descriptions[$this->code].':  '.$this->msg;

                return $this;
            }
        } else {
            $log = $this->registry->get('log');
        }
        $log->write($this->error_descriptions[$this->code].':  '.$this->version.' '.$this->msg);

        return $this;
    }

    /**
     * add error message to messages
     *
     * @param string $subject
     *
     * @return AError
     */
    public function toMessages($subject = '')
    {
        if (is_object($this->registry) && $this->registry->has('messages')) {
            /**
             * @var $messages AMessage
             */
            $messages = $this->registry->get('messages');
            $title = $subject ? $subject : $this->error_descriptions[$this->code];
            $messages->saveError($title, $this->msg);
        }

        return $this;
    }

    /**
     * send error message to mail
     *
     * @return AError
     */
    public function toMail()
    {
        //This is for future development
        return $this;
    }

    /**
     * add error message to JSON output
     *
     * @param string $status_text_and_code - any human readable text string with 3 digit at the end to represent HTTP response code
     *                                     For ex.
     *                                     VALIDATION_ERROR_406
     *
     * @param array  $err_data             - array with error text and params to control ajax
     *                                     error_code -> HTTP error code if missing in $status_text_and_code
     *                                     error_title -> Title for error dialog and header (error constant used be default)
     *                                     error_text -> Error message ( Class construct used by default )
     *                                     show_dialog -> true to show dialog with error
     *                                     reset_value -> true to reset values in a field (if applicable)
     *                                     reload_page -> true to reload page after dialog close
     *                                     TODO: Add redirect_url on dialog close
     *
     * @return mixed
     */
    public function toJSONResponse($status_text_and_code, $err_data = array())
    {
        //detect HTTP response status code based on readable text status
        preg_match('/(\d+)$/', $status_text_and_code, $match);
        if (!$match[0]) {
            if (empty($err_data['error_code'])) {
                $err_data['error_code'] = 400;
            }
        } else {
            $err_data['error_code'] = (int)$match[0];
        }

        if (empty($err_data['error_title'])) {
            $err_data['error_title'] = $this->error_descriptions[$this->code];
        }
        if (empty($err_data['error_text'])) {
            $err_data['error_text'] = $this->msg;
        }
        $http_header_txt = 'HTTP/1.1 '.(int)$err_data['error_code'].' '.$err_data['error_title'];

        if (is_object($this->registry) && $this->registry->has('response')) {
            /**
             * @var $response AResponse
             */
            $response = $this->registry->get('response');
            /**
             * @var $load ALoader
             */
            $load = $this->registry->get('load');
            $response->addHeader($http_header_txt);
            $response->addJSONHeader();
            $load->library('json');
            $response->setOutput(AJson::encode($err_data));

            return null;
        } else {
            //for some reason we do not have registry. do direct output and exit
            if (!headers_sent()) {
                header($http_header_txt);
                header('Content-Type: application/json');
            }
            include_once(DIR_CORE.'lib/json.php');
            echo AJson::encode($err_data);
            exit;
        }
    }

}