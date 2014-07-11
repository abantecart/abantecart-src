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
class ControllerJobToolBackup extends AController {
	private $error = array();

	public function dumpTables(){

		$bkp = new ABackup('manual_backup');

		if(has_value($this->request->get['sql_dump_mode'])){
			$bkp->sql_dump_mode = $this->request->get['sql_dump_mode'];
		}

		if(has_value($this->request->get['table_list'])){
			$table_list = $this->request->get['table_list'];
		}

		if(!$table_list){
			$this->loadModel('tool/backup');
			$table_list = $this->model_tool_backup->getTables();
		}

		$result = $bkp->dumpTables($table_list);

		if($result){
			$this->load->library('json');
			$this->response->addJSONHeader();
			$output = array('result' => true);
			$this->response->setOutput( AJson::encode($output) );
		}else{
			$error = new AError('dump tables error');
			return $error->toJSONResponse('APP_ERROR_402',
									array( 'error_text' => $bkp->error,
										'reset_value' => true
									));
		}





	}

	public function backupFiles(){

		$bkp = new ABackup('manual_backup');
		$result = $bkp->backupDirectory(DIR_ROOT, false);

		if($result){
			$this->load->library('json');
			$this->response->addJSONHeader();
			$output = array('result' => true);
			$this->response->setOutput( AJson::encode($output) );
		}else{
			$error = new AError('files backup error');
			return $error->toJSONResponse('APP_ERROR_402',
									array( 'error_text' => $bkp->error,
										'reset_value' => true
									));
		}

	}


	public function backupConfig(){

		$bkp = new ABackup('manual_backup');
		$result = $bkp->backupFile(DIR_ROOT . '/system/config.php', false);

		$output = array('result' => $result ? true : false);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput( AJson::encode($output) );
	}

	public function CompressBackup(){

		$bkp = new ABackup('manual_backup');
		$arc_basename =  DIR_BACKUP . $bkp->getBackupName();
		if(is_file($arc_basename.'.tar')){
			unlink($arc_basename.'.tar');
		}
		if(is_file($arc_basename.'.tar.gz')){
			unlink($arc_basename.'.tar.gz');
		}

		$result = $bkp->archive($arc_basename.'.tar.gz', DIR_BACKUP, $bkp->getBackupName());

		if($result){
			$this->load->library('json');
			$this->response->addJSONHeader();
			$output = array('result' => true, 'filename' => $bkp->getBackupName());
			$this->response->setOutput( AJson::encode($output) );
		}else{
			$error = new AError('compress backup error');
			return $error->toJSONResponse('APP_ERROR_402',
									array( 'error_text' => $bkp->error,
										'reset_value' => true
									));
		}

	}
	
}
