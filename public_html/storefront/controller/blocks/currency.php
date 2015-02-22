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
class ControllerBlocksCurrency extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);


      	$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['currency_code'] = $this->currency->getCode();

		$get_vars = $this->request->get;
		$unset = array('currency');
		if(isset($get_vars['product_id'])){
			$unset[] = 'path'; 
		}
		//build safe redirect URI
        if (!isset($this->request->get['rt'])) {
            $rt = 'index/home';
            $URI = '';
        } else {
        	$rt = $this->request->get['rt'];
        	$unset[] = 'rt';
			$URI = '&'.$this->html->buildURI($this->request->get, $unset);
			$URI = $URI=='&' ? '' : $URI;
        }

		$this->loadModel('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();

		$currencies = array();
		if (is_array($results) && $results) {
			foreach ($results as $result) {
				if ($result['status']) {
	   				$currencies[] = array(
						'title' => $result['title'],
						'code'  => $result['code'],
						'symbol' => ( !empty( $result['symbol_left'] ) ? $result['symbol_left'] : $result['symbol_right'] ),
						'href'  => $this->html->getSEOURL($rt, $URI.'&currency='.$result['code'],true)
					);
				}
			}
		}

		$this->data['currencies'] = $currencies;

		$this->view->batchAssign($this->data);
		$this->processTemplate('blocks/currency.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>