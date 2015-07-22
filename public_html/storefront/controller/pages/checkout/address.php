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
class ControllerPagesCheckoutAddress extends AController {
	private $error = array();
    public $data = array();
	
	public function shipping() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$cart_rt = 'checkout/cart';		
		//is this an embed mode	
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		}

		if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->html->getSecureURL($cart_rt));
    	}
		
    	if (!$this->cart->hasShipping()) {
			$this->redirect($this->html->getSecureURL($cart_rt));
    	}
		
		if (!$this->customer->isLogged()) {  
			$this->session->data['redirect'] = $this->html->getSecureURL('checkout/shipping');
      		
			$this->redirect($this->html->getSecureURL('account/login'));
    	}	

    	$this->document->setTitle( $this->language->get('heading_title') );

		$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 )); 

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL($cart_rt),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	 ));

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/shipping'),
        	'text'      => $this->language->get('text_shipping'),
        	'separator' => $this->language->get('text_separator')
      	 ));

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/address/shipping'),
        	'text'      => $this->language->get('text_address'),
        	'separator' => $this->language->get('text_separator')
      	 ));

		$this->loadModel('account/address');
		
		if ($this->request->is_POST() && isset($this->request->post['address_id'])) {
			$this->session->data['shipping_address_id'] = $this->request->post['address_id'];
			
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			
			if ($this->cart->hasShipping()) {
				$address_info = $this->model_account_address->getAddress($this->request->post['address_id']);
			
				if ($address_info) {
					$this->tax->setZone($address_info['country_id'], $address_info['zone_id']);
				}
			}
			unset($this->session->data['shipping_methods'], $this->session->data['shipping_method']);
			$this->redirect($this->html->getSecureURL('checkout/shipping'));
		}
		
		if ( $this->request->is_POST() ) {
			$this->error = $this->model_account_address->validateAddressData($this->request->post);
    		if ( !$this->error ) {	
				$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($this->request->post);
			
				unset($this->session->data['shipping_methods'], $this->session->data['shipping_method']);

				if ($this->cart->hasShipping()) {
					$this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
				}	

				$this->redirect($this->html->getSecureURL('checkout/shipping'));
			}
		}
	
		$this->_getForm('shipping');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  
  	public function payment() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$cart_rt = 'checkout/cart';		
		//is this an embed mode	
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		}

    	if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->html->getSecureURL($cart_rt));
    	}
		
		if (!$this->customer->isLogged()) {  
			$this->session->data['redirect'] = $this->html->getSecureURL('checkout/shipping');
      		
			$this->redirect($this->html->getSecureURL('account/login'));
    	}	
		
    	$this->document->setTitle( $this->language->get('heading_title') );  

		$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 )); 

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL($cart_rt),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
		if ($this->cart->hasShipping()) {
      		$this->document->addBreadcrumb( array ( 
        		'href'      => $this->html->getURL('checkout/shipping'),
        		'text'      => $this->language->get('text_shipping'),
        		'separator' => $this->language->get('text_separator')
      		 ));
		}
		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/payment'),
        	'text'      => $this->language->get('text_payment'),
        	'separator' => $this->language->get('text_separator')
      	 ));

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('checkout/address/payment'),
        	'text'      => $this->language->get('text_address'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
		$this->loadModel('account/address');
		 	 
    	if ($this->request->is_POST() && isset($this->request->post['address_id'])) {
			$this->session->data['payment_address_id'] = $this->request->post['address_id'];
	  		
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);
			
			$this->redirect($this->html->getSecureURL('checkout/payment'));
		} 
	   
		if ( $this->request->is_POST() ) {
			$this->error = $this->model_account_address->validateAddressData($this->request->post);
    		if ( !$this->error ) {			
				$this->session->data['payment_address_id'] = $this->model_account_address->addAddress($this->request->post);
	  		
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);
			
	  			$this->redirect($this->html->getSecureURL('checkout/payment'));
	  		}
    	}
	
    	$this->_getForm('payment');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  
  	private function _getForm($type) {

		$this->view->assign('heading_title', $this->language->get('text_'.$type).' '.$this->language->get('text_address') );
		$this->view->assign('error_firstname', $this->error['firstname'] );
		$this->view->assign('error_lastname', $this->error['lastname'] );
		$this->view->assign('error_address_1', $this->error['address_1'] );
		$this->view->assign('error_city', $this->error['city'] );
		$this->view->assign('error_postcode', $this->error['postcode'] );
		$this->view->assign('error_country', $this->error['country'] );
		$this->view->assign('error_zone', $this->error['zone'] );

		$this->data['default'] = $this->session->data[$type . '_address_id'];
		$form = new AForm();
        $form->setForm(array( 'form_name' => 'address_1' ));
        $this->data['form0'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'address_1',
                                                                       'action' => $this->html->getSecureURL('checkout/address/' . $type
                                                                       )));

        $addresses = array();
		$results = $this->model_account_address->getAddresses();

		foreach ($results as $result) {
      		$addresses[] = array(
        		'address_id' => $result['address_id'],
	    		'address'    => $result['firstname'] . ' ' . $result['lastname'] . ', ' . $result['address_1'] . ', ' . $result['city'] . ', ' . (($result['zone']) ? $result['zone']  . ', ' : FALSE) . (($result['postcode']) ? $result['postcode']  . ', ' : FALSE) . $result['country'],
        		'href'       => $this->html->getSecureURL('account/address/' . $type, 'address_id=' . $result['address_id']),
			    'radio' => $form->getFieldHtml( array('type' => 'radio',
                                                      'id' => 'a_'.$result['address_id'],
		                                              'name' => 'address_id',
		                                              'options' => array($result['address_id']=> ''),
				                                      'value' => ( $result['address_id']== $this->data['default'] ? $result['address_id'] : ''),
				                                      )),
      		);
    	}
        $this->data['addresses'] = $addresses;

		$this->data['form0'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue') ));







        $form = new AForm();
        $form->setForm(array( 'form_name' => 'Address2Frm' ));
        $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'Address2Frm',
                                                                       'action' => $this->html->getSecureURL('checkout/address/'.$type)));

		$this->data['form'][ 'firstname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'firstname',
		                                                               'value' => $this->request->post['firstname'],
		                                                               'required' => true ));
		$this->data['form'][ 'lastname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'lastname',
		                                                               'value' => $this->request->post['lastname'],
		                                                               'required' => true ));
        $this->data['form'][ 'company' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'company',
		                                                               'value' => $this->request->post['company'],
		                                                               'required' => false ));
		$this->data['form'][ 'address_1' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'address_1',
		                                                               'value' => $this->request->post['address_1'],
		                                                               'required' => true ));
		$this->data['form'][ 'address_2' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'address_2',
		                                                               'value' => $this->request->post['address_2'],
		                                                               'required' => false ));
		$this->data['form'][ 'city' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'city',
		                                                               'value' => $this->request->post['city'],
		                                                               'required' => true ));
		$this->data['form'][ 'postcode' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'postcode',
		                                                               'value' => $this->request->post['postcode'],
		                                                               'required' => true ));
		$this->loadModel('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        $options = array("FALSE" => $this->language->get('text_select') );
        foreach($countries as $item){
            $options[ $item['country_id'] ] = $item['name'];
        }
	    $this->data['form'][ 'country_id' ] = $form->getFieldHtml( array(
                                                                       'type' => 'selectbox',
		                                                               'name' => 'country_id',
                                                                       'options'=>$options,
		                                                               'value' => ( isset($this->request->post['country_id']) ? $this->request->post['country_id'] : $this->config->get('config_country_id')),
		                                                               'required' => true ));

	    $this->data['form'][ 'zone' ] = $form->getFieldHtml( array(
                                                                       'type' => 'selectbox',
		                                                               'name' => 'zone_id',
		                                                               'required' => true ));

		$this->data['form'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue') ));


		$this->data['zone_id'] = isset($this->request->post['zone_id']) ? $this->request->post['zone_id'] : 'FALSE';

		$this->loadModel('localisation/country');
    	$this->data['countries'] = $this->model_localisation_country->getCountries();

	    $this->view->batchAssign($this->data);
		if($this->config->get('embed_mode') == true){
		    //load special headers
	        $this->addChild('responses/embed/head', 'head');
	        $this->addChild('responses/embed/footer', 'footer');
		    $this->processTemplate('embed/checkout/address.tpl');
		} else {
	    	$this->processTemplate('pages/checkout/address.tpl');
	    }
  	}

}