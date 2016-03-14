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

class ControllerPagesFinish extends AController {

	public function main() {

		if (!defined('DB_HOSTNAME')) {
			header('Location: index.php?rt=license');
			exit;
		}

		$this->session->data['finish'] = 'true';
		unset($this->session->data ['ant_messages']); // prevent reinstall bugs with ant

		$this->view->assign('admin_path', 'index.php?s=' . ADMIN_PATH);

		$message = "Keep your ecommmerce secure! <br /> Delete directory " . DIR_ABANTECART . "install from your AbanteCart installation!";
		$this->view->assign('message', $message);
		$this->view->assign('salt', SALT);

		$this->addChild('common/header', 'header', 'common/header.tpl');
		$this->addChild('common/footer', 'footer', 'common/footer.tpl');

		$this->processTemplate('pages/finish.tpl');
	}

}