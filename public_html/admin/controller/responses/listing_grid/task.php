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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesListingGridTask extends AController {
	private $error = array ();
	
	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage( 'tool/task' );
		if (! $this->user->canAccess('tool/task' )) {

			$response = new stdClass();
			$response->userdata->error = sprintf( $this->language->get( 'error_permission_access' ), 'tool/task' );
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($response));
			return;
		}

		
		$page = $this->request->post ['page']; // get the requested page
		$limit = $this->request->post ['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post ['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post ['sord']; // get the direction

		$filter = array();
		//process custom search form
		$allowedSearchFilter = array ('name');

		if (isset ( $this->request->post ['filters'] ) && $this->request->post ['filters'] != '') {
				$this->request->post ['filters'] = json_decode(html_entity_decode($this->request->post ['filters']));
				$filter['value'] = $this->request->post ['filters']->rules[0]->data;
		}

		// process jGrid search parameter
		$data = array ('sort' => $sidx.":". $sord,
		               'offset' => ($page - 1) * $limit,
		               'limit' => $limit,
		               'filter' => $filter );
		$tm = new ATaskManager();

		$total = $tm->getTotalTasks ();
		if ($total > 0) {
			$total_pages = ceil ( $total / $limit );
		} else {
			$total_pages = 0;
		}
		
		$response = new stdClass ();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		$response->userdata = new stdClass();

			$results = $tm->getTasks ( $data );
			$i = 0;
			foreach ( $results as $k=>$result ) {
				$k++;
				$response->rows [$i] ['id'] = $k;

				$status = $result['status'];
				//if task works more than 30min - we think it's stuck
				if($status == 2 && time() - dateISO2Int($result['start_time']) > 1800){
					$status = -1;
				}

				switch($status){
					case -1:
						$response->userdata->classes[ $k ] = 'warning';
						$text_status = $this->language->get('text_active');
						break;
					case 1:
						$response->userdata->classes[ $k ] = 'success';
						$text_status = $this->language->get('text_scheduled');
						break;
					case 2:
						$response->userdata->classes[ $k ] = 'attention';
						$text_status = $this->language->get('text_active');
						break;
					default:
						$text_status = $this->language->get('text_disabled');

				}

				$response->rows [$i] ['cell'] = array (	$k,
														$result ['task_id'],
														$result ['name'],
														$text_status,
														dateISO2Display($result ['start_time'],$this->language->get('date_format_short'). ' '. $this->language->get('time_format')),
														dateISO2Display($result ['date_created'],$this->language->get('date_format_short'). ' '. $this->language->get('time_format')),
														);

				$i ++;
			}
	
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
		
	}

}