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

class ModelToolBackup extends Model {
	public $errors = array();
	public $backup_filename;
	public function restore($sql) {
		$this->db->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'"); // to prevent auto increment for 0 value of id
		$qr = explode(";\n", $sql);
		foreach ($qr as $sql) {
			$sql = trim($sql);
			if ($sql) {
				$this->db->query($sql);
			}
		}
		$this->db->query("SET SQL_MODE = ''");
	}
	public function load($xml) {
		$xml_obj = simplexml_load_string($xml);
		if ($xml_obj) {
			$xmlname = $xml_obj->getName();
			if ($xmlname == 'template_layouts') {
				$load = new ALayoutManager();
				$load->loadXML(array('xml' => $xml));
			} elseif ($xmlname == 'datasets') {
				$load = new ADataset();
				$load->loadXML(array('xml' => $xml));
			} elseif ($xmlname == 'forms') {
				$load = new AFormManager();
				$load->loadXML(array('xml' => $xml));
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	/**
	 * funrtion returns table list of abantecart
	 * @return array
	 */
	public function getTables() {
		$table_data = array();
		$prefix_len = strlen(DB_PREFIX);

		$query = $this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "`");

		foreach ($query->rows as $result) {
			$table_name = $result['Tables_in_' . DB_DATABASE];
			//if database prefix present - select only abantecart tables. If not - select all
			if (DB_PREFIX && substr($table_name,0,$prefix_len) != DB_PREFIX ) {
				continue;
			}
			$table_data[] = $result['Tables_in_' . DB_DATABASE];
		}
		return $table_data;
	}

	public function backup($tables, $rl = true, $config = false, $sql_dump_mode = 'data_only') {

		$bkp = new ABackup('manual_backup' .'_'. date('Y-m-d-H-i-s'));

		if($bkp->error){
			return false;
		}


		// do sql dump
		if(!in_array($sql_dump_mode, array('data_only','recreate') )){
			$sql_dump_mode = 'data_only';
		}
		$bkp->sql_dump_mode = $sql_dump_mode;
		$bkp->dumpTables($tables);


		if ($rl) {
			$bkp->backupDirectory(DIR_RESOURCE, false);
		}
		if ($config) {
			$bkp->backupFile(DIR_ROOT . '/system/config.php', false);
		}
		$result = $bkp->archive(DIR_BACKUP . $bkp->getBackupName() . '.tar.gz', DIR_BACKUP, $bkp->getBackupName());
		if (!$result) {
			$this->errors[] = $bkp->error;
		} else {
			$this->backup_filename = $bkp->getBackupName();
		}

		return $result;
	}

	public function createBackupTask($task_name, $data = array()){

		if(!$task_name){
			$this->errors[] = 'Can not to create task. Empty task name given';
		}

		$tm = new ATaskManager();

		//1. create new task
		$task_id = $tm->addTask(
			array( 'name' => $task_name,
					'starter' => 1, //admin-side is starter
					'created_by' => $this->user->getId(), //get starter id
					'status' => 1, // shedule it!
					'start_time' => date('Y-m-d H:i:s'),
					'last_time_run' => '0000-00-00 00:00:00',
					'progress' => '0',
					'last_result' => '0',
					'run_interval' => '0',
					'max_execution_time' => '0'
			)
		);
		if(!$task_id){
			$this->errors = array_merge($this->errors,$tm->errors);
			return false;
		}

		//create step for table backup
		if($data['backup'] ){
			$step_id = $tm->addStep( array(
				'task_id' => $task_id,
				'sort_order' => 1,
				'status' => 1,
				'last_time_run' => '0000-00-00 00:00:00',
				'last_result' => '0',
				'max_execution_time' => '0',
				'controller' => 'task/tool/backup/dumptables',
				'settings' => array(
								'tables' => $data['backup'],
								'sql_dump_mode'=> $data['sql_dump_mode']
								)
			));

			if(!$step_id){
				$this->errors = array_merge($this->errors,$tm->errors);
				return false;
			}
		}

		//create step for files backup
		if($data['backup_files']){
			$step_id = $tm->addStep( array(
										'task_id' => $task_id,
										'sort_order' => 2,
										'status' => 1,
										'last_time_run' => '0000-00-00 00:00:00',
										'last_result' => '0',
										'max_execution_time' => '0',
										'controller' => 'task/tool/backup/backupfiles',
										'settings' => array('interrupt_on_step_fault' =>false)
			));

			if(!$step_id){
				$this->errors = array_merge($this->errors,$tm->errors);
				return false;
			}

		}
		//create step for backup config-file
		if($data['backup_config']){
			$step_id = $tm->addStep( array(
				'task_id' => $task_id,
				'sort_order' => 3,
				'status' => 1,
				'last_time_run' => '0000-00-00 00:00:00',
				'last_result' => '0',
				'max_execution_time' => '0',
				'controller' => 'task/tool/backup/backupconfig'
			));
			if(!$step_id){
				$this->errors = array_merge($this->errors,$tm->errors);
				return false;
			}
		}

		//create last step for compressing backup

		$step_id = $tm->addStep( array(
			'task_id' => $task_id,
			'sort_order' => 4,
			'status' => 1,
			'last_time_run' => '0000-00-00 00:00:00',
			'last_result' => '0',
			'max_execution_time' => '0',
			'controller' => 'task/tool/backup/compressbackup'
		));
		if(!$step_id){
			$this->errors = array_merge($this->errors,$tm->errors);
			return false;
		}else{
			$task_details = $tm->getTaskById($task_id);
			if($task_details){
				return $task_details;
			}else{
				$this->errors[] = 'Can not to get task details for execution';
				$this->errors = array_merge($this->errors,$tm->errors);
				return false;
			}
		}

	}
}
