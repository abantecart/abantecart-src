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

class ARest {
	
	private $request = array(); 
	private $response = array();
	private $responseStatus;
	private $registry;
	
	const DEFAULT_RESPONSE_FORMAT = 'json'; // Default response format

	/**
	 * Supported data types and static data
	 * 
	 */
	private static $formats = array('xml', 'json', 'qs');

	private static $contentTypes = array(
				'xml' => 'application/xml',
				'json' => 'application/json',
				'qs' => 'text/plain'
			);

	private static $staus_codes = array(  
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',  
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',  
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'  
        );  
		
	public function __construct() {
		$this->registry = Registry::getInstance ();
		$this->processRequest();		
	}
	
	/**
	 * Processing raw HTTP requests
	*/
	private function processRequest() {
		$this->request['method'] = strtolower($_SERVER['REQUEST_METHOD']);
		$this->request['headers'] = $this->_getHeaders();
		$this->request['format'] = isset($_GET['format']) ? trim($_GET['format']) : null;
		switch($this->request['method']) {
			case 'get':
				$this->request['params'] = $_GET;
				break;
			case 'post':
				$this->request['params'] = $_POST;
				break;
			case 'put':
				parse_str(file_get_contents('php://input'), $this->request['params']);
            	break;
			case 'delete':
				$this->request['params'] = $_GET;
				break;
			default:
				break;
		}
		$this->request['content-type'] = $this->_getResponseFormat($this->request['format']);
		if(!function_exists('trim_value')) {
			function trim_value(&$value) {
				$value = trim($value);
			}
		}
		array_walk_recursive($this->request, 'trim_value');
	}

	/*
	* Adding to the responce array 
	*/
	public function setResponseData( $response_arr ) {
		$this->response = $response_arr;
	}

	public function sendResponse( $status, $response_arr = array() ) {
		$this->responseStatus = $status;
		
		if(!empty($response_arr)) {
			$this->setResponseData( $response_arr );
		}
		
		if(!empty($this->response)) {
			$method = $this->request['content-type'] . 'Response';
			$this->response = array('status' => $this->responseStatus, 'body' => $this->$method());
		} else {
			$this->request['content-type'] = 'qs';
			$this->response = array('status' => $this->responseStatus, 'body' => $this->response);
		}
		
		$status = (isset($this->response['status'])) ? $this->response['status'] : 200;
		$contentType = $this->_getResponseContentType($this->request['content-type']);
		$body = (empty($this->response['body'])) ? '' : $this->response['body'];

		$headers = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusMessage($status);

		//Prepare output
		$this->registry->get('response')->addHeader($headers);
		$this->registry->get('response')->addHeader('Content-Type: ' . $contentType);
		$this->registry->get('response')->addHeader("Access-Control-Allow-Origin:  " . $_SERVER['HTTP_ORIGIN']);
		$this->registry->get('response')->addHeader("Access-Control-Allow-Credentials: true");
		$this->registry->get('response')->setOutput( $body );
	}

	public function getRequestMethod() {
		return $this->request['method'];
	}

	public function getRequestParams() {
		return $this->request['params'];
	}

	public function getRequestParam( $param_name ) {
		return $this->request['params'][ $param_name ];
	}
	
	private function _xmlHelper($data, $version = '1.0', $encoding = 'UTF-8') {
		$xml = new XMLWriter;
		$xml->openMemory();
		$xml->startDocument($version, $encoding);

		if(!function_exists('write')) {
			function write(XMLWriter $xml, $data, $old_key = null) {
				foreach($data as $key => $value){
					if(is_array($value)){
						if(!is_int($key)) {
							$xml->startElement($key);
						}
						write($xml, $value, $key);
						if(!is_int($key)) {
							$xml->endElement();
						}
						continue;
					}
					// Special handling for integer keys in array
					$key = (is_int($key)) ? $old_key.$key : $key;
					$xml->writeElement($key, $value);
				}
			}
		}
		write($xml, $data);
		return $xml->outputMemory(true);
	}
	
	private function xmlResponse() {
		return $this->_xmlHelper($this->response);
	}

	private function jsonResponse() {
		$this->registry->get('load')->library('json');
		//autodetect JSON/JSONP
		if ( $this->request['params']['callback'] ) {
			return  $this->request['params']['callback']  ."(" . AJson::encode($this->response) . ")";
		} else {
			return AJson::encode($this->response);
		}		
	}

	private function qsResponse() {
		return http_build_query($this->response);
	}

	private function _getHeaders() {
		if(function_exists('apache_request_headers')) {
			return apache_request_headers();
		}
		$headers = array();
		$keys = preg_grep('{^HTTP_}i', array_keys($_SERVER));
		foreach($keys as $val) {
				$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($val, 5)))));
				$headers[$key] = $_SERVER[$val];
			}
		return $headers;
	}
	
  
	private function _getStatusMessage($status) {
        return (isset(self::$staus_codes[$status])) ? self::$staus_codes[$status] : self::$staus_codes[500];
    }

	private function _getResponseFormat($format) {
		return (in_array($format, self::$formats)) ? $format : self::DEFAULT_RESPONSE_FORMAT;
	}

	private function _getResponseContentType($type = null) {
		return self::$contentTypes[$type];
	}
		
}
