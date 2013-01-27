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
class ControllerPagesAccountCreate extends AController {
	private $error = array();
	public $data;
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->customer->isLogged()) {
	  		$this->redirect($this->html->getSecureURL('account/account'));
    	}

		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->loadModel('account/customer');
		
		$request_data = $this->request->post;
		if ( $this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->error = $this->model_account_customer->validateRegistrationData($request_data);
    		if ( !$this->error ) {
				//if allow login as email, need to set loginname = email
				if (!$this->config->get('prevent_email_as_login')) {
					$request_data['loginname'] = $request_data['email'];
				}
				
				$this->model_account_customer->addCustomer($request_data);
	
				unset($this->session->data['guest']);
	
				$this->customer->login($request_data['loginname'], $request_data['password']);
				
				$this->loadLanguage('mail/account_create');
				
				$subject = sprintf($this->language->get('text_subject'), $this->config->get('store_name'));
				
				$message = sprintf($this->language->get('text_welcome'), $this->config->get('store_name')) . "\n\n";
				
				if (!$this->config->get('config_customer_approval')) {
					$message .= $this->language->get('text_login') . "\n";
				} else {
					$message .= $this->language->get('text_approval') . "\n";
				}
				
				$message .= $this->html->getSecureURL('account/login') . "\n\n";
				$message .= $this->language->get('text_services') . "\n\n";
				$message .= $this->language->get('text_thanks') . "\n";
				$message .= $this->config->get('store_name');
				
				$mail = new AMail( $this->config );
				$mail->setTo($this->request->post['email']);
		  		$mail->setFrom($this->config->get('store_main_email'));
		  		$mail->setSender($this->config->get('store_name'));
		  		$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
	      		$mail->send();

				$this->extensions->hk_UpdateData($this,__FUNCTION__);
		  		$this->redirect($this->html->getSecureURL('account/success'));
	  		}
    	} 

      	$this->document->initBreadcrumb( array (
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 )); 

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('account/account'),
        	'text'      => $this->language->get('text_account'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('account/create'),
        	'text'      => $this->language->get('text_create'),
        	'separator' => $this->language->get('text_separator')
      	 ));

		if($this->config->get('prevent_email_as_login')){
			$this->data['noemaillogin'] = true;
		}

        $form = new AForm();
        $form->setForm(array( 'form_name' => 'AccountFrm' ));
        $this->data['form'][ 'form_open' ] = $form->getFieldHtml(
                                                                array(
                                                                       'type' => 'form',
                                                                       'name' => 'AccountFrm',
                                                                       'action' => $this->html->getSecureURL('account/create')));

		$this->data['form'][ 'loginname' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'loginname',
		                                                               'value' => $this->request->post['loginname'],
		                                                               'required' => true ));
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
		$this->data['form'][ 'email' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'email',
		                                                               'value' => $this->request->post['email'],
		                                                               'required' => true ));
		$this->data['form'][ 'telephone' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'telephone',
		                                                               'value' => $this->request->post['telephone'],
		                                                               'required' => true ));
		$this->data['form'][ 'fax' ] = $form->getFieldHtml( array(
                                                                       'type' => 'input',
		                                                               'name' => 'fax',
		                                                               'value' => $this->request->post['fax'],
		                                                               'required' => false ));
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
        $this->view->assign('zone_id', $this->request->post['zone_id'], 'FALSE' );
        $this->data['form'][ 'zone_id' ] = $form->getFieldHtml( array(
                                                                       'type' => 'selectbox',
		                                                               'name' => 'zone_id',
		                                                               'required' => true ));

		$this->data['form'][ 'password' ] = $form->getFieldHtml( array(
                                                                       'type' => 'password',
		                                                               'name' => 'password',
		                                                               'value' => $this->request->post['password'],
		                                                               'required' => true ));
		$this->data['form'][ 'confirm' ] = $form->getFieldHtml( array(
                                                                       'type' => 'password',
		                                                               'name' => 'confirm',
		                                                               'value' => $this->request->post['confirm'],
		                                                               'required' => true ));
		$this->data['form'][ 'newsletter' ] = $form->getFieldHtml( array(
                                                                       'type' => 'radio',
		                                                               'name' => 'newsletter',

		                                                               'value' => (isset($this->request->post['newsletter']) ? $this->request->post['newsletter'] : -1),
		                                                               'options' => array(
                                                                           '1' => $this->language->get('text_yes'),
                                                                           '0' => $this->language->get('text_no'),
                                                                       ) ));

		$agree = isset($this->request->post['agree']) ? $this->request->post['agree'] : FALSE;
		$this->data['form'][ 'agree' ] = $form->getFieldHtml( array(
                                                                    'type' => 'checkbox',
		                                                            'name' => 'agree',
		                                                            'value' => 1,
		                                                            'checked' => $agree ));

		$this->data['form'][ 'continue' ] = $form->getFieldHtml( array(
                                                                       'type' => 'submit',
		                                                               'name' => $this->language->get('button_continue') ));




        $this->data['error_warning'] = $this->error['warning'];
		$this->data['error_loginname'] = $this->error['loginname'];
		$this->data['error_firstname'] = $this->error['firstname'];
		$this->data['error_lastname'] = $this->error['lastname'];
		$this->data['error_email'] = $this->error['email'];
		$this->data['error_telephone'] = $this->error['telephone'];
		$this->data['error_password'] = $this->error['password'];
		$this->data['error_confirm'] = $this->error['confirm'];
		$this->data['error_address_1'] = $this->error['address_1'];
		$this->data['error_city'] = $this->error['city'];
		$this->data['error_postcode'] = $this->error['postcode'];
		$this->data['error_country'] = $this->error['country'];
		$this->data['error_zone'] = $this->error['zone'];

        $this->data['action'] = $this->html->getSecureURL('account/create') ;
		$this->data['newsletter'] = $this->request->post['newsletter'];


		


		if ($this->config->get('config_account_id')) {

			$this->loadModel('catalog/content');
			$content_info = $this->model_catalog_content->getContent($this->config->get('config_account_id'));
			
			if ($content_info) {
				$text_agree = $this->language->get('text_agree');
				$this->data['text_agree_href'] = $this->html->getURL('r/content/content/loadInfo', '&content_id=' . $this->config->get('config_account_id'));
				$this->data['text_agree_href_text'] = $content_info['title'];
			} else {
				$text_agree = '';
			}
		} else {
			$text_agree = '';
		}
        $this->data['text_agree'] = $text_agree;

        $text_account_already = sprintf($this->language->get('text_account_already'), $this->html->getSecureURL('account/login') );
        $this->data['text_account_already'] = $text_account_already;

		$this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/create.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	private function validate() {
  	}
}