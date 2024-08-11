<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

function isFunctionAvailable($func_name)
{
    return function_exists($func_name);
}

/**
 * prepare prices and other floats for database writing, based on locale settings of number formatting
 * @see moneyDisplayFormat()
 */
function preformatFloat($value, $decimal_point = '.')
{
    if ($decimal_point != '.' && strpos($value, $decimal_point)) {
        $value = str_replace('.', '~', $value);
        $value = str_replace($decimal_point, '.', $value);
    }
    return (float) preg_replace('/[^0-9\-.]/', '', $value);
}

/*
 * prepare integer for database writing
 * */
function preformatInteger($value)
{
    return (int) preg_replace('/[^0-9\-]/', '', $value);
}

/*
 * prepare string for text id
 * */
function preformatTextID($value)
{
    return strtolower(preg_replace("/[^A-Za-z0-9_]/", "", $value));
}

/**
 * format money float based on locale
 *
 * @param $value
 * @param $mode (no_round => show number with real decimal, hide_zero_decimal => remove zeros from decimal part)
 *
 * @return string
 * @throws AException
 * @since 1.1.8
 *
 * @see preformatFloat() - backward function
 *
 */
function moneyDisplayFormat($value, $mode = 'no_round')
{
    $value = (float) $value;
    $registry = Registry::getInstance();

    $decimal_point = $registry->get('language')->get('decimal_point');
    $decimal_point = !$decimal_point ? '.' : $decimal_point;

    $thousand_point = $registry->get('language')->get('thousand_point');
    $thousand_point = !$thousand_point ? '' : $thousand_point;

    $currency = $registry->get('currency')->getCurrency();
    $decimal_place = (int) $currency['decimal_place'];
    $decimal_place = !$decimal_place ? 2 : $decimal_place;

    // detect if need to show raw number for decimal points
    // In admin, this is regardless of currency format. Need to show real number
    if ($mode == 'no_round' && $value != round($value, $decimal_place)) {
        //count if we have more decimal than currency configuration
        $decim_portion = explode('.', $value);
        if ($decimal_place < strlen($decim_portion[1])) {
            $decimal_place = strlen($decim_portion[1]);
        }
    }

    //if only zeros after decimal point - hide zeros
    if ($mode == 'hide_zero_decimal' && round($value) == round($value, $decimal_place)) {
        $decimal_place = 0;
    }

    return number_format((float) $value, $decimal_place, $decimal_point, $thousand_point);
}

/*
 * check that argument variable has value (even 0 is a value)
 * */
function has_value($value)
{
    if ($value !== (array) $value && $value !== '' && $value !== null) {
        return true;
    } else {
        if ($value === (array) $value && count($value) > 0) {
            return true;
        } else {
            return false;
        }
    }
}

/*
 * check that argument variable has value (even 0 is a value)
 * */
function is_serialized($value)
{
    if (gettype($value) !== 'string') {
        return false;
    }
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
function is_multi($array)
{
    if ($array === (array) $array && count($array) != count($array, COUNT_RECURSIVE)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Function convert input text to alpha numeric string for SEO URL use
 * if optional parameter object_key_name (product, category, content etc) given function will return unique SEO keyword
 *
 * @param        $string_value
 * @param string $object_key_name
 * @param int $object_id
 *
 * @return string
 * @throws AException
 */
function SEOEncode($string_value, $object_key_name = '', $object_id = 0)
{
    $seo_key = html_entity_decode($string_value, ENT_QUOTES, 'UTF-8');
    $seo_key = preg_replace('/[^\pL\p{Zs}0-9\s\-_]+/u', '', $seo_key);
    $seo_key = trim(mb_strtolower($seo_key));
    $seo_key = str_replace(' ', SEO_URL_SEPARATOR, $seo_key);
    if (!$object_key_name) {
        return $seo_key;
    } else {
        //if $object_key_name given - check is seo-key unique and return unique
        return getUniqueSeoKeyword($seo_key, $object_key_name, $object_id);
    }
}

/**
 * @param        $seo_key
 * @param string $object_key_name
 * @param int $object_id
 *
 * @return string
 * @throws AException
 */
function getUniqueSeoKeyword($seo_key, $object_key_name = '', $object_id = 0)
{
    $object_id = (int) $object_id;
    $registry = Registry::getInstance();
    $db = $registry->get('db');
    $sql = "SELECT `keyword`
            FROM ".$db->table('url_aliases')."
            WHERE `keyword` like '".$db->escape($seo_key)."%'";
    if ($object_id) {
        // exclude keyword of given object (product, category, content etc)
        $sql .= " AND query<>'".$db->escape($object_key_name)."=".$object_id."'";
    }

    $result = $db->query($sql);
    if ($result->num_rows) {
        $keywords = [];
        foreach ($result->rows as $row) {
            $keywords[] = $row['keyword'];
        }

        $i = 0;
        while (in_array($seo_key, $keywords) && $i < 20) {
            $seo_key = $seo_key.SEO_URL_SEPARATOR.($object_id ?: $i);
            $i++;
        }
    }
    return $seo_key;
}

/*
* Echo array with readable formal. Useful in debugging of array data.
*/
function echo_array($array_data)
{
    $wrapper = '<div class="debug_alert alert alert-info alert-dismissible" role="alert">'
        .'<button type="button" class="close" data-dismiss="alert">'
        .'<span aria-hidden="true">&times;</span></button>';
    echo $wrapper;
    echo "<pre>";
    print_r($array_data);
    echo '</pre>';
    echo '</div>';
}

/*
 * returns list of files from directory with subdirectories
 */

function getFilesInDir($dir, $file_ext = '')
{
    if (!is_dir($dir)) {
        return [];
    }
    $dir = rtrim($dir, '\\/');
    $result = [];

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
//NOTE: Function will return false if major versions do not match.
function versionCompare($version1, $version2, $operator)
{
    $version1 = explode('.', preg_replace('/[^0-9.]/', '', $version1));
    $version2 = explode('.', preg_replace('/[^0-9.]/', '', $version2));
    $i = 0;
    while ($i < 3) {
        if (isset($version1[$i])) {
            $version1[$i] = (int) $version1[$i];
        } else {
            $version1[$i] = ($i == 2 && isset($version2[$i])) ? (int) $version2[$i] : 99;
        }
        if (isset($version2[$i])) {
            $version2[$i] = (int) $version2[$i];
        } else {
            $version2[$i] = $i == 2 ? (int) $version1[$i] : 99;
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

function getTextUploadError($error)
{
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
            $error_txt = 'The uploaded file exceeds the upload_max_filesize directive in php.ini (now '
                .ini_get('upload_max_filesize').')';
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
 * DATETIME functions
 */

/*
*  Convert PHP date format to datepicker date format.
*  AbanteCart base date format on language setting date_format_short that is PHP date function format
*  Convert to datepicker format
*  References:
*  http://docs.jquery.com/UI/Datepicker/formatDate
*  http://php.net/manual/en/function.date.php
*/
function format4Datepicker($date_format)
{
    $new_format = $date_format;
    $new_format = preg_replace('/d/', 'dd', $new_format);
    $new_format = preg_replace('/j/', 'd', $new_format);
    $new_format = preg_replace('/l/', 'DD', $new_format);
    $new_format = preg_replace('/z/', 'o', $new_format);
    $new_format = preg_replace('/m/', 'mm', $new_format);
    $new_format = preg_replace('/n/', 'm', $new_format);
    $new_format = preg_replace('/F/', 'MM', $new_format);
    return preg_replace('/Y/', 'yy', $new_format);
}

/*
* Function to format date in database format (ISO) to int format
*/
function dateISO2Int($string_date)
{
    $string_date = trim($string_date);
    $is_datetime = strlen($string_date) > 10;
    return dateFromFormat($string_date, ($is_datetime ? 'Y-m-d H:i:s' : 'Y-m-d'));
}

/*
* Function to format date from int to database format (ISO)
*/
function dateInt2ISO($int_date)
{
    return date('Y-m-d H:i:s', $int_date);
}

/*
* Function to format date from format in the display (language based) to database format (ISO)
* Param: date in specified format, format based on PHP date function (optional)
* Default format is taken from current language date_format_short setting
*/
function dateDisplay2ISO($string_date, $format = '')
{
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

function dateISO2Display($iso_date, $format = '')
{
    if (empty($format)) {
        $registry = Registry::getInstance();
        $format = $registry->get('language')->get('date_format_short');
    }
    $empties = ['0000-00-00', '0000-00-00 00:00:00', '1970-01-01', '1970-01-01 00:00:00'];
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

function dateInt2Display($int_date, $format = '')
{
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

function dateNowDisplay($format = '')
{
    if (empty($format)) {
        $registry = Registry::getInstance();
        $format = $registry->get('language')->get('date_format_short');
    }
    return date($format);
}

function dateFromFormat($string_date, $date_format, $timezone = null)
{
    $date = new DateTime();
    $timezone = is_null($timezone) ? $date->getTimezone() : $timezone;
    if (empty($date_format)) {
        return null;
    }
    $string_date = empty($string_date) ? date($date_format) : $string_date;
    $iso_date = DateTime::createFromFormat($date_format, $string_date, $timezone);
    return $iso_date ? $iso_date->getTimestamp() : null;
}

//strptime function with solution for windows
if (!function_exists("strptime")) {
    function strptime($date, $format)
    {
        $masks = [
            '%d' => '(?P<d>[0-9]{2})',
            '%m' => '(?P<m>[0-9]{2})',
            '%Y' => '(?P<Y>[0-9]{4})',
            '%H' => '(?P<H>[0-9]{2})',
            '%M' => '(?P<M>[0-9]{2})',
            '%S' => '(?P<S>[0-9]{2})',
        ];

        $regexp = "#".strtr(preg_quote($format), $masks)."#";
        if (!preg_match($regexp, $date, $out)) {
            return false;
        }

        return [
            "tm_sec"  => (int) $out['S'],
            "tm_min"  => (int) $out['M'],
            "tm_hour" => (int) $out['H'],
            "tm_mday" => (int) $out['d'],
            "tm_mon"  => $out['m'] ? $out['m'] - 1 : 0,
            "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
        ];
    }
}

/**
 * @param string $extension_txt_id
 *
 * @return SimpleXMLElement | false
 * @throws AException|DOMException
 */
function getExtensionConfigXml($extension_txt_id)
{
    $registry = Registry::getInstance();
    /** @var SimpleXMLElement|null $result */
    $result = $registry->get($extension_txt_id.'_configXML');

    if (!is_null($result)) {
        return $result;
    }

    $extension_txt_id = str_replace('../', '', $extension_txt_id);
    $filename = DIR_EXT.$extension_txt_id.'/config.xml';
    /**
     * @var $ext_configs SimpleXMLElement|false
     */
    $ext_configs = @simplexml_load_file($filename);

    if ($ext_configs === false) {
        $err_text = 'Error: cannot to load config.xml of extension '.$extension_txt_id.'.';
        $error = new AError($err_text);
        $error->toLog()->toDebug();
        foreach (libxml_get_errors() as $error) {
            $err = new AError($error->message);
            $err->toLog()->toDebug();
        }
        return false;
    }

    /**
     * DOMDocument of extension config
     */
    $base_dom = new DOMDocument();
    $base_dom->load($filename);
    $xpath = new DOMXpath($base_dom);
    $firstNode = $base_dom->getElementsByTagName('settings');
    // check is "settings" entity exists
    if (is_null($firstNode->item(0))) {
        /**
         * @var $node DOMNode
         */
        $node = $base_dom->createElement("settings");
        $base_dom->appendChild($node);
    } else {
        /**
         * @var $fst DOMElement
         */
        $fst = $base_dom->getElementsByTagName('settings')->item(0);
        /**
         * @var $firstNode DOMNode
         */
        $firstNode = $fst->getElementsByTagName('item')->item(0);
    }

    $xml_files = [
        'top'    => [
            DIR_CORE.'extension/'.'default/config_top.xml',
            DIR_CORE.'extension/'.(string) $ext_configs->type.'/config_top.xml',
        ],
        'bottom' => [
            DIR_CORE.'extension/'.'default/config_bottom.xml',
            DIR_CORE.'extension/'.(string) $ext_configs->type.'/config_bottom.xml',
        ],
    ];

    // then loop for all additional xml-config-files
    foreach ($xml_files as $place => $files) {
        foreach ($files as $filename) {
            if (file_exists($filename)) {
                $additional_config = @simplexml_load_file($filename);
                //if error - writes all
                if ($additional_config === false) {
                    foreach (libxml_get_errors() as $error) {
                        $err = new AError($error->message);
                        $err->toLog()->toDebug();
                    }
                }
                // loop by all settings items
                foreach ($additional_config->settings->item as $setting_item) {
                    /**
                     * @var $setting_item simpleXmlElement
                     */
                    $attr = $setting_item->attributes();
                    $item_id = $extension_txt_id.'_'.$attr['id'];
                    $is_exists = $ext_configs->xpath('/extension/settings/item[@id=\''.$item_id.'\']');
                    if (!$is_exists) {
                        // remove item that was appended on previous cycle from additional xml (override)
                        $qry = "/extension/settings/item[@id='".$item_id."']";
                        $existed = $xpath->query($qry);
                        if (!is_null($existed)) {
                            foreach ($existed as $node) {
                                $node->parentNode->removeChild($node);
                            }
                        }
                        // rename id for settings item
                        $setting_item['id'] = $item_id;
                        //converts simpleXMLElement node to DOMDocument node for inserting
                        $item_dom_node = dom_import_simplexml($setting_item);
                        $item_dom_node = $base_dom->importNode($item_dom_node, true);
                        $setting_node = $base_dom->getElementsByTagName('settings')->item(0);
                        if ($place == 'top' && !is_null($firstNode)) {
                            $setting_node->insertBefore($item_dom_node, $firstNode);
                        } else {
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
    if (!is_null($existed)) {
        foreach ($existed as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    $result = simplexml_import_dom($base_dom);
    $registry->set($extension_txt_id.'_configXML', $result);
    return $result;
}

/**
 * Function for starting new storefront session for control panel user
 * NOTE: do not try to save into session any data after this function call!
 * Also function returns false on POST-requests!
 *
 * @param       $user_id int - control panel user_id
 * @param array $data data for writing into new session storage
 *
 * @return bool
 */
function startStorefrontSession($user_id, $data = [])
{
    //NOTE: do not allow create sf-session via POST-request.
    // Related to language-switcher and enabled maintenance mode(see usages)
    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        return false;
    }
    $data = (array) $data;
    $data['merchant'] = (int) $user_id;
    if (!$data['merchant']) {
        return false;
    }
    session_write_close();
    $session = new ASession(defined('UNIQUE_ID') ? 'AC_SF_'.strtoupper(substr(UNIQUE_ID, 0, 10)) : 'AC_SF_PHPSESSID');
    foreach ($data as $k => $v) {
        $session->data[$k] = $v;
    }
    session_write_close();
    return true;
}

/**
 * Function to built array with sort_order equally incremented
 *
 * @param array $array to build sort order for
 * @param int $min - minimal sort order number (start)
 * @param int $max - maximum sort order number (end)
 * @param string $sort_direction
 *
 * @return array with sort order added.
 */
function build_sort_order($array, $min, $max, $sort_direction = 'asc')
{
    if (empty($array)) {
        return [];
    }

    //if no min or max, set interval to 10
    $return_arr = [];
    if ($max > 0) {
        $divider = 1;
        if (count($array) > 1) {
            $divider = (count($array) - 1);
        }
        $increment = ($max - $min) / $divider;
    } else {
        $increment = 10;
        $min = 10;
        $max = sizeof($array) * 10;
    }
    $prior_sort = -1;
    if ($sort_direction == 'asc') {
        foreach ($array as $id) {
            if ($prior_sort < 0) {
                $return_arr[$id] = $min;
            } else {
                $return_arr[$id] = round($prior_sort + $increment, 0);
            }
            $prior_sort = $return_arr[$id];
        }
    } else {
        if ($sort_direction == 'desc') {
            $prior_sort = $max + $increment;
            foreach ($array as $id) {
                $return_arr[$id] = abs(round($prior_sort - $increment, 0));
                $prior_sort = $return_arr[$id];
            }
        }
    }
    return $return_arr;
}

/**
 * Function to test if array is associative array
 *
 * @param array $test_array
 *
 * @return bool
 */
function is_assoc($test_array)
{
    return is_array($test_array) && array_diff_key($test_array, array_keys(array_keys($test_array)));
}

/**
 * Return project base
 *
 * @return string
 */
function project_base()
{
    $base = 'PGEgaHJlZj0iaHR0cHM6Ly93d3cuYWJhbnRlY2FydC5jb20iIHRhcmdldD0iX2JsY';
    $base .= 'W5rIiB0aXRsZT0iSWRlYWwgT3BlblNvdXJjZSBFLWNvbW1lcmNlIFNvbHV0aW9uIj5BYmFudGVDYXJ0PC9hPg==';
    return base64_decode($base);
}

/**
 * Validate if string is HTML
 *
 * @param string $test_string
 *
 * @return bool
 */
function is_html($test_string)
{
    if ($test_string != strip_tags($test_string)) {
        return true;
    }
    return false;
}

function compressTarGZ($tar_filename, $tar_dir, $compress_level = 5)
{
    if (!$tar_filename || !$tar_dir) {
        return false;
    }
    $compress_level = ($compress_level < 1 || $compress_level > 9) ? 5 : $compress_level;
    $exit_code = 0;
    if (pathinfo($tar_filename, PATHINFO_EXTENSION) == 'gz') {
        $filename = rtrim($tar_filename, '.gz');
    } else {
        $filename = $tar_filename.'.tar.gz';
    }
    $tar = rtrim($tar_filename, '.gz');
    //remove archive if exists
    if (is_file($tar_filename)) {
        unlink($tar_filename);
    }
    if (is_file($filename)) {
        unlink($filename);
    }
    if (is_file($tar)) {
        unlink($tar);
    }

    if (class_exists('PharData')) {
        try {
            if (!ini_get('sys_temp_dir')) {
                ini_set('sys_temp_dir', sys_get_temp_dir());
            }
            $a = new PharData($tar);
            //creates tar-file
            $a->buildFromDirectory($tar_dir);
            // remove tar-file after zipping
            if (file_exists($tar)) {
                gzip($tar, $compress_level);
                unlink($tar);
            }
        } catch (Exception $e) {
//            // commented by cause "Iterator RecursiveIteratorIterator returned a file that could not be opened"
//             $error = new AError('Tar GZ compressing error: '.$e->getMessage());
//             $error->toLog()->toDebug();
            $exit_code = 1;
        }
    } else {
        //class pharData does not exists.
        //set mark to use targz-lib
        $exit_code = 1;
    }

    if ($exit_code) {
        $registry = Registry::getInstance();
        $registry->get('load')->library('targz');
        $targz = new Atargz();
        return $targz->makeTar($tar_dir.$tar_filename, $filename, $compress_level);
    } else {
        return true;
    }
}

/**
 * @param string $src
 * @param int $level
 * @param string|bool $dst
 *
 * @return bool
 */
function gzip($src, $level = 5, $dst = false)
{
    if (!$src) {
        return false;
    }

    if (!$dst) {
        $dst = $src.".gz";
    }
    if (file_exists($src) && filesize($src)) {
        $src_handle = fopen($src, "r");
        if (!file_exists($dst)) {
            $dst_handle = gzopen($dst, "w$level");
            while (!feof($src_handle)) {
                $chunk = fread($src_handle, 2048);
                gzwrite($dst_handle, $chunk);
            }
            fclose($src_handle);
            gzclose($dst_handle);
            return true;
        } else {
            error_log($dst." already exists");
        }
    } else {
        error_log($src." doesn't exist or empty");
    }
    return false;
}

/**
 * Generate random word
 *
 * @param $length int  - {word length}
 *
 * @return string
 */
function randomWord($length = 4)
{
    $new_code_length = 0;
    $new_code = '';
    $a = $b = 0;
    while ($new_code_length < $length) {
        $x = 1;
        $y = 3;
        $part = rand($x, $y);
        if ($part == 1) {// Numbers
            $a = 48;
            $b = 57;
        }
        if ($part == 2) {// UpperCase
            $a = 65;
            $b = 90;
        }
        if ($part == 3) {// LowerCase
            $a = 97;
            $b = 122;
        }
        $code_part = chr(rand($a, $b));
        $new_code_length = $new_code_length + 1;
        $new_code = $new_code.$code_part;
    }
    return $new_code;
}

/**
 * Generate random token
 * Note: Starting PHP7 random_bytes() can be used
 *
 * @param $chars int  - {token length}
 *
 * @return string
 */
function genToken($chars = 32)
{
    $token = '';
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet) - 1;
    for ($i = 0; $i < $chars; $i++) {
        $token .= $codeAlphabet[mt_rand(0, $max)];
    }
    return $token;
}

/**
 * TODO: in the future
 *
 * @param $zip_filename
 * @param $zip_dir
 */
function compressZIP($zip_filename, $zip_dir)
{
}

function getMimeType($filename)
{
    $filename = (string) $filename;
    $mime_types = [
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',
        'flv'  => 'video/x-flv',

        // images
        'png'  => 'image/png',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip'  => 'application/zip',
        'gz'   => 'application/gzip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3'  => 'audio/mpeg',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',

        // adobe
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',

        // ms office
        'doc'  => 'application/msword',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'ppt'  => 'application/vnd.ms-powerpoint',

        // open office
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    ];

    $pieces = explode('.', $filename);
    $ext = strtolower(array_pop($pieces));

    if (has_value($mime_types[$ext])) {
        return $mime_types[$ext];
    } elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return !$mimetype ? 'application/octet-stream' : $mimetype;
    } else {
        return 'application/octet-stream';
    }
}

// function detect is maximum execution time can be changed
function canChangeExecTime()
{
    $old_set = ini_get('max_execution_time');
    if(function_exists('set_time_limit')) {
        set_time_limit('1234');
    }
    if (ini_get('max_execution_time') == 1234) {
        return false;
    } else {
        if(function_exists('set_time_limit')) {
            set_time_limit($old_set);
        }
        return true;
    }
}

function getMemoryLimitInBytes()
{
    $size_str = ini_get('memory_limit');
    switch (substr($size_str, -1)) {
        case 'M':
        case 'm':
            return (int) $size_str * 1048576;
        case 'K':
        case 'k':
            return (int) $size_str * 1024;
        case 'G':
        case 'g':
            return (int) $size_str * 1073741824;
        default:
            return $size_str;
    }
}

function is_valid_url($validate_url)
{
    if (filter_var($validate_url, FILTER_VALIDATE_URL) === false) {
        return false;
    } else {
        return true;
    }
}

/**
 * Get valid URL path considering *.php
 *
 * @param string $url
 *
 * @return string
 */
function get_url_path($url)
{
    $url_path1 = parse_url($url, PHP_URL_PATH);
    //do we have path with php in the string?
    // Treat case: /abantecart120/index.php/storefront/view/resources/image/18/6c/index.php
    $pos = stripos($url_path1, '.php');
    if ($pos) {
        //we have .php files specified.
        $filtered_url = substr($url_path1, 0, $pos + 4);
        return rtrim(dirname($filtered_url), '/.\\').'/';
    } else {
        return rtrim($url_path1, '/.\\').'/';
    }
}

/*
 * Return formatted execution back stack
 *
 * @param $depth int/string  - depth of the trace back ('full' to get complete stack)
 * @return string
*/
function genExecTrace($depth = 5)
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    array_pop($trace); // remove call to this method
    if ($depth == 'full') {
        $length = count($trace);
    } else {
        $length = $depth;
    }
    $result = [];
    for ($i = 0; $i < $length; $i++) {
        $result[] = ' - '.substr($trace[$i], strpos($trace[$i], ' '));
    }

    return "Execution stack: \t".implode("\n\t", $result);
}

/**
 * Validate if directory exists and writable
 *
 * @param string $dir
 *
 * @return bool
 */
function is_writable_dir($dir)
{
    if (empty($dir)) {
        return false;
    } else {
        if (is_dir($dir) && is_writable($dir)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Create (single level) dir if does not exists and/or make dir writable
 *
 * @param string $dir
 *
 * @return bool
 */
function make_writable_dir($dir)
{
    if (empty($dir)) {
        return false;
    } else {
        if (is_writable_dir($dir)) {
            return true;
        } else {
            if (!is_dir($dir)) {
                //Try to create directory
                mkdir($dir, 0777);
            }
            chmod($dir, 0777);
            return is_writable_dir($dir);
        }
    }
}

/**
 * Create (multiple level) dir if not exists and/or make all missing writable
 *
 * @param string $path
 *
 * @return bool
 */
function make_writable_path($path)
{
    if (empty($path)) {
        return false;
    } else {
        if (is_writable_dir($path)) {
            return true;
        } else {
            //recurse if parent directory does not exists
            $parent = dirname($path);
            if (strlen($parent) > 1 && !file_exists($parent)) {
                make_writable_path($parent);
            }
            mkdir($path, 0777, true);
            chmod($path, 0777);
            return true;
        }
    }
}

/**
 * Quotes encode a string for javascript using json_encode();
 *
 * @param string $text
 *
 * @return string
 */
function js_encode($text)
{
    return json_encode($text, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}

/**
 * Echo js_encode string;
 *
 * @param string $text
 *
 */
function js_echo($text)
{
    echo js_encode($text);
}

/**
 * Function output string with html-entities
 *
 * @param string $html
 */
function echo_html2view($html)
{
    echo html2view($html);
}
function html2view($html)
{
    return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
}

/**
 * Function to show readable file size
 *
 * @param     $bytes
 * @param int $decimals
 *
 * @return string
 */
function human_filesize($bytes, $decimals = 2)
{
    $sz = ['B', 'K', 'M', 'G', 'T', 'P'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).@$sz[$factor];
}

/**
 * Function returns image dimensions
 *
 * @param $filename
 *
 * @return array
 * @throws AException
 */
function get_image_size($filename)
{
    if (file_exists($filename) && ($info = getimagesize($filename))) {
        return [
            'width'  => $info[0],
            'height' => $info[1],
            'mime'   => $info['mime'],
        ];
    }
    if ($filename) {
        $error =
            new  AError('Error: Cannot get image size of file '.$filename.'. File not found or it\'s not an image!');
        $error->toLog()->toDebug();
    }
    return [];
}

/**
 * Function to resize image if needed and put to new location
 * NOTE: Resource Library handles resize by itself
 *
 * @param string $orig_image (full path)
 * @param string $new_image (relative path start from DIR_IMAGE)
 * @param int $width
 * @param int $height
 * @param int $quality
 *
 * @return string / path to new image
 * @throws AException
 */
function check_resize_image($orig_image, $new_image, $width, $height, $quality)
{
    if (!is_file($orig_image) || empty($new_image)) {
        return null;
    }

    //if new file not yet present, check directory
    if (!file_exists(DIR_IMAGE.$new_image)) {
        $path = '';
        $directories = explode('/', dirname(str_replace('../', '', $new_image)));
        foreach ($directories as $directory) {
            $path = $path.'/'.$directory;
            //do we have directory?
            if (!file_exists(DIR_IMAGE.$path)) {
                // Make sure the index file is there
                $indexFile = DIR_IMAGE.$path.'/index.php';
                $result = mkdir(DIR_IMAGE.$path, 0775)
                    && file_put_contents($indexFile, "<?php die('Restricted Access!'); ?>");
                if (!$result) {
                    $error =
                        new AWarning(
                            'Cannot to create directory '.DIR_IMAGE.$path.'. Please check permissions for '
                            .DIR_IMAGE
                        );
                    $error->toLog();
                }
            }
        }
    }

    if (!file_exists(DIR_IMAGE.$new_image) || (filemtime($orig_image) > filemtime(DIR_IMAGE.$new_image))) {
        $image = new AImage($orig_image);
        $result = $image->resizeAndSave(
            DIR_IMAGE.$new_image,
            $width,
            $height,
            [
                'quality' => $quality,
            ]
        );
        unset($image);
        if (!$result) {
            return null;
        }
    }

    return $new_image;
}

function redirect($url)
{
    if (Registry::getInstance()->get('forbid_redirects')) {
        exit('Redirected to  '.$url);
    }

    if (!$url) {
        return false;
    }
    header('Location: '.str_replace('&amp;', '&', $url));
    exit;
}

function df($var, $filename = 'debug.txt')
{
    $backtrace = debug_backtrace();
    $backtracePath = [];
    foreach ($backtrace as $k => $bt) {
        if ($k > 1) {
            break;
        }
        $backtracePath[] = substr($bt['file'], strlen($_SERVER['DOCUMENT_ROOT'])).':'.$bt['line'];
    }

    $data = func_get_args();
    if (count($data) == 0) {
        return;
    } elseif (count($data) == 1) {
        $data = current($data);
    }

    if (!is_string($data) && !is_numeric($data) && !is_object($data)) {
        $data = var_export($data, 1);
    }

    //   if (is_object($data)) {
    //     $data = ' Variable is Object, use DD like functions! ';
    // }

    file_put_contents(
        $filename,
        "\n--------------------------".date('Y-m-d H:i:s ').microtime()
        ."-----------------------\n Backtrace: ".implode(' → ', $backtracePath)."\n"
        .$data, FILE_APPEND
    );
}

function daysOfWeekList()
{
    $timestamp = strtotime('next Sunday');
    $days = [];
    for ($i = 0; $i < 7; $i++) {
        $days[] = strtolower(date('l', $timestamp));
        $timestamp = strtotime('+1 day', $timestamp);
    }
    return $days;
}

function setCookieOrParams($name = null, $value = null, $options = [])
{
    /** @var string $lifetime - seconds
     * @var string $path - dirname($_SERVER['PHP_SELF']);
     * @var string $domain
     * @var string $secure - if you only want to receive the cookie over HTTPS
     * @var bool $httponly - prevent JavaScript access to session cookie
     * @var string $samesite - 'none', 'lax' or 'strict'
     */
    extract($options);

    if (PHP_VERSION_ID < 70300) {
        if (!$name) {
            session_set_cookie_params($lifetime, $path.'; samesite='.$samesite, $domain, $secure, $httponly);
        } else {
            setcookie($name, $value, $lifetime, $path.'; samesite='.$samesite, $domain, $secure, $httponly);
        }
    } else {
        $options = [
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ];
        if (!$name) {
            $options['lifetime'] = $lifetime;
            session_set_cookie_params($options);
        } else {
            $options['expires'] = $lifetime;
            setcookie($name, $value, $options);
        }
    }
}

/**
 * @param string|int $source
 *
 * @return array
 * @throws AException
 */
function getMailLogoDetails(&$source)
{
    $output = [
        'uri'  => null,
        'html' => null,
    ];
    //if resource ID was given
    if (is_numeric($source)) {
        $r = new AResource('image');
        $resource_info = $r->getResource($source);
        if ($resource_info) {
            if ($resource_info['resource_code']) {
                $output['html'] = html_entity_decode(
                    $resource_info['resource_code'],
                    ENT_QUOTES, 'UTF-8'
                );
            } else {
                $source = $r->getTypeDir().$resource_info['resource_path'];
                $output['uri'] = 'cid:'
                    .md5(pathinfo($resource_info['resource_path'], PATHINFO_FILENAME))
                    .'.'
                    .pathinfo($resource_info['resource_path'], PATHINFO_EXTENSION);
            }
        }
    } // if resource path was given
    else {
        $output['uri'] = 'cid:'
            .md5(pathinfo($source, PATHINFO_FILENAME))
            .'.'
            .pathinfo($source, PATHINFO_EXTENSION);
    }
    return $output;
}

/**
 * function determine is extension will work on cart based on cart versions list
 * @param $versions
 *
 * @return array
 */
function isExtensionSupportsCart($versions)
{
    $full_check = false;
    $minor_check = false;

    foreach ($versions as $item) {
        $version = (string) $item;
        $versions[] = $version;
        $subVersionArray = explode('.', preg_replace('/[^0-9.]/', '', $version));
        $full_check = versionCompare($version, VERSION, '<=');
        $minor_check = !$minor_check
            ? versionCompare(
                $subVersionArray[0].'.'.$subVersionArray[1],
                MASTER_VERSION.'.'.MINOR_VERSION,
                '=='
            )
            : $minor_check;

        if ($full_check && $minor_check) {
            break;
        }
    }
    return [
        'full_check'  => $full_check,
        'minor_check' => $minor_check,
    ];
}

function isAssocArray($array){
   $keys = array_keys($array);
   return $keys !== array_keys($keys);
}

function saveOrCreateLayout( string $templateTextId, array $pageData, array $layoutData )
{
    if(IS_ADMIN !== true){
        throw new AException(0,'Forbidden.');
    }
    $db = Registry::getInstance()->get('db');
    // need to know if unique page existing
    $layoutId = null;

    $where = [
        "p.controller = '".$db->escape($pageData['controller'])."'"
    ];
    if ($pageData['key_value']) {
        $where[] = "p.key_param = '".$db->escape($pageData['key_param'])."'";
        $where[] = "p.key_value = '".$db->escape($pageData['key_value'])."'";
    }

    $sql = " SELECT *
            FROM ".$db->table("pages")." p 
            ".($where ? " WHERE ".implode(" AND ",$where) : '')."
            ORDER BY p.page_id ASC";
    $result = $db->query($sql);
    if($result->row){
        $pageId = (int)$result->row['page_id'];
        $sql = " SELECT pl.layout_id
                FROM ".$db->table("pages_layouts")." pl 
                INNER JOIN ".$db->table("layouts")." l 
                    ON (l.layout_id = pl.layout_id AND l.template_id = '".$db->escape($templateTextId)."') 
                WHERE pl.page_id = ".$pageId;
        $result = $db->query($sql);
        if($result->row){
            $layoutId = $result->row['layout_id'];
        }
    }else{
        //create page if not exists
        $layout = new ALayoutManager();
        $pageId = $layout->savePage($pageData);
    }
    //create new instance with specific template/page/layout data
    $layout = new ALayoutManager($templateTextId, $pageId, $layoutId);

    if(!$layoutId){
        //remove layoutId of base layout if current layout not found
        //this layoutId is ID of default layout for page. Not needed when save.
        unset($layoutData['layout_id']);
    }

    if (has_value($layoutData['source_layout_id'])) {
        //update layout request. Clone source layout
        return $layout->clonePageLayout($layoutData['source_layout_id'], $layoutId, $layoutData['layout_name']);
    } else {
        //save new layout
        return $layout->savePageLayout($layout->prepareInput($layoutData));
    }
}

//default template functions (bs5)

/**
 * @param array $menuItems - [ 'id', 'text or ']
 * @param int $level
 * @param string $parentId
 * @param array $options - [
 *                          'id_key_name' => {unique item text id}
 *                          'submenu_options' => option array that will be used entire submenu
 *                          ]
 *
 * @return string
 * @throws AException
 */
function renderDefaultSFMenu($menuItems, $level = 0, $parentId = '', $options = [ ])
{
    $logged = Registry::getInstance()->get('customer')->isLogged();
    $output = '';
    $menuItems = (array) $menuItems;
    if (!$menuItems) {
        return '';
    }
    $idKey = $options['id_key_name'] ?: 'id';

    if ($level == 0) {
        $output .= '<div '.($options['top_level']['attr'] ?: 'class="d-flex flex-wrap flex-md-nowrap "').'>';
    } else {
        $output .= '<div class="dropdown-menu '.($level > 1 ? 'dropdown-submenu' : '').'" aria-labelledby="'.$parentId.'" '.$options['submenu_level']['attr'].'>';
    }

    $ar = new AResource('image');
    foreach ($menuItems as $i => $item) {
        if ($item[$idKey] == 'home' || !is_array($item)) {
            unset($menuItems[$i]);
            continue;
        }

        if (($logged && $item[$idKey] == 'login')
            || (!$logged && $item[$idKey] == 'logout')
        ) {
            continue;
        }
        $item_title = '<span class="ms-1">'.($item['text'] ?: $item['title'] ?: $item['name']).'</span>';
        $hasChild = (bool) $item['children'];
        $output .= '<div class="dropdown me-3 me-sm-0 mb-3 mb-lg-0">';
        //check icon rl type html, image or none.
        $rl_id = $item['icon'] ? : $item['icon_rl_id'];
        $icon = '';
        if ($rl_id) {
            $resource = $ar->getResource($rl_id);
            if ($resource['resource_path'] && is_file(DIR_RESOURCE.'image/'.$resource['resource_path'])) {
                //set relative path here because of cdn-extension
                $icon = '<img class="menu_image" src="resources/image/'.$resource['resource_path'].'" />';
            } elseif ($resource['resource_code']) {
                $icon = $resource['resource_code'];
            }
        }elseif( $item['icon_html'] ){
            $icon = $item['icon_html'];
        }

        if ($hasChild) {
            $id = 'menu_'.$item[$idKey];
            $css = 'dropdown-toggle text-nowrap mb-3 mb-md-0 me-3 '. ($level ? 'dropdown-item ' : '');
            $output .= '<a id="'.$id.'" 
                            href="'.$item['href'].'" 
                            target="'.$item['settings']['target'].'"
                            class="'.$css.'" 
                            data-bs-toggle="dropdown" 
                            data-bs-target="dropdown" 
                            aria-expanded="false">';
            $output .= $icon.$item_title;
            if(!isset($options['without_caret'])) {
                $output .= '&nbsp; <i class="fa fa-caret-down"></i>';
            }
            $output .= '</a>';

            $params = [
                'menuItems' => $item['children'],
                'level' => $level + 1,
                'parentId' => $id,
                'options' => [
                    'id_key_name' => $idKey
                ]
            ];

            // for case when pass options into deep of menu
            if($options['pass_options_recursively']){
                $params['options'] = array_merge($params['options'], $options['submenu_options']);
            }

            $output .= "\r\n".call_user_func_array('renderDefaultSFMenu',$params);
        } else {
            $css = $level ? "dropdown-item" : "text-secondary " .' me-3 mb-3 text-nowrap ';
            $popoverAttr = $item['thumb']
                ? 'data-bs-toggle="popover" data-bs-content="<img src=&quot;'.$item['thumb'].'&quot;>" '
                .'data-bs-html="true" data-bs-offset="5,5" data-bs-boundary="window" '
                .'data-bs-placement="right" data-bs-trigger="hover"'
                : '';
            $output .= '<a href="'.$item['href'].'" target="'.$item['settings']['target'].'" class="'.$css.'" '.$popoverAttr.'>'.$icon.$item_title.'</a>';
        }
        $output .= '</div>';
    }
    $output .= "</div>\n";

    return $output;
}

function renderDefaultRatingStars($value, $text){
    if(!$value){
        return '';
    }
    $i = 1;
    $output = '<div title="'.htmlspecialchars($text, ENT_QUOTES, 'UTF-8').'">';
    while($i < 6){
        $output .= '<i class="fa-star '.($i<=$value ? 'fa-solid' : 'fa-regular').'"></i>';
        $i++;
    }
    return $output.'</div>';
}

function generateOrderToken($orderId, $email, $secToken = '')
{
    $registry = Registry::getInstance();
    $enc = new AEncryption($registry->get('config')->get('encryption_key'));
    /** @var ModelCheckoutFastCheckout $mdl */
    $mdl = $registry->get('load')->model('checkout/fast_checkout');
    $secToken = $secToken ?: genToken(32);
    $mdl->saveGuestToken($orderId, $secToken);
    return $enc->encrypt($orderId.'::'.$email.'::'.$secToken);
}

function filterIntegerIdList(?array $list = [])
{
    return array_unique(
        array_filter(
            array_map('intval',
                array_map('trim', (array)$list))
        )
    );
}