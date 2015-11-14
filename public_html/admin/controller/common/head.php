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
class ControllerCommonHead extends AController {
	public function main(){

		//use to init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->load->helper('html');
		$this->loadLanguage('common/header');

		$message_link = $this->html->getSecureURL('tool/message_manager');

		$this->view->assign('title', $this->document->getTitle());
		$this->view->assign('base', (HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER);
		$this->view->assign('links', $this->document->getLinks());
		$this->view->assign('styles', $this->document->getStyles());
		$this->view->assign('scripts', $this->document->getScripts());
		$this->view->assign('notifier_updater_url', $this->html->getSecureURL('listing_grid/message_grid/getnotifies'));
		$this->view->assign('system_checker_url', $this->html->getSecureURL('common/common/checksystem'));
		$this->view->assign('ck_rl_url', $this->html->getSecureURL('common/resource_library', '&type=image&mode=url'));
		$this->view->assign('language_code', $this->session->data['language']);
		$this->view->assign('retina', $this->config->get('config_retina_enable'));
		$this->view->assign('message_manager_url', $message_link);

		if( $this->session->data['checkupdates'] ){
			$this->view->assign('check_updates_url', $this->html->getSecureURL('r/common/common/checkUpdates'));
		}

		$icon_path = $this->config->get('config_icon');
		if( $icon_path){
			if(!is_file(DIR_RESOURCE.$this->config->get('config_icon'))){
				$this->messages->saveWarning('Check favicon.','Warning: please check favicon in your store settings. Current path is "'.DIR_RESOURCE.$this->config->get('config_icon').'" but file does not exists.');
				$icon_path ='';
			}
		}
		$this->view->assign('icon', $icon_path);
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		    $this->view->assign('ssl', 1);
        }
		
		$this->processTemplate('common/head.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}	
	
}