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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesEmbedFooter extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage('common/header');
		$this->data['text_copy'] = $this->config->get('store_name') .' &copy; '. date('Y', time());
		
		if ($this->config->get('config_google_analytics_code')) {
			$this->data['google_analytics'] =  $this->config->get('config_google_analytics_code');
		} else {
			$this->data['google_analytics'] =  '';
		}
		
		$this->data['text_project_label'] = $this->language->get('text_powered_by') . ' ' . project_base();

		$this->view->assign('scripts_bottom', $this->document->getScriptsBottom());		

		$this->view->batchAssign($this->data);
		$this->processTemplate('embed/footer.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}