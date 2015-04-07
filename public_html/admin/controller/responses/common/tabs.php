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
class ControllerResponsesCommonTabs extends AController {
	private $error = array();
	public $data = array();
	public $parent_controller = ''; //rt of page where you plan to place tabs

  	public function main($parent_controller,$data) {
        $this->data = $data;
        $this->parent_controller = $parent_controller; //use it in hooks to recognize what page controller calls
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $tabs = (array)$this->data['tabs'];
	    $this->data['tabs'] = $idx = array();
	    foreach($tabs as $k=>$tab){
		    $idx[] = (int)$tab['sort_order'];
	    }

	    array_multisort($idx,SORT_ASC,$tabs);
		$this->data['tabs'] = $tabs;

		$this->view->batchAssign( $this->data );
		$this->processTemplate('responses/common/tabs.tpl');
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

