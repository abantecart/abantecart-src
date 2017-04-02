<?php
if (!defined('DIR_CORE')){
	header('Location: static_pages/');
}

/**
 * Class ControllerResponsesExtensionDefaultStripe
 * @property ModelExtensionDefaultStripe $model_extension_default_stripe
 */
class ControllerResponsesExtensionDefaultStripe extends AController{

	public function main(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_stripe/default_stripe');

        $data['action'] = $this->html->getSecureURL('extension/default_stripe/send');

        //build submit form
        $form = new AForm();
        $form->setForm(array( 'form_name' => 'stripe' ));
        $data['form_open'] = $form->getFieldHtml(
            array(
                'type' => 'form',
                'name' => 'stripe',
                'attr' => 'class = "validate-creditcard"',
                'csrf' => true
            )
        );

		//neeed an order details 
		$this->loadModel('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$data['payment_address'] = $order_info['payment_address_1'] . " " . $order_info['payment_address_2'];
		$data['edit_address'] = $this->html->getSecureURL('checkout/address/payment');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');


		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['cc_owner'] = $form->getFieldHtml(
		    array (
				'type'        => 'input',
				'name'        => 'cc_owner',
				'placeholder' => $this->language->get('entry_cc_owner'),
				'value'       => $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname']
            )
        );

		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['cc_number'] = $form->getFieldHtml(
		    array (
				'type'        => 'input',
				'name'        => 'cc_number',
				'attr'        => 'autocomplete="off"',
				'placeholder' => $this->language->get('entry_cc_number'),
				'value'       => ''
            )
        );

		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');

		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		$data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
		$data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_stripe/cvv2_help');

		$data['cc_cvv2'] = $form->getFieldHtml(
		    array (
		        'type'  => 'input',
                'name'  => 'cc_cvv2',
                'value' => '',
                'style' => 'short',
                'attr'  => ' autocomplete="off" ',
		    )
        );

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_back'] = $this->language->get('button_back');

		$months = array ();

		for ($i = 1; $i <= 12; $i++){
			$months[sprintf('%02d', $i)] = sprintf('%02d - ', $i) . strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
		}
		$data['cc_expire_date_month'] = HtmlElementFactory::create(
				array ('type'    => 'selectbox',
				       'name'    => 'cc_expire_date_month',
				       'value'   => sprintf('%02d', date('m')),
				       'options' => $months,
				       'style'   => 'input-medium short'
				));

		$today = getdate();
		$years = array ();
		for ($i = $today['year']; $i < $today['year'] + 11; $i++){
			$years[strftime('%Y', mktime(0, 0, 0, 1, 1, $i))] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
		}
		$data['cc_expire_date_year'] = $form->getFieldHtml(
		    array (
		        'type'    => 'selectbox',
                'name'    => 'cc_expire_date_year',
                'value'   => sprintf('%02d', date('Y') + 1),
                'options' => $years,
                'style'   => 'short'
            )
        );

		if ($this->request->get['rt'] == 'checkout/guest_step_3'){
			$back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
		} else{
			$back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
		}
		$data['back'] = $this->html->buildElement(
            array (
                    'type'  => 'button',
                    'name'  => 'back',
                    'text'  => $this->language->get('button_back'),
                    'style' => 'button',
                    'href'  => $back_url,
                    'icon'  => 'icon-arrow-left'
            )
        );

		$data['submit'] = $this->html->buildElement(
            array (
                    'type'  => 'button',
                    'name'  => 'strype_button',
                    'text'  => $this->language->get('button_confirm'),
                    'style' => 'button btn-orange pull-right',
                    'icon'  => 'icon-ok icon-white'
            )
        );

		$this->view->batchAssign($data);

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		//load creditcard input validation
		$this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));

		$this->processTemplate('responses/default_stripe.tpl');
	}

	public function cvv2_help(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('default_stripe/default_stripe');

		$image = '<img src="' . $this->view->templateResource('/image/securitycode.jpg') . '" alt="' . $this->language->get('entry_what_cvv2') . '" />';

		$this->view->assign('title', '');
		$this->view->assign('description', $image);

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/content/content.tpl');
	}

	public function send(){
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

        $json = array();

        if(!$this->csrftoken->isTokenValid()){
            $json['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));
            return;
        }

		$this->loadLanguage('default_stripe/default_stripe');

		//validate input
		$post = $this->request->post;

		//check if saved cc mode is used
		if (!$post['use_saved_cc']){
			if (empty($post['cc_number'])){
				$json['error'] = $this->language->get('error_incorrect_number');
			}

			if (empty($post['cc_owner'])){
				$json['error'] = $this->language->get('error_incorrect_name');
			}

			if (empty($post['cc_expire_date_month']) || empty($post['cc_expire_date_year'])){
				$json['error'] = $this->language->get('error_incorrect_expiration');
			}

			if (strlen($post['cc_cvv2']) != 3 && strlen($post['cc_cvv2']) != 4){
				$json['error'] = $this->language->get('error_incorrect_cvv');
			}
		}

		if (isset($json['error'])){
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($json));
			return null;
		}

		$this->loadModel('checkout/order');
		$this->loadModel('extension/default_stripe');
		$this->loadLanguage('default_stripe/default_stripe');
		$order_id = $this->session->data['order_id'];

		$order_info = $this->model_checkout_order->getOrder($order_id);
		// currency code
		$currency = $this->currency->getCode();
		// order amount without decimal delimiter
		$amount = round($this->currency->convert($this->cart->getFinalTotal(), $this->config->get('config_currency'), $currency), 2) * 100;
		$cardnumber = preg_replace('/[^0-9]/', '', $post['cc_number']);
		$cvv2 = preg_replace('/[^0-9]/', '', $post['cc_cvv2']);
		// Card owner name
		$cardname = html_entity_decode($post['cc_owner'], ENT_QUOTES, 'UTF-8');
		$cardtype = $post['cc_type'];
		// card expire date mmyy
		$cardissue = $post['cc_issue'];

		ADebug::checkpoint('Stripe Payment: Order ID ' . $order_id);

		$pd = array (
				'amount'          => $amount,
				'currency'        => $currency,
				'order_id'        => $order_id,
				'cc_number'       => $cardnumber,
				'cc_expire_month' => $post['cc_expire_date_month'],
				'cc_expire_year'  => $post['cc_expire_date_year'],
				'cc_owner'        => $cardname,
				'cc_cvv2'         => $cvv2,
				'cc_issue'        => $cardissue,
		);

		$p_result = $this->model_extension_default_stripe->processPayment($pd);

		ADebug::variable('Processing payment result: ', $p_result);
		if ($p_result['error']){
			// transaction failed
			$json['error'] = (string)$p_result['error'];
			if ($p_result['code']){
				$json['error'] .= ' (' . $p_result['code'] . ')';
			}
		} else if ($p_result['paid']){
			$json['success'] = $this->html->getSecureURL('checkout/success');
		} else{
			//Unexpected result
			$json['error'] = $this->language->get('error_system');
		}

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}

	public function api() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->data['text_note'] = $this->language->get('text_note');
		$data['order_id'] = $this->session->data['order_id'];
		//list of required fields for payment 
		$data['required_fields'] = array(
			'name' => 'cc_owner',
			'credit_card_number' => 'cc_number', 
			'credit_card_cvv2' => 'cc_cvv2',
			'credit_card_expiration_month' => 'cc_expire_date_month',
			'credit_card_expiration_year' => 'cc_expire_date_year'
		);
				
		$data['process_rt'] = 'default_stripe/api_confirm';

		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($data));
	}

	public function api_confirm() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$payment_controller = $this->dispatch( 'responses/extension/default_stripe/send' );

		$result = $payment_controller->dispatchGetOutput();

		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->response->setOutput($result);
	}
}
