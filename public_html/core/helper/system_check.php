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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

/**
 * Main driver for running system check 
 * @since 1.2.4
 * @param  Registry $registry
 * @param string $mode ('log', 'return')
 * @return array
 *
 * Note: This is English text only. Can be call before database and languages are loaded
 */

function run_system_check($registry, $mode = 'log'){
	$mlog = $counts = array();
	
	$mlog[] = check_install_directory($registry);
	$mlog = array_merge($mlog, check_file_permissions($registry));	
	$mlog = array_merge($mlog, check_php_configuraion($registry));	
	$mlog = array_merge($mlog, check_server_configuration($registry));	
	$mlog = array_merge($mlog, check_order_statuses($registry));
	$mlog = array_merge($mlog, check_web_access());

	$counts['error_count'] = $counts['warning_count'] = $counts['notice_count'] = 0;
	foreach($mlog as $message){
	    if($message['type'] == 'E'){
	    	if($mode == 'log') {
	    		//only save errors to the log
	    		$error = new AError($message['body']);
	    		$error->toLog()->toDebug();	
	    		$registry->get('messages')->saveError($message['title'], $message['body']);		
	    	}	
	    	$counts['error_count']++;
	    } else if ($message['type'] == 'W'){
	    	if($mode == 'log') {
	    		$registry->get('messages')->saveWarning($message['title'], $message['body']);
	    	}
	    	$counts['warning_count']++;			
	    } else if ($message['type'] == 'N'){
	    	if($mode == 'log') {
	    		$registry->get('messages')->saveNotice($message['title'], $message['body']);
	    	}
	    	$counts['notice_count']++;						
	    }
	}

	return array($mlog, $counts);
}

function check_install_directory($registry){
	//check if install dir existing. warn
	if (file_exists(DIR_ROOT . '/install')) {
	    return array(
	    	'title' => 'Security warning',
	    	'body' => 'You still have install directory present in your AbanteCart main directory. It is highly recommended to delete install directory.',
	    	'type' => 'W'
	    );
	}
}

function check_file_permissions($registry){
	//check file permissions. 
	$ret_array = array();
	$index = DIR_ROOT . '/index.php';
	if (is_writable($index) || substr(sprintf("%o",fileperms($index)), -3) == '777') {
	    $ret_array[] = array(
	    	'title' => 'Incorrect index.php file permissions',
	    	'body' => $index . ' file is writable. It is recommended to set read and execute modes for this file to keep it secured and running properly!',
	    	'type' => 'W'	    
	    );
	}

	if (is_writable(DIR_SYSTEM . 'config.php')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect config.php file permissions',
	    	'body' => DIR_SYSTEM . 'config.php' . ' file needs to be set to read and execute modes to keep it secured from editing!',
	    	'type' => 'W'	    
	    );
	}

	//if cache is enabled
	if( $registry->get('config')->get('config_cache_enable') ) {
		$cache_files = get_all_files_dirs(DIR_SYSTEM . 'cache/');
		$cache_message = '';
		foreach($cache_files as $file) {
			if(in_array(basename($file), array('index.html', 'index.html','.','','..'))){
				continue;
			}
			if (!is_writable($file)) {
				$cache_message .= $file."<br/>";
			}	
		}
		if($cache_message){
		    $ret_array[] = array(
		    	'title' => 'Incorrect cache files permissions',
		    	'body' => "Following files do not have write permissions. AbanteCart will not function properly. <br/>" . $cache_message,
		    	'type' => 'E'	    
		    );	
		}
	}

	if (!is_writable(DIR_SYSTEM . 'logs') || !is_writable(DIR_SYSTEM . 'logs/error.txt')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect log dir/file permissions',
	    	'body' => DIR_SYSTEM . 'logs' . ' directory or error.txt file needs to be set to full permissions(777)! Error logs can not be saved',
	    	'type' => 'W'	    
	    );
	}

	$image_files = get_all_files_dirs(DIR_ROOT . '/image/thumbnails/');
	$image_message = '';
	foreach($image_files as $file) {
		if(in_array(basename($file), array('index.html', 'index.html','.','','..'))){
			continue;
		}
	    if (!is_writable($file)) {
	    	$image_message .= $file."<br/>";
	    }	
	}
	if($image_message){
	    $ret_array[] = array(
	    	'title' => 'Incorrect image files permissions',
	    	'body' => "Following files do not have write permissions. AbanteCart thumbnail images will not function properly. <br/>" . $image_message,
	    	'type' => 'W'	    
	    );	
	}

	if (!is_writable(DIR_ROOT . '/admin/system')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect directory permission',
	    	'body' => DIR_ROOT . '/admin/system' . ' directory needs to be set to full permissions(777)! AbanteCart backups and upgrade will not work.',
	    	'type' => 'W'	    
	    );
	}

	if (is_dir(DIR_ROOT . '/admin/system/backup') && !is_writable(DIR_ROOT . '/admin/system/backup')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect backup directory permission',
	    	'body' => DIR_ROOT . '/admin/system/backup' . ' directory needs to be set to full permissions(777)! AbanteCart backups and upgrade will not work.',
	    	'type' => 'W'
	    );
	}

	if (is_dir(DIR_ROOT . '/admin/system/temp') && !is_writable(DIR_ROOT . '/admin/system/temp')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect temp directory permission',
	    	'body' => DIR_ROOT . '/admin/system/temp' . ' directory needs to be set to full permissions(777)!',
	    	'type' => 'W'
	    );
	}

	if (is_dir(DIR_ROOT . '/admin/system/uploads') && !is_writable(DIR_ROOT . '/admin/system/uploads')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect "uploads" directory permission',
	    	'body' => DIR_ROOT . '/admin/system/uploads' . ' directory needs to be set to full permissions(777)! Probably AbanteCart file uploads will not work.',
	    	'type' => 'W'
	    );
	}
	
	return $ret_array;
}

function check_php_configuraion($registry){
	//check if all modules and settings on PHP side are OK.
	$ret_array = array();

	if (!extension_loaded('mysql') && !extension_loaded('mysqli')) {
	    $ret_array[] = array(
	    	'title' => 'MySQL extension is missging',
	    	'body' => 'MySQL extension needs to be enabled on PHP for AbanteCart to work!',
	    	'type' => 'E'	    
	    );
	}	
	if (!ini_get('file_uploads')) {
	    $ret_array[] = array(
	    	'title' => 'File Upload Warning',
	    	'body' => 'PHP file_uploads option is disabled. File uploading will not function properly',
	    	'type' => 'W'	    
	    );
	}
	if (ini_get('session.auto_start')) {
	    $ret_array[] = array(
	    	'title' => 'Issue with session.auto_start',
	    	'body' => 'AbanteCart will not work with session.auto_start enabled!',
	    	'type' => 'E'	    
	    );
	}
	if (!extension_loaded('gd')) {
	    $ret_array[] = array(
	    	'title' => 'GD extension is missing',
	    	'body' => 'GD extension needs to be enabled in PHP for AbanteCart to work! Images will not display properly',
	    	'type' => 'E'	    
	    );
	}

	if (!extension_loaded('mbstring') || !function_exists('mb_internal_encoding')) {
	    $ret_array[] = array(
	    	'title' => 'mbstring extension is missing',
	    	'body' => 'MultiByte String extension needs to be loaded in PHP for AbanteCart to work!',
	    	'type' => 'E'	    
	    );
	}
	if (!extension_loaded('zlib')) {
	    $ret_array[] = array(
	    	'title' => 'ZLIB extension is missing',
	    	'body' => 'ZLIB extension needs to be loaded in PHP for backups to work!',
	    	'type' => 'W'	    
	    );
	}

	//check memory limit

	$memory_limit = trim(ini_get('memory_limit'));
	$last = strtolower($memory_limit[strlen($memory_limit)-1]);

    switch($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
	        $memory_limit *= 1024;
        case 'm':
	        $memory_limit *= 1024;
        case 'k':
	        $memory_limit *= 1024;
    }

	//Recommended minimal PHP memory size is 64mb
	if ($memory_limit < (64 * 1024 * 1024)) {
		$ret_array[] = array(
		        'title' => 'Memory limitation',
		        'body' => 'Low PHP memory setting. Some Abantecart features will not work with memory limit less than 64Mb! Check <a href="http://php.net/manual/en/ini.core.php#ini.memory-limit" target="_help_doc">PHP memory-limit setting</a>',
		        'type' => 'W'
		);
	}


	return $ret_array;
}

function check_server_configuration($registry){
	//check server configurations. 
	$ret_array = array();
	
	$size = disk_size( DIR_ROOT );
	//check for size to drop below 10mb
	if (isset($size['bytes']) && $size['bytes'] < 1024*10000) {
	    $ret_array[] = array(
	    	'title' => 'Critically low disk space',
	    	'body' => 'AbanteCart is running on critically low disk space of '.$size['human'].'! Increase disk size to prevent failure.',
	    	'type' => 'E'	    
	    );
	}

	//if SEO is enabled
	if( $registry->get('config')->get('enable_seo_url') ) {	
		$htaccess = DIR_ROOT . '/.htaccess';
		if(!file_exists($htaccess)) {
		    $ret_array[] = array(
		    	'title' => 'SEO URLs does not work',
		    	'body' => $htaccess.' file is missing. SEO URL functionality will not work. Check the <a href="http://docs.abantecart.com/pages/tips/enable_seo.html" target="_help_doc">manual for SEO URL setting</a> ',
		    	'type' => 'W'	    
		    );		
		}
	}

	return $ret_array;
}

function get_all_files_dirs($start_dir) {
	$iter = new RecursiveIteratorIterator(
	    new RecursiveDirectoryIterator($start_dir, RecursiveDirectoryIterator::SKIP_DOTS),
	    RecursiveIteratorIterator::SELF_FIRST,
	    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
	);
	
	$paths = array($start_dir);
	foreach ($iter as $path => $dir) {
		$paths[] = $path;
	}
	return $paths;
}

function disk_size($path){
	//check if this is supported by server
	if(function_exists('disk_free_space')){
		try {
		    $bytes = disk_free_space($path);
		    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
		    $base = 1024;
		    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
		    return array(
				    'bytes' => $bytes,
				    'human' => sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class]);
		} catch (Exception $e) {
			return array();
		}
	} else {
		return array();
	}
}

/**
 * @param Registry $registry
 * @return array
 */
function check_order_statuses($registry){

	$db = $registry->get('db');

	$order_statuses = $registry->get('order_status')->getStatuses();
	$language_id = (int)$registry->get('language')->getDefaultLanguageID();

	$query = $db->query("SELECT osi.order_status_id, osi.status_text_id
							    FROM " . $db->table('order_statuses') . " os
								INNER JOIN " . $db->table('order_status_ids') . " osi
									ON osi.order_status_id = os.order_status_id
								WHERE os.language_id = '".$language_id."'");
	$db_statuses = array();
	foreach($query->rows as $row){
		$db_statuses[(int)$row['order_status_id']] = $row['status_text_id'];
	}

	$ret_array = array();

	foreach ($order_statuses as $id => $text_id){
		if($text_id != $db_statuses[$id]){
			$ret_array[] = array(
						        'title' => 'Incorrect order status with id '.$id,
						        'body' => 'Incorrect status text id for order status #'.$id.'. Value must be "'.$text_id.'" ('.$db_statuses[$id].'). Please check data of tables '.$db->table('order_status_ids').' and '.$db->table('order_statuses'),
						        'type' => 'W'
						    );
		}
	}


 return $ret_array;
}

/**
 * function checks restricted areas
 */
function check_web_access(){

	$areas = array(
			'system' => array('.htaccess', 'index.php'),
			'resources/download' => array('.htaccess'),
			'download' => array('index.html'),
			'admin' => array('.htaccess', 'index.php'),
			'admin/system' => array('.htaccess','index.html')
	);

	$ret_array = array();

	foreach($areas as $subfolder=>$rules){
		$dirname = DIR_ROOT.'/'.$subfolder;
		if(!is_dir($dirname)){ continue;}

		foreach($rules as $rule){
			$message = '';
			switch($rule){
				case '.htaccess':
					if(!is_file($dirname.'/.htaccess')){
						$message = 'Restricted directory '.$dirname.' have public access. It is highly recommended to create .htaccess file and forbid access. ';
					}
					break;
				case 'index.php':
					if(!is_file($dirname.'/index.php')){
						$message = 'Restricted directory '.$dirname.' does not contain index.php file. It is highly recommended to create it.';
					}
					break;
				case 'index.html':
					if(!is_file($dirname.'/index.html')){
						$message = 'Restricted directory '.$dirname.' does not contain empty index.html file. It is highly recommended to create it.';
					}

					break;
				default:
					break;
			}
			if($message){
				$ret_array[] = array (
						'title' => 'Security warning ('.$subfolder.', '.$rule.')',
						'body'  => $message,
						'type'  => 'W'
				);
			}
		}
	}
	return $ret_array;
}


/**
 * @param $registry
 * @param string $mode
 * @return array
 */

function run_critical_system_check($registry, $mode = 'log'){

	$mlog = array();
	$mlog[] =  check_session_save_path($registry);

	$output = array();

	foreach($mlog as $message){
		if($message['body']){
			if ($mode == 'log'){
				//only save errors to the log
				$error = new AError($message['body']);
				$error->toLog()->toDebug();
				$registry->get('messages')->saveError($message['title'], $message['body']);
			}
		$output[] = $message;
		}
	}

	return $output;
}

/**
 * @return array
 */
function check_session_save_path(){
	$savepath = ini_get('session.save_path');
	if(!is_writable($savepath)){
		return array(
			        'title' => 'Session save path is not writable! ',
			        'body' => 'Your server is unable to create a session necessary for AbanteCart functionality. Check logs for exact error details and contact your hosting support administrator to resolve this error.'
		);
	}
	return array();
}
