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

class ControllerResponsesCommonDoEmbed extends AController
{
    public function main()
    {
    }

    public function product()
    {
        if (!has_value($this->request->get['product_id'])) {
            return null;
        }
        $this->data['product_id'] = $this->request->get['product_id'];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->common('products');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->loadlanguage('common/do_embed');
        $this->view->batchAssign($this->language->getASet('common/do_embed'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/embed/do_embed_product_modal.tpl');
    }

    public function categories()
    {
        //this var can be an array
        $this->data['category_id'] = $this->request->get['category_id'];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->common('categories');
        $this->processTemplate('responses/embed/do_embed_category_modal.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function manufacturers()
    {
        $this->data['manufacturer_id'] = $this->request->get['manufacturer_id'];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->common('manufacturers');
        $this->processTemplate('responses/embed/do_embed_manufacturer_modal.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function collections()
    {
        $this->data['collection_id'] = $this->request->get['collection_id'];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->common('collections');
        $this->processTemplate('responses/embed/do_embed_collection_modal.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function common($type)
    {
        $this->loadModel('catalog/product');
        $this->loadModel('setting/store');
        $form = new AForm('ST');
        $form->setForm(
            [
                'form_name' => 'getEmbedFrm',
            ]
        );
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type' => 'form',
                'name' => 'getEmbedFrm',
                'attr' => 'class="aform form-horizontal"',
            ]
        );

        $this->data['fields']['image'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'image',
                'value' => 1,
                'style' => 'btn_switch btn-group-xs',
            ]
        );
        $this->data['fields']['name'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'name',
                'value' => 1,
                'style' => 'btn_switch btn-group-xs',
            ]
        );

        if ($type == 'products') {
            $this->data['fields']['blurb'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'blurb',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
            $this->data['fields']['price'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'price',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
            $this->data['fields']['rating'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'rating',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
            $this->data['fields']['quantity'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'quantity',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
            $this->data['fields']['addtocart'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'addtocart',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
        } elseif ($type == 'categories') {
            $this->loadModel('catalog/category');
            $this->data['fields']['products_count'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'products_count',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
            $category_id = (array) $this->data['category_id'];
            $subcategories = [];
            //if embed for only one category
            if (sizeof($category_id) == 1) {
                $cat_id = current($category_id);
                $category_info = $this->model_catalog_category->getCategory($cat_id);
                $subcategories = $this->model_catalog_category->getCategories($cat_id);
                if ($category_info['parent_id'] == 0) {
                    $options = $this->model_catalog_category->getCategories(ROOT_CATEGORY_ID);
                } else {
                    $cat_desc = $this->model_catalog_category->getCategoryDescriptions($cat_id);
                    $options = [
                        0 =>
                            [
                                'category_id' => $cat_id,
                                'name'        => $cat_desc[$this->language->getContentLanguageID()]['name'],
                            ],
                    ];
                }
            } else {
                if (!sizeof($category_id)) {
                    $options = $this->model_catalog_category->getCategoriesData(['parent_id' => 0]);
                    $category_id = [];
                    foreach ($options as $c) {
                        $category_id[] = $c['category_id'];
                    }
                } else {
                    foreach ($category_id as &$c) {
                        $c = (int) $c;
                    }
                    unset($c);
                    $subsql = ' c.category_id IN ('.implode(',', $category_id).') ';
                    $options = $this->model_catalog_category->getCategoriesData(['subsql_filter' => $subsql]);
                }
            }

            if ($subcategories) {
                $options = array_merge($options, $subcategories);
            }
            $opt = [];
            foreach ($options as $cat) {
                $opt[$cat['category_id']] = $cat['name'];
            }

            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'      => 'checkboxgroup',
                    'name'      => 'category_id[]',
                    'value'     => $category_id,
                    'options'   => $opt,
                    'scrollbox' => true,
                    'style'     => 'medium-field',
                ]
            );
        } elseif ($type == 'manufacturers') {
            $this->loadModel('catalog/manufacturer');
            $manufacturer_id = (array) $this->data['manufacturer_id'];
            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'products_count',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );

            if (!sizeof($manufacturer_id)) {
                return null;
            } else {
                foreach ($manufacturer_id as &$c) {
                    $c = (int) $c;
                }
                unset($c);
                $subsql = ' m.manufacturer_id IN ('.implode(',', $manufacturer_id).') ';
                $options = $this->model_catalog_manufacturer->getManufacturers(['subsql_filter' => $subsql]);
            }
            reset($manufacturer_id);

            $opt = [];
            foreach ($options as $m) {
                $opt[$m['manufacturer_id']] = $m['name'];
            }
            if (sizeof($manufacturer_id) > 1) {
                $this->data['fields'][] = $form->getFieldHtml(
                    [
                        'type'      => 'checkboxgroup',
                        'name'      => 'manufacturer_id[]',
                        'value'     => $manufacturer_id,
                        'options'   => $opt,
                        'scrollbox' => true,
                        'style'     => 'medium-field',
                    ]
                );
            } else {
                $this->data['fields'][] = $form->getFieldHtml(
                    [
                        'type'  => 'hidden',
                        'name'  => 'manufacturer_id[]',
                        'value' => current($manufacturer_id),
                    ]
                );
            }
        } elseif ($type == 'collections') {
            $this->loadLanguage('catalog/collections');
            $this->loadModel('catalog/collection');
            $collection_id = (array) $this->data['collection_id'];
            $this->data['fields']['price'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'price',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );

            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'  => 'input',
                    'name'  => 'limit',
                    'value' => 20,
                    'style' => 'col-sm-2',
                ]
            );
            //if embed for only one category
            if (count($collection_id) == 1) {
                $collection_id = current($collection_id);
            }

            $options = $this->model_catalog_collection->getCollections(
                [
                    'status_id' => 1,
                    'store_id'  => (int) $this->session->data['current_store_id']
                ]
            );

            $opt = $options['items'] ? array_column($options['items'],'name','id') : [];

            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'collection_id',
                    'value'   => $collection_id,
                    'options' => $opt,
                    'style'   => 'medium-field',
                ]
            );
            $this->data['entry_collection_id'] = $this->language->get('entry_collection_id');
        }

        $results = $this->language->getAvailableLanguages();
        $language_codes = [];
        foreach ($results as $v) {
            $lng_code = $this->language->getLanguageCodeByLocale($v['locale']);
            $language_codes[$lng_code] = $v['name'];
        }
        $this->data['fields'][] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'language',
                'value'   => $this->config->get('config_storefront_language'),
                'options' => $language_codes,
            ]
        );

        $this->load->model('localisation/currency');
        $results = $this->model_localisation_currency->getCurrencies();
        $currencies = [];
        foreach ($results as $v) {
            $currencies[$v['code']] = $v['title'];
        }
        $this->data['fields'][] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'currency',
                'value'   => $this->config->get('config_currency'),
                'options' => $currencies,
            ]
        );

        $this->data['url'] = $form->getFieldHtml(
            [
                'type' => 'input',
                'name' => 'url',
                'attr' => 'readonly',
            ]
        );
        $this->data['text_area'] = $form->getFieldHtml(
            [
                'type'  => 'textarea',
                'name'  => 'code_area',
                'attr'  => 'rows="10" readonly',
                'style' => 'ml_field',
            ]
        );

        $this->data['store_id'] = $store_id = $this->session->data['current_store_id'];

        $current_store_settings = $this->model_setting_store->getStore($store_id);
        $remote_store_url = $current_store_settings['config_ssl_url'] ? : $current_store_settings['config_url'];

        $this->data['sf_js_embed_url'] = $remote_store_url.INDEX_FILE.'?rt=r/embed/js';
        $this->data['direct_embed_url'] = $remote_store_url.INDEX_FILE.'?rt=r/embed/get';
            $this->data['sf_base_url'] = $remote_store_url;
        $this->data['help_url'] = $this->gen_help_url('embed');

        $template_name = $this->config->get('config_storefront_template');
        $this->data['sf_css_embed_url'] = $remote_store_url.'storefront/view/default/stylesheet/embed.css';

        //override css url for extension templates
        if ($template_name != 'default') {
            $css_file = DIR_ROOT
                .'/extensions/'
                .$template_name
                .'/storefront/view/'
                .$template_name
                .'/stylesheet/embed.css';

            if (is_file($css_file)) {
                $this->data['sf_css_embed_url'] =
                    $remote_store_url
                    .'extensions/'
                    .$template_name
                    .'/storefront/view/'
                    .$template_name
                    .'/stylesheet/embed.css';
            }
        }

        $this->loadlanguage('common/do_embed');
        $this->view->batchAssign($this->language->getASet('common/do_embed'));
        $this->view->batchAssign($this->data);
    }

    protected function _prepare_url($url)
    {
        return str_replace(['http://', 'https://'], '//', $url);
    }
}
