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
class ControllerResponsesListingGridLocationZones extends AController {

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('localisation/zone');
		$this->loadModel('localisation/zone');

		$page = $this->request->post[ 'page' ]; // get the requested page
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction

		$this->loadModel('localisation/location');
		$this->loadModel('localisation/zone');
		$this->loadModel('localisation/country');

		$data = array(
			'location_id' => $this->request->get[ 'location_id' ],
			'sort' => $sidx,
			'order' => strtoupper($sord),
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

		$zone_to_locations = $this->model_localisation_location->getZoneToLocations($data);

		$total = $this->model_localisation_location->getTotalZoneToLocationsByLocationID($this->request->get[ 'location_id' ]);

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

		$i = 0;
		foreach ($zone_to_locations as $result) {

			$response->rows[ $i ][ 'id' ] = $result[ 'zone_to_location_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$result[ 'country_name' ],
				$result[ 'name' ],
				dateISO2Display($result[ 'date_added' ], $this->language->get('date_format_short'))
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

		if (!$this->user->canModify('listing_grid/location_zones')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/location_zones'),
					'reset_value' => true
				));
		}

		$this->loadModel('localisation/zone');
		$this->loadLanguage('localisation/zone');


		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$this->loadModel('localisation/location');

				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$this->model_localisation_location->deleteLocationZone($id);
					}
				break;

			default:


		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

?>