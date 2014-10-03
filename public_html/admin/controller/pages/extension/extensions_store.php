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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}

/**
 * Class ControllerPagesExtensionExtensionsStore
 * @property ModelToolMPAPI $model_tool_mp_api
 */
class ControllerPagesExtensionExtensionsStore extends AController {
	public $data;
	public function main(){
		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle(array(
			'href' => RDIR_TEMPLATE . 'stylesheet/marketplace.css',
			'rel' => 'stylesheet'
		));

		$this->document->initBreadcrumb(array(
			'href' => $this->html->getSecureURL('index/home'),
			'text' => $this->language->get('text_home'),
			'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
			'href' => $this->html->getSecureURL('extension/extensions_store'),
			'text' => $this->language->get('heading_title'),
			'separator' => ' :: ',
			'current' => true
		));

		if (isset($this->session->data[ 'success' ])) {
			$this->data[ 'success' ] = $this->session->data[ 'success' ];
			unset($this->session->data[ 'success' ]);
		} else {
			$this->data[ 'success' ] = '';
		}

		if (isset($this->session->data[ 'error' ])) {
			$this->data[ 'error_warning' ] .= $this->session->data[ 'error' ];
			unset($this->session->data[ 'error' ]);
		}else{
			$this->data[ 'error_warning' ] = '';
		}

		$this->loadModel('tool/mp_api');

		$request_data = $this->request->get;
		if(!has_value($request_data['sidx'])){
			$request_data['sidx'] = 'rating';
		}
		if(!has_value($request_data['sord'])){
			$request_data['sord'] = 'desc';
		}
		if(has_value($request_data['limit'])){
			$request_data['rows'] = $request_data['limit'];
		}

		// vendor related
		$result = $this->model_tool_mp_api->processRequest($request_data);
		$this->view->assign('content', $result);

		$remote_store_product_url = $this->model_tool_mp_api->getMPURL().'?mp_token='.$this->session->data['mp_token'].'&mp_hash='.$this->session->data['mp_hash'];
		$this->view->assign('remote_store_product_url',$remote_store_product_url);

		$form = new AForm('ST');
		$form->setForm(array(
							'form_name' => 'extension_store_search',
					   ));

		$this->data['form']['form_open'] = $form->getFieldHtml(
													array(
														'type' => 'form',
														'name' => 'extension_store_search',
														'action' => $this->html->getSecureURL('extension/extensions_store'),
														'method' => 'get'
												   ));

		$this->data['form']['input'] = $form->getFieldHtml(array(
														'type' => 'input',
														'name' => 'keyword',
														'value' => $this->request->get['keyword'],
														'style'=> 'pull-left',
														'placeholder'=> $this->language->get('search'),
												   ));
		$this->data['form']['input'] .= $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'rt',
														'value' => $this->request->get['rt']
												   ));
		$this->data['form']['input'] .= $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 's',
														'value' => $this->request->get['s']
												   ));
		$this->data['form']['input'] .= $form->getFieldHtml(array(
														'type' => 'hidden',
														'name' => 'token',
														'value' => $this->request->get['token']
												   ));

		if( $result['products']['rows']	){
			$uri = '&limit='.$result['products']['limit'];
			if(has_value($this->request->get['keyword'])){
				$uri .= '&keyword='.$this->request->get['keyword'];
			}
			if(has_value($this->request->get['category_id'])){
				$uri .= '&category_id='.$this->request->get['category_id'];
			}
			if(has_value($this->request->get['manufacturer_id'])){
				$uri .= '&manufacturer_id='.$this->request->get['manufacturer_id'];
			}
			$sort_order = '&sidx='.$result['products']['sidx'].'&sord='.strtoupper($result['products']['sord']);

			$this->data['pagination_bootstrap'] = HtmlElementFactory::create(array(
							'type' => 'Pagination',
							'name' => 'pagination',
							'text' => $this->language->get('text_pagination'),
							'text_limit' => '',
							'total' => $result['products']['records'],
							'page' => $result['products']['page'],
							'limit' => $result['products']['limit'],
							'url' => $this->html->getSecureURL('extension/extensions_store', $uri.$sort_order . '&page={page}', '&encode'),
		    				'size_class' => 'sm',
		    				'no_perpage' => true,
							'style' => 'pagination'));

			$sorts = array();

			$sorts['&sidx=review&sord=DESC'] = $this->language->get('text_sorting_review_desc');
			$sorts['&sidx=review&sord=ASC'] = $this->language->get('text_sorting_review_asc');
			$sorts['&sidx=price&sord=ASC'] = $this->language->get('text_sorting_price_asc');
			$sorts['&sidx=price&sord=DESC'] = $this->language->get('text_sorting_price_desc');
			$sorts['&sidx=rating&sord=DESC'] = $this->language->get('text_sorting_rating_desc');
			$sorts['&sidx=rating&sord=ASC'] = $this->language->get('text_sorting_rating_asc');
			$sorts['&sidx=date_modified&sord=ASC'] = $this->language->get('text_sorting_date_modified_desc');
			$sorts['&sidx=date_modified&sord=DESC'] = $this->language->get('text_sorting_date_modified_asc');

			$this->data['listing_url'] = $this->html->getSecureURL('extension/extensions_store', $uri);
			$this->data['sorting'] = HtmlElementFactory::create(array(
							'type' => 'selectbox',
							'name' => 'sorting',
							'value'=> $sort_order,
							'options' => $sorts));
		}

		$this->data['my_extensions'] = $this->html->getSecureURL('extension/extensions');
		$this->data['my_account'] = $this->model_tool_mp_api->getMPURL().'?rt=account/account&mp_token='.$this->session->data['mp_token'].'&mp_hash='.$this->session->data['mp_hash'];
		
		$this->view->batchAssign($this->data);

		$this->processTemplate('pages/extension/extensions_store.tpl');
	}
}