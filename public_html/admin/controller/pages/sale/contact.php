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

if (defined('IS_DEMO') && IS_DEMO ) {
	header ( 'Location: static_pages/demo_mode.php' );
}

class ControllerPagesSaleContact extends AController {
	public $data = array();
	private $error = array();
	 
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('sale/customer');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->_validate())) {
			$this->loadModel('setting/store');
			$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);
			if ($store_info) {
				$store_name = $store_info['store_name'];
			} else {
				$store_name = $this->config->get('store_name');
			}

			$emails = array();
			
			// All customers by group
			if (isset($this->request->post['group'])) {
				$customers = array();
				switch ($this->request->post['group']) {
					case 'newsletter':
						$results = $this->model_sale_customer->getCustomersByNewsletter();
						foreach ($results as $result) {
							$emails[$result['customer_id']] = $result['email'];
							$customers[] = $result['email'];
						}
						break;
					case 'customer':
						$results = $this->model_sale_customer->getCustomers();
						foreach ($results as $result) {
							$emails[$result['customer_id']] = $result['email'];
							$customers[] = $result['email'];
						}						
						break;
				}
			}
			
			// All customers by name/email
			if (isset($this->request->post['to']) && $this->request->post['to']) {					
				foreach ($this->request->post['to'] as $customer_id) {
					$customer_info = $this->model_sale_customer->getCustomer($customer_id);
					if ($customer_info) {
						$emails[] = $customer_info['email'];
					}
				}
			}
			
			// All customers by product
			if (isset($this->request->post['product'])) {
				foreach ($this->request->post['product'] as $product_id) {
					$results = $this->model_sale_customer->getCustomersByProduct($product_id);
					if( $customers ){
						$emails = array();
					}
					foreach ($results as $result) {
						if($customers && in_array($result['email'],$customers)){
							$emails[] = $result['email'];
						}
					}
				}
			}
			
			// Prevent Duplicates
			$emails = array_unique($emails);
			
			if ($emails) {
				$message  = '<html dir="ltr" lang="en">' . "\n";
				$message .= '<head>' . "\n";
				$message .= '<title>' . $this->request->post['subject'] . '</title>' . "\n";
				$message .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
				$message .= '</head>' . "\n";
				$message .= '<body>' . html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
				$message .= '</html>' . "\n";
				
				foreach ($emails as $email) {
					$mail = new AMail( $this->config );
					$mail->setTo($email);
					$mail->setFrom($this->config->get('store_main_email'));
					$mail->setSender($store_name);
					$mail->setSubject($this->request->post['subject']);					

					$mail->setHtml($message);
					$mail->send();
					if($mail->error){
						$this->error['warning'] = 'Error: Emails does not sent! Please see error log for details.';
						break;
					}
				}
			
			}
			if(!$mail->error){
				$this->session->data['success'] = $this->language->get('text_success');
			}
		}

		$template_data['token'] = $this->session->data['token'];
		
 		if (isset($this->error['warning'])) {
			$template_data['error_warning'] = $this->error['warning'];
		} else {
			$template_data['error_warning'] = '';
		}
		
 		if (isset($this->error['subject'])) {
			$template_data['error_subject'] = $this->error['subject'];
		} else {
			$template_data['error_subject'] = '';
		}
	 	
		if (isset($this->error['message'])) {
			$template_data['error_message'] = $this->error['message'];
		} else {
			$template_data['error_message'] = '';
		}	

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('sale/contact'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
				
		if (isset($this->session->data['success'])) {
			$template_data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$template_data['success'] = '';
		}
				
		$template_data['action'] = $this->html->getSecureURL('sale/contact');
    	$template_data['cancel'] = $this->html->getSecureURL('sale/contact');

		if (isset($this->request->post['store_id'])) {
			$template_data['store_id'] = $this->request->post['store_id'];
		} else {
			$template_data['store_id'] = '';
		}

		
		$template_data['customers'] = array();		
		if (isset($this->request->post['to']) && $this->request->post['to']) {					
			foreach ($this->request->post['to'] as $customer_id) {
				$customer_info = $this->model_sale_customer->getCustomer($customer_id);
					
				if ($customer_info) {
					$template_data['customers'][] = array(
						'customer_id' => $customer_info['customer_id'],
						'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname'] . ' (' . $customer_info['email'] . ')'
					);
				}
			}
		}

		$this->loadModel('catalog/product');
		
		$template_data['products'] = $this->model_catalog_product->getProducts();
		
		if (isset($this->request->post['product'])) {
			$template_data['product'] = $this->request->post['product'];
		} else {
			$template_data['product'] = '';
		}
		
		if (isset($this->request->post['group'])) {
			$template_data['group'] = $this->request->post['group'];
		} else {
			$template_data['group'] = '';
		}
		
		if (isset($this->request->post['subject'])) {
			$template_data['subject'] = $this->request->post['subject'];
		} else {
			$template_data['subject'] = '';
		}
		
		if (isset($this->request->post['message'])) {
			$template_data['message'] = $this->request->post['message'];
		} else {
			$template_data['message'] = '';
		}
		
		$this->loadModel('catalog/category');
		$template_data['categories'] = $this->model_catalog_category->getCategories(0);

		$form = new AForm('ST');
		$form->setForm(array(
		    'form_name' => 'form',
			'update' => $template_data['update'],
	    ));

		$this->loadModel('setting/store');
		
		$stores = array(0 => $this->language->get('text_default'));
		$allstores = $this->model_setting_store->getStores();
		if($allstores){
			foreach( $allstores as $item){
				$stores[$item['store_id']] = $item['name'];
			}
		}


        $template_data['form']['store'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'store_id',
	        'value'=> $template_data['store_id'],
	        'options' => $stores,
	        'style' => 'medium-field'
	    ));
        $template_data['form']['group'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'group',
	        'value'=> $template_data['group'],
	        'options' => array(''=> $this->language->get('text_select'),
	                           'newsletter'=> $this->language->get('text_newsletter'),
	                           'customer'=> $this->language->get('text_customer'))
	    ));

        $template_data['form']['search'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'search',
	        'value'=> ''
	    ));
        $template_data['form']['subject'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'subject',
	        'value'=> $this->request->post['subject']
	    ));
        $template_data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_go'),
		    'style' => 'button1',
	    ));
		$template_data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

        $this->view->batchAssign( $template_data );


        $this->view->assign('category_products', $this->html->getSecureURL('product/product/category'));
        $this->view->assign('customers_list', $this->html->getSecureURL('user/customers'));
        $this->view->assign('rl', $this->html->getSecureURL('common/resource_library', '&object_name=&object_id&type=image&mode=url'));
		$this->view->assign('help_url', $this->gen_help_url('mail') );
		$this->view->assign('language_code', $this->session->data['language']);
		
		$this->processTemplate('pages/sale/contact.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}


	private function _validate() {
		if (!$this->user->canModify('sale/contact')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->request->post['subject']) {
			$this->error['subject'] = $this->language->get('error_subject');
		}

		if (!$this->request->post['message']) {
			$this->error['message'] = $this->language->get('error_message');
		}

		if(!$this->request->post['group'] && !$this->request->post['to']){
			$this->error['warning'] = $this->language->get('error_recipients');
		}
						
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>