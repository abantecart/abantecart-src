<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

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

class ControllerTaskToolImportProcess extends AController{
	public $data = array();
	private $processed_count = 0;
	public function processRows(){
		list($task_id,$step_id,) = func_get_args();
		$this->load->library('json');
		//for aborting process
		ignore_user_abort(false);
		session_write_close();

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);


		$this->processed_count = 0;
		$result = $this->_process($task_id,$step_id);
		if(!$this->processed_count){
			$result = false;
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$output = array('result'  => $result);
		if($result){
			$output['message'] = $this->processed_count . ' rows processed.';
		}else{
			$output['error_text'] = $this->processed_count . ' rows processed.';
		}

		$this->response->setOutput(AJson::encode( $output ));
	}


	private function _process($task_id, $step_id){

		if (!$task_id || !$step_id){
			$error_text = 'Cannot run task step. Task_id (or step_id) has not been set.';
			$this->_return_error($error_text);
		}

		$tm = new ATaskManager();
		$task_info = $tm->getTaskById($task_id);
		$processed = (int)$task_info['settings']['processed'];
		$task_steps = $tm->getTaskSteps($task_id);
		$step_info = array();
		foreach($task_steps as $task_step){
			if($task_step['step_id'] == $step_id){
				$step_info = $task_step;
				if($task_step['sort_order']==1){
					$tm->updateTask($task_id, array('last_time_run' => date('Y-m-d H:i:s')));
				}
				break;
			}
		}

		if(!$step_info){
			$error_text = 'Cannot run task step. Looks like task #'.$task_id.' does not contain step #'.$step_id;
			$this->_return_error($error_text);
		}

		$tm->updateStep($step_id, array('last_time_run' => date('Y-m-d H:i:s')));

		if(!$step_info['settings'] ){
			$error_text = 'Cannot run task step #'.$step_id.'. Unknown settings for it.';
			$this->_return_error($error_text);
		}

		$step_settings =  $step_info['settings'];
		$cnt = 0;
		$step_result = true;

		//get rows for process
		$rows = array();/// get file rows base on $step_settings['rows_count'] and $step_settings['rows_from']

		//remove step if no rows
		/*if(!$rows){
			$tm->deleteStep($step_id);
			if(sizeof($task_steps)==1){
				$tm->deleteTask($task_id);
			}
			return true;
		}*/
		foreach($rows as $row){
			// do something with rows here
			$result = $this->_do_row($row);

			if($result){
				$this->processed_count ++;

				//update task details to show them at the end
				$processed++;
				$tm->updateTaskDetails($task_id,
						array(
								//set 1 as "admin"
								'created_by' => 1,
								'settings'   => array(
													'total_rows_count' => $task_info['settings']['total_rows_count'],
													'processed'        => $processed
													)
				));
			}else{
				$step_result = false;
			}
			$cnt++;
		}

		$tm->updateStep($step_id, array('last_result' => $step_result));

		if(!$step_result){
			$this->_return_error('Some errors during step run.');
		}
		return $step_result;
	}

	//process one row of file
	protected function _do_row($row){



		return true;
}
	private function _return_error($error_text){
		$error = new AError($error_text);
		$error->toLog()->toDebug();
		return $error->toJSONResponse('APP_ERROR_402',
				array ('error_text'  => $error_text,
				       'reset_value' => true
				));
	}


}
