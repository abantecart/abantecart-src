<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ControllerResponsesListingGridDatasetsGrid extends AController {
	private $error = array();

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('tool/datasets_manager');
		$this->loadModel('tool/datasets_manager');

		$page = $this->request->post [ 'page' ]; // get the requested page
		$limit = $this->request->post [ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post [ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post [ 'sord' ]; // get the direction
		$offset = ($page - 1) * $limit;

		$total = $this->model_tool_datasets_manager->getTotalDatasets();
		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$response = new stdClass ();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = $this->model_tool_datasets_manager->getDatasets($sidx . " " . $sord, $limit, $offset);
		$i = 0;
		foreach ($results as $result) {
			$response->rows [ $i ] [ 'id' ] = $result [ 'dataset_id' ];
			$response->rows [ $i ] [ 'cell' ] = array( $result [ 'dataset_id' ],
				$result [ 'dataset_name' ],
				$result [ 'dataset_key' ] );
			$i++;
		}


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	/**
	 * method return information about dataset
	 * @return void
	 */
	public function info() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('tool/datasets_manager');
		$this->loadModel('tool/datasets_manager');

		$this->document->setTitle($this->language->get('heading_title'));

		$dataset_info = $this->model_tool_datasets_manager->getDatasetInfo($this->request->get[ 'dataset_id' ]);

		if ($dataset_info) {
			$response = '<tr>';
			foreach ($dataset_info as $key => $info) {
				if (!is_array($info)) {
					$response .= '<tr><td>' . $this->language->get('text_' . $key) . '</td><td>' . $info . '</td></tr>';
				} else {
					if ($info) {
						$response .= '<tr><td>' . $this->language->get('text_' . $key) . '</td><td></td></tr>';
						foreach ($info as $info_name => $info_value) {
							if (!is_array($info_value)) {
								if ($info_name == 'controller') {
									$info_value = '<a href="' . $this->html->getSecureURL($info_value, '&dataset_id=' . $this->request->get[ 'dataset_id' ]) . '" title="review">' . $info_value . '</a>';
								}
								$response .= '<tr><td></td><td>' . $info_name . ': ' . $info_value . '</td></tr>';
							} else {
								foreach ($info_value as $k => $v) {
									$response .= '<tr><td></td><td>' . $k . ': ' . $v . '</td></tr>';
								}

							}
						}
					}
				}
			}
			$response .= '</tr>';
		}

		$this->response->setOutput($response);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function edit() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('listing_grid/dataset_grid')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/dataset_grid'),
					'reset_value' => true
				));
		}

		$this->loadLanguage('tool/datasets_manager');
		$this->loadModel('tool/datasets_manager');

		$page = $this->request->post [ 'page' ]; // get the requested page
		$limit = $this->request->post [ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post [ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post [ 'sord' ]; // get the direction


		$data = array( 'sort' => $sidx . " " . $sord, 'start' => ($page - 1) * $limit, 'limit' => $limit, 'search' => $search_str );
		$total = $this->model_tool_datasets_manager->getTotalDatasetRows($search_str);
		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$response = new stdClass ();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = $this->model_tool_datasets_manager->getDatasetRows($data);
		$i = 0;
		foreach ($results as $result) {
			$response->rows [ $i ] [ 'id' ] = $result [ 'dataset_id' ];
			$response->rows [ $i ] [ 'cell' ] = array( $result [ 'dataset_id' ],
				$result [ 'dataset_name' ],
				$result [ 'dataset_key' ] );
			$i++;
		}


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));

	}

}