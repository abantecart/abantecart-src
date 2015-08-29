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
class ControllerPagesLocalisationLengthClass extends AController {
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
       		'href'      => $this->html->getSecureURL('localisation/length_class'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		));

		$grid_settings = array(
			'table_id' => 'length_grid',
			'url' => $this->html->getSecureURL('listing_grid/length_class'),
			'editurl' => $this->html->getSecureURL('listing_grid/length_class/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/length_class/update_field'),
			'sortname' => 'title',
			'sortorder' => 'asc',
			'columns_search' => false,
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/length_class/update', '&length_class_id=%ID%')
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
            $this->language->get('column_unit'),
            $this->language->get('column_value'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'title',
				'index' => 'title',
                'align' => 'left',
			),
			array(
				'name' => 'unit',
				'index' => 'unit',
                'align' => 'center',
			),
			array(
				'name' => 'value',
				'index' => 'value',
                'align' => 'center',
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/length_class/insert') );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('length_status_listing') );

		$this->processTemplate('pages/localisation/length_class_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		if ( $this->request->is_POST() && $this->_validateForm() ) {
			$length_class_id = $this->model_localisation_length_class->addLengthClass($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
      		$this->redirect($this->html->getSecureURL('localisation/length_class/update', '&length_class_id=' . $length_class_id ));
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
	  		$this->model_localisation_length_class->editLengthClass($this->request->get['length_class_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/length_class/update', '&length_class_id=' . $this->request->get['length_class_id'] ));
    	}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  
  	private function _getForm() {

		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/length_class');

   		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('localisation/length_class'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		if (isset($this->request->get['length_class_id']) && $this->request->is_GET() ) {
      		$length_class_info = $this->model_localisation_length_class->getLengthClass($this->request->get['length_class_id']);
    	}
		
		if (isset($this->request->post['length_class_description'])) {
			$this->data['length_class_description'] = $this->request->post['length_class_description'];
		} elseif (isset($this->request->get['length_class_id'])) {
			$this->data['length_class_description'] = $this->model_localisation_length_class->getLengthClassDescriptions($this->request->get['length_class_id']);
		} else {
			$this->data['length_class_description'] = array();
		}

		if (isset($this->request->post['value'])) {
			$this->data['value'] = $this->request->post['value'];
		} elseif (isset($length_class_info)) {
			$this->data['value'] = $length_class_info['value'];
		} else {
			$this->data['value'] = '';
		}

		if (!isset($this->request->get['length_class_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/length_class/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_class');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/length_class/update', '&length_class_id=' . $this->request->get['length_class_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_class');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/length_class/update_field','&id='.$this->request->get['length_class_id']);
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
			'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
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

		$content_language_id = $this->language->getContentLanguageID();

		$this->data['form']['fields']['title'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'length_class_description['.$content_language_id.'][title]',
			'value' => $this->data['length_class_description'][$content_language_id]['title'],
			'required' => true,
			'style' => 'large-field',
			'multilingual' => true,
		));
		$this->data['form']['fields']['unit'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'length_class_description['.$content_language_id.'][unit]',
			'value' => $this->data['length_class_description'][$content_language_id]['unit'],
			'required' => true,
			'style' => 'large-field',
			'multilingual' => true,
		));
		$this->data['form']['fields']['value'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'value',
			'value' => $this->data['value'],
		));

		$this->view->batchAssign( $this->data );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $content_language_id);
		$this->view->assign('help_url', $this->gen_help_url('length_status_edit') );
        $this->processTemplate('pages/localisation/length_class_form.tpl' );
  	}

	private function _validateForm() {
		if (!$this->user->canModify('localisation/length_class')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['length_class_description'] as $language_id => $value) {
			if ( mb_strlen($value['title']) < 2 || mb_strlen($value['title']) > 32 ) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if ((!$value['unit']) || mb_strlen($value['unit']) > 4 ) {
				$this->error['unit'][$language_id] = $this->language->get('error_unit');
			}
		}
		$this->extensions->hk_ValidateData( $this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
}
