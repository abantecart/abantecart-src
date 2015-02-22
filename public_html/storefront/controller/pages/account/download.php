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
			$resource = new AResource('image');
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
					$download_button = HtmlElementFactory::create(
							array ( 'type' => 'button',
									'name' => 'download_button_'.$download_info['order_download_id'],
									'title'=> $this->language->get('text_download'),
									'text' => $this->language->get('text_download'),
									'style' => 'button',
									'href' => $this->html->getSecureURL('account/download/startdownload','&order_download_id='. $download_info['order_download_id']),
									'icon' => 'fa fa-download-alt'
									)
					);
				}else{
					$download_text = $text_status;
				}

				$thumbnail = $resource->getMainThumb( 'products',
													  $download_info['product_id'],
													  $this->config->get('config_image_cart_width'),
													  $this->config->get('config_image_cart_height'),
													  false );
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
										'total'	=> sizeof($downloads),
										'page'	=> $page,
										'limit'	=> $limit,
										'url' => $this->html->getURL('account/download&page={page}', '&encode'),
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

		if(!$this->config->get('config_download')){ // if downloads not allowed
			$this->redirect($this->html->getSecureURL('account/account'));
		}

		if (has_value($this->request->get['download_id'])) {
			$download_info = $this->download->getDownloadinfo((int)$this->request->get['download_id']);
		} elseif(has_value($this->request->get['order_download_id'])) {
			// check is customer logged
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->html->getSecureURL('account/download');
				$this->redirect($this->html->getSecureURL('account/login'));
			}
			$download_info = $this->download->getOrderDownloadInfo($this->request->get['order_download_id']);
		}else {
			$download_info = array();
		}

		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		if ($download_info) {
			$result = $this->download->sendDownload($download_info);
			if($result===false){
				$this->redirect($this->html->getSecureURL('account/download'));
			}
        } else {
			$this->session->data['warning'] = $this->language->get('error_download_not_exists');
			$this->redirect($this->html->getSecureURL('account/download'));
		}
	}

}