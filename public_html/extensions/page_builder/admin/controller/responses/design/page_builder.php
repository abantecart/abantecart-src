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
    protected $templateTxtId, $pageId, $layoutId, $route;
    const DEFAULT_PRESET = 'default_preset.json';

    /**
     * @param Registry $registry
     * @param int $instance_id
     * @param string $controller
     * @param string|null $parent_controller
     * @throws AException
     */
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);

        $this->loadLanguage('page_builder/page_builder');
        $this->templateTxtId = $this->request->get['template_id']
            ?: $this->request->get['tmpl_id']
                ?: $this->config->get('config_storefront_template')
                    ?: 'default';
        $this->templateTxtId = preformatTextID($this->templateTxtId);
        $this->pageId = (int)$this->request->get['page_id'];
        $this->layoutId = (int)$this->request->get['layout_id'];

        checkPBDirs($this->templateTxtId);

        $layout = new ALayoutManager($this->templateTxtId, $this->pageId, $this->layoutId);
        $pageData = $layout->getPageData();
        if ($pageData['controller'] == 'generic') {
            $this->route = 'generic';
        } else {
            $this->route = preformatTextID(str_replace('/', '_', $pageData['controller']))
                . '-' . $this->pageId
                . '-' . $this->layoutId;
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function main()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $httpQuery = [
            'tmpl_id'   => $this->templateTxtId,
            'page_id'   => $this->pageId,
            'layout_id' => $this->layoutId
        ];
        $this->data['storage_url'] = $this->html->getSecureURL(
            'r/design/page_builder/savePage',
            '&' . http_build_query($httpQuery)
        );
        $this->data['load_url'] = $this->html->getSecureURL(
            'r/design/page_builder/loadPage',
            '&' . http_build_query($httpQuery)
        );
        //URL for logging of javascript errors
        $this->data['loggingUrl'] = $this->html->getSecureURL('r/design/page_builder/log');

        $layout = new ALayoutManager($this->templateTxtId, $this->pageId);
        $pageData = $layout->getPageData();
        if ($pageData) {
            $mainContentArea = [
                'type'      => 'abantecart-main-content-area',
                'layout_id' => $this->layoutId,
                'page_id'   => $this->pageId,
                'template'  => $this->templateTxtId,
                'route'     => $pageData['controller']
            ];
            if ($pageData['key_param']) {
                $mainContentArea['params'][$pageData['key_param']] = $pageData['key_value'];
            }
            $this->view->assign('mainContentArea', $mainContentArea);
        }

        if ($this->request->get['preset']) {
            $get = $this->request->get;
            $presetFile = DIR_PB_TEMPLATES . 'presets' . DS . $this->templateTxtId . DS . $get['preset'] . '.json';
            if (is_file($presetFile)) {
                $this->preparePreset($presetFile);
                if ($this->route) {
                    //save loaded preset as new savepoint
                    $counter = $this->getMaxCounter($this->route) + 1;
                    $savePointFile = DIR_PB_TEMPLATES . 'savepoints' . DS
                        . $this->templateTxtId . DS . $this->route . '@' . $counter . '.json';
                    copy($presetFile, $savePointFile);
                }
            }
            unset($get['rt'], $get['token'], $get['s'], $get['preset']);
            redirect($this->html->getSecureURL($this->request->get['rt'], '&' . http_build_query($get)));
        }

        //Build AbanteCart blocks list
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
                    'id'              => $block['block_id'] . '_' . $block['custom_block_id'],
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
            $tpls = glob(DIR_EXT . '/*/storefront/view/*/template/blocks/' . $block['block_txt_id'] . '/*.tpl')
                + glob(DIR_STOREFRONT . 'view/*/template/blocks/' . $block['block_txt_id'] . '/*.tpl');

            foreach ($tpls as $tpl) {
                $pos = strpos($tpl, 'blocks/' . $block['block_txt_id'] . '/');
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
        $published = false;
        $file = '';
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $maxCounter = $this->getMaxCounter($this->route);
        if ($maxCounter !== false) {
            $file = DIR_PB_TEMPLATES . 'savepoints' . DS
                . $this->templateTxtId . DS . $this->route . '@' . $maxCounter . '.json';
        }
        if (!is_file($file)) {
            //if unsaved page not found - looking for published
            $file = DIR_PB_TEMPLATES . 'public' . DS . $this->templateTxtId . DS . $this->route . '.json';
            if (is_file($file)) {
                $published = true;
            }
        }

        if (!is_file($file)) {
            //if published page not found - see default preset of core template
            $file = DIR_STOREFRONT . 'view' . DS . $this->templateTxtId . DS . self::DEFAULT_PRESET;
            //before load need to replace custom_block_ids parameters for all custom blocks
            // because of dynamic value after xml-import of layout.xml
            if (is_file($file)) {
                $file = $this->preparePreset($file);
            }
        }
        if (!is_file($file)) {
            //if core default preset not found - see default preset of extension template
            $file = DIR_EXT . $this->templateTxtId . DIR_EXT_STORE
                . 'view' . DS . $this->templateTxtId . DS . self::DEFAULT_PRESET;
            //before load need to replace custom_block_ids parameters for all custom blocks
            // because of dynamic value after xml-import of layout.xml
            if (is_file($file)) {
                $file = $this->preparePreset($file);
            }
        }
        if (!is_file($file)) {
            //if no any default preset found - take default preset of pageBuilder
            $file = DIR_EXT . 'page_builder' . DS . self::DEFAULT_PRESET;
            //before load need to replace custom_block_ids parameters for all custom blocks
            // because of dynamic value after xml-import of layout.xml
            if (is_file($file)) {
                $file = $this->preparePreset($file);
            }
        }
        $this->data['file'] = $file;
        //use to update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if (!$this->data['file'] || !is_readable($this->data['file'])) {
            $errorText = 'Unable to load page.';
            if (!$this->data['file']) {
                $errorText .= ' Nothing found.';
            } else {
                $errorText .= ' File ' . $this->data['file'] . ' is not readable.';
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
        //for new page create savepoint on loading if not published earlier
        if ($maxCounter === false && !$published) {
            copy(
                $this->data['file'],
                DIR_PB_TEMPLATES . 'savepoints' . DS . $this->templateTxtId . DS . $this->route . '@1.json'
            );
        }

        $this->response->addJSONHeader();
        $this->response->setOutput(file_get_contents($this->data['file']));
    }

    /**
     * @param string $file
     * @return string
     * @throws AException
     */
    protected function preparePreset(string $file)
    {
        $newFile = str_replace('.json', '_prepared.json', $file);
        if (is_file($newFile) && is_readable($newFile) && filesize($newFile) > 0) {
            return $newFile;
        }
        $presetData = json_decode(file_get_contents($file), true);
        $componentInfo = $presetData['pages'][0]['frames'][0]['component']['components'];
        $componentInfo = $this->processComponents($componentInfo);
        $presetData['pages'][0]['frames'][0]['component']['components'] = $componentInfo;
        if (str_ends_with($file, DS . self::DEFAULT_PRESET)) {
            $newFile = str_replace('.json', '_prepared.json', $file);
        } else {
            //overwrite preset file if it's not default
            $newFile = $file;
        }
        file_put_contents($newFile, json_encode($presetData, JSON_PRETTY_PRINT));
        return $newFile;
    }

    /**
     * @param $renderComponents
     * @return array
     * @throws AException
     */
    protected function processComponents($renderComponents)
    {
        foreach ($renderComponents as &$cmp) {
            if ($cmp['attributes']['data-gjs-custom_block_id']) {
                $sql = "SELECT * 
                        FROM " . $this->db->table('block_descriptions') . " 
                        WHERE name='" . $this->db->escape(trim($cmp['attributes']['data-gjs-custom-name'])) . "'";
                $result = $this->db->query($sql);
                if ($result->row) {
                    $cmp['custom_block_id']
                        = $cmp['attributes']['data-gjs-custom_block_id']
                        = $result->row['custom_block_id'];
                }
            }

            if ($cmp['components']) {
                $cmp['components'] = $this->processComponents($cmp['components']);
            }
        }
        return $renderComponents;
    }

    public function savePage()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $json = file_get_contents('php://input');
        $counter = $this->getMaxCounter($this->route) + 1;
        if ($this->route) {
            $res = file_put_contents(
                DIR_PB_TEMPLATES . 'savepoints' . DS . $this->templateTxtId . DS . $this->route . '@' . $counter . '.json',
                $json
            );
            if ($res === false) {
                $errorText = sprintf(
                    $this->language->get('page_builder_error_cannot_save'),
                    DIR_PB_TEMPLATES . 'savepoints' . DS . $this->templateTxtId . DS . $this->route
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
        $files = glob(DIR_PB_TEMPLATES . 'savepoints' . DS . $this->templateTxtId . DS . $fileNameMask . '*.json');
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
        return $max;
    }

    public function savePreset()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $presetName = preformatTextID($this->request->post['preset_name'], "_");
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

        if ($this->route) {
            $presetFile = DIR_PB_TEMPLATES . 'presets' . DS . $this->templateTxtId . DS . $presetName . '.json';
            $sourceFile = DIR_PB_TEMPLATES
                . 'savepoints' . DS
                . $this->templateTxtId . DS
                . $this->route
                . '@' . $this->getMaxCounter($this->route) . '.json';
            $sourceFile = !is_file($sourceFile)
                ? DIR_PB_TEMPLATES . 'public' . DS . $this->templateTxtId . DS . $this->route . '.json'
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
                    $srcContent['gjs-html'] = str_replace('<div id="' . $id . '"></div>', '', $srcContent['gjs-html']);
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
        $presetName = preformatTextID($this->request->post['preset_name'], "_");
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
        $presetFile = DIR_PB_TEMPLATES . 'presets' . DS . $this->templateTxtId . DS . $presetName . '.json';
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
        if ($this->route) {
            $pageFile = DIR_PB_TEMPLATES . 'public' . DS . $this->templateTxtId . DS . $this->route . '.json';
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
            $this->clearSavePoints($this->route);
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
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if ($this->route) {
            $publishedFile = DIR_PB_TEMPLATES . 'public' . DS . $this->templateTxtId . DS . $this->route . '.json';
            $counter = $this->getMaxCounter($this->route);
            if ($counter !== false) {
                $savepointFile = DIR_PB_TEMPLATES . 'savepoints' . DS
                    . $this->templateTxtId . DS . $this->route . '@' . $counter . '.json';
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
                    $this->clearSavePoints($this->route);
                }
                $this->data['output'] = [
                    'file'      => $publishedFile,
                    'published' => 'true'
                ];
                $this->extensions->hk_UpdateData($this, __FUNCTION__);
                $this->response->setOutput(json_encode($this->data['output']));
            } else {
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

        if ($this->route) {
            $publishedFile = DIR_PB_TEMPLATES . 'public' . DS . $this->templateTxtId . DS . $this->route . '.json';
            $counter = $this->getMaxCounter($this->route);
            if ($counter === false && is_file($publishedFile)) {
                $this->data['output']['published'] = 'true';
            } elseif ($counter === false && !is_file($publishedFile)) {
                $this->data['output']['published'] = 'nodata';
            } else {
                $this->data['output']['counter'] = $counter;
                $this->data['output']['file'] = $publishedFile;
                $this->data['output']['published'] = 'false';
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
        $files = glob(DIR_PB_TEMPLATES . 'savepoints' . DS . $this->templateTxtId . DS . $fileNameMask . '*.json');
        foreach ($files as $filename) {
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
        if ($this->route) {
            $counter = $this->getMaxCounter($this->route);
            if ($counter === false
                || !unlink(
                    DIR_PB_TEMPLATES . 'savepoints' . DS . $this->templateTxtId . DS . $this->route . '@' . $counter . '.json'
                )
            ) {
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

    public function log()
    {
        $json = file_get_contents('php://input') ?: $this->request->post;
        $json = $json ? json_decode($json, JSON_PRETTY_PRINT) : '';
        if ($json) {
            $this->log->write(
                "PageBuilder JS-error: \n" . var_export($json, true)
            );
        }
    }

    public function createNewPage()
    {
        $templateTxtId = $this->request->get['tmpl_id'];
        $pageData = [
            'controller' => $this->request->get['controller'],
            'key_param'  => $this->request->get['key_param'],
            'key_value'  => (int)$this->request->get['key_value'],
        ];

        $this->loadLanguage('catalog/product');
        if (!$pageData['key_value']) {
            unset($this->session->data['success']);
            $this->session->data['warning'] = $this->language->get('error_product_not_found');
            redirect($this->html->getSecureURL('catalog/product/update'));
        }

        $result = $this->getPageDescriptionByParams(
            $pageData['controller'],
            $pageData['key_param'],
            $pageData['key_value']
        );
        $layoutData = $result['layout_data'];
        $pageData['page_descriptions'] = $result['page_descriptions'];

        $result = saveOrCreateLayout($templateTxtId, $pageData, $layoutData);

        if ($result) {
            redirect(
                $this->html->getSecureURL(
                    'design/page_builder',
                    '&' . http_build_query(
                        [
                            'page_id'   => $result['page_id'],
                            'layout_id' => $result['layout_id'],
                            'tmpl_id'   => $templateTxtId
                        ]
                    )

                )
            );
        } else {
            redirect($this->request->server['HTTP_REFERER']);
        }
    }

    protected function getPageDescriptionByParams(string $controller, string $keyParam, int $keyValue)
    {
        $output = [];
        $lm = new ALayoutManager();

        if ($keyParam == 'product_id') {
            /** @var ModelCatalogProduct $mdl */
            $mdl = $this->loadModel('catalog/product');
            $productInfo = $mdl->getProductDescriptions($keyValue);
            if ($productInfo) {
                $srcIds = $lm->getPageLayoutIDs($controller, '', '', true);
                $output['layout_data']['source_layout_id'] = $srcIds['layout_id'];
                $output['layout_data']['layout_name'] = $this->language->get('text_product', 'catalog/product')
                    . ': ' . $productInfo[$this->language->getContentLanguageID()]['name'];
                $output['page_descriptions'] = $productInfo;
            }
        } elseif ($keyParam == 'path') {
            /** @var ModelCatalogCategory $mdl */
            $mdl = $this->loadModel('catalog/category');
            $categoryInfo = $mdl->getCategoryDescriptions($keyValue);
            if ($categoryInfo) {
                $srcIds = $lm->getPageLayoutIDs($controller, '', '', true);
                $output['layout_data']['source_layout_id'] = $srcIds['layout_id'];
                $output['layout_data']['layout_name'] = $this->language->get('text_category', 'catalog/category')
                    . ': '
                    . $categoryInfo[$this->language->getContentLanguageID()]['name'];
                $output['page_descriptions'] = $categoryInfo;
            }
        } elseif ($keyParam == 'manufacturer_id') {
            /** @var ModelCatalogManufacturer $mdl */
            $mdl = $this->loadModel('catalog/manufacturer');
            $manufacturerInfo = $mdl->getManufacturer($keyValue);
            if ($manufacturerInfo) {
                $srcIds = $lm->getPageLayoutIDs($controller, '', '', true);
                $output['layout_data']['source_layout_id'] = $srcIds['layout_id'];
                $output['layout_data']['layout_name'] = $this->language->get('text_manufacturer', 'catalog/manufacturer')
                    . ': '
                    . $manufacturerInfo['name'];
                $output['page_descriptions'] = $manufacturerInfo;
            }
        } elseif ($keyParam == 'content_id') {
            $acm = new AContentManager();
            $languageId = $this->language->getDefaultLanguageID();
            $content_info = $acm->getContent($keyValue, $languageId);
            if ($content_info) {
                $srcIds = $lm->getPageLayoutIDs($controller, '', '', true);
                $output['layout_data']['source_layout_id'] = $srcIds['layout_id'];

                $title = $content_info['title'] ?: 'Unnamed content page';
                $output['layout_data']['layout_name'] = $this->language->get('text_content', 'common/header')
                    . ': '
                    . $title;
                $output['page_descriptions'][$languageId]['name'] = $title;
            }
        } elseif ($keyParam == 'collection_id') {
            /** @var ModelCatalogCollection $mdl */
            $mdl = $this->loadModel('catalog/collection');
            $collectionInfo = $mdl->getById($keyValue);
            if ($collectionInfo) {
                $srcIds = $lm->getPageLayoutIDs($controller, '', '', true);
                if(!$srcIds){
                    $srcIds = $lm->getPageLayoutIDs('generic', '', '', true);
                }
                $output['layout_data']['source_layout_id'] = $srcIds['layout_id'];
                $output['layout_data']['layout_name'] = $this->language->get('text_collection', 'catalog/collections')
                    . ': '
                    . $collectionInfo['name'];
                $output['page_descriptions'] = $collectionInfo;
            }
        }
        return $output;
    }
}