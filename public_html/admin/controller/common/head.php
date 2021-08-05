<?php
/** @noinspection PhpUndefinedClassInspection */

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class  ControllerCommonHead extends AController
{
    public $data = [];

    public function main()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->load->helper('html');
        $this->loadLanguage('common/header');

        $message_link = $this->html->getSecureURL('tool/message_manager');

        $this->data['title'] = $this->document->getTitle();
        $this->data['base'] = (HTTPS_SERVER ? HTTPS_SERVER : HTTP_SERVER);
        $this->data['links'] = $this->document->getLinks();
        $this->data['styles'] = $this->document->getStyles();
        $this->data['scripts'] = $this->document->getScripts();
        $this->data['notifier_updater_url'] = $this->html->getSecureURL('listing_grid/message_grid/getnotifies');
        $this->data['system_checker_url'] = $this->html->getSecureURL('common/common/checksystem');
        $this->data['language_code'] = $this->session->data['language'];
        $this->data['language_details'] = $this->language->getCurrentLanguage();
        $locale = explode('.', $this->data['language_details']['locale']);
        $this->data['language_locale'] = $locale[0];

        $retina = $this->config->get('config_retina_enable');
        $this->data['retina'] = $retina;
        //remove cookie for retina
        if (!$retina && isset($this->request->cookie['HTTP_IS_RETINA'])) {
            $this->request->deleteCookie('HTTP_IS_RETINA');
        }

        $this->data['message_manager_url'] = $message_link;

        if ($this->session->data['checkupdates'] ?? false) {
            $this->data['check_updates_url'] = $this->html->getSecureURL('r/common/common/checkUpdates');
        }
        if (is_numeric($this->config->get('config_icon'))) {
            $r = new AResource('image');
            $resourceInfo = $r->getResource($this->config->get('config_icon'), $this->language->getLanguageID());
            if ($resourceInfo) {
                $this->data['icon'] = $resourceInfo['type_dir'].$resourceInfo['resource_path'];
            }
        } else {
            $this->data['icon'] = $this->config->get('config_icon');
        }

        if (HTTPS === true) {
            $this->data['ssl'] = 1;
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('common/head.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}