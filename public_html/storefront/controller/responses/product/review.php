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
class ControllerResponsesProductReview extends AController {
	private $error = array();
	public $data = array();
	
	public function review() {
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('product/product');
		$this->loadModel('catalog/review');

        $this->view->assign('text_no_reviews', $this->language->get('text_no_reviews') );

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  
		
		$reviews = array();
		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);
		foreach ($results as $result) {
        	$reviews[] = array(
        		'author'     => $result['author'],
				'rating'     => $result['rating'],
				'text'       => strip_tags($result['text']),
        		'stars'      => sprintf($this->language->get('text_stars'), $result['rating']),
        		'date_added' => dateISO2Display($result['date_added'],$this->language->get('date_format_short'))
        	);
      	}
		$this->data['reviews'] =  $reviews;
		
		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$this->data['pagination_bootstrap'] = HtmlElementFactory::create( array (
									'type' => 'Pagination',
									'name' => 'pagination',
									'text'=> $this->language->get('text_pagination'),
									'text_limit' => $this->language->get('text_per_page'),
									'total'	=> $review_total,
									'page'	=> $page,
									'limit'	=> 5,
									'url' => $this->html->getURL('product/review/review','&product_id=' . $this->request->get['product_id'] . '&page={page}'),
									'style' => 'pagination'));

		$this->view->batchAssign($this->data);

        $this->processTemplate('responses/product/review.tpl' );
        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	public function write() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('product/product');
		$this->loadModel('catalog/review');
		$json = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate()) {
			$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);
			unset($this->session->data['captcha']);
			$json['success'] = $this->language->get('text_success');
		} else {
			$json['error'] = $this->error['message'];
		}

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($json));
	}
	
	private function _validate() {
		if ((strlen(utf8_decode($this->request->post['name'])) < 3) || (strlen(utf8_decode($this->request->post['name'])) > 25)) {
			$this->error['message'] = $this->language->get('error_name');
		}
		
		if ((strlen(utf8_decode($this->request->post['text'])) < 25) || (strlen(utf8_decode($this->request->post['text'])) > 1000)) {
			$this->error['message'] = $this->language->get('error_text');
		}

		if (!$this->request->post['rating']) {
			$this->error['message'] = $this->language->get('error_rating');
		}

		if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
			$this->error['message'] = $this->language->get('error_captcha');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
