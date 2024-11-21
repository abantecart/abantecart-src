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

class ControllerPagesDesignPageBuilder extends AController
{
    public function main()
    {
        //compare urls to prevent CORS blocking. If store url under ssl - redirect admin to ssl mode too
        $sfUrl = $this->html->getCatalogURL('r/extension/page_builder/getControllerOutput','','',true);
        if(parse_url($sfUrl, PHP_URL_SCHEME) == 'https' && !HTTPS){
            $params = $this->request->get;
            unset($params['rt']);
            redirect(
                'https://'.REAL_HOST.HTTP_DIR_NAME
                .'/?s='.ADMIN_PATH.'&rt=design/page_builder&'.http_build_query($params)
            );
        }

        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('page_builder/page_builder');

        $this->data['page_url'] = $this->html->getSecureURL('p/design/page_builder');
        $this->session->data['content_language_id'] = $this->config->get('storefront_language_id');
        $this->data['tmpl_id'] = $templateTxtId
            = $this->request->get['tmpl_id']
                ?: $this->config->get('config_storefront_template')
                ?: 'default';

        $page_id = (int)$this->request->get['page_id'];
        $layout_id = (int)$this->request->get['layout_id'];
        $layout = new ALayoutManager($templateTxtId, $page_id, $layout_id);
        $allPages = $layout->getAllPages();
        if(!$page_id){
            $page_id = $this->data['pages'][0]['page_id'];
            $layout_id = $this->data['pages'][0]['layout_id'];
            $layout = new ALayoutManager($templateTxtId, $page_id, $layout_id);
        }
        $pageGroups = array_merge( $layout::PAGE_GROUPS, (array)$this->data['page_groups'] );
        $layoutPages = [];
        foreach($allPages as $page){
            $page['url'] = $this->html->getSecureURL(
                'design/layout',
                '&layout_id='.$page['layout_id'].'&page_id='.$page['page_id'].'&tmpl_id='.$templateTxtId
            );
            if(!$page['restricted']){
                $page['delete_url'] = $this->html->getSecureURL(
                    'design/layout/delete',
                    '&layout_id='.$page['layout_id'].'&page_id='.$page['page_id'].'&tmpl_id='.$templateTxtId
                );
            }
            $pageGroup = array_filter(array_keys($pageGroups), function($controller) use ($page){
                return str_starts_with($page['controller'],$controller);
            });
            if($pageGroup){
                $k = current($pageGroup);
                if(!$layoutPages[$k]){
                    $layoutPages[$k] = [
                        'id' => 'dp'.preformatTextID($k),
                        'name' => $pageGroups[$k],
                        'layout_name' => $pageGroups[$k],
                        'restricted' => true
                    ];
                }
                $layoutPages[$k]['children'][] = $page;
            }else{
                $layoutPages[] = $page;
            }
        }
        $this->data['pages'] = $layoutPages;


        $this->data['current_page'] = $layout->getPageData();
        $params = [
            'page_id'   => $page_id,
            'layout_id' => $layout->getLayoutId(),
            'tmpl_id'   => $layout->getTemplateId(),
        ];
        $this->data['page_id'] = $params['page_id'];
        $this->data['layout_id'] = $params['layout_id'];

        $this->data['proto_page_url'] = $this->html->getSecureURL(
                    'r/design/page_builder',
                    '&'.http_build_query($params)
                );
        $this->document->setTitle($this->language->get('page_builder_name'));
        // breadcrumb path
        $this->document->initBreadcrumb(
            [
                'href' => $this->html->getSecureURL('index/home'),
                'text' => $this->language->get('text_home'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL('design/page_builder'),
                'text'    => $this->language->get('page_builder_name').' - '.$params['tmpl_id'],
                'current' => true,
            ]
        );

        try {
            checkPBDirs($templateTxtId);
        }catch(AException $e){
            $this->view->assign('error_warning', $e->getMessage());
            $this->processTemplate('pages/design/page_builder.tpl');
            return;
        }

        // get templates
        $directories = glob(DIR_STOREFRONT.'view/*', GLOB_ONLYDIR);
        $this->data['templates'] = array_map('basename', $directories);

        $enabled_templates = $this->extensions->getExtensionsList(
            [
                  'filter' => 'template',
                  'status' => 1,
            ]
        );

        $this->data['templates'] = array_merge(
            $this->data['templates'],
            array_column($enabled_templates->rows,'key')
        );

        $form = new AForm('ST');
        $form->setForm(
            [
                'form_name' => 'presetFrm',
            ]
        );
        $this->data['form']['id'] = 'presetFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'presetFrm',
                'action' => $this->html->getSecureURL('r/design/page_builder/preset'),
                'attr'   => 'class="aform form-inline"',
            ]
        );

        $this->data['button_undo_title'] = $this->language->get('page_builder_button_undo_title');
        $this->data['button_undo'] = $this->language->get('page_builder_button_undo');
        $this->data['undo_confirm_text'] = $this->language->get('page_builder_button_undo_confirm_text');
        $this->data['undo_success_text'] = $this->language->get('page_builder_undo_success_text');
        $this->data['undo_url'] = $this->html->getSecureURL(
            'r/design/page_builder/undo',
            '&'.http_build_query($params)
        );

        $this->data['button_publish_title'] = $this->language->get('page_builder_button_publish_title');
        $this->data['button_publish'] = $this->language->get('page_builder_button_publish');
        $this->data['publish_success_text'] = $this->language->get('page_builder_button_publish_success');
        $this->data['publish_url'] = $this->html->getSecureURL(
            'r/design/page_builder/publish',
            '&'.http_build_query($params)
        );
        $this->data['publish_state_url'] = $this->html->getSecureURL(
            'r/design/page_builder/publishState',
            '&'.http_build_query($params)
        );

        $prvId  = $this->session->data['PB']['previewId'] ?: genToken();
        if(!$this->session->data['PB']['previewId']){
            $this->session->data['PB']['previewId'] = $prvId;
        }
        $pageData = $layout->getPageData();

        if($pageData['key_param'] && $pageData['key_value']){
            $params[$pageData['key_param']] = $pageData['key_value'];
        }
        $params['pb'] = $prvId;

        $previewRoute = $pageData['controller'] == 'generic'
                    ? 'extension/generic'
                    : str_replace('pages/','',$pageData['controller']);
        $this->data['previewUrl'] = $this->html->getCatalogURL(
            $previewRoute,
            '&'.http_build_query($params),
            '',
            true
        );

        $this->data['button_remove_custom_page_title'] = $this->language->get(
            'page_builder_button_remove_custom_page_title'
        );
        $this->data['button_remove_custom_page'] = $this->language->get(
            'page_builder_button_remove_custom_page'
        );
        $this->data['button_remove_custom_page_confirm_text'] = $this->language->get(
            'page_builder_button_remove_custom_page_confirm_text'
        );
        $this->data['remove_custom_page_success_text'] = $this->language->get(
            'page_builder_remove_custom_page_success_text'
        );

        $this->data['remove_custom_page_url'] = $this->html->getSecureURL(
            'r/design/page_builder/removeCustomPage',
            '&'.http_build_query($params)
        );

        $this->preparePresets();
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/design/page_builder.tpl');

        //use to update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function preparePresets(){
        $this->data['preset_list'] = ['' => $this->language->get('page_builder_text_select_preset')];
        foreach(glob(DIR_PB_TEMPLATES.'presets'.DS.$this->data['tmpl_id'].DS.'*.json') as $file){
            $file = pathinfo($file, PATHINFO_FILENAME);
            $this->data['preset_list'][$file] = $file;
        }
        if($this->data['preset_list']){
            $this->data['button_load_preset'] = $this->language->get('page_builder_button_load_preset');
            $this->data['page_builder_text_load_preset_confirm_text'] = $this->language->get(
                'page_builder_text_load_preset_confirm_text'
            );
            $this->data['page_builder_save_preset_success_text'] = $this->language->get(
                'page_builder_save_preset_success_text'
            );
            $this->data['page_builder_remove_preset_success_text'] = $this->language->get(
                'page_builder_remove_preset_success_text'
            );
        }
        $this->data['text_preset'] = $this->language->get('page_builder_text_preset');
        $this->data['text_prompt'] = $this->language->get('page_builder_text_prompt');
        $this->data['text_ask_save'] = $this->language->get(
            'page_builder_save_preset_confirm_text'
        );
        $this->data['save_preset_url'] = $this->html->getSecureURL(
            'r/design/page_builder/savePreset',
            '&tmpl_id='.$this->data['tmpl_id']
        );

        $this->data['delete_preset_confirm_text'] = $this->language->get('page_builder_text_delete_preset_confirm_text');
        $this->data['delete_preset_url'] = $this->html->getSecureURL('r/design/page_builder/deletePreset');
    }
}