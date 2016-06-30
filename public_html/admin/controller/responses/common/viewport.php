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

  	public function main() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$output = '';
	    /*
	     *   _rt_ - request parameter goes from ARouter.
	     *  You can use it as sign inside requested page-controller to define what template use
	     * */
	    if( $this->request->get['_rt_'] ){
		    //remove viewport variable from request to prevent loop in ARouter
		    unset($this->request->get['viewport']);
		    $dd = new ADispatcher('pages/'.$this->request->get['_rt_']);
		    $output = $dd->dispatchGetOutput('pages/'.$this->request->get['_rt_']);
	    }else{
		    $err = new AError('Viewport Router Error! Request Params are: '.var_export($this->request->get, true));
		    $err->toLog()->toDebug();
	    }

	    $get = $this->request->get;
	    unset($get['_rt_'], $get['rt'], $get['s'], $get['token']);
	    $this->view->assign('full_mode_url', $this->html->getSecureURL($this->request->get['_rt_'], '&'.http_build_query($get)));
	    $this->view->assign('title','Preview');
	    $this->view->assign('content',$output);
	    $this->processTemplate('responses/common/viewport_modal.tpl');
  	}
}