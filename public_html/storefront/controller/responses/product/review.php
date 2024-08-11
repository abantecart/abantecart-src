<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2022 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesProductReview extends AController
{
    public $error = [];

    public function review()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('product/product');
        $this->loadModel('catalog/review');

        $this->view->assign('text_no_reviews', $this->language->get('text_no_reviews'));

        $request = $this->request->get;
        $product_id = (int)$request['product_id'];

        if (isset($request['page'])) {
            $page = $request['page'];
        } else {
            $page = 1;
        }

        $reviews = [];
        if ($this->config->get('display_reviews')) {
            //todo: add this setting in the appearance section of admin
            $perPage = $this->config->get('reviews_per_page') ?: 5;
            $results = $this->model_catalog_review->getReviewsByProductId($product_id, ($page - 1) * $perPage, $perPage);

            foreach ($results as $result) {

                $reviews[] = [
                    'author'            => $result['author'],
                    'rating'            => $result['rating'],
                    'verified_purchase' => $result['verified_purchase'],
                    'initials'          => $this->model_catalog_review->getInitialsReviewUser($result['author']),
                    'text'              => str_replace("\n", '<br />', strip_tags($result['text'])),
                    'stars'             => sprintf($this->language->get('text_stars'), $result['rating']),
                    'date_added'        => dateISO2Display($result['date_added'], $this->language->get('date_format_short')),
                ];
            }

            $this->data['reviews'] = $reviews;

            $review_total = $this->model_catalog_review->getTotalReviewsByProductId($product_id);

            /** @see PaginationHtmlElement */
            $this->data['pagination_bootstrap'] = HtmlElementFactory::create([
                'type'       => 'Pagination',
                'name'       => 'pagination',
                'text'       => $this->language->get('text_pagination'),
                'text_limit' => $this->language->get('text_per_page'),
                'total'      => $review_total,
                'page'       => $page,
                'limit'      => $perPage,
                'no_perpage' => true,
                'url'        => $this->html->getURL('product/review/review', '&product_id='.$product_id.'&page={page}'),
                'direct_url' => $this->html->getURL('product/product', '&product_id='.$product_id.'&page={page}#review'),
                'style'      => 'pagination',
            ]);
        }

        $this->view->batchAssign($this->data);

        $this->processTemplate('responses/product/review.tpl');
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function write()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $product_id = (int)$this->request->get['product_id'];

        $this->loadLanguage('product/product');
        $this->loadModel('catalog/review');
        $json = [];
        if ($this->request->is_POST() && $this->_validate()) {
            $review_id = $this->model_catalog_review->addReview($product_id, $this->request->post);
            unset($this->session->data['captcha']);
            $json['success'] = $this->language->get('text_success');

            //notify admin
            $this->loadLanguage('common/im');
            $message_arr = [
                1 => [
                    'message' => sprintf($this->language->get('im_product_review_text_to_admin'), $review_id),
                ],
            ];
            $this->im->send('product_review', $message_arr, 'storefront_product_review_admin_notify', [
                'product_id'  => $product_id,
                'product_url' => $this->html->getSecureURL('catalog/review/update', '&review_id='.$product_id),
            ]);
        } else {
            $json['error'] = $this->error['message'];
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    private function _validate()
    {
        if (mb_strlen($this->request->post['name']) < 3 || mb_strlen($this->request->post['name']) > 25) {
            $this->error['message'] = $this->language->get('error_name');
        }

        if (mb_strlen($this->request->post['text']) < 25 || mb_strlen($this->request->post['text']) > 1000) {
            $this->error['message'] = $this->language->get('error_text');
        }

        if (!$this->request->post['rating']) {
            $this->error['message'] = $this->language->get('error_rating');
        }

        if ($this->config->get('config_recaptcha_secret_key')) {
            $recaptcha = new \ReCaptcha\ReCaptcha($this->config->get('config_recaptcha_secret_key'));
            $resp = $recaptcha->verify($this->request->post['g-recaptcha-response'],
                $this->request->getRemoteIP());
            if (!$resp->isSuccess() && $resp->getErrorCodes()) {
                $this->error['message'] = $this->language->get('error_captcha');
            }
        } else {
            if (!isset($this->session->data['captcha'])
                || ($this->session->data['captcha'] != $this->request->post['captcha'])
            ) {
                $this->error['message'] = $this->language->get('error_captcha');
            }
        }

        $this->extensions->hk_ValidateData($this);

        return (!$this->error);
    }
}
