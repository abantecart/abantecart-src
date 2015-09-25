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
if (!defined('DIR_CORE') || !IS_ADMIN){
	header('Location: static_pages/');
}
class ControllerResponsesListingGridOrderStatus extends AController{

	public function main(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('localisation/order_status');
		$this->loadModel('localisation/order_status');

		$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

		// process jGrid search parameter
		$allowedDirection = array ('asc', 'desc');

		if (!in_array($sord, $allowedDirection)) $sord = $allowedDirection[0];

		$data = array (
				'order'               => strtoupper($sord),
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit,
				'content_language_id' => $this->session->data['content_language_id'],
		);

		$total = $this->model_localisation_order_status->getTotalOrderStatuses();
		if ($total > 0){
			$total_pages = ceil($total / $limit);
		} else{
			$total_pages = 0;
		}

		if ($page > $total_pages){
			$page = $total_pages;
			$data['start'] = ($page - 1) * $limit;
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = $this->model_localisation_order_status->getOrderStatuses($data);
		$i = 0;

		$base_order_statuses = $this->order_status->getBaseStatuses();

		foreach ($results as $result){
			$id = $result['order_status_id'];
			$response->rows[$i]['id'] = $id;
			if (has_value($base_order_statuses[$id])){
				$response->userdata->classes[$id] = 'disable-delete';
			}
			$response->rows[$i]['cell'] = array (
					$this->html->buildInput(array (
							'name'  => 'order_status[' . $id . '][name]',
							'value' => $result['name']
					)),
					$result['status_text_id']
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/order_status')){
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
					array ('error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/order_status'),
					       'reset_value' => true
					));
		}

		$this->loadModel('localisation/order_status');
		$this->loadModel('setting/store');
		$this->loadModel('sale/order');
		$this->loadLanguage('localisation/order_status');

		switch($this->request->post['oper']){
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids))
					foreach ($ids as $id){
						$err = $this->_validateDelete($id);
						if (!empty($err)){
							$error = new AError('');
							return $error->toJSONResponse('VALIDATION_ERROR_406', array ('error_text' => $err));
						}
						$this->model_localisation_order_status->deleteOrderStatus($id);
					}
				break;
			case 'save':
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids))
					foreach ($ids as $id){
						if (isset($this->request->post['order_status'][$id])){
							foreach ($this->request->post['order_status'][$id] as $value){
								if (!$this->_validate_field($value['name'])){
									$this->response->setOutput($this->language->get('error_name'));
									return null;
								}
							}
							$this->model_localisation_order_status->editOrderStatus($id, array ('order_status' => $this->request->post['order_status'][$id]));
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
	public function update_field(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/order_status')){
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
					array ('error_text'  => sprintf($this->language->get('error_permission_modify'), 'listing_grid/order_status'),
					       'reset_value' => true
					));
		}

		$this->loadLanguage('localisation/order_status');
		$this->loadModel('localisation/order_status');
		if (isset($this->request->get['id']) && !empty($this->request->post)){
			//request sent from edit form. ID in url
			$fields = array ('name', 'status_text_id');
			foreach ($fields as $field_name){
				if (isset($this->request->post[$field_name])){
					if (!$this->_validate_field($this->request->post[$field_name])){
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array ('error_text' => $this->language->get('error_' . $field_name)));
					}
				}
			}

			$this->model_localisation_order_status->editOrderStatus($this->request->get['id'], $this->request->post);
			return null;
		}

		//request sent from jGrid. ID is key of array
		if (isset($this->request->post['order_status'])){
			foreach ($this->request->post['order_status'] as $id => $value){
				if (!$this->_validate_field($value['name'])){
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array ('error_text' => $this->language->get('error_name')));
				}
				$this->model_localisation_order_status->editOrderStatus($id, array ('name' => $value['name']));
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validate_field($value){
		if (mb_strlen($value) < 3 || mb_strlen($value) > 32){
			return false;
		}
		return true;
	}

	private function _validateDelete($order_status_id){

		if (in_array($order_status_id, array_keys($this->order_status->getBaseStatuses()))){
			return $this->language->get('error_nondeletable');
		}

		if ($this->config->get('config_order_status_id') == $order_status_id){
			return $this->language->get('error_default');
		}

		$store_total = $this->model_setting_store->getTotalStoresByOrderStatusId($order_status_id);
		if ($store_total){
			return sprintf($this->language->get('error_store'), $store_total);
		}

		$order_total = $this->model_sale_order->getOrderHistoryTotalByOrderStatusId($order_status_id);
		if ($order_total){
			return sprintf($this->language->get('error_order'), $order_total);
		}
	}

}
