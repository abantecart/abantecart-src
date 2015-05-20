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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesSaleCustomerGroup extends AController {
	public $data = array();
	public $error = array();
	private $errors = array('warning', 'name',);
 
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('breadcrumb_title') );

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
       		'href'      => $this->html->getSecureURL('sale/customer_group'),
       		'text'      => $this->language->get('breadcrumb_title'),
      		'separator' => ' :: ',
			'current'   => true
   		 ));

		$grid_settings = array(
			//id of grid
            'table_id' => 'cg_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/customer_group'),
			'editurl' => $this->html->getSecureURL('listing_grid/customer_group/update'),
            'sortname' => 'name',
            'sortorder' => 'asc',
			'multiselect' => 'true',
            // actions
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('sale/customer_group/update', '&customer_group_id=%ID%')
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                )
            ),
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_name'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 700,
				'align' => 'left',
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

        $this->view->assign('heading_title', $this->language->get('breadcrumb_title') );
		$this->view->assign('insert', $this->html->getSecureURL('sale/customer_group/insert') );
		$this->view->assign('help_url', $this->gen_help_url('customer_group_listing') );

		$this->processTemplate('pages/sale/customer_group_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		
		if ( $this->request->is_POST() && $this->_validateForm()) {
			$customer_group_id = $this->model_sale_customer_group->addCustomerGroup($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('sale/customer_group', '&customer_group_id=' . $customer_group_id ) );
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('sale/customer_group');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('sale/customer_group');

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
		
		if ( $this->request->is_POST() && $this->_validateForm()) {
			$this->model_sale_customer_group->editCustomerGroup($this->request->get['customer_group_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('sale/customer_group', '&customer_group_id=' . $this->request->get['customer_group_id'] ) );
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {

 		$this->data['error'] = array();
		foreach ( $this->errors as $f ) {
			if (isset ( $this->error[$f] )) {
				$this->data['error'][$f] = $this->error[$f];
			}
		}

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('sale/customer_group'),
       		'text'      => $this->language->get('breadcrumb_title'),
      		'separator' => ' :: '
   		 ));
			
    	$this->data['cancel'] = $this->html->getSecureURL('sale/customer_group');

		if (isset($this->request->get['customer_group_id']) &&  $this->request->is_GET() ) {
			$customer_group_info = $this->model_sale_customer_group->getCustomerGroup($this->request->get['customer_group_id']);
		}

		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} elseif (isset($customer_group_info)) {
			$this->data['name'] = $customer_group_info['name'];
		} else {
			$this->data['name'] = '';
		}

		if (!isset($this->request->get['customer_group_id'])) {
			$this->data['action'] = $this->html->getSecureURL('sale/customer_group/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('heading_title');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('sale/customer_group/update', '&customer_group_id=' . $this->request->get['customer_group_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('heading_title');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/customer_group/update_field','&id='.$this->request->get['customer_group_id']);
			$form = new AForm('HS');
		}

		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'   => true
   		 ));

		$tabs['general'] = array(
			'name' => 'customer_group_edit',
			'text' => $this->language->get('tab_general'),
			'href' => $this->html->getSecureURL('sale/customer_group/update', '&customer_group_id=' . $this->request->get['customer_group_id'], true),
			'active'=> true,
			'sort_order' => 0
		);

		$obj = $this->dispatch('responses/common/tabs',array(
															'sale/customer_group', //parent controller. Use customer group to use for other extensions that will add tabs via their hooks
															array('tabs'=>$tabs))
															);
		$this->data['tabs'] = $obj->dispatchGetOutput();

		$form->setForm(array(
		    'form_name' => 'cgFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'cgFrm',
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

		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'name',
			'value' => $this->data['name'],
			'required' => true,
		));

		$this->view->assign('help_url', $this->gen_help_url('customer_group_edit') );
		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/sale/customer_group_form.tpl' );
	}

	private function _validateForm() {
		if (!$this->user->canModify('sale/customer_group')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ( mb_strlen($this->request->post['name']) < 2 || mb_strlen($this->request->post['name']) > 64 ) {
			$this->error['name'] = $this->language->get('error_name');
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
