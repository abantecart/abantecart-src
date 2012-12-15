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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

function isFunctionAvailable( $func_name ){
	return function_exists($func_name);
}
/*
 * prepare prices and other floats for database writing,, based on locale settings of number formatting
 * */
function preformatFloat($value, $decimal_point='.'){
	if($decimal_point!='.'){
		$value = str_replace('.','~',$value);
		$value = str_replace($decimal_point,'.',$value);
	}
	return (float)preg_replace('/[^0-9\-\.]/','',$value);
}

/*
 * prepare integer for database writing
 * */
function preformatInteger($value){
	return (int)preg_replace('/[^0-9\-]/','',$value);
}

/*
 * prepare string for text id 
 * */
function preformatTextID($value){
	return strtolower( preg_replace("/[^A-Za-z0-9_]/", "", $value) );
}

/*
 * check that argument variable has value (even 0 is a value)  
 * */
function has_value( $value ){
	if ( !is_array($value) && $value !== '' && !is_null($value) ) {
		return true;	
	}
	else if (is_array($value) && count($value) > 0) {
		return true;		
	} else {
		return false;
	}
}

/*
*  Convert input text to alpaha numeric string for SEO URL use
*/
function SEOEncode( $string_value ){
	$seo_key = html_entity_decode($string_value, ENT_QUOTES,'UTF-8');
	$seo_key = preg_replace( '/[^\w\d\s_-]/si', '', $seo_key );
	$seo_key = trim( mb_strtolower( $seo_key ) );
	$seo_key = htmlentities( preg_replace( '/\s+/', '_', $seo_key ) );
	return $seo_key;
}

/*
* Echo array with readable formal. Useful in debugging of array data. 
*/
function echo_array( $array_data ) {
	echo "<pre> $sub_table_name: ";print_r( $array_data );echo'</pre>';
}


/*
 * returns list of files from directory with subdirectories
 */

function getFilesInDir($dir, $file_ext = '') {
    if(!is_dir($dir)) return false;
    $dir = rtrim($dir, '\\/');
    $result = array();

    foreach (glob("$dir/*") as $f) {
        if (is_dir($f)) {    // if is directory
            $result = array_merge($result, getFilesInDir($f, $file_ext));
        } else {
            if($file_ext && substr($f,-3)!=$file_ext){
                continue;
            }
            $result[] = $f;
        }
    }
    return $result;
}
// function for version compare
function versionCompare($version1, $version2, $operator){
	$version1 = explode('.',preg_replace('/[^0-9\.]/', '', $version1));
	$version2 = explode('.',preg_replace('/[^0-9\.]/', '', $version2));
	$i=0;
	while($i<3){
		if(isset($version1[$i])){
			$version1[$i] = (int)$version1[$i];
		}else{
			$version1[$i] = ($i==2  && isset($version2[$i])) ? (int)$version2[$i] : 99;
		}
		if(isset($version2[$i])){
			$version2[$i] = (int)$version2[$i];
		}else{
			$version2[$i] = ($i==2  && isset($version1[$i])) ? (int)$version1[$i] : 99;;
		}
	$i++;
	}

	if($version1[1]>$version2[1]){ // if major version of extension changed
		return false;
	}

	$version1 = implode('.',$version1);
	$version2 = implode('.',$version2);

	return version_compare($version1, $version2, $operator);
}

function getTextUploadError($error){
	switch ($error) {
	        case UPLOAD_ERR_INI_SIZE:
	            $error_txt = 'The uploaded file exceeds the upload_max_filesize directive in php.ini (now '.ini_get('upload_max_filesize').')';
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