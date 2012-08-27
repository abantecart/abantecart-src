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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesErrorAjaxError extends AController {

	public function main() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('error/error');
		$ret_data = array(
			'error_title' => $this->language->get('heading_title'),
			'error_text' => $this->language->get('text_error'),
            'error_code' => 400,
		);

        $this->response->addheader('HTTP/1.1 400 ' . $this->language->get('heading_title'));
        $this->response->addheader('Content-Type: application/json');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($ret_data));
	}
	public function validation() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('error/error');
		$ret_data = array(
			'error_text' => func_get_arg(0),
            'error_code' => 406,
		);

        $this->response->addheader('HTTP/1.1 406 ' . $this->language->get('heading_title'));
        $this->response->addheader('Content-Type: application/json');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($ret_data));
	}

    public function permission() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('error/permission');
		$ret_data = array(
			'error_title' => $this->language->get('heading_title'),
			'error_text' => $this->language->get('text_permission'),
            'error_code' => 402,
		);

        $this->response->addheader('HTTP/1.1 402 ' . $this->language->get('heading_title'));
        $this->response->addheader('Content-Type: application/json');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($ret_data));
	}

	public function login() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('error/login');
		$ret_data = array(
			'error_title' => $this->language->get('heading_title'),
			'error_text' => $this->language->get('text_login'),
            'error_code' => 401,
		);

        $this->response->addheader('HTTP/1.1 401 ' . $this->language->get('heading_title'));
        $this->response->addheader('Content-Type: application/json');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($ret_data));
	}

	public function not_found() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('error/not_found');
		$ret_data = array(
			'error_title' => $this->language->get('heading_title'),
			'error_text' => $this->language->get('text_not_found'),
            'error_code' => 404,
		);

        $this->response->addheader('HTTP/1.1 404 ' . $this->language->get('heading_title'));
        $this->response->addheader('Content-Type: application/json');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($ret_data));
	}

}