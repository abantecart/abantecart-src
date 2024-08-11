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

/**
 * Class ControllerPagesToolUpdater
 *
 * @property  ModelToolMPAPI $model_tool_mp_api
 */
class ControllerPagesToolUpdater extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('tool/updater');
        //refresh data from MP-server.
        $this->model_tool_updater->check4Updates(true);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
            'href'      => $this->html->getSecureURL('tool/updater'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
            ]
        );

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_no_results'] = $this->language->get('text_no_results');
        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_type'] = $this->language->get('column_type');
        $this->data['column_category'] = $this->language->get('column_category');
        $this->data['column_status'] = $this->language->get('column_status');
        $this->data['column_action'] = $this->language->get('column_action');
        $this->data['column_version'] = $this->language->get('column_version');
        $this->data['column_new_version'] = $this->language->get('column_new_version');
        $this->data['error_warning'] = null;
        $this->data['text_nothing_todo'] = '';

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = is_array($this->session->data['error'])
                ? implode("</br>", $this->session->data['error'])
                : $this->session->data['error'];
            unset($this->session->data['error']);
        }

        $this->data['extensions'] = [];
        $mp_token = $this->config->get('mp_token');
        if (!$mp_token) {
            $this->data['mp_connected'] = false;
            $this->loadModel('tool/mp_api');

            $this->data['text_marketplace_connect'] = $this->language->get('text_marketplace_connect');
            $this->data['text_connect'] = $this->language->get('text_connect');
            $this->data['text_please_connect'] = $this->language->get('text_marketplace_connect_your_store');
            $this->data['amp_disconnect_url'] = $this->html->getSecureURL('tool/extensions_store/disconnect');
        } else {
            $this->data['mp_connected'] = true;
        }

        if ($this->data['mp_connected']) {
            $updates = $this->session->data['extensions_updates'];
            $this->data['extensions'] = [];
            if (!empty($updates) && is_array($updates)) {
                foreach ($updates as $key => $version_info) {
                    $ext_info = $this->extensions->getExtensionInfo($key);

                    $current_version = $ext_info['version'];
                    $new_version = $version_info['version'];

                    //skip old or current versions
                    if (versionCompare($current_version, $new_version, '>=')) {
                        continue;
                    }

                    //skip versions that not supported by current cart version
                    if (!in_array(VERSION, $version_info['cart_versions'])) {
                        continue;
                    }

                    $this->data['extensions'][$key]['installed_version'] = $current_version;
                    $this->data['extensions'][$key]['new_version'] = $new_version;
                    $this->data['extensions'][$key]['type'] = $ext_info['type'];
                    $this->data['extensions'][$key]['category'] = $ext_info['category'];
                    $this->data['extensions'][$key]['status'] = $this->html->buildElement(
                        [
                            'type'  => 'checkbox',
                            'id'    => $key.'_status',
                            'name'  => $key.'_status',
                            'value' => $ext_info['status'],
                            'style' => 'btn_switch btn-group-xs disabled',
                            'attr'  => 'readonly="true" data-edit-url="'.$this->html->getSecureURL('extension/extensions/edit', '&extension='.$key).'"',
                        ]
                    );

                    if ($version_info['installation_key']) {
                        $this->data['extensions'][$key]['install_url'] = $this->html->getSecureURL('tool/package_installer', '&extension_key='.$version_info['installation_key']);
                    } else {
                        $this->data['extensions'][$key]['install_url'] = $version_info['url'];
                    }
                    $this->data['extensions'][$key]['name'] = $this->extensions->getExtensionName($key);

                }
            }

            if (!$this->data['extensions']) {
                $this->data['text_nothing_todo'] = $this->language->get('text_nothing_todo');
            }
        }
        $this->data['info'] = $this->language->get('text_note');

        $this->view->assign('help_url', $this->gen_help_url());
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/tool/updater.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}