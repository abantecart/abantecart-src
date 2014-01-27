<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerDefaultGoogleTalkDefaultGoogleTalk extends AController {
	public function main() {
		$code = html_entity_decode( $this->config->get('default_google_talk_code') );
		if(!$code){
			return;
		}
		$this->loadLanguage('default_google_talk/default_google_talk');
		$this->view->assign( 'heading_title', $this->language->get('heading_title'));
        $this->view->assign( 'code', $code );
		$this->processTemplate();

	}
}
?>