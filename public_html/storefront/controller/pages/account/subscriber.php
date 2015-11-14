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
class ControllerPagesAccountSubscriber extends AController {
	private $error = array();
	public $data;
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->customer->isLogged()) {
	  		$this->redirect($this->html->getSecureURL('account/newsletter'));
    	}
		$this->loadModel('account/customer');
    	$this->loadLanguage('account/create');
    	$this->loadLanguage('account/newsletter');

		$this->document->setTitle( $this->language->get('text_subscribe_register') );

		$request_data = $this->request->post;

		if ( $this->request->is_POST()) {
			$this->error = $this->model_account_customer->validateSubscribeData($request_data);

    		if ( !$this->error ) {
				// generate random password for subsribers only
				$request_data['password'] = md5(mt_rand(0,10000)); //random password
				$request_data['loginname'] = md5(time()); // loginname must be unique!
				$request_data['newsletter'] = 1; // sign of subscriber
				$request_data['status'] = $request_data['approved'] = 0; //disable login ability for subscribers
				$request_data['customer_group_id'] = $this->model_account_customer->getSubscribersCustomerGroupId();
				$request_data['ip'] = $this->request->server['REMOTE_ADDR'];

				$this->model_account_customer->addCustomer($request_data);
				$this->extensions->hk_UpdateData($this,__FUNCTION__);
		  		$this->redirect($this->html->getSecureURL('account/subscriber','&success=1'));
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
        	'href'      => $this->html->getURL('account/subscriber'),
        	'text'      => $this->language->get('text_create'),
        	'separator' => $this->language->get('text_separator')
      	 ));

		if($this->request->get['success']){
			$this->data['success'] = $this->language->get('text_success_subscribe');
			$this->data['continue'] =  $this->html->buildButton( array( 'name' => 'continue',
																		'href' => $this->html->getURL('index/home'),
																		'text' => $this->language->get('button_continue'),
																		'icon' => 'fa fa-arrow-right'
																	));
			$this->data['text_subscribe_register'] = $this->language->get('text_success_subscribe_heading');

		}else{

			if($this->config->get('prevent_email_as_login')){
				$this->data['noemaillogin'] = true;
			}

			$form = new AForm();
			$form->setForm(array( 'form_name' => 'SubscriberFrm' ));
			$this->data['form'][ 'form_open' ] = $form->getFieldHtml(
																	array(
																		   'type' => 'form',
																		   'name' => 'SubscriberFrm',
																		   'action' => $this->html->getSecureURL('account/subscriber')));

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
																		   'value' => $this->request->get_or_post('email'),
																		   'required' => true ));

			if($this->config->get('config_recaptcha_site_key')) {
				$this->data['form']['captcha'] = $form->getFieldHtml(
					array(
						'type' => 'recaptcha',
						'name' => 'captcha',
						'recaptcha_site_key' => $this->config->get('config_recaptcha_site_key'),
						'language_code' => $this->language->getLanguageCode()
					)
				);			
			} else {
				$this->data['form']['captcha'] = $form->getFieldHtml( array(
																			'type'=>'captcha',
					                                               			'name' =>'captcha',
																			'required' => true
																		));
			}

			$this->data[ 'continue' ] = $form->getFieldHtml( array(
																	'type' => 'submit',
																	'name' => $this->language->get('button_continue'),
																	'icon' => 'fa fa-check',
																	'style' => 'btn-orange'
			));


			$this->data[ 'create_account' ] = $form->getFieldHtml( array(
																	'type' => 'button',
																	'text' => $this->language->get('text_customer_registration'),
																	'href' => $this->html->getSecureURL('account/create'),
																	'icon' => 'fa fa-user',
			));


			$this->data['error_warning'] = $this->error['warning'];

			$this->data['error_firstname'] = $this->error['firstname'];
			$this->data['error_lastname'] = $this->error['lastname'];
			$this->data['error_email'] = $this->error['email'];
			$this->data['error_confirm'] = $this->error['confirm'];
			$this->data['error_captcha'] = $this->error['captcha'];


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

		}

		$this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/subscriber.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}