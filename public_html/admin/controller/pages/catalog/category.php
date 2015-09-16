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

/**
 * Class ControllerPagesCatalogCategory
 */
class ControllerPagesCatalogCategory extends AController {
	private $error = array();
	public $data = array();
	private $fields = array('category_description', 'status', 'parent_id', 'category_store', 'keyword', 'sort_order');

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));
		$this->view->assign('help_url', $this->gen_help_url('category_listing'));

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('catalog/category'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current' => true
		));

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$grid_settings = array(
				'table_id' => 'category_grid',
				'url' => $this->html->getSecureURL('listing_grid/category'),
				'editurl' => $this->html->getSecureURL('listing_grid/category/update'),
				'update_field' => $this->html->getSecureURL('listing_grid/category/update_field'),
				'sortname' => 'sort_order',
				'sortorder' => 'asc',
				'drag_sort_column' => 'sort_order',
				'actions' => array(
						'edit' => array(
								'text' => $this->language->get('text_edit'),
								'href' => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%'),
								'children' => array_merge(array(
							                'general' => array(
										                'text' => $this->language->get('tab_general'),
										                'href' => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%'),
						                                ),
							                'data' => array(
										                'text' => $this->language->get('tab_data'),
										                'href' => $this->html->getSecureURL('catalog/category/update', '&category_id=%ID%').'#data',
						                                ),
							                'layout' => array(
										                'text' => $this->language->get('tab_layout'),
										                'href' => $this->html->getSecureURL('catalog/category/edit_layout', '&category_id=%ID%'),
						                                ),
								),(array)$this->data['grid_edit_expand'])

						),
						'save' => array(
								'text' => $this->language->get('button_save'),
						),
						'delete' => array(
								'text' => $this->language->get('button_delete'),
						)
				),
				'grid_ready' => 'grid_ready(data);'
		);

		$grid_settings['colNames'] = array(
				'',
				$this->language->get('column_name'),
				$this->language->get('column_sort_order'),
				$this->language->get('column_status'),
				$this->language->get('column_products'),
				$this->language->get('column_subcategories'),
		);
		$grid_settings['colModel'] = array(
				array(
						'name' => 'image',
						'index' => 'image',
						'align' => 'center',
						'width' => 70,
						'sortable' => false,
						'search' => false,
				),
				array(
						'name' => 'name',
						'index' => 'name',
						'width' => 310,
						'align' => 'left',
				),
				array(
						'name' => 'sort_order',
						'index' => 'sort_order',
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
						'name' => 'products',
						'index' => 'products',
						'width' => 100,
						'align' => 'center',
						'search' => false,
						'sortable' => false,
				),
				array(
						'name' => 'subcategories',
						'index' => 'subcategories',
						'width' => 140,
						'align' => 'center',
						'search' => false,
						'sortable' => false,
				),
		);
		if ($this->config->get('config_show_tree_data')) {
			$grid_settings['expand_column'] = "name";
			$grid_settings['multiaction_class'] = 'hidden';
		}

		$results = $this->model_catalog_category->getCategories(0);
		$parents = array(0 => $this->language->get('text_select_parent'));
		foreach ($results as $c) {
			$parents[$c['category_id']] = $c['name'];
		}

		$form = new AForm();
		$form->setForm(array(
				'form_name' => 'category_grid_search',
		));

		$grid_search_form = array();
		$grid_search_form['id'] = 'category_grid_search';
		$grid_search_form['form_open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'category_grid_search',
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
		$grid_search_form['fields']['parent_id'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'parent_id',
				'options' => $parents,
				'style' => 'chosen',
				'placeholder' => $this->language->get('text_select_parent')
		));

		$grid_settings['search_form'] = true;

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		if( $this->config->get('config_embed_status') ){
			$this->view->assign('embed_url', $this->html->getSecureURL('common/do_embed/categories'));
		}

		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign('search_form', $grid_search_form);
		$this->view->assign('grid_url', $this->html->getSecureURL('listing_grid/category'));

		$this->document->setTitle($this->language->get('heading_title'));
		$this->view->assign('insert', $this->html->getSecureURL('catalog/category/insert'));
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

		$this->processTemplate('pages/catalog/category_list.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}

	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		if ($this->request->is_POST() && $this->_validateForm()) {

			$languages = $this->language->getAvailableLanguages();
			$content_language_id = $this->language->getContentLanguageID();

			foreach ($languages as $l) {
				if ($l['language_id'] == $content_language_id) continue;
				$this->request->post['category_description'][$l['language_id']] = $this->request->post['category_description'][$content_language_id];
			}

			$category_id = $this->model_catalog_category->addCategory($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/category/update', '&category_id=' . $category_id));
		}
		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));
		$this->view->assign('help_url', $this->gen_help_url('category_edit'));

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		$this->view->assign('insert', $this->html->getSecureURL('catalog/category/insert', '&parent_id='.$this->request->get['category_id']));
		
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		if ($this->request->is_POST() && $this->_validateForm()) {
			$this->model_catalog_category->editCategory($this->request->get['category_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/category/update', '&category_id=' . $this->request->get['category_id']));
		}
		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _getForm() {

		$content_language_id = $this->language->getContentLanguageID();

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('error_name', $this->error['name']);
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

		$categories = array(0 => $this->language->get('text_none'));
		foreach ($this->data['categories'] as $c) {
			$categories[$c['category_id']] = $c['name'];
		}

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
			$this->data['category_id'] = $category_id;
			unset($categories[$category_id]);
		}

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('catalog/category'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '
		));

		$this->view->assign('cancel', $this->html->getSecureURL('catalog/category'));

		if ($category_id && $this->request->is_GET()) {
			$category_info = $this->model_catalog_category->getCategory($category_id);
		}

		foreach ($this->fields as $f) {
			if (isset ($this->request->post [$f])) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($category_info) && isset($category_info[$f])) {
				$this->data[$f] = $category_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		if (isset($this->request->post['category_description'])) {
			$this->data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($category_info)) {
			$this->data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
		} else {
			$this->data['category_description'] = array();
		}

		if ($this->data['status'] == '') {
			$this->data['status'] = 1;
		}
		if ($this->request->is_GET() && has_value($this->request->get['parent_id'])) {
			$this->data['parent_id'] =  $this->request->get['parent_id'];
		}
		if ($this->data['parent_id'] == '') {
			$this->data['parent_id'] = 0;
		}

		$this->loadModel('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();
		if (isset($this->request->post['category_store'])) {
			$this->data['category_store'] = $this->request->post['category_store'];
		} elseif (isset($category_info)) {
			$this->data['category_store'] = $this->model_catalog_category->getCategoryStores($category_id);
		} else {
			$this->data['category_store'] = array(0);
		}

		$stores = array(0 => $this->language->get('text_default'));
		foreach ($this->data['stores'] as $s) {
			$stores[$s['store_id']] = $s['name'];
		}

		if (!$category_id) {
			$this->data['action'] = $this->html->getSecureURL('catalog/category/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . ' ' . $this->language->get('text_category');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/category/update', '&category_id=' . $category_id);
			$this->data['heading_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('text_category') . ' - ' . $this->data['category_description'][$content_language_id]['name'];
			$this->data['update'] = $this->html->getSecureURL('listing_grid/category/update_field', '&id=' . $category_id);
			$form = new AForm('HS');
		}

		$this->document->addBreadcrumb(
				array('href' => $this->data['action'],
						'text' => $this->data['heading_title'],
						'separator' => ' :: ',
						'current' => true
				));

		$form->setForm(
				array('form_name' => 'editFrm',
						'update' => $this->data['update'],
				));

		$this->data['form']['id'] = 'editFrm';
		$this->data['form']['form_open'] = $form->getFieldHtml(
				array('type' => 'form',
						'name' => 'editFrm',
						'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
						'action' => $this->data['action'],
				));
		$this->data['form']['submit'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'submit',
						'text' => $this->language->get('button_save'),
						'style' => 'button1',
				));
		$this->data['form']['cancel'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'cancel',
						'text' => $this->language->get('button_cancel'),
						'style' => 'button2',
				));

		$this->data['form']['fields']['general']['status'] = $form->getFieldHtml(
				array('type' => 'checkbox',
						'name' => 'status',
						'value' => $this->data['status'],
						'style' => 'btn_switch',
				));
		$this->data['form']['fields']['general']['parent_category'] = $form->getFieldHtml(
				array('type' => 'selectbox',
						'name' => 'parent_id',
						'value' => $this->data['parent_id'],
						'options' => $categories,
						'style' => 'chosen'
				));
		$this->data['form']['fields']['general']['name'] = $form->getFieldHtml(
				array('type' => 'input',
						'name' => 'category_description[' . $content_language_id . '][name]',
						'value' => $this->data['category_description'][$content_language_id]['name'],
						'required' => true,
						'style' => 'large-field',
						'attr' => ' maxlength="255" ',
						'multilingual' => true,
				));
		$this->data['form']['fields']['general']['description'] = $form->getFieldHtml(
				array('type' => 'textarea',
						'name' => 'category_description[' . $content_language_id . '][description]',
						'value' => $this->data['category_description'][$content_language_id]['description'],
						'style' => 'xl-field',
						'multilingual' => true,	
				));
		$this->data['form']['fields']['data']['meta_keywords'] = $form->getFieldHtml(
				array('type' => 'textarea',
						'name' => 'category_description[' . $content_language_id . '][meta_keywords]',
						'value' => $this->data['category_description'][$content_language_id]['meta_keywords'],
						'style' => 'xl-field',
						'multilingual' => true,
				));
		$this->data['form']['fields']['data']['meta_description'] = $form->getFieldHtml(
				array('type' => 'textarea',
						'name' => 'category_description[' . $content_language_id . '][meta_description]',
						'value' => $this->data['category_description'][$content_language_id]['meta_description'],
						'style' => 'xl-field',
						'multilingual' => true,
				));

		$this->data['keyword_button'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'generate_seo_keyword',
				'text' => $this->language->get('button_generate'),
			//set button not to submit a form
				'attr' => 'type="button"',
				'style' => 'btn btn-info'
		));
		$this->data['generate_seo_url'] = $this->html->getSecureURL('common/common/getseokeyword', '&object_key_name=category_id&id=' . $category_id);

		$this->data['form']['fields']['data']['keyword'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'keyword',
				'value' => $this->data['keyword'],
				'help_url' => $this->gen_help_url('seo_keyword'),
				'multilingual' => true,
				'attr' => ' gen-value="' . SEOEncode($this->data['category_description']['name']) . '" '
		));

		$this->data['form']['fields']['data']['store'] = $form->getFieldHtml(
				array('type' => 'checkboxgroup',
						'name' => 'category_store[]',
						'value' => $this->data['category_store'],
						'options' => $stores,
						'style' => 'chosen',
				));

		$this->data['form']['fields']['data']['sort_order'] = $form->getFieldHtml(
				array('type' => 'input',
						'name' => 'sort_order',
						'value' => $this->data['sort_order'],
						'style' => 'small-field'
				));

		$this->data['active'] = 'general';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/category_tabs', array($this->data));
		$this->data['category_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		if( $category_id && $this->config->get('config_embed_status')){
			$this->data['embed_url'] = $this->html->getSecureURL( 'common/do_embed/categories', '&category_id='.$category_id );
		}


		$this->view->batchAssign($this->data);
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $content_language_id);
		$this->view->assign('language_code', $this->session->data['language']);

		$this->addChild('responses/common/resource_library/get_resources_html', 'resources_html', 'responses/common/resource_library_scripts.tpl');
		$resources_scripts = $this->dispatch(
				'responses/common/resource_library/get_resources_scripts',
				array(
						'object_name' => 'categories',
						'object_id' => $category_id,
						'types' => array('image'),
				)
		);
		$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
		$this->view->assign('rl', $this->html->getSecureURL('common/resource_library', '&action=list_library&object_name=&object_id&type=image&mode=single'));

		$this->view->assign('current_url', $this->html->currentURL());

		$this->processTemplate('pages/catalog/category_form.tpl');
	}

	private function _validateForm() {

		if (!$this->user->canModify('catalog/category')) {
			$this->error['warning'][] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['category_description'] as $language_id => $value) {
			$len = mb_strlen($value['name']);
			if (($len < 2) || ($len > 255)) {
				$this->error['warning'][] = $this->language->get('error_name');
			}
		}
		if (($error_text = $this->html->isSEOkeywordExists('category_id=' . $this->request->get['category_id'], $this->request->post['keyword']))) {
			$this->error['warning'][] = $error_text;
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			if (!isset($this->error['warning'])) {
				$this->error['warning'][] = $this->language->get('error_required_data');
			}
			$this->error['warning'] = implode('<br>', $this->error['warning']);
			return FALSE;
		}
	}


	public function edit_layout() {
		$page_controller = 'pages/product/category';
		$page_key_param = 'path';
		$category_id = (int)$this->request->get['category_id'];
		$this->data['category_id'] = $category_id;
		$page_url = $this->html->getSecureURL('catalog/category/edit_layout', '&category_id=' . $category_id);
		//note: category can not be ID of 0.
		if (!has_value($category_id)) {
			$this->redirect($this->html->getSecureURL('catalog/category'));
		}

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('catalog/category');
		$this->loadLanguage('design/layout');
		$this->data['help_url'] = $this->gen_help_url('layout_edit');

		if (has_value($category_id) && $this->request->is_GET()) {
			$this->loadModel('catalog/category');
			$this->data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
		}

	    // Alert messages
	    if (isset($this->session->data['warning'])) {
	      $this->data['error_warning'] = $this->session->data['warning'];
	      unset($this->session->data['warning']);
	    }
	    if (isset($this->session->data['success'])) {
	      $this->data['success'] = $this->session->data['success'];
	      unset($this->session->data['success']);
	    }

		$this->data['heading_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('text_category') . ' - ' . $this->data['category_description'][$this->language->getContentLanguageID()]['name'];

		$this->document->setTitle($this->data['heading_title']);
		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('catalog/category'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('catalog/category/update', '&category_id=' . $category_id),
				'text' => $this->data['heading_title'],
				'separator' => ' :: '
		));
		$this->document->addBreadcrumb(array(
				'href' => $page_url,
				'text' => $this->language->get('tab_layout'),
				'separator' => ' :: ',
				'current'	=> true
		));

		$this->data['active'] = 'layout';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/category_tabs', array($this->data));
		$this->data['category_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$layout = new ALayoutManager();
		//get existing page layout or generic
		$page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $category_id);
		$page_id = $page_layout['page_id'];
		$layout_id = $page_layout['layout_id'];
		if (isset($this->request->get['tmpl_id'])) {
			$tmpl_id = $this->request->get['tmpl_id'];
		} else {
			$tmpl_id = $this->config->get('config_storefront_template');
		}			
	    $params = array(
	      'category_id' => $category_id,
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

		$action = $this->html->getSecureURL('catalog/category/save_layout');
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
	    $this->data['current_url'] = $this->html->getSecureURL('catalog/category/edit_layout', $url);
	
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
		
		$this->view->batchAssign($this->data);

		$this->processTemplate('pages/catalog/category_layout.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function save_layout() {
		if (($this->request->is_GET())) {
			$this->redirect($this->html->getSecureURL('catalog/category'));
		}

		$page_controller = 'pages/product/category';
		$page_key_param = 'path';
		$category_id = (int)$this->request->get_or_post('category_id');

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('catalog/category');

		if (!has_value($category_id)) {
			$this->redirect($this->html->getSecureURL('catalog/category'));
		}

		// need to know unique page existing
		$post_data = $this->request->post;
		$tmpl_id = $post_data['tmpl_id'];
		$layout = new ALayoutManager();
		$pages = $layout->getPages($page_controller, $page_key_param, $category_id);
		if (count($pages)) {
			$page_id = $pages[0]['page_id'];
			$layout_id = $pages[0]['layout_id'];
		} else {
			$page_info = array('controller' => $page_controller,
					'key_param' => $page_key_param,
					'key_value' => $category_id);
			$this->loadModel('catalog/category');
			$category_info = $this->model_catalog_category->getCategoryDescriptions($category_id);
			if ($category_info) {
				foreach ($category_info as $language_id => $description) {
					if (!has_value($language_id)) {
						continue;
					}
					$page_info['page_descriptions'][$language_id] = $description;
				}
			}
			$page_id = $layout->savePage($page_info);
			$layout_id = '';
			// need to generate layout name
			$default_language_id = $this->language->getDefaultLanguageID();
			$post_data['layout_name'] = 'Category: ' . $category_info[$default_language_id]['name'];
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
		$this->redirect($this->html->getSecureURL('catalog/category/edit_layout', '&category_id=' . $category_id));
	}

}
