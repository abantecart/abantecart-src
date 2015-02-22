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

class ControllerPagesAccountAddress extends AController {
	private $error = array();
	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/address');
			$this->redirect($this->html->getSecureURL('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		unset($this->session->data['success']);
	}

	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/address');

			$this->redirect($this->html->getSecureURL('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->is_POST() && $this->validateForm()) {
			$this->model_account_address->addAddress($this->request->post);

			$this->session->data['success'] = $this->language->get('text_insert');

			$this->redirect($this->html->getSecureURL('account/address'));
		}

		$this->getForm();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/address');

			$this->redirect($this->html->getSecureURL('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->is_POST() && $this->validateForm()) {
			$this->model_account_address->editAddress($this->request->get['address_id'], $this->request->post);

			if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);

				$this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
			}

			if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);
			}

			$this->session->data['success'] = $this->language->get('text_update');

			$this->redirect($this->html->getSecureURL('account/address'));
		}

		$this->getForm();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function delete() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/address');

			$this->redirect($this->html->getSecureURL('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['address_id']) && $this->validateDelete()) {
			$this->model_account_address->deleteAddress($this->request->get['address_id']);

			if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
				unset($this->session->data['shipping_address_id']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
			}

			if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
				unset($this->session->data['payment_address_id']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);
			}

			$this->session->data['success'] = $this->language->get('text_delete');

			$this->redirect($this->html->getSecureURL('account/address'));
		}

		$this->getList();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function getList() {
		$this->document->addBreadcrumb(array(
			'href' => $this->html->getURL('index/home'),
			'text' => $this->language->get('text_home'),
			'separator' => FALSE
		));

		$this->document->addBreadcrumb(array(
			'href' => $this->html->getURL('account/account'),
			'text' => $this->language->get('text_account'),
			'separator' => $this->language->get('text_separator')
		));

		$this->document->addBreadcrumb(array(
			'href' => $this->html->getURL('account/address'),
			'text' => $this->language->get('heading_title'),
			'separator' => $this->language->get('text_separator')
		));

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);

		$addresses = array();

		$results = $this->model_account_address->getAddresses();

		foreach ($results as $result) {
			$formated_address = $this->customer->getFormatedAdress($result, $result['address_format']);

			$edit = HtmlElementFactory::create(array(
				'type' => 'button',
				'text' => $this->language->get('button_edit'),
				'style' => 'button btn-primary',
				'icon' => 'fa-edit fa',
				'attr' => 'onclick="location = \'' . $this->html->getSecureURL('account/address/update', '&address_id=' . $result['address_id']) . '\'" '));
			$delete = HtmlElementFactory::create(array(
				'type' => 'button',
				'text' => $this->language->get('button_delete'),
				'style' => '',
				'icon' => 'fa fa-remove',
				'attr' => 'onclick="location = \'' . $this->html->getSecureURL('account/address/delete', '&address_id=' . $result['address_id']) . '\'" '));

			$addresses[] = array(
				'address_id' => $result['address_id'],
				'address' => $formated_address,
				'button_edit' => $edit,
				'button_delete' => $delete,
				'default' => $this->customer->getAddressId() == $result['address_id'] ? true : false,
			);
		}

		$this->view->assign('addresses', $addresses);

		$this->view->assign('insert', $this->html->getSecureURL('account/address/insert'));

		$insert = HtmlElementFactory::create(array(
			'type' => 'button',
			'name' => 'insert',
			'text' => $this->language->get('button_new_address'),
			'icon' => 'fa fa-plus',
			'style' => 'button'));
		$this->view->assign('button_insert', $insert);

		$back = HtmlElementFactory::create(array(
			'type' => 'button',
			'name' => 'back',
			'text' => $this->language->get('button_back'),
			'icon' => 'fa fa-arrow-left',
			'style' => 'button'));
		$this->view->assign('button_back', $back);
		$this->view->assign('back', $this->html->getSecureURL('account/account'));

		$this->processTemplate('pages/account/addresses.tpl');
	}

	private function getForm() {
		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array(
			'href' => $this->html->getURL('index/home'),
			'text' => $this->language->get('text_home'),
			'separator' => FALSE
		));

		$this->document->addBreadcrumb(array(
			'href' => $this->html->getURL('account/account'),
			'text' => $this->language->get('text_account'),
			'separator' => $this->language->get('text_separator')
		));

		$this->document->addBreadcrumb(array(
			'href' => $this->html->getURL('account/address'),
			'text' => $this->language->get('heading_title'),
			'separator' => $this->language->get('text_separator')
		));

		if (!isset($this->request->get['address_id'])) {
			$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL('account/address/insert'),
				'text' => $this->language->get('text_edit_address'),
				'separator' => $this->language->get('text_separator')
			));
		} else {
			$this->document->addBreadcrumb(array(
				'href' => $this->html->getURL('account/address/update', 'address_id=' . $this->request->get['address_id']),
				'text' => $this->language->get('text_edit_address'),
				'separator' => $this->language->get('text_separator')
			));
		}

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('error_firstname', $this->error['firstname']);
		$this->view->assign('error_lastname', $this->error['lastname']);
		$this->view->assign('error_address_1', $this->error['address_1']);
		$this->view->assign('error_city', $this->error['city']);
		$this->data['error_postcode'] = $this->error['postcode'];
		$this->view->assign('error_country', $this->error['country']);
		$this->view->assign('error_zone', $this->error['zone']);


		if (isset($this->request->get['address_id']) && $this->request->is_GET()) {
			$address_info = $this->model_account_address->getAddress($this->request->get['address_id']);
		}


		$this->data['back'] = $this->html->getSecureURL('account/address');

		$form = new AForm();
		$form->setForm(array('form_name' => 'AddressFrm'));

		if (!isset($this->request->get['address_id'])) {
			$action = $this->html->getSecureURL('account/address/insert');
		} else {
			$action = $this->html->getSecureURL('account/address/update', '&address_id=' . $this->request->get['address_id']);
		}
		$this->data['form']['form_open'] = $form->getFieldHtml(
			array(
				'type' => 'form',
				'name' => 'AddressFrm',
				'action' => $action));


		if (isset($this->request->post['firstname'])) {
			$firstname = $this->request->post['firstname'];
		} elseif (isset($address_info)) {
			$firstname = $address_info['firstname'];
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
		} elseif (isset($address_info)) {
			$lastname = $address_info['lastname'];
		} else {
			$lastname = '';
		}
		$this->data['form']['lastname'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'lastname',
			'value' => $lastname,
			'required' => true));

		if (isset($this->request->post['company'])) {
			$company = $this->request->post['company'];
		} elseif (isset($address_info)) {
			$company = $address_info['company'];
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
		} elseif (isset($address_info)) {
			$address_1 = $address_info['address_1'];
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
		} elseif (isset($address_info)) {
			$address_2 = $address_info['address_2'];
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
		} elseif (isset($address_info)) {
			$city = $address_info['city'];
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
		} elseif (isset($address_info)) {
			$postcode = $address_info['postcode'];
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
		} elseif (isset($address_info)) {
			$country_id = $address_info['country_id'];
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
			$this->data['zone_id'] = $this->request->post['zone_id'];
		} elseif (isset($address_info)) {
			$this->data['zone_id'] = $address_info['zone_id'];
		} else {
			$this->data['zone_id'] = 'FALSE';
		}

		$this->data['form']['zone_id'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'zone_id',
			'value' => $this->data['zone_id'],
			'required' => true));
		if (isset($this->request->post['default'])) {
			$default = $this->request->post['default'];
		} elseif (isset($this->request->get['address_id'])) {
			$default = $this->customer->getAddressId() == $this->request->get['address_id'];
		} else {
			$default = FALSE;
		}
		$this->data['form']['default'] = $form->getFieldHtml(array(
			'type' => 'radio',
			'name' => 'default',
			'value' => $default,
			'options' => array(
				'1' => $this->language->get('text_yes'),
				'0' => $this->language->get('text_no'),
			)));
		$this->data['form']['back'] = $form->getFieldHtml(array(
			'type' => 'button',
			'name' => 'back',
			'text' => $this->language->get('button_back'),
			'icon' => 'fa fa-arrow-left',
			'style' => 'button'));
		$this->data['form']['submit'] = $form->getFieldHtml(array(
			'type' => 'submit',
			'icon' => 'fa fa-check',
			'name' => $this->language->get('button_continue')
		));

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/account/address.tpl');
	}

	private function validateForm() {
		$this->loadModel('account/address');
		$this->error = $this->model_account_address->validateAddressData($this->request->post);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function validateDelete() {
		if ($this->model_account_address->getTotalAddresses() == 1) {
			$this->error['warning'] = $this->language->get('error_delete');
		}

		if ($this->customer->getAddressId() == $this->request->get['address_id']) {
			$this->error['warning'] = $this->language->get('error_default');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}