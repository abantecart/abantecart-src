<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerCommonMenu extends AController {

	private $menu_items;

	public function main() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('common/header');

		$cache_name = 'storefront_menu';
		$this->menu_items = $this->cache->get($cache_name, $this->config->get('storefront_language_id'));
		if(!$this->menu_items){
			$menu = new AMenu_Storefront();
			$this->menu_items = $menu->getMenuItems();
			$this->cache->set($cache_name, $this->menu_items, $this->config->get('storefront_language_id'));
		}
		$storefront_menu = $this->_buildMenu('');

		$this->view->assign('storemenu', $storefront_menu);
		$this->processTemplate('common/menu.tpl');

		//init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _buildMenu( $parent = '' ) {
		$menu = array();
		if ( empty($this->menu_items[$parent]) ) return $menu;

		$lang_id = $this->config->get('storefront_language_id');
		$logged = $this->customer->isLogged();

		$resource = new AResource('image');

		foreach ( $this->menu_items[$parent] as $item ) {
			if(($logged && $item['item_id']=='login')
				||	(!$logged && $item['item_id']=='logout')){
				continue;
			}
			$href = '';
			if( preg_match ( "/^http/i", $item ['item_url'] ) ){
				$href = $item ['item_url'];
			} else {
				$href = $this->html->getURL ( $item ['item_url'] );
			}
			
			$menu[] = array(
				'id' => $item['item_id'],
				'icon' => $item['item_icon'],
				'href' =>  $href,
				'text' => $item['item_text'][$lang_id],
				'children' => $this->_buildMenu( $item['item_id'] ),
			);
		}

		return $menu;
	}
}
?>