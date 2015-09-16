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
 * @param $registry
 * @param $mode ('log', 'return') 
 * @return array
 *
 * Note: This is English text only. Can be call before database and languges are loaded
 */

function run_system_check($registry, $mode = 'log'){
	$mlog = $counts = array();
	
	$mlog[] = check_install_directory($registry);
	$mlog = array_merge($mlog, check_file_permissions($registry));	
	$mlog = array_merge($mlog, check_php_configuraion($registry));	
	$mlog = array_merge($mlog, check_server_configuration($registry));	
	
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
	    	'body' => $index . ' file is writable. It is recommended to set read and execute modes (644 or 755) for this file to keep it secured and running properly!',
	    	'type' => 'W'	    
	    );
	}

	if (is_writable(DIR_SYSTEM . 'config.php')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect config.php file permissions',
	    	'body' => DIR_SYSTEM . 'config.php' . ' file needs to be set to read and execute modes (644 or 755) to keep it secured from editing!',
	    	'type' => 'W'	    
	    );
	}

	//if cache is anabled
	if( $registry->get('config')->get('config_cache_enable') ) {
		$cache_files = get_all_files_dirs(DIR_SYSTEM . 'cache/');
		$cache_message = '';
		foreach($cache_files as $file) {
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
	    if (!is_writable($file)) {
	    	$image_message .= $file."<br/>";
	    }	
	}
	if($image_message){
	    $ret_array[] = array(
	    	'title' => 'Incorrect image files permissions',
	    	'body' => "Following files do not have write permissions. AbanteCart thumbnail images will not function properly. <br/>" . $cache_message,
	    	'type' => 'W'	    
	    );	
	}

	if (!is_writable(DIR_ROOT . '/admin/system/backup')) {
	    $ret_array[] = array(
	    	'title' => 'Incorrect backup directory permission',
	    	'body' => DIR_ROOT . '/admin/system/backup' . ' directory needs to be set to full permissions(777)! AbanteCart backups and upgrade will not work.',
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

	//if SEO is anabled
	if( $registry->get('config')->get('enable_seo_url') ) {	
		$htaccess = DIR_ROOT . '/.htaccess';
		if(!file_exists($htaccess)) {
		    $ret_array[] = array(
		    	'title' => 'SEO URLs does not work',
		    	'body' => $htaccess.' file is missing. SEO URL functionality will not work. Check the <a href="http://docs.abantecart.com/pages/settings/system.html">manual for SEO URL setting</a> ',
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
		    return array('bytes' => $bytes, 'human' => sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class]);
		} catch (Exception $e) {
			return array();
		}
	} else {
		return array();
	}
}


