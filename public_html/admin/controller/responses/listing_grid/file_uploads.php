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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesListingGridFileUploads extends AController {
	public $data;
	private $error = array ();

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage( 'tool/files' );
		if (! $this->user->canAccess('tool/file_uploads' )) {
			$response = new stdClass();
			$response->userdata->error = sprintf( $this->language->get( 'error_permission_access' ), 'tool/file_uploads' );
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($response));
			return null;
		}

		$this->loadModel ( 'tool/file_uploads' );

		$page = $this->request->post ['page']; // get the requested page
		$limit = $this->request->post ['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post ['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post ['sord']; // get the direction

		$filter = array();
		//process custom search form
		$allowedSearchFilter = array ('date_added', 'section' );

		if (isset ( $this->request->post ['filters'] ) && $this->request->post ['filters'] != '') {
			$this->request->post ['filters'] = json_decode(html_entity_decode($this->request->post ['filters']));
			$filter['value'] = $this->request->post ['filters']->rules[0]->data;
		}

		// process jGrid search parameter

		$data = array (
				'sort' => $sidx.":". $sord,
				'offset' => ($page - 1) * $limit,
				'limit' => $limit,
				'filter' => $filter );

		$total = $this->model_tool_file_uploads->getTotalRows ( $filter );
		if ($total > 0) {
			$total_pages = ceil ( $total / $limit );
		} else {
			$total_pages = 0;
		}

		if($page > $total_pages){
			$page = $total_pages;
			$data['offset'] = ($page - 1) * $limit;
		}

		$response = new stdClass ();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = (array)$this->model_tool_file_uploads->getLog ( $data );
		$i = 0;
		foreach ( $results as $k=>$result ) {
			$k++;
			$response->rows [$i] ['id'] = $k;

			$response->rows [$i] ['cell'] = array (
				$k,
				$result ['date_added'],
				$result ['section'],
				(is_file($result ['path']) ? '<a target="_blank" title="'.$this->language->get ( 'text_download' ).'" href="'.$this->html->getSecureUrl('tool/files/download','&filename='.urlencode($result ['name']) ).'&attribute_type=' . $result['section'] . '&attribute_id=' . $result['section_id'] . '">'.$result ['name'].'</a>': ''),
			);

			$i ++;
		}
		$this->data = $response; // for hook access
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($this->data));

	}

}