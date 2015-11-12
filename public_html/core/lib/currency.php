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
/**
 * Class ACurrency
 */
final class ACurrency {
  	private $code;
  	private $currencies = array();
    private $config;
    private $db;
    private $language;
    private $request;
    private $session;
    private $log;
    private $message;
	/**
	 * @var bool - sign that currency was switched
	 */
	private $is_switched = false;

    /**
     * @param $registry Registry
     */
    public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->language = $registry->get('language');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		$this->log = $registry->get('log');
        /**
         * @var AMessage
         */
        $this->message = $registry->get('messages');

		$query = $this->db->query("SELECT * FROM " . $this->db->table("currencies"));

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
		
		if (isset($this->request->get['currency']) && array_key_exists($this->request->get['currency'], $this->currencies) ) {
			$this->set($this->request->get['currency']);
			$this->is_switched = true; // set sign for external use via isSwitched method
			unset($this->request->get['currency'],
				  $this->session->data['shipping_methods'],
				  $this->session->data['shipping_method']);

    	} elseif (isset($this->session->data['currency']) && array_key_exists($this->session->data['currency'], $this->currencies) ) {
      		$this->set($this->session->data['currency']);
    	} elseif (isset($this->request->cookie['currency']) && array_key_exists($this->request->cookie['currency'], $this->currencies) ) {
			if(IS_ADMIN===true){
				$this->set($this->config->get('config_currency'));
			}else{
      			$this->set($this->request->cookie['currency']);
			}
    	} else {
			// need to know about currency switch. Check if currency was setted but not in list of available currencies
			if(isset($this->request->get['currency']) || isset($this->session->data['currency']) || isset($this->request->cookie['currency'])){
				$this->is_switched = true; // set sign for external use via isSwitched method
			}
      		$this->set($this->config->get('config_currency'));
    	}
  	}

	/**
	 * @return bool
	 */
	public function isSwitched(){
		return $this->is_switched;
	}

    /**
     * @param string $currency
     */
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
			//Set cookie for the currency code
			setcookie(	'currency',
						$currency, 
						time() + 60 * 60 * 24 * 30, 
						dirname($this->request->server['PHP_SELF']), 
						null,
						(defined('HTTPS') && HTTPS)
					);
    	}
  	}
  	
  	/**
  	 * Format only number part (digit based)
  	 * @param float $number
     * @param string $currency
     * @param string $crr_value
     * @return string
     */
    public function format_number($number, $currency = '', $crr_value = '') {
		return $this->format($number, $currency, $crr_value, FALSE);
	}

  	/**
  	 * Format number part and/or currency symbol
     * @param float $number
     * @param string $currency
     * @param string $crr_value
     * @param bool $format
     * @return string
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
		
    	//check if number is negative
    	$sign = '';
    	if ($value < 0) {
	    	$sign = '-';
    	}
		$formated_number = number_format(round(abs($value), (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);
    	$string = $sign.$symbol_left.$formated_number.$symbol_right;    	
    	
    	return $string;
  	}

    /**
     * @param float $value
     * @param string $code_from
     * @param string $code_to
     * @return float|bool
     */
    public function convert($value, $code_from, $code_to) {
		if (isset($this->currencies[$code_from])) {
			$from = $this->currencies[$code_from]['value'];
		} else {
			$from = 0;
		}
		
		if (isset($this->currencies[$code_to])) {
			$to = $this->currencies[$code_to]['value'];
		} else {
			$to = 0;
		}

        $error = false;
        if(!$to){
            $msg = 'Error: tried to convert into unaccessable currency! Currency code is '.$code_to;
            $this->log->write('ACurrency '.$msg);
            $this->message->saveError('Currency convertion error', $msg );
            $error = true;
        }
        if(!$from){
            $msg = 'Error: tried to convert from unaccessable currency! Currency code is '.$code_from;
            $this->log->write('ACurrency '.$msg);
            $this->message->saveError('Currency convertion error .', $msg );
            $error = true;
        }

        if($error){
            return false;
        }
		
		return $value * ($to / $from);
  	}
	
  	public function getCurrencies() {
        return $this->currencies;
  	}

    /**
     * @param string $code
     * @return array
     */
    public function getCurrency( $code = '' ) {
        if ($code == ''){
            $code = $this->code;
        }
		return $this->currencies[$code];
  	}

    /**
     * @return int
     */
    public function getId() {
		return $this->currencies[$this->code]['currency_id'];
  	}

    /**
     * @return string
     */
    public function getCode() {
    	return $this->code;
  	}

    /**
     * @param string $currency
     * @return float
     */
    public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0.00;
		}
  	}

    /**
     * @param string $code
     * @return bool
     */
    public function has($code) {
    	return isset($this->currencies[$code]);
  	}
}
