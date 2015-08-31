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
class ControllerPagesLocalisationStockStatus extends AController {
	public $data = array();
	public $error = array();
   
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );
		
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
       		'href'      => $this->html->getSecureURL('localisation/stock_status'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		));

		$grid_settings = array(
			'table_id' => 'stock_grid',
			'url' => $this->html->getSecureURL('listing_grid/stock_status'),
			'editurl' => $this->html->getSecureURL('listing_grid/stock_status/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/stock_status/update_field'),
			'sortname' => 'name',
			'sortorder' => 'asc',
			'columns_search' => false,
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/stock_status/update', '&stock_status_id=%ID%')
                ),
	            'save' => array(
                    'text' => $this->language->get('button_save'),
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
				'width' => 600,
                'align' => 'left',
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/stock_status/insert') );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());

		$this->view->assign('help_url', $this->gen_help_url('stock_status_listing') );
		$this->processTemplate('pages/localisation/stock_status_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
              
  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		if ( $this->request->is_POST() && $this->_validateForm() ) {

			$stock_status_id = $this->model_localisation_stock_status->addStockStatus($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
      		$this->redirect($this->html->getSecureURL('localisation/stock_status/update', '&stock_status_id=' . $stock_status_id ));
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

    	$this->document->setTitle( $this->language->get('heading_title') );

    	if ( $this->request->is_POST() && $this->_validateForm()) {
	  		$this->model_localisation_stock_status->editStockStatus($this->request->get['stock_status_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/stock_status/update', '&stock_status_id=' . $this->request->get['stock_status_id'] ));
    	}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	private function _getForm() {

		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/stock_status');

   		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('localisation/stock_status'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 )); 
		
		if (isset($this->request->post['stock_status'])) {
			$this->data['stock_status'] = $this->request->post['stock_status'];
		} elseif (isset($this->request->get['stock_status_id'])) {
			$this->data['stock_status'] = $this->model_localisation_stock_status->getStockStatusDescriptions($this->request->get['stock_status_id']);
		} else {
			$this->data['stock_status'] = array();
		}

		if (!isset($this->request->get['stock_status_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/stock_status/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .' '. $this->language->get('text_status');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/stock_status/update', '&stock_status_id=' . $this->request->get['stock_status_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_status');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/stock_status/update_field','&id='.$this->request->get['stock_status_id']);
			$form = new AForm('HS');
		}

		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$form->setForm(array(
		    'form_name' => 'editFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'editFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'editFrm',
		    'action' => $this->data['action'],
			'attr' => 'data-confirm-exit="true" class="aform form-horizontal"'
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
			'name' => 'stock_status['.$this->session->data['content_language_id'].'][name]',
			'value' => $this->data['stock_status'][$this->session->data['content_language_id']]['name'],
			'required' => true,
			'style' => 'large-field',
			'multilingual' => true,
		));

		$this->view->batchAssign( $this->data );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $this->session->data['content_language_id']);
		$this->view->assign('help_url', $this->gen_help_url('stock_status_edit') );
        $this->processTemplate('pages/localisation/stock_status_form.tpl' );
  	}
  	
	private function _validateForm() {
    	if (!$this->user->canModify('localisation/stock_status')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
	
    	foreach ($this->request->post['stock_status'] as $language_id => $value) {
      		if ( mb_strlen($value['name']) < 2 || mb_strlen($value['name']) > 32 ) {
        		$this->error['name'][$language_id] = $this->language->get('error_name');
      		}
    	}

		$this->extensions->hk_ValidateData($this);
		
		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

}