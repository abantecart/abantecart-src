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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerCommonFooter extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('common/header');

		$menu = new AMenu('admin','menu');
		$documentation = $menu -> getMenuItem('documentation');
		$documentation = '<a onclick="'.$documentation['item_url'].'">'.$this->language->get($documentation['item_text']).'</a>';
		$support = $menu -> getMenuItem('support');
		$support = '<a onclick="'.$support['item_url'].'">'.$this->language->get($support['item_text']).'</a>';

		$this->view->assign('text_footer_left', sprintf($this->language->get('text_footer_left'), date('Y')));
		$this->view->assign('text_footer', sprintf($this->language->get('text_footer'),date('Y')).VERSION);
		$this->view->assign('text_footer_right', $documentation.$support);

		if (!$this->user->isLogged() || !isset($this->request->get['token']) || !isset($this->session->data['token']) || ($this->request->get['token'] != $this->session->data['token'])) {
			$this->view->assign('logged', '');
			$this->view->assign('home', $this->html->getSecureURL('index/login', '', true));
		} else {
			$this->view->assign('logged', sprintf($this->language->get('text_logged'), $this->user->getUserName()));
			$this->view->assign('avatar', $this->user->getAvatar());
			$this->view->assign('username', $this->user->getUserName());
			$this->view->assign('last_login', sprintf($this->language->get('text_last_login'), $this->user->getLastLogin()));
			$this->view->assign('account_edit', $this->html->getSecureURL('index/edit_details', '', true));
		}

		$this->processTemplate('common/footer.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
?>