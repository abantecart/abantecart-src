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
class ControllerPagesCatalogProductPromotions extends AController {
	private $error = array();
	public $data = array();
     
  	public function main() {

          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('catalog/product');
    	$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('catalog/product');



		if (isset($this->request->get['product_id']) && $this->request->is_GET()) {
      		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			if ( !$product_info ) {
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
    	}

		if ($this->request->is_POST() && $this->_validateForm()) {
			if($this->request->post['promotion_type']=='discount'){
				if(has_value($this->request->get['product_discount_id'])){ //update
					$this->model_catalog_product->updateProductDiscount($this->request->get['product_discount_id'], $this->request->post);
				}else{ //insert
					$this->model_catalog_product->addProductDiscount($this->request->get['product_id'], $this->request->post);
				}
			}elseif($this->request->post['promotion_type']=='special'){
				if(has_value($this->request->get['product_special_id'])){ //update
					$this->model_catalog_product->updateProductSpecial($this->request->get['product_special_id'], $this->request->post);
				}else{ //insert
					$this->model_catalog_product->addProductSpecial($this->request->get['product_id'], $this->request->post);
				}
			}
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] ));
		}
		  
		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);  

		$this->view->assign('error_warning', $this->error['warning'] = implode('<br>', $this->error));
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

    	$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/product'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product') . ' - '. $this->data['product_description'][$this->session->data['content_language_id']]['name'],
			'separator' => ' :: '
		 ));
		 $this->document->addBreadcrumb( array (
			'href'      => $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id'] ),
			'text'      => $this->language->get('tab_promotions'),
			'separator' => ' :: ',
			'current' => true
		 ));

		$this->loadModel('sale/customer_group');
		$results = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_groups'] = array();
		foreach( $results as $r ) {
            $this->data['customer_groups'][ $r['customer_group_id'] ] = $r['name'];
        }

		$this->data['form_title'] = $this->language->get('text_edit')  .'&nbsp;'. $this->language->get('text_product');
		$this->data['product_discounts'] = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
		$this->data['delete_discount'] = $this->html->getSecureURL('catalog/product_promotions/delete', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=%ID%' );
		$this->data['update_discount'] = $this->html->getSecureURL('catalog/product_discount_form/update', '&product_id=' . $this->request->get['product_id'].'&product_discount_id=%ID%' );

		$this->data['product_specials'] = $this->model_catalog_product->getProductSpecials($this->request->get['product_id']);
		$this->data['delete_special'] = $this->html->getSecureURL('catalog/product_promotions/delete', '&product_id=' . $this->request->get['product_id'].'&product_special_id=%ID%' );
		$this->data['update_special'] = $this->html->getSecureURL('catalog/product_special_form/update', '&product_id=' . $this->request->get['product_id'].'&product_special_id=%ID%' );


		foreach ($this->data['product_discounts'] as $i => $item) {
			if ( $item['date_start'] == '0000-00-00' ) $this->data['product_discounts'][$i]['date_start'] = '';
			if ( $item['date_end'] == '0000-00-00' ) $this->data['product_discounts'][$i]['date_end'] = '';
		}
		foreach ($this->data['product_specials'] as $i => $item) {
			if ( $item['date_start'] == '0000-00-00' ) $this->data['product_specials'][$i]['date_start'] = '';
			if ( $item['date_end'] == '0000-00-00' ) $this->data['product_specials'][$i]['date_end'] = '';
		}

		$this->data['button_remove'] = $this->html->buildElement(
					array( 'type' => 'button',
							'text' => $this->language->get('button_remove'),
							'style' => 'button2',
		));
		$this->data['button_edit'] = $this->html->buildElement(
							array( 'type' => 'button',
									'text' => $this->language->get('button_edit'),
									'style' => 'button2',
		));
		$this->data['button_add_discount'] = $this->html->buildElement(
																array(
																	'type' => 'button',
																	'text' => $this->language->get('button_add_discount'),
																	'href' => $this->html->getSecureURL('catalog/product_discount_form/insert', '&product_id=' . $this->request->get['product_id'] ),
																	'style' => 'button1'
																));
		$this->data['button_add_special'] = $this->html->buildElement(
																array(
																	'type' => 'button',
																	'text' => $this->language->get('button_add_special'),
																	'href' => $this->html->getSecureURL('catalog/product_special_form/insert', '&product_id=' . $this->request->get['product_id'] ),
																	'style' => 'button1'
																));

		$this->data['active'] = 'promotions';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$this->view->assign('help_url', $this->gen_help_url('product_promotions') );
	    if($this->config->get('config_embed_status')){
		    $this->data['embed_url'] = $this->html->getSecureURL('common/do_embed/product', '&product_id=' . $this->request->get['product_id']);
	    }
        $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/catalog/product_promotions.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}


	public function delete() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->loadModel('catalog/product');
		if(has_value($this->request->get['product_discount_id'])){
			$this->model_catalog_product->deleteProductDiscount($this->request->get['product_discount_id']);
		}elseif(has_value($this->request->get['product_special_id'])){
			$this->model_catalog_product->deleteProductSpecial($this->request->get['product_special_id']);
		}
		$this->session->data['success'] = $this->language->get('text_success');
		$this->redirect($this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get['product_id']));

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateForm() {
		if (!$this->user->canModify('catalog/product_promotions')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if(has_value($this->request->post['promotion_type'])){
			if ($this->request->post['date_start'] != '0000-00-00' && $this->request->post['date_end'] != '0000-00-00'
					&& $this->request->post['date_start'] != '' && $this->request->post['date_end'] != ''
					&& dateFromFormat($this->request->post['date_start'], $this->language->get('date_format_short')) > dateFromFormat($this->request->post['date_end'], $this->language->get('date_format_short'))
			) {
				$this->error['date_end'] = $this->language->get('error_date');
			}
		}

		$this->extensions->hk_ValidateData($this,__FUNCTION__);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}