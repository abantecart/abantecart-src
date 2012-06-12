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
class ControllerBlocksCurrency extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['currency_code'])) {
      		$this->currency->set($this->request->post['currency_code']);
			
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
				
			if (isset($this->request->post['redirect'])) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->html->getURL('index/home'));
			}
   		}

      	$this->view->assign('heading_title', $this->language->get('heading_title'));
		$this->view->assign('currency_code', $this->currency->getCode());
		$this->loadModel('localisation/currency');
		$this->view->assign('currencies', array());
		$results = $this->model_localisation_currency->getCurrencies();	

		$currencies = array();
		foreach ($results as $result) {
			if ($result['status']) {
   				$currencies[] = array(
					'title' => $result['title'],
					'code'  => $result['code']
				);
			}
		}

		$this->view->assign('currencies', $currencies );


		if (!isset($this->request->get['rt'])) {
			$redirect = $this->html->getURL('index/home');
		} else {
			$this->loadModel('tool/seo_url');
			$data = $this->request->get;
			unset($data['_route_']);
			$route = $data['rt'];
			unset($data['rt']);
			$url = '';
			if ($data) {
				$url = '&' . urldecode(http_build_query($data));
			}			
			
			$redirect =  $this->html->getSEOURL( $route,  $url, '&encode');
		}
		$this->data['redirect'] = $redirect;

		$form = new AForm();
        $form->setForm(array( 'form_name' => 'currency_form' ));
        $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'currency_form',
                                                                       'action' => $this->html->getURL('index/home')));
		$this->data['form'][ 'code' ] = $form->getFieldHtml( array(
                                                                       'type' => 'hidden',
		                                                               'name' => 'currency_code',
		                                                               'value' => '' ));
		$this->data['form'][ 'redirect' ] = $form->getFieldHtml( array(
                                                                       'type' => 'hidden',
		                                                               'name' => 'redirect',
		                                                               'value' => $redirect ));

		$this->view->batchAssign($this->data);
		$this->processTemplate('blocks/currency.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>