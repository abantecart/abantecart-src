<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ControllerPagesProductProduct extends AController {

	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb( array ( 
			'href'      => $this->html->getURL('index/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => FALSE
		 ));
		
		$this->loadModel('tool/seo_url');
		$this->loadModel('catalog/category');	
		
		if (isset($this->request->get['path'])) {
			$path = '';
				
			foreach (explode('_', $this->request->get['path']) as $path_id) {
				$category_info = $this->model_catalog_category->getCategory($path_id);
				
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}
				
				if ($category_info) {
					$this->document->addBreadcrumb( array ( 
						'href'      => $this->html->getSEOURL('product/category','&path=' . $path, '&encode'),
						'text'      => $category_info['name'],
						'separator' => $this->language->get('text_separator')
					 ));
				}
			}
		}
		
		$this->loadModel('catalog/manufacturer');	
		
		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {	
				$this->document->addBreadcrumb( array ( 
					'href'	    => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'], '&encode'),
					'text'	    => $manufacturer_info['name'],
					'separator' => $this->language->get('text_separator')
				 ));
			}
		}
		
		if (isset($this->request->get['keyword'])) {
			$url = '';

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}	
			
			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}
			
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getURL('product/search','&keyword=' . $this->request->get['keyword'] . $url, '&encode'),
				'text'      => $this->language->get('text_search'),
				'separator' => $this->language->get('text_separator')
			 ));	
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$urls = array(
			'is_group_option' => $this->html->getURL('r/product/product/is_group_option', '&product_id=' . $product_id, '&encode'),
		);
		$this->view->assign('urls', $urls);

		$this->loadModel('catalog/product');
		$promoton = new APromotion();
				
		$product_info = $this->model_catalog_product->getProduct($product_id);

		//can not locate product? get out
		if (!$product_info) { 
			$this->_product_not_found($product_id);
			return;
		}

		$url = $this->_build_url();
				    			
		$this->view->assign('error', '' );
	    if (isset($this->session->data['error'])) {
	        $this->view->assign('error', $this->session->data['error'] );
		    unset($this->session->data['error']);
		}

		$this->document->addBreadcrumb( array ( 
		    'href'      => $this->html->getSEOURL('product/product', $url . '&product_id=' . $product_id, '&encode'),
		    'text'      => $product_info['name'],
		    'separator' => $this->language->get('text_separator')
		 ));			
		
		$this->document->setTitle( $product_info['name'] );
		$this->document->setKeywords( $product_info['meta_keywords'] );
		$this->document->setDescription( $product_info['meta_description'] );
		$this->document->addLink ( array(
		    'href' => $this->html->getSEOURL('product/product','&product_id=' . $product_id, '&encode'),
		    'rel'  => 'canonical'
		));

        $this->data['heading_title'] = $product_info['name'];
		$this->data['minimum'] = $product_info['minimum'];
		$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
		$this->data['maximum'] = $product_info['maximum'];
		$this->data['text_maximum'] = sprintf($this->language->get('text_maximum'), $product_info['maximum']);
		
		$this->loadModel('catalog/review');
		$this->data['tab_review'] = sprintf($this->language->get('tab_review'), $this->model_catalog_review->getTotalReviewsByProductId($product_id)) ;

		if ($this->config->get('enable_reviews')) {
		    $average = $this->model_catalog_review->getAverageRating($product_id);	
		} else {
		    $average = false;
		}

		$this->data['review_status'] = $this->config->get('enable_reviews');
		$this->data['text_stars'] = sprintf($this->language->get('text_stars'), $average);
		$this->data['rating_element'] = HtmlElementFactory::create(array(
		    'type' => 'rating',
		    'name' => 'rating',
		    'value' => '',
		    'options' => array(1 => 1, 2, 3, 4, 5),
            'pack' => true,
		));
		$this->data['review_name'] = HtmlElementFactory::create(array( 'type'=>'input',
		                                               'name' =>'name' ));
		$this->data['review_text'] = HtmlElementFactory::create(array( 'type'=>'textarea',
		                                               'name' =>'text',
		                                               'attr' =>' rows="8" cols="50" ',
		                                        ));
		$this->data['review_captcha'] = HtmlElementFactory::create(array( 'type'=>'input',
		                                               'name' =>'captcha',
		                                               'attr' =>''));
		$this->data['review_button'] = HtmlElementFactory::create(array( 'type' => 'button',
		                                               'name' => 'review_submit',
		                                               'text' => $this->language->get('button_submit'),
		                                               'style'=> 'btn-primary',
		                                               'icon' => 'fa fa-comment'
		                                              ));

		$this->data['product_info'] = $product_info;


		$form = new AForm();
		$form->setForm(array( 'form_name' => 'product' ));
		$this->data['form'][ 'form_open' ] = $form->getFieldHtml(
		    													  array(
		    														   'type' => 'form',
		    														   'name' => 'product',
		    														   'action' => $this->html->getSecureURL('checkout/cart')));


			$product_price = $product_info['price'];

			$discount = $promoton->getProductDiscount($product_id);

			if ($discount) {
				$product_price = $discount;
				$this->data['price_num'] = $this->tax->calculate(
					$discount,
					$product_info['tax_class_id'],
					(bool)$this->config->get('config_tax')
				);
				$this->data['special'] = FALSE;
			} else {
				$this->data['price_num'] = $this->tax->calculate(
					$product_info['price'],
					$product_info['tax_class_id'],
					(bool)$this->config->get('config_tax')
				);

				$special = $promoton->getProductSpecial($product_id);

				if ($special) {
					$product_price = $special;
					$this->data['special_num'] =$this->tax->calculate(
						$special,
						$product_info['tax_class_id'],
						(bool)$this->config->get('config_tax')
					);
				} else {
					$this->data['special'] = FALSE;
				}
			}

			$this->data['price'] = $this->currency->format($this->data['price_num']);

			if ( isset($this->data['special_num']) ) {
				$this->data['special'] = $this->currency->format($this->data['special_num']);
			}

			$product_discounts = $promoton->getProductDiscounts($product_id);

			$discounts = array();

			foreach ($product_discounts as $discount) {
				$discounts[] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], (bool)$this->config->get('config_tax')))
				);
			}
			$this->data['discounts'] = $discounts;
			$this->data['product_price'] = $product_price;
			$this->data['tax_class_id'] = $product_info['tax_class_id'];


		if(!$product_info['call_to_order']){
			$this->data['form']['minimum'] = $form->getFieldHtml( array(
																'type' => 'input',
																'name' => 'quantity',
																'value' => ( $product_info['minimum'] ? (int)$product_info['minimum'] : 1),
																'style' => 'short',
																'attr' => ' size="3" '));

			$this->data['form'][ 'add_to_cart' ] = $form->getFieldHtml( array(
																	'type' => 'button',
																	'name' => 'add_to_cart',
																	'text' => $this->language->get('button_add_to_cart'),
																	'style' => 'button1' ));


		}
		$this->data['form'][ 'product_id' ] = $form->getFieldHtml( array(
                                                                'type' => 'hidden',
		                                                        'name' => 'product_id',
		                                                        'value' => $product_id));
		$this->data['form'][ 'redirect' ] = $form->getFieldHtml( array(
                                                                'type' => 'hidden',
		                                                        'name' => 'redirect',
		                                                        'value' => $this->html->getURL('product/product',  $url . '&product_id=' . $product_id, '&encode') ));

		$this->data['model'] = $product_info['model'];
		$this->data['manufacturer'] = $product_info['manufacturer'];
		$this->data['manufacturers'] = $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $product_info['manufacturer_id'], '&encode');
		$this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
		$this->data['product_id'] = $product_id;
		$this->data['average'] = $average;

		$resource = new AResource('image');
		$thumbnail = $resource->getMainThumb('manufacturers',
		    $product_info['manufacturer_id'],
			(int)$this->config->get('config_image_grid_width'),
			(int)$this->config->get('config_image_grid_height'),
		    true);			
		if ( !preg_match('/no_image/', $thumbnail['thumb_url'])) {
		    $this->data['manufacturer_icon'] = $thumbnail['thumb_url']; 
		}	

		// Preapare options and values for display 
        $elements_with_options = HtmlElementFactory::getElementsWithOptions();
		$options = array();
        $product_options = $this->model_catalog_product->getProductOptions($product_id);

		foreach ($product_options as $option) {
		    $values = array();
		    $name = $price = $default_value = '';
            foreach ($option['option_value'] as $option_value) {
				$default_value = $option_value['default'] ? $option_value['product_option_value_id']: $default_value;
				// for case when trying to add to cart withot required options. we get option-array back inside _GET
				if(has_value($this->request->get['option'][$option['product_option_id']])){
					$default_value = $this->request->get['option'][$option['product_option_id']];
				}

            	$name = $option_value['name'];     
            	//check if we disable options based on out of stock setting
                if($option_value['subtract'] && $this->config->get('config_nostock_autodisable') && $option_value['quantity'] <= 0) {
                	continue;
                }
                     	
		    	//Apply option price modifier
		    	if ( $option_value['prefix'] == '%' ) {
		    		$price = $this->tax->calculate(
		    				($product_price * $option_value['price'] / 100),
		    				$product_info['tax_class_id'],
							(bool)$this->config->get('config_tax') );
		    		if( $price != 0 ){
		    			$price = $this->currency->format( $price );
		    		}else{
		    			$price = '';
		    		}
		    	} else {
		    		$price = $this->tax->calculate(	$option_value['price'],	$product_info['tax_class_id'],	(bool)$this->config->get('config_tax') );
		    		if( $price != 0 ){
		    			$price = $this->currency->format( $price );
		    		}else{
		    			$price = '';
		    		}
		    	}
		    	//Check stock and status 
		    	$opt_stock_message = '';
		    	if($option_value['subtract'] ) {
		    		if ( $option_value['quantity'] <= 0) {
		    			//show out of stock message
		    			$opt_stock_message = $product_info['stock_status'];
		    		} else {
		    			if ($this->config->get('config_stock_display')) {
		    				$opt_stock_message = $option_value['quantity'] . " " . $this->language->get('text_instock');
		    			}
		    		}					
		    	}
                $values[$option_value['product_option_value_id']] = $option_value['name'] . ' ' . $price . ' ' . $opt_stock_message;
		    }

		    //if not values are build, nothing to show
		    if (count($values)) {
		    	$value = '';
	            //add price to option name if it is not element with options
	            if ( !in_array($option['element_type'], $elements_with_options) ) {
	                $option['name'] .= ' <small>'.$price.'</small>';
	                if ( $opt_stock_message ) {
	                	$option['name'] .= '<br />' . $opt_stock_message;
	                }
		            $value = $name;
	            }
	            
				//set default selection is nothing selected
				if ( !has_value($value) ) {
					if( has_value($default_value) ) { 
						$value = $default_value;
					} else {
						if(in_array($option['element_type'], $elements_with_options) && $option['element_type']!='S'){
							//set first from the list to default
							reset($values);
							$value = key($values);
						}
					}
				}
					            
		    	$option_data = array(
		    			'type' => $option['html_type'],
		    			'name' => !in_array($option['element_type'], HtmlElementFactory::getMultivalueElements()) ? 'option['.$option['product_option_id'].']' : 'option['.$option['product_option_id'].'][]',
		    			'value' => $value,
		    			'options' => $values,
		    			'required' => $option['required'],
		    			'placeholder' => $option['option_placeholder'],
		    			'regexp_pattern' => $option['regexp_pattern'],
		    			'error_text' => $option['error_text']
						);
		    	if($option['html_type']=='checkbox'){
		    		$option_data['label_text'] = $value;
		    	}
	
		    	$options[] = array(
		    		'name' => $option['name'],
		    		'html' => $this->html->buildElement( $option_data),  // not a string!!! it's object!
		    	);
		    }
		}
        $this->data['options'] = $options;
		
		//handle stock messages
		// if track stock is off. no messages needed. 
		if ( $this->model_catalog_product->isStockTrackable($product_id) ) {
		    $total_quantity = $this->model_catalog_product->hasAnyStock($product_id);
			$this->data['track_stock'] = true;
			//out of stock if no quantity and no stick checkout is disabled
		    if ( $total_quantity <= 0 && !$this->config->get('config_stock_checkout')) {
		    	$this->data['in_stock'] = false;
		    	//show out of stock message
		    	$this->data['stock'] = $product_info['stock_status'];
		    } else {
				$this->data['in_stock'] = true;
		    	if ($this->config->get('config_stock_display')) {
		    		$this->data['stock'] = $product_info['quantity'];
		    	} else {
		    		$this->data['stock'] = $this->language->get('text_instock') ;
		    	}
		    }
		    
		    //check if we need to disable product for no stock 
		    if ($this->config->get('config_nostock_autodisable') && $total_quantity <= 0) {
		    	//set available data
		    	$pd_identifiers = "ID: " . $product_id;
		    	$pd_identifiers .= (empty($product_info['model']) ? '' : " Model: " . $product_info['model']);
		    	$pd_identifiers .= (empty($product_info['sku']) ? '' :  " SKU: " . $product_info['sku']);					
		    	$message_ttl = sprintf($this->language->get('notice_out_of_stock_ttl'), $product_info['name']);
		    	$message_txt = sprintf($this->language->get('notice_out_of_stock_body'), $product_info['name'], $pd_identifiers);
		    	//record to message box
	        	$msg = new AMessage();
	        	$msg->saveNotice( $message_ttl, $message_txt);
	        	$this->model_catalog_product->updateStatus($product_id, 0);     	
	        	$this->redirect($this->html->getSEOURL('product/product','&product_id=' . $product_info['product_id'], '&encode'));
		    }
		}
		
		// main product image
		$sizes = array('main'=> array( 'width'=>$this->config->get('config_image_popup_width'),
		    	                       'height' => $this->config->get('config_image_popup_height')),
		    		   'thumb'=> array('width'=>$this->config->get('config_image_thumb_width'),
		    	                       'height' => $this->config->get('config_image_thumb_height')));
		$this->data['image_main'] = $resource->getResourceAllObjects('products', $product_id, $sizes,1, false);
		$this->data['image_main']['sizes'] = $sizes;

		// additional images
		$sizes = array('main'=> array( 'width'=>$this->config->get('config_image_popup_width'),
		    	                       'height' => $this->config->get('config_image_popup_height')),
		    		   'thumb'=> array('width'=>$this->config->get('config_image_additional_width'),
		    	                       'height' => $this->config->get('config_image_additional_height')));

		$this->data['images'] = $resource->getResourceAllObjects('products', $product_id, $sizes,0,false);

		$products = array();
		$results = $this->model_catalog_product->getProductRelated($product_id);

		foreach ($results as $result) {

		    // related product image
		    $sizes = array('main'=> array( 'width'=>$this->config->get('config_image_related_width'),
		    							   'height' => $this->config->get('config_image_related_height')),
		    			   'thumb'=> array('width'=>$this->config->get('config_image_related_width'),
		    							   'height' => $this->config->get('config_image_related_height')));
		    $image = $resource->getResourceAllObjects('products',  $result['product_id'], $sizes,1);


		    if ($this->config->get('enable_reviews')) {
		    	$rating = $this->model_catalog_review->getAverageRating($result['product_id']);
		    } else {
		    	$rating = false;
		    }
		    
		    $special = FALSE;
		    $discount = $promoton->getProductDiscount($result['product_id']);
		    if ($discount) {
		    	$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], (bool)$this->config->get('config_tax')));
		    } else {
		    	$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], (bool)$this->config->get('config_tax')));
		    	$special = $promoton->getProductSpecial($result['product_id']);
		    	if ($special) {
		    		$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], (bool)$this->config->get('config_tax')));
		    	}
		    }
		
		    $options = $this->model_catalog_product->getProductOptions($result['product_id']);

		    if ($options) {
		    	$add = $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode');
		    } else {
                if($this->config->get('config_cart_ajax')){
                    $add = '#';
                }else{
                    $add = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
                }
		    }
		
		    $products[] = array(
		    					'product_id'    => $result['product_id'],
		    					'name'    		=> $result['name'],
		    					'model'   		=> $result['model'],
		    					'rating'  		=> $rating,
		    					'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
		    					'price'   		=> $price,
								'call_to_order'=> $result['call_to_order'],
		    					'options'   	=> $options,
		    					'special' 		=> $special,
		    					'image'   		=> $image,
		    					'href'    		=> $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode'),
		    					'add'    		=> $add
		    );
		}

        $this->data['related_products'] = $products;

		if ($this->config->get('config_customer_price')) {
		    $display_price = TRUE;
		} elseif ($this->customer->isLogged()) {
		    $display_price = TRUE;
		} else {
		    $display_price = FALSE;
		}
        $this->data['display_price'] = $display_price;
		
		$this->model_catalog_product->updateViewed($product_id);
		
		$tags = array();
		$results = $this->model_catalog_product->getProductTags($product_id);
		foreach ($results as $result) {
		    if ($result['tag']) {
		    	$tags[] = array(
		    		'tag'	=> $result['tag'],
		    		'href'	=> $this->html->getURL('product/search','&keyword=' . $result['tag'], '&encode')
		    	);
		    }
		}
        $this->data['tags'] = $tags;


		//downloads before order
		$dwn = new ADownload();
		$download_list = $dwn->getDownloadsBeforeOrder($product_id);
		if($download_list){

			foreach($download_list as $download){
				$href = $this->html->getURL('account/download/startdownload','&download_id='.$download['download_id']);
				$download['attributes'] = $this->download->getDownloadAttributesValuesForCustomer($download['download_id']);

				$download['href'] = $form->getFieldHtml(
						array(  'type'=> 'button',
								'id' => 'download_'. $download['download_id'],
								'href'=> $href,
								'title' => $this->language->get('text_start_download'),
								'text' => $this->language->get('text_start_download'),
								'style' => 'button1 fa fa-download-alt'	));

				$downloads[] = $download;
			}


		$this->data['downloads'] = $downloads;
		}


		$this->view->setTemplate( 'pages/product/product.tpl' );


		$this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  	
  	private function _product_not_found ($product_id) {

		$url = $this->_build_url();
				    	
      	$this->document->addBreadcrumb( array (
		    		'href'      => $this->html->getSEOURL('product/product', $url . '&product_id=' . $product_id, '&encode'),
		    		'text'      => $this->language->get('text_error'),
		    		'separator' => $this->language->get('text_separator')
      	 ));			
		
		
      	$this->document->setTitle( $this->language->get('text_error') );

        $this->data['heading_title'] = $this->language->get('text_error');
        $this->data['text_error'] = $this->language->get('text_error');
		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                           'name' => 'continue_button',
		                                           'text'=> $this->language->get('button_continue'),
		                                           'style' => 'button'));
		$this->view->assign('button_continue', $continue );
      	$this->data['continue'] =  $this->html->getURL('index/home');

        $this->view->setTemplate( 'pages/error/not_found.tpl' );  	
        
		$this->view->batchAssign($this->data);
        $this->processTemplate();  	
  	}
 
	private function _build_url () {
		$url = '';
		if (isset($this->request->get['path'])) {
		    $url .= '&path=' . $this->request->get['path'];
		}
		
		if (isset($this->request->get['manufacturer_id'])) {
		    $url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
		}			

		if (isset($this->request->get['keyword'])) {
		    $url .= '&keyword=' . $this->request->get['keyword'];
		}			

		if (isset($this->request->get['category_id'])) {
		    $url .= '&category_id=' . $this->request->get['category_id'];
		}
		    
		if (isset($this->request->get['description'])) {
		    $url .= '&description=' . $this->request->get['description'];
		}				
		
		return $url;
	} 
  	
}