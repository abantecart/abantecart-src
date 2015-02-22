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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * @property ADB $db
 */
class ATaskManager {
	protected $registry;
	public $errors = array(); // errors during process
	private $starter;
	private $task_log;

	public function __construct() {
		/*if (! IS_ADMIN) { // forbid for non admin calls
			throw new AException ( AC_ERR_LOAD, 'Error: permission denied to change forms' );
		}*/
		
		$this->registry = Registry::getInstance ();
		$this->starter = IS_ADMIN===true ? 1 : 0; // who is starter

		$this->task_log = new ALog(DIR_LOGS.'task_log.txt');

	}
	
	public function __get($key) {
		return $this->registry->get ( $key );
	}
	
	public function __set($key, $value) {
		$this->registry->set ( $key, $value );
	}

	public function runTasks(){
		$task_list = $this->_getScheduledTasks();
		// run loop tasks
		foreach($task_list as $task){
			//check interval and skip task
			$this->toLog('Tried to run task #'.$task['task_id']);
			if($task['interval']>0 && (time() - dateISO2Int($task['last_time_run']) >= $task['interval'] || is_null($task['last_time_run']))  ){
				$this->toLog('task #'.$task['task_id'].' skipped.');
				continue;
			}
			$task_settings = unserialize($task['settings']);
			$this->_run_steps($task['task_id'], $task_settings);
			$this->toLog('task #'.$task['task_id'].' finished.');
		}
	}


	public function runTask($task_id){
		$this->toLog('Tried to run task #'.$task_id.'.');
		$task_id = (int)$task_id;
		$task = $this->_getScheduledTasks($task_id);

		//check interval and skip task
		if($task['interval']>0
				&& (time() - dateISO2Int($task['last_time_run']) >= $task['interval'] || is_null($task['last_time_run']))  ){
			$this->toLog('task #'.$task_id.' skipped.');
			return false;
		}
		$task_settings = unserialize($task['settings']);
		$this->_run_steps($task['task_id'], $task_settings);
		$this->toLog('task #'.$task_id.' finished.');
		return true;
	}


	private function _getScheduledTasks($task_id = 0){
		$task_id = (int)$task_id;
		//get list only sheduled tasks
		$sql = "SELECT *
				FROM ".$this->db->table('tasks')." t
				WHERE t.status = 1 AND t.starter IN ('".$this->starter."','2')
				".($task_id ? " AND t.task_id = ".$task_id : '');
		$result = $this->db->query($sql);
		return $task_id ? $result->row : $result->rows;
	}

	private function _run_steps($task_id, $task_settings ){
		$task_id = (int)$task_id;
		if(!$task_id){ return false; }

		$this->_update_task_state($task_id, array('status'=>2));//change status of task to "active" while it run
		//get steps
		$steps = $this->getScheduledTaskSteps($task_id);
		$task_result = 0;

		$steps_count = sizeof($steps); // total count of steps to calculate percentage (for future)
		$k=0;
		foreach($steps as $step){
			$this->toLog('Tried to run step #'.$step['step_id'].' of task #'.$task_id);
			//change status to active
			$this->_update_step_state( $step['step_id'],
										array( 'result' => 1, // mark last result as "failed"
											   'last_time_run' => date('Y-m-d H:i:s'),
												'status' => 2) ); //change status of step to active while it run

			try{
				$dd = new ADispatcher($step['controller'],$step_settings['params']);
				$response = $dd->dispatchGetOutput($step['controller']);
			}catch(AException $e){	}



			$result = $response['result']==true ? 0 : 1;
			$this->_update_step_state( $step['step_id'],
														array('result' => $result,
															  'last_time_run' => date('Y-m-d H:i:s'),
															  'status'=>1) );

			if(!$result){
				$this->log->write('Sheduled step #'.$step['step_id'].' of task #'.$task_id.' failed during process');
				//interrupt task if need
				if($task_settings['interrupt_on_step_fault']===true){
					$this->_update_task_state($task_id, array( 'result' => 1, // mark last result of task as "failed"
															   'last_time_run' => date('Y-m-d H:i:s'),
															   'status'=>1)//change status of task to sheduled for future run
					);
					return false;
				}
				$task_result = 1;
				$this->toLog('Step #'.$step['step_id'].' of task #'.$task_id.' failed.');
			}
			$this->toLog('Step #'.$step['step_id'].' of task #'.$task_id.' finished.');

			$this->_update_task_state($task_id, array( 'progress' => ceil($k*100/$steps_count)));
		}

		$this->_update_task_state($task_id, array( 'result' => $task_result,
												   'last_time_run' => date('Y-m-d H:i:s'),
													'status' => 1)//change status of task to sheduled for future run
		);
		return true;
	}

	private function _update_task_state($task_id, $state = array()){
		$task_id = (int)$task_id;
		if(!$task_id){ return false; }

		$upd_flds = array('last_result',
						  'last_time_run',
						  'status',
						  'progress');
		$data = array();
		foreach($upd_flds as $fld_name){
			if(has_value($state[$fld_name])){
				$data[$fld_name] = $state[$fld_name];
			}
		}
		return $this->updateTask($task_id,$data);
	}

	private function _update_step_state($step_id, $state = array()){
		$upd_flds = array('task_id',
						  'last_result',
						  'last_time_run',
						  'status');
		$data = array();
		foreach($upd_flds as $fld_name){
			if(has_value($state[$fld_name])){
				$data[$fld_name] = $state[$fld_name];
			}
		}
		return $this->updateStep($step_id,$data);
	}


	public function toLog($message){
		$this->task_log->write($message);
	}

	/**
	 * @param array $data
	 * @return int
	 */
	public function addTask($data = array()){
		if(!$data){
			$this->errors[] = 'Error: Can not to create task. Empty data given.';
			return false;
		}
		// check
		$sql = "SELECT * from ".$this->db->table('tasks')." WHERE name = '".$this->db->escape($data['name'])."'";
		$res = $this->db->query($sql);
		if($res->num_rows){
			$this->deleteTask($res->row['task_id']);
			$this->toLog('Error: Task with name "'.$data['name'].'" is already exists. Overrided!');
		}

		$sql = "INSERT INTO ".$this->db->table('tasks')."
				(`name`,`starter`,`status`,`start_time`,`last_time_run`,`progress`,`last_result`,`run_interval`,`max_execution_time`,`date_modified`)
				VALUES ('".$this->db->escape($data['name'])."',
						'".(int)$data['starter']."',
						'".(int)$data['status']."',
						'".$this->db->escape($data['start_time'])."',
						'".$this->db->escape($data['last_time_run'])."',
						'".(int)$data['progress']."',
						'".(int)$data['last_result']."',
						'".(int)$data['run_interval']."',
						'".(int)$data['max_execution_time']."',
						NOW())";
		$this->db->query($sql);
		$task_id =  $this->db->getLastId();
		if(has_value($data['created_by']) || has_value($data['settings'])){
			$this->updateTaskDetails($task_id, $data);
		}
		return $task_id;
	}

	public function updateTask($task_id, $data = array()){
		$task_id = (int)$task_id;
		if(!$task_id){ return false; }

		$upd_flds = array(
							'name' => 'string',
							'starter' => 'int',
							'status' => 'int',
							'start_time' => 'timestamp',
							'last_time_run' => 'timestamp',
							'progress' => 'int',
							'last_result' => 'int',
							'run_interval' => 'int',
							'max_execution_time' => 'int',
						  	'date_modified' => 'timestamp'
						);
		$update = array();
		foreach($upd_flds as $fld_name => $fld_type){
			if(has_value($data[$fld_name])){
				switch($fld_type){
					case 'int':
						$value = (int)$data[$fld_name];
						break;
					case 'string':
					case 'timestamp':
						$value = $this->db->escape( $data[$fld_name] );
						break;
					default:
						$value = $this->db->escape( $data[$fld_name] );
				}
				$update[] = $fld_name." = '".$value."'";
			}
		}
		if(!$update){ //if nothing to update
			return false;
		}

		$sql = "UPDATE ".$this->db->table( 'tasks' )."
				SET ".implode(', ', $update)."
				WHERE task_id = ".(int)$task_id;
		$this->db->query($sql);

		if(has_value($data['created_by']) || has_value($data['settings'])){
			$this->updateTaskDetails($task_id, $data);
		}
		return true;
	}


	/**
	 * function insert or update task details
	 * @param $task_id
	 * @param array $data
	 * @return bool
	 */
	public function updateTaskDetails($task_id, $data = array()){
		$task_id = (int)$task_id;
		if(!$task_id){ return false;}

		$sql = "SELECT * FROM ".$this->db->table('task_details')." WHERE task_id = ".$task_id;
		$result = $this->db->query($sql);
		if($result->num_rows){
			foreach($result->row as $k=>$ov){
				if(!has_value($data[$k])){
					$data[$k] = $ov;
				}
			}
			$sql = "UPDATE ".$this->db->table( 'task_details' )."
					SET created_by = '".$this->db->escape($data['created_by'])."',
						settings = '".$this->db->escape($data['settings'])."'
					WHERE task_id = ".$task_id;
		}else{
			$sql = "INSERT INTO ".$this->db->table( 'task_details' )."
					(task_id, created_by, settings, date_modified)
					 VALUES (   '".$task_id."',
					 			'".$this->db->escape($data['created_by'])."',
					 			'".$this->db->escape($data['settings'])."',
					 			NOW())";
		}
		$this->db->query($sql);
	}

	public function addStep($data = array()){
		if(!$data){
			$this->errors[] = 'Error: Can not to create task\'s step. Empty data given.';
			return false;
		}
		$data['settings'] = !is_string( $data['settings'] ) ? serialize($data['settings']) : $data['settings'];
		$sql = "INSERT INTO ".$this->db->table('task_steps')."
				(`task_id`,`sort_order`,`status`,`last_time_run`,`last_result`,`max_execution_time`,`controller`, `settings`,`date_modified`)
				VALUES (
						'".(int)$data['task_id']."',
						'".(int)$data['sort_order']."',
						'".(int)$data['status']."',
						'".$this->db->escape($data['last_time_run'])."',
						'".(int)$data['last_result']."',
						'".(int)$data['max_execution_time']."',
						'".$this->db->escape($data['controller'])."',
						'".$this->db->escape( $data['settings'])."',
						NOW())";
		$this->db->query($sql);
		return $this->db->getLastId();
	}

	public function updateStep($step_id, $data = array()){
		$step_id = (int)$step_id;
		if(!$step_id){ return false; }

		$upd_flds = array(
							'task_id' => 'int',
							'starter' => 'int',
							'status' => 'int',
							'sort_order' => 'int',
							'last_time_run' => 'timestamp',
							'last_result' => 'int',
							'max_execution_time' => 'int',
							'controller' => 'string',
							'settings' => 'string',
							'date_modified' => 'timestamp'
						);
		$update = array();
		foreach($upd_flds as $fld_name => $fld_type){
			if(has_value($data[$fld_name])){
				switch($fld_type){
					case 'int':
						$value = (int)$data[$fld_name];
						break;
					case 'string':
					case 'timestamp':
						$value = $this->db->escape( $data[$fld_name] );
						break;
					default:
						$value = $this->db->escape( $data[$fld_name] );
				}
				$update[] = $fld_name." = '".$value."'";
			}
		}
		if(!$update){ //if nothing to update
			return false;
		}

		$sql = "UPDATE ".$this->db->table( 'task_steps' )."
				SET ".implode(', ', $update)."
				WHERE step_id = ".(int)$step_id;
		$this->db->query($sql);
		return true;
	}

	public function deleteTask($task_id){
		$sql[] = "DELETE FROM ".$this->db->table('tasks')." WHERE task_id = '".(int)$task_id."'";
		$sql[] = "DELETE FROM ".$this->db->table('task_steps')." WHERE task_id = '".(int)$task_id."'";
		$sql[] = "DELETE FROM ".$this->db->table('task_details')." WHERE task_id = '".(int)$task_id."'";
		foreach($sql as $q){
			$this->db->query($q);
		}
	}

	public function deleteStep($step_id){
		$sql = "DELETE FROM ".$this->db->table('task_steps')." WHERE step_id = '".(int)$step_id."'";
		$this->db->query($sql);
	}


	public function getTaskById($task_id){
		$task_id = (int)$task_id;
		if(!$task_id){ return array();}
		$sql = "SELECT *
				FROM ".$this->db->table('tasks')." t
				LEFT JOIN ".$this->db->table('task_details')." td ON td.task_id = t.task_id
				WHERE t.task_id = '".$task_id."'";
		$result = $this->db->query($sql);
		$output = $result->row;
		if($output){
			$output['steps'] = $this->getScheduledTaskSteps($output['task_id']);
		}

		return $output;
	}

	public function getTaskByName($task_name){
		$task_name = $this->db->escape($task_name);
		if(!$task_name){ return array();}

		$sql = "SELECT *
				FROM ".$this->db->table('tasks')." t
				LEFT JOIN ".$this->db->table('task_details')." td ON td.task_id = t.task_id
				WHERE t.task_name = '".$task_name."'";
		$result = $this->db->query($sql);
		$output = $result->row;
		if($output){
			$output['steps'] = $this->getScheduledTaskSteps($output['task_id']);
		}

		return $output;
	}

	public function getTaskSteps($task_id){
		$task_id = (int)$task_id;
		if(!$task_id){ return array();}

		$sql = "SELECT *
				FROM ".$this->db->table('task_steps')."
				WHERE task_id = ".$task_id."
				ORDER BY sort_order";
		$result = $this->db->query($sql);
		$output = array();
		foreach($result->rows as $row){
			$row['settings'] = $row['settings'] ? unserialize($row['settings']) : '';
			$output[] = $row;
		}
		return $output;
	}

	public function getScheduledTaskSteps($task_id){
		$task_id = (int)$task_id;
		if(!$task_id){ return array();}

		$all_steps = $this->getTaskSteps($task_id);
		foreach($all_steps as $step){
			if($step['status']!=1){ //skip all steps that not scheduled
				continue;
			}
			$steps[$step['step_id']] = $step;
		}
		return $steps;
	}

	public function getTotalTasks($data = array()){
		$sql = "SELECT COUNT(*) as total
				FROM ".$this->db->table('tasks');
		$sql .= ' WHERE 1=1 ';

		if (!empty($data['subsql_filter'])) {
			$sql .= " AND " . $data['subsql_filter'];
		}

		if (has_value($filter['name'])) {
			$sql .= " AND (LCASE(t.name) LIKE '%" . $this->db->escape(mb_strtolower($filter['name'])) . "%'";
		}


		$result = $this->db->query($sql);
		return $result->row['total'];
	}

	public function getTasks($data = array()){

		$sql = "SELECT *
				FROM ".$this->db->table('tasks')." t ";

		$sql .= 'WHERE 1=1 ';

		if (!empty($data['subsql_filter'])) {
			$sql .= " AND " . $data['subsql_filter'];
		}

		if (has_value($filter['name'])) {
			$sql .= " AND (LCASE(t.name) LIKE '%" . $this->db->escape(mb_strtolower($filter['name'])) . "%'";
		}

		$sort_data = array(
			'name' => 't.name',
			'status' => 't.status',
			'start_time' => 't.start_time',
			'date_modified' => 't.date_modified',
		);

		if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $sort_data[$data['sort']];
		} else {
			$sql .= " ORDER BY t.date_modified";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}


		$result = $this->db->query($sql);
		return $result->rows;
	}
}