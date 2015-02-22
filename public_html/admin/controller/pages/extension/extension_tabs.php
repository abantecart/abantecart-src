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
class ControllerPagesExtensionExtensionTabs extends AController {

	public $data = array();
     
  	public function main() {

        //Load input argumets for gid settings
        $this->data = func_get_arg(0);

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('extension/extensions');

	    $groups = (array)$this->data['groups'];
		array_unshift($groups,'general');
	    $this->data['groups'] = $groups;
		$this->data['link_general'] = $this->html->getSecureURL('p/extension/extensions/edit', '&extension='.$this->request->get['extension']);

	    $this->data['active'] = $this->data['active_group'];
	    $this->data['active'] = !$this->data['active'] ? current($groups) : $this->data['active'];

	    $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/extension_tabs.tpl');

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

