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

use AbanteCart\PBRender;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ExtensionPageBuilder extends Extension
{

    public $hookAll = true;

    /**
     * @return bool
     * @throws AException
     */
    protected function isEnabled()
    {
        $registry = Registry::getInstance();
        return ($registry?->get('config')->get('page_builder_status'));
    }

    //method for interception of calls of block controllers (including page-controllers)
    //This method calls from PBRender class
    public function __call($method, $args)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $that = $this->baseObject;
        $registry = Registry::getInstance();
        if (!$registry?->get('PBuilder_interception')) {
            return;
        }

        if (str_contains($method, 'InitData') && str_starts_with($method, 'on') && $this->baseObject_method == 'main') {
            //this resetting of children list needed to prevent loop of calls of blocks.
            /* @see AController constructor for details (there is check if controller is Page. Then asks all his children) */
            $this->baseObject->resetChildren();
        }

        if (str_contains($method, 'UpdateData') && str_starts_with($method, 'on')) {
            $template = (string)$registry->get('PBuilder_block_template');
            if ($template && $that->view->isTemplateExists($template)) {
                $that->view->setTemplate($template);
            }
            //use session as storage yet. It saves data into the filesystem
            $registry->set(
                'PBRunData',
                [
                    'document' => $that->document,
                    'data'     => array_merge($that->view->getData(), $that->data)
                ]
            );
        }
    }

    /**
     * @return bool
     * @throws AException
     */
    public function overrideControllerCommonPage_InitData()
    {
        $that = $this->baseObject;
        $router = Registry::getInstance()->get('router');
        if (IS_ADMIN === true || !$this->isEnabled() || !$router) {
            return false;
        }

        if ($that->data['already_called'] === true) {
            return false;
        }

        if ($this->baseObject_method != 'main') {
            return false;
        }

        //ok, now try to get custom template made with pageBuilder
        $preview = (bool)$that->request->get['pb'];
        if ($preview) {
            $templateTxtId = preformatTextID($that->request->get['tmpl_id']);
            $pageId = (int)$that->request->get['page_id'];
            $layoutId = (int)$that->request->get['layout_id'];
        } else {
            $templateTxtId = (string)$that->config->get('config_storefront_template');
            $pageId = $that->layout->getPageId();
            $layoutId = $that->layout->getLayoutId();
        }

        $pbTemplateData = $this->findPageTemplate($preview, $templateTxtId, $pageId, $layoutId);

        $pageRoute = $router->getController();
        if (!$pbTemplateData) {
            if ($pageRoute == 'pages/product/product'
                && $preview && !$that->request->get['product_id']
            ) {
                //in case when layout is for default product page - take a random product id
                $sql = "SELECT product_id 
                        FROM " . $that->db->table('products') . " 
                        WHERE COALESCE(date_available, NOW()) <= NOW() AND status=1
                        ORDER BY rand() 
                        LIMIT 1";
                $res = $that->db->query($sql);
                $that->request->get['product_id'] = $res->row['product_id'];
            } else {
                $this->baseObject->data['already_called'] = true;
                // if custom pageBuilder's template not found
                // do not interrupt base method "main" of ControllerCommonPage
            }
            return false;
        }

        $pbTemplateData = json_decode($pbTemplateData, true, JSON_PRETTY_PRINT);
        try {
            //TODO: need to create some PB-Renderer to process tpl
            // pass real requested route. Render must know it for call routes
            // by mask (in case when mask cover all controller down to the directory tree.
            // For example route pages/account will cover pages/account/login )
            $render = new PBRender($pageRoute);
            $render->setTemplate($pbTemplateData);
            //redirect ot log in page for all account pages
            if(str_starts_with($pageRoute,'pages/account')
                && !str_contains($pageRoute,'account/login')
                && !$that->customer->isLogged()){
                $that->session->data['redirect'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                redirect($that->html->getSecureURL('account/login'));
            }

            $output = $render->render();
            if (!$output) {
                $that->log->write(
                    'PageBuilder Render error: Empty output of renderer for route ' . $pageRoute . '!'
                );
                // if error - show base page built by dispatcher
                return false;
            }
            $response = new AResponse();
            $response->addHeader('Render: PageBuilder');
            $response->setOutput($output);
            $response->output();
        } catch (Exception $e) {
            $that->log->write(
                'PageBuilder Render error: ' . $e->getMessage() . "\n Route: " . $router->getController()
            );
            // if error - show base page built by dispatcher
            return false;
        }
        return true;
    }

    public function onAHook_InitEnd()
    {
        $registry = Registry::getInstance();
        if ($registry?->get('request')->get['tmpl_id']) {
            $registry?->get('config')->set('config_storefront_template', $registry->get('request')->get['tmpl_id']);
            $registry?->set('layout', new ALayout($registry, $registry->get('request')->get['tmpl_id']));
        }
    }


    // disable preview of main content area of FastCheckout int the canvas of PageBuilder js-editor
    public function overrideControllerPagesCheckoutFastCheckout_InitData()
    {
        /** @var ControllerPagesCheckoutFastCheckout $that */
        $that = $this->baseObject;
        if ($this->baseObject_method == '__construct') {
            return false;
        }
        //for view inside editor canvas
        if ($that->request->get['render_mode'] == 'editor') {
            if (Registry::getInstance()->get('PBuilder_dryrun')) {
                $output = '';
            } else {
                $output = '<div class="info-alert"> Main Content Area of FastCheckout page</div>';
            }
            $response = new AResponse();
            $response->setOutput($output);
            $response->output();
            return true;
        }
        return false;
    }

    /**
     * @return false|string
     * @throws AException
     */
    protected function findPageTemplate(bool $preview, string $templateTxtId, $pageId, $layoutId)
    {
        $that = $this->baseObject;
        $route = Registry::getInstance()->get('router')->getController();

        if (!$route) {
            return false;
        }

        $defaultLayout = $that->layout->getDefaultLayout();

        $r = $route == 'pages/extension/generic' ? 'generic' : $route;
        $filename = '';
        while (strlen($r) > 0) {
            $mask = strtolower(str_replace('/', '_', $r));
            if ($mask != 'generic') {
                $mask .= '-' . $pageId . '-' . $layoutId;
            }

            $fullPath = DIR_PB_TEMPLATES;
            //if run preview
            if ($preview) {
                $filename = $this->getLastSavePoint($templateTxtId, $mask);
                if ($filename) {
                    $fullPath .= 'savepoints' . DS . $templateTxtId . DS . $filename;
                }
            }

            if (!$filename && is_file($fullPath . 'public' . DS . $templateTxtId . DS . $mask . '.json')) {
                $filename = $mask . '.json';
                $fullPath .= 'public' . DS . $templateTxtId . DS . $filename;
            }
            //seek default page layout
            if ($defaultLayout['layout_id'] == $layoutId
                && !$filename
                && is_file($fullPath . 'public' . DS . $templateTxtId . DS . 'generic.json')
            ) {
                $filename = 'generic.json';
                $fullPath .= 'public' . DS . $templateTxtId . DS . $filename;
            }

            if (is_file($fullPath)) {
                if (is_readable($fullPath)) {
                    return file_get_contents($fullPath);
                } else {
                    $err = new AError('PageBuilder Render error: Template file ' . $fullPath . ' is NOT readable!');
                    $err->toLog()->toMessages();
                    break;
                }
            }
            $pos = strrpos($r, "/");
            $r = substr($r, 0, $pos);
        }

        return false;
    }

    /**
     * method returns current counter for saving snapshot of page for "undo"
     *
     * @param string $templateTxtId
     * @param string $fileNameMask
     *
     * @return false|string
     */
    protected function getLastSavePoint(string $templateTxtId, string $fileNameMask)
    {
        if (!$fileNameMask || !$templateTxtId) {
            return false;
        }
        $files = glob(
            DIR_PB_TEMPLATES . 'savepoints' . DS . $templateTxtId . DS . $fileNameMask . '*'
        );

        if (!$files) {
            return false;
        }
        $max = 0;
        array_map(function ($path) use (&$max) {
            $name = basename($path, '.json');
            $array = explode('@', $name);
            $max = max((int)$array[1], $max);
            return (int)$array[1];
        }, $files);
        return $fileNameMask . '@' . $max . '.json';
    }



    public function onControllerPagesCatalogProductLayout_UpdateData()
    {
        $that = $this->baseObject;
        $keyParam = 'product_id';
        $keyValue = (int)$that->request->get['product_id'];
        if (!$keyValue) {
            return;
        }
        $execController = 'pages/product/product';
        $this->addButton2DesignPage($execController, $keyParam, $keyValue);
    }

    public function onControllerPagesCatalogCategory_UpdateData()
    {
        if ($this->baseObject_method != 'edit_layout') {
            return;
        }

        $that = $this->baseObject;
        $keyParam = 'path';
        $keyValue = (int)$that->request->get['category_id'];
        if (!$keyValue) {
            return;
        }
        $execController = 'pages/product/category';
        $this->addButton2DesignPage($execController, $keyParam, $keyValue);
    }

    public function onControllerPagesCatalogManufacturerLayout_UpdateData()
    {
        $that = $this->baseObject;

        $keyParam = 'manufacturer_id';
        $keyValue = (int)$that->request->get['manufacturer_id'];
        if (!$keyValue) {
            return;
        }
        $execController = 'pages/product/manufacturer';
        $this->addButton2DesignPage($execController, $keyParam, $keyValue);
    }

    public function onControllerPagesDesignContent_UpdateData()
    {
        if ($this->baseObject_method != 'edit_layout') {
            return;
        }
        $that = $this->baseObject;

        $keyParam = 'content_id';
        $keyValue = (int)$that->request->get['content_id'];
        if (!$keyValue) {
            return;
        }
        $execController = 'pages/content/content';
        $this->addButton2DesignPage($execController, $keyParam, $keyValue);
    }
    public function onControllerPagesDesignLayout_UpdateData()
    {
        /** @var ControllerPagesDesignLayout $that */
        $that = $this->baseObject;

        $pageData = $that->layout->getPageData();

        $keyParam = $pageData['key_param'];
        $keyValue = (int)$pageData['key_value'];

        $execController = $pageData['controller'];
        $this->addButton2DesignPage($execController, $keyParam, $keyValue);
    }

    protected function isPagePublished(string $templateTxtId, string $rt, int $pageId, int $layoutId)
    {
        return is_file(
            DIR_PB_TEMPLATES . 'public' . DS . $templateTxtId . DS
            . preformatTextID(str_replace('/', '_', $rt)) . '-' . $pageId. '-' . $layoutId.'.json'
        );
    }

    protected function getLayoutIdsByParameters(string $rt, int $keyValue):array
    {
        $that = $this->baseObject;
        $pageId = $layoutId = null;

        $templateTxtId = $that->request->get['tmpl_id']
            ?: Registry::getInstance()->get('config')->get('config_storefront_template');
        $layout = new ALayout(Registry::getInstance(), $templateTxtId);
        $keyParam = $layout->getKeyParamByController($rt);
        $pages = $layout->getPages(
            $rt,
            $keyParam,
            $keyValue
        );
        if ($pages) {
            foreach ($pages as $page) {
                if ($page['key_param'] && $page['key_value']) {
                    $pageId = (int)$page['page_id'];
                    $layoutId = (int)$page['layout_id'];
                    break;
                }
            }
        }
        return [
            'templateTxtId' => $templateTxtId,
            'pageId' => $pageId,
            'layoutId' => $layoutId
        ];
    }
    protected function addButton2DesignPage($execController, $keyParam, $keyValue)
    {
        $that = $this->baseObject;
        $that->loadLanguage('page_builder/page_builder');

        $res = $this->getLayoutIdsByParameters($execController, $keyValue);
        extract($res);
        $link_page_builder = $that->html->getSecureUrl(
            'r/design/page_builder/createNewPage',
            '&' . http_build_query(
                [
                    'tmpl_id' => $templateTxtId,
                    'controller' => $execController,
                    'key_param' => $keyParam,
                    'key_value' => $keyValue
                ]
            )
        );
        if($pageId && $layoutId) {
            $isPublished = $this->isPagePublished($templateTxtId, $execController, $pageId, $layoutId);
            if($isPublished) {
                $link_page_builder = $that->html->getSecureUrl(
                    'design/page_builder',
                    '&' . http_build_query(
                        [
                            'tmpl_id'   => $templateTxtId,
                            'page_id'   => $pageId,
                            'layout_id' => $layoutId
                        ]
                    )
                );
                $that->view->assign('block_layout_form', false);

                $that->view->addHookVar(
                    'layout_form_post',
                    '<div id="page-layout" class="panel-body panel-body-nopadding tab-content col-xs-12 text-center">'
                    .'<div class="layout_form_post padding10 pt10">'
                    .$that->language->get('page_builder_text_already_published').'&nbsp;'
                    .$that->html->buildElement([
                        'type' => 'button',
                        'href' => $link_page_builder,
                        'text' => $that->language->get('page_builder_button_click_to_edit_page'),
                        'style' => 'btn btn-default'
                    ]).'</div></div>'
                );
            }else {
                $that->view->addHookVar(
                    'layout_form_action_post',
                    '<div class="btn-group mr10"><div class="pull-right">'
                    .$that->language->get('page_builder_text_can_try').'&nbsp;'
                    .$that->html->buildElement([
                        'type' => 'button',
                        'href' => $link_page_builder,
                        'text' => $that->language->get('page_builder_button_click_to_try'),
                    ]).'</div></div>'
                );
            }
        }else{
            $that->view->addHookVar(
                'layout_form_action_post',
                '<div class="btn-group mr10"><div class="pull-right">'
                .$that->language->get('page_builder_text_can_try').'&nbsp;'
                .$that->html->buildElement([
                    'type' => 'button',
                    'href' => $link_page_builder,
                    'text' => $that->language->get('page_builder_button_click_to_try'),
                    'style' => 'btn btn-default'
                ]).'</div></div>'
            );
        }
    }
}