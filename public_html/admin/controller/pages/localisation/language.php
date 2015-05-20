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
class ControllerPagesLocalisationLanguage extends AController {
	public $data = array();
	public $error = array();
	private $fields = array('name', 'code', 'locale', 'image', 'directory', 'sort_order', 'status' );
  
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

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
       		'href'      => $this->html->getSecureURL('localisation/language'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$grid_settings = array(
			//id of grid
            'table_id' => 'languages_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/language'),
            // url to send data for edit / delete
			'editurl' => $this->html->getSecureURL('listing_grid/language/update'),
            // url to update one field
			'update_field' => $this->html->getSecureURL('listing_grid/language/update_field'),
            // default sort column
			'sortname' => 'sort_order',
			// columns for drag sort
			'drag_sort_column' => 'sort_order',
            // actions
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/language/update', '&language_id=%ID%')
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
                'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
            ),
		);

        $form = new AForm();
	    $form->setForm(array(
		    'form_name' => 'languages_grid_search',
	    ));

	    $grid_search_form = array();
        $grid_search_form['id'] = 'languages_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'languages_grid_search',
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

	    $grid_search_form['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'status',
            'options' => array(
                1 => $this->language->get('text_enabled'),
    	        0 => $this->language->get('text_disabled'),
                '' => $this->language->get('text_select_status'),
            ),
	    ));

		$grid_settings['search_form'] = true;


		$grid_settings['colNames'] = array(
			$this->language->get('column_name'),
			$this->language->get('column_code'),
			$this->language->get('column_sort_order'),
			$this->language->get('entry_status'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 270,
				'align' => 'center',
				'sorttype' => 'string',
			),
			array(
				'name' => 'code',
				'index' => 'code',
				'width' => 70,
                'align' => 'center',
				'sorttype' => 'string',
			),
			array(
				'name' => 'sort_order',
				'index' => 'sort_order',
				'width' => 90,
                'align' => 'center',
				'sorttype' => 'string',
				'search' => false,
			),
            array(
				'name' => 'status',
				'index' => 'status',
				'width' => 110,
                'align' => 'center',
				'sortable' => false,
	            'search' => false,
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign ( 'search_form', $grid_search_form );
		
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/language/insert') );
		$this->view->assign('help_url', $this->gen_help_url('language_listing') );

		$this->view->assign('manage_extensions', $this->html->buildElement(
				array(
						'type' => 'button',
						'name' => 'manage_extensions',
						'href' => $this->html->getSecureURL('extension/extensions/language'),
						'text' => $this->language->get('button_manage_extensions'),
						'title' => $this->language->get('button_manage_extensions')
				)));

		$this->processTemplate('pages/localisation/language_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		if ($this->request->is_POST() && $this->_validateForm()) {

			$language_id = $this->model_localisation_language->addLanguage($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/language/update', '&language_id=' . $language_id ));
		}

		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if(!$this->request->get['language_id']){
			$this->redirect($this->html->getSecureURL('localisation/language'));
		}
		
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->setTitle( $this->language->get('heading_title') );
		if ($this->request->is_POST() && $this->_validateForm()) {
			$this->model_localisation_language->editLanguage($this->request->get['language_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');			
			$this->redirect($this->html->getSecureURL('localisation/language/update', '&language_id=' . $this->request->get['language_id'] ));
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function loadlanguageData() {
        $this->extensions->hk_InitData($this,__FUNCTION__);
		if ($this->request->post['source_language']) {
			$this->session->data['success'] = $this->language->fillMissingLanguageEntries( $this->request->get['language_id'], $this->request->post['source_language'], $this->request->post['translate_method']);
			//This update effect cross system data. Clean whole cache
			$this->cache->delete('*');
		}
		$this->redirect($this->html->getSecureURL('localisation/language/update', '&language_id=' . $this->request->get['language_id'] ));
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {
		if ($this->error) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		$this->data['error'] = $this->error;

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/language'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$this->data['cancel'] = $this->html->getSecureURL('localisation/language');

		if (isset($this->request->get['language_id']) && $this->request->is_GET()) {
			$language_info = $this->model_localisation_language->getLanguage($this->request->get['language_id']);
		}

		foreach ( $this->fields as $field ) {
			if (isset($this->request->post[$field])) {
				$this->data[$field] = $this->request->post[$field];
			} elseif (isset($language_info)) {
				$this->data[$field] = $language_info[$field];
			} else {
				$this->data[$field] = '';
			}
		}

		if (!isset($this->request->get['language_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/language/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .'&nbsp;'. $this->language->get('text_language');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/language/update', '&language_id=' . $this->request->get['language_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .'&nbsp;'. $this->language->get('text_language') .' - ' . $this->data['name'];
			$this->data['update'] = $this->html->getSecureURL('listing_grid/language/update_field','&id='.$this->request->get['language_id']);
			$form = new AForm('HS');
		}
		
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$form->setForm(array(
		    'form_name' => 'languageFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'languageFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'languageFrm',
		    'action' => $this->data['action'],
			'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
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
			'style'  => 'btn_switch',
			'value' => $this->data['status'],
			'help_url' => $this->gen_help_url('status'),
	    ));
		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'name',
				'value' => $this->data['name'],
				'required' => true,
			    'help_url' => $this->gen_help_url('name'),
		));

		$this->data['form']['fields']['code'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'code',
				'value' => $this->data['code'],
				'required' => true,
			    'help_url' => $this->gen_help_url('code'),
		));

		$this->data['form']['fields']['locale'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'locale',
				'value' => $this->data['locale'],
				'required' => true,
			    'help_url' => $this->gen_help_url('locale'),
		));

		$this->data['form']['fields']['directory'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'directory',
				'value' => $this->data['directory'],
				'required' => true,
			    'help_url' => $this->gen_help_url('directory'),
		));

		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'sort_order',
				'value' => $this->data['sort_order'],
				'required' => true,
		));

		if(isset($this->request->get['language_id'])){
			$form2 = new AForm('HT');
			$form2->setForm(array(
				'form_name' => 'languageLoadFrm',
			));

			$this->data['form2']['id'] = 'languageFrm';
			$this->data['form2']['form_open'] = $form2->getFieldHtml(array(
				'type' => 'form',
				'name' => 'languageLoadFrm',
				'action' => $this->html->getSecureURL('localisation/language/loadlanguageData', '&language_id=' . $this->request->get['language_id'] ),
				'attr' => 'class="aform form-horizontal"',
			));
			$this->data['form2']['load_data'] = $form2->getFieldHtml(array(
				'type' => 'button',
				'name' => 'load_data',
				'text' => $this->language->get('button_load_language'),
				'style' => 'button3',
			));

			$all_languages = array('');
			$all_languages[0] = "-----";
			foreach ($this->language->getAvailableLanguages() as $result) {
				$all_languages[$result['language_id']] = $result['name'];
			}
			$this->data['form2']['fields']['language_selector'] = $form2->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'source_language',
				'value' => '',
				'options' => $all_languages,
			));

			$translate_methods = $this->language->getTranslationMethods();
			$this->data['form2']['fields']['translate_method_selector'] = $form2->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'translate_method',
				'value' => '',
				'options' => $translate_methods,
			));
		}else{
			$this->data['entry_create_language_note'] = $this->language->get('create_language_note');
		}

		$this->view->assign('help_url', $this->gen_help_url('language_edit') );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/localisation/language_form.tpl' );
	}

	private function _validateForm() {
		if (!$this->user->canModify('localisation/language')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ( mb_strlen($this->request->post['name']) < 2 || mb_strlen($this->request->post['name']) > 32 ) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (mb_strlen($this->request->post['code']) < 2) {
			$this->error['code'] = $this->language->get('error_code');
		}

		if (!$this->request->post['locale']) {
			$this->error['locale'] = $this->language->get('error_locale');
		}

		if (!$this->request->post['directory']) {
			$this->error['directory'] = $this->language->get('error_directory');
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}