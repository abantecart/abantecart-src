<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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

class ExtensionBannerManager extends Extension
{
    private $registry;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    private function _getResourceBanners($resource_id, $language_id = 0)
    {

        if (!$language_id) {
            $language_id = $this->registry->get('language')->getContentLanguageID();
        }

        $sql = "SELECT rm.object_id, 'banners' as object_name, bd.name
                FROM ".$this->registry->get('db')->table("resource_map")." rm
                LEFT JOIN ".$this->registry->get('db')->table("banner_descriptions")." bd ON ( rm.object_id = bd.banner_id AND bd.language_id = '".(int)$language_id."')
                WHERE rm.resource_id = '".(int)$resource_id."'
                    AND rm.object_name = 'banners'";
        $query = $this->registry->get('db')->query($sql);
        $resource_objects = $query->rows;

        $result = array();
        foreach ($resource_objects as $row) {
            $result[] = array(
                'object_id'   => $row['object_id'],
                'object_name' => $row['object_name'],
                'name'        => $row['name'],
                'url'         => $this->registry->get('html')->getSecureURL('extension/banner_manager/edit', '&banner_id='.$row['object_id']),
            );
        }

        return $result;
    }

    public function onControllerResponsesCommonResourceLibrary_InitData()
    {
        $this->baseObject->loadLanguage('banner_manager/banner_manager');
    }

    public function onControllerResponsesCommonResourceLibrary_UpdateData()
    {
        if ($this->baseObject_method == 'main') {
            $resource = &$this->baseObject->data['resource'];
            $result = $this->_getResourceBanners($resource['resource_id'], $resource['language_id']);
            if ($result) {
                $key = $this->registry->get('language')->get('text_banners');
                $key = !$key ? 'banners' : $key;
                $resource['resource_objects'][$key] = $result;
            }
        }
    }

    public function onControllerPagesDesignBlocks_UpdateData()
    {
        $method_name = $this->baseObject_method;
        $that = $this->baseObject;
        if ($method_name != 'main') {
            return null;
        }
        $lm = new ALayoutManager();
        $block = $lm->getBlockByTxtId('banner_block');
        $block_id = $block['block_id'];

        $inserts = $that->view->getData('inserts');
        $inserts[] = array(
            'text' => $that->language->get('text_banner_block'),
            'href' => $that->html->getSecureURL('extension/banner_manager/insert_block', '&block_id='.$block_id),
        );
        $that->view->assign('inserts', $inserts);
    }

    public function onControllerPagesDesignBlocks_InitData()
    {

        $this->baseObject->loadLanguage('banner_manager/banner_manager');
        $method_name = $this->baseObject_method;
        if ($method_name == 'edit') {
            $lm = new ALayoutManager();
            $blocks = $lm->getAllBlocks();
            $block_txt_id = '';
            foreach ($blocks as $block) {
                if ($block['custom_block_id'] == (int)$this->request->get['custom_block_id']) {
                    $block_txt_id = $block['block_txt_id'];
                    break;
                }
            }

            if ($block_txt_id == 'banner_block') {
                redirect($this->html->getSecureURL('extension/banner_manager/edit_block', '&custom_block_id='.(int)$this->request->get['custom_block_id']));
            }
        }
    }

    public function onControllerResponsesCommonTabs_InitData()
    {
        if ($this->baseObject->parent_controller == 'design/blocks') {
            $that = $this->baseObject;
            $lm = new ALayoutManager();
            $that->loadLanguage('banner_manager/banner_manager');
            $that->loadLanguage('design/blocks');
            $block = $lm->getBlockByTxtId('banner_block');
            $block_id = $block['block_id'];
            $that->data['tabs'][] = array(
                'name'       => $block_id,
                'text'       => $that->language->get('text_banner_block'),
                'href'       => $that->html->getSecureURL('extension/banner_manager/insert_block', '&block_id='.$block_id),
                'active'     => ($block_id == $that->request->get['block_id'] ? true : false),
                'sort_order' => 3,
            );
        }
    }

}