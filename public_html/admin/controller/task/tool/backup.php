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
class ControllerTaskToolBackup extends AController {
	private $error = array();

	public function dumpTables(){
		if($this->request->get['eta']>30){
			set_time_limit((int)$this->request->get['eta']*2);
		}
		$backup_name = preg_replace('[^0-9A-z_\.]','', $this->request->get['backup_name']);
		$backup_name = !$backup_name ? 'manual_backup' : $backup_name;
		$bkp = new ABackup($backup_name);

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

		if($table_list===false){
			$error_text = 'Dump tables error. Cannot obtain table list.';
			$error = new AError($error_text);
			return $error->toJSONResponse('APP_ERROR_402',
									array( 'error_text' => $error_text,
											'reset_value' => true
									));
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
									array( 'error_text' => implode("\n",$bkp->error),
										'reset_value' => true
									));
		}

	}

	public function backupContentFiles(){
		if($this->request->get['eta']>30){
			set_time_limit((int)$this->request->get['eta']+30);
		}
		$backup_name = preg_replace('[^0-9A-z_\.]','', $this->request->get['backup_name']);
		$backup_name = !$backup_name ? 'manual_backup' : $backup_name;
		$bkp = new ABackup($backup_name);
		$content_dirs = array( // white list
					'resources',
					'image',
					'download'
				);

		$result = true;
		$files = glob(DIR_ROOT.'/*', GLOB_ONLYDIR);
		foreach($files as $file){
			$res = true;
			if(is_dir($file) && in_array(basename($file),$content_dirs)){ //only dirs from white list
				$res = $bkp->backupDirectory($file, false);
			}
			$result = !$res ? $res : $result;
		}

		if($result){
			$this->load->library('json');
			$this->response->addJSONHeader();
			$output = array('result' => true);
			$this->response->setOutput( AJson::encode($output) );
		}else{
			$error = new AError('files backup error');
			return $error->toJSONResponse('APP_ERROR_402',
									array( 'error_text' => implode("\n",$bkp->error),
										'reset_value' => true
									));
		}

	}

	public function backupCodeFiles(){
		if($this->request->get['eta']>30){
			set_time_limit((int)$this->request->get['eta']+30);
		}
		$backup_name = preg_replace('[^0-9A-z_\.]','', $this->request->get['backup_name']);
		$backup_name = !$backup_name ? 'manual_backup' : $backup_name;
		$bkp = new ABackup($backup_name);
		$code_dirs = array( // white list
			'admin',
			'core',
			'storefront',
			'extensions',
			'system',
			'static_pages'
		);

		$result = true;
		$files = array_merge(glob(DIR_ROOT.'/.*'), glob(DIR_ROOT.'/*'));

		foreach($files as $file){
			if(in_array(basename($file), array('.','..'))){ continue; } //those filenames give glob for hidden files (see above)
			$res = true;
			if(is_file($file)){
				$res = $bkp->backupFile($file, false);
			}else if(is_dir($file) && in_array(basename($file),$code_dirs)){ //only dirs from white list
				$res = $bkp->backupDirectory($file, false);
			}
			$result = !$res ? $res : $result;
		}

		if($result){
			$this->load->library('json');
			$this->response->addJSONHeader();
			$output = array('result' => true);
			$this->response->setOutput( AJson::encode($output) );
		}else{
			$error = new AError('files backup error');
			return $error->toJSONResponse('APP_ERROR_402',
									array( 'error_text' => implode("\n",$bkp->error),
										'reset_value' => true
									));
		}

	}


	public function backupConfig(){

		$backup_name = preg_replace('[^0-9A-z_\.]','', $this->request->get['backup_name']);
		$backup_name = !$backup_name ? 'manual_backup' : $backup_name;
		$bkp = new ABackup($backup_name);
		$result = $bkp->backupFile(DIR_ROOT . '/system/config.php', false);

		$output = array('result' => $result ? true : false);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput( AJson::encode($output) );
	}

	public function CompressBackup(){
		if($this->request->get['eta']>30){
			set_time_limit((int)$this->request->get['eta']+30);
		}
		$backup_name = preg_replace('[^0-9A-z_\.]','', $this->request->get['backup_name']);
		$backup_name = !$backup_name ? 'manual_backup' : $backup_name;
		$bkp = new ABackup($backup_name);

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
									array( 'error_text' => implode("\n",$bkp->error),
										'reset_value' => true
									));
		}

	}
	
}
