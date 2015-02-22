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
class ControllerResponsesListingGridMenu extends AController {
	private $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$language_id = $this->session->data[ 'content_language_id' ];
		$this->loadLanguage('design/menu');
		$this->loadModel('tool/image');
		$model = $this->model_tool_image;

		$page = $this->request->post[ 'page' ]; // get the requested page
		if ((int)$page < 0) $page = 0;
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction


		$search_str = '';
		//process custom search form
		$this->menu = new AMenu_Storefront();
		$menu_items = $this->menu->getMenuItems();

		$new_level = 0;
		$leafnodes = array();
		//get all leave menus 
		$leafnodes = $this->menu->getLeafMenus();
		//build parent id
		$menu_parent_id = '';
		if ($this->request->get[ 'parent_id' ]) {
			$menu_parent_id = $this->request->get[ 'parent_id' ];
		} else if ($this->request->post[ 'nodeid' ]) {
			$menu_parent_id = $this->request->post[ 'nodeid' ];
			$new_level = (integer)$this->request->post[ "n_level" ] + 1;
		}

		if (!empty($menu_parent_id))
			$menu_items = $menu_items[ $menu_parent_id ];
		else
			$menu_items = $menu_items[ "" ];

		//sort
		$allowedSort = array( 'item_id', 'item_text', 'sort_order' );
		$allowedDirection = array( SORT_ASC => 'asc', SORT_DESC => 'desc' );
		if (!in_array($sidx, $allowedSort)) $sidx = $allowedSort[ 0 ];
		if (!in_array($sord, $allowedDirection)) {
			$sord = SORT_ASC;
		} else {
			$sord = array_search($sord, $allowedDirection);
		}

		$sort = array();
		$total = count($menu_items);
		$response = new stdClass();

		if ($total > 0) {
			foreach ($menu_items as $item) {
				if ($sidx == 'item_text') {
					$sort[ ] = $item[ $sidx ][ $language_id ];
				} else {
					$sort[ ] = $item[ $sidx ];
				}
			}

			array_multisort($sort, $sord, $menu_items);
			$total_pages = ceil($total / $limit);

			$results = array_slice($menu_items, ($page - 1) * -$limit, $limit);

			$i = 0;
			$ar = new AResource('image');
			foreach ($results as $result) {
				$resource = $ar->getResource($result[ 'item_icon_rl_id' ]);
				if($resource['resource_path'] || !$resource['resource_code']) {
					$thumb = $ar->getResourceThumb($result['item_icon_rl_id'],
							(int)$this->config->get('config_image_grid_width'),
							(int)$this->config->get('config_image_grid_height'));
					$icon = '<img src="' . $thumb . '" alt="" />';
				}elseif($resource['resource_code']){
					$icon = '<i class="fa fa-code fa-2x"></i>';
				}
				$response->rows[ $i ][ 'id' ] = $result[ 'item_id' ];
				$response->rows[ $i ][ 'cell' ] = array(
					$icon,
					$result[ 'item_id' ],
					$result[ 'item_text' ][ $language_id ],
					$this->html->buildInput(array(
						'name' => 'sort_order[' . $result[ 'item_id' ] . ']',
						'value' => $result[ 'sort_order' ],
					)),
					'action',
					$new_level,
					($menu_parent_id ? $menu_parent_id : NULL),
					($result[ 'item_id' ] == $leafnodes[ $result[ 'item_id' ] ] ? true : false),
					false
				);
				$i++;
			}

		} else {
			$total_pages = 0;
		}

		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('design/menu');
		if (!$this->user->canModify('listing_grid/menu')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/menu'),
					'reset_value' => true
				));
		}

		$menu = new AMenu_Storefront();
		$item_keys = array( 'item_text', 'item_url', 'parent_id', 'sort_order' );

		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids)) {
					$all_menu_ids = $menu->getItemIds();
					foreach ($ids as $item_id) {

						if (in_array($item_id, $all_menu_ids)) {
							$menu->deleteMenuItem($item_id);
						}
					}
				}
				break;
			case 'save':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids)) {
					//resort required. 
					if(  $this->request->post['resort'] == 'yes' ) {
						//get only ids we need
						foreach($ids as $id){
							$array[$id] = $this->request->post['sort_order'][$id];
						}
						$new_sort = build_sort_order($ids, min($array), max($array), $this->request->post['sort_direction']);
	 					$this->request->post['sort_order'] = $new_sort;
					}
					foreach ($ids as $item_id) {
						$item_values = array();
						foreach($item_keys as $key) {
							if ( isset($this->request->post[$key][$item_id]) ) {
								$item_values[$key] = $this->request->post[$key][$item_id];
							}
						}
						// if item already in menu dataset
						if ($menu->getMenuItem($item_id)) {
							$menu->updateMenuItem($item_id, $item_values);
						}
					}
				}
				break;

			default:

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('localisation/language');
		if (!$this->user->canModify('listing_grid/menu')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/menu'),
					'reset_value' => true
				));
		}

		$menu = new AMenu_Storefront();
		$allowedFields = array( 'item_icon', 'item_text', 'item_url', 'parent_id', 'sort_order' );

		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				if (!in_array($key, $allowedFields)) continue;
				$data = array( $key => $value );
				$menu->updateMenuItem($this->request->get[ 'id' ], $data);
			}
			return null;
		}

		//request sent from jGrid. ID is key of array
		foreach ($this->request->post as $key => $value) {
			if (!in_array($key, $allowedFields)) continue;
			foreach ($value as $k => $v) {
				$data = array( $key => $v );
				$menu->updateMenuItem($k, $data);
			}
		}


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}
