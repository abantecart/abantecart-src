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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

/**
 * Class ModelToolImportProcess
 * @property ModelToolImportProcess $model_tool_import_process
 */
class ModelToolImportProcess extends Model{
	public $errors = array ();
	private $eta = array ();

	/**
	 * @param string $task_name
	 * @param array $data
	 * @return array|bool
	 */
	public function createTask($task_name, $data = array ()){

		if (!$task_name) {
			$this->errors[] = 'Can not to create task. Empty task name has been given.';
		}

		//first of all needs to define recipient count
		//TODO: Pavel, deal with it
		$rows = $this->_get_file_rows($filepath);
		$total_rows_count = sizeof($rows);

		//task controller who will process task steps
		$task_controller = 'task/tool/import_process/processRows';

		//numbers of rows per task step
		$divider = 10;
		//timeout in seconds for one row
		$time_per_send = 4;
		$steps_count = ceil(sizeof($total_rows_count) / $divider);

		$tm = new ATaskManager();

		//create new task
		$task_id = $tm->addTask(
				array (
						'name'               => $task_name,
						'starter'            => 1, //admin-side is starter
						'created_by'         => $this->user->getId(), //get starter id
						'status'             => $tm::STATUS_READY,
						'start_time'         => date('Y-m-d H:i:s',	mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))),
						'last_time_run'      => '0000-00-00 00:00:00',
						'progress'           => '0',
						'last_result'        => '1', // think all fine until some failed step will set 0 here
						'run_interval'       => '0',
					//think that task will execute with some connection errors
						'max_execution_time' => ($total_rows_count * $time_per_send * 2)
				)
		);
		if (!$task_id) {
			$this->errors = array_merge($this->errors, $tm->errors);
			return false;
		}

		$tm->updateTaskDetails($task_id,
				array (
						'created_by' => $this->user->getId(),
						'settings'   => array (
								'total_rows_count' => $total_rows_count,
								'processed'        => 0
						)
				)
		);

		//create steps for sending
		$k = 0;
		$sort_order = 1;
		while ($steps_count > 0) {
			$rows = array_slice($rows, $k, $divider);
			$step_id = $tm->addStep(array (
					'task_id'            => $task_id,
					'sort_order'         => $sort_order,
					'status'             => 1,
					'last_time_run'      => '0000-00-00 00:00:00',
					'last_result'        => '0',
				//think that task will execute with some connection errors
					'max_execution_time' => ($time_per_send * $divider * 2),
					'controller'         => $task_controller,
					'settings'           => array (
							'rows_from' => '',
							'rows_count'   => ''
					)
			));

			if (!$step_id) {
				$this->errors = array_merge($this->errors, $tm->errors);
				return false;
			} else {
				// get eta in seconds
				$this->eta[$step_id] = ($time_per_send * $divider);
			}
			$steps_count--;
			$k = $k + $divider;
			$sort_order++;
		}

		$task_details = $tm->getTaskById($task_id);

		if ($task_details) {
			foreach ($this->eta as $step_id => $eta) {
				$task_details['steps'][$step_id]['eta'] = $eta;
				//remove settings from output json array. We will take it from database on execution.
				$task_details['steps'][$step_id]['settings'] = array ();
			}
			return $task_details;
		} else {
			$this->errors[] = 'Can not to get task details for execution';
			$this->errors = array_merge($this->errors, $tm->errors);
			return false;
		}
	}

	protected function _get_file_rows($filepath){
		return 10000;
	}
}
