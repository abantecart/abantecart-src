<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ControllerPagesCatalogProductOptions extends AController
{
    public $error = [];
    protected $attribute_manager;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('catalog/product');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('catalog/product');
        $this->attribute_manager = new AAttribute_Manager();
        $product_id = $this->request->get['product_id'] ?? 0;
        if ($this->request->is_POST() && $this->_validateForm()) {
            $product_option_id = $this->model_catalog_product->addProductOption(
                $product_id,
                $this->request->post
            );
            $this->session->data['success'] = $this->language->get('text_success');
            redirect(
                $this->html->getSecureURL(
                    'catalog/product_options',
                    '&product_id='.$product_id.'&product_option_id='.$product_option_id
                )
            );
        }

        $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
        if (!$product_info) {
            $this->session->data['warning'] = $this->language->get('error_product_not_found');
            redirect($this->html->getSecureURL('catalog/product'));
        }

        $this->data['attributes'] = [
            'new' => $this->language->get('text_add_new_option'),
        ];
        $results = $this->attribute_manager->getAttributes(
            [
                'search' => " ga.attribute_type_id = '".$this->attribute_manager->getAttributeTypeID('product_option')."'
                            AND ga.status = 1
                            AND ga.attribute_parent_id = 0 ",
                'sort'   => 'sort_order',
                'order'  => 'ASC',
                // !we can not have unlimited, so set 1000 for now
                'limit'  => 1000
            ],
            $this->session->data['content_language_id']
        );
        foreach ($results as $type) {
            $this->data['attributes'][$type['attribute_id']] = $type['name'];
        }

        $this->data['product_description'] = $this->model_catalog_product->getProductDescriptions($product_id);
        $product_options = $this->model_catalog_product->getProductOptions($product_id);

        $content_language_id = $this->language->getContentLanguageID();
        $default_language_id = $this->language->getDefaultLanguageID();
        foreach ($product_options as &$option) {
            $option_name = trim($option['language'][$content_language_id]['name']);
            $option['language'][$content_language_id]['name'] = $option_name ? : 'n/a';
            $option_name = trim($option['language'][$default_language_id]['name']);
            $option['language'][$default_language_id]['name'] = $option_name ? : 'n/a';
        }
        unset($option);

        $this->data['product_options'] = $product_options;
        $this->data['language_id'] = $this->session->data['content_language_id'];
        $this->data['url']['load_option'] = $this->html->getSecureURL(
            'product/product/load_option',
            '&product_id='.$product_id
        );
        $this->data['url']['update_option'] = $this->html->getSecureURL(
            'product/product/update_option',
            '&product_id='.$product_id
        );
        $this->data['url']['get_options_list'] = $this->html->getSecureURL(
            'product/product/get_options_list',
            '&product_id='.$product_id
        );

        $this->view->assign('error', $this->error);
        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(
            [
                'href' => $this->html->getSecureURL('index/home'),
                'text' => $this->language->get('text_home'),
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href' => $this->html->getSecureURL('catalog/product'),
                'text' => $this->language->get('heading_title'),
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href' => $this->html->getSecureURL(
                    'catalog/product/update',
                    '&product_id='.$product_id
                ),
                'text' => $this->language->get('text_edit')
                    .'&nbsp;'
                    .$this->language->get('text_product')
                    .' - '
                    .$this->data['product_description'][$content_language_id]['name'],
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL(
                    'catalog/product_options',
                    '&product_id='.$this->request->get['product_id']
                ),
                'text'    => $this->language->get('tab_option'),
                'current' => true,
            ]
        );

        $this->data['active'] = 'options';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/product_tabs', [$this->data]);
        $this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $results = HtmlElementFactory::getAvailableElements();
        $element_types = ['' => $this->language->get('text_select')];
        foreach ($results as $key => $type) {
            // allowed field types
            if (in_array($key, AAttribute_Manager::$allowedProductOptionFieldTypes)) {
                $element_types[$key] = $type['type'];
            }
        }

        $this->data['button_add_option'] = $this->html->buildButton(
            [
                'text'  => $this->language->get('button_add_option'),
                'style' => 'button1',
            ]
        );
        $this->data['button_add_option_value'] = $this->html->buildButton(
            [
                'text'  => $this->language->get('button_add_option_value'),
                'style' => 'button1',
            ]
        );
        $this->data['button_remove'] = $this->html->buildButton(
            [
                'text'  => $this->language->get('button_remove'),
                'style' => 'button1',
            ]
        );
        $this->data['button_reset'] = $this->html->buildButton(
            [
                'text'  => $this->language->get('button_reset'),
                'style' => 'button2',
            ]
        );

        $this->data['action'] = $this->html->getSecureURL(
            'catalog/product_options',
            '&product_id='.$this->request->get['product_id']
        );
        $this->data['form_title'] = $this->language->get('text_edit').'&nbsp;'.$this->language->get('text_product');
        $this->data['update'] = '';
        $form = new AForm('HT');

        $product_opt = [];
        foreach ($product_options as $option) {
            $product_opt[$option['product_option_id']] = $option['language'][$content_language_id]['name'];
        }
        $product_option_id = $this->request->get['product_option_id'] ? : $this->data['product_option_id'];
        $this->data['options'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'option',
                'value'   => $product_option_id,
                'options' => $product_opt,
            ]
        );

        $form->setForm(
            [
                'form_name' => 'product_form',
                'update'    => $this->data['update'],
            ]
        );

        $this->data['form']['id'] = 'product_form';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'product_form',
                'action' => $this->data['action'],
                'attr'   => 'data-confirm-exit="true"  class="form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_add'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        $form->setForm(
            [
                'form_name' => 'new_option_form',
                'update'    => '',
            ]
        );
        $this->data['attributes'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'attribute_id',
                'options' => $this->data['attributes'],
                'style'   => 'chosen',
            ]
        );
        $this->data['option_name'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'option_name',
                'required' => true,
            ]
        );
        $this->data['status'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'status',
                'value' => 1,
                'style' => 'btn_switch',
            ]
        );
        $this->data['sort_order'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'sort_order',
                'style' => 'small-field',
            ]
        );
        $this->data['required'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'required',
                'style' => 'btn_switch',
            ]
        );
        $this->data['element_type'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'element_type',
                'required' => true,
                'options'  => $element_types,
            ]
        );

        $this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');
        $object_title = $this->language->get('text_product').' '.$this->language->get('text_option_value');
        $params = '&object_name=product_option_value&object_title='.urlencode($object_title);
        $this->data['rl_resource_library'] = $this->html->getSecureURL('common/resource_library', $params);
        $this->data['rl_resources'] = $this->html->getSecureURL('common/resource_library/resources', $params);

        $this->data['rl_resource_single'] = $this->html->getSecureURL(
            'common/resource_library/get_resource_details',
            $params
        );

        $this->data['rl_delete'] = $this->html->getSecureURL('common/resource_library/delete');
        $this->data['rl_unmap'] = $this->html->getSecureURL('common/resource_library/unmap', $params);
        $this->data['rl_map'] = $this->html->getSecureURL('common/resource_library/map', $params);
        $this->data['rl_download'] = $this->html->getSecureURL('common/resource_library/get_resource_preview');
        $this->data['rl_upload'] = $this->html->getSecureURL('common/resource_library/upload', $params);
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            [
                'object_name' => 'product_option_value',
                'object_id'   => '',
                'types'       => ['image'],
                //sign loading thumbs on page load. disable it for hidden attribute values info
                'onload'      => false,
            ]
        );
        if ($this->config->get('config_embed_status')) {
            $this->data['embed_url'] = $this->html->getSecureURL(
                'common/do_embed/product',
                '&product_id='.$product_id
            );
        }
        $this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
        $this->view->assign('help_url', $this->gen_help_url('product_options'));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/product_options.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validateForm()
    {
        if (!$this->user->canModify('catalog/product_options')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if ($this->model_catalog_product->isProductGroupOption(
            $this->request->get['product_id'],
            $this->request->post['attribute_id']
        )
        ) {
            $this->error['warning'] = $this->language->get('error_option_in_group');
        }
        $this->extensions->hk_ValidateData($this);
        return (!$this->error);
    }
}