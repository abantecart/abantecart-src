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

function isFunctionAvailable($func_name) {
	return function_exists($func_name);
}

/*
 * prepare prices and other floats for database writing,, based on locale settings of number formatting
 * */
function preformatFloat($value, $decimal_point = '.') {
	if ($decimal_point != '.') {
		$value = str_replace('.', '~', $value);
		$value = str_replace($decimal_point, '.', $value);
	}
	return (float)preg_replace('/[^0-9\-\.]/', '', $value);
}

/*
 * prepare integer for database writing
 * */
function preformatInteger($value) {
	return (int)preg_replace('/[^0-9\-]/', '', $value);
}

/*
 * prepare string for text id 
 * */
function preformatTextID($value) {
	return strtolower(preg_replace("/[^A-Za-z0-9_]/", "", $value));
}

/**
 * format money float based on locale
 * @since 1.1.8
 * @param $value
 * @param $mode (no_round => show number with real decimal, hide_zero_decimal => remove zeros from decimal part) 
 * @return string
 */

function moneyDisplayFormat($value, $mode = 'no_round'){
	$registry = Registry::getInstance();

	$decimal_point = $registry->get('language')->get('decimal_point');
	$decimal_point = !$decimal_point ? '.' : $decimal_point;

	$thousand_point = $registry->get('language')->get('thousand_point');
	$thousand_point = !$thousand_point ? '' : $thousand_point;

	$currency = $registry->get('currency')->getCurrency();
	$decimal_place = (int)$currency['decimal_place'];
	$decimal_place = !$decimal_place ? 2 : $decimal_place;

	// detect if need to show raw number for decimal points 
	// In admin, this is regardless of currency format. Need to show real number
	if ($mode == 'no_round' && $value != round($value,$decimal_place)) {
		//count if we have more decimal than currency configuration
		$decim_portion = explode('.', $value);
		if ($decimal_place < strlen($decim_portion[1])) {
			$decimal_place = strlen($decim_portion[1]);
		}
	}

	//if only zeros after decimal point - hide zeros
	if($mode == 'hide_zero_decimal' && round($value) == round($value,$decimal_place)){
		$decimal_place = 0;
	}

	return number_format((float)$value, $decimal_place, $decimal_point,$thousand_point);
}

/*
 * check that argument variable has value (even 0 is a value)  
 * */
function has_value($value) {
	if ($value !== (array)$value && $value !== '' && $value !== null ) {
		return true;
	} else if ($value === (array)$value && count($value) > 0) {
		return true;
	} else {
		return false;
	}
}

/*
 * check that argument variable has value (even 0 is a value)  
 * */
function is_serialized ($value) {
	$test_data = @unserialize($value);
	if ($value === 'b:0;' || $test_data !== false) {
	    return true;
	} else {
	    return false;
	}
}

/*
 * check that argument array is multidimensional  
 * */
function is_multi ($array) {
	if ($array === (array)$array && count($array) != count($array, COUNT_RECURSIVE)) {
	    return true;
	} else {
	    return false;
	}
}
 
/*
*
*/
/**
 * Function convert input text to alpha numeric string for SEO URL use
 * if optional parameter object_key_name (product, category, content etc) given function will return unique SEO keyword
 * @param $string_value
 * @param string $object_key_name
 * @param int $object_id
 * @param int $language_id
 * @return string
 */
function SEOEncode($string_value, $object_key_name='', $object_id=0, $language_id=0) {
	$seo_key = html_entity_decode($string_value, ENT_QUOTES, 'UTF-8');
	$seo_key = preg_replace('/[^\pL\p{Zs}0-9\s\-_]+/u', '', $seo_key);
	$seo_key = trim(mb_strtolower($seo_key));
	$seo_key = str_replace(' ',SEO_URL_SEPARATOR, $seo_key);
	if(!$object_key_name){
		return $seo_key;
	}else{
		//if $object_key_name given - check is seo-key unique and return unique
		return getUniqueSeoKeyword($seo_key, $object_key_name, $object_id, $language_id);
	}
}

/**
 * @param $seo_key
 * @param string $object_key_name
 * @param int $object_id
 * @return string
 */
function getUniqueSeoKeyword($seo_key, $object_key_name='', $object_id=0){

	$object_id=(int)$object_id;

	$registry = Registry::getInstance();
	$db = $registry->get('db');
	$sql = "SELECT `keyword`
			FROM ".$db->table('url_aliases')."
			WHERE query like '".$db->escape($object_key_name)."=%'
					AND `keyword` like '".$db->escape($seo_key)."%'";
	if($object_id){
		// exclude keyword of given object (product, category, content etc)
		$sql .= " AND query<>'".$db->escape($object_key_name)."=".$object_id."'";
	}

	$result = $db->query($sql);
	if($result->num_rows){
		foreach($result->rows as $row){
			$keywords[] = $row['keyword'];
		}

		$i=0;
		while(in_array($seo_key,$keywords) && $i<20){
			$seo_key = $seo_key.'_'.($object_id?$object_id:$i);
			$i++;
		}
	}
	return $seo_key;
}

/*
* Echo array with readable formal. Useful in debugging of array data. 
*/
function echo_array($array_data) {
	$wrapper = '<div class="debug_alert salert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>';	
	echo $wrapper;
	echo "<pre>";// $sub_table_name: ";
	print_r($array_data);
	echo'</pre>';
	echo'</div>';	
}


/*
 * returns list of files from directory with subdirectories
 */

function getFilesInDir($dir, $file_ext = '') {
	if (!is_dir($dir)) return array();
	$dir = rtrim($dir, '\\/');
	$result = array();

	foreach (glob("$dir/*") as $f) {
		if (is_dir($f)) { // if is directory
			$result = array_merge($result, getFilesInDir($f, $file_ext));
		} else {
			if ($file_ext && substr($f, -3) != $file_ext) {
				continue;
			}
			$result[] = $f;
		}
	}
	return $result;
}

//Custom function for version compare between store version and extensions
//NOTE: Function will return false if major versions donot match. 
function versionCompare($version1, $version2, $operator) {
	$version1 = explode('.', preg_replace('/[^0-9\.]/', '', $version1));
	$version2 = explode('.', preg_replace('/[^0-9\.]/', '', $version2));
	$i = 0;
	while ($i < 3) {
		if (isset($version1[$i])) {
			$version1[$i] = (int)$version1[$i];
		} else {
			$version1[$i] = ($i == 2 && isset($version2[$i])) ? (int)$version2[$i] : 99;
		}
		if (isset($version2[$i])) {
			$version2[$i] = (int)$version2[$i];
		} else {
			$version2[$i] = ($i == 2 && isset($version1[$i])) ? (int)$version1[$i] : 99;
			;
		}
		$i++;
	}

	if ($version1[1] > $version2[1]) { 
		//not compatible, if major version is higher
		return false;
	}

	$version1 = implode('.', $version1);
	$version2 = implode('.', $version2);

	return version_compare($version1, $version2, $operator);
}

function getTextUploadError($error) {
	switch ($error) {
		case UPLOAD_ERR_INI_SIZE:
			$error_txt = 'The uploaded file exceeds the upload_max_filesize directive in php.ini (now ' . ini_get('upload_max_filesize') . ')';
			break;
		case UPLOAD_ERR_FORM_SIZE:
			$error_txt = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			break;
		case UPLOAD_ERR_PARTIAL:
			$error_txt = 'The uploaded file was only partially uploaded';
			break;
		case UPLOAD_ERR_NO_FILE:
			$error_txt = 'No file was uploaded';
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			$error_txt = 'Missing a php temporary folder';
			break;
		case UPLOAD_ERR_CANT_WRITE:
			$error_txt = 'Failed to write file to disk';
			break;
		case UPLOAD_ERR_EXTENSION:
			$error_txt = 'File upload stopped by php-extension';
			break;
		default:
			$error_txt = 'Some problem happen with file upload. Check error log for more information';
	}
	return $error_txt;
}

/*
 * DATETIME funtions
 */

/*
*  Convert PHP date format to datepicker date format.
*  AbanteCart base date format on language setting date_format_short that is PHP date function format
*  Convert to datepicker format
*  References:
*  http://docs.jquery.com/UI/Datepicker/formatDate
*  http://php.net/manual/en/function.date.php
*/
function format4Datepicker($date_format) {
	$new_format = $date_format;
	$new_format = preg_replace('/d/', 'dd', $new_format);
	$new_format = preg_replace('/j/', 'd', $new_format);
	$new_format = preg_replace('/l/', 'DD', $new_format);
	$new_format = preg_replace('/z/', 'o', $new_format);
	$new_format = preg_replace('/m/', 'mm', $new_format);
	$new_format = preg_replace('/n/', 'm', $new_format);
	$new_format = preg_replace('/F/', 'MM', $new_format);
	$new_format = preg_replace('/Y/', 'yy', $new_format);
	return $new_format;
}

/*
* Function to format date in database format (ISO) to int format
*/
function dateISO2Int($string_date) {
	$string_date = trim($string_date);
	$is_datetime = strlen($string_date) > 10 ? true : false;
	return dateFromFormat($string_date, ($is_datetime ? 'Y-m-d H:i:s' : 'Y-m-d'));
}

/*
* Function to format date from int to database format (ISO)
*/
function dateInt2ISO($int_date) {
	return date('Y-m-d H:i:s', $int_date);
}

/*
* Function to format date from format in the display (language based) to database format (ISO)
* Param: date in specified format, format based on PHP date function (optional)
* Default format is taken from current language date_format_short setting
*/
function dateDisplay2ISO($string_date, $format = '') {

	if (empty($format)) {
		$registry = Registry::getInstance();
		$format = $registry->get('language')->get('date_format_short');
	}

	if ($string_date) {
		return dateInt2ISO(dateFromFormat($string_date, $format));
	} else {
		return '';
	}
}

/*
* Function to format date from database format (ISO) into the display (language based) format 
* Param: iso date, format based on PHP date function (optional)
* Default format is taken from current language date_format_short setting
*/

function dateISO2Display($iso_date, $format = '') {

	if (empty($format)) {
		$registry = Registry::getInstance();
		$format = $registry->get('language')->get('date_format_short');
	}
	$empties = array('0000-00-00', '0000-00-00 00:00:00', '1970-01-01', '1970-01-01 00:00:00');
	if ($iso_date && !in_array($iso_date, $empties)) {
		return date($format, dateISO2Int($iso_date));
	} else {
		return '';
	}

}

/*
* Function to format date from integer into the display (language based) format
* Param: int date, format based on PHP date function (optional)
* Default format is taken from current language date_format_short setting
*/

function dateInt2Display($int_date, $format = '') {

	if (empty($format)) {
		$registry = Registry::getInstance();
		$format = $registry->get('language')->get('date_format_short');
	}

	if ($int_date) {
		return date($format, $int_date);
	} else {
		return '';
	}

}

/*
* Function to show Now date (local time) in the display (language based) format 
* Param: format based on PHP date function (optional)
* Default format is taken from current language date_format_short setting
*/

function dateNowDisplay($format = '') {
	if (empty($format)) {
		$registry = Registry::getInstance();
		$format = $registry->get('language')->get('date_format_short');
	}
	return date($format);
}


function dateFromFormat($string_date, $date_format, $timezone = null) {
	$date = new DateTime();
	$timezone = is_null($timezone) ? $date->getTimezone() : $timezone;
	if (empty($date_format)) return null;
	$string_date = empty($string_date) ? date($date_format) : $string_date;
	if(method_exists($date,'createFromFormat')){
		$iso_date = DateTime::createFromFormat($date_format, $string_date, $timezone);
		$result = $iso_date ? $iso_date->getTimestamp() : null;
	}else{
		$iso_date = DateTimeCreateFromFormat($date_format, $string_date, $timezone);
		$result = $iso_date ? $iso_date : null;
	}
	return $result;
}


/**
 * Function of getting integer timestamp from string date formatted by date() function
 * @deprecated since php 5.3
 * @param string $date_format
 * @param string $string_date
 * @return int
 */
function DateTimeCreateFromFormat($date_format, $string_date) {
	// convert date format first from format of date() to format of strftime()
    $caracs = array(
        // Day - no strf eq : S
        'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
        // Week - no date eq : %U, %W
        'W' => '%V',
        // Month - no strf eq : n, t
        'F' => '%B', 'm' => '%m', 'M' => '%b',
        // Year - no strf eq : L; no date eq : %C, %g
        'o' => '%G', 'Y' => '%Y', 'y' => '%y',
        // Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
        'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
        // Timezone - no strf eq : e, I, P, Z
        'O' => '%z', 'T' => '%Z',
        // Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
        'U' => '%s'
    );
    $strftime_format = strtr((string)$date_format, $caracs);

	$date_parsed = strptime($string_date, $strftime_format);
	$int_date = mktime($date_parsed["tm_hour"],$date_parsed["tm_min"],$date_parsed["tm_sec"],$date_parsed["tm_mon"]+1,($date_parsed["tm_mday"]),(1900+$date_parsed["tm_year"]));
	return $int_date;
}

//strptime function with solution for windows
if( !function_exists("strptime")) {
	function strptime($date, $format) {
	    $masks = array(
	      '%d' => '(?P<d>[0-9]{2})',
	      '%m' => '(?P<m>[0-9]{2})',
	      '%Y' => '(?P<Y>[0-9]{4})',
	      '%H' => '(?P<H>[0-9]{2})',
	      '%M' => '(?P<M>[0-9]{2})',
	      '%S' => '(?P<S>[0-9]{2})',
	    );
	
	    $rexep = "#".strtr(preg_quote($format), $masks)."#";
	    if(!preg_match($rexep, $date, $out))
	      return false;
	
	    $ret = array(
	      "tm_sec"  => (int) $out['S'],
	      "tm_min"  => (int) $out['M'],
	      "tm_hour" => (int) $out['H'],
	      "tm_mday" => (int) $out['d'],
	      "tm_mon"  => $out['m']?$out['m']-1:0,
	      "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
	    );
	    return $ret;
	}
}

/**
 * @param string $extension_txt_id
 * @return SimpleXMLElement | bool
 */
function getExtensionConfigXml($extension_txt_id) {
	$registry = Registry::getInstance();
	$result = $registry->get($extension_txt_id.'_configXML');

	if(!is_null($result)){
		return $result;
	}

	$extension_txt_id = str_replace('../', '', $extension_txt_id);
	$filename = DIR_EXT . $extension_txt_id . '/config.xml';
	$ext_configs = simplexml_load_file($filename);

	if($ext_configs === false){
		$err_text = 'Error: cannot to load config.xml of extension '.$extension_txt_id.'.';
		$error = new AError($err_text);
		$error->toLog()->toDebug()->toMessages();
		foreach(libxml_get_errors() as $error) {
			$err = new AError($error->message);
			$err->toLog()->toDebug()->toMessages();
		}
		return false;
	}

	/**
	 * DOMDocument of extension config
	 * @var $base_dom DOMDocument
	 */
	$base_dom = new DOMDocument();
	$base_dom->load($filename);
	$xpath = new DOMXpath($base_dom);
	/**
	 * @var $firstNode DOMNodeList
	 */
	$firstNode = $base_dom->getElementsByTagName('settings');
	// check is "settings" entity exists
	if(is_null($firstNode->item(0))){
		/**
		 * @var $node DOMNode
		 */
		$node = $base_dom->createElement("settings");
		$base_dom->appendChild($node);
	}else{
		/**
		 * @var $fst DOMElement
		 */
		$fst = $base_dom->getElementsByTagName('settings')->item(0);
		/**
		 * @var $firstNode DOMNode
		 */
		$firstNode = $fst->getElementsByTagName('item')->item(0);
	}

	$xml_files = array('top'    => array(
										DIR_CORE.'extension/' . 'default/config_top.xml',
										DIR_CORE.'extension/' . (string)$ext_configs->type . '/config_top.xml'),
					   'bottom' => array(
						   				DIR_CORE.'extension/' . 'default/config_bottom.xml',
						   				DIR_CORE.'extension/' . (string)$ext_configs->type . '/config_bottom.xml'
					   ));

	// then loop for all additional xml-config-files
	foreach($xml_files as $place=>$files){
		foreach($files as $filename){
			if ( file_exists($filename) ) {
				$additional_config = simplexml_load_file($filename);
				//if error - writes all
				if($additional_config===false){
					foreach(libxml_get_errors() as $error) {
						$err = new AError($error->message);
						$err->toLog()->toDebug()->toMessages();
					}
				}
				// loop by all settings items
				foreach($additional_config->settings->item as $setting_item){
					/**
					 * @var $setting_item simpleXmlElement
					 */
					$attr = $setting_item->attributes();
					$item_id = $extension_txt_id.'_'.$attr['id'];
					$is_exists = $ext_configs->xpath('/extension/settings/item[@id=\''.$item_id.'\']');
					if(!$is_exists){
						// remove item that was appended on previous cicle from additional xml (override)
						$qry = "/extension/settings/item[@id='".$item_id."']";
						$existed = $xpath->query($qry);
						if(!is_null($existed)){
							foreach ($existed as $node) {
								$node->parentNode->removeChild($node);
							}
						}
						// rename id for settings item
						$setting_item['id'] = $item_id;
						//converts simpleXMLElement node to DOMDocument node for inserting
						$item_dom_node = dom_import_simplexml($setting_item);
						$item_dom_node  = $base_dom->importNode($item_dom_node, TRUE);
						$setting_node = $base_dom->getElementsByTagName('settings')->item(0);
						if($place=='top' && !is_null($firstNode)){
							$setting_node->insertBefore($item_dom_node, $firstNode);
						}else{
							$setting_node->appendChild($item_dom_node);
						}
					}
				}
			}
		}
	}


	//remove all disabled items from list
	$qry = '/extension/settings/item[disabled="true"]';
	$existed = $xpath->query($qry);
	if(!is_null($existed)){
		foreach ($existed as $node) {
			$node->parentNode->removeChild($node);
		}
	}


	$result = simplexml_import_dom($base_dom);
	$registry->set($extension_txt_id.'_configXML',$result);
	return $result;
}

/**
 * Function for starting new storefront session for control panel user
 * NOTE: do not try to save into session any data after this function call!
 *
 * @param $user_id int - control panel user_id
 * @param array $data data for writing into new session storage
 * @return bool
 */
function startStorefrontSession($user_id, $data=array()){
    $data = (array)$data;
    $data['merchant'] = (int)$user_id;
    if(!$data['merchant']){ return false;}
    session_write_close();
    $session = new ASession(defined('UNIQUE_ID') ? 'AC_SF_'.strtoupper(substr(UNIQUE_ID, 0, 10)) : 'AC_SF_PHPSESSID');
    foreach($data as $k=>$v){
        $session->data[$k] = $v;
    }
    session_write_close();
    return true;
}


/**
 * Function to built array with sort_order equaly encremented
 *
 * @param array $array to build sort order for
 * @param int $min - minimal sort order numer (start)
 * @param int $max - maximum sort order number (end)
 * @param string $sort_direction
 * @return array with sort order added.
 */
function build_sort_order($array, $min, $max, $sort_direction = 'asc'){
	if ( empty($array) ) {
		return array();
	}

	//if no min or max, set interval to 10
	$return_arr = array();
	if ($max > 0) {
		$divider = 1;
		if (count($array) > 1) {
			$divider = (count($array) - 1);
		}
		$increment = ($max - $min ) / $divider;	
	} else {
		$increment = 10;
		$min = 10;
		$max = sizeof($array)*10;
	}
	$prior_sort = -1;
	if ( $sort_direction == 'asc') {
		foreach( $array as $id ){
		    if($prior_sort < 0) {
		    	$return_arr[$id] = $min;
		    } else {
		    	$return_arr[$id] = round($prior_sort + $increment, 0);
		    }
		    $prior_sort = $return_arr[$id];
		}
	} else if ( $sort_direction == 'desc') {
		$prior_sort = $max+$increment;
		foreach( $array as $id ){
		   $return_arr[$id] = abs(round($prior_sort - $increment, 0));
		   $prior_sort = $return_arr[$id];
		}	
	}
	return $return_arr;
}

/**
 * Function to test if array is assosiative array
 *
 * @param array $test_array
 * @return bool
 */
function is_assoc($test_array) {
	return is_array($test_array) && array_diff_key($test_array,array_keys(array_keys($test_array)));
}

/**
 * Return project base
 *
 * @return string
 */

function project_base() {
	$base = 'PGEgaHJlZj0iaHR0cDovL3d3dy5hYmFudGVjYXJ0LmNvbSIgb25jbGljaz0id2luZG93Lm9wZW4odGhpcy5ocmVm';
	$base .= 'KTtyZXR1cm4gZmFsc2U7IiB0aXRsZT0iSWRlYWwgT3BlblNvdXJjZSBFLWNvbW1lcmNlIFNvbHV0aW9uIj5BYmFudGVDYXJ0PC9hPg==';
	return base64_decode($base);
}

/**
 * Validate if string is HTML
 *
 * @param string $test_string
 * @return bool
 */

function is_html($test_string) {
	if($test_string != strip_tags($test_string)) {
		return true;
	}
	return false;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param int|string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @return String containing either just a URL or a complete image tag
 */
function getGravatar( $email = '', $s = 80, $d = 'mm', $r = 'g') {
    if ( empty($email) ) {
    	return null;
    }
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=".$s."&d=".$d."&r=".$r;
    return $url;
}

function compressTarGZ($tar_filename, $tar_dir, $compress_level = 5){

	$compress_level = ($compress_level<1 || $compress_level>9) ? 5 : $compress_level;

	$exit_code = 0;
	if(pathinfo($tar_filename,PATHINFO_EXTENSION)=='gz'){
		$filename = rtrim($tar_filename,'.gz');
	}else{
		$filename = $tar_filename.'.tar.gz';
	}
	$tar = rtrim($tar_filename,'.gz');
	//remove archive if exists
	if(is_file($tar_filename)){
		unlink($tar_filename);
	}
	if(is_file($filename)){
		unlink($filename);
	}
	if(is_file($tar)){
		unlink($tar);
	}

	if(class_exists('PharData') ){
		try{
			$a = new PharData($tar );
			$a->buildFromDirectory($tar_dir); // this code creates tar-file
			if(file_exists($tar)){ // remove tar-file after zipping
				gzip($tar, $compress_level);
				unlink($tar);
			}
		}catch (Exception $e){
			$error = new AError( $e->getMessage() );
			$error->toLog()->toDebug();
			$exit_code =1;
		}
	}else{
		$exit_code =1;
	}

	if ( $exit_code ) {
		$registry = Registry::getInstance();
		$registry->get('load')->library('targz');
		$targz = new Atargz();
		return $targz->makeTar($tar_dir.$tar_filename, $filename, $compress_level);
	}else{
		return true;
	}
}

function gzip($src, $level = 5, $dst = false){
    if($dst == false){
        $dst = $src.".gz";
    }
    if(file_exists($src)){
        $filesize = filesize($src);
        $src_handle = fopen($src, "r");
        if(!file_exists($dst)){
            $dst_handle = gzopen($dst, "w$level");
            while(!feof($src_handle)){
                $chunk = fread($src_handle, 2048);
                gzwrite($dst_handle, $chunk);
            }
            fclose($src_handle);
            gzclose($dst_handle);
            return true;
        } else {
            error_log("$dst already exists");
        }
    } else {
        error_log("$src doesn't exist");
    }
    return false;
}


/**
 * Generate random word
 *
 * @param $length int  - {word length}
 * @return string
 */
function randomWord($length = 4){
    $newcode_length=0;
    $newcode='';
    while($newcode_length < $length) {
        $x=1;
        $y=3;
        $part = rand($x,$y);
        if($part==1){// Numbers
        	$a=48;
        	$b=57;
        }  
        if($part==2){// UpperCase
        	$a=65;
        	$b=90;
        }  
        if($part==3){// LowerCase
        	$a=97;
        	$b=122;
        } 
        $code_part=chr(rand($a,$b));
        $newcode_length = $newcode_length + 1;
        $newcode = $newcode.$code_part;
    }
    return $newcode;
}

/**
 * TODO: in the future
 * @param $zip_filename
 * @param $zip_dir
 */
function compressZIP($zip_filename, $zip_dir){

}

function getMimeType($filename) {
$filename = (string)$filename;
    $mime_types = array(

        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $ext = strtolower(array_pop(explode('.',$filename)));
    if (has_value($mime_types[$ext])) {
        return $mime_types[$ext];
    }elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
	    $mimetype = !$mimetype ? 'application/octet-stream' : $mimetype;
        return $mimetype;
    }
    else {
        return 'application/octet-stream';
    }
}

// function detect is maximum execution time can be changed
function canChangeExecTime(){
	$old_set = ini_get('max_execution_time');
	set_time_limit('1234');
	if(ini_get('max_execution_time')==1234){
		return false;
	}else{
		set_time_limit($old_set);
		return true;
	}
}

function getMemoryLimitInBytes(){
	$size_str = ini_get('memory_limit');
    switch (substr ($size_str, -1)){
        case 'M': case 'm': return (int)$size_str * 1048576;
        case 'K': case 'k': return (int)$size_str * 1024;
        case 'G': case 'g': return (int)$size_str * 1073741824;
        default: return $size_str;
    }
}

function is_valid_url( $validate_url ) {
	if (filter_var($validate_url, FILTER_VALIDATE_URL) === FALSE) {
	    return false;	
	} else {
	    return true;		
	}
}

/*
	Get valid URL path considering *.php
*/
function get_url_path( $url ) {
	$url_path1 = parse_url($url,PHP_URL_PATH);	
	//do we have path with php in the string? Treat case: /abantecart120/index.php/storefront/view/resources/image/18/6c/index.php
	$pos = stripos($url_path1, '.php');
	if ($pos) {
		//we have .php files specified.
		$filtered_url = substr($url_path1, 0, $pos+4);
		return rtrim(dirname($filtered_url), '/.\\').'/';
	} else {
		return rtrim($url_path1, '/.\\').'/';	
	}
}

/*
	Return formated execution back stack
 *
 * @param $depth int/string  - depth of the trace back ('full' to get complete stack)
 * @return string
	
*/
function genExecTrace($depth = 5){
	$e = new Exception();
	$trace = explode("\n", $e->getTraceAsString());
	array_pop($trace); // remove call to this method
	if($depth == 'full') {
		$length = count($trace);		
	} else {
		$length = $depth;
	}
	$result = array();
	for ($i = 0; $i < $length; $i++) {
	    $result[] = ' - ' . substr($trace[$i], strpos($trace[$i], ' '));
	}
	
	return "Execution stack: \t" . implode("\n\t", $result);
}

/**
 * Validate if directory exists and writable
 *
 * @param string $dir 
 * @return bool
*/
function is_writable_dir($dir) {
	if (empty($dir)){
		return false;
	} else if(is_dir($dir) && is_writable($dir)) {
		return true;	
	} else {
		return false;	
	}
}

/**
 * Create (signle level) dir if does not exists and/or make dir writable
 *
 * @param string $dir 
 * @return bool
*/
function make_writable_dir($dir) {
	if (empty($dir)){
		return false;
	} else if(is_writable_dir($dir)) {
		return true;	
	} else if(is_dir($dir) ) {
		//Try to make directory writable 
		chmod($dir,0777);
		return is_writable_dir($dir);
	} else {
		//Try to create directory
		mkdir($dir,0777);
		return is_writable_dir($dir);	
	}
}

/**
 * Qoutes encode a string for javascript using json_encode();
 *
 * @param string $text 
 * @return string
*/
function js_encode($text) {
	return json_encode($text);
}

/**
 * Echo js_encode string;
 *
 * @param string $text 
 * @return string
*/
function js_echo($text) {
	echo js_encode($text);
}