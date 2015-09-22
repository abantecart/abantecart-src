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

/**
 * Class ControllerPagesToolUpdater
 * @property  ModelToolMPAPI $model_tool_mp_api
 */
class ControllerPagesToolUpdater extends AController {

	public $data;

	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('tool/updater');
		//refresh data from MP-server.
		$this->model_tool_updater->check4Updates(true);

		$this->document->setTitle( $this->language->get('heading_title') );
		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb( array ( 'href'=>$this->html->getSecureURL('index/home'),
		                                        'text'=>$this->language->get('text_home'),
		                                        'separator'=>FALSE  ));

		$this->document->addBreadcrumb( array ( 'href'=>$this->html->getSecureURL('tool/updater'),
		                                        'text'=>$this->language->get('heading_title'),
		                                        'separator'=>' :: ',
												'current' => true));

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_type'] = $this->language->get('column_type');
		$this->data['column_category'] = $this->language->get('column_category');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['column_version'] = $this->language->get('column_version');
		$this->data['column_new_version'] = $this->language->get('column_new_version');
		$this->data['error_warning'] = null;
		$this->data['text_nothing_todo'] = $this->data['success'] = '';
		
		if ( isset($this->session->data['success']) ) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if ( isset($this->session->data['error']) ) {
			$this->data['error_warning'] = is_array($this->session->data['error']) ? implode("</br>",$this->session->data['error']) : $this->session->data['error'];
			unset($this->session->data['error']);
		}
		
		$this->data['extensions'] = array();

		$mp_token = $this->config->get('mp_token');
		if ( !$mp_token ) {
			$this->data['mp_connected'] = false;
			$this->loadModel('tool/mp_api');

			$this->data['text_marketplace_connect'] = $this->language->get('text_marketplace_connect');
			$this->data['text_connect'] = $this->language->get('text_connect');
			$this->data['text_please_connect'] = $this->language->get('text_marketplace_connect_your_store');

			$return_url = base64_encode($this->html->getSecureURL('tool/extensions_store/connect'));
			$mp_params = '?rt=account/authenticate&return_url='.$return_url;
			$mp_params .= '&store_id='.UNIQUE_ID;
			$mp_params .= '&store_url='.HTTP_SERVER;
			$mp_params .= '&store_version='.VERSION;
			$this->data['amp_connect_url'] = $this->model_tool_mp_api->getMPURL().$mp_params;
			$this->data['amp_disconnect_url'] = $this->html->getSecureURL('tool/extensions_store/disconnect');

		}else{
			$this->data['mp_connected'] = true;
			$updates = $this->cache->get('extensions.updates');
			$this->data['extensions'] = array ();

			if (!empty($updates) && is_array($updates)){
				foreach ($updates as $key => $upd){
					$ext_info = $this->extensions->getExtensionInfo($key);
					$this->data['extensions'][$key]['installed_version'] = $ext_info['version'];
					$this->data['extensions'][$key]['new_version'] = $upd['version'];
					$this->data['extensions'][$key]['type'] = $ext_info['type'];
					$this->data['extensions'][$key]['category'] = $ext_info['category'];
					$this->data['extensions'][$key]['status'] = $this->html->buildCheckbox(array (
							'id'    => $key . '_status',
							'name'  => $key . '_status',
							'value' => $ext_info['status'],
							'style' => 'btn_switch btn-group-xs disabled',
							'attr'  => 'readonly="true" data-edit-url="' . $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $key) . '"'
					));

					$this->data['extensions'][$key]['mp_url'] = $upd['url'];
					if ($upd['installation_key']){
						$this->data['extensions'][$key]['install_url'] = $this->html->getSecureURL('tool/package_installer', '&extension_key=' . $upd['installation_key']);
					}
					$this->data['extensions'][$key]['name'] = $this->extensions->getExtensionName($key);
				}
			}

			if (!$this->data['extensions']){
				$this->data['text_nothing_todo'] = $this->language->get('text_nothing_todo');
			}
		}
		$this->view->assign('help_url', $this->gen_help_url() );
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/tool/updater.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
