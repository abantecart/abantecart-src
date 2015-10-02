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
class ControllerPagesCheckoutGuestStep1 extends AController {
	private $error = array();
	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//is this an embed mode	
		$cart_rt = 'checkout/cart';		
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		}

		if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->html->getSecureURL($cart_rt));
		}

		//validate if order min/max are met
		if (!$this->cart->hasMinRequirement() || !$this->cart->hasMaxRequirement()) {
			$this->redirect($this->html->getSecureURL($cart_rt));
		}

		if ($this->customer->isLogged()) {
			$this->redirect($this->html->getSecureURL('checkout/shipping'));
		}

		if (!$this->config->get('config_guest_checkout') || $this->cart->hasDownload()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('checkout/shipping');

			$this->redirect($this->html->getSecureURL('account/login'));
		}

		if ($this->request->is_POST() && $this->_validate()) {
			$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
			$this->session->data['guest']['email'] = $this->request->post['email'];
			$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
			$this->session->data['guest']['fax'] = $this->request->post['fax'];
			$this->session->data['guest']['company'] = $this->request->post['company'];
			$this->session->data['guest']['address_1'] = $this->request->post['address_1'];
			$this->session->data['guest']['address_2'] = $this->request->post['address_2'];
			$this->session->data['guest']['postcode'] = $this->request->post['postcode'];
			$this->session->data['guest']['city'] = $this->request->post['city'];
			$this->session->data['guest']['country_id'] = $this->request->post['country_id'];
			$this->session->data['guest']['zone_id'] = $this->request->post['zone_id'];

			//if ($this->cart->hasShipping()) {
			$this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
			//}

			$this->loadModel('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

			if ($country_info) {
				$this->session->data['guest']['country'] = $country_info['name'];
				$this->session->data['guest']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['country'] = '';
				$this->session->data['guest']['iso_code_2'] = '';
				$this->session->data['guest']['iso_code_3'] = '';
				$this->session->data['guest']['address_format'] = '';
			}

			$this->loadModel('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$this->session->data['guest']['zone'] = $zone_info['name'];
				$this->session->data['guest']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['guest']['zone'] = '';
				$this->session->data['guest']['zone_code'] = '';
			}

			if (isset($this->request->post['shipping_indicator'])) {
				$this->session->data['guest']['shipping']['firstname'] = $this->request->post['shipping_firstname'];
				$this->session->data['guest']['shipping']['lastname'] = $this->request->post['shipping_lastname'];
				$this->session->data['guest']['shipping']['company'] = $this->request->post['shipping_company'];
				$this->session->data['guest']['shipping']['address_1'] = $this->request->post['shipping_address_1'];
				$this->session->data['guest']['shipping']['address_2'] = $this->request->post['shipping_address_2'];
				$this->session->data['guest']['shipping']['postcode'] = $this->request->post['shipping_postcode'];
				$this->session->data['guest']['shipping']['city'] = $this->request->post['shipping_city'];
				$this->session->data['guest']['shipping']['country_id'] = $this->request->post['shipping_country_id'];
				$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['shipping_zone_id'];

				$shipping_country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);

				if ($shipping_country_info) {
					$this->session->data['guest']['shipping']['country'] = $shipping_country_info['name'];
					$this->session->data['guest']['shipping']['iso_code_2'] = $shipping_country_info['iso_code_2'];
					$this->session->data['guest']['shipping']['iso_code_3'] = $shipping_country_info['iso_code_3'];
					$this->session->data['guest']['shipping']['address_format'] = $shipping_country_info['address_format'];
				} else {
					$this->session->data['guest']['shipping']['country'] = '';
					$this->session->data['guest']['shipping']['iso_code_2'] = '';
					$this->session->data['guest']['shipping']['iso_code_3'] = '';
					$this->session->data['guest']['shipping']['address_format'] = '';
				}

				$shipping_zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);

				if ($zone_info) {
					$this->session->data['guest']['shipping']['zone'] = $shipping_zone_info['name'];
					$this->session->data['guest']['shipping']['zone_code'] = $shipping_zone_info['code'];
				} else {
					$this->session->data['guest']['shipping']['zone'] = '';
					$this->session->data['guest']['shipping']['zone_code'] = '';
				}

			} else {
				unset($this->session->data['guest']['shipping']);
			}

			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);

			$this->redirect($this->html->getSecureURL('checkout/guest_step_2'));
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL($cart_rt),
				'text' => $this->language->get('text_cart'),
				'separator' => $this->language->get('text_separator')
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('checkout/guest_step_1'),
				'text' => $this->language->get('text_guest_step_1'),
				'separator' => $this->language->get('text_separator')
		));

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('error_firstname', $this->error['firstname']);
		$this->view->assign('error_lastname', $this->error['lastname']);
		$this->view->assign('error_email', $this->error['email']);
		$this->view->assign('error_telephone', $this->error['telephone']);
		$this->view->assign('error_address_1', $this->error['address_1']);
		$this->view->assign('error_city', $this->error['city']);
		$this->view->assign('error_postcode', $this->error['postcode']);
		$this->view->assign('error_country', $this->error['country']);
		$this->view->assign('error_zone', $this->error['zone']);
		$this->view->assign('error_shipping_firstname', $this->error['shipping_firstname']);
		$this->view->assign('error_shipping_lastname', $this->error['shipping_lastname']);
		$this->view->assign('error_shipping_address_1', $this->error['shipping_address_1']);
		$this->view->assign('error_shipping_city', $this->error['shipping_city']);
		$this->view->assign('error_shipping_postcode', $this->error['shipping_postcode']);
		$this->view->assign('error_shipping_country', $this->error['shipping_country']);
		$this->view->assign('error_shipping_zone', $this->error['shipping_zone']);

		//$this->view->assign('action', $this->html->getSecureURL('checkout/guest_step_1'));

		$form = new AForm();
		$form->setForm(array('form_name' => 'guestFrm'));
		$this->data['form']['form_open'] = $form->getFieldHtml(
				array(
						'type' => 'form',
						'name' => 'guestFrm',
						'action' => $this->html->getSecureURL('checkout/guest_step_1')));

		if (isset($this->request->post['firstname'])) {
			$firstname = $this->request->post['firstname'];
		} elseif (isset($this->session->data['guest']['firstname'])) {
			$firstname = $this->session->data['guest']['firstname'];
		} else {
			$firstname = '';
		}

		$this->data['form']['firstname'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'firstname',
				'value' => $firstname,
				'required' => true));


		if (isset($this->request->post['lastname'])) {
			$lastname = $this->request->post['lastname'];
		} elseif (isset($this->session->data['guest']['lastname'])) {
			$lastname = $this->session->data['guest']['lastname'];
		} else {
			$lastname = '';
		}
		$this->data['form']['lastname'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'lastname',
				'value' => $lastname,
				'required' => true));
		if (isset($this->request->post['email'])) {
			$email = $this->request->post['email'];
		} elseif (isset($this->session->data['guest']['email'])) {
			$email = $this->session->data['guest']['email'];
		} else {
			$email = '';
		}

		$this->data['form']['email'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'email',
				'value' => $email,
				'required' => true));
		if (isset($this->request->post['telephone'])) {
			$telephone = $this->request->post['telephone'];
		} elseif (isset($this->session->data['guest']['telephone'])) {
			$telephone = $this->session->data['guest']['telephone'];
		} else {
			$telephone = '';
		}
		$this->data['form']['telephone'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'telephone',
				'value' => $telephone
				));
		if (isset($this->request->post['fax'])) {
			$fax = $this->request->post['fax'];
		} elseif (isset($this->session->data['guest']['fax'])) {
			$fax = $this->session->data['guest']['fax'];
		} else {
			$fax = '';
		}
		$this->data['form']['fax'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'fax',
				'value' => $fax,
				'required' => false));
		if (isset($this->request->post['company'])) {
			$company = $this->request->post['company'];
		} elseif (isset($this->session->data['guest']['company'])) {
			$company = $this->session->data['guest']['company'];
		} else {
			$company = '';
		}

		$this->data['form']['company'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'company',
				'value' => $company,
				'required' => false));
		if (isset($this->request->post['address_1'])) {
			$address_1 = $this->request->post['address_1'];
		} elseif (isset($this->session->data['guest']['address_1'])) {
			$address_1 = $this->session->data['guest']['address_1'];
		} else {
			$address_1 = '';
		}
		$this->data['form']['address_1'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'address_1',
				'value' => $address_1,
				'required' => true));


		if (isset($this->request->post['address_2'])) {
			$address_2 = $this->request->post['address_2'];
		} elseif (isset($this->session->data['guest']['address_2'])) {
			$address_2 = $this->session->data['guest']['address_2'];
		} else {
			$address_2 = '';
		}
		$this->data['form']['address_2'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'address_2',
				'value' => $address_2,
				'required' => false));

		if (isset($this->request->post['city'])) {
			$city = $this->request->post['city'];
		} elseif (isset($this->session->data['guest']['city'])) {
			$city = $this->session->data['guest']['city'];
		} else {
			$city = '';
		}


		$this->data['form']['city'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'city',
				'value' => $city,
				'required' => true));

		if (isset($this->request->post['postcode'])) {
			$postcode = $this->request->post['postcode'];
		} elseif (isset($this->session->data['guest']['postcode'])) {
			$postcode = $this->session->data['guest']['postcode'];
		} else {
			$postcode = '';
		}
		$this->data['form']['postcode'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'postcode',
				'value' => $postcode,
				'required' => true));


		if (isset($this->request->post['country_id'])) {
			$country_id = $this->request->post['country_id'];
		} elseif (isset($this->session->data['guest']['country_id'])) {
			$country_id = $this->session->data['guest']['country_id'];
		} else {
			$country_id = $this->config->get('config_country_id');
		}

		$this->loadModel('localisation/country');
		$countries = $this->model_localisation_country->getCountries();
		$options = array("FALSE" => $this->language->get('text_select'));
		foreach ($countries as $item) {
			$options[$item['country_id']] = $item['name'];
		}
		$this->data['form']['country_id'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'country_id',
				'options' => $options,
				'value' => $country_id,
				'required' => true));

		if (isset($this->request->post['zone_id'])) {
			$zone_id = $this->request->post['zone_id'];
		} elseif (isset($this->session->data['guest']['zone_id'])) {
			$zone_id = $this->session->data['guest']['zone_id'];
		} else {
			$zone_id = 'FALSE';
		}
		$this->view->assign('zone_id', $zone_id);

		$this->data['form']['zone_id'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'zone_id',
				'required' => true));

		$this->data['form']['shipping_indicator'] = $form->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => 'shipping_indicator',
				'value' => 1,
				'checked' => (isset($this->request->post['shipping_indicator'])
								? (bool)$this->request->post['shipping_indicator']
								: false),
				'label_text' => $this->language->get('text_indicator'),
		));

		if (isset($this->request->post['shipping_firstname'])) {
			$shipping_firstname = $this->request->post['shipping_firstname'];
		} elseif (isset($this->session->data['guest']['shipping']['firstname'])) {
			$shipping_firstname = $this->session->data['guest']['shipping']['firstname'];
		} else {
			$shipping_firstname = '';
		}
		$this->data['form']['shipping_firstname'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_firstname',
				'value' => $shipping_firstname,
				'required' => true));
		if (isset($this->request->post['shipping_lastname'])) {
			$shipping_lastname = $this->request->post['shipping_lastname'];
		} elseif (isset($this->session->data['guest']['shipping']['lastname'])) {
			$shipping_lastname = $this->session->data['guest']['shipping']['lastname'];
		} else {
			$shipping_lastname = '';
		}
		$this->data['form']['shipping_lastname'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_lastname',
				'value' => $shipping_lastname,
				'required' => true));
		if (isset($this->request->post['shipping_company'])) {
			$shipping_company = $this->request->post['shipping_company'];
		} elseif (isset($this->session->data['guest']['shipping']['company'])) {
			$shipping_company = $this->session->data['guest']['shipping']['company'];
		} else {
			$shipping_company = '';
		}
		$this->data['form']['shipping_company'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_company',
				'value' => $shipping_company,
				'required' => false));
		if (isset($this->request->post['shipping_address_1'])) {
			$shipping_address_1 = $this->request->post['shipping_address_1'];
		} elseif (isset($this->session->data['guest']['shipping']['address_1'])) {
			$shipping_address_1 = $this->session->data['guest']['shipping']['address_1'];
		} else {
			$shipping_address_1 = '';
		}
		$this->data['form']['shipping_address_1'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_address_1',
				'value' => $shipping_address_1,
				'required' => true));
		if (isset($this->request->post['shipping_address_2'])) {
			$shipping_address_2 = $this->request->post['shipping_address_2'];
		} elseif (isset($this->session->data['guest']['shipping']['address_2'])) {
			$shipping_address_2 = $this->session->data['guest']['shipping']['address_2'];
		} else {
			$shipping_address_2 = '';
		}
		$this->data['form']['shipping_address_2'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_address_2',
				'value' => $shipping_address_2,
				'required' => false));
		if (isset($this->request->post['shipping_city'])) {
			$shipping_city = $this->request->post['shipping_city'];
		} elseif (isset($this->session->data['guest']['shipping']['city'])) {
			$shipping_city = $this->session->data['guest']['shipping']['city'];
		} else {
			$shipping_city = '';
		}
		$this->data['form']['shipping_city'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_city',
				'value' => $shipping_city,
				'required' => true));
		if (isset($this->request->post['shipping_postcode'])) {
			$shipping_postcode = $this->request->post['shipping_postcode'];
		} elseif (isset($this->session->data['guest']['shipping']['postcode'])) {
			$shipping_postcode = $this->session->data['guest']['shipping']['postcode'];
		} else {
			$shipping_postcode = '';
		}
		$this->data['form']['shipping_postcode'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'shipping_postcode',
				'value' => $shipping_postcode,
				'required' => true));

		$options = array("FALSE" => $this->language->get('text_select'));
		foreach ($countries as $item) {
			$options[$item['country_id']] = $item['name'];
		}
		if (isset($this->request->post['shipping_country_id'])) {
			$shipping_country_id = $this->request->post['shipping_country_id'];
		} elseif (isset($this->session->data['guest']['shipping']['country_id'])) {
			$shipping_country_id = $this->session->data['guest']['shipping']['country_id'];
		} else {
			$shipping_country_id = $this->config->get('config_country_id');
		}
		$this->data['form']['shipping_country_id'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'shipping_country_id',
				'options' => $options,
				'value' => $shipping_country_id,
				'required' => true));


		if (isset($this->request->post['shipping_zone_id'])) {
			$shipping_zone_id = $this->request->post['shipping_zone_id'];
		} elseif (isset($this->session->data['guest']['shipping']['zone_id'])) {
			$shipping_zone_id = $this->session->data['guest']['shipping']['zone_id'];
		} else {
			$shipping_zone_id = 'FALSE';
		}
		$this->view->assign('shipping_zone_id', $shipping_zone_id);
		$this->data['form']['shipping_zone_id'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'shipping_zone_id',
				'required' => true));


		if (isset($this->request->post['shipping_indicator'])) {
			$this->view->assign('shipping_addr', TRUE);
		} elseif (isset($this->session->data['guest']['shipping'])) {
			$this->view->assign('shipping_addr', TRUE);
		} else {
			$this->view->assign('shipping_addr', FALSE);
		}

		$this->view->assign('shipping', $this->cart->hasShipping());
		$this->loadModel('localisation/country');
		$this->view->assign('countries', $this->model_localisation_country->getCountries());

		$this->view->assign('back', $this->html->getURL($cart_rt));

		$this->data['form']['back'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'back',
				'text' => $this->language->get('button_back'),
				'style' => 'button'
		));

		$this->data['form']['continue'] = $form->getFieldHtml(array(
				'type' => 'submit',
				'name' => $this->language->get('button_continue')));

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/checkout/guest_step_1.tpl');

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validate() {
		if ((mb_strlen($this->request->post['firstname']) < 3) || (mb_strlen($this->request->post['firstname']) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((mb_strlen($this->request->post['lastname']) < 3) || (mb_strlen($this->request->post['lastname']) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (!preg_match(EMAIL_REGEX_PATTERN, $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ( mb_strlen($this->request->post['telephone']) > 32 ) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if ((mb_strlen($this->request->post['address_1']) < 3) || (mb_strlen($this->request->post['address_1']) > 128)) {
			$this->error['address_1'] = $this->language->get('error_address_1');
		}

		if ((mb_strlen($this->request->post['city']) < 3) || (mb_strlen($this->request->post['city']) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}
		if ((mb_strlen($this->request->post['postcode']) < 3) || (mb_strlen($this->request->post['postcode']) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}

		if ($this->request->post['country_id'] == 'FALSE') {
			$this->error['country'] = $this->language->get('error_country');
		}

		if ($this->request->post['zone_id'] == 'FALSE') {
			$this->error['zone'] = $this->language->get('error_zone');
		}

		if (isset($this->request->post['shipping_indicator'])) {

			if ((mb_strlen($this->request->post['shipping_firstname']) < 3) || (mb_strlen($this->request->post['shipping_firstname']) > 32)) {
				$this->error['shipping_firstname'] = $this->language->get('error_firstname');
			}

			if ((mb_strlen($this->request->post['shipping_lastname']) < 3) || (mb_strlen($this->request->post['shipping_lastname']) > 32)) {
				$this->error['shipping_lastname'] = $this->language->get('error_lastname');
			}

			if ((mb_strlen($this->request->post['shipping_address_1']) < 3) || (mb_strlen($this->request->post['shipping_address_1']) > 128)) {
				$this->error['shipping_address_1'] = $this->language->get('error_address_1');
			}

			if ((mb_strlen($this->request->post['shipping_city']) < 3) || (mb_strlen($this->request->post['shipping_city']) > 128)) {
				$this->error['shipping_city'] = $this->language->get('error_city');
			}
			if ((mb_strlen($this->request->post['shipping_postcode']) < 3) || (mb_strlen($this->request->post['shipping_postcode']) > 10)) {
				$this->error['shipping_postcode'] = $this->language->get('error_postcode');
			}

			if ($this->request->post['shipping_country_id'] == 'FALSE') {
				$this->error['shipping_country'] = $this->language->get('error_country');
			}

			if ($this->request->post['shipping_zone_id'] == 'FALSE') {
				$this->error['shipping_zone'] = $this->language->get('error_zone');
			}

		}

		if (!$this->error) {
			return TRUE;
		} else {
			$this->error['warning'] = $this->language->get('gen_data_entry_error');
			return FALSE;
		}
	}

}
