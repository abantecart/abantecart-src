<?php

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
