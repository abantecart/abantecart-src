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

final class ACurrency {
  	private $code;
  	private $currencies = array();
  
  	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->language = $registry->get('language');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currencies");

    	foreach ($query->rows as $result) {
      		$this->currencies[$result['code']] = array(
			    'code'          => $result['code'],
        		'currency_id'   => $result['currency_id'],
        		'title'         => $result['title'],
        		'symbol_left'   => $result['symbol_left'],
        		'symbol_right'  => $result['symbol_right'],
        		'decimal_place' => $result['decimal_place'],
        		'value'         => $result['value'],
			    'status'        => $result['status']
      		); 
    	}		
		
		if (isset($this->request->get['currency']) && (array_key_exists($this->request->get['currency'], $this->currencies))) {
			$this->set($this->request->get['currency']);
			unset($this->request->get['currency'],
				  $this->session->data['shipping_methods'],
				  $this->session->data['shipping_method']);

    	} elseif ((isset($this->session->data['currency'])) && (array_key_exists($this->session->data['currency'], $this->currencies))) {
      		$this->set($this->session->data['currency']);
    	} elseif ((isset($this->request->cookie['currency'])) && (array_key_exists($this->request->cookie['currency'], $this->currencies))) {
			if(IS_ADMIN===true){
				$this->set($this->config->get('config_currency'));
			}else{
      			$this->set($this->request->cookie['currency']);
			}
    	} else {
      		$this->set($this->config->get('config_currency'));
    	}
  	}
	
  	public function set($currency) {
		  // if currency disabled - set first enabled from list
		if(!$this->currencies[$currency]['status']){
			foreach($this->currencies as $curr){
				if($curr['status']){
					$currency = $curr['code'];
					break;
				}
			}
		}

    	$this->code = $currency;

    	if ((!isset($this->session->data['currency'])) || ($this->session->data['currency'] != $currency)) {
      		$this->session->data['currency'] = $currency;
    	}

    	if ((!isset($this->request->cookie['currency'])) || ($this->request->cookie['currency'] != $currency)) {
	  		setcookie('currency', $currency, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
    	}
  	}
  	
  	/*
  	* Format only number part (digit based)
  	*/
  	public function format_number($number, $currency = '', $crr_value = '') {
		return $this->format($number, $currency, $crr_value, FALSE);
	}

  	/*
  	* Format number part and/or currency symbol
  	*/
  	public function format($number, $currency = '', $crr_value = '', $format = TRUE) {
		if ( empty ($currency) ) {
			$currency = $this->code;
		}   	
		   	
    	if (!$crr_value) {
      		$crr_value = $this->currencies[$currency]['value'];
    	}

    	if ($crr_value) {
      		$value = $number * $crr_value;
    	} else {
      		$value = $number;
    	}

    	$string = '';
    	$symbol_left = '';
    	$symbol_right = '';
      	$decimal_place = $this->currencies[$currency]['decimal_place'];

    	if ($format) {
      		$symbol_left   = $this->currencies[$currency]['symbol_left'];
      		$symbol_right  = $this->currencies[$currency]['symbol_right'];
			$decimal_point = $this->language->get('decimal_point');
			$thousand_point = $this->language->get('thousand_point');			
		} else {
			$decimal_point = '.';
			$thousand_point = '';
		}
		
    	$string = $symbol_left . number_format(round($value, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point) .$symbol_right;

    	return $string;
  	}
	
  	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 0;
		}
		
		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 0;
		}		
		
		return $value * ($to / $from);
  	}
	
  	public function getCurrencies() {
        return $this->currencies;
  	}
  	public function getCurrency( $code = '' ) {
        if ( $code == '' ) $code = $this->code;
		return $this->currencies[$code];
  	}

    public function getId() {
		return $this->currencies[$this->code]['currency_id'];
  	}
	
  	public function getCode() {
    	return $this->code;
  	}
  
  	public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
  	}
    
  	public function has($currency) {
    	return isset($this->currencies[$currency]);
  	}
}
?>