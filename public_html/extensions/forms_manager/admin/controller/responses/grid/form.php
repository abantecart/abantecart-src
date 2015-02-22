<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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
/**
 * Class ControllerResponsesGridForm
 * @property ModelToolFormsManager $model_tool_forms_manager
 */
class ControllerResponsesGridForm extends AController {
	private $error = array();

	public function main() {

		$this->loadLanguage('forms_manager/forms_manager');
		$this->loadModel('tool/forms_manager');

		//Clean up parametres if needed
		if (isset($this->request->get[ 'keyword' ]) && $this->request->get[ 'keyword' ] == $this->language->get('filter_form')) {
			unset($this->request->get[ 'keyword' ]);
		}

		//Prepare filter config
		$filter_params = array( 'status', 'keyword', 'match' );
		$grid_filter_params = array( 'form_name', 'description', 'status' );

		$filter_form = new AFilter(array( 'method' => 'get', 'filter_params' => $filter_params ));
		$filter_grid = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ));

		$total = $this->model_tool_forms_manager->getTotalForms(array_merge($filter_form->getFilterData(), $filter_grid->getFilterData()));
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;

		$results = $this->model_tool_forms_manager->getForms(array_merge($filter_form->getFilterData(), $filter_grid->getFilterData()));

		$i = 0;
		foreach ($results as $result) {


			$response->rows[ $i ][ 'id' ] = $result[ 'form_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$this->html->buildInput(array(
					'name' => 'form_name[' . $result[ 'form_id' ] . ']',
					'value' => $result[ 'form_name' ],
				)),
				$this->html->buildInput(array(
					'name' => 'form_description[' . $result[ 'form_id' ] . ']',
					'value' => $result[ 'description' ],
				)),
				$this->html->buildCheckbox(array(
					'name' => 'form_status[' . $result[ 'form_id' ] . ']',
					'value' => $result[ 'status' ],
					'style' => 'btn_switch',
				)),
			);
			$i++;
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('tool/forms_manager')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'tool/forms_manager'),
						'reset_value' => true
				));
		}

		$this->loadModel('tool/forms_manager');
		$this->loadLanguage('forms_manager/forms_manager');

		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$this->model_tool_forms_manager->deleteForm($id);
					}
				break;
			case 'save':
				$fields = array( 'form_name', 'form_description', 'form_status' );
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						foreach ($fields as $f) {

							if ( $f == 'form_status' && !isset($this->request->post[ 'form_status' ][ $id ]) ) {
								$this->request->post[ 'form_status' ][ $id ] = 0;
							}
							if ( isset($this->request->post[ $f ][ $id ]) ) {
								$err = $this->_validateField($f, $this->request->post[ $f ][ $id ]);
								if (!empty($err)) {
									$error = new AError('');
									return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
								}
								$this->model_tool_forms_manager->updateForm(array( 'form_id' => $id, $f => $this->request->post[ $f ][ $id ] ));
							}
						}
					}

				break;

			default:

		}

	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		if ( $this->request->is_POST() ) {
			$this->loadModel('tool/forms_manager');

			foreach ($this->request->post as $field => $value ) {

				if(is_array($value)){
					$data = array();
					foreach($value as $id=>$val){
						$data['form_id'] = $id;
						$data[$field] = $val;
						$this->model_tool_forms_manager->updateForm($data);
					}
				}else{
					if((int)$this->request->get['form_id']){
						$this->model_tool_forms_manager->updateForm(array('form_id' => $this->request->get['form_id'],$field => $value));
					}
				}
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateField($field, $value) {
		$err = '';

		if ( mb_strlen($value) < 1 ) {
			$err = $field.": ". $this->language->get('error_required');
		}

		return $err;
	}

	private function _validateDelete($id) {
		return null;
	}

}