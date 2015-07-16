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

class ControllerResponsesAccountDownload extends AController {
	private $error = array();
	public $data = array();

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		try{
			$this->config->set('embed_mode', true);
			$cntr = $this->dispatch('pages/account/download');
			$html_out = $cntr->dispatchGetOutput();
		}catch(AException $e){	}
	
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->response->setOutput($html_out);
	}

	public function startDownload() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		try{
			$this->config->set('embed_mode', true);
			$cntr = $this->dispatch('pages/account/download/startDownload');
			$html_out = $cntr->dispatchGetOutput();
		}catch(AException $e){	}

        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->response->setOutput($html_out);
	}

}