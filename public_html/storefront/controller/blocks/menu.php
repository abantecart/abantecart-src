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

class ControllerBlocksMenu extends AController
{
    public $data = array();
    private $menu_items;

    public function main()
    {

        //HTML cache only for non-customer
        if (!$this->customer->isLogged() && !$this->customer->isUnauthCustomer()) {
            if ($this->html_cache()) {
                return;
            }
        }

        $this->loadLanguage('blocks/menu');
        $this->loadLanguage('common/header');

        $this->data['heading_title'] = $this->language->get('heading_title', 'blocks/menu');

        $cache_key = 'storefront_menu.store_'.(int)$this->config->get('config_store_id').'_lang_'.$this->config->get('storefront_language_id');
        $this->menu_items = $this->cache->pull($cache_key);
        if ($this->menu_items === false) {
            $menu = new AMenu_Storefront();
            $this->menu_items = $menu->getMenuItems();

            //writes into cache result of calling _buildMenu func!
            $this->cache->push($cache_key, $this->menu_items);
        }

        //build menu structure after caching. related to http/https urls
        $this->menu_items = $this->_buildMenu('');

        $storefront_menu = $this->menu_items;
        $this->session->data['storefront_menu'] = $storefront_menu;
        $this->data['storemenu'] = $storefront_menu;

        $this->view->batchAssign($this->data);
        $this->processTemplate();
    }

    private function _buildMenu($parent = '')
    {
        $menu = array();
        if (empty($this->menu_items[$parent])) {
            return $menu;
        }
        $lang_id = (int)$this->config->get('storefront_language_id');

        foreach ($this->menu_items[$parent] as $item) {
            if (preg_match("/^http/i", $item ['item_url'])) {
                $href = $item ['item_url'];
            } //process relative url such as ../blog/index.php
            elseif (preg_match("/^\.\.\//i", $item ['item_url'])) {
                $href = str_replace('../', '', $item ['item_url']);
            } else {
                $href = $this->html->getSecureURL($item ['item_url']);
            }
            $menu[] = array(
                'id'         => $item['item_id'],
                'current'    => $item['current'],
                'icon'       => $item['item_icon'],
                'icon_rl_id' => $item['item_icon_rl_id'],
                'href'       => $href,
                'text'       => $item['item_text'][$lang_id],
                'children'   => $this->_buildMenu($item['item_id']),
            );
        }
        return $menu;
    }
}
