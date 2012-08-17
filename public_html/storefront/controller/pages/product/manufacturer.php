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
class ControllerPagesProductManufacturer extends AController {
	public $data = array();
	
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('catalog/manufacturer');
		$this->loadModel('catalog/product');
		$this->loadModel('tool/seo_url'); 
		$this->loadModel('tool/image');
		
		$this->document->resetBreadcrumbs();
		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 ));

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = $this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}
		
		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
	
		if ($manufacturer_info) {
      		$this->document->addBreadcrumb( array ( 
        		'href'      => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'], '&encode'),
        		'text'      => $manufacturer_info['name'],
        		'separator' => $this->language->get('text_separator')
      		 ));
					  		
			$this->document->setTitle( $manufacturer_info['name'] );
									
            $this->view->assign('heading_title', $manufacturer_info['name'] );
            $this->view->assign('text_sort', $this->language->get('text_sort') );

			$product_total = $this->model_catalog_product->getTotalProductsByManufacturerId($this->request->get['manufacturer_id']);
			
			if ($product_total) {
				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}
				if (isset($this->request->get['limit'])) {
					$limit = (int)$this->request->get['limit'];
					$limit = $limit>50 ? 50 : $limit;
				} else {
					$limit = $this->config->get('config_catalog_limit');
				}

				if (isset($this->request->get['sort'])) {
					list($sort,$order) = explode("-",$this->request->get['sort']);
				} else {
					$sort = 'p.sort_order';
				}
				
				$this->loadModel('catalog/review');
				
                $this->view->assign('button_add_to_cart', $this->language->get('button_add_to_cart') );
				
				$products = array();
        		
				$results = $this->model_catalog_product->getProductsByManufacturerId($this->request->get['manufacturer_id'],
				                                                                     $sort,
				                                                                     $order,
				                                                                     ($page - 1) * $limit,
				                                                                     $limit);
				foreach($results as $result){
					$product_ids[] = (int)$result['product_id'];
				}
				$products_info = $this->model_catalog_product->getProductsAllInfo($product_ids);

				$resource = new AResource('image');

        		foreach ($results as $result) {
					$thumbnail = $resource->getMainThumb('products',
			                                     $result['product_id'],
			                                     $this->config->get('config_image_product_width'),
			                                     $this->config->get('config_image_product_height'),true);

					
					$rating = $products_info[$result['product_id']]['rating'];
					
					$special = FALSE;
					
					$discount = $products_info[$result['product_id']]['discount'];
 					
					if ($discount) {
						$price = $this->currency->format($this->tax->calculate($discount, $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					
						$special = $products_info[$result['product_id']]['special'];
					
						if ($special) {
							$special = $this->currency->format($this->tax->calculate($special, $result['tax_class_id'], $this->config->get('config_tax')));
						}						
					}
					
					$options = $products_info[$result['product_id']]['options'];
					
					if ($options) {
						$add = $this->html->getSEOURL('product/product', '&product_id=' . $result['product_id'], '&encode');
					} else {
                        if($this->config->get('config_cart_ajax')){
                            $add = '#';
                        }else{
                            $add = $this->html->getSecureURL('checkout/cart', '&product_id=' . $result['product_id'], '&encode');
                        }
					}
					
          			$products[] = array(
						'product_id' => $result['product_id'],
            			'name'    => $result['name'],
						'model'   => $result['model'],
						'rating'  => $rating,
						'stars'   => sprintf($this->language->get('text_stars'), $rating),            			
						'thumb'   => $thumbnail,
            			'price'   => $price,
            			'options' => $options,
						'special' => $special,
						'href'    => $this->html->getSEOURL('product/product','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&product_id=' . $result['product_id'], '&encode'),
						'add'	  => $add
          			);
        		}
				$this->data['products'] = $products;
				
				if ($this->config->get('config_customer_price')) {
					$display_price = TRUE;
				} elseif ($this->customer->isLogged()) {
					$display_price = TRUE;
				} else {
					$display_price = FALSE;
				}
                $this->view->assign('display_price', $display_price );

				$url = '';
		
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
                if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}
				
				$sorts = array();
				
				$sorts[] = array(
					'text'  => $this->language->get('text_default'),
					'value' => 'p.sort_order-ASC',
					'href'  => $this->html->getSEOURL('product/manufacturer','&path=' . $this->request->get['manufacturer_id'] . '&sort=p.sort_order&order=ASC'.$url, '&encode')
				);
				
				$sorts[] = array(
					'text'  => $this->language->get('text_name_asc'),
					'value' => 'pd.name-ASC',
					'href'  => $this->html->getSEOURL('product/manufacturer', '&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=pd.name&order=ASC'.$url, '&encode')
				);  
 
				$sorts[] = array(
					'text'  => $this->language->get('text_name_desc'),
					'value' => 'pd.name-DESC',
					'href'  => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=pd.name&order=DESC'.$url, '&encode')
				);  

				$sorts[] = array(
					'text'  => $this->language->get('text_price_asc'),
					'value' => 'p.price-ASC',
					'href'  => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.price&order=ASC'.$url, '&encode')
				); 

				$sorts[] = array(
					'text'  => $this->language->get('text_price_desc'),
					'value' => 'p.price-DESC',
					'href'  => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=p.price&order=DESC'.$url, '&encode')
				); 
				
				$sorts[] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=rating&order=DESC'.$url, '&encode')
				); 
				
				$sorts[] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=rating&order=ASC'.$url, '&encode')
				);

				$options = array();
				foreach($sorts as $item){
					$options[$item['value']] = $item['text'];
				}
				$sorting = $this->html->buildSelectbox( array (
													 'name' => 'sort',
													 'options'=> $options,
													 'value'=> $this->request->get['sort']
													 ) );
				$this->view->assign( 'sorting', $sorting );
				$this->view->assign( 'url', $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id']) );
				
				$pagination = new APagination();
				$pagination->total = $product_total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->text = $this->language->get('text_pagination'); $pagination->text_limit = $this->language->get('text_per_page');
				$pagination->url = $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $this->request->get['manufacturer_id'] . '&sort=' . $this->request->get['sort'] . '&page={page}' . '&limit=' . $limit, '&encode');
			
				$this->view->assign('pagination', $pagination->render() );

                $this->view->assign('sort', $sort );
                $this->view->assign('order', $order );
				
				$this->view->setTemplate( 'pages/product/manufacturer.tpl' );
      		} else {
        		$this->document->setTitle( $manufacturer_info['name'] );

        		$this->view->assign('heading_title', $manufacturer_info['name'] );
        		$this->view->assign('text_error', $this->language->get('text_empty') );
				$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
				$this->view->assign('button_continue', $continue->getHtml());

                $this->view->assign('continue',  $this->html->getURL('index/home') );

                $this->view->setTemplate( 'pages/error/not_found.tpl' );
      		}
    	} else {
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
				
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}	
			
      		$this->document->addBreadcrumb( array ( 
        		'href'      => $this->html->getSEOURL('product/manufacturer','&manufacturer_id=' . $manufacturer_id . $url, '&encode'),
        		'text'      => $this->language->get('text_error'),
        		'separator' => $this->language->get('text_separator')
      		 ));

            $this->document->setTitle( $this->language->get('text_error') );

      		$this->view->assign('heading_title', $this->language->get('text_error') );
            $this->view->assign('text_error', $this->language->get('text_error') );
			$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
			$this->view->assign('button_continue', $continue->getHtml());
      		$this->view->assign('continue',  $this->html->getURL('index/home') );

            $this->view->setTemplate( 'pages/error/not_found.tpl' );
		}
		
		$this->view->batchAssign( $this->data );

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
?>