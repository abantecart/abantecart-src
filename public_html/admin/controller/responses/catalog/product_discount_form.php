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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}


class ControllerResponsesCatalogProductDiscountForm extends AController {
	public $data = array();


	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');
		$this->_getForm();
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');

		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}



	private function _getForm() {

		$view = new AView($this->registry, 0);

		$view->batchAssign( $this->language->getASet('catalog/product'));

		$view->assign('error_warning', $this->error['warning']);
		$view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id']);

		$this->data['active'] = 'promotions';

		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		$this->data['heading_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product') . ' - ' . $this->data['product_description'][$this->session->data['content_language_id']]['name'];


		if (isset($this->request->get['product_discount_id']) && $this->request->is_GET()) {
			$discount_info = $this->model_catalog_product->getProductDiscount($this->request->get['product_discount_id']);
			if ($discount_info['date_start'] == '0000-00-00') $discount_info['date_start'] = '';
			if ($discount_info['date_end'] == '0000-00-00') $discount_info['date_end'] = '';
		}

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_groups'] = array();
		foreach ($results as $r) {
			$this->data['customer_groups'][$r['customer_group_id']] = $r['name'];
		}

		$fields = array('customer_group_id', 'quantity', 'priority', 'price', 'date_start', 'date_end',);
		foreach ($fields as $f) {
			if (isset ($this->request->post [$f])) {
				$this->data [$f] = $this->request->post [$f];
				if (in_array($f, array('date_start', 'date_end'))) {
					$this->data [$f] = dateDisplay2ISO($this->data [$f], $this->language->get('date_format_short'));
				}
			} elseif (isset($discount_info)) {
				$this->data[$f] = $discount_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (!isset($this->request->get['product_discount_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id']);
			$this->data['form_title'] = $this->language->get('text_insert') . '&nbsp;' . $this->language->get('entry_discount');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] . '&product_discount_id=' . $this->request->get['product_discount_id']);
			$this->data['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('entry_discount');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/product/update_discount_field', '&id=' . $this->request->get['product_discount_id']);
			$form = new AForm('HS');

		}

		$this->document->addBreadcrumb(array(
				'href' => $this->data['action'],
				'text' => $this->data['form_title'],
				'separator' => ' :: '
		));

		$form->setForm(array(
				'form_name' => 'productFrm',
				'update' => $this->data['update'],
		));

		$this->data['form']['id'] = 'productFrm';
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'productFrm',
				'action' => $this->data['action'],
				'attr' => 'data-confirm-exit="true"  class="aform form-horizontal"',
		)).
				$form->getFieldHtml(array(
								'type' => 'hidden',
								'name' => 'promotion_type',
								'value' => 'discount'
						));

		$this->data['form']['submit'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_save'),
				'style' => 'button1',
		));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel'),
				'style' => 'button2',
		));


		$this->data['form']['fields']['customer_group'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'customer_group_id',
				'value' => $this->data['customer_group_id'],
				'options' => $this->data['customer_groups'],
		));

		$this->data['form']['fields']['quantity'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'quantity',
				'value' => $this->data['quantity'],
				'style' => 'small-field',
		));
		$this->data['form']['fields']['priority'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'priority',
				'value' => $this->data['priority'],
				'style' => 'small-field',
		));
		$this->data['form']['fields']['price'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'price',
				'value' => moneyDisplayFormat($this->data['price']),
				'style' => 'tiny-field'
		));

		$this->data['js_date_format'] = format4Datepicker($this->language->get('date_format_short'));
		$this->data['form']['fields']['date_start'] = $form->getFieldHtml(array(
				'type' => 'date',
				'name' => 'date_start',
				'value' => dateISO2Display($this->data['date_start'], $this->language->get('date_format_short')),
				'default' => '',
				'dateformat' => format4Datepicker($this->language->get('date_format_short')),
				'highlight' => 'future',
				'style' => 'small-field',
		));
		$this->data['form']['fields']['date_end'] = $form->getFieldHtml(array(
				'type' => 'date',
				'name' => 'date_end',
				'value' => dateISO2Display($this->data['date_end'], $this->language->get('date_format_short')),
				'default' => '',
				'dateformat' => format4Datepicker($this->language->get('date_format_short')),
				'highlight' => 'future',
				'style' => 'small-field',
		));

		$view->assign('help_url', $this->gen_help_url('product_discount_edit'));
		$view->batchAssign($this->data);
		$this->data[ 'response' ] = $view->fetch('responses/catalog/product_promotion_form.tpl');
		$this->response->setOutput($this->data[ 'response' ]);
	}


}