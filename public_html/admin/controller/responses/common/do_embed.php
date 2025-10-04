<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
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
        $this->data['product_id'] = (int)$this->request->get['product_id'];
        if (!$this->data['product_id']) {
            return;
        }
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
        $this->data['category_id'] = (array)$this->request->get['category_id'];
        if (!$this->data['category_id']) {
            return;
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->common('categories');
        $this->processTemplate('responses/embed/do_embed_category_modal.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function manufacturers()
    {
        $this->data['manufacturer_id'] = (array)$this->request->get['manufacturer_id'];
        if (!$this->data['manufacturer_id']) {
            return;
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->common('manufacturers');
        $this->processTemplate('responses/embed/do_embed_manufacturer_modal.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function collections()
    {
        $this->data['collection_id'] = (int)$this->request->get['collection_id'];
        if (!$this->data['collection_id']) {
            return;
        }
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
        /** @var ModelSettingStore $stStoreMdl */
        $stStoreMdl = $this->loadModel('setting/store');

        $storeId = (int)($this->request->get['store_id'] ?? $this->session->data['current_store_id']);
        $this->data['store_id'] = $storeId;

        $currentStoreSettings = $stStoreMdl->getStore($storeId);
        $remoteStoreUrl = $currentStoreSettings['config_ssl_url'] ?: $currentStoreSettings['config_url'];


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
            /** @var ModelCatalogCategory $catMdl */
            $catMdl = $this->loadModel('catalog/category');
            $this->data['fields']['products_count'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'products_count',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );
            $categoryIdList = filterIntegerIdList($this->data['category_id']);
            $subcategories = [];
            //if embed for only one category
            if (sizeof($categoryIdList) == 1) {
                $catId = (int)current($categoryIdList);
                $categoryInfo = $catMdl->getCategory($catId);
                $subcategories = $catMdl->getCategories($catId);
                if ($categoryInfo['parent_id'] == 0) {
                    $options = $catMdl->getCategories(ROOT_CATEGORY_ID);
                } else {
                    $cat_desc = $catMdl->getCategoryDescriptions($catId);
                    $options = [
                        0 =>
                            [
                                'category_id' => $catId,
                                'name'        => $cat_desc[$this->language->getContentLanguageID()]['name'],
                            ],
                    ];
                }
            } else {
                if (!sizeof($categoryIdList)) {
                    $options = $catMdl->getCategoriesData(['parent_id' => 0]);
                    $categoryIdList = array_column($options, 'category_id');
                } else {
                    $subsql = ' c.category_id IN (' . implode(',', $categoryIdList) . ') ';
                    $options = $this->model_catalog_category->getCategoriesData(['subsql_filter' => $subsql]);
                }
            }

            if ($subcategories) {
                $options = array_merge($options, $subcategories);
            }
            $opt = array_column($options, 'name', 'category_id');
            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'      => 'checkboxgroup',
                    'name'      => 'category_id[]',
                    'value'     => $categoryIdList,
                    'options'   => $opt,
                    'scrollbox' => true,
                    'style'     => 'medium-field',
                ]
            );
        } elseif ($type == 'manufacturers') {
            $this->loadModel('catalog/manufacturer');
            $manufacturerIdList = filterIntegerIdList((array)$this->data['manufacturer_id']);
            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'products_count',
                    'value' => 1,
                    'style' => 'btn_switch btn-group-xs',
                ]
            );

            if (!sizeof($manufacturerIdList)) {
                return null;
            } else {
                $subsql = ' m.manufacturer_id IN (' . implode(',', $manufacturerIdList) . ') ';
                $options = $this->model_catalog_manufacturer->getManufacturers(['subsql_filter' => $subsql]);
            }

            $opt = array_column((array)$options, 'name', 'manufacturer_id');

            if (sizeof($manufacturerIdList) > 1) {
                $this->data['fields'][] = $form->getFieldHtml(
                    [
                        'type'      => 'checkboxgroup',
                        'name'      => 'manufacturer_id[]',
                        'value'     => $manufacturerIdList,
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
                        'value' => current($manufacturerIdList),
                    ]
                );
            }
        } elseif ($type == 'collections') {
            $this->loadLanguage('catalog/collections');
            $this->loadModel('catalog/collection');
            $collectionIdList = filterIntegerIdList((array)$this->data['collection_id']);
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
            if (count($collectionIdList) == 1) {
                $collectionIdList = current($collectionIdList);
            }

            $options = $this->model_catalog_collection->getCollections(
                [
                    'status_id' => 1,
                    'store_id'  => (int)($this->request->get['store_id'] ?? $this->session->data['current_store_id'])
                ]
            );

            $opt = $options['items'] ? array_column($options['items'], 'name', 'id') : [];

            $this->data['fields'][] = $form->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'collection_id',
                    'value'   => $collectionIdList,
                    'options' => $opt,
                    'style'   => 'medium-field',
                ]
            );
            $this->data['entry_collection_id'] = $this->language->get('entry_collection_id');
        }

        $results = $this->language->getAvailableLanguages();
        $language_codes = [];
        foreach ($results as $v) {
            $lng_code = $this->language->getLanguageCodeByLocale((string)$v['locale']);
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
        $currencies = array_column($results, 'title', 'code');
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


        $this->data['sf_js_embed_url'] = $remoteStoreUrl . INDEX_FILE . '?rt=r/embed/js';
        $this->data['direct_embed_url'] = $remoteStoreUrl . INDEX_FILE . '?rt=r/embed/get';
        $this->data['sf_base_url'] = $remoteStoreUrl;
        $this->data['help_url'] = $this->gen_help_url('embed');
        $template_name = $currentStoreSettings['config_storefront_template'];
        //look into extensions
        foreach (['stylesheet', 'css'] as $cssDir) {
            $cssRelPath = 'extensions' . DS
                . $template_name . DS
                . 'storefront' . DS
                . 'view' . DS
                . $template_name . DS
                . $cssDir . DS
                . 'embed.css';
            $css_file = DIR_ROOT . DS . $cssRelPath;
            if (is_file($css_file)) {
                $this->data['sf_css_embed_url'] = $remoteStoreUrl . $cssRelPath;
                break;
            }
        }
        //look into the core
        if (!$this->data['sf_css_embed_url']) {
            foreach (['stylesheet', 'css'] as $cssDir) {
                $cssRelPath = 'storefront' . DS . 'view' . DS . $template_name . DS . $cssDir . DS . 'embed.css';
                $css_file = DIR_ROOT . DS . $cssRelPath;
                if (is_file($css_file)) {
                    $this->data['sf_css_embed_url'] = $remoteStoreUrl . $cssRelPath;
                    break;
                }
            }
        }
        //look into default
        if (!$this->data['sf_css_embed_url']) {
            foreach (['stylesheet', 'css'] as $cssDir) {
                $cssRelPath = 'storefront' . DS . 'view' . DS . 'default' . DS . $cssDir . DS . 'embed.css';
                $css_file = DIR_ROOT . DS . $cssRelPath;
                if (is_file($css_file)) {
                    $this->data['sf_css_embed_url'] = $remoteStoreUrl . $cssRelPath;
                    break;
                }
            }
        }
        $this->loadlanguage('common/do_embed');
        $this->view->batchAssign($this->language->getASet('common/do_embed'));
        $this->view->batchAssign($this->data);
    }

    /** @noinspection HttpUrlsUsage */
    protected function _prepare_url(string $url)
    {
        return str_replace(['http://', 'https://'], '//', $url);
    }
}
