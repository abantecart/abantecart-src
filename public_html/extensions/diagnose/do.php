<?php
/*
 IMPORTANT note about this script!!!!

 This script is provided and intended for AbanteCart.com support only. 
 Please do not modify this script and remove it after suport is completed. 
*/

/* Implment on live
if ($_SERVER['REMOTE_ADDR'] != "96.56.229.11") {
	respond("You are not authorized to access this page!");
}
*/

$curr_dir = dirname(__FILE__);
$main_dir = $curr_dir . '/../../';
define('DIR_ROOT', $main_dir);
define('DIR_CORE', DIR_ROOT . '/core/');

$validated = false;
if(!isset($_GET['t'])) {
	respond('Bad request!');
}
$rtoken = $_GET['t'];

//check if token is available in the file
if ( file_exists( $curr_dir."/token.php" ) ) {
	if((@include $curr_dir."/token.php" ) === false){
	    respond('Bad request!');
	}	
	if(!isset($token) || !$token){
	    respond('Bad request!');	
	} else if ($token != $rtoken) {
	    respond('Bad tokens!');		
	} else {
		$validated = true;
	}
}

//try to include all libraries and report issues
if((@include DIR_CORE.'version.php') === false)
{
    echo "error including version.php";
}
define('VERSION', MASTER_VERSION . '.' . MINOR_VERSION . '.' . VERSION_BUILT);
define('DIR_SYSTEM', DIR_ROOT . '/system/');
define('DIR_IMAGE', DIR_ROOT . '/image/');
define('DIR_DOWNLOAD', DIR_ROOT . '/download/');
define('DIR_DATABASE', DIR_ROOT . '/core/database/');
define('DIR_CONFIG', DIR_ROOT . '/core/config/');
define('DIR_CACHE', DIR_ROOT . '/system/cache/');
define('DIR_LOGS', DIR_ROOT . '/system/logs/');
define('DIR_RESOURCE', DIR_ROOT . '/resources/');

if((@include DIR_ROOT.'system/config.php') === false){
    echo "error including system/config.php";
}
if((@include DIR_CORE.'lib/exceptions.php') === false){
    echo "error including lib/exceptions.php";
}
if((@include DIR_CORE.'lib/error.php') === false){
    echo "error including lib/error.php";
}
if((@include DIR_CORE.'lib/warning.php') === false){
    echo "error including lib/warning.php";
}
if((@include DIR_CORE.'engine/registry.php') === false){
    echo "error including registry.php";
}
if((@include DIR_CORE.'helper/utils.php') === false){
    echo "error including utils.php";
}
if((@include DIR_CORE.'lib/response.php') === false){
    echo "error including response.php";
}
if((@include DIR_CORE.'lib/cache.php') === false){
    echo "error including cache.php";
}
if((@include DIR_CORE.'lib/config.php') === false){
    echo "error including config.php";
}
if((@include DIR_CORE.'lib/db.php') === false){
    echo "error including db.php";
}
if((@include DIR_CORE.'lib/json.php') === false){
    echo "error including json.php";
}

//load registery and required classes
// Registry
$registry = Registry::getInstance();

// Response
$response = new AResponse();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);
unset($response);

// Database
$registry->set('db', new ADB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE));

// Cache
$registry->set('cache', new ACache());

// Config
$config = new AConfig($registry);
$registry->set('config', $config);

//check if we allow connection for token or extension is enabled 
if(!$validated) {
	if(!$config->get('diagnose_status') || !$config->get('mp_token')) {
		respond('Not enabled!'); 
	} else if ($config->get('mp_token') != $rtoken) {
		//not valid token
	    respond('Bad tokens!');	
	}
}

$responce = array();

#1. Check if there's a query string
if(isset($_GET['q'])){

	if ($_GET['q'] == 'php') {
		$theinfo = php_details(); 
		$responce['php'] = gzcompress($theinfo, 9);
	}

	if ($_GET['q'] == 'files') {
		$theinfo = get_files(); 
		$responce['files'] = $theinfo; //gzcompress($theinfo, 9);
	}


echo_array($responce);
	
	echo AJson::encode($responce);
exit;



	if(preg_match('/htdocs/', $_GET['q'])){
		header("Location: " . $PHP_SELF);
	}


	if(preg_match("/\.\./", $_GET['q'], $matches, PREG_OFFSET_CAPTURE, 3)){
		print_error("ERROR accessing ..... ".$_GET['q']); 
	}

	//echo "<br>";


	
}

else{

	$t=mysql_query("select version() as ve");
	echo mysql_error();
	$r=mysql_fetch_object($t);
	
	
	if(preg_match('/Zen/', PROJECT_VERSION_NAME)){
		echo '<li>';
		echo '<strong>CART VERSION NAME:</strong>' . PROJECT_VERSION_NAME . '<br />';
		echo '</li>';
		echo '<li>';
		echo '<strong>CART VERSION MAJOR:</strong>' . PROJECT_VERSION_MAJOR . '<br />';
		echo '</li>';
		echo '<li>';
		echo '<strong>CART VERSION MINOR:</strong>' . PROJECT_VERSION_MINOR. '<br />';
		echo '</li>';
		
	}
	
	else{
		echo '<li>';
		echo '<strong>CART VERSION :</strong>' . PROJECT_VERSION . '<br />';
		echo '</li>';
	}
	
	
	
	
	echo '<li>';
	echo '<strong>MYSQL VERSION :</strong> ' . $r->ve;
	echo '</li><li>';
	echo '<strong>PHP VERSION : </strong>' . phpversion();
	echo '</li><li>';
	echo '<strong>DOCUMENT ROOT : </strong>' . $_SERVER['DOCUMENT_ROOT'];
	echo '</li><li>';
	echo '<strong>REQUEST URI : </strong>' . $_SERVER['REQUEST_URI'];
	echo '</li><li>';
	echo '<strong>SERVER SOFTWARE : </strong>' . $_SERVER['SERVER_SOFTWARE'];
	echo '</li><li>';
	echo '<strong>SERVER SIGNATURE : </strong>' . $_SERVER['SERVER_SIGNATURE'];
	echo '</li><li>';
	echo '<strong>SERVER PROTOCOL : </strong>' . $_SERVER['SERVER_PROTOCOL'];
	echo '</li><li>';
	echo '<strong>SERVER PORT : </strong>' . $_SERVER['SERVER_PORT'];
	echo '</li><li>';
	echo '<strong>SERVER HOST : </strong>' . $_SERVER['HTTP_HOST'];
	echo '</li><li>';
	echo '<strong>USER AGENT : </strong>' . $_SERVER['HTTP_USER_AGENT'];
	echo '</li><li>';
	echo '<strong>GATEWAY INTERFACE : </strong>' . $_SERVER['GATEWAY_INTERFACE'];
	echo '</li><li>';
	echo '<strong>SERVER NAME : </strong>' . $_SERVER['SERVER_NAME'];
	echo '</li><li>';
	echo '<strong>SERVER ADMIN : </strong>' .$_SERVER['SERVER_ADMIN'];
	echo '</li>';
	
	phpinfo();
	
}

function php_details(){
	ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
	return $phpinfo;
}

function get_files($req_file = '') {

	$out = array();
	$str_path = DIR_ROOT;
	if ($file) {
		//specific request 
		$str_path = DIR_ROOT . $req_file;
	}
	
	if(is_dir($str_path)){
		#DIRECTORY DISPLAY
		// open the specified directory and check if it's opened successfully
		$handle = opendir($str_path);
		$file_array = array();
		   // keep reading the directory entries 'til the end
		   while (false !== ($file = readdir($handle))) {
			  // just skip the reference to current and parent directory
			  if ($file != "." && $file != "..") {
				  if ($file != ".") {
					 if( ($req_file == ".") && ($file == "..") ) continue;
					 if (is_dir($str_path.'/'.$file)) {
						// found a directory, do something with it?
						$file .="/";
					 }
					 $file_array[] = $file;
				 }
			  }
		   }
		   sort($file_array);
		   foreach($file_array as $file){
		   		if(substr($file, strlen($file)-1,1) == "/") $filepath = substr($file, 0, strlen($file)-1); 
				else $filepath = $file;
				$out[] = $filepath;
		   }
		
		   // ALWAYS remember to close what you opened
		   closedir($handle);
	}
	
	elseif(file_exists($str_path)){
		//Check if it is binary file 
		if (IsBinary($str_path)) {
			//Display file size only 
			$out[$str_path] = filesize($str_path);
		} 
		else { 
			//return file back
			if ($fp = fopen($str_path, 'r')) {
	   			$content = '';
	   			// keep reading until there's nothing left 
	   			while ($line = fread($fp, 1024)) {
	      			$content .= $line;
	   			}
				$out[$str_path] = $content;
			}
		}
	}
	else {
		return "Error accessing directory/file $file";
	}
}


function respond ( $text ) {
	echo $text;
	exit;
}

function print_error($str){
	die("<font color='red'>".$str."</font>");
}

function IsBinary($file) 
{ 
  if (file_exists($file)) { 
    if (!is_file($file)) return 0; 

    $fh  = fopen($file, "r"); 
    $blk = fread($fh, 512); 
    fclose($fh); 
    clearstatcache(); 

    return ( 
      0 or substr_count($blk, "^ -~", "^\r\n")/512 > 0.3 
        or substr_count($blk, "\x00") > 0 
    ); 
  } 
  return 0; 
} 

?>
