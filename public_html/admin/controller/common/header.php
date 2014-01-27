<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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

/** @noinspection PhpUndefinedClassInspection */
class ControllerCommonHeader extends AController {
	public function main() {

		//use to init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->load->helper('html');

		$this->view->assign('breadcrumbs', $this->document->getBreadcrumbs());

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['language_code'])) {
			unset($this->session->data['content_language']);
			$this->session->data['language'] = $this->request->post['language_code'];
			$this->cache->delete('admin_menu');

			if (isset($this->request->post['redirect'])) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->html->getURL('index/home'));
			}
		}

		$this->view->assign('language_code', $this->session->data['language']);
		$this->view->assign('languages', array());
		$this->view->assign('languages', $this->language->getActiveLanguages());
		$this->view->assign('content_language_id', $this->language->getContentLanguageID());

		$new_messages = array();
		if (isset($this->session->data['new_messages']) && sizeof($this->session->data['new_messages']) > 0) {
			foreach ($this->session->data['new_messages'] as $key => $value) {
				$new_messages[$key] = $value > 0 ? $value : null;
			}
		}
		$this->view->assign('new_messages', $new_messages);
		$this->view->assign('messages_link', $this->html->getSecureURL('tool/message_manager'));

		$this->view->assign('action', $this->html->getSecureURL('index/home'));
		$this->view->assign('search_action', $this->html->getSecureURL('tool/global_search'));
		//redirect after language change
		if (!isset($this->request->get['rt'])) {
			$this->view->assign('redirect', $this->html->getSecureURL('index/home'));
		} else {
			$this->view->assign('redirect', $this->html->currentURL());
		}

		if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			$this->view->assign('logged', '');
			$this->view->assign('home', $this->html->getSecureURL('index/login', '', true));
		} else {
			$this->view->assign('home', $this->html->getSecureURL('index/home', '', true));
			$this->view->assign('logged', sprintf($this->language->get('text_logged'), $this->user->getUserName()));

			$stores = array();
			$this->loadModel('setting/store');
			$results = $this->model_setting_store->getStores();
			foreach ($results as $result) {
				$stores[] = array(
					'name' => $result['name'],
					'href' => $result['config_url']
				);
			}
			$this->view->assign('stores', $stores);

			$this->view->assign('logout', $this->html->getSecureURL('index/logout'));
			$this->view->assign('store', HTTP_CATALOG);
			// add dynamic menu based on dataset scheme
			$this->addChild('common/menu', 'menu', 'common/menu.tpl');
		}
		if ($this->user->isLogged()) {
			$this->view->assign('ant', $this->messages->getANTMessage());
		}
		$this->view->assign('config_voicecontrol', $this->config->get('config_voicecontrol'));
		$this->view->assign('voicecontrol_setting_url', $this->html->getSecureURL('setting/setting/system'));
		$this->view->assign('command_lookup_url', $this->html->getSecureURL('common/action_commands'));
		$this->view->assign('search_suggest_url', $this->html->getSecureURL('listing_grid/global_search_result/suggest'));
		$this->view->assign('search_everywhere', $this->language->get('search_everywhere'));
		$this->view->assign('text_all_matches', $this->language->get('text_all_matches'));
		$this->view->assign('dialog_title', $this->language->get('text_quick_edit_form'));
		$this->view->assign('button_go', $this->html->buildButton(array('name' => 'searchform_go', 'text' => $this->language->get('button_go'), 'style' => 'button5')));

		//check install dir existing
		if (file_exists(DIR_ROOT . '/install')) {
			$this->messages->saveWarning($this->language->get('text_install_warning_subject'), $this->language->get('text_install_warning'));
		}
		$this->processTemplate('common/header.tpl');
		//use to update data before render
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}
