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

class ControllerBlocksHTMLBlock extends AController
{

    public function main($instance_id = 0)
    {
        //disable cache when login display price setting is off or enabled showing of prices with taxes
        if (
            $this->config->get('config_customer_price')
            && !$this->config->get('config_tax')
            && $this->html_cache()
        ) {
            return null;
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $block_data = $this->getBlockContent($instance_id);
        $this->view->assign('block_framed', (int) $block_data['block_framed']);
        $this->view->assign('content', $block_data['content']);
        $this->view->assign('heading_title', $block_data['title']);

        if ($block_data['content']) {
            // need to set wrapper for non products listing blocks
            if ($this->view->isTemplateExists($block_data['block_wrapper'])) {
                $this->view->setTemplate($block_data['block_wrapper']);
            }
            $this->processTemplate();
        }
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getBlockContent($instance_id)
    {
        $block_info = $this->layout->getBlockDetails($instance_id);
        $custom_block_id = $block_info['custom_block_id'];
        $descriptions = $this->layout->getBlockDescriptions($custom_block_id);
        if ($descriptions[$this->config->get('storefront_language_id')]) {
            $key = $this->config->get('storefront_language_id');
        } else {
            $key = key($descriptions);
        }

        return [
            'title'         => $descriptions[$key]['title'],
            'content'       => html_entity_decode($descriptions[$key]['content'], ENT_QUOTES, 'utf-8'),
            'block_wrapper' => $descriptions[$key]['block_wrapper'],
            'block_framed'  => $descriptions[$key]['block_framed'],
        ];
    }
}