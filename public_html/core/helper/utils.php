<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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

/*
 * check that argument variable has value (even 0 is a value)  
 * */
function has_value($value) {
	if (!is_array($value) && $value !== '' && !is_null($value)) {
		return true;
	} else if (is_array($value) && count($value) > 0) {
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
*  Convert input text to alpaha numeric string for SEO URL use
*/
function SEOEncode($string_value) {
	$seo_key = html_entity_decode($string_value, ENT_QUOTES, 'UTF-8');
	$seo_key = preg_replace('/[^\w\d\s_-]/si', '', $seo_key);
	$seo_key = trim(mb_strtolower($seo_key));
	$seo_key = htmlentities(preg_replace('/\s+/', '_', $seo_key));
	return $seo_key;
}

/*
* Echo array with readable formal. Useful in debugging of array data. 
*/
function echo_array($array_data) {
	echo "<pre> $sub_table_name: ";
	print_r($array_data);
	echo'</pre>';
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

// function for version compare
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

	if ($version1[1] > $version2[1]) { // if major version of extension changed
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



function checkRequirements() {
	$error = '';
	if (phpversion() < '5.2') {
		$error = 'Warning: You need to use PHP5.2 or above for AbanteCart to work!';
	}

	if (!ini_get('file_uploads')) {
		$error = 'Warning: file_uploads needs to be enabled!';
	}

	if (ini_get('session.auto_start')) {
		$error = 'Warning: AbanteCart will not work with session.auto_start enabled!';
	}

	if (!extension_loaded('mysql')) {
		$error = 'Warning: MySQL extension needs to be loaded for AbanteCart to work!';
	}

	if (!extension_loaded('gd')) {
		$error = 'Warning: GD extension needs to be loaded for AbanteCart to work!';
	}

	if (!extension_loaded('mbstring')) {
		$error = 'Warning: MultiByte String extension needs to be loaded for AbanteCart to work!';
	}
	if (!extension_loaded('zlib')) {
		$error = 'Warning: ZLIB extension needs to be loaded for AbanteCart to work!';
	}
	return $error;
}


/**
 * @param string $extension_txt_id
 * @return SimpleXMLElement
 */
function getExtensionConfigXml($extension_txt_id) {
	$extension_txt_id = str_replace('../', '', $extension_txt_id);
	$filename = DIR_EXT . $extension_txt_id . '/config.xml';
	return simplexml_load_file($filename);
}
