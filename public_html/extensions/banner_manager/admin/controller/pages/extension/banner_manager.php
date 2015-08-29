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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

/**
 * @property ModelExtensionBannerManager $model_extension_banner_manager
 */
class ControllerPagesExtensionBannerManager extends AController {
	public $data = array();
	public $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('banner_manager/banner_manager');

		$this->document->setTitle($this->language->get('banner_manager_name'));
		$this->data['heading_title'] = $this->language->get('banner_manager_list');

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE,
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('extension/banner_manager'),
				'text' => $this->language->get('banner_manager_name'),
				'separator' => ' :: ',
				'current' => true
		));

		$grid_settings = array('table_id' => 'banner_grid',
				'url' => $this->html->getSecureURL('listing_grid/banner_manager'),
				'editurl' => $this->html->getSecureURL('listing_grid/banner_manager/edit'),
				'update_field' => $this->html->getSecureURL('listing_grid/banner_manager/update_field'),
				'sortname' => 'date_modified',
				'sortorder' => 'desc',
				'columns_search' => true,
				'actions' => array(
						'edit' => array(
								'text' => $this->language->get('text_edit'),
								'href' => $this->html->getSecureURL('extension/banner_manager/edit', '&banner_id=%ID%')
						),
						'delete' => array(
								'text' => $this->language->get('button_delete'),
								'href' => $this->html->getSecureURL('extension/banner_manager/delete', '&banner_id=%ID%')
						)
				),
		);

		$form = new AForm ();
		$form->setForm(array('form_name' => 'banner_grid_search'));

		$grid_settings['colNames'] = array($this->language->get('column_banner_id'),
				'', //icons
				$this->language->get('column_banner_name'),
				$this->language->get('column_banner_group'),
				$this->language->get('column_banner_type'),
				$this->language->get('column_status'),
				$this->language->get('column_update_date'));

		$grid_settings['colModel'] = array(array('name' => 'banner_id',
				'index' => 'banner_id',
				'width' => 20,
				'align' => 'center',
				'search' => false),
				array('name' => 'banner_icon',
						'index' => 'icon',
						'width' => 50,
						'align' => 'center',
						'search' => false
				),
				array('name' => 'banner_name',
						'index' => 'name',
						'width' => 110,
						'align' => 'left',
				),
				array('name' => 'banner_group',
						'index' => 'banner_group_name',
						'width' => 110,
						'align' => 'left',
				),
				array('name' => 'banner_type',
						'index' => 'banner_type',
						'width' => 70,
						'align' => 'center',
						'search' => false),
				array('name' => 'status',
						'index' => 'status',
						'align' => 'center',
						'width' => 60,
						'search' => false),
				array('name' => 'date_modified',
						'index' => 'date_modified',
						'width' => 80,
						'align' => 'center',
						'search' => false));

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->data['listing_grid'] = $grid->dispatchGetOutput();

		if (isset ($this->session->data['warning'])) {
			$this->data['error_warning'] = $this->session->data['warning'];
			$this->session->data['warning'] = '';
		} else {
			$this->data ['error_warning'] = '';
		}
		if (isset ($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			$this->session->data['success'] = '';
		} else {
			$this->data ['success'] = '';
		}
		
		$this->data['banner_types'] = array(
			array(
				'href' => $this->html->getSecureURL('extension/banner_manager/insert', '&banner_type=1'),
				'text' => $this->language->get('text_graphic_banner'),
				'icon' => '<i class="fa fa-file-image-o fa-fw"></i>'
			),
			array(
				'href' => $this->html->getSecureURL('extension/banner_manager/insert', '&banner_type=2'),
				'text' => $this->language->get('text_text_banner'),
				'icon' => '<i class="fa fa-file-text-o fa-fw"></i>'
			),		
		);
		$this->data['text_type'] = $this->language->get('column_banner_type');
		$this->data['insert'] = $this->html->getSecureURL('extension/banner_manager/insert');

		$this->data['form_language_switch'] = $this->html->getContentLanguageSwitcher();

		$this->view->batchAssign($this->language->getASet());
		$this->view->batchAssign($this->data);
		$this->view->assign('help_url', $this->gen_help_url('banner_manager'));

		$this->processTemplate('pages/extension/banner_manager.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('banner_manager/banner_manager');

		$this->document->setTitle($this->language->get('banner_manager_name'));
		$this->data['heading_title'] = $this->language->get('banner_manager_name');

		if ($this->request->is_POST() && $this->_validateForm()) {

			$this->_prepareData();

			$this->loadModel('extension/banner_manager');
			$banner_id = $this->model_extension_banner_manager->addBanner($this->request->post);

			$this->session->data ['success'] = $this->language->get('text_banner_success');
			$this->redirect($this->html->getSecureURL('extension/banner_manager/edit', '&banner_id=' . $banner_id));
		}

		foreach ($this->request->post as $k => $v) {
			$this->data[$k] = $v;
		}

		$this->_getForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function edit() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('banner_manager/banner_manager');

		$this->document->setTitle($this->language->get('banner_manager_name'));
		$this->data['heading_title'] = $this->language->get('banner_manager_name');
		$banner_id = (int)$this->request->get['banner_id'];

		// saving
		if ($this->request->is_POST() && $this->_validateForm() && $banner_id) {

			$this->_prepareData();

			$this->loadModel('extension/banner_manager');
			$this->model_extension_banner_manager->editBanner($banner_id, $this->request->post);

			$this->session->data ['success'] = $this->language->get('text_banner_success');
			$this->redirect($this->html->getSecureURL('extension/banner_manager/edit', '&banner_id=' . $banner_id));
		}

		$this->data['error'] = $this->error;

		$this->loadModel('extension/banner_manager');
		$info = $this->model_extension_banner_manager->getBanner($banner_id, 0);
		foreach ($info as $k => $v) {
			$this->data[$k] = $v;
		}
		$this->data['banner_group_name'] = array($this->data['banner_group_name'], $this->data['banner_group_name']);

		$this->_getForm();
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function delete() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$banner_id = (int)$this->request->get['banner_id'];
		$this->loadModel('extension/banner_manager');
		$this->model_extension_banner_manager->deleteBanner($banner_id);
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->redirect($this->html->getSecureURL('extension/banner_manager'));
	}

	private function _getForm() {

		if (isset ($this->session->data['warning'])) {
			$this->data ['error_warning'] = $this->session->data['warning'];
			$this->session->data['warning'] = '';
		} else {
			$this->data ['error_warning'] = '';
		}

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->initBreadcrumb(array('href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE));
		$this->document->addBreadcrumb(array('href' => $this->html->getSecureURL('extension/banner_manager'),
				'text' => $this->language->get('banner_manager_name'),
				'separator' => ' :: '));

		$this->data ['cancel'] = $this->html->getSecureURL('extension/banner_manager');

		$banner_type = 1;
		if (!isset($this->request->get['banner_id'])) {
			if ( $this->request->get['banner_type'] ) {
				$banner_type = $this->request->get['banner_type'];
			} elseif ($this->request->post['banner_type']) {
				$banner_type = $this->request->post['banner_type'];
			}	
					
			$this->data ['action'] = $this->html->getSecureURL('extension/banner_manager/insert');
			$this->data ['form_title'] = $this->language->get('text_create');
			$this->data ['update'] = '';
			$form = new AForm ('ST');
		} else {
			$this->data ['action'] = $this->html->getSecureURL('extension/banner_manager/edit', '&banner_id=' . $this->request->get ['banner_id']);
			$this->data ['form_title'] = $this->language->get('text_edit') . ' ' . $this->data['name'];
			$this->data ['update'] = $this->html->getSecureURL('listing_grid/banner_manager/update_field', '&banner_id=' . $this->request->get ['banner_id']);
			$form = new AForm ('HS');

			$this->data['button_details'] = $this->html->buildElement(
					array('type' => 'button',
							'name' => 'btn_details',
							'href' => $this->html->getSecureUrl('extension/banner_manager_stat/details', '&banner_id=' . $this->request->get ['banner_id']),
							'text' => $this->language->get('text_view_stat')
					));
					
			$banner_type = $this->data['banner_type'];
		} 

		if ($banner_type == 1) {
			$this->data['banner_types'] = array(
				'text' => $this->language->get('text_graphic_banner'),
				'icon' => '<i class="fa fa-file-image-o fa-fw"></i>'
			);		
		} else {
			$this->data['banner_types'] = array(
				'text' => $this->language->get('text_text_banner'),
				'icon' => '<i class="fa fa-file-text-o fa-fw"></i>'
			);				
		}

		$this->document->addBreadcrumb(
				array('href' => $this->data['action'],
						'text' => $this->data ['form_title'],
						'separator' => ' :: ',
						'current' => true
				));

		$form->setForm(array('form_name' => 'BannerFrm', 'update' => $this->data ['update']));

		$this->data['form']['form_open'] = $form->getFieldHtml(
				array('type' => 'form',
						'name' => 'BannerFrm',
						'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
						'action' => $this->data ['action']));

		$this->data['form']['hidden_fields']['type'] = $form->getFieldHtml(array(
				'type' => 'hidden',
				'name' => 'banner_type',
				'value' => $banner_type ));

		$this->data['form']['submit'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'submit',
						'text' => $this->language->get('button_save')));
		$this->data['form']['cancel'] = $form->getFieldHtml(
				array('type' => 'button',
						'name' => 'cancel',
						'text' => $this->language->get('button_cancel')));

		//check if banner is active based on dates and update status
		$now = time();
		if (dateISO2Int($this->data['start_date']) > $now ) {
			$this->data['status'] = 0;
		}
		$stop = dateISO2Int($this->data['end_date']);

		if($stop > 0 && $stop < $now){
			$this->data['status'] = 0;
		}

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array('type' => 'checkbox',
				'name' => 'status',
				'value' => $this->data['status'],
				'style' => 'btn_switch'));
		$this->data['form']['text']['status'] = $this->language->get('entry_banner_status');

		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'name',
				'value' => $this->data['name'],
				'multilingual' => true,
				'required' => true));
		$this->data['form']['text']['name'] = $this->language->get('entry_banner_name');

		// groups of banners
		$this->loadModel('extension/banner_manager');
		$result = $this->model_extension_banner_manager->getBannerGroups();
		$groups = array('0' => $this->language->get('text_select'), 'new' => $this->language->get('text_add_new_group'));
		if ($result) {
			foreach ($result as $row) {
				$groups[$row['banner_group_name']] = $row['banner_group_name'];
			}
		}
		$value = $this->data['banner_group_name'][0];
		if(!$value && sizeof($groups)==2){
			$value = 'new';
		}

		$this->data['form']['fields']['banner_group_name'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'banner_group_name[0]',
				'options' => $groups,
				'value' => $value,
				'required' => true,
				'style' => 'no-save'
		));
		$this->data['form']['text']['banner_group_name'] = $this->language->get('entry_banner_group_name');

		$this->data['form']['fields']['new_banner_group'] = $form->getFieldHtml(
				array(  'type' => 'input',
						'name' => 'banner_group_name[1]',
						'value' => (!in_array($this->data['banner_group_name'][1], $groups) ? $this->data['banner_group_name'][1] : ''),
						'placeholder' => $this->language->get('text_put_new_group'),
						'style' => 'no-save'
						));
		$this->data['new_group_hint'] = $this->language->get('text_put_new_group');
		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'sort_order',
				'value' => $this->data['sort_order'],
				'style' => 'small-field',
				''));
		$this->data['form']['text']['sort_order'] = $this->language->get('entry_banner_sort_order');

		if ($banner_type == 1){
			$this->data['form']['fields']['url'] = $form->getFieldHtml(
					array (
							'type'     => 'input',
							'name'     => 'target_url',
							'value'    => $this->data['target_url'],
							'required' => true
					));
			$this->data['form']['text']['url'] = $this->language->get('entry_banner_url');

			$this->data['form']['fields']['blank'] = $form->getFieldHtml(
					array ('type'  => 'checkbox',
					       'name'  => 'blank',
					       'value' => $this->data['blank'],
					       'style' => 'btn_switch'
					));
			$this->data['form']['text']['blank'] = $this->language->get('entry_banner_blank');
		}
		$this->data['form']['fields']['date_start'] = $form->getFieldHtml(array(
				'type' => 'date',
				'name' => 'start_date',
				'value' => dateISO2Display($this->data['start_date']),
				'default' => dateNowDisplay(),
				'dateformat' => format4Datepicker($this->language->get('date_format_short')),
				'highlight' => 'future',
				'style' => 'small-field'));
		$this->data['form']['text']['date_start'] = $this->language->get('entry_banner_date_start');

		$this->data['form']['fields']['date_end'] = $form->getFieldHtml(array(
				'type' => 'date',
				'name' => 'end_date',
				'value' => dateISO2Display($this->data['end_date']),
				'default' => '',
				'dateformat' => format4Datepicker($this->language->get('date_format_short')),
				'highlight' => 'pased',
				'style' => 'small-field'));

		$this->data['form']['text']['date_end'] = $this->language->get('entry_banner_date_end');

		$this->data['banner_id'] = $this->request->get['banner_id'] ? $this->request->get['banner_id'] : '-1';
		
		if ($banner_type == 1) {
			$this->data['form']['fields']['meta'] = $form->getFieldHtml(array(  
				'type' => 'textarea',
				'name' => 'meta',
				'value' => $this->data ['meta'],
				'attr' => ' style="height: 50px;"'  )
				);
			$this->data['form']['text']['meta'] = $this->language->get('entry_banner_meta');
	
			$this->addChild('responses/common/resource_library/get_resources_html', 'resources_html', 'responses/common/resource_library_scripts.tpl');
			$resources_scripts = $this->dispatch(
					'responses/common/resource_library/get_resources_scripts',
					array(
							'object_name' => 'banners',
							'object_id' => $this->data['banner_id'],
							'types' => array('image'),
					)
			);
	
			$this->view->assign('current_url', $this->html->currentURL());
			$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
			$this->view->assign('rl', $this->html->getSecureURL('common/resource_library', '&object_name=banners&type=image'));
		} else {
			$this->data['form']['fields']['description'] = $form->getFieldHtml(array(
				'type' => 'textarea',
				'name' => 'description',
				'value' => $this->data ['description'],
				'attr' => '')
				);
			$this->data['form']['text']['description'] = $this->language->get('entry_banner_html');
			$resources_scripts = $this->dispatch(
					'responses/common/resource_library/get_resources_scripts',
					array(
							'object_name' => '',
							'object_id' => '',
							'types' => array('image'),
					)
			);
			$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
		}

		$this->view->batchAssign($this->language->getASet());
		$this->view->batchAssign($this->data);
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_code', $this->session->data['language']);
		$this->view->assign('help_url', $this->gen_help_url('banner_edit'));

		$this->processTemplate('pages/extension/banner_manager_form.tpl');
	}

	private function _validateForm() {
		if (!$this->user->canModify('extension/banner_manager')) {
			$this->session->data['warning'] = $this->error ['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post) {
			$required = array('name', 'banner_type', 'banner_group_name');
			if ($this->request->post['banner_type'] == 1) {
				$required[] = 'target_url';
			}
			foreach ($this->request->post as $name => $value) {
				if (in_array($name, $required) && empty($value)) {
					$this->error ['warning'] = $this->language->get('error_empty');
					$this->session->data['warning'] = $this->language->get('error_empty');
					break;
				}
			}

			if (!is_array($this->request->post['banner_group_name'])
					|| (!$this->request->post['banner_group_name'][1] && in_array($this->request->post['banner_group_name'][0], array('0', 'new')))
					|| trim($this->request->post['banner_group_name'][1]) == trim($this->language->get('text_put_new_group')) && in_array($this->request->post['banner_group_name'][0], array('0', 'new'))
			) {

				$this->error ['warning'] = $this->language->get('error_empty');
				$this->session->data['warning'] = $this->language->get('error_empty');
			}
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	// Prepare data before passing to model
	private function _prepareData() {
		if (isset($this->request->post['start_date']) && $this->request->post['start_date']) {
			$this->request->post['start_date'] = dateDisplay2ISO($this->request->post['start_date']);
		}
		if (isset($this->request->post['end_date']) && $this->request->post['end_date']) {
			$this->request->post['end_date'] = dateDisplay2ISO($this->request->post['end_date']);
		}

		if (is_array($this->request->post['banner_group_name']) && isset($this->request->post['banner_group_name'][1])) {
			$this->request->post['banner_group_name'][1] = trim($this->request->post['banner_group_name'][1]);
			$this->request->post['banner_group_name'][1] = mb_ereg_replace('/^[0-9A-Za-z\ \. _\-]/', '', $this->request->post['banner_group_name'][1]);
		}

		if ($this->request->post['banner_group_name'][1] && $this->request->post['banner_group_name'][0] == 'new') {
			$this->request->post['banner_group_name'] = $this->request->post['banner_group_name'][1];
		} else {
			$this->request->post['banner_group_name'] = $this->request->post['banner_group_name'][0];
		}
	}

	public function insert_block() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('design/blocks');
		$this->loadLanguage('banner_manager/banner_manager');
		$this->document->setTitle($this->language->get('banner_manager_name'));
		$this->data['heading_title'] = $this->language->get('text_banner_block');

		$lm = new ALayoutManager();
		$block = $lm->getBlockByTxtId('banner_block');
		$this->data['block_id'] = $block['block_id'];

		if ($this->request->is_POST() && $this->_validateBlockForm()) {
			if (isset($this->session->data['layout_params'])) {
				$layout = new ALayoutManager($this->session->data['layout_params']['tmpl_id'],
						$this->session->data['layout_params']['page_id'],
						$this->session->data['layout_params']['layout_id']);
				$blocks = $layout->getLayoutBlocks();
				if ($blocks) {
					foreach ($blocks as $block) {
						if ($block['block_id'] == $this->session->data['layout_params']['parent_block_id']) {
							$parent_instance_id = $block['instance_id'];
							$position = 0;
							if ($block['children']) {
								foreach ($block['children'] as $child) {
									$position = $child['position'] > $position ? $child['position'] : $position;
								}
							}
							break;
						}
					}
				}
				$savedata = $this->session->data['layout_params'];
				$savedata['parent_instance_id'] = $parent_instance_id;
				$savedata['position'] = $position + 10;
				$savedata['status'] = 1;
			} else {
				$layout = new ALayoutManager();
			}

			$content = '';
			if ($this->request->post['banner_group_name']) {
				$content = serialize(array('banner_group_name' => $this->request->post['banner_group_name']));
			}

			$custom_block_id = $layout->saveBlockDescription(
					$this->data['block_id'],
					0,
					array('name' => $this->request->post['block_name'],
							'title' => $this->request->post['block_title'],
							'description' => $this->request->post['block_description'],
							'content' => $content,
							'status' => (int)$this->request->post['block_status'],
							'block_wrapper' => $this->request->post['block_wrapper'],
							'block_framed' => $this->request->post['block_framed'],
							'language_id' => $this->session->data['content_language_id']));

			// save custom_block in layout
			if (isset($this->session->data['layout_params'])) {
				$savedata['custom_block_id'] = $custom_block_id;
				$savedata['block_id'] = $this->data['block_id'];
				$layout->saveLayoutBlocks($savedata);
				unset($this->session->data['layout_params']);
			}
			// save list if it is custom

			if ($this->request->post['block_banners']) {
				$listing_manager = new AListingManager($custom_block_id);
				$listing_manager->deleteCustomListing();
				foreach ($this->request->post['block_banners'] as $k=>$id) {
					$listing_manager->saveCustomListItem(
							array('data_type' => 'banner_id',
									'id' => (int)$id,
									'sort_order' => (int)$k));

				}

			}

			$this->session->data ['success'] = $this->language->get('text_banner_success');
			$this->redirect($this->html->getSecureURL('extension/banner_manager/edit_block', '&custom_block_id=' . $custom_block_id));
		}

		foreach ($this->request->post as $k => $v) {
			$this->data[$k] = $v;
		}


		$blocks = array();
		$custom_block_types = array('html_block', 'listing_block');
		foreach ($custom_block_types as $txt_id) {
			$block = $lm->getBlockByTxtId($txt_id);
			if ($block['block_id']) {
				$blocks[$block['block_id']] = $this->language->get('text_' . $txt_id);
			}
		}
		foreach ($blocks as $block_text) {
			$this->data['tabs'][] = array('href' => $this->html->getSecureURL('design/blocks/insert', '&block_id=' . $this->data['block_id']),
					'text' => $block_text,
					'active' => false);
		}
		$this->data['tabs'][] = array('href' => $this->html->getSecureURL('extension/banner_manager/insert_block', '&block_id=' . $this->data['block_id']),
				'text' => $this->language->get('text_banner_block'),
				'active' => true);

		$this->_getBlockForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}

	public function edit_block() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('banner_manager/banner_manager');
		$this->loadLanguage('design/blocks');
		$this->document->setTitle($this->language->get('banner_manager_name'));
		$this->data['heading_title'] = $this->language->get('text_banner_block');

		$lm = new ALayoutManager();
		$block = $lm->getBlockByTxtId('banner_block');
		$this->data['block_id'] = $block['block_id'];
		$custom_block_id = (int)$this->request->get['custom_block_id'];
		if (!$custom_block_id) {
			$this->redirect($this->html->getSecureURL('extension/banner_manager/insert_block'));
		}
		$layout = new ALayoutManager();
		if ($this->request->is_POST() && $this->_validateBlockForm()) {
			$content = '';
			if ($this->request->post['banner_group_name']) {
				$content = serialize(array('banner_group_name' => $this->request->post['banner_group_name']));
			}
			// saving
			$layout->saveBlockDescription($this->data['block_id'],
					$custom_block_id,
					array('name' => $this->request->post['block_name'],
							'title' => $this->request->post['block_title'],
							'description' => $this->request->post['block_description'],
							'content' => $content,
							'status' => (int)$this->request->post['block_status'],
							'block_wrapper' => $this->request->post['block_wrapper'],
							'block_framed' => $this->request->post['block_framed'],
							'language_id' => $this->session->data['content_language_id']));

			// save list if it is custom
			if ($this->request->post['block_banners']) {
				$listing_manager = new AListingManager($custom_block_id);
				$listing_manager->deleteCustomListing();
				$k=0;
				foreach ($this->request->post['block_banners'] as $id) {
					$listing_manager->saveCustomListItem(
							array('data_type' => 'banner_id',
									'id' => $id,
									'sort_order' => (int)$k));
					$k++;
				}
			} else {
				//delete the list as nothing provided
				$listing_manager = new AListingManager($custom_block_id);
				$listing_manager->deleteCustomListing();			
			}
			
			$this->session->data ['success'] = $this->language->get('text_banner_success');
			$this->redirect($this->html->getSecureURL('extension/banner_manager/edit_block', '&custom_block_id=' . $custom_block_id));
		}

		$this->data['tabs'][0] = array('href' => $this->html->getSecureURL('extension/banner_manager/insert_block', '&block_id=' . $this->data['block_id']),
				'text' => $this->language->get('text_banner_block'),
				'active' => true);

		$this->_getBlockForm();

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}


	private function _getBlockForm() {
		if (isset ($this->session->data['warning'])) {
			$this->data ['error_warning'] = $this->session->data['warning'];
			$this->session->data['warning'] = '';
		} else {
			$this->data ['error_warning'] = '';
		}
		$this->load->library('json');
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}


		$this->document->initBreadcrumb(array('href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE));
		$this->document->addBreadcrumb(array('href' => $this->html->getSecureURL('design/blocks'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '));

		$this->data ['cancel'] = $this->html->getSecureURL('design/blocks');
		$custom_block_id = (int)$this->request->get ['custom_block_id'];

		// need to get data of custom listing
		$options_list = array();
		if ($custom_block_id) {
			$lm = new ALayoutManager();
			$block_info = $lm->getBlockDescriptions($custom_block_id);

			$language_id = $this->session->data['content_language_id'];
			if (!isset($block_info[$language_id])) {
				$language_id = key($block_info);
			}

			foreach ($block_info[$language_id] as $k => $v) {
				$this->data[$k] = $v;
			}
			$content = $block_info[$this->language->getContentLanguageID()]['content'];

			if ($content) {
				$content = unserialize($content);
			} else {
				$content = current($block_info);
				$content = unserialize($content['content']);
			}

			$this->data['banner_group_name'] = $content['banner_group_name'];
			$lm = new AListingManager($this->request->get ['custom_block_id']);
			$list = $lm->getCustomList();

			$options_list = array();
			if ($list) {
				foreach ($list as $row) {
					$options_list[(int)$row['id']] = array();
				}
				$ids = array_keys($options_list);
				$assigned_banners = $this->model_extension_banner_manager->getBanners(array('subsql_filter' => 'b.banner_id IN ('.implode(', ',$ids).')'));

				$rm = new AResourceManager();
				$rm->setType('image');

				foreach($assigned_banners as $banner){
					$id = $banner['banner_id'];
					if(in_array($id, $ids)){
						$thumbnail = $rm->getMainThumb('banners',
														$banner['banner_id'],
														(int)$this->config->get('config_image_grid_width'),
														(int)$this->config->get('config_image_grid_height'),
														false);
						$icon = $thumbnail['thumb_html'] ? $thumbnail['thumb_html'] : '<i class="fa fa-code fa-4x"></i>&nbsp;';
						$options_list[$id] = array(
														'image' => $icon,
														'id' => $id,
														'name' => $banner['name'],
														'sort_order' => (int)$banner['sort_order'],
													);
					}
				}
			}
		}


		if (!$custom_block_id) {
			$this->data ['action'] = $this->html->getSecureURL('extension/banner_manager/insert_block');
			$this->data ['form_title'] = $this->language->get('text_create_block');
			$this->data ['update'] = '';
			$form = new AForm ('ST');
		} else {
			$this->data ['action'] = $this->html->getSecureURL('extension/banner_manager/edit_block', '&custom_block_id=' . $custom_block_id);
			$this->data ['form_title'] = $this->language->get('text_edit') . ' ' . $this->data['name'];
			$this->data ['update'] = $this->html->getSecureURL('listing_grid/blocks_grid/update_field', '&custom_block_id=' . $custom_block_id);
			$form = new AForm ('HS');
		}

		$this->document->addBreadcrumb(array('href' => $this->data['action'],
				'text' => $this->data ['form_title'],
				'separator' => ' :: ',
				'current' => true
		));

		$form->setForm(array('form_name' => 'BannerBlockFrm', 'update' => $this->data ['update']));

		$this->data['form']['form_open'] = $form->getFieldHtml(array('type' => 'form',
				'name' => 'BannerBlockFrm',
				'action' => $this->data ['action'],
				'attr' => 'data-confirm-exit="true" class="aform form-horizontal"'
				));
		$this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_save'),
				'style' => 'button1'));
		$this->data['form']['cancel'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel'),
				'style' => 'button2'));

		if ($custom_block_id) {
			$this->data['form']['fields']['block_status'] = $form->getFieldHtml(
					array('type' => 'checkbox',
					'name' => 'block_status',
					'value' => $this->data['status'],
					'style' => 'btn_switch'
			));
			$this->data['form']['text']['block_status'] = $this->html->convertLinks($this->language->get('entry_block_status'));

			$this->data['form']['fields']['block_status_note'] = '';
			$this->data['form']['text']['block_status_note'] = $this->html->convertLinks($this->language->get('entry_block_status_note'));
		}

		$this->data['form']['fields']['block_name'] = $form->getFieldHtml(array('type' => 'hidden',
				'name' => 'block_id',
				'value' => $this->data['block_id']));
		$this->data['form']['fields']['block_name'] .= $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'block_name',
				'value' => $this->data['name'],
				'multilingual' => true,
				'required' => true));
		$this->data['form']['text']['block_name'] = $this->language->get('entry_block_name');

		$this->data['form']['fields']['block_title'] = $form->getFieldHtml(array('type' => 'input',
				'name' => 'block_title',
				'required' => true,
				'multilingual' => true,
				'value' => $this->data ['title']
		));
		$this->data['form']['text']['block_title'] = $this->language->get('entry_block_title');


		// list of templates for block
		$tmpl_ids = $this->extensions->getInstalled('template');
		$tmpl_ids[] = 'default';
		$this->data['block_wrappers'] = array();
		foreach ($tmpl_ids as $tmpl_id) {
			// for tpls of block that stores in db
			$layout_manager = new ALayoutManager($tmpl_id);
			$block = $layout_manager->getBlockByTxtId('banner_block');
			$block_templates = (array)$layout_manager->getBlockTemplates($block['block_id']);
			foreach ($block_templates as $item) {
				if ($item['template']) {
					$this->data['block_wrappers'][$item['template']] = $item['template'];
				}
			}

			//Automatic block template selection mode based on parent is limited to 1 template per location
			//To extend, allow custom block's template to be selected to suppress automatic selection

			//for tpls that stores in main.php (other extensions templates)
			$ext_tpls = $this->extensions->getExtensionTemplates();
			foreach ($ext_tpls as $section) {
				foreach ($section as $s => $tpls) {
					if ($s != 'storefront') {
						continue;
					}
					foreach ($tpls as $tpl) {
						if (isset($this->data['block_wrappers'][$tpl]) || strpos($tpl, 'blocks/banner_block/') === false) {
							continue;
						}
						$this->data['block_wrappers'][$tpl] = $tpl;
					}
				}
			}

			$tpls = glob(DIR_STOREFRONT . 'view/*/template/blocks/banner_block/*.tpl');
			foreach ($tpls as $tpl) {
				$pos = strpos($tpl, 'blocks/banner_block/');
				$tpl = substr($tpl, $pos);
				if (!isset($this->data['block_wrappers'][$tpl])) {
					$this->data['block_wrappers'][$tpl] = $tpl;
				}
			}

		}

		ksort($this->data['block_wrappers']);
		array_unshift($this->data['block_wrappers'], $this->language->get('text_automatic'));

		if ($this->data['block_wrapper'] && !isset($this->data['block_wrappers'][$this->data['block_wrapper']])) {
			$this->data['block_wrappers'] = array_merge(array($this->data['block_wrapper'] => $this->data['block_wrapper']), $this->data['block_wrappers']);
		}

		$this->data['form']['fields']['block_wrapper'] = $form->getFieldHtml(array('type' => 'selectbox',
				'name' => 'block_wrapper',
				'options' => $this->data['block_wrappers'],
				'value' => $this->data['block_wrapper'],
				));
		$this->data['form']['text']['block_wrapper'] = $this->language->get('entry_block_wrapper');


		$this->data['form']['fields']['block_framed'] = $form->getFieldHtml(array('type' => 'checkbox',
				'name' => 'block_framed',
				'value' => $this->data['block_framed'],
				'style' => 'btn_switch',
				));
		$this->data['form']['text']['block_framed'] = $this->language->get('entry_block_framed');

		$this->data['form']['fields']['block_description'] = $form->getFieldHtml(array('type' => 'textarea',
				'name' => 'block_description',
				'value' => $this->data ['description'],
				'attr' => ' style="height: 50px;"',
				'multilingual' => true,
		));
		$this->data['form']['text']['block_description'] = $this->language->get('entry_block_description');

		// groups of banners
		$this->loadModel('extension/banner_manager');
		$result = $this->model_extension_banner_manager->getBannerGroups();
		$groups = array('0' => $this->language->get('text_select'));
		if ($result) {
			foreach ($result as $row) {
				$groups[$row['banner_group_name']] = $row['banner_group_name'];
			}
		}
		$this->data['form']['fields']['banner_group_name'] = $form->getFieldHtml(array('type' => 'selectbox',
				'name' => 'banner_group_name',
				'options' => $groups,
				'value' => $this->data['banner_group_name'],
				'style' => 'no-save'
		));
		$this->data['form']['text']['banner_group_name'] = $this->language->get('entry_banner_group_name');


		$this->data['form']['text']['listed_banners'] = $this->language->get('entry_banners_selected');

		//load only prior saved products
		$this->data['banners'] = array();

		$this->data['form']['fields']['listed_banners'] = $form->getFieldHtml( array(
		        'type' => 'multiselectbox',
		        'name' => 'block_banners[]',
		        'value' => $ids,
		        'options' => $options_list,
		        'style' => 'no-save chosen',
		        'ajax_url' => $this->html->getSecureURL('listing_grid/banner_manager/banners'),
		        'placeholder' => $this->language->get('text_select_from_lookup'),
		));

		$this->view->batchAssign($this->language->getASet());
		$this->view->batchAssign($this->data);
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_code', $this->session->data['language']);
		$this->view->assign('help_url', $this->gen_help_url('banner_edit'));
		$this->view->assign('rl', $this->html->getSecureURL('common/resource_library', '&object_name=banners&type=image&mode=url'));

		$this->processTemplate('pages/extension/banner_manager_block_form.tpl');
	}


	private function _validateBlockForm() {
		if (!$this->user->canModify('extension/banner_manager')) {
			$this->session->data['warning'] = $this->error ['warning'] = $this->language->get('error_permission');
		}

		if (!$this->data['block_id']) {
			$this->error ['warning'] = $this->session->data['warning'] = 'Block with txt_id "banner_block" does not exists in your database!';
		}

		if ($this->request->post) {
			$required = array('block_name', 'block_title');

			foreach ($this->request->post as $name => $value) {
				if (in_array($name, $required) && empty($value)) {
					$this->error ['warning'] = $this->session->data['warning'] = $this->language->get('error_empty');
					break;
				}
			}
		}

		foreach ($required as $name) {
			if (!in_array($name, array_keys($this->request->post))) {
				return false;
			}
		}
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}