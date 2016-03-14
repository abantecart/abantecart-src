<?php   
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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
		$this->view->assign('language_code', $this->session->data['language']);

		$retina = $this->config->get('config_retina_enable');
		$this->view->assign('retina', $retina);
		//remove cookie for retina
		if(!$retina){
			$this->request->deleteCookie('HTTP_IS_RETINA');
		}

		$this->view->assign('message_manager_url', $message_link);

		if( $this->session->data['checkupdates'] ){
			$this->view->assign('check_updates_url', $this->html->getSecureURL('r/common/common/checkUpdates'));
		}

		$this->view->assign('icon', $this->config->get('config_icon'));

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		    $this->view->assign('ssl', 1);
        }
		
		$this->processTemplate('common/head.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}