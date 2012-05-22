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

final class AResponse {
	private $headers = array(); 
	private $output;
	private $level = 0;
	private $registry;

    public function __construct() {
	    $this->registry = Registry::getInstance();
        $this->output = '';
    }
	
	public function addHeader($header) {
		$header_name = explode(":",$header);
		$header_name = strtolower( trim ($header_name[0]));
		$this->headers[$header_name] = $header;
	}

	public function redirect($url) {
		header('Location: ' . $url);
		exit;
	}

	public function setOutput($stdout, $level = null) {
		$this->output = $stdout;
		if(is_null($level)){
			$level =  $this->registry->get('config') ? (int)$this->registry->get('config')->get('config_compression') : 0;
		}
		$this->level = $level;
	}

    public function getOutput() {
		return $this->output;
	}

    public function cleanOutput() {
		unset($this->output);
	}

	private function compress($data, $level = 0) {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)) {
			$encoding = 'gzip';
		} 

		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== FALSE)) {
			$encoding = 'x-gzip';
		}

		if (!isset($encoding)) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status()) { 
			return $data;
		}

		$this->addHeader('Content-Encoding: ' . $encoding);

		return gzencode($data, (int)$level);
	}

	public function output() {
		if ($this->level && $this->registry->get('config')) {
			$output = $this->compress($this->output, $this->level);
		} else {
			$output = $this->output;
		}	
			
		if (!headers_sent()) {
			foreach ($this->headers as $header) {
				header($header, TRUE);
			}
		}
		
		echo $output;
	}
}
?>