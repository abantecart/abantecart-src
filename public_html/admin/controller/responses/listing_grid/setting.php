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
class ControllerResponsesListingGridSetting extends AController {
	public $groups = array();

	public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
		parent::__construct($registry, $instance_id, $controller, $parent_controller);
		//load available groups for settings
		$this->groups = $this->config->groups;
	}

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		//load available groups for settings

		$this->loadLanguage('setting/setting');
		$this->loadModel('setting/setting');

		//Prepare filter config
		$grid_filter_params = array( 'alias', 'group', 'key' );
		$filter_grid = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ));


		$total = $this->model_setting_setting->getTotalSettings($filter_grid->getFilterData());
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;
		$response->userdata = new stdClass();
		$response->userdata->href = array();

		$results = $this->model_setting_setting->getAllSettings($filter_grid->getFilterData());

		$i = 0;
		foreach ($results as $result) {

			if (($result[ 'value' ] == '1' || $result[ 'value' ] == '0')
					&& !is_int(strpos($result[ 'key' ], '_id'))
					&& !is_int(strpos($result[ 'key' ], 'level'))
			) {
				$value = $this->html->buildCheckbox(array(
					'name' => '',
					'value' => $result[ 'value' ],
					'style' => 'btn_switch disabled',
					'attr' => 'readonly="true"'
				));
			} else {
				$value = $result[ 'value' ];
			}

			$response->rows[ $i ][ 'id' ] = $result[ 'group' ] . '-' . $result[ 'key' ] . '-' . $result[ 'store_id' ];
			if($result['group']=='appearance'){
				$response->userdata->href[$response->rows[ $i ][ 'id' ]] = $this->html->getSecureURL('setting/setting/appearance');
			}
			$response->rows[ $i ][ 'cell' ] = array(
				$result[ 'alias' ],
				$result[ 'group' ],
				$result[ 'key' ],
				$value,
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/setting')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/setting'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('setting/setting');
		$this->loadModel('setting/setting');
		if (isset($this->request->get[ 'group' ])) {
			$group = $this->request->get[ 'group' ];
			//for appearance settings per template
			if( $this->request->get['group']=='appearance' && has_value($this->request->get['tmpl_id']) && $this->request->get['tmpl_id'] != 'default'){
				$group = $this->request->get['tmpl_id'];
			}

			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$err = $this->_validateField($group, $key, $value);
				if (!empty($err)) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
				}
				$data = array($key => $value);

				//html decode store name
				if (has_value($data['store_name'])) {
					$data['store_name'] = html_entity_decode($data['store_name'], ENT_COMPAT, 'UTF-8');
				}

				$this->model_setting_setting->editSetting($group, $data, $this->request->get[ 'store_id' ]);
                startStorefrontSession($this->user->getId());
			}
			return null;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateField($group, $field, $value) {

		$this->load->library('config_manager');
		$config_mngr = new AConfigManager();
		$result = $config_mngr->validate($group, array( $field => $value ));
		return is_array($result[ 'error' ]) ? current($result[ 'error' ]) : $result[ 'error' ];
	}
}
