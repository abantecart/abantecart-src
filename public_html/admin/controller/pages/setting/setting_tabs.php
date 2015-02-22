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
class ControllerPagesSettingSettingTabs extends AController {

	public $data = array();
     
  	public function main() {

        //Load input argumets for gid settings
        $this->data = func_get_arg(0);
        if (!is_array($this->data)) {
            throw new AException (AC_ERR_LOAD, 'Error: Could not create grid. Grid definition is not array.');
        }
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('setting/setting');

		$this->data['groups'] = $this->config->groups;
		array_unshift($this->data['groups'],'all');

		$this->data['link_all'] = $this->html->getSecureURL('setting/setting/all');
		foreach ($this->data['groups'] as $group) {
			$this->data['link_' . $group] = $this->html->getSecureURL('setting/setting/' . $group, '&store_id=' . $this->data['store_id']);
		}

		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/setting/setting_tabs.tpl');

		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

