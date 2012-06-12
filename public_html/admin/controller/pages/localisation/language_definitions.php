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
class ControllerPagesLocalisationLanguageDefinitions extends AController {
	public $data = array();
	private $error = array();
	private $fields = array('language_key', 'language_value', 'block', 'section');
  
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
       		'href'      => $this->html->getSecureURL('localisation/language_definitions'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$grid_settings = array(
			'table_id' => 'lang_definition_grid',
			'url' => $this->html->getSecureURL('listing_grid/language_definitions'),
			'editurl' => $this->html->getSecureURL('listing_grid/language_definitions/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/language_definitions/update_field'),
			'sortname' => 'update_date',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/language_definitions/update', '&language_definition_id=%ID%')
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
		    'form_name' => 'lang_definition_grid_search',
	    ));

		$grid_search_form = array();
        $grid_search_form['id'] = 'lang_definition_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'lang_definition_grid_search',
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

	    $grid_search_form['fields']['section'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'section',
            'options' => array(
	            '' => $this->language->get('text_select_section'),
                1 => $this->language->get('text_admin'),
                0 => $this->language->get('text_storefront'),
            ),
	    ));

		$grid_settings['search_form'] = true;


		$grid_settings['colNames'] = array(
            $this->language->get('column_language'),
            $this->language->get('column_block'),
			$this->language->get('column_key'),
			$this->language->get('column_translation'),
			$this->language->get('column_update_date'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
                'align' => 'center',
				'sorttype' => 'string',
			),
            array(
				'name' => 'block',
				'index' => 'block',
                'align' => 'center',
				'sorttype' => 'string',
			),
            array(
				'name' => 'language_key',
				'index' => 'language_key',
				'align' => 'center',
				'sorttype' => 'string',
			),
			array(
				'name' => 'language_value',
				'index' => 'language_value',
                'align' => 'center',
				'sorttype' => 'string',
                'sortable' => false,
			),
			array(
				'name' => 'update_date',
				'index' => 'update_date',
                'align' => 'center',
				'sorttype' => 'string',
				'search' => false,
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign ( 'search_form', $grid_search_form );
		
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/language_definitions/insert') );
		$this->view->assign('help_url', $this->gen_help_url('language_definitions_listing') );

		$this->processTemplate('pages/localisation/language_definitions_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			foreach ( $this->request->post['language_definition_id'] as $lang_id => $id ) {
                $data = array(
                    'language_id' => $lang_id,
                    'section' => $this->request->post['section'],
                    'block' => $this->request->post['block'],
                    'language_key' => $this->request->post['language_key'],
                    'language_value' => $this->request->post['language_value'][$lang_id],
                );
                $language_definition_id = $this->model_localisation_language_definitions->addLanguageDefinition( $data);
            }

			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/language_definitions/update', '&language_definition_id=' . $language_definition_id ));
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
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
            foreach ( $this->request->post['language_definition_id'] as $lang_id => $id ) {
                $data = array(
                    'language_id' => $lang_id,
                    'section' => $this->request->post['section'],
                    'block' => $this->request->post['block'],
                    'language_key' => $this->request->post['language_key'],
                    'language_value' => $this->request->post['language_value'][$lang_id],
                );
                if ($id) {
                	$this->model_localisation_language_definitions->editLanguageDefinition($id, $data);
                } else{
                   $language_definition_id = $this->model_localisation_language_definitions->addLanguageDefinition( $data);
                }	
            }

			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/language_definitions/update', '&language_definition_id=' . $this->request->get['language_definition_id'] ));
		}

		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {

		if (isset($this->error['warning'])) {
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
       		'href'      => $this->html->getSecureURL('localisation/language_definitions'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$this->data['cancel'] = $this->html->getSecureURL('localisation/language_definitions');
        $languages = $this->language->getAvailableLanguages();
		foreach ( $languages as $lang ) {
			$this->data['languages'][ $lang['language_id'] ] = $lang;
		}

		if (isset($this->request->get['language_definition_id'])) {
			//language_definition_id is provieded, need to load definition for all languages.
			$item = $this->model_localisation_language_definitions->getLanguageDefinition($this->request->get['language_definition_id']);
			//make sure we load all the langaues properly in case they were not used yet. 
			foreach ( $languages as $lang ) {
				$new_lang_obj = new ALanguage ( $this->registry, $lang['code'], $item['section']);
				$new_lang_obj->_load( $new_lang_obj->convert_block_to_file( $item['block'] ) );
			}			
			//load definitions for all languages now		
            $items = $this->model_localisation_language_definitions->getLanguageDefinitions(array(
            	'subsql_filter' => "section = '".$item['section']."' AND block = '".$item['block']."' AND language_key = '".$item['language_key']."'  "));		           	
		}

		foreach ( $this->fields as $field ) {
			if (isset($this->request->post[$field])) {
				$this->data[$field] = $this->request->post[$field];
			} elseif (isset($item)) {
				$this->data[$field] = $item[$field];
			} else {
				$this->data[$field] = '';
			}
		}

		if (!isset($this->request->get['language_definition_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/language_definitions/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .' '. $this->language->get('text_definition');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/language_definitions/update', '&language_definition_id=' . $this->request->get['language_definition_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_definition');
			$this->data['update'] = '';//$this->html->getSecureURL('listing_grid/language_definitions/update_field','&id='.$this->request->get['language_definition_id']);
			$form = new AForm('ST');
		}
		
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: '
   		 ));

		$form->setForm(array(
		    'form_name' => 'definitionFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'definitionFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'definitionFrm',
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

        $this->data['form']['fields']['section'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'section',
			'options' => array(
                '' => $this->language->get('text_select'),
                1 => $this->language->get('text_admin'),
                0 => $this->language->get('text_storefront'),
            ),
		    'value' => $this->data['section'],
			'required' => true,
	    ));
        $this->data['form']['fields']['block'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'block',
		    'value' => $this->data['block'],
			'required' => true,
	        'help_url' => $this->gen_help_url('block'),
	    ));
	    $this->data['form']['fields']['language_key'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'language_key',
		    'value' => $this->data['language_key'],
		    'required' => true,
		    'help_url' => $this->gen_help_url('language_key'),
	    ));

        foreach ( $this->data['languages'] as $i ) {
            $value = '';
            $id = '';
            if ( !empty( $this->request->post['language_value'][$i['language_id']] )) {
                $value = $this->request->post['language_value'][$i['language_id']];
	            foreach ( $items as $ii ) {
                    if ( $ii['language_id'] == $i['language_id'] ) {
                        $id = $ii['language_definition_id'];
                        break;
                    }
                }
            } else if ( !empty( $items ) ) {
                foreach ( $items as $ii ) {
                    if ( $ii['language_id'] == $i['language_id'] ) {
                        $value = $ii['language_value'];
                        $id = $ii['language_definition_id'];
                        break;
                    }
                }
            }
            $this->data['form']['fields']['language_value'][$i['language_id']] = $form->getFieldHtml(array(
                'type' => 'textarea',
                'name' => 'language_value['.$i['language_id'].']',
                'value' => $value,
                'required' => true,
                'style' => 'large-field',
            ));

            $this->data['form']['fields']['language_definition_id'][$i['language_id']] = $form->getFieldHtml(array(
                'type' => 'hidden',
                'name' => 'language_definition_id['.$i['language_id'].']',
                'value' => $id,
                'required' => true,
            ));


        }
		$this->view->assign('help_url', $this->gen_help_url('language_definition_edit') );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/localisation/language_definitions_form.tpl' );
	}
	
	private function _validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/language_definitions')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['language_key']) {
			$this->error['language_key'] = $this->language->get('error_language_key');
		}

        foreach ( $this->request->post['language_value'] as $key => $val ) {
            if ( empty($val) )
                $this->error['language_value'][$key] = $this->language->get('error_language_value');
        }

        if (!$this->request->post['block']) {
			$this->error['block'] = $this->language->get('error_block');
		}

        if (!is_numeric($this->request->post['section'])) {
			$this->error['section'] = $this->language->get('error_section');
		}


		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
?>