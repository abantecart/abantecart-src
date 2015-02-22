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
class ControllerCommonPage extends AController {
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->view->assign('lang', $this->language->get('code'));
		$this->view->assign('direction', $this->language->get('direction'));
		$this->view->assign('scripts', $this->document->getScripts());
		
        $children = $this->getChildren();
        //save only first child - page content controller
        $this->setChildren( array($children[0]) );
		$this->addChild('common/head', 'head', 'common/head.tpl');
        $this->addChild('common/header', 'header', 'common/header.tpl');
        $this->addChild('common/footer', 'footer', 'common/footer.tpl');

		//build view for the children
		$this->view->assign("children_blocks", $this->getChildrenBlocks( ) );
		$this->view->assign("layout_width", $this->config->get('admin_width'));
		$this->processTemplate('common/page.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>