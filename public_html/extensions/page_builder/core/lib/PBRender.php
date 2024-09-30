<?php

namespace AbanteCart;

use ADispatcher;
use ADocument;
use AException;
use AResource;
use ARouter;
use DOMDocument;
use DOMXPath;
use Exception;
use Registry;

/**
 *
 */
class PBRender
{
    /** @var Registry|null */
    protected $registry;
    /** @var */
    protected $output = '';
    /**
     * @var string Route of main Content Area
     */
    protected $mainRoute = '';
    protected $mainContent = [];
    protected $title = '';
    protected $docStyles = [];
    protected $docJs = [];
    protected $templateData = [];
    protected $customData = [];
    public $componentTypes = [
        'abantecart-generic-block',
        'abantecart-static-block',
        'abantecart-listing-block',
        'abantecart-main-content-area',
    ];

    public function __construct($mainRoute = '')
    {
        $this->registry = Registry::getInstance();
        if (!$this->registry) {
            throw new AException(AC_ERR_LOAD, 'Registry instance not found!');
        }
        $this->registry->get('extensions')->hk_InitData($this, __FUNCTION__);
        $this->mainRoute = $mainRoute;
    }

    public function setTitle(string $title = '')
    {
        $this->title = $title;
    }

    public function setTemplate($templateData = [])
    {
        $this->templateData = $templateData;

    }

    /** not implemented yet */
    public function batchAssign($data = [])
    {
        $this->customData += $data;
    }

    public function render()
    {
        $registry = Registry::getInstance();
        $templateTxtId = $registry->get('config')->get('config_storefront_template');
        $baseHtmlFile = DIR_PB_TEMPLATES.$templateTxtId.DS.'base.html';
        if (!is_file($baseHtmlFile)) {
            $baseHtmlFile = $templateTxtId == 'default'
                ? DIR_STOREFRONT.'view'.DS.$templateTxtId.DS.'base.html'
                : DIR_EXT.$templateTxtId.DS.'storefront'.DS.'view'.DS.$templateTxtId.DS.'base.html';
            if (!is_file($baseHtmlFile)) {
                copy(DIR_EXT.'page_builder'.DS.'base.html', $baseHtmlFile);
            }
        }
        $this->output = file_get_contents($baseHtmlFile);

        $body = $this->templateData['pageHtml'][0]['html'];
        $bodyDoc = new DOMDocument();
        $bodyDoc->loadHTML($body);
        $xpath = new DOMXpath($bodyDoc);
        // remove meta and title tags from body before rendering
        foreach ($xpath->evaluate("//body/base | //body/meta | //body/link | //body/title") as $node) {
            $node->remove();
        }
        // add specific css-class for route
        $bodyDomNode = $xpath->query("//body")[0];
        $bodyDomNode->setAttribute('class',(str_replace("/", "-", $registry->get('request')->get['rt']) ?: 'home'));

        $componentInfo = $this->templateData['pages'][0]['frames'][0]['component']['components'];
        //paste markers into html for replacement with results
        $this->prepareOutput($bodyDoc, $xpath, $componentInfo);
        $body = $bodyDoc->saveHTML($bodyDomNode);

        $this->output = str_replace(
            [
                '{{lang}}',
                '{{version}}',
                '<style></style>',
                '{{baseUrl}}',
                '{{storeName}}',
                '{{currency}}',
                '{{default_currency}}',
                '{{text_add_cart_confirm}}',
                '<body></body>'
            ],
            [
                $this->registry->get('language')->getLanguageCode(),
                VERSION,
                '<style>'.$this->templateData['pageHtml'][0]['css'].'</style>',
                HTTPS_SERVER,
                $registry->get('config')->get('config_title_'.$this->registry->get('language')->getLanguageID()),
                $registry->get('currency')->getCode(),
                $registry->get('config')->get('config_currency'),
                $this->registry->get('language')->get('text_add_cart_confirm'),
                $body
            ],
            $this->output
        );

        //run page-controller first to fill some document info, such breadcrumbs
        $this->processMainContentArea($componentInfo);

        //replacing of markers with results
        $this->processComponents($componentInfo);

        //paste cumulative styles and js of blocks
        if ($this->docStyles) {
            $cssTags = '';
            foreach ($this->docStyles as $style) {
                $cssTags .= '<link rel="'.$style['rel'].'" type="text/css" href="'.$style['href'].'" media="'.$style['media'].'" />'."\n";
            }
            $this->output = str_replace(
                '<!--  {{blocks_css}}-->',
                $cssTags,
                $this->output
            );
        }
        if ($this->docJs) {
            $jsTags = '';
            foreach ($this->docJs as $jsSrc) {
                $jsTags .= '<script type="text/javascript" src="'.$jsSrc.'" defer></script>'."\n";
            }
            $this->output = str_replace(
                '<!--  {{blocks_js}}-->',
                $jsTags,
                $this->output
            );
        }

        $iconUri = $this->registry->get('config')->get('config_icon');
        //see if we have a resource ID or path
        if ($iconUri) {
            if (is_numeric($iconUri)) {
                $resource = new AResource('image');
                $resourceInfo = $resource->getResource($iconUri);
                if (is_file(DIR_RESOURCE.$resourceInfo['type_dir'].$resourceInfo['resource_path'])) {
                    $iconUri = $resourceInfo['type_dir'].$resourceInfo['resource_path'];
                }
            }
            $this->output = str_replace(
                '<!--  {{favicon}}-->',
                '<link href="resources/'.$iconUri.'" type="'.mime_content_type(DIR_RESOURCE . $iconUri).'" rel="icon">',
                $this->output
            );
        }

        return $this->output;
    }

    /**
     * @param DOMDocument $doc
     * @param array $renderComponents
     */
    protected function prepareOutput(&$doc, &$xpath, $renderComponents)
    {
        foreach ($renderComponents as $cmp) {
            if (in_array($cmp['type'], $this->componentTypes)) {
                $container = $xpath->query("//*[@id='".$cmp['attributes']['id']."']")->item(0);
                if (!$container) {
                    continue;
                }
                $container->nodeValue = 'content'.$cmp['attributes']['id'];
            }
            if ($cmp['components']) {
                $this->prepareOutput($doc, $xpath, $cmp['components']);
            }
        }
    }

    public function processMainContentArea($renderComponents)
    {
        //seek main content area component
        $this->findMainContentComponent((array)$renderComponents);
        if(!$this->mainContent) {
            return;
        }
        $cmp = $this->mainContent;

        if ($cmp['route'] == 'generic') {
            $Router = new ARouter($this->registry);
            $Router->resetRt($this->registry->get('request')->get['rt']);
            $Router->detectController('pages');
            $cmp['route'] = $Router->getController() ? : 'pages/extension/generic';
        }
        if (!$cmp['params']
                && !$this->registry->get('request')->get['product_id']
                && $cmp['route'] == 'pages/product/product'
        ) {
            //in case when layout is for default product page - take a random product id
            $sql = "SELECT product_id 
                    FROM ".$this->registry->get('db')->table('products')." 
                    WHERE date_available <= NOW() AND status=1
                    ORDER BY rand() 
                    LIMIT 1";
            $res = $this->registry->get('db')->query($sql);
            $this->registry->get('request')->get['product_id'] = $res->row['product_id'];
        }

        /** @var ADocument $doc */
        $doc = Registry::getInstance()->get('document');

        $this->callDispatcher($cmp);
        //change Title of Page. take it from main content controller
        $title = $doc ? $doc->getTitle() : '';
        if (!$title) {
            $this->registry->get('log')->write('DEBUG: '.__CLASS__.' Unknown title for page route '.$this->mainRoute);
        }else {
            $this->output = str_replace(
                '<title></title>',
                '<title>'.$title.'</title>',
                $this->output
            );
        }

        //page description
        $keywords = $doc ? $doc->getKeywords() :'';
        if($keywords) {
            $this->output = str_replace(
                '<!--  {{keywords}}-->',
                '<meta name="keywords" content="'.$keywords.'">',
                $this->output
            );
        }
        //page description
        $description = $doc ? $doc->getDescription() :'';
        if($description) {
            $this->output = str_replace(
                '<!--  {{description}}-->',
                '<meta name="description" content="'.$description.'">',
                $this->output
            );
        }

    }

    /**
     * @param array $renderComponents
     *
     * @return void
     */
    protected function findMainContentComponent(array $renderComponents)
    {
        foreach($renderComponents as $cmp){
            if($cmp['type'] == 'abantecart-main-content-area'){
                $this->mainContent = $cmp;
                break;
            }
            if($cmp['components']){
               $this->findMainContentComponent((array)$cmp['components']);
            }
        }
    }

    /**
     * @param array $renderComponents
     */
    protected function processComponents($renderComponents)
    {
        $router = new ARouter($this->registry);
        $router->resetRt();

        foreach ($renderComponents as $cmp) {

            $route = $cmp['route'];
            if($cmp['route'] == $this->mainRoute){
                continue;
            }

            if (in_array($cmp['type'], $this->componentTypes) && $route) {
                $this->callDispatcher($cmp);
            }

            if ($cmp['components']) {
                $this->processComponents($cmp['components']);
            }
        }
    }

    protected function callDispatcher($cmp)
    {
        /** @var ADocument $doc */
        $doc = Registry::getInstance()->get('document');
        $route = $cmp['route'];
        $args = [
            'instance_id' => 0,
            'custom_block_id' => $cmp['custom_block_id'],
        ];

        try {
            $dis = new ADispatcher($route, $args);
            $this->registry->set('PBuilder_interception', $dis->getClass());
            $this->registry->set(
                'PBuilder_block_template',
                $cmp['attributes']['data-gjs-template'] ? : $cmp['attributes']['blockTemplate']
            );
            $result = $dis->dispatchGetOutput();

            $this->registry->set('PBuilder_interception', false);
            $this->registry->set('PBuilder_block_template', '');
            if (!$result) {
                $result = '';
            } //check if block have own scripts and styles
            elseif ($doc) {
                $blockStyles = $doc->getStyles();
                if ($blockStyles) {
                    $this->docStyles += $blockStyles;
                }
                $blockJs = array_merge( $doc->getScripts(), $doc->getScriptsBottom() );
                if ($blockJs) {
                    $this->docJs += $blockJs;
                }
            }
            $this->output = str_replace('content'.$cmp['attributes']['id'], $result, $this->output);
        } catch (Exception $e) {
            Registry::getInstance()->get('log')->write($e->getMessage()."\n".$e->getTraceAsString());
            exit($e->getMessage());
        }
    }
}