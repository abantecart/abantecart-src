<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

class ControllerPagesCatalogManufacturerLayout extends AController
{
    public function main()
    {
        $page_controller = 'pages/product/manufacturer';
        $page_key_param = 'manufacturer_id';
        $manufacturer_id = (int)$this->request->get['manufacturer_id'];
        $page_url = $this->html->getSecureURL('catalog/manufacturer_layout', '&manufacturer_id=' . $manufacturer_id);

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        /** @see public_html/admin/language/english/catalog/manufacturer.xml */
        $this->loadLanguage('catalog/manufacturer');
        $this->loadLanguage('design/layout');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->loadModel('catalog/manufacturer');
        $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

        $this->data['help_url'] = $this->gen_help_url('manufacturer_layout');

        if (!$manufacturer_info) {
            $this->session->data['warning'] = $this->language->get('error_manufacturer_not_found');
            redirect($this->html->getSecureURL('catalog/manufacturer'));
        }

        $this->data['heading_title'] = $this->language->get('text_edit')
            . $this->language->get('text_manufacturer')
            . ' - '
            . $manufacturer_info['name'];
        $this->data['manufacturer_edit'] = $this->html->getSecureURL(
            'catalog/manufacturer/update',
            '&manufacturer_id=' . $manufacturer_id
        );

        $this->data['tab_edit'] = $this->language->get('entry_edit');
        $this->data['tab_layout'] = $this->language->get('entry_layout');
        $this->data['manufacturer_layout'] = $page_url;

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $this->data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('catalog/manufacturer'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->data['manufacturer_edit'],
                'text'      => $this->language->get('entry_layout') . ' - ' . $manufacturer_info['name'],
                'separator' => ' :: ',
                'current'   => true,
            ]
        );

        $this->data['active'] = 'layout';

        $tmpl_id = $this->request->get['tmpl_id'] ?: $this->config->get('config_storefront_template');
        $layout = new ALayoutManager($tmpl_id);
        //get existing page layout or generic
        $page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $manufacturer_id);
        $page_id = $page_layout['page_id'];
        $layout_id = $page_layout['layout_id'];

        $params = [
            'manufacturer_id' => $manufacturer_id,
            'page_id'         => $page_id,
            'layout_id'       => $layout_id,
            'tmpl_id'         => $tmpl_id,
        ];
        $url = '&' . $this->html->buildURI($params);

        // get templates
        $this->data['templates'] = [];
        $directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
        if ($directories) {
            $this->data['templates'] = array_map('basename', $directories);
        }
        $enabled_templates = $this->extensions->getExtensionsList(
            [
                'filter' => 'template',
                'status' => 1,
            ]
        );
        $this->data['templates'] = array_merge($this->data['templates'], array_column($enabled_templates->rows, 'key'));

        $action = $this->html->getSecureURL('catalog/manufacturer_layout/save');
        // Layout form data
        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'layout_form',
            ]
        );

        $this->data['form_begin'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'layout_form',
                'attr'   => 'data-confirm-exit="true"',
                'action' => $action,
            ]
        );

        $this->data['hidden_fields'] = [];
        foreach ($params as $name => $value) {
            $this->data[$name] = $value;
            $this->data['hidden_fields'][] = $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => $name,
                    'value' => $value,
                ]
            );
        }

        $this->data['page_url'] = $page_url;
        $this->data['current_url'] = $this->html->getSecureURL('catalog/manufacturer_layout', $url);

        // insert external form of layout
        $layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);

        $layoutForm = $this->dispatch('common/page_layout', [$layout]);
        $this->data['layoutform'] = $layoutForm->dispatchGetOutput();

        //build pages and available layouts for cloning
        $this->data['pages'] = $layout->getAllPages();
        $av_layouts = ["0" => $this->language->get('text_select_copy_layout')];
        foreach ($this->data['pages'] as $page) {
            if ($page['layout_id'] != $layout_id) {
                $av_layouts[$page['layout_id']] = $page['layout_name'];
            }
        }

        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'cp_layout_frm',
            ]
        );

        $this->data['cp_layout_select'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'source_layout_id',
                'value'   => '',
                'options' => $av_layouts,
            ]
        );

        $this->data['cp_layout_frm'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'cp_layout_frm',
                'attr'   => 'class="aform form-inline"',
                'action' => $action,
            ]
        );
        if ($this->config->get('config_embed_status')) {
            $this->view->assign(
                'embed_url',
                $this->html->getSecureURL('common/do_embed/manufacturers', '&manufacturer_id=' . $manufacturer_id)
            );
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/catalog/manufacturer_layout.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save()
    {
        if ($this->request->is_GET() || !$this->request->post) {
            redirect($this->html->getSecureURL('catalog/manufacturer_layout'));
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $post = $this->request->post;
        $pageData = [
            'controller' => 'pages/product/manufacturer',
            'key_param'  => 'manufacturer_id',
            'key_value'  => (int)$post['manufacturer_id'],
        ];


        $this->loadLanguage('catalog/manufacturer');

        if (!$pageData['key_value']) {
            unset($this->session->data['success']);
            redirect($this->html->getSecureURL('catalog/manufacturer'));
        }

        /** @var ModelCatalogManufacturer $mdl */
        $mdl = $this->loadModel('catalog/manufacturer');
        $manufacturerInfo = $mdl->getManufacturer($pageData['key_value']);
        if ($manufacturerInfo) {
            $post['layout_name'] = $this->language->get('text_manufacturer') . ': ' . $manufacturerInfo['name'];
            $languages = $this->language->getAvailableLanguages();
            foreach ($languages as $l) {
                $pageData['page_descriptions'][$l['language_id']] = $manufacturerInfo;
            }
        }

        if (saveOrCreateLayout($post['tmpl_id'], $pageData, $post)) {
            $this->session->data['success'] = $this->language->get('text_success_layout');
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        redirect(
            $this->html->getSecureURL(
                'catalog/manufacturer_layout',
                '&manufacturer_id=' . $pageData['key_value'] . '&tmpl_id=' . $post['tmpl_id']
            )
        );
    }
}