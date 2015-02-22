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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesCatalogReview extends AController {
	private $error = array();
	public $data = array();
	private $fields = array('status', 'rating', 'text', 'author');
 
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

   		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home')
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/review'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current' => true

   		 ));

		
		$grid_settings = array(
			'table_id' => 'review_grid',
			'url' => $this->html->getSecureURL('listing_grid/review'),
			'editurl' => $this->html->getSecureURL('listing_grid/review/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/review/update_field'),
			'sortname' => 'date_added',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('catalog/review/update', '&review_id=%ID%')
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
                'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
            ),
		);

        $form = new AForm();
	    $form->setForm(array(
		    'form_name' => 'review_grid_search',
	    ));

		$grid_search_form = array();
		$grid_search_form['id'] = 'review_grid_search';
        $grid_search_form['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'review_grid_search',
		    'action' => '',
	    ));
        $grid_search_form['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_go'),
		    'style' => 'button6',
	    ));
		$grid_search_form['reset'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'reset',
		    'text' => $this->language->get('button_reset'),
		    'style' => 'button2',
	    ));

	    $grid_search_form['fields']['product_id'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'product_id',
		    'options' => array( '' => $this->language->get('text_select_product') ) + $this->model_catalog_review->getReviewProducts(),
			'style' => 'chosen'
	    ));

        $grid_search_form['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'status',
            'options' => array(
                1 => $this->language->get('text_enabled'),
                0 => $this->language->get('text_disabled'),
                '' => $this->language->get('text_select_status'),
            ),
	    ));

		$grid_settings['search_form'] = true;

		$grid_settings['colNames'] = array(
            '',
            $this->language->get('column_product'),
			$this->language->get('column_author'),
			$this->language->get('column_rating'),
			$this->language->get('column_status'),
			$this->language->get('column_date_added'),
		);
		$grid_settings['colModel'] = array(
            array(
				'name' => 'image',
				'index' => 'image',
                'align' => 'center',
				'width' => 50,
				'sortable' => false,
				'search' => false,
			),
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 190,
                'align' => 'left',
			),
			array(
				'name' => 'author',
				'index' => 'author',
				'width' => 90,
                'align' => 'center',
			),
            array(
				'name' => 'rating',
				'index' => 'rating',
				'width' => 60,
				'align' => 'center',
                'search' => false,
			),
			array(
				'name' => 'status',
				'index' => 'status',
				'width' => 130,
                'align' => 'center',
                'search' => false,
			),
			array(
				'name' => 'date_added',
				'index' => 'date_added',
				'width' => 90,
                'align' => 'center',
                'search' => false,
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign('search_form', $grid_search_form);

		$this->document->setTitle( $this->language->get('heading_title') );
		$this->view->assign( 'insert', $this->html->getSecureURL('catalog/review/insert') );
		$this->view->assign('help_url', $this->gen_help_url('review_listing') );
		$this->processTemplate('pages/catalog/review_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		
		if ($this->request->is_POST() && $this->_validateForm()) {
			$review_id = $this->model_catalog_review->addReview($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect( $this->html->getSecureURL('catalog/review/update','&review_id='.$review_id) );
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		
		if ( $this->request->is_POST() && $this->_validateForm() ) {
			$this->model_catalog_review->editReview($this->request->get['review_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect( $this->html->getSecureURL('catalog/review/update','&review_id='.$this->request->get['review_id']) );
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}


	private function _getForm() {
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		$this->data['error'] = $this->error;

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/review'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));


		$this->data['cancel'] = $this->html->getSecureURL('catalog/review');

		$review_id = (int)$this->request->get['review_id'];

		if ( $review_id && $this->request->is_GET() ) {
			$review_info = $this->model_catalog_review->getReview( $review_id );
		}

		foreach ( $this->fields as $field ) {
			if (isset($this->request->post[$field])) {
				$this->data[$field] = $this->request->post[$field];
			} elseif (isset($review_info)) {
				$this->data[$field] = $review_info[$field];
			} else {
				$this->data[$field] = '';
			}
		}

		if (isset($this->request->post['product_id'])) {
			$this->data['product_id'] = $this->request->post['product_id'];
		} elseif (isset($review_info)) {
			$this->data['product_id'] = $review_info['product_id'];
		} else {
			$this->data['product_id'] = 0;
		}

		$this->loadModel('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($this->data['product_id']);
		if ($product_info) {
			$this->data['product'] =  $product_info['name'];
			$this->data['preview'] = $this->html->getCatalogURL('product/product', '&product_id='.$this->data['product_id']);
		} else {
			$this->data['product'] = $this->language->get('text_none');
		}

		if (!isset($this->request->get['review_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/review/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .' '. $this->language->get('text_review');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/review/update', '&review_id=' . $review_id );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_review');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/review/update_field','&id='.$review_id );
			$form = new AForm('HS');
		}

		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$form->setForm(array(
		    'form_name' => 'reviewFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'reviewFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'reviewFrm',
		    'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
		    'action' => $this->data['action'],
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'status',
		    'value' => $this->data['status'],
			'style'  => 'btn_switch',
	    ));
		$this->data['form']['fields']['author'] = $form->getFieldHtml(array(
		    'type' => 'input',
		    'name' => 'author',
		    'value' => $this->data['author'],
			'required' => true
	    ));

		$this->data['products'] = array(0=>$this->language->get('text_select_product'));
		$this->loadModel('catalog/product');
		$results = $this->model_catalog_product->getProducts();
		foreach( $results as $r ) {
			$this->data['products'][ $r['product_id'] ] = $r['name'];
		}

		$this->data['form']['fields']['product'] = $form->getFieldHtml(
															array(
																	'type' => 'selectbox',
																	'name' => 'product_id',
																	'value' => $this->data['product_id'],
																	'options' => $this->data['products'],
																	'style' => 'chosen',
																	'placeholder' => $this->language->get('text_select_product'),
																	'required' => true
															));


		$this->data['form']['fields']['text'] = $form->getFieldHtml(array(
		    'type' => 'textarea',
		    'name' => 'text',
		    'value' => $this->data['text'],
			'required' => true,
	    ));		
		$this->data['form']['fields']['rating'] = $form->getFieldHtml(array(
		    'type' => 'rating',
		    'name' => 'rating',
		    'value' => $this->data['rating'],
			'options' => array(1 => 1, 2, 3, 4, 5),
			'required' => true,
            'pack' => false,
	    ));

		$this->view->assign('help_url', $this->gen_help_url('review_edit') );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/catalog/review_form.tpl' );
	}

	
	private function _validateForm() {
		if (!$this->user->canModify('catalog/review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['product_id']) {
			$this->error['product'] = $this->language->get('error_product');
		}
		
		if ( mb_strlen($this->request->post['author']) < 2 || mb_strlen( $this->request->post['author'] ) > 64 ) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (mb_strlen( $this->request->post['text'] ) < 25  || mb_strlen( $this->request->post['text'] ) > 1000 ) {
			$this->error['text'] = $this->language->get('error_text');
		}
		
		if ( mb_strlen($this->request->post['text']) < 25 || mb_strlen( $this->request->post['text'] ) > 1000 ) {
			$this->error['text'] = $this->language->get('error_text');
		}
		
		if (!isset($this->request->post['rating'])) {
			$this->error['rating'] = $this->language->get('error_rating');
		}

		$this->extensions->hk_ValidateData($this);
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
