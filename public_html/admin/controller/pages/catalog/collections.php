<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

/**
 * Class ControllerPagesCatalogCategory
 */
class ControllerPagesCatalogCollections extends AController
{
    public $error = array();
    public $data = array();

    private $isGrid = false;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('catalog/collections');

        $this->isGrid = true;

        $this->buildHeader();

        $grid_settings = [
            'table_id'       => 'collections_grid',
            'url'            => $this->html->getSecureURL('listing_grid/collections'),
            'editurl'        => $this->html->getSecureURL('listing_grid/collections/update'),
            'update_field'   => $this->html->getSecureURL('listing_grid/collections/update_field'),
            'sortname'       => 'text_id',
            'sortorder'      => 'asc',
            'columns_search' => true,
            'actions'        => array(
                'edit'   => array(
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('catalog/collections/update', '&id=%ID%'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
            ),
        ];

        $grid_settings['colNames'] = array(
            $this->language->get('collection_column_name'),
            $this->language->get('collection_column_status'),
            $this->language->get('collection_column_date_added'),
        );

        $grid_settings['colModel'] = array(
            [
                'name'  => 'name',
                'index' => 'name',
                'width' => 200,
                'align' => 'left',
            ],
            [
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 50,
                'align'  => 'center',
                'search' => false,
            ],
            [
                'name'  => 'date_added',
                'index' => 'date_added',
                'width' => 50,
                'align' => 'left',
            ],
        );

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

        $this->view->assign('insert', $this->html->getSecureURL('catalog/collections/insert'));
        $this->view->assign('help_url', $this->gen_help_url('data_collections'));

        $this->processTemplate('pages/catalog/collections_list.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function buildHeader()
    {
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->document->initBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ));
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('catalog/collections'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ));

        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->buildHeader();

        $this->loadModel('catalog/collection');

        if ($this->request->is_POST() && $this->validate($this->request->post)) {
            $data = $this->request->post;
            $data['store_id'] = (int)$this->config->get('config_store_id');
            $collection = $this->model_catalog_collection->insert($data);
        }

        if ($this->session->data['success']) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if ($this->session->data['warning']) {
            $this->error['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!empty($this->error)) {
            $this->view->assign('error', $this->error);
        }

        if ($collection) {
            redirect($this->html->getSecureURL('catalog/collections/update', '&id='.$collection['id']));
        }

        $this->data['form_title'] = $this->language->get('collection_new');
        $this->data['conditions_title'] = $this->language->get('conditions_title');

        $this->getForm();

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('data_collections'));
        $this->view->assign('list_url', $this->html->getSecureURL('catalog/collections'));
        $this->processTemplate('pages/catalog/collection_form.tpl');

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->buildHeader();

        $this->loadModel('catalog/collection');
        $this->loadModel('localisation/language');

        if ($this->request->is_POST()) {
            $collection = $this->model_catalog_collection->getById((int)$this->request->get['id']);

            if ($collection && $this->validate($this->request->post)) {
                try {
                    $this->model_catalog_collection->update((int)$this->request->get['id'], $this->request->post);
                    $this->session->data['success'] = $this->language->get('save_complete');
                    $this->extensions->hk_ProcessData($this, 'update');
                    redirect($this->html->getSecureURL('catalog/collections/update', '&id='.(int)$this->request->get['id']));
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage());
                    $this->session->data['warning'] = $this->language->get('save_error');
                }
            }

            if (!$collection || !$this->validate($this->request->post)) {
                $this->session->data['warning'] = $this->language->get('save_error');
            }
        }

        if (!(int)$this->request->get['id']) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }

        if ($this->session->data['success']) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if ($this->session->data['warning']) {
            $this->error['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!empty($this->error)) {
            $this->view->assign('error', $this->error);
        }

        $this->data['form_title'] = $this->language->get('collection_update');
        $this->data['conditions_title'] = $this->language->get('conditions_title');

        $this->getForm();

        if ($this->config->get('config_embed_status')) {
            $this->view->assign('embed_url', $this->html->getSecureURL('common/do_embed/collections', '&id='.(int)$this->request->get['id']));
        }

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('data_collections'));
        $this->view->assign('list_url', $this->html->getSecureURL('catalog/collections'));
        $this->processTemplate('pages/catalog/collection_form.tpl');
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm($args = [])
    {
        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('error_name', $this->error['name']);

        $this->view->assign('cancel', $this->html->getSecureURL('catalog/collections'));

        if ((int)$this->request->get['id']) {

            $collection = $this->model_catalog_collection->getById((int)$this->request->get['id']);
            if ($collection) {
                foreach ($collection as $key => $value) {
                    $this->data[$key] = $value;
                }
                $products = $this->model_catalog_collection->getProducts($collection['conditions'], 'date_modified', 'DESC', '0', 1, (int)$this->request->get['id']);
                $this->data['products_count'] = $products['total'];

                $this->data['form']['show_on_storefront'] = new stdClass();

                $storeHome = $this->config->get('config_ssl_url') ?: $this->config->get('config_url');
                if ($this->config->get('config_ssl') && !empty($this->config->get('config_ssl_url'))) {
                    $storeHome = $this->config->get('config_ssl_url');
                }

                if (substr($storeHome, -1) == '/') {
                    $storeHome = substr($storeHome, 0, -1);
                }

                $this->data['form']['show_on_storefront']->href = $storeHome.'/?rt=product/collection&collection_id='.(int)$this->request->get['id'];
                if ($this->data['keyword'] && (int)$this->config->get('enable_seo_url')) {
                    $this->data['form']['show_on_storefront']->href = $storeHome.'/'.$this->data['keyword'];
                }

                $this->data['form']['show_on_storefront']->text = $this->language->get('text_storefront');
            }
        }

        if ($this->request->get['products']) {
            $productIds = $this->request->get['products'];
            if (is_array($productIds) && !empty($productIds)) {
                $this->data['conditions']['conditions'][] = [
                    'object'   => 'products',
                    'value'    => $productIds,
                    'operator' => 'in',
                ];
            }
        }

        if ($this->request->is_POST()) {
            foreach ($this->request->post as $key => $value) {
                $this->data[$key] = $value;
            }
        }

        $form = new AForm ('ST');
        if ($collection) {
            $this->data['action'] = $this->html->getSecureURL('catalog/collections/update', '&id='.$collection['id']);
            $this->data['update'] = $this->html->getSecureURL('listing_grid/collections/update_field', '&id='.$collection['id']);
            $form = new AForm ('HS');
        }
        $form->setForm([
            'form_name' => 'collectionsFrm',
            'update'    => $this->data['update'],
        ]);

        $this->data['form']['id'] = 'collectionsFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'collectionsFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]);

        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]);

        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]);

        $this->data['form']['fields']['general']['status'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'status',
                'value' => isset($this->data['status']) ? $this->data['status'] : 1,
                'style' => 'btn_switch',
            ]);

        $this->data['form']['fields']['general']['name'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'name',
                'value'    => $this->data['name'],
                'required' => true,
            ]);

        $this->data['form']['fields']['general']['description'] = $form->getFieldHtml(
            [
                'type'  => 'textarea',
                'name'  => 'description',
                'value' => $this->data['description'],
            ]);

        $content_language_id = $this->language->getContentLanguageID();
        $this->data['form']['fields']['general']['title'] = $form->getFieldHtml(
            array(
                'type'         => 'input',
                'name'         => 'title',
                'value'        => $this->data['title'],
                'multilingual' => true,
            ));
        $this->data['form']['fields']['general']['meta_keywords'] = $form->getFieldHtml(
            array(
                'type'         => 'textarea',
                'name'         => 'meta_keywords',
                'value'        => $this->data['meta_keywords'],
                'style'        => 'xl-field',
                'multilingual' => true,
            ));
        $this->data['form']['fields']['general']['meta_description'] = $form->getFieldHtml(
            array(
                'type'         => 'textarea',
                'name'         => 'meta_description',
                'value'        => $this->data['meta_description'],
                'style'        => 'xl-field',
                'multilingual' => true,
            ));

        $this->data['keyword_button'] = $form->getFieldHtml(array(
            'type'  => 'button',
            'name'  => 'generate_seo_keyword',
            'text'  => $this->language->get('button_generate'),
            //set button not to submit a form
            'attr'  => 'type="button"',
            'style' => 'btn btn-info',
        ));
        $this->data['generate_seo_url'] = $this->html->getSecureURL('common/common/getseokeyword', '&object_key_name=collection_id&id='.($this->request->get['id'] ?: '0'));

        $this->data['form']['fields']['general']['keyword'] = $form->getFieldHtml(array(
            'type'         => 'input',
            'name'         => 'keyword',
            'value'        => $this->data['keyword'],
            'help_url'     => $this->gen_help_url('seo_keyword'),
            'multilingual' => true,
            'attr'         => ' gen-value="'.SEOEncode($this->data['name']).'" ',
        ));

        if ((int)$this->request->get['id']) {
            $this->data['form']['fields']['general']['products_count'] = $form->getFieldHtml(array(
                'type'  => 'input',
                'name'  => 'products_count',
                'value' => $this->data['products_count'],
                'attr'  => ' disabled',
            ));
        }

        // relations between conditions
        $this->data['conditions_relation']['fields']['if'] = array(
            'text'  => $this->language->get('text_if_1'),
            'field' => $form->getFieldHtml(
                array(
                    'type'    => 'selectbox',
                    'name'    => 'conditions[relation][if]',
                    'options' => array(
                        'all' => $this->language->get('text_all'),
                        'any' => $this->language->get('text_any'),
                    ),
                    'value'   => (isset($this->data['conditions']['relation']['if']) ? $this->data['conditions']['relation']['if'] : ''),
                )
            ),
        );

        $this->data['conditions_relation']['fields']['value'] = array(
            'text'  => $this->language->get('text_if_2'),
            'field' => $form->getFieldHtml(
                array(
                    'type'    => 'selectbox',
                    'name'    => 'conditions[relation][value]',
                    'options' => array(
                        'true'  => $this->language->get('text_true'),
                        'false' => $this->language->get('text_false'),
                    ),
                    'value'   => (isset($this->data['conditions']['relation']['value']) ? $this->data['conditions']['relation']['value'] : ''),
                )
            ),
        );

        // 	conditions
        if (isset($this->data['conditions']['conditions'])) {
            $i = 0;
            $this->load->library('json');

            foreach ($this->data['conditions']['conditions'] as $rule) {
                $this->request->post['idx'] = $i;
                $this->request->post['condition_object'] = $rule['object'];
                $args = array(
                    array(
                        'operator' => $rule['operator'],
                        'value'    => $rule['value'],
                    ),
                );
                $fields = $this->dispatch('responses/listing_grid/collections/getFieldsByConditionObject', $args);

                $fields = AJson::decode($fields->dispatchGetOutput(), true);
                $this->data['form']['fields']['conditions'][$i]['id'] = $rule['object'];
                $this->data['form']['fields']['conditions'][$i]['text'] = $fields['text'];
                $this->data['form']['fields']['conditions'][$i]['field'] = $fields['fields'];
                $i++;
            }
        }

        $cond_objects = [
            'product_price',
            'categories',
            'brands',
            'products',
            'tags',
        ];

        foreach ($cond_objects as $obj) {
            $this->data['condition_objects'][$obj] = $this->language->get('text_'.$obj);
        }
        array_unshift($this->data['condition_objects'], $this->language->get('text_select'));
        $this->data['condition_object'] = [];
        $this->data['condition_object']['field'] = $form->getFieldHtml(
            array(
                'type'    => 'selectbox',
                'name'    => 'condition_object',
                'options' => $this->data['condition_objects'],
                'value'   => $this->data['promotion_type'],
            ));
        $this->data['condition_object']['text'] = $this->language->get('entry_condition_object');

        $this->data['condition_url'] = $this->html->getSecureURL('listing_grid/collections/getFieldsByConditionObject', '&id='.$this->data['id']);
        $this->data['active'] = 'general';
        $tabs_obj = $this->dispatch('pages/catalog/collection_tabs', array($this->data));
        $this->data['collection_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

    }

    private function validate(array $data)
    {
        $this->loadModel('catalog/collection');

        if (isset($data['name'])) {
            if (strlen(trim($data['name'])) === 0 || strlen(trim($data['name'])) > 254) {
                $this->error['name'] = $this->language->get('save_error_name');
            }
        }

        if (($error_text = $this->html->isSEOkeywordExists('collection_id='.$this->request->get['id'], $this->request->post['keyword']))) {
            $this->error['warning'] = $error_text;
            $this->error['keyword'] = $this->language->get('save_error_unique_keyword');
        }

        if (empty($this->error)) {
            return true;
        }
        return false;
    }

    public function edit_layout()
    {
        $page_controller = 'pages/product/collection';
        $page_key_param = 'collection_id';
        $collection_id = (int)$this->request->get['id'];
        $this->data['collection_id'] = $collection_id;
        $this->data['id'] = $collection_id;
        $page_url = $this->html->getSecureURL('catalog/collections/edit_layout', '&id='.$collection_id);
        //note: category can not be ID of 0.
        if (!has_value($collection_id)) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('catalog/collections');
        $this->loadLanguage('design/layout');
        $this->data['help_url'] = $this->gen_help_url('layout_edit');

        if (has_value($collection_id) && $this->request->is_GET()) {
            $this->loadModel('catalog/collection');
            $collection = $this->model_catalog_collection->getById($collection_id);
        }

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->data['heading_title'] = $this->language->get('text_edit').' '.$this->language->get('text_collection').' - '.$collection['name'];

        $this->document->setTitle($this->data['heading_title']);
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('catalog/collections'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('catalog/collections/update', '&id='.$collection_id),
            'text'      => $this->data['heading_title'],
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $page_url,
            'text'      => $this->language->get('tab_layout'),
            'separator' => ' :: ',
            'current'   => true,
        ));

        $this->data['active'] = 'layout';
        //load tabs controller
        $tabs_obj = $this->dispatch('pages/catalog/collection_tabs', array($this->data));
        $this->data['collection_tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $layout = new ALayoutManager();
        //get existing page layout or generic
        $page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $collection_id);
        $page_id = $page_layout['page_id'];
        $layout_id = $page_layout['layout_id'];
        if (isset($this->request->get['tmpl_id'])) {
            $tmpl_id = $this->request->get['tmpl_id'];
        } else {
            $tmpl_id = $this->config->get('config_storefront_template');
        }
        $params = array(
            'id'        => $collection_id,
            'page_id'   => $page_id,
            'layout_id' => $layout_id,
            'tmpl_id'   => $tmpl_id,
        );
        $url = '&'.$this->html->buildURI($params);

        // get templates
        $this->data['templates'] = array();
        $directories = glob(DIR_STOREFRONT.'view/*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $this->data['templates'][] = basename($directory);
        }
        $enabled_templates = $this->extensions->getExtensionsList(array(
            'filter' => 'template',
            'status' => 1,
        ));
        foreach ($enabled_templates->rows as $template) {
            $this->data['templates'][] = $template['key'];
        }

        $action = $this->html->getSecureURL('catalog/collections/save_layout');
        // Layout form data
        $form = new AForm('HT');
        $form->setForm(array(
            'form_name' => 'layout_form',
        ));

        $this->data['form_begin'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'layout_form',
            'attr'   => 'data-confirm-exit="true"',
            'action' => $action,
        ));

        $this->data['hidden_fields'] = '';
        foreach ($params as $name => $value) {
            $this->data[$name] = $value;
            $this->data['hidden_fields'] .= $form->getFieldHtml(
                array(
                    'type'  => 'hidden',
                    'name'  => $name,
                    'value' => $value,
                ));
        }

        $this->data['page_url'] = $page_url;
        $this->data['current_url'] = $this->html->getSecureURL('catalog/collection/edit_layout', $url);

        // insert external form of layout
        $layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);

        $layout_form = $this->dispatch('common/page_layout', array($layout));
        $this->data['layoutform'] = $layout_form->dispatchGetOutput();

        //build pages and available layouts for cloning
        $this->data['pages'] = $layout->getAllPages();
        $av_layouts = array("0" => $this->language->get('text_select_copy_layout'));
        foreach ($this->data['pages'] as $page) {
            if ($page['layout_id'] != $layout_id) {
                $av_layouts[$page['layout_id']] = $page['layout_name'];
            }
        }

        $form = new AForm('HT');
        $form->setForm(array(
            'form_name' => 'cp_layout_frm',
        ));

        $this->data['cp_layout_select'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'layout_change',
            'value'   => '',
            'options' => $av_layouts,
        ));

        $this->data['cp_layout_frm'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'cp_layout_frm',
            'attr'   => 'class="aform form-inline"',
            'action' => $action,
        ));
        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/catalog/collection_layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save_layout()
    {
        if ($this->request->is_GET()) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }

        $page_controller = 'pages/product/collection';
        $page_key_param = 'collection_id';
        $collection_id = (int)$this->request->get_or_post('id');

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('catalog/collections');

        if (!$collection_id) {
            redirect($this->html->getSecureURL('catalog/collections'));
        }

        // need to know unique page existing
        $post_data = $this->request->post;
        $tmpl_id = $post_data['tmpl_id'];
        $layout = new ALayoutManager();
        $pages = $layout->getPages($page_controller, $page_key_param, $collection_id);
        if (count($pages)) {
            $page_id = $pages[0]['page_id'];
            $layout_id = $pages[0]['layout_id'];
        } else {
            $page_info = array(
                'controller' => $page_controller,
                'key_param'  => $page_key_param,
                'key_value'  => $collection_id,
            );
            $this->loadModel('catalog/collection');
            $collection_info = $this->model_catalog_collection->getById($collection_id);
            $page_id = $layout->savePage($page_info);
            $layout_id = '';
            // need to generate layout name
            $default_language_id = $this->language->getDefaultLanguageID();
            $post_data['layout_name'] = 'Collection: '.$collection_info['name'];
        }

        //create new instance with specific template/page/layout data
        $layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
        if (has_value($post_data['layout_change'])) {
            //update layout request. Clone source layout
            $layout->clonePageLayout($post_data['layout_change'], $layout_id, $post_data['layout_name']);
            $this->session->data['success'] = $this->language->get('text_success_layout');
        } else {
            //save new layout
            $layout_data = $layout->prepareInput($post_data);
            if ($layout_data) {
                $layout->savePageLayout($layout_data);
                $this->session->data['success'] = $this->language->get('text_success_layout');
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        redirect($this->html->getSecureURL('catalog/collections/edit_layout', '&id='.$collection_id));
    }
}
