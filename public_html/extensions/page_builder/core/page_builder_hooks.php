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

    public function onControllerPagesCatalogProductTabs_InitData()
    {
        $inactive = false;
        $that = $this->baseObject;
        $product_id = $that->request->get['product_id'];
        if (!$product_id) {
            return;
        }
        $execController = 'pages/product/product';
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template'));
        $pages = $layout->getPages(
            $execController,
            $that->layout->getKeyParamByController($execController),
            $product_id
        );
        if (!$pages) {
            $inactive = true;
        } else {
            $page_id = $layout_id = null;
            foreach ($pages as $page) {
                if ($page['key_param'] && $page['key_value']) {
                    $page_id = $page['page_id'];
                    $layout_id = $page['layout_id'];
                    $that->data['link_page_builder'] = $that->html->getSecureUrl(
                        'design/page_builder',
                        '&tmpl_id=' . $that->config->get('config_storefront_template') . '&page_id=' . $page_id . '&layout_id='
                        . $layout_id
                    );
                    break;
                }
            }
            if (!$page_id || !$layout_id) {
                $inactive = true;
            }
        }

        $that->loadLanguage('page_builder/page_builder');
        $that->data['additionalTabs'][] = 'page_builder';
        $that->data['tab_page_builder'] = $that->language->get('page_builder_name');
        if ($inactive) {
            $that->data['link_page_builder'] = "Javascript:void(0);";
            $that->data['inactive'][] = 'page_builder';
            $that->data['title_page_builder'] = $that->language->get('page_builder_tab_title');
        }
    }

    public function onControllerPagesCatalogCategoryTabs_InitData()
    {
        $inactive = false;
        $that = $this->baseObject;
        $category_id = $that->request->get['category_id'];
        if (!$category_id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template'));
        $execController = 'pages/product/category';
        $pages = $layout->getPages(
            $execController,
            $that->layout->getKeyParamByController($execController),
            $category_id
        );
        if (!$pages) {
            $inactive = true;
        } else {
            $page_id = $layout_id = null;
            foreach ($pages as $page) {
                if ($page['key_param'] && $page['key_value']) {
                    $page_id = $page['page_id'];
                    $layout_id = $page['layout_id'];
                    $that->data['link_page_builder'] = $that->html->getSecureUrl(
                        'design/page_builder',
                        '&tmpl_id=' . $that->config->get('config_storefront_template') . '&page_id=' . $page_id . '&layout_id='
                        . $layout_id
                    );
                    break;
                }
            }
            if (!$page_id || !$layout_id) {
                $inactive = true;
            }
        }

        $that->loadLanguage('page_builder/page_builder');
        $that->data['additionalTabs'][] = 'page_builder';
        $that->data['tab_page_builder'] = $that->language->get('page_builder_name');
        if ($inactive) {
            $that->data['link_page_builder'] = "Javascript:void(0);";
            $that->data['inactive'][] = 'page_builder';
            $that->data['title_page_builder'] = $that->language->get('page_builder_tab_title');
        }
    }

    public function onControllerPagesCatalogManufacturer_InitData()
    {
        $inactive = false;
        $that = $this->baseObject;
        $id = $that->request->get['manufacturer_id'];
        if (!$id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template'));
        $execController = 'pages/product/manufacturer';
        $pages = $layout->getPages(
            $execController,
            $that->layout->getKeyParamByController($execController),
            $id
        );
        if (!$pages) {
            $inactive = true;
        } else {
            $page_id = $layout_id = null;
            foreach ($pages as $page) {
                if ($page['key_param'] && $page['key_value']) {
                    $page_id = $page['page_id'];
                    $layout_id = $page['layout_id'];
                    $that->data['link_page_builder'] = $that->html->getSecureUrl(
                        'design/page_builder',
                        '&tmpl_id=' . $that->config->get('config_storefront_template') . '&page_id=' . $page_id . '&layout_id='
                        . $layout_id
                    );
                    break;
                }
            }
            if (!$page_id || !$layout_id) {
                $inactive = true;
            }
        }

        $that->loadLanguage('page_builder/page_builder');
        $that->data['additionalTabs'][] = 'page_builder';
        $that->data['tab_page_builder'] = $that->language->get('page_builder_name');
        if ($inactive) {
            $that->data['link_page_builder'] = "Javascript:void(0);";
            $that->data['inactive'][] = 'page_builder';
            $that->data['title_page_builder'] = $that->language->get('page_builder_tab_title');
        }

        $that->view->addHookVar(
            'extension_tabs',
            '<li><a href="' . $that->data['link_page_builder'] . '" title="' . $that->data['title_page_builder'] . '"><span>'
            . $that->data['tab_page_builder'] . '</span></a></li>'
        );
    }

    public function onControllerPagesCatalogManufacturerLayout_InitData()
    {
        $inactive = false;
        $that = $this->baseObject;
        $id = $that->request->get['manufacturer_id'];
        if (!$id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template'));
        $execController = 'pages/product/manufacturer';
        $pages = $layout->getPages(
            $execController,
            $that->layout->getKeyParamByController($execController),
            $id
        );
        if (!$pages) {
            $inactive = true;
        } else {
            $page_id = $layout_id = null;
            foreach ($pages as $page) {
                if ($page['key_param'] && $page['key_value']) {
                    $page_id = $page['page_id'];
                    $layout_id = $page['layout_id'];
                    $that->data['link_page_builder'] = $that->html->getSecureUrl(
                        'design/page_builder',
                        '&tmpl_id=' . $that->config->get('config_storefront_template') . '&page_id=' . $page_id . '&layout_id='
                        . $layout_id
                    );
                    break;
                }
            }
            if (!$page_id || !$layout_id) {
                $inactive = true;
            }
        }

        $that->loadLanguage('page_builder/page_builder');
        $that->data['additionalTabs'][] = 'page_builder';
        $that->data['tab_page_builder'] = $that->language->get('page_builder_name');
        if ($inactive) {
            $that->data['link_page_builder'] = "Javascript:void(0);";
            $that->data['inactive'][] = 'page_builder';
            $that->data['title_page_builder'] = $that->language->get('page_builder_tab_title');
        }

        $that->view->addHookVar(
            'extension_tabs',
            '<li class="' . ($inactive ? 'inactive' : '') . '"><a href="' . $that->data['link_page_builder'] . '" title="'
            . $that->data['title_page_builder'] . '"><span>' . $that->data['tab_page_builder'] . '</span></a></li>'
        );
    }

    public function onControllerPagesDesignContent_InitData()
    {
        $inactive = false;
        if (!in_array($this->baseObject_method, ['edit_layout', 'update'])) {
            return;
        }
        $that = $this->baseObject;
        if (is_int(strpos($that->request->get['content_id'], '_'))) {
            list(, $id) = explode('_', $that->request->get['content_id']);
        } else {
            $id = $that->request->get['content_id'];
        }
        if (!$id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template'));
        $execController = 'pages/content/content';
        $pages = $layout->getPages(
            $execController,
            $that->layout->getKeyParamByController($execController),
            $id
        );
        if (!$pages) {
            $inactive = true;
        } else {
            $page_id = $layout_id = null;
            foreach ($pages as $page) {
                if ($page['key_param'] && $page['key_value']) {
                    $page_id = $page['page_id'];
                    $layout_id = $page['layout_id'];
                    $that->data['link_page_builder'] = $that->html->getSecureUrl(
                        'design/page_builder',
                        '&tmpl_id=' . $that->config->get('config_storefront_template') . '&page_id=' . $page_id . '&layout_id='
                        . $layout_id
                    );
                    break;
                }
            }
            if (!$page_id || !$layout_id) {
                $inactive = true;
            }
        }

        $that->loadLanguage('page_builder/page_builder');
        $that->data['additionalTabs'][] = 'page_builder';
        $that->data['tab_page_builder'] = $that->language->get('page_builder_name');
        if ($inactive) {
            $that->data['link_page_builder'] = "Javascript:void(0);";
            $that->data['inactive'][] = 'page_builder';
            $that->data['title_page_builder'] = $that->language->get('page_builder_tab_title');
        }

        $that->view->addHookVar(
            'extension_tabs',
            '<li class="' . ($inactive ? 'inactive' : '') . '"><a href="' . $that->data['link_page_builder'] . '" title="'
            . $that->data['title_page_builder'] . '"><span>' . $that->data['tab_page_builder'] . '</span></a></li>'
        );
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

}
