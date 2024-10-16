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

class ControllerResponsesDesignPageBuilder extends AController
{
    protected $storageDir, $tmpl_id;
    const DEFAULT_PRESET = 'default_preset.json';

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('page_builder/page_builder');
        $this->storageDir = DIR_PB_TEMPLATES;
        $this->tmpl_id = $this->request->get['tmpl_id'] ?: $this->config->get('config_storefront_template') ?: 'default';
        $this->storageDir .= $this->tmpl_id.DS;
        foreach (
            [
                $this->storageDir,
                $this->storageDir.'savepoints',
                $this->storageDir.'public',
            ] as $dir
        ) {
            if (!is_writable_dir($dir)) {
                if (!is_dir($dir) && mkdir($dir, 0755) !== false) {
                    if (is_writable_dir($dir)) {
                        continue;
                    }
                }
                throw new AException(
                    AC_ERR_CLASS_PROPERTY_NOT_EXIST,
                    sprintf($this->language->get('page_builder_error_storage_permissions'), $dir)
                );
            }
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function main()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $template = $this->request->get['template_id'] ? : $this->request->get['tmpl_id'] ? : 'default';
        $page_id = $this->request->get['page_id'];
        $layout_id = $this->request->get['layout_id'];

        $this->data['storage_url'] = $this->html->getSecureURL(
            'r/design/page_builder/savePage',
            '&page_id='.$page_id.'&layout_id='.$layout_id
        );
        $this->data['load_url'] = $this->html->getSecureURL(
            'r/design/page_builder/loadPage',
            '&page_id='.$page_id.'&layout_id='.$layout_id.'&tmpl_id='.$template
        );
        //URL for logging of javascript errors
        $this->data['loggingUrl'] = $this->html->getSecureURL( 'r/design/page_builder/log' );

        $layout = new ALayoutManager($template, $page_id);
        $pageData = $layout->getPageData();
        if ($page_id && $pageData) {
            $mainContentArea = [
                'type'     => 'abantecart-main-content-area',
                'layout_id'  => $layout_id,
                'page_id'  => $page_id,
                'template' => $template,
                'route'    => $pageData['controller']
            ];
            if($pageData['key_param']){
                $mainContentArea['params'][$pageData['key_param']] = $pageData['key_value'];
            }
            $this->view->assign('mainContentArea', $mainContentArea);
        }

        if ($this->request->get['preset']) {
            $get = $this->request->get;
            $presetFile = DIR_PB_PRESETS.$get['preset'].'.json';
            if (is_file($presetFile)) {
                $pageRoute = $this->getPageRoute($page_id, $layout_id);
                if ($pageRoute) {
                    $counter = $this->getMaxCounter($pageRoute) + 1;
                    copy(
                        $presetFile,
                        $this->storageDir.'savepoints'.DS.$pageRoute.'@'.$counter.'.json'
                    );
                }
            }
            unset($get['rt'],$get['token'],$get['s'],$get['preset']);
            redirect($this->html->getSecureURL($this->request->get['rt'], '&'.http_build_query($get)));
        }

        //blocks list
        $layout = new ALayoutManager();
        $installedBlocks = $layout->getInstalledBlocks();
        $availableBlocks = [];
        foreach ($installedBlocks as $block) {
            $textId = mb_strtoupper($block['block_name'] ?? $block['block_txt_id']);
            if (isset($availableBlocks[$textId])) {
                $availableBlocks[$textId]['templates'][$block['template']] = [
                    'id'   => $block['template'],
                    'name' => $block['template'],
                ];
            } else {
                $availableBlocks[$textId] = [
                    'instance_id'     => $block['instance_id'],
                    'id'              => $block['block_id'].'_'.$block['custom_block_id'],
                    'block_id'        => $block['block_id'],
                    'block_txt_id'    => $block['block_txt_id'],
                    'title'           => $textId,
                    'custom_block_id' => $block['custom_block_id'],
                    'controller'      => $block['controller'],
                    'templates'       => [
                        $block['template'] => [
                            'id'   => $block['template'],
                            'name' => $block['template'],
                        ],
                    ],
                ];
            }

            //custom block tpls grouped by directory name. This name same as block type
            $tpls = glob(DIR_EXT.'/*/storefront/view/*/template/blocks/'.$block['block_txt_id'].'/*.tpl')
                + glob(DIR_STOREFRONT.'view/*/template/blocks/'.$block['block_txt_id'].'/*.tpl');

            foreach ($tpls as $tpl) {
                $pos = strpos($tpl, 'blocks/'.$block['block_txt_id'].'/');
                $tpl = substr($tpl, $pos);
                $availableBlocks[$textId]['templates'][$tpl] = [
                    'id'   => $tpl,
                    'name' => $tpl,
                ];
            }
        }

        $this->data['text_abantecart_blocks'] = $this->language->get('page_builder_text_abantecart_blocks');
        $this->data['text_basic_blocks'] = $this->language->get('page_builder_text_basic_blocks');
        $this->data['text_extra'] = $this->language->get('page_builder_text_extra');
        $this->data['text_forms'] = $this->language->get('page_builder_text_forms');
        $this->data['abc_blocks'] = $availableBlocks;
        $this->data['block_content_url'] = $this->html->getCatalogURL(
            'r/extension/page_builder/getControllerOutput',
            '',
            '',
            true
        );
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/design/proto_page.tpl');

        //use to update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function loadPage()
    {
        $file = '';
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $page_id = $this->request->get['page_id'];
        $layout_id = $this->request->get['layout_id'];
        $pageRoute = $this->getPageRoute($page_id, $layout_id);
        $this->session->data['PB']['current_route'] = $pageRoute;

        $maxCounter = $this->getMaxCounter($pageRoute);
        if($maxCounter !== false) {
            $file = $this->storageDir . 'savepoints' . DS . $pageRoute . '@' . $maxCounter . '.json';
        }
        if (!is_file($file)) {
            //if unsaved page not found - looking for published
            $file = $this->storageDir.'public'.DS.$pageRoute.'.json';
        }

        if (!is_file($file)) {
            //if published page not found - see default preset of core template
            $file = DIR_STOREFRONT.'view'.DS.$this->tmpl_id.DS.self::DEFAULT_PRESET;
        }
        if (!is_file($file)) {
            //if core default preset not found - see default preset of extension template
            $file = DIR_EXT.$this->tmpl_id.DIR_EXT_STORE.'view'.DS.$this->tmpl_id.DS.self::DEFAULT_PRESET;
        }
        if (!is_file($file)) {
            //if no any default preset found - take default preset of pageBuilder
            $file = DIR_EXT.'page_builder'.DS.self::DEFAULT_PRESET;
        }
        $this->data['file'] = $file;
        //use to update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if(!$this->data['file'] || !is_readable($this->data['file'])){
            $errorText = 'Unable to load page.';
            if(!$this->data['file']){
                $errorText .= ' Nothing found.';
            }else{
                $errorText .= ' File '.$this->data['file'].' is not readable.';
            }
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
        //for empty page create savepoint on loading
        if($maxCounter === false){
            copy(
                $this->data['file'],
                $this->storageDir.'savepoints'.DS.$pageRoute.'@1.json'
            );
        }

        $this->response->addJSONHeader();
        $this->response->setOutput(file_get_contents($this->data['file']));
    }

    public function savePage()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $json = file_get_contents('php://input');
        $page_id = $this->request->get['page_id'];
        $layout_id = $this->request->get['layout_id'];
        $pageRoute = $this->getPageRoute($page_id, $layout_id);
        $this->session->data['PB']['current_route'] = $pageRoute;
        $counter = $this->getMaxCounter($pageRoute)+1;
        if ($pageRoute) {
            $res = file_put_contents(
                $this->storageDir.'savepoints'.DS.$pageRoute.'@'.$counter.'.json',
                $json
            );
            if($res === false){
                $errorText = sprintf(
                    $this->language->get('page_builder_error_cannot_save'),
                    $this->storageDir.'savepoints'.DS.$pageRoute
                );

                $err = new AError($errorText);
                $err->toJSONResponse(
                    AC_ERR_REQUIREMENTS,
                    [
                        'error_text'  => $errorText,
                        'reset_value' => true,
                    ]
                );
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getPageRoute($page_id, $layout_id)
    {
        $layout = new ALayoutManager($this->tmpl_id, $page_id, $layout_id);
        $pageData = $layout->getPageData();
        if($pageData['controller'] == 'generic'){
            return 'generic';
        }
        return preformatTextID(
                str_replace('/', '_', $pageData['controller'])
            )
            .'-'.$this->request->get['page_id']
            .'-'.$this->request->get['layout_id'];
    }

    /**
     * method returns current counter for saving snapshot of page for "undo"
     *
     * @param string $fileNameMask
     *
     * @return false|int
     */
    protected function getMaxCounter($fileNameMask)
    {
        if (!$fileNameMask) {
            return false;
        }
        $files = glob($this->storageDir.'savepoints'.DS.$fileNameMask.'*.json');
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
        return $max;
    }

    public function savePreset()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $presetName = $this->request->post['preset_name'];
        if (!$presetName) {
            $errorText = $this->language->get('page_builder_error_empty_preset_name');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }

        $pageRoute = $this->session->data['PB']['current_route'];
        if ($pageRoute) {
            $presetFile = DIR_PB_PRESETS.preg_replace('/[^A-z0-9]/', '_', $presetName).'.json';
            $sourceFile = $this->storageDir
                .'savepoints'.DS
                .$pageRoute.'@'.$this->getMaxCounter($pageRoute).'.json';
            $sourceFile = !is_file($sourceFile) ? $this->storageDir
                .'public'.DS
                .$pageRoute.'.json'
                : $sourceFile;
            //remove content-main-area-block before saving
            //this block can have additional parameters that not allowed on another pages
            if (is_file($sourceFile)) {
                $srcContent = file_get_contents($sourceFile);
                $srcContent = json_decode($srcContent, true);
                $srcContent['gjs-components'] = json_decode($srcContent['gjs-components'], true);
                $divIds = [];
                $srcContent['gjs-components'] = $this->findAndRemoveMainArea($srcContent['gjs-components'], $divIds);

                foreach ($divIds as $id) {
                    $srcContent['gjs-html'] = str_replace('<div id="'.$id.'"></div>', '', $srcContent['gjs-html']);
                }
                $srcContent['gjs-components'] = json_encode($srcContent['gjs-components']);
                file_put_contents($presetFile, json_encode($srcContent));
                $this->extensions->hk_UpdateData($this, __FUNCTION__);
            }
        } else {
            $errorText = $this->language->get('page_builder_error_route_not_found');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
    }

    /**
     * @param array $componentList
     */
    protected function findAndRemoveMainArea($componentList, &$divIds)
    {
        foreach ($componentList as $k => &$cmp) {
            if ($cmp['type'] == 'abantecart-main-content-area') {
                $divIds[] = $cmp['attributes']['id'];
                unset($componentList[$k]);
            }

            if ($cmp['components']) {
                $cmp['components'] = $this->findAndRemoveMainArea($cmp['components'], $divIds);
            }
        }
        return $componentList;
    }

    public function deletePreset()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $presetName = $this->request->post['preset_name'];
        if (!$presetName) {
            $errorText = $this->language->get('page_builder_error_empty_preset_name');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
        $presetFile = DIR_PB_PRESETS.preg_replace('/[^A-z0-9]/', '_', $presetName).'.json';
        if (is_file($presetFile)) {
            if (!unlink($presetFile)) {
                $errorText = sprintf($this->language->get('page_builder_error_preset_permissions'), $presetFile);
                $err = new AError($errorText);
                $err->toJSONResponse(
                    AC_ERR_REQUIREMENTS,
                    [
                        'error_text'  => $errorText,
                        'reset_value' => true,
                    ]
                );
            }
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
        } else {
            $errorText = $this->language->get('page_builder_error_preset_not_found');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
    }

    /**
     * Method removes already saved custom pages
     *
     * @throws AException
     */
    public function removeCustomPage()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $pageRoute = $this->session->data['PB']['current_route'];
        if ($pageRoute) {
            $pageFile = $this->storageDir.'public'.DS.$pageRoute.'.json';
            if (is_file($pageFile)) {
                if (!unlink($pageFile)) {
                    $errorText = sprintf($this->language->get('page_builder_error_remove_page'), $pageFile);
                    $err = new AError($errorText);
                    $err->toJSONResponse(
                        AC_ERR_REQUIREMENTS,
                        [
                            'error_text'  => $errorText,
                            'reset_value' => true,
                        ]
                    );
                    return;
                }
                $this->extensions->hk_UpdateData($this, __FUNCTION__);
            }
            $this->clearSavePoints($pageRoute);
        } else {
            $errorText = $this->language->get('page_builder_error_route_not_found');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
    }

    public function publish()
    {
        $output = $this->language->get('page_builder_error_nothing_to_publish');
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $pageRoute = $this->session->data['PB']['current_route'];
        if ($pageRoute) {
            $publishedFile = $this->storageDir.'public'.DS.$pageRoute.'.json';
            $counter = $this->getMaxCounter($pageRoute);
            if($counter !== false) {
                $savepointFile = $this->storageDir
                    . 'savepoints' . DS
                    . $pageRoute . '@' . $counter . '.json';
                if (!copy($savepointFile, $publishedFile)) {
                    $errorText = sprintf(
                        $this->language->get('page_builder_error_cannot_copy'),
                        $savepointFile,
                        $publishedFile
                    );
                    $err = new AError($errorText);
                    $err->toJSONResponse(
                        AC_ERR_REQUIREMENTS,
                        [
                            'error_text'  => $errorText,
                            'reset_value' => true,
                        ]
                    );
                } //if all fine - remove all save-points for route
                else {
                    $this->clearSavePoints($pageRoute);
                }
                $this->extensions->hk_UpdateData($this, __FUNCTION__);
                $this->response->setOutput(json_encode($output));
            }else{
                $errorText = $this->language->get('page_builder_error_nothing_to_publish');
                $err = new AError($errorText);
                $err->toJSONResponse(
                    AC_ERR_REQUIREMENTS,
                    [
                        'error_text'  => $errorText,
                        'reset_value' => true,
                    ]
                );

            }
        } else {
            $errorText = $this->language->get('page_builder_error_route_not_found');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
    }

    /**
     * Method returns status of publishing of page
     * @throws AException
     */
    public function publishState()
    { 
        $this->data['output'] = [];
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $pageRoute = $this->session->data['PB']['current_route'];

        if ($pageRoute) {
            $publishedFile = $this->storageDir.'public'.DS.$pageRoute.'.json';
            $counter = $this->getMaxCounter($pageRoute);
            if ($counter===false && is_file($publishedFile)) {
                $this->data['output']['published'] = 'true';
            } elseif ($counter === false && !is_file($publishedFile)) {
                $this->data['output']['published'] = 'nodata';
            } else {
                $this->data['output']['counter'] = $counter;
                $this->data['output']['file'] = $publishedFile;
                $this->data['output']['published'] = 'false';
            }
        } else {
            $errorText = $this->language->get('page_builder_error_route_not_found') ;
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
            return;
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->addJSONHeader();
        $this->response->setOutput(json_encode($this->data['output']));
    }

    /**
     * @param string $fileNameMask
     */
    protected function clearSavePoints($fileNameMask)
    {
        $files = glob($this->storageDir.'savepoints'.DS.$fileNameMask.'*.json');
        foreach($files as $filename){
            unlink($filename);
        }
    }

    /**
     * Rollback last changes. this function just delete last savepoint-file
     *
     * @throws AException
     */
    public function undo()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $pageRoute = $this->session->data['PB']['current_route'];
        if ($pageRoute) {
            $counter = $this->getMaxCounter($pageRoute);
            if ( $counter===false || !unlink($this->storageDir.'savepoints'.DS.$pageRoute.'@'.$counter.'.json')) {
                $errorText = $this->language->get('page_builder_error_cannot_undo');
                $err = new AError($errorText);
                $err->toJSONResponse(
                    AC_ERR_REQUIREMENTS,
                    [
                        'error_text'  => $errorText,
                        'reset_value' => true,
                    ]
                );
            }
        } else {
            $errorText = $this->language->get('page_builder_error_route_not_found');
            $err = new AError($errorText);
            $err->toJSONResponse(
                AC_ERR_REQUIREMENTS,
                [
                    'error_text'  => $errorText,
                    'reset_value' => true,
                ]
            );
        }
    }

    public function log(){
        $json = file_get_contents('php://input') ?: $this->request->post;
        $json = $json ? json_decode($json, JSON_PRETTY_PRINT) : '';
        if($json) {
            $this->log->write(
                "PageBuilder JS-error: \n".var_export($json, true)
            );
        }
    }
}