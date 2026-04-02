<?php
/**
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2026 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details are bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 *   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *   versions in the future. If you wish to customize AbanteCart for your
 *   needs, please refer to http://www.AbanteCart.com for more information.
 */

if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

/**
 *
 * Class to handle stripe payment transaction
 * @property AConfig $config
 * @property ALoader $load
 * @property ALanguage $language
 * @property ACart $cart
 * @property ACurrency $currency
 * @property ModelExtensionStripe $model_extension_stripe
 */
final class PaymentHandler{
	/**
	 * @var Registry
	 */
	public $registry;

	public function __construct($registry){
		$this->registry = $registry;
	}

	public function __get($key){
		return $this->registry->get($key);
	}

	public function __set($key, $value){
		$this->registry->set($key, $value);
	}

	public function recurring_billing(){
		return false;
	}

	public function id(){
		return 'stripe';
	}

	public function is_avaialable($payment_address){
		$this->load->model('extension/' . $this->id());
		$details = $this->{'model_extension_' . $this->id()}->getMethod($payment_address);
		if($details){
		    return true;
		} else {
		    return false;
		}					
	}

	public function details(){
		return array(
		    'id'         => $this->id,
		    'title'      => $this->language->get('text_title'),
		    'sort_order' => $this->config->get('stripe_sort_order')
		);
	}

	public function validate_payment_details($data = array ()){
		$this->load->language('stripe/stripe');

		//check if saved cc mode is used
		$errors = array ();
		if (!$data['use_saved_cc']){
			if (empty($data['cc_number'])){
				$errors[] = $this->language->get('error_incorrect_number');
			}

			if (empty($data['cc_owner'])){
				$errors[] = $this->language->get('error_incorrect_name');
			}

			if (empty($data['cc_expire_date_month']) || empty($data['cc_expire_date_year'])){
				$errors[] = $this->language->get('error_incorrect_expiration');
			}

			if (strlen($data['cc_cvv2']) != 3 && strlen($data['cc_cvv2']) != 4){
				$errors[] = $this->language->get('error_incorrect_cvv');
			}
		}
		return $errors;
	}

}
