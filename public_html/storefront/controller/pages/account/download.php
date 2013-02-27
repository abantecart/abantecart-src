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
class ControllerPagesAccountDownload extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/download');

			$this->redirect($this->html->getSecureURL('account/login'));
		}
         		
		$this->document->setTitle( $this->language->get('heading_title') );

      	$this->document->resetBreadcrumbs();

      	$this->document->addBreadcrumb( array ( 
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
        	'href'      => $this->html->getURL('account/download'),
        	'text'      => $this->language->get('text_downloads'),
        	'separator' => $this->language->get('text_separator')
      	 ));
				
		$download_total = $this->model_account_download->getTotalDownloads();
		if(!$this->config->get('config_download')){
			$download_total =0;
		}

		if (isset($this->request->get['limit'])) {
	        $limit = (int)$this->request->get['limit'];
	        $limit = $limit>50 ? 50 : $limit;
	    } else {
	        $limit = $this->config->get('config_catalog_limit');
	    }

		if ($download_total) {

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}			
	
			$downloads = array();
			
			$results = $this->model_account_download->getDownloads(($page - 1) * $limit, $limit);
			$k=0;
			foreach ($results as $result) {
				$result['filename'] = trim($result['filename']);
				if (file_exists(DIR_RESOURCE . $result['filename']) && $result['filename'] ) {
					$size = filesize(DIR_RESOURCE . $result['filename']);
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

					$link = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'download_button_'.$k,
			                                           'text'=> $this->language->get('text_download'),
			                                           'style' => 'button',
			                                           'href' => $this->html->getSecureURL('account/download/download','&order_download_id=' . $result['order_download_id'])
					                                    )
					);

					$downloads[] = array(
						'order_id'   => $result['order_id'],
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'name'       => $result['name'],
						'remaining'  => $result['remaining'],
						'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
						'link'       => $link->getHtml()
					);
				}
			$k++;
			}
            $this->view->assign('downloads', $downloads );
		
			$pagination = new APagination();
			$pagination->total = sizeof($downloads);
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->text = $this->language->get('text_pagination');
			$pagination->text_limit = $this->language->get('text_per_page');
			$pagination->url = $this->html->getURL('account/download&page={page}');
			
			$this->view->assign( 'pagination', $pagination->render() );
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
			                                           'href' => $this->html->getSecureURL('account/account')));
		$this->data['button_continue'] = $continue->getHtml();
		$this->view->batchAssign($this->data);
        $this->processTemplate($template);

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function download() {

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->html->getSecureURL('account/download');
			$this->redirect($this->html->getSecureURL('account/login'));
		}
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (isset($this->request->get['order_download_id'])) {
			$order_download_id = $this->request->get['order_download_id'];
		} else {
			$order_download_id = 0;
		}
		$this->loadModel('account/download');
		$download_info = $this->model_account_download->getDownload($order_download_id);
		
		if ($download_info) {
			$file = DIR_RESOURCE . $download_info['filename'];
			$mask = basename($download_info['mask']);
			$mime = 'application/octet-stream';
			$encoding = 'binary';

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Pragma: public');
					header('Expires: 0');
					header('Content-Description: File Transfer');
					header('Content-Type: ' . $mime);
					header('Content-Transfer-Encoding: ' . $encoding);
					header('Content-Disposition: attachment; filename=' . ($mask ? $mask : basename($file)));
					header('Content-Length: ' . filesize($file));
					ob_clean();
					flush();
					readfile($file,'rb');
					exit;
				} else {
                    throw new AException(AC_ERR_LOAD, 'Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		
			$this->model_account_download->updateRemaining($this->request->get['order_download_id']);

			//init controller data
            $this->extensions->hk_UpdateData($this,__FUNCTION__);
		} else {
			$this->redirect($this->html->getSecureURL('account/download'));
		}
	}
}
?>