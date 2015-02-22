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
			return null;
		}

		
		$page = $this->request->post ['page']; // get the requested page
		$limit = $this->request->post ['rows']; // get how many rows we want to have into the grid

		//Prepare filter config
		$grid_filter_params = array('name');
		$filter = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );
		$filter_data = $filter->getFilterData();

		$tm = new ATaskManager();
		$total = $tm->getTotalTasks ($filter_data);
		if ($total > 0) {
			$total_pages = ceil ( $total / $limit );
		} else {
			$total_pages = 0;
		}
		$results = $tm->getTasks ( $filter_data );

		$response = new stdClass ();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		$response->userdata = new stdClass();


			$i = 0;
			foreach ( $results as $result ) {

				$id = $result ['task_id'];
				$response->rows [$i] ['id'] = $id;

				$status = $result['status'];
				//if task works more than 30min - we think it's stuck
				if($status == 2 && time() - dateISO2Int($result['start_time']) > 1800){
					$status = -1;
				}

				switch($status){
					case -1: // stuck
						$response->userdata->classes[ $id ] = 'warning disable-run disable-edit';
						$text_status = $this->language->get('text_active');
						break;
					case 1: // scheduled
						$response->userdata->classes[ $id ] = 'success disable-restart disable-edit';
						$text_status = $this->language->get('text_scheduled');
						break;
					case 2: //disable all buttons for active tasks
						$response->userdata->classes[ $id ] = 'attention disable-run disable-restart disable-edit disable-delete';
						$text_status = $this->language->get('text_active');
						break;
					default: // disabled
						$response->userdata->classes[ $id ] = 'attention disable-run disable-restart disable-edit disable-delete';
						$text_status = $this->language->get('text_disabled');

				}

				$response->rows [$i] ['cell'] = array (
														$result ['task_id'],
														$result ['name'],
														$text_status,
														dateISO2Display($result ['start_time'],$this->language->get('date_format_short'). ' '. $this->language->get('time_format')),
														dateISO2Display($result ['date_modified'],$this->language->get('date_format_short'). ' '. $this->language->get('time_format')),
														);

				$i ++;
			}
	
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
		
	}

	public function restart(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();

		if(has_value($this->request->post_or_get('task_id'))){
			$tm = new ATaskManager();
			$task = $tm->getTaskById($this->request->post_or_get('task_id'));

			//check
			if($task && $task['status'] == 2 && time() - dateISO2Int($task['start_time']) > 1800){
				foreach($task['steps'] as $step){
					$tm->updateStep($step['step_id'], array('status'=>1));
				}
				$tm->updateTask($task['task_id'], array(
													'status' => 1, //scheduled
													'start_time' => date('Y-m-d H:i:s'),
													'last_result' => 2 //interrupted
														));
			}
			$this->_run_task();
		}else{
			$this->response->setOutput(AJson::encode(array('result'=> false)));
		}


		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	public function run(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();

		if(has_value($this->request->post_or_get('task_id'))){
			$tm = new ATaskManager();
			$task = $tm->getTaskById($this->request->post_or_get('task_id'));
			//check
			if($task && $task['status'] == 1){
				$tm->updateTask($task['task_id'], array(
													'start_time' => date('Y-m-d H:i:s'),
														));
			}
			$this->_run_task();
		}else{
			$this->response->setOutput(AJson::encode(array('result'=> false)));
		}


		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	//
	private function _run_task(){

		$connect = new AConnect(true);
		$url = $this->config->get('config_url').'task.php';
		$connect->getDataHeaders( $url );
	}


}