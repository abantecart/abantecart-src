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
class ControllerResponsesListingGridDownload extends AController {

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('catalog/download');
		$this->loadModel('catalog/download');

		//Prepare filter config
		$grid_filter_params = array( 'name');
		$filter = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params,'additional_filter_string' => 'shared=1'));
		$filter_data = $filter->getFilterData();

		$total = $this->model_catalog_download->getTotalDownloads($filter_data);
		$response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages($total);
		$response->records = $total;
		$response->userdata = new stdClass();
		$results = $this->model_catalog_download->getDownloads($filter_data);
		$i = 0;
		foreach ($results as $result) {
			if (!is_file(DIR_RESOURCE . $result[ 'filename' ])) {
				$response->userdata->classes[ $result[ 'download_id' ] ] = 'warning';
			}
			$response->rows[ $i ][ 'id' ] = $result[ 'download_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$this->html->buildInput(array(
				                    'name'  => 'name['.$result['download_id'].']',
				                    'value' => $result['name'],
								    'attr' => ' maxlength="64" '
				                )),
				$this->html->buildCheckbox(array(
				                    'name'  => 'status['.$result[ 'download_id' ].']',
				                    'value' => $result['status'],
				                    'style'  => 'btn_switch',
				                )),
				$result[ 'product_count' ],
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
				$allowedFields = array('name', 'status');

				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) ) {
					foreach( $ids as $id ) {
						foreach ( $allowedFields as $field ) {
							$this->model_catalog_download->editDownload($id, array($field => $this->request->post[$field][$id]) );
						}
					}
				}
				break;

			default:


		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		return null;
	}

	/**
	 * update only one field
	 *
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
		$this->loadLanguage('catalog/files');

		$this->loadModel('catalog/download');
		$allowedFields = array( 'name',
								'filename',
								'mask',
								'max_downloads',
								'shared',
								'expire_days',
								'sort_order',
								'activate_order_status_id',
								'status',
								'attributes');


		if (isset($this->request->get[ 'id' ])) {
			$download_id = (int)$this->request->get[ 'id' ];
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				if (!in_array($key, $allowedFields)) continue;
				// check first
				if($key=='name' && (mb_strlen($value)<2 || mb_strlen($value)>64) ) {
					$error = $this->language->get('error_download_name');
				}elseif($key=='activate' && !in_array($data[ 'activate' ],array('before_order','immediately','order_status','manually')) ) {
					$error = $this->language->get('error_activate');
				}elseif($key=='attributes'){
					$attr_mngr = new AAttribute_Manager('download_attribute');
					$attr_errors = $attr_mngr->validateAttributeData($value[$download_id]);
					if($attr_errors){
						$error = $this->language->get('error_download_attributes').'<br>&nbsp;&nbsp;&nbsp;'. implode('<br>&nbsp;&nbsp;&nbsp;',$attr_errors);
					}
				}

				if(!$error){
					$data = array( $key => $value );
					$this->model_catalog_download->editDownload($download_id, $data);
				}else{
					$e = new AError('');
					return $e->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $error ));
				}
			}
			return null;
		}else{
			//request sent from jGrid. ID is key of array
			foreach ($this->request->post as $field => $value ) {
				foreach ( $value as $k => $v ) {
					 if($field=='name'){
						if (mb_strlen($v) < 2 || mb_strlen($v) >64 ) {
							$err = $this->language->get('error_name');
							$error = new AError('');
							return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
						}
					}
					$this->model_catalog_download->editDownload($k, array($field => $v) );
				}
			}
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		return null;
	}
}
