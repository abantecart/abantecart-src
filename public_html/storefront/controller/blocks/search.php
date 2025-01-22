<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2024 Belavier Commerce LLC

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

class ControllerBlocksSearch extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('blocks/search');

        $this->data['heading_title'] = $this->language->get('heading_title', 'blocks/search');

        $this->data['text_advanced'] = $this->language->get('text_advanced');
        $this->data['entry_search'] = $this->language->get('entry_search');
        $this->data['text_category'] = $this->language->get('text_category');
        $this->data['search'] = $this->html->buildElement(
            [
                'type'        => 'input',
                'name'        => 'filter_keyword',
                'value'       => $this->request->get['keyword'] ?? $this->language->get('text_keyword'),
                'placeholder' => $this->language->get('text_keyword'),

            ]
        );

        //load top level categories
        $this->load->model('catalog/category');
        $this->data['top_categories'] = $this->model_catalog_category->getCategories(ROOT_CATEGORY_ID);
        $this->data['button_go'] = $this->language->get('button_go');

        $this->view->batchAssign($this->data);
        $this->processTemplate();
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
