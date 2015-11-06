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

class ControllerPagesDesignBlocks extends AController {
	public $data = array('custom_block_types' => array('html_block', 'listing_block'));
	private $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE,
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('design/blocks'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current' => true
		));

		$grid_settings = array('table_id' => 'block_grid',
				'url' => $this->html->getSecureURL('listing_grid/blocks_grid'),
				'editurl' => $this->html->getSecureURL('listing_grid/blocks/edit'),
				'update_field' => $this->html->getSecureURL('listing_grid/blocks/update_field'),
				'sortname' => 'date_added',
				'sortorder' => 'desc',
				'columns_search' => true,
				'multiselect' => 'false',
				'grid_ready' => 'grid_ready();',
				'actions' => array(
						'edit' => array(
								'text' => $this->language->get('text_edit'),
								'href' => $this->html->getSecureURL('design/blocks/edit')
						),
						'view' => array(
								'text' => $this->language->get('text_view'),
								'href' => $this->html->getSecureURL('design/blocks_manager/block_info', '&block_id=%ID%')
						),
						'delete' => array(
								'text' => $this->language->get('button_delete'),
								'href' => $this->html->getSecureURL('design/blocks/delete')
						)
				),
		);

		$grid_settings['colNames'] = array($this->language->get('column_block_id'),
				$this->language->get('column_block_txt_id'),
				$this->language->get('column_block_name'),
				$this->language->get('column_status'),
				$this->language->get('column_date_added'));

		$grid_settings['colModel'] = array(array('name' => 'block_id',
				'index' => 'block_id',
				'width' => 100,
				'align' => 'center',
				'search' => false),
				array('name' => 'block_txt_id',
						'index' => 'block_txt_id',
						'width' => 210,
						'align' => 'left',
						'search' => 'true'),
				array('name' => 'block_name',
						'index' => 'name',
						'width' => 250,
						'align' => 'left',
						'search' => 'true'),
				array('name' => 'status',
						'index' => 'status',
						'align' => 'center',
						'width' => 110,
						'search' => false),
				array('name' => 'block_date_added',
						'index' => 'block_date_added',
						'align' => 'center',
						'width' => 100,
						'search' => false));

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign('popup_title', 'Information about block'); //$this->language->get('Information about block'));

		if (isset ($this->session->data['warning'])) {
			$this->view->assign('error_warning', $this->session->data['warning']);
			$this->session->data['warning'] = '';
		} else {
			$this->data ['error_warning'] = '';
		}
		if (isset ($this->session->data['success'])) {
			$this->view->assign('success', $this->session->data['success']);
			$this->session->data['success'] = '';
		} else {
			$this->data ['success'] = '';
		}

		$this->view->batchAssign($this->language->getASet());
		$this->initTabs();
		$this->view->assign('inserts', $this->data['tabs']);

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('block_listing'));

		$this->processTemplate('pages/design/blocks.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function insert() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->document->setTitle($this->language->get('heading_title'));

		$block_id = (int)$this->request->get['block_id'] ? (int)$this->request->get['block_id'] : (int)$this->request->post['block_id'];
		// now need to know what custom block is this
		$lm = new ALayoutManager();
		$blocks = $lm->getAllBlocks();
		foreach ($blocks as $block) {
			if ($block['block_id'] == $block_id) {
				$block_txt_id = $block['block_txt_id'];
				break;
			}
		}

		if ($this->request->is_POST() && $this->_validateForm()) {
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


			switch ($block_txt_id) {
				case 'listing_block':
					$content = array('listing_datasource' => $this->request->post['listing_datasource']);

					if (strpos($content['listing_datasource'], 'custom_') === FALSE) {
						$content['limit'] = $this->request->post['limit'];
					}
					if ($content['listing_datasource'] == 'media') {
						$content['resource_type'] = $this->request->post['resource_type'];
					}

					$content = serialize($content);
					break;
				case 'html_block':
					$content = $this->request->post['block_content'];
					break;
				default:
					$this->redirect($this->html->getSecureURL('design/blocks'));
					break;
			}

			$custom_block_id = $layout->saveBlockDescription($block_id,
					0,
					array('name' => $this->request->post['block_name'],
							'title' => $this->request->post['block_title'],
							'description' => $this->request->post['block_description'],
							'content' => $content,
							'status' => (int)$this->request->post['block_status'],
							'block_wrapper' => $this->request->post['block_wrapper'],
							'block_framed' => (int)$this->request->post['block_framed'],
							'language_id' => $this->session->data['content_language_id'])
			);
			// save custom_block in layout
			if (isset($this->session->data['layout_params'])) {
				$savedata['custom_block_id'] = $custom_block_id;
				$savedata['block_id'] = $block_id;
				$layout->saveLayoutBlocks($savedata);
				unset($this->session->data['layout_params']);
			}

			// save list if it is custom
			if (strpos($this->request->post['listing_datasource'], 'custom_') !== FALSE) {
				$listing_manager = new AListingManager($custom_block_id);
				if ($this->request->post['selected']) {
					$listing_manager->deleteCustomListing();
					$k = 0;
					foreach ($this->request->post['selected'] as $id) {
						$listing_manager->saveCustomListItem(
								array(
										'listing_datasource' => $this->request->post['listing_datasource'],
										'id' => $id,
										'limit' => $this->request->post['limit'],
										'sort_order' => (int)$k));
						$k++;
					}
				}
			}


			$this->session->data ['success'] = $this->language->get('text_success');
			unset($this->session->data['custom_list_changes'][$custom_block_id], $this->session->data['layout_params']);
			$this->redirect($this->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $custom_block_id));
		}

		// if we need to save new block in layout - keep parameters in session
		if (!isset($this->session->data['layout_params']) && isset($this->request->get['layout_id'])) {
			$this->session->data['layout_params']['layout_id'] = $this->request->get['layout_id'];
			$this->session->data['layout_params']['page_id'] = $this->request->get['page_id'] ? $this->request->get['page_id'] : 1;
			$this->session->data['layout_params']['tmpl_id'] = $this->request->get['tmpl_id'];
			$this->session->data['layout_params']['parent_block_id'] = $this->request->get['parent_block_id'];

		}

		$this->initTabs();
		switch ($block_txt_id) {
			case 'listing_block':
				$this->_getListingForm();
				break;
			case 'html_block':
			default:
				$this->_getHTMLForm();
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function edit() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->document->setTitle($this->language->get('heading_title'));

		if (is_int(strpos($this->request->get['custom_block_id'], '_'))) {
			$t = explode('_', $this->request->get['custom_block_id']);
			$custom_block_id = $t[1];
		} else {
			$custom_block_id = (int)$this->request->get['custom_block_id'];
		}

		// now need to know what custom block is this
		$lm = new ALayoutManager();
		$blocks = $lm->getAllBlocks();
		foreach ($blocks as $block) {
			if ($block['custom_block_id'] == $custom_block_id) {
				$block_txt_id = $block['block_txt_id'];
				break;
			}
		}

		$layout = new ALayoutManager();

		// saving
		if ($this->request->is_POST() && $this->_validateForm()) {
			switch ($block_txt_id) {
				case 'listing_block':
					$content = array('listing_datasource' => $this->request->post['listing_datasource']);

					$listing_manager = new AListingManager($custom_block_id);
					// need to check previous listing_datasource of that block
					$block_info = $lm->getBlockDescriptions($custom_block_id);
					$block_info = current($block_info);
					$block_info['content'] = unserialize($block_info['content']);
					// if datasource changed - drop custom list

					if ($block_info['content']['listing_datasource'] != $content['listing_datasource']) {
						$listing_manager->deleteCustomListing();
					}
					if (strpos($content['listing_datasource'], 'custom_') !== FALSE) {

						if ($this->request->post['selected']) {
							$listing_manager->deleteCustomListing();
							$k = 0;
							foreach ($this->request->post['selected'] as $id) {
								$listing_manager->saveCustomListItem(
										array('listing_datasource' => $content['listing_datasource'],
												'id' => $id,
												'limit' => $this->request->post['limit'],
												'sort_order' => $k));
								$k++;
							}
						}
					} else {
						if ($content['listing_datasource'] == 'media') {
							$content['resource_type'] = $this->request->post['resource_type'];
						}
						$content['limit'] = $this->request->post['limit'];
					}
					$content = serialize($content);
					break;
				case 'html_block':
					$content = $this->request->post['block_content'];
					break;
				default:
					$this->redirect($this->html->getSecureURL('design/blocks'));
					break;
			}

			$layout->saveBlockDescription(0,
					$custom_block_id,
					array('name' => $this->request->post['block_name'],
							'title' => $this->request->post['block_title'],
							'description' => $this->request->post['block_description'],
							'content' => $content,
							'block_wrapper' => $this->request->post['block_wrapper'],
							'block_framed' => (int)$this->request->post['block_framed'],
							'language_id' => $this->session->data['content_language_id']));
			$layout->editBlockStatus((int)$this->request->post['block_status'], 0,	$custom_block_id);
			$this->session->data ['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $custom_block_id));
		}
		// end of saving

		$info = $layout->getBlockDescriptions($custom_block_id);

		if (isset($info[$this->session->data['content_language_id']])) {
			$info = $info[$this->session->data['content_language_id']];
		} else {
			$info = current($info);
			unset($info['name'], $info['title'], $info['description']);
		}

		foreach ($info as $k => $v) {
			$this->data[$k] = $v;
		}

		$this->data['tabs'][0] = array('href' => '',
				'text' => $this->language->get('text_' . $block_txt_id),
				'active' => true);

		switch ($block_txt_id) {
			case 'listing_block':
				$this->_getListingForm();
				break;
			case 'html_block':
			default:
				$this->_getHTMLForm();
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function initTabs() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$blocks = array();
		$lm = new ALayoutManager();

		foreach ($this->data['custom_block_types'] as $txt_id) {
			$block = $lm->getBlockByTxtId($txt_id);
			if ($block['block_id']) {
				$blocks[$block['block_id']] = $this->language->get('text_' . $txt_id);
			}
			if ($txt_id == 'html_block') {
				$default_block_type = $block['block_id'];
			}
		}

		$this->request->get['block_id'] = !(int)$this->request->get['block_id'] ? $default_block_type : $this->request->get['block_id'];
		$i = 0;
		foreach ($blocks as $block_id => $block_text) {
			$this->data['tabs'][$i] = array(
					'href' => $this->html->getSecureURL('design/blocks/insert', '&block_id=' . $block_id),
					'text' => $block_text,
					'active' => ($block_id == $this->request->get['block_id'] ? true : false));
			$i++;
		}
		ksort($this->data['tabs']);
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function delete() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$custom_block_id = (int)$this->request->get['custom_block_id'];
		$layout = new ALayoutManager();
		if (!$layout->deleteCustomBlock($custom_block_id)) {
			$this->session->data['warning'] = $this->language->get('error_delete');
		} else {
			$this->session->data['success'] = $this->language->get('text_success_deleted');
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->redirect($this->html->getSecureURL('design/blocks'));
	}

	private function _getHTMLForm() {

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
		$this->document->addBreadcrumb(array('href' => $this->html->getSecureURL('design/blocks'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '));

		$this->data ['cancel'] = $this->html->getSecureURL('design/blocks');
		$this->document->addScript(RDIR_TEMPLATE . 'javascript/ckeditor/ckeditor.js');


		if (!isset ($this->request->get ['custom_block_id'])) {
			$this->data ['action'] = $this->html->getSecureURL('design/blocks/insert');
			$this->data ['heading_title'] = $this->language->get('text_create');
			$this->data ['update'] = '';
			$form = new AForm ('ST');
		} else {
			$this->data ['action'] = $this->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $this->request->get ['custom_block_id']);
			$this->data ['heading_title'] = $this->language->get('text_edit') . ' ' . $this->data['name'];
			$this->data ['update'] = $this->html->getSecureURL('listing_grid/blocks_grid/update_field', '&custom_block_id=' . $this->request->get ['custom_block_id']);
			$form = new AForm ('HS');
		}

		$this->document->addBreadcrumb(array('href' => $this->data['action'],
				'text' => $this->data['heading_title'],
				'separator' => ' :: ',
				'current' => true
		));

		$form->setForm(array('form_name' => 'BlockFrm', 'update' => $this->data ['update']));

		$this->data['form']['form_open'] = $form->getFieldHtml(array('type' => 'form',
				'name' => 'BlockFrm',
				'attr' => 'data-confirm-exit="true"',
				'action' => $this->data ['action']));
		$this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_save'), 'style' => 'button1'));
		$this->data['form']['cancel'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel'), 'style' => 'button2'));


		if (isset($this->request->get['custom_block_id'])) {
			$this->data['form']['fields']['block_status'] = $form->getFieldHtml(array('type' => 'checkbox',
					'name' => 'block_status',
					'value' => $this->data['status'],
					'style' => 'btn_switch status_switch'));
			$this->data['entry_block_status'] = $this->html->convertLinks($this->language->get('entry_block_status'));
			$this->data['form']['fields']['block_status_note'] = '';
			$this->data['entry_block_status_note'] = $this->html->convertLinks($this->language->get('entry_block_status_note'));
		}

		$lm = new ALayoutManager();
		foreach ($this->data['custom_block_types'] as $txt_id) {
			$block = $lm->getBlockByTxtId($txt_id);
			if ($block['block_id']) {
				$blocks[$block['block_id']] = $this->language->get('text_' . $txt_id);
				if ($txt_id == 'html_block') {
					$default_block_type = $block['block_id'];
				}
			}
		}

		$this->data['form']['fields']['block_name'] = $form->getFieldHtml(array('type' => 'hidden',
				'name' => 'block_id',
				'value' => $default_block_type));
		$this->data['form']['fields']['block_name'] .= $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'block_name',
				'value' => $this->data['name'],
				'required' => true,
				'multilingual' => true,
		));
		$this->data['form']['text']['block_name'] = $this->language->get('entry_block_name');

		$this->data['form']['fields']['block_title'] = $form->getFieldHtml(array('type' => 'input',
				'name' => 'block_title',
				'required' => true,
				'value' => $this->data ['title'],
				'multilingual' => true,
		));
		$this->data['form']['text']['block_title'] = $this->language->get('entry_block_title');

		// list of templates for block
		$tmpl_ids = $this->extensions->getInstalled('template');
		$tmpl_ids[] = 'default';

		$this->data['block_wrappers'] = array();
		foreach ($tmpl_ids as $tmpl_id) {
			// for tpls of block that stores in db
			$layout_manager = new ALayoutManager($tmpl_id);
			$block = $layout_manager->getBlockByTxtId('html_block');
			$block_templates = (array)$layout_manager->getBlockTemplates($block['block_id']);
			foreach ($block_templates as $item) {
				if ($item['template']) {
					$this->data['block_wrappers'][$item['template']] = $item['template'];
				}
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
					if (isset($this->data['block_wrappers'][$tpl]) || strpos($tpl, 'blocks/html__block/') === false) {
						continue;
					}
					$this->data['block_wrappers'][$tpl] = $tpl;
				}
			}
		}

		$tpls = glob(DIR_STOREFRONT . 'view/*/template/blocks/html_block/*.tpl');
		foreach ($tpls as $tpl) {
			$pos = strpos($tpl, 'blocks/html_block/');
			$tpl = substr($tpl, $pos);
			if (!isset($this->data['block_wrappers'][$tpl])) {
				$this->data['block_wrappers'][$tpl] = $tpl;
			}
		}

		ksort($this->data['block_wrappers']);
		array_unshift($this->data['block_wrappers'], $this->language->get('text_automatic'));

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

		$this->data['form']['fields']['block_content'] = $form->getFieldHtml(array('type' => 'textarea',
				'name' => 'block_content',
				'value' => $this->data ['content'],
				'multilingual' => true,
		));
		$this->data['form']['text']['block_content'] = $this->language->get('entry_block_content');

		$this->view->batchAssign($this->language->getASet());
		$this->view->batchAssign($this->data);
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_code', $this->session->data['language']);
		$this->view->assign('help_url', $this->gen_help_url('block_edit'));

		$this->addChild('responses/common/resource_library/get_resources_html', 'resources_html', 'responses/common/resource_library_scripts.tpl');
		$resources_scripts = $this->dispatch(
				'responses/common/resource_library/get_resources_scripts',
				array(
						'object_name' => '',
						'object_id' => '',
						'types' => array('image'),
				)
		);
		$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
		$this->view->assign('rl', $this->html->getSecureURL('common/resource_library', '&action=list_library&object_name=&object_id&type=image&mode=single'));

		$this->processTemplate('pages/design/blocks_form.tpl');
	}


	private function _getListingForm() {

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

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('design/blocks'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: '));


		$this->data ['cancel'] = $this->html->getSecureURL('design/blocks');

		if (!isset ($this->request->get ['custom_block_id'])) {
			$this->data ['action'] = $this->html->getSecureURL('design/blocks/insert');
			$this->data ['heading_title'] = $this->language->get('text_create');
			$this->data ['update'] = '';
			$form = new AForm ('ST');
		} else {
			$this->data ['action'] = $this->html->getSecureURL('design/blocks/edit', '&custom_block_id=' . $this->request->get ['custom_block_id']);
			$this->data ['heading_title'] = $this->language->get('text_edit') . ' ' . $this->data['name'];
			$this->data ['update'] = $this->html->getSecureURL('listing_grid/blocks_grid/update_field', '&custom_block_id=' . $this->request->get ['custom_block_id']);
			$form = new AForm ('HS');
		}

		$this->document->addBreadcrumb(array('href' => $this->data['action'],
				'text' => $this->data['heading_title'],
				'separator' => ' :: ',
				'current' => true
		));

		$form->setForm(array('form_name' => 'BlockFrm', 'update' => $this->data ['update']));

		$this->data['form']['form_open'] = $form->getFieldHtml(array('type' => 'form',
				'name' => 'BlockFrm',
				'attr' => 'data-confirm-exit="true"',
				'action' => $this->data ['action']));
		$this->data['form']['submit'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_save'),
				'style' => 'button1'));
		$this->data['form']['cancel'] = $form->getFieldHtml(array('type' => 'button',
				'name' => 'cancel',
				'text' => $this->language->get('button_cancel'),
				'style' => 'button2'));


		if (isset($this->request->get['custom_block_id'])) {
			$this->data['form']['fields']['block_status'] = $form->getFieldHtml(array('type' => 'checkbox',
					'name' => 'block_status',
					'value' => $this->data['status'],
					'style' => 'btn_switch status_switch'
			));
			$this->data['form']['text']['block_status'] = $this->html->convertLinks($this->language->get('entry_block_status'));
			$this->data['form']['fields']['block_status_note'] = '';
			$this->data['form']['text']['block_status_note'] = $this->html->convertLinks($this->language->get('entry_block_status_note'));
		}

		$lm = new ALayoutManager();
		foreach ($this->data['custom_block_types'] as $txt_id) {
			$block = $lm->getBlockByTxtId($txt_id);
			if ($block['block_id']) {
				$blocks[$block['block_id']] = $this->language->get('text_' . $txt_id);
				if ($txt_id == 'listing_block') {
					$default_block_type = $block['block_id'];
				}
			}
		}

		if (isset($this->request->get['custom_block_id'])) {
			// need to khow what type of listing is that
			$this->data['content'] = unserialize($this->data['content']);
			$this->data['autoload'] = 'load_subform({\'listing_datasource\': \'' . $this->data['content']['listing_datasource'] . '\'});';
		}

		$this->data['form']['fields']['block_name'] = $form->getFieldHtml(array(
				'type' => 'hidden',
				'name' => 'block_id',
				'value' => $default_block_type));
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
			$block = $layout_manager->getBlockByTxtId('listing_block');
			$block_templates = (array)$layout_manager->getBlockTemplates($block['block_id']);
			foreach ($block_templates as $item) {
				if ($item['template']) {
					$this->data['block_wrappers'][$item['template']] = $item['template'];
				}
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
					if (isset($this->data['block_wrappers'][$tpl]) || strpos($tpl, 'blocks/listing_block/') === false) {
						continue;
					}
					$this->data['block_wrappers'][$tpl] = $tpl;
				}
			}
		}


		$tpls = glob(DIR_STOREFRONT . 'view/*/template/blocks/listing_block/*.tpl');
		foreach ($tpls as $tpl) {
			$pos = strpos($tpl, 'blocks/listing_block/');
			$tpl = substr($tpl, $pos);
			if (!isset($this->data['block_wrappers'][$tpl])) {
				$this->data['block_wrappers'][$tpl] = $tpl;
			}
		}

		ksort($this->data['block_wrappers']);
		array_unshift($this->data['block_wrappers'], $this->language->get('text_automatic'));

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

		$listing_manager = new AListingManager((int)$this->request->get ['custom_block_id']);
		$listing_datasources = array('' => array('text' => 'text_select_listing'));
		$listing_datasources = array_merge($listing_datasources, $listing_manager->getListingDataSources());
		foreach ($listing_datasources as $k => $v) {
			$listing_datasources[$k] = $this->language->get($v['text']);
		}

		$default_listing_datasource = $this->data['content']['listing_datasource'];

		$this->data['form']['fields']['listing_datasource'] = $form->getFieldHtml(array('type' => 'selectbox',
				'name' => 'listing_datasource',
				'options' => $listing_datasources,
				'value' => $default_listing_datasource,
				'style' => 'no-save',
				));
		$this->data['form']['text']['listing_datasource'] = $this->language->get('entry_listing_datasource');

		if (!isset($this->data['subform_url'])) {
			$this->data['subform_url'] = $this->html->getSecureURL('listing_grid/blocks_grid/getsubform', ($this->request->get['custom_block_id']
					? '&custom_block_id=' . $this->request->get['custom_block_id'] : ''));
		}

		$this->view->batchAssign($this->language->getASet());
		$this->view->batchAssign($this->data);
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_code', $this->session->data['language']);
		$this->view->assign('help_url', $this->gen_help_url('block_edit'));
		$this->view->assign('rl', $this->html->getSecureURL('common/resource_library', '&object_name=custom_block&type=image&mode=url'));

		$this->processTemplate('pages/design/blocks_form_listing.tpl');
	}

	private function _validateForm() {
		if (!$this->user->canModify('design/blocks')) {
			$this->session->data['warning'] = $this->error ['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post) {
			$required = array('block_name', 'block_title');
			// if insert - add block_id (custom block type) in check array
			if (!isset($this->request->get['custom_block_id'])) {
				$required[] = 'block_id';
			}

			foreach ($this->request->post as $name => $value) {
				if (in_array($name, $required) && empty($value)) {
					$this->error ['warning'] = $this->language->get('error_empty');
					$this->session->data['warning'] = $this->language->get('error_empty');
					break;
				}
			}
		}

		foreach ($required as $name) {
			if (!in_array($name, array_keys($this->request->post))) {

				return false;
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