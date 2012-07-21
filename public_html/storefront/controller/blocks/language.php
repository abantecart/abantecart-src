<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerBlocksLanguage extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['language_code'])) {
			$this->session->data['language'] = $this->request->post['language_code'];
		
			if (isset($this->request->post['redirect'])) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->html->getURL('index/home'));
			}
    	}

      	$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['language_code'] = $this->session->data['language'];
		
		$this->data['languages'] = $this->language->getActiveLanguages();

		$URI = $_SERVER['REQUEST_URI'];
		$query_vars =  explode('&',$_SERVER['QUERY_STRING']);
		foreach ($query_vars as $pair){
			$URI = str_replace('&'.$pair,'',$URI);
			$URI = str_replace('?'.$pair,'',$URI);
		}
		$get_vars = $this->request->get;
		unset($get_vars['language']);
		if(isset($get_vars['product_id'])){
			unset($get_vars['path']);
		}

		$URI = str_replace('?', '', $URI);
		$URI .= '?'.urldecode(http_build_query($get_vars));

		foreach($this->data['languages'] as &$lang){
			$lang['href'] = $URI.'&language='.$lang['code'];
		}




		$this->view->batchAssign($this->data);
		$this->processTemplate('blocks/language.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>