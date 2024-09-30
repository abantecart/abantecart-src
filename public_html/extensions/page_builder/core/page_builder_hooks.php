<?php

use AbanteCart\PBRender;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}
require_once ('helper.php');
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
        if ($registry->get('config')) {
            return ($registry->get('config')->get('page_builder_status'));
        }
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
        if (!$registry->get('PBuilder_interception')) {
            return;
        }

        if (is_int(strpos($method, 'InitData'))
            && substr($method, 0, 2) == 'on'
            && $this->baseObject_method == 'main') {
            //this resetting of children list needed to prevent loop of calls of blocks.
            /* @see AController constructor for details (there is check if controller is Page. Then asks all his children) */
            $this->baseObject->resetChildren();
        }

        if (is_int(strpos($method, 'UpdateData')) && substr($method, 0, 2) == 'on') {
            $template = (string) $registry->get('PBuilder_block_template');

            if ($template && $that->view->isTemplateExists($template)) {
                $that->view->setTemplate($template);
            }
            //use session as storage yet. It saves data into the filesystem
            $registry->set(
                'PBRunData', ['document' => $that->document, 'data' => array_merge($that->view->getData(), $that->data)]
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
        $pbTemplateData = $this->findPageTemplate();
        if (!$pbTemplateData) {
            if(
                $router->getController() == 'pages/product/product'
                && $that->request->get['pb']
                && !$that->request->get['product_id']
            ){
                //in case when layout is for default product page - take a random product id
                $sql = "SELECT product_id 
                        FROM ". $that->db->table('products')." 
                        WHERE date_available <= NOW() AND status=1
                        ORDER BY rand() 
                        LIMIT 1";
                $res = $that->db->query($sql);
                $that->request->get['product_id'] = $res->row['product_id'];
                return false;
            }else {
                $this->baseObject->data['already_called'] = true;
                // if custom pageBuilder's template not found
                // do not interrupt base method "main" of ControllerCommonPage
                return false;
            }
        }

        $pbTemplateData = json_decode($pbTemplateData, true, JSON_PRETTY_PRINT);
        try {
            //TODO: need to create some PB-Renderer to process tpl
            // pass real requested route. Render must know it for call routes
            // by mask (in case when mask cover all controller down to the directory tree.
            // For example route pages/account will cover pages/account/login )
            $render = new PBRender($router->getController());
            $render->setTemplate($pbTemplateData);
            //not implemented yet
            //$render->batchAssign((array)$someViewData);
            $output = $render->render();
            if (!$output) {
                $that->log->write(
                    'PageBuilder Render error: Empty output of renderer for route '
                    .$router->getController().'!'
                );
                // if error - show base page built by dispatcher
                return false;
            }
            $response = new AResponse();
            $response->setOutput($output);
            $response->output();
        } catch (Exception $e) {
            $that->log->write(
                'PageBuilder Render error: '.$e->getMessage()
                ."\n Route: ".$router->getController()
            );
            // if error - show base page built by dispatcher
            return false;
        }
        return true;
    }

    public function onAHook_InitEnd(){
        $registry = Registry::getInstance();
        if($registry->get('request')->get['tmpl_id']){
            $registry->get('config')->set('config_storefront_template', $registry->get('request')->get['tmpl_id']);
            $registry->set('layout', new ALayout($registry, $registry->get('request')->get['tmpl_id']));
        }
    }

    /**
     * @return false|string
     * @throws AException
     */
    protected function findPageTemplate()
    {
        $that = $this->baseObject;
        $route = Registry::getInstance()->get('router')->getController();

        if (!$route) {
            return false;
        }

        $config = $that->config;
        $currentTemplate = $config->get('config_storefront_template');
        $defaultLayout = $that->layout->getDefaultLayout();

        $r = ($route == 'pages/extension/generic') ? 'generic' : $route;
        $filename = '';
        $layoutId = $that->request->get['pb'] && $that->request->get['layout_id']
            ? $that->request->get['layout_id']
            : $that->layout->getLayoutId();

        $pageId = $that->request->get['pb'] && $that->request->get['page_id']
            ? $that->request->get['page_id']
            : $that->layout->getPageId();

        while (strlen($r) > 0) {
            $mask = strtolower(str_replace('/', '_', $r)).'-'.$pageId.'-'.$layoutId;
            $fullPath = DIR_PB_TEMPLATES.$currentTemplate.DIRECTORY_SEPARATOR;
            //if run preview
            if ($that->request->get['pb']) {
                $filename = $this->getLastSavePoint($mask);
                if ($filename) {
                    $fullPath .= 'savepoints'.DIRECTORY_SEPARATOR.$filename;
                }
            }

            if (!$filename && is_file($fullPath.'public'.DIRECTORY_SEPARATOR.$mask.'.json')) {
                $filename = $mask.'.json';
                $fullPath .= 'public'.DIRECTORY_SEPARATOR.$filename;
            }
            //seek default page layout
            if ($defaultLayout['layout_id'] == $layoutId
                && !$filename
                && is_file($fullPath.'public'.DIRECTORY_SEPARATOR.'generic.json')
            ) {
                $filename = 'generic.json';
                $fullPath .= 'public'.DIRECTORY_SEPARATOR.$filename;
            }

            if (is_file($fullPath)) {
                if (is_readable($fullPath)) {
                    return file_get_contents($fullPath);
                } else {
                    $err = new AError('PageBuilder Render error: Template file '.$fullPath.' is NOT readable!');
                    $err->toLog()->toMessages();
                    break;
                }
            }
            $pos = strrpos($r, "/");
            $r = substr($r, 0, $pos);
        }

        if ($currentTemplate != 'default') {
            $filename = strtolower(str_replace('/', '_', $route)).'.json';
            $fullPath = DIR_PB_TEMPLATES.'default'.DIRECTORY_SEPARATOR.$filename;
            if (is_file($fullPath)) {
                if (is_readable($fullPath)) {
                    return file_get_contents($fullPath);
                } else {
                    $err = new AError('PageBuilder Render error: Template file '.$fullPath.' is NOT readable!');
                    $err->toLog()->toMessages();
                }
            }
        }
        return false;
    }

    /**
     * method returns current counter for saving snapshot of page for "undo"
     *
     * @param string $fileNameMask
     *
     * @return false|string
     * @throws AException
     */
    protected function getLastSavePoint($fileNameMask)
    {
        if (!$fileNameMask) {
            return false;
        }
        $currentTemplate = Registry::getInstance()->get('config')->get('config_storefront_template');
        $files = glob(
            DIR_PB_TEMPLATES.$currentTemplate.DIRECTORY_SEPARATOR.'savepoints'.DIRECTORY_SEPARATOR.$fileNameMask.'*'
        );

        if (!$files) {
            return false;
        }
        $max = 0;
        array_map(function ($path) use (&$max) {
            $name = basename($path, '.json');
            $array = explode('@', $name);
            $max = max((int) $array[1], $max);
            return (int) $array[1];
        }, $files);
        return $fileNameMask.'@'.$max.'.json';
    }

    public function onControllerPagesCatalogProductTabs_InitData()
    {
        $that = $this->baseObject;
        $product_id = $that->request->get['product_id'];
        if (!$product_id) {
            return;
        }
        $execController = 'pages/product/product';
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template') );
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
                        '&tmpl_id='.$that->config->get('config_storefront_template').'&page_id='.$page_id.'&layout_id='
                        .$layout_id
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
        $that = $this->baseObject;
        $category_id = $that->request->get['category_id'];
        if (!$category_id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template') );
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
                        '&tmpl_id='.$that->config->get('config_storefront_template').'&page_id='.$page_id.'&layout_id='
                        .$layout_id
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
        $that = $this->baseObject;
        $id = $that->request->get['manufacturer_id'];
        if (!$id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template') );
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
                        '&tmpl_id='.$that->config->get('config_storefront_template').'&page_id='.$page_id.'&layout_id='
                        .$layout_id
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
            '<li><a href="'.$that->data['link_page_builder'].'" title="'.$that->data['title_page_builder'].'"><span>'
            .$that->data['tab_page_builder'].'</span></a></li>'
        );
    }

    public function onControllerPagesCatalogManufacturerLayout_InitData()
    {
        $that = $this->baseObject;
        $id = $that->request->get['manufacturer_id'];
        if (!$id) {
            return;
        }
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template') );
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
                        '&tmpl_id='.$that->config->get('config_storefront_template').'&page_id='.$page_id.'&layout_id='
                        .$layout_id
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
            '<li class="'.($inactive ? 'inactive' : '').'"><a href="'.$that->data['link_page_builder'].'" title="'
            .$that->data['title_page_builder'].'"><span>'.$that->data['tab_page_builder'].'</span></a></li>'
        );
    }

    public function onControllerPagesDesignContent_InitData()
    {
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
        $layout = new ALayout(Registry::getInstance(), $that->config->get('config_storefront_template') );
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
                        '&tmpl_id='.$that->config->get('config_storefront_template').'&page_id='.$page_id.'&layout_id='
                        .$layout_id
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
            '<li class="'.($inactive ? 'inactive' : '').'"><a href="'.$that->data['link_page_builder'].'" title="'
            .$that->data['title_page_builder'].'"><span>'.$that->data['tab_page_builder'].'</span></a></li>'
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
