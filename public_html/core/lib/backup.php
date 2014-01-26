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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

final class ABackup {
	private $backup_name;
	private $backup_dir;
	private $registry;
	private $log;
	private $message;
	public  $error;

  	public function __construct( $name ) {
		$this->registry = Registry::getInstance();
		$this->log = $this->registry->get('log');
		$this->message = $this->registry->get('messages');

  		//Add [date] snapshot to the name and validate if archive is already used.
  		//Return error if archive can not be created 
		$this->backup_name = $name .'_'. date('Y-m-d-H-i-s');
		//Create a tmp directory with backup name in admin/system/backup/ (add config constant DIR_BACKUP with path in init.php)
		//Create subdirectory /code and  /data
		$this->backup_dir = DIR_BACKUP . $this->backup_name.'/';
		  
		$result = mkdir($this->backup_dir);

		if(!$result){
			$this->error = "Error: Can't create directory ".$this->backup_dir." during backup.";
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			
			$this->backup_dir = $this->backup_name = null;
		}
		chmod($this->backup_dir,0777);
		mkdir($this->backup_dir.'code');
		chmod($this->backup_dir.'code',0777);
		mkdir($this->backup_dir.'data');
		chmod($this->backup_dir.'data',0777);

  	}

	public function getBackupName() {
		return $this->backup_name;
	}

	public function dumpDatabase() {
		if(!$this->backup_dir){
			return FALSE;
		}

		$backupFile = $this->backup_dir.'data/' .DB_DATABASE.'_dump_'. date("Y-m-d-H-i-s") . '.sql';
		$command = "mysqldump --opt -h " . DB_HOSTNAME . " -u " . DB_USERNAME . " -p" . DB_PASSWORD . " " . DB_DATABASE . " > " . $backupFile;
		if(isFunctionAvailable('system')){
			system($command);
		}

		if(!file_exists($backupFile)){
			$this->error = "Error: Can't create sql dump of database during backup";
			$this->log->write($this->error);
			$this->message->saveError('SQL-Backup Error',$this->error);
            if(isFunctionAvailable('system')){
			    return false;
            }
		}
		chmod($backupFile,0777);
		return true;
	}

	public function dumpTable( $table_name ) {
		if(!$this->backup_dir || trim($table_name)){
			return FALSE;
		}

		$table_name = $this->registry->get('db')->escape($table_name); // for any case

		$backupFile = $this->backup_dir.'data/' .DB_DATABASE.'_'.$table_name.'_dump_'. date("Y-m-d-H-i-s") . '.sql';
		$command = "mysqldump --opt -h " . DB_HOSTNAME . " -u " . DB_USERNAME . " -p" . DB_PASSWORD . " " . DB_DATABASE . "  ".$table_name." > " . $backupFile;
		$result = null;
        if(isFunctionAvailable('system')){
			$result = system($command);
		}
		if(!$result){
			$this->error = "Error: Can't create sql dump of database table during backup";
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			return false;
		}
		chmod($backupFile,0777);
		return true;
	}
	
	public function backupDirectory ( $dir_path, $remove=true  ) {
		if(!$this->backup_dir){
			return FALSE;
		}

		if(!is_dir($dir_path)){
			$this->error = "Error: Can't backup directory ".$dir_path.' because is not a directory!';
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			return false;
		}

		$path = str_replace(DIR_ROOT.'/','',$dir_path);
		mkdir($this->backup_dir.'code/'.$path,0777,TRUE); // it need for nested dirs, for example code/extensions
		
		if(file_exists($this->backup_dir.'code/'.$path)){
			if($path){
				$this->_removeDir($this->backup_dir.'code/'.$path);  // delete stuck dir
			}				
		}
		if($remove){

			$result = rename($dir_path, $this->backup_dir.'code/'.$path);
		}else{
			$result = $this->_copyDir($dir_path, $this->backup_dir.'code/'.$path);
		}

		if(!$result){
			$this->error = "Error: Can't move directory \"".$dir_path. " to backup folder \"".$this->backup_dir."code/".$path."\" during backup\n";
			if(!is_writable($dir_path)){
				$this->error .= "Check write permission for directory \"".$dir_path. "";
			}
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			return false;
		}

		//Copy directory with content to the directory(s) with the same path starting from $this->backup_dir . '/code/'
		// Call $this->backupFile if needed. 
		//generate errors: No space on device (log to message as error too), No permissons, Others  
		//return Success or failed.
		return true;
	}

	public function backupFile ( $file_path, $remove=true ) {
		if(!$this->backup_dir || !$file_path){
			return FALSE;
		}
		$base_name = pathinfo($file_path,PATHINFO_BASENAME);
		$path = str_replace(DIR_ROOT.'/','',$file_path);
		$path = str_replace($base_name,'',$path);

		if($path){ //if nested folders presents in path
			if(!file_exists($this->backup_dir.'code/'.$path)){
				$result = mkdir($this->backup_dir.'code/'.$path,0777,TRUE); // create dir with nested folders
			}else{
				$result = true;
			}	
			if(!$result){
				$this->error = "Error: Can't create directory ".$this->backup_dir.'code/'.$path. " during backup";
				$this->log->write($this->error);
				$this->message->saveError('Backup Error',$this->error);
				return false;
			}
			if(!is_writable($this->backup_dir.'code/'.$path)){
				$this->error = "Error: Directory ".$this->backup_dir.'code/'.$path. ' is not writable for backup.';
				$this->log->write($this->error);
				$this->message->saveError('Backup Error',$this->error);
				return false;
			}
		}
			// move file
			if( file_exists($this->backup_dir.'code/'.$path.$base_name)){
				@unlink($this->backup_dir.'code/'.$path.$base_name); // delete stuck file
			}
			if($remove){
				$result = rename($file_path, $this->backup_dir.'code/'.$path.$base_name);
			}else{
				$result = copy($file_path, $this->backup_dir.'code/'.$path.$base_name);
			}
			if(!$result){
				$this->error = "Error: Can't move file ".$file_path. ' into '.$this->backup_dir.'code/'.$path.'during backup.';
				$this->log->write($this->error);
				$this->message->saveError('Backup Error',$this->error);
				return false;
			}

	return true;
	}
	
	public function archive($tar_filename, $tar_dir, $filename ) {
		//Archive the backup to DIR_BACKUP, delete tmp files in directory $this->backup_dir 
		//And create record in the database for created archive. 
		//generate errors: No space on device (log to message as error too), No permissons, Others 
		//return Success or failed.

		$command = 'tar -C ' . $tar_dir . ' -czvf ' . $tar_filename . ' ' . $filename. ' > /dev/null';
		if(isFunctionAvailable('system')){
			system($command,$exit_code);
		}else{
			$exit_code = 1;
		}


		if ( $exit_code ) {
			$this->registry->get('load')->library('targz');
			$targz = new Atargz();
		    $targz->makeTar($tar_dir.$filename,$tar_filename);
		}

		if(!file_exists($tar_filename)){
			$this->error = 'Error: cannot to pack ' . $tar_filename."\n Exit code:". $exit_code;
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			return false;
		}
		@chmod($tar_filename,0777);
		$this->_removeDir( $tar_dir.$filename );
		return true;
	}


	// Future:  1. We will add methods to brows and restore backup. 
	// 			2. Incremental backup for the database changes. 

    /**
     * method removes non-empty directory (use it carefully)
     *
     * @param string $dir
     * @return boolean
     */
	private function _removeDir( $dir='' ) {
			if ( is_dir($dir) ) {
				$objects = scandir($dir);
				foreach ( $objects as $obj ) {
					if ( $obj != "." && $obj != ".." ) {
						@chmod($dir . "/" . $obj,0777);
						$err = is_dir($dir . "/" . $obj) ? $this->_removeDir($dir . "/" . $obj) : unlink($dir . "/" . $obj);
						if ( ! $err ) {
							$this->error = "Error: Can't to delete file or directory: '".$dir . "/" . $obj."'.";
							$this->message->saveError('Backup Error',$this->error);
							$this->log->write($this->error);
							return false;
						}
					}
				}
				reset($objects);
				rmdir($dir);
				return true;
			} else {
				return $dir;
			}
	}


	function _copyDir($src, $dst) {
		  if (is_dir($src)) {
			if(!is_dir($dst)){
				mkdir($dst);
				chmod($dst,0777);
			}
			$files = scandir($src);
			foreach ($files as $file)
			if ($file != "." && $file != ".."){
				$this->_copyDir("$src/$file", "$dst/$file");
			}
		  }elseif(file_exists($src)){
			   copy($src, $dst);
			   chmod($dst,0777);
		  }
	return true;
	}

}
?>