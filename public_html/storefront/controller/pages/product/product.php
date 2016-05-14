<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if(!defined('DIR_CORE')){
	header('Location: static_pages/');
}

class ControllerPagesProductProduct extends AController{

	public $data = array();
	private $routes = array();

	private function _init(){
		//is this an embed mode
		if($this->config->get('embed_mode') == true){
			$this->routes['cart_rt'] = 'r/checkout/cart/embed';
		} else{
			$this->routes['cart_rt'] = 'checkout/cart';
		}
	}

	/**
	 * Check if HTML Cache is enabled for the method
	 * @return array - array of data keys to be used for cache key building  
	 */	
	public static function main_cache_keys(){
		return array('product_id','path','key','manufacturer_id','category_id','description','keyword');
	}
	
	public function main(){

		$request = $this->request->get;

		$this->_init();
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->document->resetBreadcrumbs();

		$this->document->addBreadcrumb(array(
				'href'      => $this->html->getURL('index/home'),
				'text'      => $this->language->get('text_home'),
				'separator' => false
		));

		$this->loadModel('tool/seo_url');
		$this->loadModel('catalog/category');

		if(isset($request['path'])){
			$path = '';

			foreach(explode('_', $request['path']) as $path_id){
				$category_info = $this->model_catalog_category->getCategory($path_id);

				if(!$path){
					$path = $path_id;
				} else{
					$path .= '_' . $path_id;
				}

				if($category_info){
					$this->document->addBreadcrumb(array(
							'href'      => $this->html->getSEOURL('product/category', '&path=' . $path, '&encode'),
							'text'      => $category_info['name'],
							'separator' => $this->language->get('text_separator')
					));
				}
			}
		}

		$this->loadModel('catalog/manufacturer');

		if(isset($request['manufacturer_id'])){
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($request['manufacturer_id']);

			if($manufacturer_info){
				$this->document->addBreadcrumb(array(
						'href'      => $this->html->getSEOURL('product/manufacturer', '&manufacturer_id=' . $request['manufacturer_id'], '&encode'),
						'text'      => $manufacturer_info['name'],
						'separator' => $this->language->get('text_separator')
				));
			}
		}

		if(isset($request['keyword'])){
			$url = '';

			if(isset($request['category_id'])){
				$url .= '&category_id=' . $request['category_id'];
			}

			if(isset($request['description'])){
				$url .= '&description=' . $request['description'];
			}

			$this->document->addBreadcrumb(array(
					'href'      => $this->html->getURL('product/search', '&keyword=' . $request['keyword'] . $url, '&encode'),
					'text'      => $this->language->get('text_search'),
					'separator' => $this->language->get('text_separator')
			));
		}

		$key = array(); //key of product from cart

		if(has_value($request['key'])){
			$key = explode(':', $request['key']);
			$product_id = (int)$key[0];
		} elseif(has_value($request['product_id'])){
			$product_id = (int)$request['product_id'];
		} else{
			$product_id = 0;
		}

		$urls = array(
				'is_group_option' => $this->html->getURL('r/product/product/is_group_option', '&product_id=' . $product_id, '&encode'),
		);
		$this->view->assign('urls', $urls);

		$this->loadModel('catalog/product');
		$promotion = new APromotion();

		$product_info = $this->model_catalog_product->getProduct($product_id);
		//can not locate product? get out
		if(!$product_info){
			$this->_product_not_found($product_id);
			return null;
		}

		$url = $this->_build_url_params();

		$this->view->assign('error', '');
		if(isset($this->session->data['error'])){
			$this->view->assign('error', $this->session->data['error']);
			unset($this->session->data['error']);
		}

		$this->document->addBreadcrumb(array(
				'href'      => $this->html->getSEOURL('product/product', $url . '&product_id=' . $product_id, '&encode'),
				'text'      => $product_info['name'],
				'separator' => $this->language->get('text_separator')
		));

		$this->document->setTitle($product_info['name']);
		$this->document->setKeywords($product_info['meta_keywords']);
		$this->document->setDescription($product_info['meta_description']);
		$this->document->addLink(array(
				'href' => $this->html->getSEOURL('product/product', '&product_id=' . $product_id, '&encode'),
				'rel'  => 'canonical'
		));

		$this->data['heading_title'] = $product_info['name'];
		$this->data['minimum'] = $product_info['minimum'];
		$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
		$this->data['maximum'] = $product_info['maximum'];
		$this->data['text_maximum'] = sprintf($this->language->get('text_maximum'), $product_info['maximum']);
		$this->data['option_resources_url'] = $this->html->getURL('r/product/product/get_option_resources');
		$this->data['calc_total_url'] = $this->html->getURL('r/product/product/calculateTotal');
		$this->data['product_review_url'] = $this->html->getURL('product/review/review', '&product_id=' . $product_id);
		$this->data['product_review_write_url'] = $this->html->getURL('product/review/write', '&product_id=' . $product_id);
		$this->data['product_wishlist_add_url'] = $this->html->getURL('product/wishlist/add', '&product_id=' . $product_id);
		$this->data['product_wishlist_remove_url'] = $this->html->getURL('product/wishlist/remove', '&product_id=' . $product_id);
		$this->data['captcha_url'] = $this->html->getURL('common/captcha');
		$this->data['update_view_count_url'] = $this->html->getURL('common/view_count/product', '&product_id=' . $product_id);

		$this->loadModel('catalog/review');
		$this->data['tab_review'] = sprintf($this->language->get('tab_review'), $this->model_catalog_review->getTotalReviewsByProductId($product_id));

		if($this->config->get('enable_reviews')){
			$average = $this->model_catalog_review->getAverageRating($product_id);
		} else{
			$average = false;
		}

		$this->data['review_status'] = $this->config->get('enable_reviews');
		$this->data['text_stars'] = sprintf($this->language->get('text_stars'), $average);
		$this->data['rating_element'] = HtmlElementFactory::create(
				array(
						'type'    => 'rating',
						'name'    => 'rating',
						'value'   => '',
						'options' => array(1 => 1, 2, 3, 4, 5),
						'pack'    => true,
				));
		$this->data['review_name'] = HtmlElementFactory::create(
				array(
						'type' => 'input',
						'name' => 'name'
				));
		$this->data['review_text'] = HtmlElementFactory::create(
				array(
						'type' => 'textarea',
						'name' => 'text',
						'attr' => ' rows="8" cols="50" ',
				));

		if($this->config->get('config_recaptcha_site_key')) {
			$this->data['recaptcha_site_key'] = $this->config->get('config_recaptcha_site_key');
			$this->data['review_recaptcha'] = HtmlElementFactory::create(
				array(
						'type' => 'recaptcha',
						'name' => 'recaptcha',
						'recaptcha_site_key' => $this->data['recaptcha_site_key'],
						'language_code' => $this->language->getLanguageCode()
				));		
		
		} else {
			$this->data['review_captcha'] = HtmlElementFactory::create(
				array(
						'type' => 'input',
						'name' => 'captcha',
						'attr' => ''));		
		}
		$this->data['review_button'] = HtmlElementFactory::create(
				array(
						'type'  => 'button',
						'name'  => 'review_submit',
						'text'  => $this->language->get('button_submit'),
						'style' => 'btn-primary',
						'icon'  => 'fa fa-comment'
				));

		$this->data['product_info'] = $product_info;


		$form = new AForm();
		$form->setForm(array('form_name' => 'product'));
		$this->data['form']['form_open'] = $form->getFieldHtml(
				array(
						'type'   => 'form',
						'name'   => 'product',
						'action' => $this->html->getSecureURL($this->routes['cart_rt'])));


		$product_price = $product_info['price'];

		$discount = $promotion->getProductDiscount($product_id);

		if($discount){
			$product_price = $discount;
			$this->data['price_num'] = $this->tax->calculate(
					$discount,
					$product_info['tax_class_id'],
					(bool)$this->config->get('config_tax')
			);
			$this->data['special'] = false;
		} else{
			$this->data['price_num'] = $this->tax->calculate(
					$product_info['price'],
					$product_info['tax_class_id'],
					(bool)$this->config->get('config_tax')
			);

			$special = $promotion->getProductSpecial($product_id);

			if($special){
				$product_price = $special;
				$this->data['special_num'] = $this->tax->calculate(
						$special,
						$product_info['tax_class_id'],
						(bool)$this->config->get('config_tax')
				);
			} else{
				$this->data['special'] = false;
			}
		}

		$this->data['price'] = $this->currency->format($this->data['price_num']);

		if(isset($this->data['special_num'])){
			$this->data['special'] = $this->currency->format($this->data['special_num']);
		}

		$product_discounts = $promotion->getProductDiscounts($product_id);

		$discounts = array();

		foreach($product_discounts as $discount){
			$discounts[] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], (bool)$this->config->get('config_tax')))
			);
		}
		$this->data['discounts'] = $discounts;
		$this->data['product_price'] = $product_price;
		$this->data['tax_class_id'] = $product_info['tax_class_id'];


		if(!$product_info['call_to_order']){
			$qnt = (int)$request['quantity'];
			if(!$qnt){
				$qnt = ($product_info['minimum'] ? (int)$product_info['minimum'] : 1);
			}

			$qnt = (int)$product_info['minimum'] && $product_info['minimum']>$qnt ?  (int)$product_info['minimum'] : $qnt;
			$qnt = (int)$product_info['maximum'] && $product_info['maximum']<$qnt ?  (int)$product_info['maximum'] : $qnt;

			$this->data['form']['minimum'] = $form->getFieldHtml(
					array(
							'type'  => 'input',
							'name'  => 'quantity',
							'value' => $qnt,
							'style' => 'short',
							'attr'  => ' size="3" '));

			$this->data['form']['add_to_cart'] = $form->getFieldHtml(
					array(
							'type'  => 'button',
							'name'  => 'add_to_cart',
							'text'  => $this->language->get('button_add_to_cart'),
							'style' => 'button1'));


		}
		$this->data['form']['product_id'] = $form->getFieldHtml(
				array(
						'type'  => 'hidden',
						'name'  => 'product_id',
						'value' => $product_id));
		$this->data['form']['redirect'] = $form->getFieldHtml(
				array(
						'type'  => 'hidden',
						'name'  => 'redirect',
						'value' => $this->html->getURL('product/product', $url . '&product_id=' . $product_id, '&encode')));

		$this->data['model'] = $product_info['model'];
		$this->data['manufacturer'] = $product_info['manufacturer'];
		$this->data['manufacturers'] = $this->html->getSEOURL('product/manufacturer', '&manufacturer_id=' . $product_info['manufacturer_id'], '&encode');
		$this->data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
		$this->data['product_id'] = $product_id;
		$this->data['average'] = $average;

		$resource = new AResource('image');
		$thumbnail = $resource->getMainThumb('manufacturers',
				$product_info['manufacturer_id'],
				(int)$this->config->get('config_image_grid_width'),
				(int)$this->config->get('config_image_grid_height')
		);
		if(!preg_match('/no_image/', $thumbnail['thumb_url'])){
			$this->data['manufacturer_icon'] = $thumbnail['thumb_url'];
		}

		// Preapare options and values for display 
		$elements_with_options = HtmlElementFactory::getElementsWithOptions();
		$options = array();
		$product_options = $this->model_catalog_product->getProductOptions($product_id);


		//get info from cart if key presents
		$cart_product_info = array();
		if($key){
			$cart_product_info = $this->cart->getProduct($request['key']);
		}

		foreach($product_options as $option){
			$values = array();
			$name = $price = '';
			$default_value = $cart_product_info['options'][$option['product_option_id']];
			if($option['element_type'] == 'R'){
				$default_value = is_array($default_value) ? current($default_value) : (string)$default_value;
			}
			$preset_value = $default_value;
			$opt_stock_message = '';
			foreach($option['option_value'] as $option_value){
				$default_value = $option_value['default'] && !$default_value ? $option_value['product_option_value_id'] : $default_value;

				// for case when trying to add to cart withot required options. we get option-array back inside _GET
				if(has_value($request['option'][$option['product_option_id']])){
					$default_value = $request['option'][$option['product_option_id']];
				}

				$name = $option_value['name'];
				//check if we disable options based on out of stock setting
				if($option_value['subtract'] && $this->config->get('config_nostock_autodisable') && $option_value['quantity'] <= 0){
					continue;
				}

				//Apply option price modifier
				if($option_value['prefix'] == '%'){
					$price = $this->tax->calculate(
							($product_price * $option_value['price'] / 100),
							$product_info['tax_class_id'],
							(bool)$this->config->get('config_tax'));
					if($price != 0){
						$price = $this->currency->format($price);
					} else{
						$price = '';
					}
				} else{
					$price = $this->tax->calculate($option_value['price'], $product_info['tax_class_id'], (bool)$this->config->get('config_tax'));
					if($price != 0){
						$price = $this->currency->format($price);
					} else{
						$price = '';
					}
				}
				//Check stock and status
				$opt_stock_message = '';
				if($option_value['subtract']){
					if($option_value['quantity'] <= 0){
						//show out of stock message
						$opt_stock_message = $product_info['stock_status'];
					} else{
						if($this->config->get('config_stock_display')){
							$opt_stock_message = $option_value['quantity'] . " " . $this->language->get('text_instock');
						}
					}
				}
				$values[$option_value['product_option_value_id']] = $option_value['name'] . ' ' . $price . ' ' . $opt_stock_message;
			}

			//if not values are build, nothing to show
			if(count($values)){
				$value = '';
				//add price to option name if it is not element with options
				if(!in_array($option['element_type'], $elements_with_options)){
					$option['name'] .= ' <small>' . $price . '</small>';
					if($opt_stock_message){
						$option['name'] .= '<br />' . $opt_stock_message;
					}
					$value = $default_value ? $default_value : $name;
				}

				//set default selection is nothing selected
				if(!has_value($value)){
					if(has_value($default_value)){
						$value = $default_value;
					}
				}

				//for checkbox with empty value
				if($value == '' && $option['element_type'] == 'C'){
					$value = 1;
				}

				$option_data = array(
						'type'           => $option['html_type'],
						'name'           => !in_array($option['element_type'], HtmlElementFactory::getMultivalueElements()) ? 'option[' . $option['product_option_id'] . ']' : 'option[' . $option['product_option_id'] . '][]',
						'value'          => $value,
						'options'        => $values,
						'required'       => $option['required'],
						'placeholder'    => $option['option_placeholder'],
						'regexp_pattern' => $option['regexp_pattern'],
						'error_text'     => $option['error_text']
				);
				if($option['element_type'] == 'C'){
					if(!in_array($value, array('0', '1'))){
						$option_data['label_text'] = $value;
					}
					$option_data['checked'] = $preset_value ? true : false;
				}

				$options[] = array(
						'name' => $option['name'],
						'html' => $this->html->buildElement($option_data),  // not a string!!! it's object!
				);
			}
		}
		$this->data['options'] = $options;

		//handle stock messages
		// if track stock is off. no messages needed. 
		if($this->model_catalog_product->isStockTrackable($product_id)){
			$total_quantity = $this->model_catalog_product->hasAnyStock($product_id);
			$this->data['track_stock'] = true;
			//out of stock if no quantity and no stick checkout is disabled
			if($total_quantity <= 0 && !$this->config->get('config_stock_checkout')){
				$this->data['in_stock'] = false;
				//show out of stock message
				$this->data['stock'] = $product_info['stock_status'];
			} else{
				$this->data['in_stock'] = true;
				if($this->config->get('config_stock_display')){
					$this->data['stock'] = $product_info['quantity'];
				} else{
					$this->data['stock'] = $this->language->get('text_instock');
				}
			}

			//check if we need to disable product for no stock
			if($this->config->get('config_nostock_autodisable') && $total_quantity <= 0){
				//set available data
				$pd_identifiers = "ID: " . $product_id;
				$pd_identifiers .= (empty($product_info['model']) ? '' : " Model: " . $product_info['model']);
				$pd_identifiers .= (empty($product_info['sku']) ? '' : " SKU: " . $product_info['sku']);
				$message_ttl = sprintf($this->language->get('notice_out_of_stock_ttl'), $product_info['name']);
				$message_txt = sprintf($this->language->get('notice_out_of_stock_body'), $product_info['name'], $pd_identifiers);
				//record to message box
				$msg = new AMessage();
				$msg->saveNotice($message_ttl, $message_txt);
				$this->model_catalog_product->updateStatus($product_id, 0);
				$this->redirect($this->html->getSEOURL('product/product', '&product_id=' . $product_info['product_id'], '&encode'));
			}
		}

		// main product image
		$sizes = array('main'  => array('width'  => $this->config->get('config_image_popup_width'),
		                                'height' => $this->config->get('config_image_popup_height')),
		               'thumb' => array('width'  => $this->config->get('config_image_thumb_width'),
		                                'height' => $this->config->get('config_image_thumb_height')));
		$this->data['image_main'] = $resource->getResourceAllObjects('products', $product_id, $sizes, 1, false);
		if($this->data['image_main']){
			$this->data['image_main']['sizes'] = $sizes;
		}

		// additional images
		$sizes = array('main'  => array('width'  => $this->config->get('config_image_popup_width'),
		                                'height' => $this->config->get('config_image_popup_height')),
		               'thumb' => array('width'  => $this->config->get('config_image_additional_width'),
		                                'height' => $this->config->get('config_image_additional_height')),
		               'thumb2' => array('width'  => $this->config->get('config_image_thumb_width'),
		                                'height' => $this->config->get('config_image_thumb_height')));
		$this->data['images'] = $resource->getResourceAllObjects('products', $product_id, $sizes, 0, false);

		$products = array();
		$results = $this->model_catalog_product->getProductRelated($product_id);

		foreach($results as $result){

			// related product image
			$sizes = array('main'  => array('width'  => $this->config->get('config_image_related_width'),
			                                'height' => $this->config->get('config_image_related_height')),
			               'thumb' => array('width'  => $this->config->get('config_image_related_width'),
			                                'height' => $this->config->get('config_image_related_height')));
			$image = $resource->getResourceAllObjects('products', $result['product_id'], $sizes, 1);


			if($this->config->get('enable_reviews')){
				$rating = $this->model_catalog_review->getAverageRating($result['product_id']);
			} else{
				$rating = false;
			}

			$special = false;
			$discount = $promotion->getProductDiscount($result['product_id']);
			if($discount){
				$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], (bool)$this->config->get('config_tax')));
			} else{
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], (bool)$this->config->get('config_tax')));
				$special = $promotion->getProductSpecial($result['product_id']);
				if($special){
					$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], (bool)$this->config->get('config_tax')));
				}
			}

			$options = $this->model_catalog_product->getProductOptions($result['product_id']);

			if($options){
				$add = $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode');
			} else{
				if($this->config->get('config_cart_ajax')){
					$add = '#';
				} else{
					$add = $this->html->getSecureURL($this->routes['cart_rt'], '&product_id=' . $result['product_id'], '&encode');
				}
			}

			$products[] = array(
					'product_id'    => $result['product_id'],
					'name'          => $result['name'],
					'model'         => $result['model'],
					'rating'        => $rating,
					'stars'         => sprintf($this->language->get('text_stars'), $rating),
					'price'         => $price,
					'call_to_order' => $result['call_to_order'],
					'options'       => $options,
					'special'       => $special,
					'image'         => $image,
					'href'          => $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode'),
					'add'           => $add
			);
		}

		$this->data['related_products'] = $products;

		if($this->config->get('config_customer_price')){
			$display_price = true;
		} elseif($this->customer->isLogged()){
			$display_price = true;
		} else{
			$display_price = false;
		}
		$this->data['display_price'] = $display_price;

		$tags = array();
		$results = $this->model_catalog_product->getProductTags($product_id);
		foreach($results as $result){
			if($result['tag']){
				$tags[] = array(
						'tag'  => $result['tag'],
						'href' => $this->html->getURL('product/search', '&keyword=' . $result['tag'], '&encode')
				);
			}
		}
		$this->data['tags'] = $tags;

		//downloads before order if allowed
		if($this->config->get('config_download')){
			$dwn = new ADownload();
			$download_list = $dwn->getDownloadsBeforeOrder($product_id);
			if($download_list){
				$downloads = array();

				foreach($download_list as $download){
					$href = $this->html->getURL('account/download/startdownload', '&download_id=' . $download['download_id']);
					$download['attributes'] = $this->download->getDownloadAttributesValuesForCustomer($download['download_id']);

					$download['button'] = $form->getFieldHtml(
							array('type'  => 'button',
							      'id'    => 'download_' . $download['download_id'],
							      'href'  => $href,
							      'title' => $this->language->get('text_start_download'),
							      'text'  => $this->language->get('text_start_download')));

					$downloads[] = $download;
				}


				$this->data['downloads'] = $downloads;
			}
		}

		#check if product is in a wishlist 
		$this->data['is_customer'] = false;
		if($this->customer->isLogged() || $this->customer->isUnauthCustomer()){
			$this->data['is_customer'] = true;
			$whishlist = $this->customer->getWishList();
			if($whishlist[$product_id]){
				$this->data['in_wishlist'] = true;
			}
		}

		$this->view->setTemplate('pages/product/product.tpl');


		$this->view->batchAssign($this->data);
		$this->processTemplate();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _product_not_found($product_id){

		$this->_init();

		$url = $this->_build_url_params();

		$this->document->addBreadcrumb(array(
				'href'      => $this->html->getSEOURL('product/product', $url . '&product_id=' . $product_id, '&encode'),
				'text'      => $this->language->get('text_error'),
				'separator' => $this->language->get('text_separator')
		));


		$this->document->setTitle($this->language->get('text_error'));

		$this->data['heading_title'] = $this->language->get('text_error');
		$this->data['text_error'] = $this->language->get('text_error');
		$continue = HtmlElementFactory::create(array('type'  => 'button',
		                                             'name'  => 'continue_button',
		                                             'text'  => $this->language->get('button_continue'),
		                                             'style' => 'button'));
		$this->view->assign('button_continue', $continue);
		$this->data['continue'] = $this->html->getURL('index/home');

		$this->view->setTemplate('pages/error/not_found.tpl');

		$this->view->batchAssign($this->data);
		$this->processTemplate();
	}

	private function _build_url_params(){
		$url = '';
		if(isset($request['path'])){
			$url .= '&path=' . $request['path'];
		}

		if(isset($request['manufacturer_id'])){
			$url .= '&manufacturer_id=' . $request['manufacturer_id'];
		}

		if(isset($request['keyword'])){
			$url .= '&keyword=' . $request['keyword'];
		}

		if(isset($request['category_id'])){
			$url .= '&category_id=' . $request['category_id'];
		}

		if(isset($request['description'])){
			$url .= '&description=' . $request['description'];
		}

		return $url;
	}

}