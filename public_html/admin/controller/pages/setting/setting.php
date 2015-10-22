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
 * Class ControllerPagesSettingSetting
 * @property AConfigManager $conf_mngr
 */
class ControllerPagesSettingSetting extends AController {
	public $error = array();
	public $groups = array();
	public $data = array();

    /**
     * @param Registry $registry
     * @param int $instance_id
     * @param string $controller
     * @param string $parent_controller
     */
    public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		//load available groups for settings
		$this->groups = $this->config->groups;
	}

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));
		$group = $this->request->get['active'];
		if ($this->request->is_POST() && $this->_validate($this->request->get['active'])) {
			if (has_value($this->request->post['config_logo'])) {
				$this->request->post['config_logo'] = html_entity_decode($this->request->post['config_logo'], ENT_COMPAT, 'UTF-8');
			} else if(!$this->request->post['config_logo'] && isset($this->request->post['config_logo_resource_id'])) {
				//we save resource ID vs resource path
				$this->request->post['config_logo'] = $this->request->post['config_logo_resource_id'];
			} 
			if (has_value($this->request->post['config_icon'])) {
				$this->request->post['config_icon'] = html_entity_decode($this->request->post['config_icon'], ENT_COMPAT, 'UTF-8');
			} else if(!$this->request->post['config_icon'] && isset($this->request->post['config_icon_resource_id'])) {
				//we save resource ID vs resource path
				$this->request->post['config_icon'] = $this->request->post['config_icon_resource_id'];
			}
			//html decode store name
			if (has_value($this->request->post['store_name'])) {
				$this->request->post['store_name'] = html_entity_decode($this->request->post['store_name'], ENT_COMPAT, 'UTF-8');
			}

			$this->model_setting_setting->editSetting( $group, $this->request->post, $this->request->get['store_id']);
			if ($this->config->get('config_currency_auto')) {
				$this->loadModel('localisation/currency');
				$this->model_localisation_currency->updateCurrencies();
			}

			$this->session->data['success'] = $this->language->get('text_success');
            if(has_value($this->request->post['config_maintenance']) && $this->request->post['config_maintenance']){
                //mark storefront session as merchant session
                startStorefrontSession($this->user->getId());
            }
			$redirect_url = $this->html->getSecureURL('setting/setting',
					'&active=' . $this->request->get['active'] . '&store_id=' . (int)$this->request->get['store_id']);
			$this->redirect($redirect_url);
		}

		$this->data['store_id'] = 0;
		if ($this->request->get['store_id']) {
			$this->data['store_id'] = $this->request->get['store_id'];
		} else {
			$this->data['store_id'] = $this->session->data['current_store_id'];
		}


		$this->data['groups'] = $this->groups;
		if (isset($this->request->get['active']) && strpos($this->request->get['active'], '-') !== false) {
			$this->request->get['active'] = substr($this->request->get['active'], 0, strpos($this->request->get['active'], '-'));
		}
		$this->data['active'] = isset($this->request->get['active']) && in_array($this->request->get['active'], $this->data['groups']) ?
			$this->request->get['active'] : $this->data['groups'][0];


		$this->data['token'] = $this->session->data['token'];
		$this->data['error'] = $this->error;

		$this->document->initBreadcrumb(array(
			'href' => $this->html->getSecureURL('index/home'),
			'text' => $this->language->get('text_home'),
			'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
			'href' => $this->html->getSecureURL('setting/setting'),
			'text' => $this->language->get('heading_title'),
			'separator' => ' :: ',
			'current'	=> true
		));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->session->data['error'])) {
			$this->error['warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		}

		$this->data['new_store_button'] = $this->html->buildElement(array(
			'type' => 'button',
			'title' => $this->language->get('button_add_store'),
			'text' => '&nbsp;',
			'href' => $this->html->getSecureURL('setting/store/insert'),
		));

		if($group=='system'){
			$this->data['phpinfo_button'] = $this->html->buildElement(array(
				'type' => 'button',
				'title' => 'PHP Info',
				'href' => $this->html->getSecureURL('setting/setting/phpinfo'),
			));
		}

		if ($this->data['store_id'] > 0) {
			$this->data['edit_store_button'] = $this->html->buildElement(array(
				'type' => 'button',
				'title' => $this->language->get('text_edit_store'),
				'text' => $this->language->get('text_edit_store'),
				'href' => $this->html->getSecureURL('setting/store/update', '&store_id=' . $this->data['store_id']),
				'style' => 'button2',
			));
		}

		$this->data['cancel'] = $this->html->getSecureURL('setting/setting');
		$this->data['action'] = $this->html->getSecureURL('setting/setting');

		require_once(DIR_CORE.'lib/config_manager.php');
		$this->conf_mngr = new AConfigManager();

		//activate quick start guide button
		$this->loadLanguage('common/quick_start');
		$this->data['quick_start_url'] = $this->html->getSecureURL('setting/setting_quick_form/quick_start');
		
		$group = $this->data['active'];

		if($this->data['active']=='appearance'){
			$this->data['manage_extensions'] = $this->html->buildElement(
					array(
							'type' => 'button',
							'name' => 'manage_extensions',
							'href' => $this->html->getSecureURL('extension/extensions/template'),
							'text' => $this->language->get('button_manage_extensions'),
							'title' => $this->language->get('button_manage_extensions')
					));
		}

		$this->data['settings'] = $this->model_setting_setting->getSetting($group, $this->data['store_id']);
		unset($this->data['settings']['one_field']); //remove sign of single form field
		foreach ($this->data['settings'] as $key => $value) {
			if (isset($this->request->post[$key])) {
				$this->data['settings'][$key] = $this->request->post[$key];
			}
		}
		$this->loadModel('setting/store');
		$store_info = $this->model_setting_store->getStore($this->data['store_id']);
		$this->data['status_off'] = '';
		if(!$store_info['status']) {
			$this->data['status_off'] = 'status_off';
		}

		$this->_getForm();

		$this->data['content_language_id'] = $this->session->data['content_language_id'];
		$this->data['common_zone'] = $this->html->getSecureURL('common/zone');
		$this->data['template_image'] = $this->html->getSecureURL('setting/template_image');

		//load tabs controller
		$tabs_obj = $this->dispatch('pages/setting/setting_tabs', array( $this->data ) );
		$this->data['setting_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());
		$this->view->assign('help_url', $this->gen_help_url($this->data['active']));
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/setting/setting.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function all() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->view->assign('error_warning', $this->session->data['warning']);
		if (isset($this->session->data['warning'])) {
			unset($this->session->data['warning']);
		}
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
			'href' => $this->html->getSecureURL('setting/setting'),
			'text' => $this->language->get('heading_title'),
			'separator' => ' :: ',
			'current'	=> true
		));

		$grid_settings = array(
			'table_id' => 'setting_grid',
			'url' => $this->html->getSecureURL('listing_grid/setting'),
			'editurl' => '',
			'update_field' => '',
			'sortname' => 'group',
			'sortorder' => 'asc',
			'multiselect' => "false",
			'actions' => array(
				'edit' => array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->html->getSecureURL('setting/setting_quick_form',
														'&target=edit_dialog&active=%ID%')
				),
			),
			'grid_ready' => 'grid_ready(data);'
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_store_alias'),
			$this->language->get('column_group'),
			$this->language->get('column_key'),
			$this->language->get('column_value'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'store_alias',
				'index' => 'alias',
				'align' => 'center',
				'width' => 110,
			),
			array(
				'name' => 'group',
				'index' => 'group',
				'align' => 'center',
				'width' => 120,
			),
			array(
				'name' => 'key',
				'index' => 'key',
				'align' => 'left',
				'width' => 260,
			),
			array(
				'name' => 'value',
				'index' => 'value',
				'align' => 'center',
				'sortable' => false,
				'search' => false,
			),
		);

		$form = new AForm();
		$form->setForm(array(
			'form_name' => 'setting_grid_search',
		));

		$grid_settings['search_form'] = true;

		$this->data['insert'] = $this->html->buildElement(
				array( 'type' => 'button',
					   'title' => $this->language->get('button_add_store'),
					   'text' => $this->language->get('button_insert'),
					   'href' => $this->html->getSecureURL('setting/store/insert')
		));

		$this->loadModel('setting/store');
		$results = $this->model_setting_store->getStores();
		$stores = array();
		$stores[0] = $this->language->get('text_default');
		foreach ($results as $result) {
			$stores[$result['store_id']] = $result['alias'];
		}


		//load tabs controller
		$this->data['active'] = 'all';
		$tabs_obj = $this->dispatch('pages/setting/setting_tabs', array( $this->data ) );
		$this->data['setting_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		// include rl-scripts for quick edit logo and icon of store from modal
		$resources_scripts = $this->dispatch(
			'responses/common/resource_library/get_resources_scripts',
			array(
				'object_name' => 'store',
				'object_id' => (int)$this->request->get['store_id'],
				'types' => array('image'),
				'onload' => true,
				'mode' => 'single'
			)
		);
		$this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();
		$this->data['content_language_id'] = $this->language->getContentLanguageID();

		//activate quick start guide button
		$this->loadLanguage('common/quick_start');
		$this->data['quick_start_url'] = $this->html->getSecureURL('setting/setting_quick_form/quick_start');
		
		$this->view->batchAssign($this->data);
		$this->view->assign('help_url', $this->gen_help_url('setting_listing'));

		$this->processTemplate('pages/setting/setting_list.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function details () {
		$this->request->get['active'] = 'details';
		$this->main();
	}

	public function general () {
		$this->request->get['active'] = 'general';
		$this->main();
	}

	public function checkout () {
		$this->request->get['active'] = 'checkout';
		$this->main();
	}

	public function appearance () {
		$this->request->get['active'] = 'appearance';
		$this->main();
	}

	public function mail () {
		$this->request->get['active'] = 'mail';
		$this->main();
	}

	public function api () {
		$this->request->get['active'] = 'api';
		$this->main();
	}

	public function system () {
		$this->request->get['active'] = 'system';
		$this->main();
	}

	private function _getForm() {

		$this->data['action'] = $this->html->getSecureURL('setting/setting',
				'&active=' . $this->data['active'] . '&store_id=' . $this->data['store_id']);
		$this->data['form_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('heading_title');
		$this->data['update'] = $this->html->getSecureURL('listing_grid/setting/update_field',
				'&group=' . $this->data['active'] . '&store_id=' . $this->data['store_id']);
		$this->view->assign('language_code', $this->session->data['language']);
		$form = new AForm('HS');

		$form->setForm(array(
			'form_name' => 'settingFrm',
			'update' => $this->data['update'],
		));

		$this->data['form']['id'] = 'settingFrm';
		$this->data['form']['form_open'] = $form->getFieldHtml(array(
			'type' => 'form',
			'name' => 'settingFrm',
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

		//need resource script on every page for quick start
		$resources_scripts = $this->dispatch(
		    'responses/common/resource_library/get_resources_scripts',
		    array(
		    	'object_name' => 'store',
		    	'object_id' => (int)$this->data['store_id'],
		    	'types' => array('image'),
		    	'onload' => true,
		    	'mode' => 'single'
		    )
		);
		$this->data['resources_scripts'] = $resources_scripts->dispatchGetOutput();

		switch ($this->data['active']) {
			case 'details':
				$this->data = array_merge_recursive($this->data, $this->_build_details($form, $this->data['settings']));
				break;
			case 'general' :
				$this->data = array_merge_recursive($this->data, $this->_build_general($form, $this->data['settings']));
				break;
			case 'checkout':
				$this->data = array_merge_recursive($this->data, $this->_build_checkout($form, $this->data['settings']));
				break;
			case 'appearance' :
				$this->data = array_merge_recursive($this->data, $this->_build_appearance($form, $this->data['settings']));
				break;
			case 'mail' :
				$this->data = array_merge_recursive($this->data, $this->_build_mail($form, $this->data['settings']));
				break;
			case 'api' :
				$this->data = array_merge_recursive($this->data, $this->_build_api($form, $this->data['settings']));
				break;
			case 'system':
				$this->data = array_merge_recursive($this->data, $this->_build_system($form, $this->data['settings']));
				break;
			default:
		}
	}

    /**
     * @param AForm $form
     * @param array $data
     * @return array
     */
    private function _build_details($form, $data) {
		$ret_data = array();
		$ret_data['form_language_switch'] = $this->html->getContentLanguageSwitcher();
		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('details', $form, $data));
		return $ret_data;
	}

    /**
     * @param AForm $form
     * @param $data
     * @return array
     */
    private function _build_general($form, $data) {
		$ret_data = array();
		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('general', $form, $data));
		return $ret_data;
	}
    /**
     * @param AForm $form
     * @param $data
     * @return array
     */
	private function _build_checkout($form, $data) {
		$ret_data = array();
		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('checkout', $form, $data));
		return $ret_data;
	}
    /**
     * @param AForm $form
     * @param $data
     * @return array
     */
	private function _build_appearance($form, $data) {
		$ret_data = array();

		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('appearance', $form, $data));

		return $ret_data;
	}
    /**
     * @param AForm $form
     * @param $data
     * @return array
     */
	private function _build_mail($form, $data) {
		$ret_data = array();
		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('mail', $form, $data));
		return $ret_data;
	}
    /**
     * @param AForm $form
     * @param $data
     * @return array
     */
	private function _build_api($form, $data) {
		$ret_data = array();
		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('api', $form, $data));
		return $ret_data;
	}

    /**
     * @param AForm $form
     * @param $data
     * @return array
     */
	private function _build_system($form, $data) {
		$ret_data = array();

		if ($data['storefront_template_debug']) {
			$this->session->data['tmpl_debug'] = AEncryption::getHash(mt_rand());
			$ret_data['storefront_debug_url'] = $this->html->getCatalogURL('index/home', '&tmpl_debug=' . $this->session->data['tmpl_debug']);
			$ret_data['admin_debug_url'] = $this->html->getSecureURL('index/home', '&tmpl_debug=' . $this->session->data['tmpl_debug']);
		} else {
			unset($this->session->data['tmpl_debug']);
			$ret_data['storefront_debug_url'] = '';
			$ret_data['admin_debug_url'] = '';
		}

		$ignore = array(
			'common/login',
			'common/logout',
			'error/not_found',
			'error/permission'
		);

		$ret_data['tokens'] = array();

		$files_pages = glob(DIR_APP_SECTION . 'controller/pages/*/*.php');
		$files_response = glob(DIR_APP_SECTION . 'controller/responses/*/*.php');
		$files = array_merge($files_pages, $files_response);

		foreach ($files as $file) {
			$tmp_data = explode('/', dirname($file));
			$token = end($tmp_data) . '/' . basename($file, '.php');
			if (!in_array($token, $ignore)) {
				$ret_data['tokens'][$token] = $token;
			}
		}

		$ret_data['form'] = array('fields' => $this->conf_mngr->getFormFields('system', $form, $data));

		return $ret_data;
	}

    /**
     * @param string $group
     * @return bool
     */
    private function _validate($group) {
		if (!$this->user->canModify('setting/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->library('config_manager');
		$config_mngr = new AConfigManager();
		$result = $config_mngr->validate($group, $this->request->post);
		$this->error = $result['error'];
		$this->request->post = $result['validated']; // for changed data saving

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

	public function phpinfo(){
		phpinfo();
		exit;
	}

}
