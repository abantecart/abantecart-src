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
class ControllerPagesLocalisationCurrency extends AController {
	public $data = array();
	public $error = array();
	private $fields = array('title', 'code', 'symbol_left', 'symbol_right', 'decimal_place', 'value', 'status');
 
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
       		'href'      => $this->html->getSecureURL('localisation/currency'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'   => true
   		 ));

		$grid_settings = array(
			'table_id' => 'currency_grid',
			'url' => $this->html->getSecureURL('listing_grid/currency'),
			'editurl' => $this->html->getSecureURL('listing_grid/currency/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/currency/update_field'),
			'sortname' => 'title',
			'sortorder' => 'asc',
			'columns_search' => false,
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/currency/update', '&currency_id=%ID%')
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
            $this->language->get('column_title'),
            $this->language->get('column_code'),
            $this->language->get('column_value'),
            $this->language->get('column_date_modified'),
            $this->language->get('column_status'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'title',
				'index' => 'title',
				'width' => 200,
                'align' => 'center',
			),
			array(
				'name' => 'code',
				'index' => 'code',
				'width' => 120,
                'align' => 'center',
			),
			array(
				'name' => 'value',
				'index' => 'value',
				'width' => 130,
                'align' => 'center',
			),
			array(
				'name' => 'date_modified',
				'index' => 'date_modified',
				'width' => 100,
                'align' => 'center',
			),
			array(
				'name' => 'status',
				'index' => 'status',
				'width' => 130,
                'align' => 'center',
			),
		);

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		
 		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/currency/insert') );
		$this->view->assign('help_url', $this->gen_help_url('currency_listing') );

		$this->processTemplate('pages/localisation/currency_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );

		if ( $this->request->is_POST() && $this->_validateForm() ) {
			$currency_id = $this->model_localisation_currency->addCurrency($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('localisation/currency/update', '&currency_id=' . $currency_id ) );
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

		if ( $this->request->is_POST() && $this->_validateForm() ) {
			$this->model_localisation_currency->editCurrency($this->request->get['currency_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('localisation/currency/update', '&currency_id=' . $this->request->get['currency_id'] ) );
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {
		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/currency');

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('localisation/currency'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));


		if (isset($this->request->get['currency_id']) && $this->request->is_GET() ) {
			$currency_info = $this->model_localisation_currency->getCurrency($this->request->get['currency_id']);
		}

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($currency_info)) {
				$this->data[$f] = $currency_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (!isset($this->request->get['currency_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/currency/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .' '. $this->language->get('text_currency');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/currency/update', '&currency_id=' . $this->request->get['currency_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_currency') . ' - ' . $this->data['title'];
			$this->data['update'] = $this->html->getSecureURL('listing_grid/currency/update_field','&id='.$this->request->get['currency_id']);
			$form = new AForm('HS');
		}
		
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$form->setForm(array(
		    'form_name' => 'cgFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'cgFrm',
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

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
				    'type' => 'checkbox',
				    'name' => 'status',
				    'value' => $this->data['status'],
					'style'  => 'btn_switch',
			    ));

		foreach ( $this->fields as $f ) {
			if ( $f == 'status' ) break;
			$this->data['form']['fields'][$f] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => $f,
				'value' => $this->data[$f],
				'required' => ( !in_array($f, array('title','code')) ? false: true),
			));
		}

		$this->view->assign('help_url', $this->gen_help_url('currency_edit') );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/localisation/currency_form.tpl' );
	}
	
	private function _validateForm() {
		if (!$this->user->canModify('localisation/currency')) { 
			$this->error['warning'] = $this->language->get('error_permission');
		} 

		if ( mb_strlen($this->request->post['title']) < 2 || mb_strlen($this->request->post['title']) > 32 ) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if ( mb_strlen($this->request->post['code']) != 3) {
			$this->error['code'] = $this->language->get('error_code');
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) { 
			return TRUE;
		} else {
			return FALSE;
		}
	}
}