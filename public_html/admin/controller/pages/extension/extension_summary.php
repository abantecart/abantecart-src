<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerPagesExtensionExtensionSummary
 *
 * @property ModelToolMPAPI $model_tool_mp_api
 */
class ControllerPagesExtensionExtensionSummary extends AController
{
    public $data = [];

    public function main()
    {
        $this->loadModel('tool/mp_api');
        //Load input arguments for gid settings
        $this->data = func_get_arg(0);
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('extension/extensions');
        $extension = $this->request->get['extension'];
        if ($extension && !$this->data['extension_info']) {
            $this->data['extension_info'] = $this->extensions->getExtensionInfo($extension);
        }

        $icon_ext_img_url = HTTPS_EXT.$extension.'/image/icon.png';
        $icon_ext_dir = DIR_EXT.$extension.'/image/icon.png';
        $icon = (is_file($icon_ext_dir) ? $icon_ext_img_url : RDIR_TEMPLATE.'image/default_extension.png');

        $this->data['extension_info']['icon'] = $icon;
        $this->data['extension_info']['name'] = $this->language->get($extension.'_name');

        $datetime_format = $this->language->get('date_format_short').' '.$this->language->get('time_format');

        if ($this->data['extension_info']['date_installed']) {
            $this->data['extension_info']['installed'] =
                dateISO2Display(
                    $this->data['extension_info']['date_installed'],
                    $datetime_format
                );
        }
        if ($this->data['extension_info']['date_added']) {
            $this->data['extension_info']['date_added'] =
                dateISO2Display(
                    $this->data['extension_info']['date_added'],
                    $datetime_format
                );
        }
        $updates = $this->session->data['extensions_updates'];

        // if update available
        if (is_array($updates) && isset($updates[$extension])) {
            //show button for upgrading when version greater than current
            if (version_compare($updates[$extension]['version'], $this->data['extension_info']['version'], '>')) {
                if ($updates[$extension]['installation_key']) {
                    $update_now_url = $this->html->getSecureURL(
                        'tool/package_installer', '&extension_key='.$updates[$extension]['installation_key']
                    );
                } else {
                    $update_now_url = $updates[$extension]['url'];
                }
                $this->data['upgrade_button'] = $this->html->buildElement(
                    [
                        'type' => 'button',
                        'name' => 'btn_upgrade',
                        'id'   => 'upgradenow',
                        'href' => $update_now_url,
                        'text' => $this->language->get('button_upgrade'),
                    ]
                );
            }
        }

        $mpProductUrl = $expires = '';
        if ($this->data['extension_info']['license_key']) {
            if($updates && isset($updates[$extension]['support_expiration'])){
                $expires = $this->data['extension_info']['support_expiration'];
            }
            if(!$expires && $this->data['extension_info']['support_expiration']){
                $expires = $this->data['extension_info']['support_expiration'];
            }
            if ($expires) {
                if(dateISO2Int($expires) < time()){
                    $mpProductUrl = $this->data['extension_info']['mp_product_url'];
                }
                $this->data['extension_info']['support_expiration'] = dateISO2Display(
                    $expires,
                    $this->language->get('date_format_short')
                );
                $this->data['text_support_expiration'] = $this->language->get('text_support_expiration');
            }
            if(!$mpProductUrl){
                $mpProductUrl = $this->model_tool_mp_api->getMPURL().$extension.'/support';
            }

            //if license_key presents - show support button
            $this->data['get_support_button'] = $this->html->buildElement(
                [
                    'type'   => 'button',
                    'name'   => 'btn_get_support',
                    'id'     => 'getsupportnow',
                    'target' => "_new",
                    'href'   => $mpProductUrl,
                    'text'   => $this->language->get('button_get_support'),
                ]
            );
        }


        $this->data['extension_info']['license'] = $this->data['extension_info']['license_key'];
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/extension/extension_summary.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}