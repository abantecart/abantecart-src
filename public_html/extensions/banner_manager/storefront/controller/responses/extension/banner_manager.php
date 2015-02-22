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
class ControllerResponsesExtensionBannerManager extends AController {
	public $data = array();
	
	public function main() {
    	//default controller function to register view or click
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$banner_id = (int)$this->request->get['banner_id'];
		//type of registered activity 1 = view and 2 = click
		$type = (int)$this->request->get['type'];

		if($banner_id){
			$this->loadModel('extension/banner_manager');
			$this->model_extension_banner_manager->writeBannerStat($banner_id,$type);
		}

		$output = array();
		$output['success'] = 'OK';
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($output));	
	}

	public function click() {
		//controller function to register click and redirect
		//NOTE: Work only for banners with target_url
		//For security reson, do not allow URL as parameter for this redirect
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$banner_id = (int)$this->request->get['banner_id'];
		$url = INDEX_FILE;
		//register click
		if($banner_id){
			$this->loadModel('extension/banner_manager');
			$banner = $this->model_extension_banner_manager->getBanner($banner_id, '');
			$url = $banner['target_url'];
			if ( empty($url) || $url[0] == '#') {
				$url = INDEX_FILE . $url;
			}
			$this->model_extension_banner_manager->writeBannerStat($banner_id,2);
		}
		//go to URL
		$this->redirect($url);

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}