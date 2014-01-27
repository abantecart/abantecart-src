<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesCatalogProductSpecial extends AController {
	private $error = array(); 
	public $data = array(); 
     
  	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('catalog/product');

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			if ( !$product_info ) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
    	}  

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

    	$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/product'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));


		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_groups'] = array();
		foreach( $results as $r ) {
            $this->data['customer_groups'][ $r['customer_group_id'] ] = $r['name'];
        }
		  
		$this->data['product_specials'] = $this->model_catalog_product->getProductSpecials($this->request->get['product_id']);

		$this->data['delete'] = $this->html->getSecureURL('catalog/product_special/delete', '&product_id=' . $this->request->get['product_id'].'&product_special_id=%ID%' );
		$this->data['update'] = $this->html->getSecureURL('catalog/product_special/update', '&product_id=' . $this->request->get['product_id'].'&product_special_id=%ID%' );
		$this->data['insert'] = $this->html->getSecureURL('catalog/product_special/insert', '&product_id=' . $this->request->get['product_id'] );

		$this->data['link_general'] = $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_links'] = $this->html->getSecureURL('catalog/product_links', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_options'] = $this->html->getSecureURL('catalog/product_options', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_discount'] = $this->html->getSecureURL('catalog/product_discount', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_special'] = $this->html->getSecureURL('catalog/product_special', '&product_id=' . $this->request->get['product_id'] );
		$this->data['link_images'] = $this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get['product_id'] );

		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		$this->data['form_title'] = $this->language->get('text_edit') . $this->data['product_description'][$this->session->data['content_language_id']]['name'];
		$this->data['button_remove'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_remove'),
			'style' => 'button2',
		));
		$this->data['button_edit'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_edit'),
			'style' => 'button2',
		));
		$this->data['button_add_special'] = $this->html->buildButton(array(
			'text' => $this->language->get('button_add_special'),
			'style' => 'button1',
		));
		$this->view->assign('help_url', $this->gen_help_url('product_special') );
		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/catalog/product_special_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            $product_special_id = $this->model_catalog_product->addProductSpecial($this->request->get['product_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('catalog/product_special/update', '&product_id=' . $this->request->get['product_id'].'&product_special_id=' . $product_special_id ) );
    	}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_catalog_product->updateProductSpecial($this->request->get['product_special_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('catalog/product_special/update', '&product_id=' . $this->request->get['product_id'].'&product_special_id=' . $this->request->get['product_special_id'] ) );
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	public function delete() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->loadModel('catalog/product');
    	$this->model_catalog_product->deleteProductSpecial($this->request->get['product_special_id']);
		$this->session->data['success'] = $this->language->get('text_success');
		$this->redirect($this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] ));

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	private function _getForm() {

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

    	$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] );

		$this->data['active'] = 'promotions';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		$this->data['heading_title'] = $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product') . ' - '. $this->data['product_description'][$this->session->data['content_language_id']]['name'];

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
			'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/product'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product') . ' - '. $this->data['product_description'][$this->session->data['content_language_id']]['name'],
			'separator' => ' :: '
		 ));
		 $this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('tab_promotions'),
			'separator' => ' :: '
		 ));

		if (isset($this->request->get['product_special_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$special_info = $this->model_catalog_product->getProductSpecial($this->request->get['product_special_id']);
			if ( $special_info['date_start'] == '0000-00-00' ) $special_info['date_start'] = '';
			if ( $special_info['date_end'] == '0000-00-00' ) $special_info['date_end'] = '';
    	}

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_groups'] = array();
		foreach( $results as $r ) {
            $this->data['customer_groups'][ $r['customer_group_id'] ] = $r['name'];
        }

        $fields = array('customer_group_id', 'priority', 'price', 'date_start', 'date_end',);
		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
				if(in_array($f,array('date_start','date_end'))){
					$this->data [$f] = dateDisplay2ISO($this->data [$f],$this->language->get('date_format_short'));
				}
			} elseif (isset($special_info)) {
				$this->data[$f] = $special_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (!isset($this->request->get['product_special_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/product_special/insert', '&product_id=' . $this->request->get['product_id'] );
			$this->data['form_title'] = $this->language->get('text_insert') .'&nbsp;'. $this->language->get('entry_special');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/product_special/update', '&product_id=' . $this->request->get['product_id'].'&product_special_id=' . $this->request->get['product_special_id'] );
			$this->data['form_title'] = $this->language->get('text_edit') .'&nbsp;'. $this->language->get('entry_special');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/product/update_special_field','&id='.$this->request->get['product_special_id']);
			$form = new AForm('HS');
		}

		$this->document->addBreadcrumb( array (
			'href'      => $this->data['action'],
			'text'      => $this->data['form_title'],
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
		    'attr' => 'confirm-exit="true"',
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

		));
		$this->data['js_date_format'] = format4Datepicker($this->language->get('date_format_short'));
        $this->data['form']['fields']['date_start'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'date_start',
			'value' => dateISO2Display($this->data['date_start'],$this->language->get('date_format_short')),
            'style' => 'date'
		));
		$this->data['form']['fields']['date_end'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'date_end',
			'value' => dateISO2Display($this->data['date_end'],$this->language->get('date_format_short')),
            'style' => 'date'
		));

		$this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$this->view->assign('help_url', $this->gen_help_url('product_special_edit') );
        $this->view->batchAssign( $this->data );
        $this->processTemplate('pages/catalog/product_special_form.tpl' );
  	} 
	
  	private function _validateForm() {
    	if (!$this->user->canModify('catalog/product')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}

		if ( $this->request->post['date_start'] != '0000-00-00' && $this->request->post['date_end'] != '0000-00-00'
		     &&	dateFromFormat($this->request->post['date_start'],$this->language->get('date_format_short')) > dateFromFormat($this->request->post['date_end'],$this->language->get('date_format_short'))
		) {
			$this->error['date_end'] = $this->language->get('error_date');
		}

    	if (!$this->error) {
			return TRUE;
    	} else {
      		return FALSE;
    	}
  	}
	
}