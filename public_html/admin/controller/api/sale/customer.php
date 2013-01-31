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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerApiSaleCustomer extends AControllerAPI {
	public $data = array();
	private $error = array();
	private $fields = array('loginname', 'firstname', 'lastname', 'email', 'telephone', 'fax', 'newsletter', 'customer_group_id',
        'status', 'approved', 'password');
  
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('sale/customer'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}


		$grid_settings = array(
			//id of grid
            'table_id' => 'customer_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/customer'),
			'editurl' => $this->html->getSecureURL('listing_grid/customer/update'),
			'update_field' => $this->html->getSecureURL ( 'listing_grid/customer/update_field' ),
            'sortname' => 'name',
            'sortorder' => 'asc',
			'multiselect' => 'true',
            // actions
            'actions' => array(
                'approve' => array(
                    'text' => $this->language->get('button_approve'),
				    'href' => $this->html->getSecureURL('sale/customer/approve', '&customer_id=%ID%')
                ),
	            'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('sale/customer/update', '&customer_id=%ID%')
                ),
	            'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
            ),
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_name'),
			$this->language->get('column_email'),
			$this->language->get('column_group'),
			$this->language->get('column_status'),
			$this->language->get('column_approved'),
		);
		$grid_settings['colModel'] = array(
			array( 'name' => 'name', 'index' => 'name', 'width' => 160, 'align' => 'center', ),
			array( 'name' => 'email', 'index' => 'c.email', 'width' => 140, 'align' => 'center', ),
			array( 'name' => 'customer_group', 'index' => 'customer_group', 'width' => 80, 'align' => 'center', 'search' => false ),
			array( 'name' => 'status', 'index' => 'c.status', 'width' => 120, 'align' => 'center', 'search' => false ),
			array( 'name' => 'approved', 'index' => 'c.approved', 'width' => 110, 'align' => 'center', 'search' => false ),
		);

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$groups = array( '' => $this->language->get('text_select_group'), );
		foreach ( $results as $item) {
			$groups[ $item['customer_group_id'] ] = $item['name'];
		}

		$statuses = array(
			'' => $this->language->get('text_select_status'),
			1 => $this->language->get('text_enabled'),
			0 => $this->language->get('text_disabled'),
		);

		$approved = array(
			'' => $this->language->get('text_select_approved'),
			1 => $this->language->get('text_yes'),
			0 => $this->language->get('text_no'),
		);

		$form = new AForm();
	    $form->setForm(array(
		    'form_name' => 'customer_grid_search',
	    ));

	    $grid_search_form = array();
        $grid_search_form['id'] = 'customer_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'customer_grid_search',
		    'action' => '',
	    ));
        $grid_search_form['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_go'),
		    'style' => 'button1',
	    ));
		$grid_search_form['reset'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'reset',
		    'text' => $this->language->get('button_reset'),
		    'style' => 'button2',
	    ));

		$grid_search_form['fields']['customer_group'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'customer_group',
            'options' => $groups,
	    ));
        $grid_search_form['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'status',
            'options' => $statuses,
	    ));
		$grid_search_form['fields']['approved'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'approved',
            'options' => $approved,
	    ));

		$grid_settings['search_form'] = true;


        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign ( 'search_form', $grid_search_form );

		$this->document->setTitle( $this->language->get('heading_title') );
		$this->view->assign( 'insert', $this->html->getSecureURL('sale/customer/insert') );
		$this->view->assign('help_url', $this->gen_help_url('customer_listing') );

		$this->processTemplate('pages/sale/customer_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
  
  	private function _getForm() {
    	
		$this->data['token'] = $this->session->data['token'];
		$this->data['error'] = $this->error;

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('sale/customer'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$this->data['cancel'] = $this->html->getSecureURL('sale/customer');

    	if (isset($this->request->get['customer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$customer_info = $this->model_sale_customer->getCustomer($this->request->get['customer_id']);
    	}

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($customer_info)) {
				$this->data[$f] = $customer_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}
			
    	if (!isset($this->data['customer_group_id'])) {
      		$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
    	}
    	if (!isset($this->data['status'])) {
      		$this->data['status'] = 1;
    	}
		if (!isset($this->data['password']) && isset($this->request->post['password'])) {
			$this->data['password'] = $this->request->post['password'];
		} else {
			$this->data['password'] = '';
		}		

		$this->loadModel('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
			
		if (isset($this->request->post['addresses'])) { 
      		$this->data['addresses'] = $this->request->post['addresses'];
		} elseif (isset($this->request->get['customer_id'])) {
			$this->data['addresses'] = $this->model_sale_customer->getAddressesByCustomerId($this->request->get['customer_id']);
		} else {
			$this->data['addresses'] = array();
    	}

        $this->data['category_products'] = $this->html->getSecureURL('product/product/category');
        $this->data['common_zone'] = $this->html->getSecureURL('common/zone');

		  if (!isset($this->request->get['customer_id'])) {
			$this->data['action'] = $this->html->getSecureURL('sale/customer/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_customer');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('sale/customer/update', '&customer_id=' . $this->request->get['customer_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_customer') . ' - ' . $this->data['firstname'] . ' ' . $this->data['lastname'] ;
			$this->data['update'] = $this->html->getSecureURL('listing_grid/customer/update_field','&id='.$this->request->get['customer_id']);
			$form = new AForm('HS');
		}

		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: '
   		 ));

		$form->setForm(array(
		    'form_name' => 'cgFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'cgFrm',
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
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

		$required_input = array('loginname', 'firstname', 'lastname', 'email', 'telephone', 'fax', 'password');

		foreach ( $required_input as $f ) {
			$this->data['form']['fields'][$f] = $form->getFieldHtml(array(
				'type' => ($f == 'password' ? 'passwordset' : 'input' ),
				'name' => $f,
				'value' => $this->data[$f],
				'required' => ( in_array($f, array('password', 'fax')) ? false: true),
			));
		}

		$this->data['form']['fields']['newsletter'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'newsletter',
		    'value' => $this->data['newsletter'],
			'options' => array(
				1 => $this->language->get('text_enabled'),
				0 => $this->language->get('text_disabled'),
			),
	    ));

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$groups = array( '' => $this->language->get('text_select_group'), );
		foreach ( $results as $item) {
			$groups[ $item['customer_group_id'] ] = $item['name'];
		}

		$this->data['form']['fields']['customer_group'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'customer_group_id',
		    'value' => $this->data['customer_group_id'],
			'options' => $groups,
	    ));

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'status',
		    'value' => $this->data['status'],
			'style'  => 'btn_switch',
	    ));
          $this->data['form']['fields']['approved'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'approved',
		    'value' => $this->data['approved'],
			'style'  => 'btn_switch',
	    ));
		$this->view->assign('help_url', $this->gen_help_url('customer_edit') );
		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/sale/customer_form.tpl' );
	}
	 
}
?>
