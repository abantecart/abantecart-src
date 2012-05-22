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
class ControllerPagesProductProduct extends AController {
	private $error = array(); 
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

		$urls = array(
			'is_group_option' => $this->html->getURL('r/product/product/is_group_option', '&product_id=' . $this->request->get['product_id'], '&encode'),
		);
		$this->view->assign('urls', $urls);

		$this->loadModel('catalog/product');
		$promoton = new APromotion();
		
		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$resource = new AResource('image');
		if ($product_info) {

			$this->view->assign('error', '' );
	        if (isset($this->session->data['error'])) {
	            $this->view->assign('error', $this->session->data['error'] );
		        unset($this->session->data['error']);
			}

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
									
			$this->document->addBreadcrumb( array ( 
				'href'      => $this->html->getSEOURL('product/product', $url . '&product_id=' . $this->request->get['product_id'], '&encode'),
				'text'      => $product_info['name'],
				'separator' => $this->language->get('text_separator')
			 ));			
			
			$this->document->setTitle( $product_info['name'] );
			
			$this->document->setKeywords( $product_info['meta_keywords'] );
			
			$this->document->setDescription( $product_info['meta_description'] );
	
			$this->document->addLink ( array(
				'href' => $this->html->getSEOURL('product/product','&product_id=' . $this->request->get['product_id'], '&encode'),
				'rel'  => 'canonical'
			));

            $this->data['heading_title'] = $product_info['name'];

			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			
			$this->loadModel('catalog/review');
			$this->data['tab_review'] = sprintf($this->language->get('tab_review'), $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id'])) ;

			if ($this->config->get('enable_reviews')) {
				$average = $this->model_catalog_review->getAverageRating($this->request->get['product_id']);	
			} else {
				$average = false;
			}

			$this->data['review_status'] = $this->config->get('enable_reviews');
			$this->data['text_stars'] = sprintf($this->language->get('text_stars'), $average);
			$html_item = HtmlElementFactory::create(array(
				'type' => 'rating',
				'name' => 'rating',
				'value' => '',
				'options' => array(1 => 1, 2, 3, 4, 5),
                'pack' => true,
			));
			$this->data['rating_element'] = $html_item->getHtml();

			$html_item = HtmlElementFactory::create(array( 'type'=>'input',
			                                               'name' =>'name' ));
			$this->data['review_name'] = $html_item->getHtml();

			$html_item = HtmlElementFactory::create(array( 'type'=>'textarea',
			                                               'name' =>'text',
			                                               'attr' =>' rows="8" cols="50" ',
			                                        ));
			$this->data['review_text'] = $html_item->getHtml();

			$html_item = HtmlElementFactory::create(array( 'type'=>'input',
			                                               'name' =>'captcha',
			                                               'attr' =>''));
			$this->data['review_captcha'] = $html_item->getHtml();

			$html_item = HtmlElementFactory::create(array( 'type' => 'button',
				                                           'name' => 'review_submit',
			                                               'text' => $this->language->get('button_continue'),
				                                           'style'=> 'button'
			                                              ));
			$this->data['review_button'] = $html_item->getHtml();



			$this->data['product_info'] = $product_info;
			$product_price = $product_info['price'];

			$discount = $promoton->getProductDiscount($this->request->get['product_id']);
			
			if ($discount) {
				$product_price = $discount;
				$this->data['price'] = $this->currency->format($this->tax->calculate($discount, $product_info['tax_class_id'], $this->config->get('config_tax')));
				$this->data['special'] = FALSE;
			} else {
				$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'],
				                                                                     $product_info['tax_class_id'],
				                                                                     $this->config->get('config_tax')));
			
				$special = $promoton->getProductSpecial($this->request->get['product_id']);
			
				if ($special) {
					$product_price = $special;
					$this->data['special'] = $this->currency->format($this->tax->calculate($special,
					                                                                       $product_info['tax_class_id'],
					                                                                       $this->config->get('config_tax')));
				} else {
					$this->data['special'] = FALSE;
				}
			}
			
			$product_discounts = $promoton->getProductDiscounts($this->request->get['product_id']);
			
			$discounts = array();
			
			foreach ($product_discounts as $discount) {
				$discounts[] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			}
            $this->data['discounts'] = $discounts;
            $this->data['product_price'] = $product_price;
            $this->data['tax_class_id'] = $product_info['tax_class_id'];

			if ($product_info['quantity'] <= 0) {
				$this->data['stock'] = $product_info['stock_status'];
			} else {
				if ($this->config->get('config_stock_display')) {
					$this->data['stock'] = $product_info['quantity'];
				} else {
					$this->data['stock'] = $this->language->get('text_instock') ;
				}
			}


			$form = new AForm();
			$form->setForm(array( 'form_name' => 'product' ));
			$this->data['form'][ 'form_open' ] = $form->getFieldHtml(
																	  array(
																		   'type' => 'form',
																		   'name' => 'product',
																		   'action' => $this->html->getSecureURL('checkout/cart')));

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
			$this->data['form'][ 'product_id' ] = $form->getFieldHtml( array(
                                                                    'type' => 'hidden',
		                                                            'name' => 'product_id',
		                                                            'value' => $this->request->get['product_id']));
			$this->data['form'][ 'redirect' ] = $form->getFieldHtml( array(
                                                                    'type' => 'hidden',
		                                                            'name' => 'redirect',
		                                                            'value' => $this->html->getURL('product/product',  $url . '&product_id=' . $this->request->get['product_id'], '&encode') ));

			$this->data['model'] = $product_info['model'];
			$this->data['manufacturer'] = $product_info['manufacturer'];
			$this->data['manufacturers'] = $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $product_info['manufacturer_id'], '&encode');
			$this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
			$this->data['product_id'] = $this->request->get['product_id'];
			$this->data['average'] = $average;

            $elements_with_options = HtmlElementFactory::getElementsWithOptions();
			$options = array();
            $product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);

			foreach ($product_options as $option) {
				$values = array();
				$name = '';
				$price = '';
                foreach ($option['option_value'] as $option_value) {
	                $name = $option_value['name'];

					if ( $option_value['prefix'] == '%' ) {

						$price = $this->tax->calculate(
								($product_price * $option_value['price'] / 100),
								$product_info['tax_class_id'],
								$this->config->get('config_tax') );
						if( $price!=0 ){
							$price = $this->currency->format( $price );
						}else{
							$price = '';
						}
					} else {
						$price = $this->tax->calculate(	$option_value['price'],	$product_info['tax_class_id'],	$this->config->get('config_tax') );
						if( $price>0 ){
							$price = $this->currency->format( $price );
						}else{
							$price = '';
						}
					}
                    $values[$option_value['product_option_value_id']] = $option_value['name'] . ' ' . $price;
				}

				$value = '';
                //add price to option name if it is not element with options
                if ( !in_array($option['element_type'], $elements_with_options) ) {
                    $option['name'] .= ' <small>'.$price.'</small>';
	                $value = $name;
                }
				$option_data = array(
						'type' => $option['html_type'],
						'name' => !in_array($option['element_type'], HtmlElementFactory::getMultivalueElements()) ? 'option['.$option['product_option_id'].']' : 'option['.$option['product_option_id'].'][]',
						'value' => $value,
						'options' => $values,
						'required' => $option['required']);
				if($option['html_type']=='checkbox'){
					$option_data['label_text'] = $value;
				}

				$options[] = array(
					'name' => $option['name'],
					'html' => $this->html->buildElement( $option_data),
				);
			}
            $this->data['options'] = $options;
			

			// main product image
			$sizes = array('main'=> array( 'width'=>$this->config->get('config_image_popup_width'),
					                       'height' => $this->config->get('config_image_popup_height')),
						   'thumb'=> array('width'=>$this->config->get('config_image_thumb_width'),
					                       'height' => $this->config->get('config_image_thumb_height')));
			$this->data['image_main'] = $resource->getResourceAllObjects('products', $this->request->get['product_id'], $sizes,1, false);

			// additional images
			$sizes = array('main'=> array( 'width'=>$this->config->get('config_image_popup_width'),
					                       'height' => $this->config->get('config_image_popup_height')),
						   'thumb'=> array('width'=>$this->config->get('config_image_additional_width'),
					                       'height' => $this->config->get('config_image_additional_height')));

			$this->data['images'] = $resource->getResourceAllObjects('products', $this->request->get['product_id'], $sizes,0,false);


			$products = array();
			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {

				// related product image
				$sizes = array('main'=> array( 'width'=>$this->config->get('config_image_related_width'),
											   'height' => $this->config->get('config_image_related_height')),
							   'thumb'=> array('width'=>$this->config->get('config_image_thumb_width'),
											   'height' => $this->config->get('config_image_thumb_height')));
				$image = $resource->getResourceAllObjects('products',  $result['product_id'], $sizes,1);


				if ($this->config->get('enable_reviews')) {
					$rating = $this->model_catalog_review->getAverageRating($result['product_id']);
				} else {
					$rating = false;
				}
				
				$special = FALSE;
				$discount = $promoton->getProductDiscount($result['product_id']);
				if ($discount) {
					$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					$special = $promoton->getProductSpecial($result['product_id']);
					if ($special) {
						$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
					}
				}
			
				$options = $this->model_catalog_product->getProductOptions($result['product_id']);

				if ($options) {
					$add = $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode');
				} else {
					$add = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
				}
			
				$products[] = array(
									'product_id'    => $result['product_id'],
									'name'    		=> $result['name'],
									'model'   		=> $result['model'],
									'rating'  		=> $rating,
									'stars'   		=> sprintf($this->language->get('text_stars'), $rating),
									'price'   		=> $price,
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
			
			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			
			$tags = array();
			$results = $this->model_catalog_product->getProductTags($this->request->get['product_id']);
			foreach ($results as $result) {
				if ($result['tag']) {
					$tags[] = array(
						'tag'	=> $result['tag'],
						'href'	=> $this->html->getURL('product/search','&keyword=' . $result['tag'], '&encode')
					);
				}
			}
            $this->data['tags'] = $tags;

			$this->view->setTemplate( 'pages/product/product.tpl' );
		} else {
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
			$this->view->assign('button_continue', $continue->getHtml());
      		$this->data['continue'] =  $this->html->getURL('index/home');

            $this->view->setTemplate( 'pages/error/not_found.tpl' );
    	}

		$this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}