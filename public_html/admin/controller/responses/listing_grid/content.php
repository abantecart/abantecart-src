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
class ControllerResponsesListingGridContent extends AController {
	private $error = array();
	private $acm;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('design/content');
		$this->acm = new AContentManager();

		//Prepare filter config
		$grid_filter_params = array( 'sort_order', 'title', 'status' );
		//Build advanced filter
		$filter_grid = new AFilter(array( 'method' => 'post',
			'grid_filter_params' => $grid_filter_params,
		));
		$total = $this->acm->getTotalContents($filter_grid->getFilterData());
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;
		$results = $this->acm->getContents($filter_grid->getFilterData());
		$results = !$results ? array() : $results;
		$multiSelect = $this->acm->getContentsForSelect();
		$i = 0;
		if ($multiSelect) {
			foreach ($results as $result) {
				$multi_temp = $multiSelect;
				unset($multi_temp[ $result[ 'content_id' ] ]);

				$response->rows[ $i ][ 'id' ] = $result[ 'content_id' ];
				$response->rows[ $i ][ 'cell' ] = array( strip_tags(html_entity_decode($result[ 'title' ])),

					$this->html->buildMultiSelectbox(array(
						'name' => 'parent_content_id[' . $result[ 'content_id' ] . '][]',
						'options' => $multi_temp,
						'value' => $result[ 'parent_content_id' ],
						'attr' => 'size = "3"'
					)),
					$this->html->buildCheckbox(array(
						'name' => 'status[' . $result[ 'content_id' ] . ']',
						'value' => $result[ 'status' ],
						'style' => 'btn_switch',
					)),
					$this->html->buildInput(array(
						'name' => 'sort_order[' . $result[ 'content_id' ] . ']',
						'value' => $result[ 'sort_order' ],
					)),
				);
				$i++;
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('design/content');
		$this->acm = new AContentManager();
		if (!$this->user->canModify('listing_grid/content')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/content'),
					'reset_value' => true
				));
		}

		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {

						if ($this->config->get('config_account_id') == $id) {
							$this->response->setOutput($this->language->get('error_account'));
							return;
						}

						if ($this->config->get('config_checkout_id') == $id) {
							$this->response->setOutput($this->language->get('error_checkout'));
							return;
						}


						$this->acm->deleteContent($id);
					}
				break;
			case 'save':
				$allowedFields = array( 'sort_order', 'status', 'parent_content_id' );
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						foreach ($allowedFields as $field) {
							$this->acm->editContentField($id, $field, $this->request->post[ $field ][ $id ]);
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
		$this->loadLanguage('design/content');
		$this->acm = new AContentManager();
		if (!$this->user->canModify('listing_grid/content')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/content'),
					'reset_value' => true
				));
		}
		$allowedFields = array( 'name', 'title', 'description', 'keyword', 'store_id', 'sort_order', 'status', 'parent_content_id' );

		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $field => $value) {
				if (!in_array($field, $allowedFields)) {
					continue;
				}
				$this->acm->editContentField($this->request->get[ 'id' ], $field, $value);
			}
			return;
		}

		//request sent from jGrid. ID is key of array
		foreach ($this->request->post as $field => $value) {
			if (!in_array($field, $allowedFields)) continue;
			foreach ($value as $k => $v) {
				$this->acm->editContentField($k, $field, $v);
			}
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}

?>