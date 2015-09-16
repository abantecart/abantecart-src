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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

class ControllerPagesDesignContent extends AController {
	public $error = array();
	public $data = array();

	/**
	 * @var AContentManager
	 */
	private $acm;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('design/content'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current' => true
		));

		$grid_settings = array(
				'table_id' => 'content_grid',
				'url' => $this->html->getSecureURL('listing_grid/content'),
				'editurl' => $this->html->getSecureURL('listing_grid/content/update'),
				'update_field' => $this->html->getSecureURL('listing_grid/content/update_field'),
				'sortname' => 'sort_order',
				'sortorder' => 'asc',
				'drag_sort_column' => 'sort_order',
				'columns_search' => true,
				'actions' => array(
						'edit' => array(
								'text' => $this->language->get('text_edit'),
								'href' => $this->html->getSecureURL('design/content/update', '&content_id=%ID%')
						),
						'delete' => array(
								'text' => $this->language->get('button_delete')
						),
						'save' => array(
								'text' => $this->language->get('button_save')
						),
				),
		);

		$grid_settings['colNames'] = array(
				$this->language->get('column_title'),
				$this->language->get('column_parent'),
				$this->language->get('column_status'),
				$this->language->get('column_sort_order'),
		);
		$grid_settings['colModel'] = array(
				array(
						'name' => 'title',
						'index' => 'id.title',
						'width' => 250,
						'align' => 'left',
				),
				array(
						'name' => 'parent_name',
						'index' => 'parent_name',
						'width' => 100,
						'align' => 'center',
						'search' => false,
				),
				array(
						'name' => 'status',
						'index' => 'status',
						'width' => 100,
						'align' => 'center',
						'search' => false,
				),
				array(
						'name' => 'sort_order',
						'index' => 'sort_order',
						'width' => 100,
						'align' => 'center',
						'search' => false,
				));
		if ($this->config->get('config_show_tree_data')) {
			$grid_settings['expand_column'] = 'title';
			$grid_settings['multiaction_class'] = 'hidden';
		}


		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->document->setTitle($this->language->get('heading_title'));
		$this->view->assign('insert', $this->html->getSecureURL('design/content/insert'));
		$this->view->assign('help_url', $this->gen_help_url('content_listing'));

		$this->processTemplate('pages/design/content_list.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));
		$this->acm = new AContentManager();
		if ($this->request->is_POST() && $this->_validateForm()) {
			$savedata = $this->request->post;
			unset($savedata['parent_content_id'], $savedata['sort_order']);
			$content_ids = (array)$this->request->post['parent_content_id'];
			foreach ($content_ids as $par_id) {
				list($tmp, $parent_id) = explode('_', $par_id);
				$savedata['parent_content_id'][] = (int)$parent_id;
				$savedata['sort_order'][] = (int)$this->request->post['sort_order'][$par_id];
			}

			$content_id = $this->acm->addContent($savedata);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('design/content/update', '&content_id=' . $content_id));
		}

		// content language switcher
		$languages = $this->language->getActiveLanguages();
		if (sizeof($languages) > 1) {

			$this->view->assign('languages', $languages);
			$this->view->assign('language_code', $this->session->data['content_language']); //selected in selectbox
			$get = $this->request->get;
			foreach ($get as $name => $value) {
				if ($name == 'content_language_code') continue;
				$hiddens[$name] = $value;
			}
			$this->view->assign('lang_action', $this->html->getSecureURL('design/content/update'));
			$this->view->assign('hiddens', $hiddens);
		}
		$this->_initTabs('form');
		$this->_getForm($content_id);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('update_title'));

		$this->acm = new AContentManager();
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());

		$content_id = $this->_get_content_id($this->request->get['content_id']);

		if ($this->request->is_POST() && $this->_validateForm()) {
			$savedata = $this->request->post;
			unset($savedata['parent_content_id'], $savedata['sort_order']);
			//process parents IDs
			$parents_ids = (array)$this->request->post['parent_content_id'];
			//build an array for each parent id
			if (count($parents_ids) == 0) {
				//set top parent by default
				$parents_ids[] = '0_0';
			}
			foreach ($parents_ids as $par_id) {
				list($tmp, $parent_id) = explode('_', $par_id);
				$savedata['parent_content_id'][] = (int)$parent_id;
				$savedata['sort_order'][$parent_id] = (int)$this->request->post['sort_order'][$par_id];
			}
			$this->acm->editContent($content_id, $savedata);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->html->redirect($this->html->getSecureURL('design/content/update', '&content_id=' . $content_id));
		}
		$this->_initTabs('form');
		$this->view->assign('content_id', $content_id);
		$this->view->assign('insert', $this->html->getSecureURL('design/content/insert', '&parent_content_id='.$content_id));
		
		$this->_getForm($content_id);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}


	private function _initTabs($active = null) {

		$content_id = $this->_get_content_id($this->request->get['content_id']);

		if(!$content_id){ return null; } //no need tabs for new content

		$this->data['tabs'] = array();
		$this->data['tabs']['form'] = array(
				'href' => $this->html->getSecureURL('design/content/' . ($content_id ? 'update' : 'insert'), '&content_id=' . $content_id),
				'text' => $this->language->get('tab_form'));

		$this->data['tabs']['layout'] = array(
				'href' => $this->html->getSecureURL('design/content/edit_layout', '&content_id=' . $content_id),
				'text' => $this->language->get('tab_layout'));


		if (in_array($active, array_keys($this->data['tabs']))) {
			$this->data['tabs'][$active]['active'] = 1;
		} else {
			$this->data['tabs']['form']['active'] = 1;
		}
	}

	private function _getForm($content_id) {

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->data['error'] = $this->error;
		$this->data['language_id'] = $this->config->get('storefront_language_id');

		if (has_value($content_id) && $this->request->is_GET()) {
			$content_info = $this->acm->getContent($content_id);
		}

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('design/content'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '
		));

		if (has_value($content_id)) {
			$this->document->addBreadcrumb(array(
					'href' => $this->html->getSecureURL('design/content/update', '&content_id=' . $content_id),
					'text' => $this->language->get('update_title') . ' - ' . $content_info['title'],
					'separator' => ' :: ',
					'current' => true
			));
		} else {
			$this->document->addBreadcrumb(array(
					'href' => $this->html->getSecureURL('design/content/insert'),
					'text' => $this->language->get('insert_title'),
					'separator' => ' :: ',
					'current' => true
			));
		}

		$this->data['cancel'] = $this->html->getSecureURL('design/content');

		$allowedFields = array('status', 'description', 'title', 'content', 'parent_content_id', 'sort_order', 'store_id', 'keyword');

		foreach ($allowedFields as $field) {
			if (isset($this->request->post[$field])) {
				$this->data[$field] = $this->request->post[$field];
			} elseif (isset($content_info)) {
				$this->data[$field] = $content_info[$field];
			} else {
				$this->data[$field] = '';
			}
		}
		//if get parent_id, we create new content for parent 
		if ($this->request->get['parent_content_id']) {
			$this->data['parent_content_id'][] = $this->request->get['parent_content_id'];
		}

		if (!has_value($content_id)) {
			$this->data['action'] = $this->html->getSecureURL('design/content/insert');
			$this->data['form_title'] = $this->language->get('insert_title');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('design/content/update', '&content_id=' . $content_id);
			$this->data['form_title'] = $this->language->get('update_title');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/content/update_field', '&id=' . $content_id);
			$form = new AForm('HS');
		}

		$form->setForm(array(
				'form_name' => 'contentFrm',
				'update' => $this->data['update'],
		));

		$this->data['form']['id'] = 'contentFrm';
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'contentFrm',
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

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
				'type' => 'checkbox',
				'name' => 'status',
				'value' => $this->data['status'],
				'style' => 'btn_switch',
		));

		// we need get contents list for multiselect
		$multiSelect = $this->acm->getContentsForSelect(false);

		$selected_parents = array();
		$this->data['parent_content_id'] = (array)$this->data['parent_content_id'];
		foreach ($this->data['parent_content_id'] as $parent_id) {
			//check if we have combined ID
			if (preg_match('/\d+_\d+/', $parent_id)) {
				list($void, $parent_id) = explode('_', $parent_id);
			}
			foreach ($multiSelect as $option_id => $option_value) {
				list($void, $p_content_id) = explode('_', $option_id);
				if ($parent_id == $p_content_id) {
					$selected_parents[$option_id] = $option_id;
				}
				if ($p_content_id == $content_id) {
					$disabled_parents[$option_id] = $option_id;
				}
			}
		}
		if (!$selected_parents) {
			$selected_parents = array('0_0' => '0_0');
		}

		$this->data['form']['fields']['parent'] = $form->getFieldHtml(array(
				'type' => 'multiSelectbox',
				'name' => 'parent_content_id[]',
				'options' => $multiSelect,
				'value' => $selected_parents,
				'disabled' => $disabled_parents,
				'attr' => 'size = "' . (sizeof($multiSelect) > 10 ? 10 : sizeof($multiSelect)) . '"'
		));

		$this->data['form']['fields']['title'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'title',
				'value' => $this->data['title'],
				'required' => true,
				'multilingual' => true,
		));
		$this->data['form']['fields']['description'] = $form->getFieldHtml(array(
				'type' => 'textarea',
				'name' => 'description',
				'value' => $this->data['description'],
				'multilingual' => true,
		));

		$this->data['form']['fields']['content'] = $form->getFieldHtml(array(
				'type' => 'textarea',
				'name' => 'content',
				'value' => $this->data['content'],
				'required' => true,
				'multilingual' => true,
		));

		$this->data['keyword_button'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'generate_seo_keyword',
				'text' => $this->language->get('button_generate'),
				'style' => 'btn btn-info'
		));

		$this->data['generate_seo_url'] = $this->html->getSecureURL('common/common/getseokeyword', '&object_key_name=content_id&id=' . $content_id);

		$this->data['form']['fields']['keyword'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'keyword',
				'value' => $this->data['keyword'],
				'style' => 'large-field',
				'multilingual' => true,
				'help_url' => $this->gen_help_url('seo_keyword')
		));


		// get array with stores looks like array (store_id=>array(content_id=>store_name))
		$store_values = $store_selected = array();
		$store_values[0] = $this->language->get('text_default');

		$stores = $this->acm->getContentStores($content_id);

		foreach ($stores as $store_id => $store) {
			$store_values[$store_id] = trim(current($store));
			if (isset($store[$content_id])) {
				$store_selected[$store_id] = $store_id;
			}
		}

		if(!$store_selected){
			$store_selected[0] = 0;
		}

		$this->data['form']['fields']['store'] = $form->getFieldHtml(array(
				'type' => 'checkboxgroup',
				'name' => 'store_id[]',
				'value' => $store_selected,
				'options' => $store_values,
				'scrollbox' => true,
				'style' => 'chosen'
		));

		$this->data['form']['fields']['sort_order'] = array();
		foreach ($selected_parents as $option_id) {
			list($void, $parent_id) = explode('_', $option_id);
			$this->data['form']['fields']['sort_order'][$option_id] = array(
					'label' => $multiSelect[$option_id],

					'field' => $form->getFieldHtml(array(
									'type' => 'input',
									'name' => 'sort_order[' . $option_id . ']',
									'value' => $this->data['sort_order'][$parent_id],
									'style' => 'tiny-field',
							)));
		}

		$this->view->assign('help_url', $this->gen_help_url('content_edit'));

		$resources_scripts = $this->dispatch(
				'responses/common/resource_library/get_resources_scripts',
				array(
						'object_name' => 'contents',
						'object_id' => $p_content_id_id,
						'types' => array('image'),
				)
		);
		$this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
		$this->data['rl'] = $this->html->getSecureURL('common/resource_library', '&action=list_library&object_name=&object_id&type=image&mode=single');

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/design/content_form.tpl');
	}

	private function _validateForm() {
		if (!$this->user->canModify('design/content')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (mb_strlen($this->request->post['title']) < 2 || mb_strlen($this->request->post['title']) > 64) {
			$this->error['title'] = $this->language->get('error_title');
		}

		if (mb_strlen($this->request->post['content']) < 2) {
			$this->error['content'] = $this->language->get('error_content');
		}
		if (($error_text = $this->html->isSEOkeywordExists('content_id=' . $this->request->get['content_id'], $this->request->post['keyword']))) {
			$this->error['keyword'] = $error_text;
		}

		$this->extensions->hk_ValidateData($this);
		if (!$this->error) {
			return TRUE;
		} else {
			if (!isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_required_data');
			}
			return FALSE;
		}
	}


	public function edit_layout() {
		$page_controller = 'pages/content/content';
		$page_key_param = 'content_id';

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('design/layout');
		$this->document->setTitle($this->language->get('update_title'));
		$this->acm = new AContentManager();

		$content_id = $this->_get_content_id($this->request->get['content_id']);
		if (!has_value($content_id)) {
			$this->redirect($this->html->getSecureURL('design/content'));
		}

		$page_url = $this->html->getSecureURL('design/content/edit_layout', '&content_id=' . $content_id);

	    // Alert messages
	    if (isset($this->session->data['warning'])) {
	      $this->data['error_warning'] = $this->session->data['warning'];
	      unset($this->session->data['warning']);
	    }
	    if (isset($this->session->data['success'])) {
	      $this->data['success'] = $this->session->data['success'];
	      unset($this->session->data['success']);
	    }

		$this->data['help_url'] = $this->gen_help_url('content_layout');

		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('design/content'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('design/content/update', '&content_id=' . $content_id),
				'text' => $this->language->get('update_title'),
				'separator' => ' :: '
		));
		$content_info = $this->acm->getContent($content_id); 
		$this->document->addBreadcrumb(array(
				'href' => $page_url,
				'text' => $this->language->get('tab_layout') . ' - ' . $content_info['title'],
				'current' =>  true
		));

		$this->_initTabs('layout');

		$layout = new ALayoutManager();
		//get existing page layout or generic
		$page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $content_id);
		$page_id = $page_layout['page_id'];
		$layout_id = $page_layout['layout_id'];
		if (isset($this->request->get['tmpl_id'])) {
			$tmpl_id = $this->request->get['tmpl_id'];
		} else {
			$tmpl_id = $this->config->get('config_storefront_template');
		}			
	    $params = array(
	      'content_id' => $content_id,
	      'page_id' => $page_id,
	      'layout_id' => $layout_id,
	      'tmpl_id' => $tmpl_id,
	    );	
	    $url = '&'.$this->html->buildURI($params);

		// get templates
		$this->data['templates'] = array();
		$directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
		foreach ($directories as $directory) {
		  $this->data['templates'][] = basename($directory);
		}
		$enabled_templates = $this->extensions->getExtensionsList(array(
		  'filter' => 'template',
		  'status' => 1,
		));
		foreach ($enabled_templates->rows as $template) {
		  $this->data['templates'][] = $template['key'];
		}

		$action = $this->html->getSecureURL('design/content/save_layout');
	    // Layout form data
	    $form = new AForm('HT');
	    $form->setForm(array(
	      'form_name' => 'layout_form',
	    ));
	
	    $this->data['form_begin'] = $form->getFieldHtml(array(
	      'type' => 'form',
	      'name' => 'layout_form',
	      'attr' => 'data-confirm-exit="true"',
	      'action' => $action
	    ));
	
	    $this->data['hidden_fields'] = '';
	    foreach ($params as $name => $value) {
	      $this->data[$name] = $value;
	      $this->data['hidden_fields'] .= $form->getFieldHtml(array(
	        'type' => 'hidden',
	        'name' => $name,
	        'value' => $value
	      ));
	    }
	
	    $this->data['page_url'] = $page_url;
	    $this->data['current_url'] = $this->html->getSecureURL('design/content/edit_layout', $url);
	
		// insert external form of layout
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);

	    $layoutform = $this->dispatch('common/page_layout', array($layout));
	    $this->data['layoutform'] = $layoutform->dispatchGetOutput();
		
		//build pages and available layouts for clonning
		$this->data['pages'] = $layout->getAllPages();
		$av_layouts = array( "0" => $this->language->get('text_select_copy_layout'));
		foreach($this->data['pages'] as $page){
			if ( $page['layout_id'] != $layout_id ) {
				$av_layouts[$page['layout_id']] = $page['layout_name'];
			}
		}

		$form = new AForm('HT');
		$form->setForm(array(
		    'form_name' => 'cp_layout_frm',
	    ));
	    
		$this->data['cp_layout_select'] = $form->getFieldHtml(array('type' => 'selectbox',
													'name' => 'layout_change',
													'value' => '',
													'options' => $av_layouts ));

		$this->data['cp_layout_frm'] = $form->getFieldHtml(array('type' => 'form',
		                                        'name' => 'cp_layout_frm',
		                                        'attr' => 'class="aform form-inline"',
			                                    'action' => $action));

		$this->view->assign('heading_title', $this->language->get('heading_title'));

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/design/content_layout.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}

	public function save_layout() {
		$page_controller = 'pages/content/content';
		$page_key_param = 'content_id';
		$content_id = $this->_get_content_id($this->request->get_or_post('content_id'));
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->acm = new AContentManager();
		if (!has_value($content_id)) {
			$this->redirect($this->html->getSecureURL('design/content'));
		}

		if ($this->request->is_POST()) {

			// need to know unique page existing
			$post_data = $this->request->post;
			$tmpl_id = $post_data['tmpl_id'];
			$layout = new ALayoutManager();
			
			$pages = $layout->getPages($page_controller, $page_key_param, $content_id);
			if (count($pages)) {
				$page_id = $pages[0]['page_id'];
				$layout_id = $pages[0]['layout_id'];
			} else {
				// create new page record
				$page_info = array('controller' => $page_controller,
						'key_param' => $page_key_param,
						'key_value' => $content_id);

				$default_language_id = $this->language->getDefaultLanguageID();
				$content_info = $this->acm->getContent($content_id, $default_language_id);
				if ($content_info) {
					if ($content_info['title']) {
						$page_info['page_descriptions'][$default_language_id]['name'] = $content_info['title'];
					} else {
						$page_info['page_descriptions'][$default_language_id]['name'] = 'Unnamed content page';
					}
				}
				$page_id = $layout->savePage($page_info);
				$layout_id = '';
				// need to generate layout name
				$post_data['layout_name'] = 'Content: ' . $content_info['title'];
			}

			//create new instance with specific template/page/layout data
			$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
			if (has_value($post_data['layout_change'])) {	
				//update layout request. Clone source layout
				$layout->clonePageLayout($post_data['layout_change'], $layout_id, $post_data['layout_name']);
				$this->session->data[ 'success' ] = $this->language->get('text_success_layout');
			} else {
				//save new layout
	      		$layout_data = $layout->prepareInput($post_data);
	      		if ($layout_data) {
	      			$layout->savePageLayout($layout_data);
	      			$this->session->data[ 'success' ] = $this->language->get('text_success_layout');
	      		} 
			}

			$this->redirect($this->html->getSecureURL('design/content/edit_layout', '&content_id=' . $content_id));
		}
		$this->redirect($this->html->getSecureURL('design/content/'));
	}
	
	private function _get_content_id( $input ) {
		//select $content_id from parent/child type or straight
		$content_id = '';
		if (is_int(strpos($input, '_'))) {
			list($void, $content_id) = explode('_', $input);
		} else {
			$content_id = $input;
		}
		return $content_id;
	}
		
}
