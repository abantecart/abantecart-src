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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesIncludesHead extends AController
{
    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('common/header');
        $this->view->assign('template', $this->config->get('config_storefront_template'));
        $this->view->assign('retina', $this->config->get('config_retina_enable'));

        if (HTTPS === true) {
            $this->view->assign('base', HTTPS_SERVER);
            $this->view->assign('ssl', 1);
        } else {
            $this->view->assign('base', HTTP_SERVER);
        }

        $this->view->assign('lang', $this->language->get('code'));
        $this->view->assign('direction', $this->language->get('direction'));
        $this->view->assign('links', $this->document->getLinks());
        $this->view->assign('styles', $this->document->getStyles());
        $this->view->assign('scripts', $this->document->getScripts());
        $this->view->assign('store', $this->config->get('store_name'));

        if ($this->config->get('config_maintenance') && isset($this->session->data['merchant'])) {
            $this->view->assign('maintenance_warning', $this->language->get('text_maintenance_notice'));
        }

        $view = new AView($this->registry, 0);
        $data = array();
        $data['store'] = $this->config->get('store_name');
        $data['logo'] = $this->config->get('config_logo');
        $data['homepage'] = $this->html->getHomeURL();
        $logo_path = DIR_RESOURCE.$data['logo'];

        //see if we have a resource ID instead of path
        if (is_numeric($data['logo'])) {
            $resource = new AResource('image');
            $image_data = $resource->getResource($data['logo']);
            $img_sub_path = $image_data['type_name'].'/'.$image_data['resource_path'];
            if (is_file(DIR_RESOURCE.$img_sub_path)) {
                $data['logo'] = $img_sub_path;
                $logo_path = DIR_RESOURCE.$img_sub_path;
            } else {
                $data['logo'] = $image_data['resource_code'];
            }
        }

        //get logo image dimensions
        $info = get_image_size($logo_path);
        $data['logo_width'] = $info['width'];
        $data['logo_height'] = $info['height'];
        $view->batchAssign($data);
        $this->view->assign('header', $view->fetch('responses/includes/page_header.tpl'));

        $this->processTemplate('responses/includes/head.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }
}