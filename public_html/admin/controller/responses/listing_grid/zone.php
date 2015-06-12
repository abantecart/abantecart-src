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
class ControllerResponsesListingGridZone extends AController {

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('localisation/zone');
		$this->loadModel('localisation/zone');

		$page = $this->request->post[ 'page' ]; // get the requested page
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction

		$this->loadModel('localisation/country');
		$template_data[ 'countries' ] = $this->model_localisation_country->getCountries();
		$countries = array( '' => $this->language->get('text_select_country') );
		foreach ($template_data[ 'countries' ] as $c) {
			$countries[ $c[ 'country_id' ] ] = $c[ 'name' ];
		}

		$search_str = '';
		//process custom search form
		$allowedSearchFilter = array( 'country_id' );
		$search_param = array();
		foreach ($allowedSearchFilter as $filter) {
			if (isset($this->request->get[ $filter ]) && $this->request->get[ $filter ] != '') {
				$search_param[ ] = " z.`" . $filter . "` = '" . $this->db->escape($this->request->get[ $filter ]) . "' ";
			}
		}
		if (!empty($search_param))
			$search_str = implode(" AND ", $search_param);

		$data = array(
			'sort' => $sidx,
			'order' => strtoupper($sord),
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'search' => $search_str,
		);


		$total = $this->model_localisation_zone->getTotalZones($data);
		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		if($page > $total_pages){
			$page = $total_pages;
			$data['start'] = ($page - 1) * $limit;
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = $this->model_localisation_zone->getZones($data);
		$i = 0;
		foreach ($results as $result) {

			$response->rows[ $i ][ 'id' ] = $result[ 'zone_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$result[ 'country' ],
				$this->html->buildInput(array(
					'name' => 'zone_name[' . $result[ 'zone_id' ] . '][' . $this->session->data[ 'content_language_id' ] . '][name]',
					'value' => $result[ 'name' ],
				)),
				$this->html->buildInput(array(
					'name' => 'code[' . $result[ 'zone_id' ] . ']',
					'value' => $result[ 'code' ],
				)),
				$this->html->buildCheckbox(array(
					'name' => 'status[' . $result[ 'zone_id' ] . ']',
					'value' => $result[ 'status' ],
					'style' => 'btn_switch',
				)),
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

		if (!$this->user->canModify('listing_grid/zone')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/zone'),
					'reset_value' => true
				));
		}

		$this->loadModel('localisation/zone');
		$this->loadLanguage('localisation/zone');

		switch ($this->request->post[ 'oper' ]) {
			case 'del':

				$this->loadModel('setting/store');
				$this->loadModel('sale/customer');
				$this->loadModel('localisation/location');

				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$err = $this->_validateDelete($id);
						if (!empty($err)) {
							$error = new AError('');
							return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
						}

						$this->model_localisation_zone->deleteZone($id);
					}
				break;
			case 'save':
				$fields = array( 'status', 'code' );
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						foreach ($fields as $f) {
							if ($f == 'status' && !isset($this->request->post[ 'status' ][ $id ]))
								$this->request->post[ 'status' ][ $id ] = 0;

							if (isset($this->request->post[ $f ][ $id ])) {
								$err = $this->_validateField($f, $this->request->post[ $f ][ $id ]);
								if (!empty($err)) {
									$this->response->setOutput($err);
									return null;
								}
								$this->model_localisation_zone->editZone($id, array( $f => $this->request->post[ $f ][ $id ] ));
							}
						}
						
						$err = '';
						if (isset($this->request->post['zone_name'][ $id ]) ) {
							foreach ($this->request->post[ 'zone_name' ][ $id ] as $lang => $value) {
		    					$err = $this->_validateField('name', $value['name']);
		    					if (!empty($err)) {
									$this->response->setOutput($err);
									return null;
								}
							}
							$this->model_localisation_zone->editZone($id, array( 'zone_name' => $this->request->post['zone_name'][ $id ] ));
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
		if (!$this->user->canModify('listing_grid/zone')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/zone'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('localisation/zone');
		$this->loadModel('localisation/zone');
		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$err = '';
				if ( $key == 'zone_name' ) {
					foreach ($value as $lang => $dvalue) {		
		    			$err .= $this->_validateField('name', $dvalue['name']);
		    		}				
				} else {
					$err = $this->_validateField($key, $value);			
				}
				if (!empty($err)) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
				}
				$data = array( $key => $value );
				$this->model_localisation_zone->editZone($this->request->get[ 'id' ], $data);
			}
			return null;
		}

		//request sent from jGrid. ID is key of array
		$fields = array( 'status', 'code' );
		foreach ($fields as $f) {
			if (isset($this->request->post[ $f ]))
				foreach ($this->request->post[ $f ] as $k => $v) {
					$err = $this->_validateField($f, $v);
					if (!empty($err)) {
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
					}
					$this->model_localisation_zone->editZone($k, array( $f => $v ));
				}
		}

		if (isset($this->request->post['zone_name']) ) {
			foreach ($this->request->post['zone_name'] as $id => $v) {
				foreach ($v as $lang => $value) {		
		    		$err = $this->_validateField('name', $value['name']);
		    		if (!empty($err)) {
		    			$error = new AError('');
		    			return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
		    		}
		    	}
		    	$this->model_localisation_zone->editZone($id, array( 'zone_name' => $this->request->post['zone_name'][ $id ] ));
		    }
		}												

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateField($field, $value) {
		$err = '';
		switch ($field) {
			case 'name' :
				if (mb_strlen($value) < 2 || mb_strlen($value) > 128) {
					$err = $this->language->get('error_name');
				}
				break;
		}

		return $err;
	}

	private function _validateDelete($zone_id) {

		if ($this->config->get('config_zone_id') == $zone_id) {
			return $this->language->get('error_default');
		}

		$store_total = $this->model_setting_store->getTotalStoresByZoneId($zone_id);
		if ($store_total) {
			return sprintf($this->language->get('error_store'), $store_total);
		}

		$address_total = $this->model_sale_customer->getTotalAddressesByZoneId($zone_id);
		if ($address_total) {
			return sprintf($this->language->get('error_address'), $address_total);
		}

		$zone_to_location_total = $this->model_localisation_location->getTotalZoneToLocationByZoneId($zone_id);
		if ($zone_to_location_total) {
			return sprintf($this->language->get('error_zone_to_location'), $zone_to_location_total);
		}
	}

}
