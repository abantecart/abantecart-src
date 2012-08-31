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
class ControllerResponsesListingGridDownload extends AController {
	private $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/download');
		$this->loadModel('catalog/download');

		//Prepare filter config
		$grid_filter_params = array( 'name' );
		$filter = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ));
		$filter_data = $filter->getFilterData();

		$total = $this->model_catalog_download->getTotalDownloads($filter_data);
		$response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages($total);
		$response->records = $total;
		$results = $this->model_catalog_download->getDownloads($filter_data);
		$i = 0;
		foreach ($results as $result) {
			if (!file_exists(DIR_RESOURCE . $result[ 'filename' ]) || !is_file(DIR_RESOURCE . $result[ 'filename' ])) {
				$response->userdata->classes[ $result[ 'download_id' ] ] = 'warning';
			}
			$response->rows[ $i ][ 'id' ] = $result[ 'download_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$result[ 'name' ],
				$result[ 'remaining' ],
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

		if (!$this->user->canModify('listing_grid/download')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/download'),
					'reset_value' => true
				));
		}

		$this->loadModel('catalog/download');
		$this->loadLanguage('catalog/download');
		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$this->model_catalog_download->deleteDownload($id);
					}
				break;
			case 'save':

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

		if (!$this->user->canModify('listing_grid/download')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/download'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('catalog/download');
		$this->loadModel('catalog/download');
		$allowedFields = array( 'download_description', 'remaining' );

		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				if (!in_array($key, $allowedFields)) continue;
				$data = array( $key => $value );
				$this->model_catalog_download->editDownload($this->request->get[ 'id' ], $data);
			}
			return;
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}

?>