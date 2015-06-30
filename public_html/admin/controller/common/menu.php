<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

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
class ControllerCommonMenu extends AController {

	public $data = array();

	public function main() {

		$this->loadLanguage('common/header');

		$menu = new ADataset ('menu', 'admin');
		$this->data['menu_items'] = $menu->getRows();

		//use to update data before render
		$this->extensions->hk_ProcessData($this);

		// need to resort by sort_order property and exlude disabled extension items
		$enabled_extension = $this->extensions->getEnabledExtensions();

		$offset = 0; // it needs for process repeating order numbers
		$tmp = array();
		foreach ($this->data['menu_items'] as $i => $item) {
			if ($i > 0) {
				if ($this->data['menu_items'][ $i - 1 ]['parent_id'] != $item ['parent_id']) {
					$offset = 0;
				}
			}
			//checks for disabled extension
			if ($item ['item_type'] == 'extension') {

				// looks for this name in enabled extensions list. if is not there - skip it
				if (!$this->_find_itemId_in_extensions($item ['item_id'], $enabled_extension)) {
					continue;
				} else { // if all fine - loads language of extension for menu item text show
					if (strpos($item ['item_url'], 'http') === false) {
						$this->loadLanguage($item ['item_id'] . '/' . $item ['item_id'], 'silent');
						$item['language'] = $item ['item_id'] . '/' . $item ['item_id'];
					}
				}
			}
			if (isset ($tmp [ $item ['parent_id'] ] [ $item ['sort_order'] ])) {
				$offset++;
			}

			$tmp [ $item ['parent_id'] ] [ $item ['sort_order'] + $offset ] = $item;
		}
		$this->data['menu_items'] = array();
		foreach ($tmp as $item) {
			ksort($item);
			$this->data['menu_items'] = array_merge($this->data['menu_items'], $item);
		}
		unset ($tmp);


		$this->view->assign('menu_html',  renderAdminMenu(
										$this->_buildMenuArray($this->data['menu_items']),
										0, 
										$this->request->get_or_post('rt') )
							);
		$this->processTemplate('common/menu.tpl');
		//use to update data before render
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _find_itemId_in_extensions($item_id, $extension_list) {
		if (in_array($item_id, $extension_list)) return true;
		foreach ($extension_list as $ext_id) {
			$pos = strpos($item_id, $ext_id);
			if ($pos === 0 && substr($item_id, strlen($ext_id), 1) == '_') {
				return true;
			}
		}
		return false;
	}

	private function _buildMenuArray($menu_items = array()) {
		$dashboard = array(
			'dashboard' => array(
				'icon' => '<i class="fa fa-home"></i>',
				'id' => 'dashboard',
				'rt' => 'index/home',
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_dashboard'),
			)
		);
		return array_merge($dashboard, $this->_getChildItems('', $menu_items));

	}

	private function _getChildItems($item_id, $menu_items) {
		$rm = new AResourceManager();
		$rm->setType('image');
		$result = array();
		foreach ($menu_items as $item) {
			if ($item['parent_id'] == $item_id && isset($item['item_id'])) {
				if (isset($item ['language'])) {
					$this->loadLanguage($item ['language'], 'silent');
				}
				$childen = $this->_getChildItems($item['item_id'], $menu_items);
				$rt = '';
				$menu_link = '';
				if ( preg_match("/(http|https):/", $item['item_url']) ) {
					$menu_link = $item['item_url'];	
				} else if ($item['item_url']) {
					//rt based link, need to save rt 
					$menu_link = $this->html->getSecureURL($item['item_url'],'',true);
					$rt = $item['item_url'];
				}
				
				$link_keyname = strpos($item ['item_url'], "http") ? "onclick" : "href";

				$icon = $rm->getResource($item ['item_icon_rl_id']);
				$icon = $icon['resource_code'] ? $icon['resource_code'] : '';

				$temp = array(
							'id' => $item ['item_id'],
							$link_keyname => $menu_link,
							'text' => $this->language->get($item ['item_text']),
							'icon' => $icon
							);

				if ($rt) {
					$temp['rt'] = $rt;
				}

				if ($childen) {
					$temp['children'] = $childen;
				}

				$result[ $item['item_id'] ] = $temp;
			}
		}
		return $result;
	}
}
