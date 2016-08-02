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
class ControllerPagesAccountDownload extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/download');

			$this->redirect($this->html->getSecureURL('account/login'));
		}
         
        //if disabled downloads redirect to 
        if (!$this->config->get('config_download')) {
        	$this->redirect($this->html->getSecureURL('account/account'));
        }
         		
		$this->document->setTitle( $this->language->get('heading_title') );

      	$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getSecureURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 )); 

      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getSecureURL('account/account'),
        	'text'      => $this->language->get('text_account'),
        	'separator' => $this->language->get('text_separator')
      	 ));
		
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getSecureURL('account/download'),
        	'text'      => $this->language->get('text_downloads'),
        	'separator' => $this->language->get('text_separator')
      	 ));


		if (isset($this->request->get['limit'])) {
	        $limit = (int)$this->request->get['limit'];
	        $limit = $limit>50 ? 50 : $limit;
	    } else {
	        $limit = $this->config->get('config_catalog_limit');
	    }

		if ($this->config->get('config_download')) {

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}			
	
			$downloads = array();
			//get only enabled, not expired, which have remaining count > 0 and available
			$customer_downloads = $this->download->getCustomerDownloads(($page-1) * $limit, $limit);
			$product_ids = array();
			foreach($customer_downloads as $result){
				$product_ids[] = (int)$result['product_id'];
			}
			$resource = new AResource('image');
			$thumbnails = $resource->getMainThumbList(
							'products',
							$product_ids,
							$this->config->get('config_image_cart_width'),
							$this->config->get('config_image_cart_height'),
							false);

			foreach ($customer_downloads as $download_info) {
				$text_status = $this->download->getTextStatusForOrderDownload($download_info);

				$size = filesize(DIR_RESOURCE . $download_info['filename']);
				$i = 0;
				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);
				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}
				if(!$text_status){
					$download_button = $this->html->buildElement(
							array ( 'type' => 'button',
									'name' => 'download_button_'.$download_info['order_download_id'],
									'title'=> $this->language->get('text_download'),
									'text' => $this->language->get('text_download'),
									'style' => 'button',
									'href' => $this->html->getSecureURL(
												'account/download/startdownload',
												'&order_download_id='. $download_info['order_download_id']),
									'icon' => 'fa fa-download-alt'
									)
					);
				}else{
					$download_text = $text_status;
				}

				$thumbnail = $thumbnails[$download_info['product_id']];
				$attributes = $this->download->getDownloadAttributesValuesForCustomer($download_info['download_id']);

				$downloads[] = array(
					'thumbnail'  => $thumbnail,
					'attributes' => $attributes,
					'order_id'   => $download_info['order_id'],
					'date_added' => dateISO2Display($download_info['date_added'],$this->language->get('date_format_short')),
					'name'       => $download_info['name'],
					'remaining'  => $download_info['remaining_count'],
					'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
					'button'	=> $download_button,
					'text'	=> $download_text,
					'expire_date'=> dateISO2Display($download_info['expire_date'], $this->language->get('date_format_short').' '.$this->language->get('time_format_short'))
				);

			}

			$this->data['downloads'] = $downloads;

			$this->data['pagination_bootstrap'] = HtmlElementFactory::create( array (
										'type' => 'Pagination',
										'name' => 'pagination',
										'text'=> $this->language->get('text_pagination'),
										'text_limit' => $this->language->get('text_per_page'),
										'total'	=> $this->download->getTotalDownloads(),
										'page'	=> $page,
										'limit'	=> $limit,
										'url'   => $this->html->getURL('account/download&limit='.$limit.'&page={page}', '&encode'),
										'style' => 'pagination'));



			if($downloads){
				$template = 'pages/account/download.tpl';
			}else{
				$template = 'pages/error/not_found.tpl';
			}
		} else {
			$template = 'pages/error/not_found.tpl';
		}

		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button',
														'icon' => 'fa fa-arrow-right',
			                                           'href' => $this->html->getSecureURL('account/account')));
		$this->data['button_continue'] = $continue;
		$this->view->batchAssign($this->data);
        $this->processTemplate($template);

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function startdownload() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$download_id = (int)$this->request->get['download_id'];
		$order_download_id = (int)$this->request->get['order_download_id'];
		$resource_id = (int)$this->request->get['resource_id'];
		$object_id = (int)$this->request->get['object_id'];
		$object_name = $this->request->get['object_name'];


		if(!$this->config->get('config_download') && !$resource_id ){
			$this->redirect($this->html->getSecureURL('account/account'));
		}
		//send downloads before order
		if ($download_id) {
			$download_info = $this->download->getDownloadinfo($download_id);
			//do not allow download after orders by download_id
			if($download_info && $download_info['activate'] != 'before_order'){
				$download_info = array();
			}
		//send purchased downloads only for logged customers
		} elseif($order_download_id && $this->customer->isLogged()) {
			$download_info = $this->download->getOrderDownloadInfo($order_download_id);
			if($download_info){
				//check is customer can this download
				$customer_downloads = $this->getCustomerDownloads();
				if (!in_array($download_info['order_download_id'], array_keys($customer_downloads))){
					$download_info = array();
				}
			}
		}
		// allow download resources only for admin side
		elseif ($resource_id ) {
			$resource = new AResource('download');
			$resource_info = $resource->getResource( $resource_id, $this->language->getLanguageID());
			//override resource_type property of aresource instance
			if($resource_info['type_name']!='download'){
				$resource = new AResource($resource_info['type_name']);
			}
			//allow to download any resource for admin
			if( $resource_info && IS_ADMIN === true){
				$download_info = array(
					'filename' => $resource->getTypeDir().'/'.$resource_info['resource_path'],
					'mask' => ( $resource_info['name'] ? $resource_info['name'] : basename($resource_info['resource_path']) ) ,
					'activate' => 'before_order'
				);
			}
			//for storefront allow to get resource only for id and object assistance except download-resources
			elseif($resource_info && $resource_info['type_name'] != 'download' && $object_id && $object_name){
				$obj_resources = $resource->getResources($object_name, $object_id);
				foreach($obj_resources as $res){
					if($res['resource_id'] == $resource_id){
						$download_info = array(
							'filename' => $resource->getTypeDir().'/'.$resource_info['resource_path'],
							'mask' => ( $resource_info['name'] ? $resource_info['name'] : basename($resource_info['resource_path']) ) ,
							'activate' => 'before_order'
						);
						break;
					}
				}
			}else{
				$download_info = array();
			}
		}else{
			$download_info = array();
		}

		//if info presents - send file to output
		if ($download_info && is_array($download_info)) {
			//if it's ok - send file and exit, otherwise do nothing
			$this->download->sendDownload($download_info);
        }

		$this->session->data['warning'] = $this->language->get('error_download_not_exists');
		$this->redirect($this->html->getSecureURL('account/download'));
	}

}