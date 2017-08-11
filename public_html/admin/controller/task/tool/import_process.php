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

/**
 * Class ControllerTaskToolImportProcess
 * @property ModelToolImportProcess $model_tool_import_process
 */
class ControllerTaskToolImportProcess extends AController{
	public $data = array();
	protected $success_count = 0;
	protected $failed_count = 0;

	public function processRows(){
		list($task_id, $step_id, ) = func_get_args();
		$this->load->library('json');
		//for aborting process
		ignore_user_abort(false);
		session_write_close();

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->success_count = 0;
		$this->failed_count = 0;
		$result = $this->_process($task_id, $step_id);
		if(!$this->success_count){
			$result = false;
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$output = array('result'  => $result);
		if($result){
			$output['message'] = $this->success_count . ' rows processed.';
			if($this->failed_count){
				$output['message'] .= $this->failed_count . ' rows processed with error.';
			}
		}else{
			$output['error_text'] = $this->failed_count . ' rows processed with error.';
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
		//get setting with import details
		$import_details = $task_info['settings']['import_data'];
		$file_format = $import_details['format'];
		$step_info = $tm->getTaskStep($task_id, $step_id);
		if(!$step_info['settings'] ){
			$error_text = "Cannot run task #{$task_id} step #{$step_id}. Can not locate settings for the step.";
			$this->_return_error($error_text);
		}
		//record the start
		$tm->updateStep($step_id, array('last_time_run' => date('Y-m-d H:i:s')));

		$return = array();
		$start = $step_info['settings']['start'];
		$stop = $step_info['settings']['stop'];
		$filename = $import_details['file'];
		$type = $import_details['table'];
		$delimiter = $import_details['delimiter'];

		$step_result = false;
		//read records from source file
		$records = $this->readFileSeek($filename, $start, ($stop-$start));

		if(count($records)) {
			//process column names
			$columns = str_getcsv($records[0], $delimiter, '"');
			//skip header and process each record
			array_shift($records);
			$this->loadModel('tool/import_process');
			$step_failed_count = 0;
			$a_data = new AData();
			foreach ($records as $index => $row) {
				$vals = array();
				$rowData = str_getcsv($row, $delimiter, '"');
				//check if we match row data count to header
				if (count($rowData) != count($columns)) {
					//incomplete row. Exit
					$return[] = "Error: incomplete data in row number: {$index} with: {$rowData[0]}";
					$step_failed_count++;
					continue;
				}
				for ($i = 0; $i < count($columns); $i++) {
					$vals[$columns[$i]] = $rowData[$i];
				}

				if($file_format == 'internal'){
					//$results = $a_data->importData($vals);
					//$result = true;
					$result = false;
				}else {
					//main driver to process data and import
					$method = "process_" . $type . "_record";
					try{
						$result = $this->model_tool_import_process->$method($task_id, $vals, $import_details);
					} catch (AException $e){
						$result = false;
					}
				}
				if ($result) {
					$this->success_count++;
				}else{
					$step_failed_count++;
				}
			}

			$this->failed_count = $this->failed_count + $step_failed_count;
			$tm->updateTaskDetails($task_id,
				array(
					'settings'   => array(
						'logfile'           => $type.'_import_'.$task_id.'.txt',
						'import_data'       => $task_info['settings']['import_data'],
						'total_rows_count'  => $task_info['settings']['total_rows_count'],
						'success_count'     => (int)$task_info['settings']['success_count'] + $this->success_count,
						'failed_count'      => (int)$task_info['settings']['failed_count'] + $this->failed_count
					)
				)
			);
			//sends always true as result
			$step_result = true;
			$tm->updateStep($step_id, array('last_result' => $step_result));
			//all done, clear cache
			$this->cache->remove('*');
		}

		return $step_result;
	}

	protected function readFileSeek($source, $line_num = 1, $range = 1){
		if(!$source){
			return array();
		}
		$buffer = array();
		$fh = fopen($source, 'r');
		if(!$fh){
			return array();
		}

		$lineNo = 0;
		$startLine = $line_num;
		$endLine = $line_num + $range;
		while ($line = fgets($fh)) {
			//always return first line with header
			if($lineNo++ == 0) {
				$buffer[] = $line;
				continue;
			}

			if ($lineNo >= $startLine) {
				$buffer[] = $line;
			}
			if ($lineNo == $endLine) {
				break;
			}
		}
		fclose($fh);
		return $buffer;
	}

	protected function _return_error($error_text){
		$error = new AError($error_text);
		$error->toLog()->toDebug();
		return $error->toJSONResponse('APP_ERROR_402',
				array ('error_text'  => $error_text,
					   'reset_value' => true
				));
	}

}
