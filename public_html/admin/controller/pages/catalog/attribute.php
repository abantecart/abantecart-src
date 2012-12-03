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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerPagesCatalogAttribute extends AController {
	public $data = array();
	private $error = array();
	private $attribute_manager;

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		$this->attribute_manager = new AAttribute_Manager();
	}

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->view->assign('error_warning', $this->error[ 'warning' ]);
		$this->view->assign('success', $this->session->data[ 'success' ]);
		if (isset($this->session->data[ 'success' ])) {
			unset($this->session->data[ 'success' ]);
		}

		$this->document->initBreadcrumb(array(
		                                     'href' => $this->html->getSecureURL('index/home'),
		                                     'text' => $this->language->get('text_home'),
		                                     'separator' => FALSE
		                                ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/attribute'),
		                                    'text' => $this->language->get('heading_title'),
		                                    'separator' => ' :: '
		                               ));

		$grid_settings = array(
			'table_id' => 'attribute_grid',
			'url' => $this->html->getSecureURL('listing_grid/attribute'),
			'editurl' => $this->html->getSecureURL('listing_grid/attribute/update'),
			'update_field' => $this->html->getSecureURL('listing_grid/attribute/update_field'),
			'sortname' => 'ag.sort_order',
			'sortorder' => 'asc',
			'columns_search' => false,
			'actions' => array(
				'edit' => array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=%ID%')
				),
				'save' => array(
					'text' => $this->language->get('button_save'),
				),
				'delete' => array(
					'text' => $this->language->get('button_delete'),
				)
			),
		);

		$attribute_types = array( '' => $this->language->get('text_select_type') );
		$results = $this->attribute_manager->getAttributeTypes();
		foreach ($results as $type) {
			$attribute_types[ $type[ 'attribute_type_id' ] ] = $type[ 'type_name' ];
		}

		$attributes = array( '' => $this->language->get('text_select_parent') );
		$results = $this->attribute_manager->getAttributes(array(), 0, 0);
		foreach ($results as $type) {
			$attributes[ $type[ 'attribute_id' ] ] = $type[ 'name' ];
		}

		$form = new AForm();
		$form->setForm(array(
		                    'form_name' => 'attribute_grid_search',
		               ));

		$grid_search_form = array();
		$grid_search_form[ 'id' ] = 'attribute_grid_search';
		$grid_search_form[ 'form_open' ] = $form->getFieldHtml(array(
		                                                            'type' => 'form',
		                                                            'name' => 'attribute_grid_search',
		                                                            'action' => '',
		                                                       ));
		$grid_search_form[ 'submit' ] = $form->getFieldHtml(array(
		                                                         'type' => 'button',
		                                                         'name' => 'submit',
		                                                         'text' => $this->language->get('button_go'),
		                                                         'style' => 'button6',
		                                                    ));
		$grid_search_form[ 'reset' ] = $form->getFieldHtml(array(
		                                                        'type' => 'button',
		                                                        'name' => 'reset',
		                                                        'text' => $this->language->get('button_reset'),
		                                                        'style' => 'button2',
		                                                   ));

		$grid_search_form[ 'fields' ][ 'attribute_parent_id' ] = $form->getFieldHtml(array(
		                                                                                  'type' => 'selectbox',
		                                                                                  'name' => 'attribute_parent_id',
		                                                                                  'options' => $attributes,
		                                                                             ));
		$grid_search_form[ 'fields' ][ 'attribute_type_id' ] = $form->getFieldHtml(array(
		                                                                                'type' => 'selectbox',
		                                                                                'name' => 'attribute_type_id',
		                                                                                'options' => $attribute_types,
		                                                                           ));
		$grid_search_form[ 'fields' ][ 'status' ] = $form->getFieldHtml(array(
		                                                                     'type' => 'selectbox',
		                                                                     'name' => 'status',
		                                                                     'value' => '',
		                                                                     'options' => array(
			                                                                     '' => $this->language->get('text_select_status'),
			                                                                     1 => $this->language->get('text_enabled'),
			                                                                     0 => $this->language->get('text_disabled'),
		                                                                     ),
		                                                                ));

		$grid_settings[ 'search_form' ] = true;

		$grid_settings[ 'colNames' ] = array(
			$this->language->get('column_name'),
			$this->language->get('column_type'),
			$this->language->get('column_sort_order'),
			$this->language->get('column_status'),
		);
		$grid_settings[ 'colModel' ] = array(
			array(
				'name' => 'name',
				'index' => 'gad.name',
				'align' => 'left',
			),
			array(
				'name' => 'type',
				'index' => 'ga.attribute_type_id',
				'align' => 'center',
				'width' => 90,				
			),
			array(
				'name' => 'sort_order',
				'index' => 'ga.sort_order',
				'align' => 'center',
				'width' => 60,				
			),
			array(
				'name' => 'status',
				'index' => 'ga.status',
				'align' => 'center',
				'width' => 80,				
			),
		);

		if ( $this->config->get('config_show_tree_data') ) {
			$grid_settings[ 'expand_column' ] = "name";	
			$grid_settings[ 'multiaction_class' ] = 'hidden';	
		}

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign('search_form', $grid_search_form);

		$this->view->assign('insert', $this->html->getSecureURL('catalog/attribute/insert'));
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('global_attributes_listing'));

		$this->processTemplate('pages/catalog/attribute_list.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && $this->_validateForm()) {
			$attribute_id = $this->attribute_manager->addAttribute($this->request->post);
			$this->session->data[ 'success' ] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attribute_id));
		}
		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->view->assign('success', $this->session->data[ 'success' ]);
		if (isset($this->session->data[ 'success' ])) {
			unset($this->session->data[ 'success' ]);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && $this->_validateForm()) {
			$this->attribute_manager->updateAttribute($this->request->get[ 'attribute_id' ], $this->request->post);
			$this->session->data[ 'success' ] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $this->request->get[ 'attribute_id' ]));
		}

		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _getForm() {

		$this->data = array();
		$this->data[ 'error' ] = $this->error;
		$this->data[ 'cancel' ] = $this->html->getSecureURL('catalog/attribute');
		$this->data[ 'get_attribute_type' ] = $this->html->getSecureURL('r/catalog/attribute/get_attribute_type');

		$this->document->initBreadcrumb(array(
		                                     'href' => $this->html->getSecureURL('index/home'),
		                                     'text' => $this->language->get('text_home'),
		                                     'separator' => FALSE
		                                ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/attribute'),
		                                    'text' => $this->language->get('heading_title'),
		                                    'separator' => ' :: '
		                               ));

		$this->data[ 'elements_with_options' ] = HtmlElementFactory::getElementsWithOptions();


		if (isset($this->request->get[ 'attribute_id' ]) && ($this->request->server[ 'REQUEST_METHOD' ] != 'POST')) {
			$attribute_info = $this->attribute_manager->getAttribute(
				$this->request->get[ 'attribute_id' ],
				$this->session->data[ 'content_language_id' ]
			);
			//load values for attributes with options
			if (in_array($attribute_info[ 'element_type' ], $this->data[ 'elements_with_options' ])) {
				$values = $this->attribute_manager->getAttributeValues(
					$this->request->get[ 'attribute_id' ],
					$this->session->data[ 'content_language_id' ]
				);
				$attribute_info[ 'values' ] = array();
				foreach ($values as $v) {
					$attribute_info[ 'values' ][ ] = addslashes(html_entity_decode($v[ 'value' ], ENT_COMPAT, 'UTF-8'));
				}
			}
		}

		$fields = array( 'name',
		                 'attribute_parent_id',
		                 'attribute_group_id',
		                 'attribute_type_id',
		                 'element_type',
		                 'sort_order',
		                 'required',
		                 'status',
		                 'values' );
		foreach ($fields as $f) {
			if (isset($this->request->post[ $f ])) {
				$this->data[ $f ] = $this->request->post[ $f ];
			} elseif (isset($attribute_info)) {
				$this->data[ $f ] = $attribute_info[ $f ];
			} else {
				$this->data[ $f ] = '';
				if ($f == 'status') $this->data[ $f ] = 1;
			}
		}

		$results = HtmlElementFactory::getAvailableElements();
		$element_types = array( '' => $this->language->get('text_select') );
		foreach ($results as $key => $type) {
			// allowed field types
			if ( in_array($key,array('I','T','S','M','R','C','G','H')) ) {
				$element_types[$key] = $type['type'];
			}
		}

		$attribute_types = array( '' => $this->language->get('text_select') );
		$results = $this->attribute_manager->getAttributeTypes();
		foreach ($results as $type) {
			$attribute_types[ $type[ 'attribute_type_id' ] ] = $type[ 'type_name' ];
		}

		//NOTE: Future inplementation
		/*$attribute_groups = array( '' => $this->language->get('text_select'));
		$results = $this->attribute_manager->getAttributeGroups(array('language_id' => $this->session->data['content_language_id']));
		foreach ($results as $type) {
		    $attribute_groups[$type['attribute_group_id']] = $type['name'];
	    }*/

		$attributes = array( '' => $this->language->get('text_select') );
		$results = $this->attribute_manager->getAttributes(array(), 0, 0);
		foreach ($results as $type) {
			if (isset($this->request->get[ 'attribute_id' ]) && $this->request->get[ 'attribute_id' ] == $type[ 'attribute_id' ]) {
				continue;
			}
			$attributes[ $type[ 'attribute_id' ] ] = $type[ 'name' ];
		}

		if (!isset($this->request->get[ 'attribute_id' ])) {
			$this->data[ 'action' ] = $this->html->getSecureURL('catalog/attribute/insert');
			$this->data[ 'heading_title' ] = $this->language->get('text_insert') . $this->language->get('text_attribute');
			$this->data[ 'update' ] = '';
			$form = new AForm('ST');
		} else {
			$this->data[ 'action' ] = $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $this->request->get[ 'attribute_id' ]);
			$this->data[ 'heading_title' ] = $this->language->get('text_edit') . $this->language->get('text_attribute');
			$this->data[ 'update' ] = $this->html->getSecureURL('listing_grid/attribute/update_field', '&id=' . $this->request->get[ 'attribute_id' ]);
			$form = new AForm('HT');
		}

		$this->document->addBreadcrumb(array(
		                                    'href' => $this->data[ 'action' ],
		                                    'text' => $this->data[ 'heading_title' ],
		                                    'separator' => ' :: '
		                               ));

		$form->setForm(array(
		                    'form_name' => 'editFrm',
		                    'update' => $this->data[ 'update' ],
		               ));

		$this->data[ 'form' ][ 'id' ] = 'editFrm';
		$this->data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(array(
		                                                                'type' => 'form',
		                                                                'name' => 'editFrm',
		                                                                'attr' => 'confirm-exit="true"',
		                                                                'action' => $this->data[ 'action' ],
		                                                           ));
		$this->data[ 'form' ][ 'submit' ] = $form->getFieldHtml(array(
		                                                             'type' => 'button',
		                                                             'name' => 'submit',
		                                                             'text' => $this->language->get('button_save'),
		                                                             'style' => 'button1',
		                                                        ));
		$this->data[ 'form' ][ 'cancel' ] = $form->getFieldHtml(array(
		                                                             'type' => 'button',
		                                                             'name' => 'cancel',
		                                                             'text' => $this->language->get('button_cancel'),
		                                                             'style' => 'button2',
		                                                        ));

		$this->data[ 'form' ][ 'fields' ][ 'status' ] = $form->getFieldHtml(array(
		                                                                         'type' => 'checkbox',
		                                                                         'name' => 'status',
		                                                                         'value' => $this->data[ 'status' ],
		                                                                         'style' => 'btn_switch',
		                                                                    ));
		$this->data[ 'form' ][ 'fields' ][ 'name' ] = $form->getFieldHtml(array(
		                                                                       'type' => 'input',
		                                                                       'name' => 'name',
		                                                                       'value' => $this->data[ 'name' ],
		                                                                       'required' => true,
		                                                                       'style' => 'large-field',
		                                                                  ));
		$this->data[ 'form' ][ 'fields' ][ 'attribute_parent' ] = $form->getFieldHtml(array(
		                                                                                   'type' => 'selectbox',
		                                                                                   'name' => 'attribute_parent_id',
		                                                                                   'value' => $this->data[ 'attribute_parent_id' ],
		                                                                                   'options' => $attributes,
		                                                                              ));
		//NOTE: Future implementation
		/*$this->data['form']['fields']['attribute_group'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'attribute_group_id',
			'value' => $this->data['attribute_group_id'],
			'options' => $attribute_groups,
		));*/
		$this->data[ 'form' ][ 'fields' ][ 'attribute_type' ] = $form->getFieldHtml(array(
		                                                                                 'type' => 'selectbox',
		                                                                                 'name' => 'attribute_type_id',
		                                                                                 'value' => $this->data[ 'attribute_type_id' ],
		                                                                                 'required' => true,
		                                                                                 'options' => $attribute_types,
		                                                                            ));
		$this->data[ 'form' ][ 'fields' ][ 'element_type' ] = $form->getFieldHtml(array(
		                                                                               'type' => 'selectbox',
		                                                                               'name' => 'element_type',
		                                                                               'value' => $this->data[ 'element_type' ],
		                                                                               'required' => true,
		                                                                               'options' => $element_types,
		                                                                          ));
		$this->data[ 'form' ][ 'fields' ][ 'sort_order' ] = $form->getFieldHtml(array(
		                                                                             'type' => 'input',
		                                                                             'name' => 'sort_order',
		                                                                             'value' => $this->data[ 'sort_order' ],
		                                                                             'style' => 'small-field'
		                                                                        ));
		$this->data[ 'form' ][ 'fields' ][ 'required' ] = $form->getFieldHtml(array(
		                                                                           'type' => 'checkbox',
		                                                                           'name' => 'required',
		                                                                           'value' => $this->data[ 'required' ],
		                                                                      ));


		//Build atribute values part of the form
		if ( $this->request->get['attribute_id'] ) {
			
			$this->data['child_count'] = $this->attribute_manager->totalChildren( $this->request->get['attribute_id'] );
			if ( $this->data['child_count'] > 0) {
				$children_attr = $this->attribute_manager->getAttributes(array(), 0, $this->request->get['attribute_id']);
				foreach ($children_attr as $attr) {
					$this->data['children'][] = array( 'name' => $attr['name'], 
												 'link' => $this->html->getSecureURL('catalog/attribute/update', '&attribute_id=' . $attr['attribute_id']) );
				}
			}
			
			$attribute_values = $this->attribute_manager->getAttributeValues( $this->request->get[ 'attribute_id' ] );
			foreach ($attribute_values as $atr_val) {
				$atr_val_id = $atr_val['attribute_value_id'];
				$attributes_fields[$atr_val_id]['sort_order'] = $form->getFieldHtml(array(
															'type' => 'input',
															'name' => 'sort_orders['.$atr_val_id.']',
															'value' => $atr_val['sort_order'],
															'style' => 'small-field'
														));
				$attributes_fields[$atr_val_id]['values'] = $form->getFieldHtml(array(
															'type' => 'input',
				                                            'name' => 'values['.$atr_val_id.']',
				                                            'value' => $atr_val['value'],
				                                            'style' => 'medium-field'
				                                        ));				
				$attributes_fields[$atr_val_id]['attribute_value_ids'] = $form->getFieldHtml(array(
															'type' => 'hidden',
				                                            'name' => 'attribute_value_ids['.$atr_val_id.']',
				                                            'value' => $atr_val_id,
				                                            'style' => 'medium-field'
				                                        ));				
			}	
		}
		if ( !$attributes_fields ) {
				$attributes_fields[0]['sort_order'] = $form->getFieldHtml(array(
															'type' => 'input',
															'name' => 'sort_orders[]',
															'value' => '',
															'style' => 'small-field no-save'
														));
				$attributes_fields[0]['values'] = $form->getFieldHtml(array(
															'type' => 'input',
				                                            'name' => 'values[]',
				                                            'value' => '',
				                                            'style' => 'medium-field no-save'
				                                        ));						
				$attributes_fields[0]['attribute_value_ids'] = $form->getFieldHtml(array(
															'type' => 'hidden',
				                                            'name' => 'attribute_value_ids['.$atr_val_id.']',
				                                            'value' => 'new',
				                                            'style' => 'medium-field'
				                                        ));				
		}
		$this->data['form']['fields']['attribute_values'] = $attributes_fields;

		$this->view->batchAssign($this->data);
		$this->view->assign('insert', $this->html->getSecureURL('catalog/attribute/insert'));
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $this->session->data[ 'content_language_id' ]);
		$this->view->assign('text_parent_note', $this->language->get('text_parent_note'));
		$this->view->assign('help_url', $this->gen_help_url('global_attributes_edit'));
		$this->processTemplate('pages/catalog/attribute_form.tpl');
	}

	private function _validateForm() {
		if (!$this->user->canModify('catalog/attribute')) {
			$this->error[ 'warning' ] = $this->language->get('error_permission');
		}

		if ((strlen(utf8_decode($this->request->post[ 'name' ])) < 2) || (strlen(utf8_decode($this->request->post[ 'name' ])) > 32)) {
			$this->error[ 'name' ] = $this->language->get('error_name');
		}

		if (empty($this->request->post[ 'attribute_type_id' ])) {
			$this->error[ 'attribute_type' ] = $this->language->get('error_required');
		}
		if (empty($this->request->post[ 'element_type' ])) {
			$this->error[ 'element_type' ] = $this->language->get('error_required');
		}

		if (!isset($this->request->post[ 'required' ])) {
			$this->request->post[ 'required' ] = 0;
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}

?>
