<?php

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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerBlocksLanguage extends AController
{
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->data['empty_render_text'] = 'To view content of block you should have at least two enabled languages';
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('blocks/language');

        if ($this->request->is_POST() && isset($this->request->post['language_code'])) {
            $this->session->data['language'] = $this->request->post['language_code'];
            redirect($this->request->post['redirect'] ?? $this->html->getHomeURL());
        }

        $this->data['heading_title'] = $this->language->get('heading_title', 'blocks/language');
        $this->data['language_code'] = $this->session->data['language'];

        $this->data['languages'] = $this->language->getActiveLanguages();

        $get_vars = $this->request->get;
        $unset = ['language'];
        if (isset($get_vars['product_id'])) {
            $unset[] = 'path';
        }

        //build safe redirect URI
        if (!isset($this->request->get['rt'])) {
            $rt = 'index/home';
            $URI = '';
        } else {
            $rt = $this->request->get['rt'];
            $unset[] = 'rt';
            $URI = '&'.$this->html->buildURI($this->request->get, $unset);
            $URI = $URI == '&' ? '' : $URI;
        }
        foreach ($this->data['languages'] as &$lang) {
            $lang['href'] = $this->html->getURL($rt, $URI.'&language='.$lang['code'], true);
        }

        $this->view->batchAssign($this->data);
        $this->processTemplate('blocks/language.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
