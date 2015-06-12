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
class ControllerResponsesListingGridDatasetsGrid extends AController {

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

		if($page > $total_pages){
			$page = $total_pages;
			$offset = ($page - 1) * $limit;
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

		$this->view->assign('dataset_info', $dataset_info);

		$response = $this->view->fetch('responses/tool/dataset_info.tpl');
		$this->response->setOutput($response);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}