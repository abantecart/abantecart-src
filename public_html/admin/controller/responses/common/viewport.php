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
class ControllerResponsesCommonViewPort extends AController {

	public function main(){
		$this->modal();
	}

  	public function modal() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $page_rt = $this->request->get['viewport_rt'];

	    $output = '';
	    if( $page_rt ){
		    $page_rt = preg_replace('/^p\//', '', $page_rt);
		    //send sign about viewport mode via arguments to use it for template selection
		    $dd = new ADispatcher('pages/'.$page_rt, array(array('viewport_mode' => 'modal')) );
		    $output = $dd->dispatchGetOutput($page_rt);
	    }else{
		    $err = new AError('Viewport Router Error! Request Params are: '.var_export($this->request->get, true));
		    $err->toLog()->toDebug();
	    }

	    $this->view->assign('content',$output);
	    $this->processTemplate('responses/common/viewport_modal.tpl');
  	}
}