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
class ControllerPagesSaleCustomer extends AController {
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
                'clone' => array(
                    'text' => $this->language->get('button_actas'),
				    'href' => $this->html->getSecureURL('sale/customer/actonbehalf', '&customer_id=%ID%'),
				    'target' => 'new',
                ),
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
			$this->language->get('text_order'),
		);
		$grid_settings['colModel'] = array(
			array( 'name' => 'name',
					'index' => 'name',
					'width' => 160,
					'align' => 'center', ),
			array( 'name' => 'email',
					'index' => 'email',
					'width' => 140,
					'align' => 'center', ),
			array( 'name' => 'customer_group',
					'index' => 'customer_group',
					'width' => 80,
					'align' => 'center',
					'search' => false ),
			array( 'name' => 'status',
					'index' => 'status',
					'width' => 120,
					'align' => 'center',
					'search' => false ),
			array( 'name' => 'approved',
					'index' => 'approved',
					'width' => 110,
					'align' => 'center',
					'search' => false ),
			array( 'name' => 'orders',
					'index' => 'orders_count',
					'width' => 70,
					'align' => 'center',
					'search' => false ),
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
  
  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
      	  	$customer_id = $this->model_sale_customer->addCustomer($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('sale/customer/update', '&customer_id=' . $customer_id ) );
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	} 
   
  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$customer_id = $this->request->get['customer_id'];
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm($customer_id)) {

		    if( (int)$this->request->post['approved'] ){
		  		$customer_info = $this->model_sale_customer->getCustomer($customer_id);
		  		if( !$customer_info['approved']){
					  $this->_sendMail($customer_id);
		  		}
		  	}

			$this->model_sale_customer->editCustomer($this->request->get['customer_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('sale/customer/update', '&customer_id=' . $customer_id ) );
		}

    	$this->_getForm();

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

		if(has_value($customer_info['orders_count']) && $this->request->get['customer_id']){
			$this->data['button_orders_count'] = $this->html->buildButton(
							array(
								'name' => 'view orders',
								'text' => $this->language->get('text_order').': '.$customer_info['orders_count'],
								'style' => 'button2',
								'href'=> $this->html->getSecureURL('sale/order','&customer_id='.$this->request->get['customer_id']),
								'title' => $this->language->get('text_view').' '.$this->language->get('tab_history')
							)
			);
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

		$this->data['tabs']['general'] = array(
										'href'  => "Javascript:void(0);",
										'text'  => $this->language->get('tab_customer_details'),
										'class' => 'active'
										);
		if(has_value($this->request->get['customer_id'])){
		$this->data['tabs'][] = array(
										'href' => $this->html->getSecureURL('sale/customer_transaction','&customer_id='.$this->request->get['customer_id']),
										'text' => $this->language->get('tab_transactions'),
										'class'=>''
										);
		}

        $this->data['button_actas'] = $this->html->buildButton(array(
		    'text' => $this->language->get('button_actas'),
		    'style' => 'button1',
			'href' => $this->html->getSecureURL('sale/customer/actonbehalf', '&customer_id='.$this->request->get['customer_id']),	
			'target' => 'new'	    
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
        $this->loadModel('sale/customer_transaction');
		$balance = $this->model_sale_customer_transaction->getBalance($this->request->get['customer_id']);
		$currency = $this->currency->getCurrency($this->config->get('config_currency'));

		$this->data['balance'] = $this->language->get('text_balance').' '.$currency['symbol_left'].round($balance,2).$currency['symbol_right'];
		$this->view->batchAssign( $this->data );

		$this->processTemplate('pages/sale/customer_form.tpl' );
	}

	public function approve() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('mail/customer');
    	
		if (!$this->user->canModify('sale/customer')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('sale/customer'));
		}

		if (!isset($this->request->get['customer_id'])) {
			$this->redirect($this->html->getSecureURL('sale/customer'));
		}

		$this->model_sale_customer->editCustomerField($this->request->get['customer_id'], 'approved', true );
		$this->_sendMail($this->request->get['customer_id']);

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->redirect($this->html->getSecureURL('sale/customer'));
	} 
	 
	public function actonbehalf() {

        $this->extensions->hk_InitData($this,__FUNCTION__);
    	
		if (isset($this->request->get['customer_id'])) {
            startStorefrontSession($this->user->getId(), array('customer_id' => $this->request->get['customer_id']));
			$this->redirect($this->html->getCatalogURL('account/account'));
		}

        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->redirect($this->html->getSecureURL('sale/customer'));
	}

	/**
	 * @param null $customer_id
	 * @return bool
	 */
	private function _validateForm($customer_id = null) {
    	if (!$this->user->canModify('sale/customer')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
    			
		$login_name_pattern = '/^[\w._-]+$/i';
    	if ( (strlen(utf8_decode($this->request->post['loginname'])) < 5) || (strlen(utf8_decode($this->request->post['loginname'])) > 64)
    		|| (!preg_match($login_name_pattern, $this->request->post['loginname']) && $this->config->get('prevent_email_as_login') ) ) {
      		$this->error['loginname'] = $this->language->get('error_loginname');
		//check uniqunes of login name
    	} else if ( !$this->model_sale_customer->is_unique_loginname($this->request->post['loginname'], $customer_id) ) {
      		$this->error['loginname'] = $this->language->get('error_loginname_notunique');
    	}
   	    	
    	if ((strlen(utf8_decode($this->request->post['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['firstname'])) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((strlen(utf8_decode($this->request->post['lastname'])) < 1) || (strlen(utf8_decode($this->request->post['lastname'])) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}

		$email_pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}\.[A-Z]{2,6}$/i';
    	
		if ((strlen(utf8_decode($this->request->post['email'])) > 96) || (!preg_match($email_pattern, $this->request->post['email']))) {
      		$this->error['email'] = $this->language->get('error_email');
    	}

    	if ((strlen(utf8_decode($this->request->post['telephone'])) < 3) || (strlen(utf8_decode($this->request->post['telephone'])) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}

    	if (($this->request->post['password']) || (!isset($this->request->get['customer_id']))) {
      		if ((strlen(utf8_decode($this->request->post['password'])) < 4)) {
        		$this->error['password'] = $this->language->get('error_password');
      		}

	  		if (!$this->error['password'] && $this->request->post['password'] != $this->request->post['password_confirm']) {
	    		$this->error['password'] = $this->language->get('error_confirm');
	  		}
    	}
		if($this->request->post['addresses']){
			foreach ($this->request->post['addresses'] as $key => $address) {
				if ((strlen(utf8_decode($address['firstname'])) < 1) || (strlen(utf8_decode($address['firstname'])) > 32)) {
					$this->error[$key]['firstname'] = $this->language->get('error_address_firstname');
				}
				if ((strlen(utf8_decode($address['lastname'])) < 1) || (strlen(utf8_decode($address['lastname'])) > 32)) {
					$this->error[$key]['lastname'] = $this->language->get('error_address_lastname');
				}
				if ((strlen(utf8_decode($address['address_1'])) < 1)) {
					$this->error[$key]['address_1'] = $this->language->get('error_address_1');
				}
				if ((strlen(utf8_decode($address['city'])) < 1)) {
					$this->error[$key]['city'] = $this->language->get('error_city');
				}
				if (empty($address['country_id']) || $address['country_id'] == 'FALSE') {
					$this->error[$key]['country_id'] = $this->language->get('error_country');
				}
				if (empty($address['zone_id']) || $address['zone_id'] == 'FALSE') {
					$this->error[$key]['zone_id'] = $this->language->get('error_zone');
				}
			}
		}
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

	/**
	 * @param int $id  - customer_id
	 */
	private function _sendMail($id){

			// send email to customer
			$customer_info = $this->model_sale_customer->getCustomer($id);

			if ($customer_info) {

				$this->loadLanguage('mail/customer');
				$this->loadModel('setting/store');

				$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

				if ($store_info) {
					$store_name = $store_info['store_name'];
					$store_url = $store_info['config_url'] . 'index.php?rt=account/login';
				} else {
					$store_name = $this->config->get('store_name');
					$store_url = $this->config->get('config_url') . 'index.php?rt=account/login';
				}

				$message  = sprintf($this->language->get('text_welcome'), $store_name) . "\n\n";;
				$message .= $this->language->get('text_login') . "\n";
				$message .= $store_url . "\n\n";
				$message .= $this->language->get('text_services') . "\n\n";
				$message .= $this->language->get('text_thanks') . "\n";
				$message .= $store_name;
				$mail = new AMail( $this->config );
				$mail->setTo($customer_info['email']);
				$mail->setFrom($this->config->get('store_main_email'));
				$mail->setSender($store_name);
				$mail->setSubject(sprintf($this->language->get('text_subject'), $store_name));
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
	}

}