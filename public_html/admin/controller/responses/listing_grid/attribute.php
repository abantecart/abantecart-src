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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesListingGridAttribute extends AController {
	private $error = array();
	private $attribute_manager;

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		$this->attribute_manager = new AAttribute_Manager();
		$this->loadLanguage('catalog/attribute');
	}

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$page = $this->request->post[ 'page' ]; // get the requested page
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction

		$search_str = '';
		//process custom search form
		$allowedSearchFilter = array( 'attribute_parent_id', 'attribute_type_id', 'status' );
		$search_param = array();
		foreach ($allowedSearchFilter as $filter) {
			if (isset($this->request->get[ $filter ]) && $this->request->get[ $filter ] != '') {
				$search_param[ ] = "ga.`" . $filter . "` = '" . $this->db->escape($this->request->get[ $filter ]) . "' ";
			}
		}
		if (!empty($search_param)) {
			$search_str = implode(" AND ", $search_param);
		}

		$data = array(
			'sort' => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'search' => $search_str,
		);

		$total = $this->attribute_manager->getTotalAttributes();
		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		$response->search = $search_str;

		$attribute_types = array( '' => $this->language->get('text_select') );
		$results = $this->attribute_manager->getAttributeTypes();
		foreach ($results as $type) {
			$attribute_types[ $type[ 'attribute_type_id' ] ] = $type[ 'type_name' ];
		}

		$new_level = 0;
		$attr_parent_id = null;
		$leafnodes = array();
		//get all leave attributes 
		$leafnodes = $this->attribute_manager->getLeafAttributes();
		if ($this->config->get('config_show_tree_data')) {
			if ($this->request->post[ 'nodeid' ]) {
				$attr_parent_id = (integer)$this->request->post[ 'nodeid' ];
				$new_level = (integer)$this->request->post[ "n_level" ] + 1;
			} else {
				$attr_parent_id = 0;
			}
		}

		$results = $this->attribute_manager->getAttributes($data, '', $attr_parent_id);
		$i = 0;
		foreach ($results as $result) {
			//treegrid structure
			$name_lable = '';
			if ($this->config->get('config_show_tree_data')) {
				$name_lable = $result[ 'name' ];
			} else {
				$name_lable = $this->html->buildInput(array(
					'name' => 'name[' . $result[ 'attribute_id' ] . ']',
					'value' => $result[ 'name' ],
				));
			}

			$response->rows[ $i ][ 'id' ] = $result[ 'attribute_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$name_lable,
				$attribute_types[ $result[ 'attribute_type_id' ] ],
				$this->html->buildInput(array(
					'name' => 'sort_order[' . $result[ 'attribute_id' ] . ']',
					'value' => $result[ 'sort_order' ],
				)),
				$this->html->buildCheckbox(array(
					'name' => 'status[' . $result[ 'attribute_id' ] . ']',
					'value' => $result[ 'status' ],
					'style' => 'btn_switch',
				)),
				'action',
				$new_level,
				($attr_parent_id ? $attr_parent_id : NULL),
				($result[ 'attribute_id' ] == $leafnodes[ $result[ 'attribute_id' ] ] ? true : false),
				false
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/attribute')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/attribute'),
					'reset_value' => true
				));
		}

		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$err = $this->_validateDelete($id);
						if (!empty($err)) {
							$dd = new ADispatcher('responses/error/ajaxerror/validation', array( 'error_text' => $err ));
							return $dd->dispatch();
						}
						$this->attribute_manager->deleteAttribute($id);
					}
				break;
			case 'save':
				$fields = array( 'name', 'attribute_type_id', 'sort_order', 'status' );
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						foreach ($fields as $f) {
							if (isset($this->request->post[ $f ][ $id ])) {
								$err = $this->_validateField($f, $this->request->post[ $f ][ $id ]);
								if (!empty($err)) {
									$dd = new ADispatcher('responses/error/ajaxerror/validation', array( 'error_text' => $err ));
									return $dd->dispatch();
								}
								$this->attribute_manager->updateAttribute($id, array( $f => $this->request->post[ $f ][ $id ] ));
							}
						}
					}

				break;

			default:
				//print_r($this->request->post);

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

		if (!$this->user->canModify('listing_grid/attribute')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/attribute'),
					'reset_value' => true
				));
		}

		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$err = $this->_validateField($key, $value);
				if (!empty($err)) {
					$dd = new ADispatcher('responses/error/ajaxerror/validation', array( 'error_text' => $err ));
					return $dd->dispatch();
				}
				$data = array( $key => $value );
				$this->attribute_manager->updateAttribute($this->request->get[ 'id' ], $data);
			}
			return;
		}

		//request sent from jGrid. ID is key of array
		$fields = array( 'name', 'attribute_type_id', 'sort_order', 'status' );
		foreach ($fields as $f) {
			if (isset($this->request->post[ $f ]))
				foreach ($this->request->post[ $f ] as $k => $v) {
					$err = $this->_validateField($f, $v);
					if (!empty($err)) {
						$dd = new ADispatcher('responses/error/ajaxerror/validation', array( 'error_text' => $err ));
						return $dd->dispatch();
					}
					$this->attribute_manager->updateAttribute($k, array( $f => $v ));
				}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateField($field, $value) {
		$err = '';
		switch ($field) {
			case 'name' :
				if ((strlen(utf8_decode($value)) < 2) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_name');
				}
				break;
			case 'attribute_type_id' :
				if (empty($value)) {
					$err = $this->language->get('error_required');
				}
				break;
		}

		return $err;
	}

	private function _validateDelete($attribute_id) {
		return;
	}

}