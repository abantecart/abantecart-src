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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesProductSearch extends AController {
	private $category;
	private $path;
	public $data = array();
	public function main() {
		$request = $this->request->get;
		$this->path = explode(',',$request['category_id']);

		//is this an embed mode
		if($this->config->get('embed_mode') == true){
			$cart_rt = 'r/checkout/cart/embed';
		} else{
			$cart_rt = 'checkout/cart';
		}

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		$this->document->resetBreadcrumbs();

   		$this->document->addBreadcrumb( array (  
       		'href'      => $this->html->getURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));

		$url = '';
		
		if (isset($request['keyword'])) {
			$url .= '&keyword=' . $request['keyword'];
		}
		
		if (isset($request['category_id'])) {
			$url .= '&category_id=' . $request['category_id'];
		}
		
		if (isset($request['description'])) {
			$url .= '&description=' . $request['description'];
		}
		
		if (isset($request['model'])) {
			$url .= '&model=' . $request['model'];
		}

		if (isset($request['sort'])) {
			$url .= '&sort=' . $request['sort'];
		}	

		if (isset($request['order'])) {
			$url .= '&order=' . $request['order'];
		}
				
		if (isset($request['page'])) {
			$url .= '&page=' . $request['page'];
		}
        if (isset($request['limit'])) {
            $url .= '&limit=' . $request['limit'];
        }
			
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getURL('product/search',  $url),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => $this->language->get('text_separator')
   		 ));

  		if (isset($request['page'])) {
			$page = $request['page'];
		} else {
			$page = 1;
		}

		if (isset($request['sort'])) {
			$sorting_href = $request['sort'];
		} else {
			$sorting_href = $this->config->get('config_product_default_sort_order');
		}

		list($sort,$order) = explode("-",$sorting_href);
		if($sort=='name'){
			$sort = 'pd.'.$sort;
		}elseif(in_array($sort,array('sort_order','price'))){
			$sort = 'p.'.$sort;
		}

		$this->data['keyword'] = $this->html->buildElement(
				array ( 'type' => 'input',
			            'name'=>'keyword',
			            'value'=> $request['keyword'],
		        ));

		$this->loadModel('catalog/category');
		$categories = $this->_getCategories(0);
		$options = array( 0 =>$this->language->get('text_category'));
		if($categories){
			foreach( $categories as $item){
				$options[$item['category_id']] = $item['name'];
			}
		}
		$this->data['category'] = $this->html->buildElement( array ( 'type' => 'selectbox',
																	   'name' => 'category_id',
																	   'options'=> $options,
																	   'value'=> $request['category_id'],
															 ) );

		$this->data['description'] = $this->html->buildElement( array('type' => 'checkbox',
																		'id' => 'description',
																		'name' => 'description',
																		'checked' => (int)$request['description'],
																		'value' => 1,
																		'label_text' => $this->language->get('entry_description')
																   ));

		$this->data['model'] = $this->html->buildElement( array(     'type' => 'checkbox',
														'id' => 'model',
														'name' => 'model',
														'checked' => (bool)$request['model'],
														'value' => 1,
			                                            'label_text' => $this->language->get('entry_model')
		                                           ));

		$this->data['submit'] = $this->html->buildElement( array (
			                                                    'type' => 'button',
		                                                        'name' => 'search_button',
			                                                    'text'=> $this->language->get('button_search'),
			                                                    'icon' => 'fa fa-search',
			                                                    'style' => 'btn-default'));



		if (isset($request['keyword'])) {
			$this->loadModel('catalog/product');
			$promotion = new APromotion();
			if(isset($request['category_id'])){
				$category_id = explode(',',$request['category_id']);
				end($category_id);
				$category_id = current($category_id);
			}else{
				$category_id = '';
			}


			$product_total = $this->model_catalog_product->getTotalProductsByKeyword($request['keyword'],
			                                                                         $category_id,
			                                                                         isset($request['description']) ? $request['description'] : '',
			                                                                         isset($request['model']) ? $request['model'] : '');

			if ($product_total) {
				$url = '';

				if (isset($request['category_id'])) {
					$url .= '&category_id=' . $request['category_id'];
				}
		
				if (isset($request['description'])) {
					$url .= '&description=' . $request['description'];
				}
				
				if (isset($request['model'])) {
					$url .= '&model=' . $request['model'];
				}

                $limit = $this->config->get('config_catalog_limit');
                if (isset($request['limit']) && intval($request['limit']) > 0 ) {
					$limit = intval($request['limit']);
                    if ( $limit > 50 ) $limit = 50;
				}
				
				$this->loadModel('catalog/review');
				$this->loadModel('tool/seo_url');
				
        		$products = array();

                $products_result = $this->model_catalog_product->getProductsByKeyword(  $request['keyword'],
																				$category_id,
																				isset($request['description']) ? $request['description'] : '',
																				isset($request['model']) ? $request['model'] : '',
																				$sort,
																				$order,
																				($page - 1) * $limit,
																				$limit
                );

				//if single result, redirect to the product
				if (count($products_result) == 1) {
					$this->redirect($this->html->getSEOURL('product/product','&product_id=' . key($products_result), '&encode'));		
				}

				if (is_array($products_result) && $products_result) {

					$product_ids = array();
					foreach($products_result as $result){
						$product_ids[] = (int)$result['product_id'];
					}

					//Format product data specific for confirmation page
			        $resource = new AResource('image');
					$thumbnails = $resource->getMainThumbList(
									'products',
									$product_ids,
									$this->config->get('config_image_product_width'),
									$this->config->get('config_image_product_height')
					);
					$stock_info = $this->model_catalog_product->getProductsStockInfo($product_ids);

					foreach ($products_result as $result) {
						$thumbnail = $thumbnails[$result['product_id']];
						if ($this->config->get('enable_reviews')) {
							$rating = $this->model_catalog_review->getAverageRating($result['product_id']);	
						} else {
							$rating = false;
						}
						
						$special = FALSE;
						
						$discount = $promotion->getProductDiscount($result['product_id']);
	 					
						if ($discount) {
							$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
						} else {
							$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
							$special = $promotion->getProductSpecial($result['product_id']);
							if ($special) {
								$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
							}					
						}
						
						$options = $this->model_catalog_product->getProductOptions($result['product_id']);
						if ($options) {
							$add = $this->html->getSEOURL('product/product','&product_id=' . $result['product_id'], '&encode');
						} else {
	                        if($this->config->get('config_cart_ajax')){
	                            $add = '#';
	                        }else{
		                        $add = $this->html->getSecureURL($cart_rt, '&product_id=' . $result['product_id'], '&encode');
	                        }
						}

						//check for stock status, availability and config
						$track_stock = false;
						$in_stock = false;
						$no_stock_text = $result['stock'];
						$total_quantity = 0;
						if ( $stock_info[$result['product_id']]['subtract']  ) {
							$track_stock = true;
			    			$total_quantity = $stock_info[$result['product_id']]['quantity'];
			    			//we have stock or out of stock checkout is allowed
			    			if ($total_quantity > 0 || $this->config->get('config_stock_checkout')) {
				    			$in_stock = true;
			    			}
						}
						
						$products[] = array(
							'product_id' => $result['product_id'],
	            			'name'    => $result['name'],
				            'blurb' => $result['blurb'],
							'model'   => $result['model'],
							'rating'  => $rating,
							'stars'   => sprintf($this->language->get('text_stars'), $rating),
	            			'thumb'   => $thumbnail,
	            			'price'   => $price,
							'call_to_order'=> $result['call_to_order'],
	            			'options' => $options,
							'special' => $special,
							'href'    => $this->html->getSEOURL('product/product','&keyword=' . $request['keyword'] . $url . '&product_id=' . $result['product_id'], '&encode'),
							'add'	  => $add,
							'description'	=> html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
							'track_stock' => $track_stock,
							'in_stock'		=> $in_stock,
							'no_stock_text' => $no_stock_text,
							'total_quantity'=> $total_quantity,
	          			);
	        		}
				}

				$this->data['products'] = $products;
				
				if ($this->config->get('config_customer_price')) {
					$display_price = TRUE;
				} elseif ($this->customer->isLogged()) {
					$display_price = TRUE;
				} else {
					$display_price = FALSE;
				}
                $this->data['display_price'] = $display_price;
				
				$url = '';
				
				if (isset($request['keyword'])) {
					$url .= '&keyword=' . $request['keyword'];
				}
				
				if (isset($request['category_id'])) {
					$url .= '&category_id=' . $request['category_id'];
				}
				
				if (isset($request['description'])) {
					$url .= '&description=' . $request['description'];
				}
				
				if (isset($request['model'])) {
					$url .= '&model=' . $request['model'];
				}

				if (isset($request['page'])) {
					$url .= '&page=' . $request['page'];
				}
                if (isset($request['limit'])) {
                    $url .= '&limit=' . $request['limit'];
                }
				
				$sorts = array();
				$sorts[] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'p.sort_order-ASC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=p.sort_order&order=ASC', '&encode')
				);
				
				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_name_asc'),
					'value' => 'pd.name-ASC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=pd.name&order=ASC', '&encode')
				); 

				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_name_desc'),
					'value' => 'pd.name-DESC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=pd.name&order=DESC', '&encode')
				);  

				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_price_asc'),
					'value' => 'p.price-ASC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=p.price&order=ASC', '&encode')
				); 

				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_price_desc'),
					'value' => 'p.price-DESC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=p.price&order=DESC', '&encode')
				); 
				
				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=rating&order=DESC', '&encode')
				); 
				
				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->html->getURL('product/search', $url . '&sort=rating&order=ASC', '&encode')
				);

				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_date_desc'),
					'value' => 'date_modified-DESC',
					'href'  => $this->html->getSEOURL('product/search', $url . '&sort=date_modified&order=DESC', '&encode')
				);

				$sorts[] = array(
					'text'  => $this->language->get('text_sorting_date_asc'),
					'value' => 'date_modified-ASC',
					'href'  => $this->html->getSEOURL('product/search', $url . '&sort=date_modified&order=ASC', '&encode')
				);

                $this->data['sorts'] = $sorts;
				$options = array();
				foreach($sorts as $item){
					$options[$item['value']] = $item['text'];
				}

				$sorting = $this->html->buildElement( array (
												 'type' => 'selectbox',
		                                         'name' => 'sort',
			                                     'options'=> $options,
			                                     'value'=> $sort.'-'.$order
		                                         ) );

				$this->data['sorting'] = $sorting;

				$url = '';

				if (isset($request['keyword'])) {
					$url .= '&keyword=' . $request['keyword'];
				}
				if (isset($request['category_id'])) {
					$url .= '&category_id=' . $request['category_id'];
				}
				
				if (isset($request['description'])) {
					$url .= '&description=' . $request['description'];
				}
				
				if (isset($request['model'])) {
					$url .= '&model=' . $request['model'];
				}
				
				if (isset($request['sort'])) {
					$url .= '&sort=' . $request['sort'];
				}	

				if (isset($request['order'])) {
					$url .= '&order=' . $request['order'];
				}
                if (isset($request['limit'])) {
                    $url .= '&limit=' . $request['limit'];
                }

				$this->data['pagination_bootstrap'] = $this->html->buildElement( array (
											'type' => 'Pagination',
											'name' => 'pagination',
											'text'=> $this->language->get('text_pagination'),
											'text_limit' => $this->language->get('text_per_page'),
											'total'	=> $product_total,
											'page'	=> $page,
											'limit'	=> $limit,
											'url' => $this->html->getURL('product/search',  $url . '&page={page}', '&encode'),
											'style' => 'pagination'));
                $this->data['sort'] = $sort;
                $this->data['order'] = $order;
                $this->data['limit'] = $limit;

			}
		}
		$this->data['review_status'] = $this->config->get('enable_reviews');
		
        $this->view->batchAssign($this->data);
		$this->processTemplate('pages/product/search.tpl');
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
	
	private function _getCategories($parent_id, $level = 0) {
		$level++;
		$data = array();
		$cat_id = explode(',',$parent_id);
		end($cat_id); 
		$results = $this->model_catalog_category->getCategories(current($cat_id));

		foreach ($results as $result) {

			if(in_array($result['category_id'],$this->path)){
				$this->category = $result['category_id'];
			}else{
				$this->category = 0;
			}

			$data[] = array(
				'category_id' => $parent_id.','.$result['category_id'],
				'name'        => str_repeat('&nbsp;&nbsp;&nbsp;', $level) . $result['name']
			);
			$children = array();
			if($this->category){
				$children = $this->_getCategories($parent_id.','.$result['category_id'], $level);
			}
			
			if ($children) {
			  $data = array_merge($data, $children);
			}
			unset($children);
		}
		
		return $data;
	}	
}
