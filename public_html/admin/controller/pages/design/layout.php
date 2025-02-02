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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesDesignLayout extends AController
{
    public $layout;

    /**
     * @param Registry $registry
     * @param int $instance_id
     * @param string $controller
     * @param int $parent_controller
     * @throws AException
     */
    public function __construct($registry, $instance_id, $controller, $parent_controller)
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $templateTxtId = $this->request->get['tmpl_id'] ?? $registry->get('config')->get('config_storefront_template');
        $page_id = $this->request->get['page_id'] ?? null;
        $layout_id = $this->request->get['layout_id'] ?? null;
        $this->layout = new ALayoutManager($templateTxtId, $page_id, $layout_id);
    }

    public function main()
    {
        $layout_data = [];
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $layout_data['current_page'] = $this->layout->getPageData();

        $this->session->data['content_language_id'] = $this->config->get('storefront_language_id');

        $this->document->setTitle(
            $this->language->get('heading_title') . ' - ' . $layout_data['current_page']['layout_name']
        );

        $templateTxtId = $this->request->get['tmpl_id'] ?? $this->config->get('config_storefront_template');

        $layout_data['page_url'] = $this->html->getSecureURL('design/layout');

        $allPages = $this->layout->getAllPages();
        $pageGroups = array_merge($this->layout::PAGE_GROUPS, (array)$this->data['page_groups']);
        $layoutPages = [];
        foreach ($allPages as $page) {
            $httpQuery = [
                'page_id'   => $page['page_id'],
                'layout_id' => $page['layout_id'],
                'tmpl_id'   => $templateTxtId
            ];
            $page['url'] = $this->html->getSecureURL('design/layout', '&' . http_build_query($httpQuery));
            if (!$page['restricted']) {
                $page['delete_url'] = $this->html->getSecureURL(
                    'design/layout/delete',
                    '&' . http_build_query($httpQuery)
                );
            }
            $pageGroup = array_filter(
                array_keys($pageGroups),
                function ($controller) use ($page) {
                    return str_starts_with($page['controller'], $controller);
                }
            );
            if ($pageGroup) {
                $k = current($pageGroup);
                if (!$layoutPages[$k]) {
                    $layoutPages[$k] = [
                        'id'          => 'dp' . preformatTextID($k),
                        'name'        => $pageGroups[$k],
                        'layout_name' => $pageGroups[$k],
                        'restricted'  => true
                    ];
                }
                $layoutPages[$k]['children'][] = $page;
            } else {
                $layoutPages[] = $page;
            }
        }

        $layout_data['pages'] = $layoutPages;

        $params = [
            'page_id'   => $layout_data['current_page']['page_id'],
            'layout_id' => $this->layout->getLayoutId(),
            'tmpl_id'   => $this->layout->getTemplateId(),
        ];

        $url = '&' . $this->html->buildURI($params);

        // get templates
        $layout_data['templates'] = $this->layout->getTemplateList(true);

        // breadcrumb path
        $this->document->initBreadcrumb(
            [
                'href' => $this->html->getSecureURL('index/home'),
                'text' => $this->language->get('text_home'),
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL('design/layout'),
                'text'    => $this->document->getTitle(),
                'current' => true,
            ]
        );

        // Layout form data
        $form = new AForm('HT');
        $form->setForm(
            [
                'form_name' => 'layout_form',
            ]
        );

        $layout_data['form_begin'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'layout_form',
                'attr'   => 'data-confirm-exit="true"',
                'action' => $this->html->getSecureURL('design/layout/save'),
            ]
        );

        $layout_data['hidden_fields'] = '';
        foreach ($params as $name => $value) {
            $layout_data[$name] = $value;
            $layout_data['hidden_fields'] .= $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => $name,
                    'value' => $value,
                ]
            );
        }

        $layout_data['page_builder_url'] = $this->html->getSecureURL('r/design/page_builder');
        $layout_data['generate_preview_url'] = $this->html->getSecureURL('design/layout/preview');
        $layout_data['current_url'] = $this->html->getSecureURL('design/layout', $url);
        $layout_data['page_delete_url'] = $this->html->getSecureURL('design/layout/delete');
        $layout_data['insert_url'] = $this->html->getSecureURL('design/layout/insert', $url);
        $layout_data['help_url'] = $this->gen_help_url('layout');
        $layout_data['new_layout_modal_url'] = $this->html->getSecureURL(
            'r/design/page_layout',
            '&'.http_build_query(['tmpl_id' => $params['tmpl_id']])
        );

        // Alert messages
        if (isset($this->session->data['warning'])) {
            $layout_data['error_warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        if (isset($this->session->data['success'])) {
            $layout_data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $layoutForm = $this->dispatch('common/page_layout', [$this->layout]);
        $layout_data['block_layout_form'] = $layoutForm->dispatchGetOutput();

        $this->view->batchAssign($layout_data);
        $this->processTemplate('pages/design/layout.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function save()
    {
        if(!$this->request->is_POST()) {
            redirect( $this->html->getSecureURL( 'design/layout' ) );
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $templateTxtId = $this->request->post['tmpl_id'];
        $pageId = (int)$this->request->post['page_id'];
        $layoutId = (int)$this->request->post['layout_id'];

        $layout = new ALayoutManager($templateTxtId, $pageId, $layoutId);
        $layout_data = $layout->prepareInput($this->request->post);
        if ($layout_data) {
            $layout->savePageLayout($layout_data);
            $this->session->data['success'] = $this->language->get('text_success');
        }

        redirect(
            $this->html->getSecureURL(
                'design/layout',
                '&' . http_build_query(
                    [
                        'tmpl_id'   => $templateTxtId,
                        'page_id'   => $pageId,
                        'layout_id' => $layoutId,
                    ]
                )
            )
        );
    }

    public function delete()
    {
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $templateTxtId = $this->request->post_or_get('tmpl_id');
        $pageId = (int)$this->request->post_or_get('page_id');
        $layoutId = (int)$this->request->post_or_get('layout_id');

        $success = false;
        if ($this->request->is_GET() && $this->request->get['confirmed_delete'] == 'yes') {
            $layout = new ALayoutManager($templateTxtId, $pageId, $layoutId);
            //do delete this page/layout validate that it is allowed to delete
            $page = $layout->getPageData();
            if ($page['restricted']) {
                $this->session->data['warning'] = $this->language->get('text_delete_restricted');
            } else {
                if ($layout->deletePageLayoutByID($pageId, $layoutId)) {
                    $this->session->data['success'] = $this->language->get('text_delete_success');
                    $success = true;
                } else {
                    $this->session->data['warning'] = 'Error! Try again.';
                }
            }
        }

        $httpQuery = [];
        if ($templateTxtId) {
            $httpQuery['tmpl_id'] = $templateTxtId;
        }
        if (!$success) {
            if ($layoutId) {
                $httpQuery['layout_id'] = $layoutId;
            }
            if ($pageId) {
                $httpQuery['page_id'] = $pageId;
            }
        }
        redirect(
            $this->html->getSecureURL(
                'design/layout',
                '&' . http_build_query( $httpQuery )
            )
        );
    }
}