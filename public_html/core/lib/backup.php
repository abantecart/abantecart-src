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
	/**
	 * @var string - mode of sql dump. can be "data_only" and "recreate"
	 */
	public  $sql_dump_mode = 'data_only';
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
		$this->backup_name = $name;
		//Create a tmp directory with backup name in admin/system/backup/ (add config constant DIR_BACKUP with path in init.php)
		//Create subdirectory /code and  /data
		$this->backup_dir = DIR_BACKUP . $this->backup_name.'/';


		if(!is_dir($this->backup_dir)){
			$result = mkdir($this->backup_dir);

			if(!$result){
				$this->error = "Error: Can't create directory ".$this->backup_dir." during backup.";
				$this->log->write($this->error);
				$this->message->saveError('Backup Error',$this->error);
				$this->backup_dir = $this->backup_name = null;
			}
			chmod($this->backup_dir,0777);
		}

		if(!is_dir($this->backup_dir.'code')){
			mkdir($this->backup_dir.'code');
			chmod($this->backup_dir.'code',0777);
		}

		if(!is_dir($this->backup_dir.'data')){
			mkdir($this->backup_dir.'data');
			chmod($this->backup_dir.'data',0777);
		}
  	}


	public function __get($key) {
		return $this->registry->get ( $key );
	}

	public function __set($key, $value) {
		$this->registry->set ( $key, $value );
	}

	public function getBackupName() {
		return $this->backup_name;
	}
	public function setBackupName($name) {
		return $this->backup_name = $name;
	}

	/**
	 * @param array $tables - tables list
	 * @param string $dump_file - path of file with sql dump
	 * @return bool|string - path of dump file or false
	 */
	public function dumpTables($tables = array(), $dump_file=''){
		if(!$tables || !is_array($tables) || !$this->backup_dir){
			return false;
		}

		$prefix_len = strlen(DB_PREFIX);

		if( !$dump_file) {
			$dump_file = $this->backup_dir.'data/' .DB_DATABASE.'_dump_'. date("Y-m-d-H-i-s") . '.sql';
		}

		$file = fopen($dump_file,'w');
		if(!$file){
			$error_text = 'Error: Cannot create file as "'.$dump_file.'" during sql-dumping. Check is it writable.';
			$error = new AError($error_text);
			$error->toLog()->toDebug();
			return false;
		}

		// make dump
		foreach ($tables as $table) {
			//if database prefix present - dump all abantecart tables. If not - dump all
			if (DB_PREFIX && substr($table,0,$prefix_len) != DB_PREFIX ) {
				continue;
			}

			if($this->sql_dump_mode == 'data_only'){
				fwrite($file,"TRUNCATE TABLE `" . $table . "`;\n\n");
			}elseif($this->sql_dump_mode == 'recreate'){

				$sql = "SHOW CREATE TABLE `" . $table . "`;";
				$result = $this->db->query($sql);
				$ddl = $result->row['Create Table'];

				fwrite($file,"DROP TABLE IF EXISTS `" . $table . "`;\n\n");
				fwrite($file, $ddl . "\n\n");
			}

			// dump data with using "INSERT"
			$query = $this->db->query("SELECT * FROM `" . $table . "`");
			foreach ($query->rows as $result) {
				$fields = '';
				foreach (array_keys($result) as $value) {
					$fields .= '`' . $value . '`, ';
				}
				$values = '';

				foreach (array_values($result) as $value) {
					$value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
					$value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
					$value = str_replace('\\', '\\\\', $value);
					$value = str_replace('\'', '\\\'', $value);
					$value = str_replace('\\\n', '\n', $value);
					$value = str_replace('\\\r', '\r', $value);
					$value = str_replace('\\\t', '\t', $value);

					$values .= '\'' . $value . '\', ';
				}
				fwrite($file, 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n");
			}

			fwrite($file,"\n\n");
		}

		fclose($file);
		chmod($dump_file,0777);

		return $dump_file;
	}


	public function dumpDatabase() {
		if(!$this->backup_dir){
			return FALSE;
		}

		$this->load->model('tool/backup');
		$table_list = $this->model_tool_backup->getTables();

		if( !$this->dumpTables($table_list) ){
			$this->error = "Error: Can't create sql dump of database during backup";
			$this->log->write($this->error);
			$this->message->saveError('SQL-Backup Error',$this->error);
			return false;
		}

		return true;
	}

	public function dumpTable( $table_name ) {
		if(!$this->backup_dir || trim($table_name)){
			return FALSE;
		}

		$table_name = $this->registry->get('db')->escape($table_name); // for any case

		$backupFile = $this->backup_dir.'data/' .DB_DATABASE.'_'.$table_name.'_dump_'. date("Y-m-d-H-i-s") . '.sql';

		$result = $this->dumpTables($tables = array($table_name), $backupFile);

		if(!$result){
			$this->error = "Error: Can't create sql dump of database table during backup";
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			return false;
		}
		return true;
	}
	
	public function backupDirectory ( $dir_path, $remove=false  ) {
		if(!$this->backup_dir){
			return FALSE;
		}

		if(!is_dir($dir_path)){
			$this->error = "Error: Can't backup directory ".$dir_path.' because is not a directory!';
			$this->log->write($this->error);
			$this->message->saveError('Backup Error',$this->error);
			return false;
		}

		$path = pathinfo($dir_path, PATHINFO_BASENAME);

		if(!is_dir($this->backup_dir.'code/'.$path)){
			mkdir($this->backup_dir.'code/'.$path,0777,TRUE); // it need for nested dirs, for example code/extensions
		}
		
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
	
	public function archive($archive_filename, $src_dir, $filename ) {
		//Archive the backup to DIR_BACKUP, delete tmp files in directory $this->backup_dir 
		//And create record in the database for created archive. 
		//generate errors: No space on device (log to message as error too), No permissons, Others 
		//return Success or failed.

		compressTarGZ($archive_filename, $src_dir.$filename);

		if(!file_exists($archive_filename)){
			$this->error = 'Error: cannot to pack ' . $archive_filename."\n ";
			$this->log->write($this->error);
			$this->message->saveError('Backup Compress Error',$this->error);
			return false;
		}else{
			@chmod($archive_filename,0777);
		}
		//remove source folder after compress
		$this->_removeDir( $src_dir.$filename );
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
	public function _removeDir( $dir='' ) {
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

	function _copyDir($src, $dest) {
		// If source is not a directory stop processing
		if (!is_dir($src)) return false;
		//prevent recursive copying
		if(rtrim($src,'/') == rtrim($this->backup_dir,'/')){ return false; }

		// If the destination directory does not exist create it
		if (!is_dir($dest)) {
			if (!mkdir($dest)) {
				// If the destination directory could not be created stop processing
				return false;
			}
		}

		// Open the source directory to read in files
		$i = new DirectoryIterator($src);
		foreach ($i as $f) {
			/**
			 * @var $f DirectoryIterator
			 */
			if ($f->isFile()) {
				copy($f->getRealPath(), "$dest/" . $f->getFilename());
			} else if (!$f->isDot() && $f->isDir()) {
				$this->_copyDir($f->getRealPath(), "$dest/$f");
			}
		}
		return true;
	}
}
