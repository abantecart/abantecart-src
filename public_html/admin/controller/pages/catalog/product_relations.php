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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
class ControllerPagesCatalogProductRelations extends AController {
    private $error = array();
    public $data = array();

    public function main() {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('catalog/product');

        if (!$this->registry->has('jqgrid_script')) {

            $locale = $this->session->data['language'];
            if (!file_exists(DIR_ROOT . '/' . RDIR_TEMPLATE . 'javascript/jqgrid/js/i18n/grid.locale-' . $locale . '.js')) {
                $locale = 'en';
            }
            $this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/i18n/grid.locale-' . $locale . '.js');
            $this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/jquery.jqGrid.min.js');
            $this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/plugins/jquery.grid.fluid.js');
            $this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/jquery.ba-bbq.min.js');
            $this->document->addScript(RDIR_TEMPLATE . 'javascript/jqgrid/js/grid.history.js');

            //set flag to not include scripts/css twice
            $this->registry->set('jqgrid_script', true);
        }

        if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
            if (!$product_info) {
                $this->session->data['warning'] = $this->language->get('error_product_not_found');
                $this->redirect($this->html->getSecureURL('catalog/product'));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {

            $this->load->library('json');
            $this->request->post['selected'][0] = AJson::decode(html_entity_decode($this->request->post['selected'][0]), true);
            $this->request->post['selected'][1] = AJson::decode(html_entity_decode($this->request->post['selected'][1]), true);
            if ($this->request->post['selected']) {
                $related = array();
                foreach ($this->request->post['selected'][1] as $id => $related_product) {
                    if ($related_product['status']) {
                        $related[] = $id;
                    }
                }
                $product_category = array();
                foreach ($this->request->post['selected'][0] as $id => $category) {
                    if ($category['status']) {
                        $product_category[] = $id;
                    }
                }
                unset($this->request->post['selected']);
                $this->request->post['product_related'] = $related;
                $this->request->post['product_category'] = $product_category;
            }


            $this->model_catalog_product->updateProductLinks($this->request->get['product_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('catalog/product_relations', '&product_id=' . $this->request->get['product_id']));
        }

        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('catalog/product'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get['product_id']),
            'text' => $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product') . ' - ' . $this->data['product_description'][$this->session->data['content_language_id']]['name'],
            'separator' => ' :: '
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $this->request->get['product_id']),
            'text' => $this->language->get('tab_relations'),
            'separator' => ' :: '
        ));


        $this->loadModel('catalog/category');
        $this->data['categories'] = array();
        $results = $this->model_catalog_category->getCategories(0);
        foreach ($results as $r) {
            $this->data['categories'][$r['category_id']] = $r['name'];
        }

        $this->loadModel('setting/store');
        $this->data['stores'] = array(0 => $this->language->get('text_default'));
        $results = $this->model_setting_store->getStores();
        foreach ($results as $r) {
            $this->data['stores'][$r['store_id']] = $r['name'];
        }

        $this->data['product_category'] = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
        $this->data['product_store'] = $this->model_catalog_product->getProductStores($this->request->get['product_id']);
        $this->data['product_related'] = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

        $this->data['active'] = 'relations';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

        $this->data['category_products'] = $this->html->getSecureURL('product/product/category');
        $this->data['related_products'] = $this->html->getSecureURL('product/product/related');
        $this->data['action'] = $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $this->request->get['product_id']);
        $this->data['form_title'] = $this->language->get('text_edit') . '&nbsp;' . $this->language->get('text_product');
        $this->data['update'] = $this->html->getSecureURL('listing_grid/product/update_relations_field', '&id=' . $this->request->get['product_id']);
        $form = new AForm('HS');

        $form->setForm(array(
            'form_name' => 'productFrm',
            'update' => $this->data['update'],
        ));

        $this->data['form']['id'] = 'productFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'productFrm',
            'action' => $this->data['action'],
            'attr' => 'confirm-exit="true"',
        ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
            'style' => 'button1',
        ));
        $this->data['form']['cancel'] = $form->getFieldHtml(array(
            'type' => 'button',
			'href' => $this->html->getSecureURL('catalog/product/update','&product_id='.$this->request->get['product_id']),
            'name' => 'cancel',
            'text' => $this->language->get('button_cancel'),
            'style' => 'button2',
        ));
        $this->data['form']['fields']['product_store'] = $form->getFieldHtml(array(
            'type' => 'checkboxgroup',
            'name' => 'product_store[]',
            'value' => $this->data['product_store'],
            'options' => $this->data['stores'],
            'scrollbox' => true,
        ));

        $this->load->library('json');

        $listing_data = array();
        if ($this->data['product_related']) {

            foreach ($this->data['product_related'] as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);
                $listing_data[$product_id] = array('id' => $product_id,
                    'name' => html_entity_decode($product_info['name'] . ' (' . $product_info['model'] . ')'),
					'status' => 1);

            }
        }
        // exclude this product from multivalue list. why we need relate recursion?
        $this->session->data['multivalue_excludes']['product_id'] = $this->request->get['product_id'];
        $this->data['form']['fields']['list'] = $form->getFieldHtml(array(
            'id' => 'category_products_list',
            'type' => 'multivaluelist',
            'name' => 'category_products',
            'content_url' => $this->html->getSecureUrl('product/product/related'),
            'edit_url' => '',
            'multivalue_hidden_id' => 'popup',
            'values' => $listing_data,
            'return_to' => 'productFrm_popup_item_count',
            'text' => array(
                'delete' => $this->language->get('button_delete'),
                'delete_confirm' => $this->language->get('text_delete_confirm'),
            )
        ));


        $this->data['form']['fields']['list_hidden'] = $form->getFieldHtml(
            array('id' => 'popup',
                'type' => 'multivalue',
                'name' => 'popup',
                'title' => $this->language->get('text_select_from_list'),
                'selected' => ($listing_data ? AJson::encode($listing_data) : "{}"),
                'content_url' => $this->html->getSecureUrl('catalog/product_listing', '&form_name=productFrm&multivalue_hidden_id=popup'),
                'postvars' => '',
                'return_to' => '', // placeholder's id of listing items count.
                'popup_height' => 708,
                'js' => array(
                    'apply' => "productFrm_category_products_buildList();",
                    'cancel' => 'productFrm_category_products_buildList();',
                ),
                'text' => array(
                    'selected' => $this->language->get('text_count_selected'),
                    'edit' => $this->language->get('text_save_edit'),
                    'apply' => $this->language->get('text_apply'),
                    'save' => $this->language->get('button_save'),
                    'reset' => $this->language->get('button_reset')),
            ));


// CATEGORY
        $listing_data = array();
        if ($this->data['product_category']) {
            foreach ($this->data['product_category'] as $category_id) {
                $listing_data[$category_id] = array('id' => $category_id,
                    'name' => html_entity_decode($this->model_catalog_category->getPath($category_id)),
                    'status' => 1);
            }
        }

        $this->data['form']['fields']['category_list'] = $form->getFieldHtml(array(
            'id' => 'product_categories_list',
            'type' => 'multivaluelist',
            'name' => 'product_categories',
            'content_url' => $this->html->getSecureUrl('product/product/product_categories'),
            'edit_url' => '',
            'multivalue_hidden_id' => 'cat_popup',
            'values' => $listing_data,
            'return_to' => '',
            'text' => array(
                'delete' => $this->language->get('button_delete'),
                'delete_confirm' => $this->language->get('text_delete_confirm'),
            )
        ));


        $this->data['form']['fields']['category_list_hidden'] = $form->getFieldHtml(
            array('id' => 'cat_popup',
                'type' => 'multivalue',
                'name' => 'cat_popup',
                'title' => $this->language->get('text_select_from_list'),
                'selected' => ($listing_data ? AJson::encode($listing_data) : "{}"),
                'content_url' => $this->html->getSecureUrl('catalog/category_listing',
                    '&form_name=productFrm&multivalue_hidden_id=cat_popup'),
                'postvars' => '',
                'return_to' => '', // placeholder's id of listing items count.
                'js' => array(
                    'apply' => "productFrm_product_categories_buildList();",
                    'cancel' => 'productFrm_product_categories_buildList();',
                ),
                'text' => array(
                    'selected' => 'Count of selected items: ',
                    'edit' => $this->language->get('text_save_edit'),
                    'apply' => $this->language->get('text_apply'),
                    'save' => $this->language->get('button_save'),
                    'reset' => $this->language->get('button_reset')),
            ));

//end

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');
        $this->view->assign('help_url', $this->gen_help_url('product_relations'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_relations.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}