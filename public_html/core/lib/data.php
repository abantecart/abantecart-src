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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
/**
 * Class AData
 * @property ModelToolTableRelationships $model_tool_table_relationships
 */
final class AData {
	/**
	 * @var Registry
	 */
	private $registry;
	/**
	 * @var AMessage
	 */
	private $message;
	/**
	 * @var ADB
	 */
	private $db;
	private $csvDelimiters = array(',',';','\t');
	private $status_arr = array();
	private $run_mode;
	private $nested_array = array();
	private $actions = array('insert', 'update', 'update_or_insert', 'delete');

	public function __construct() {
		if ( !IS_ADMIN ) {
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access this section');
		}
		$this->registry = Registry::getInstance();
		$this->load->model('tool/table_relationships');
		$this->sections = $this->model_tool_table_relationships->getSections();
		$this->message = $this->registry->get('messages');
		$this->db = $this->registry->get ( 'db' );
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @return array
	 */
	public function getDelimiters() {
		return $this->csvDelimiters;
	}

	/**
	 * @return array
	 */
	public function getSections()
	{
		return $this->sections;
	}

	/**
	 * @param string $table_name
	 * @return mixed
	 */
	public function getTableColumns($table_name) {
		return $this->model_tool_table_relationships->get_table_columns($table_name);
	}

	/**
	 * @param array $request - See manual for more details
	 * @param bool $skip_inner_ids - Exclude IDs for nested structured related to parent level
	 * @return array
	 */
	public function exportData( $request, $skip_inner_ids = true ) {
		$result_arr = array();
		$result_arr['timestamp'] = date('m/d/Y H:i:s');
		$result_arr['tables'] = array();
		$idx = 0;
		//Validate request and process each main level request
		foreach ($request as $table_name => $sub_request){
			//check if valid main table 
			if ( $table_cfg = $this->model_tool_table_relationships->find_table_cfg( $table_name ) ){
				$idx = array_push($result_arr['tables'], $this->_process_section( $table_name, $sub_request, $table_cfg, $skip_inner_ids ));
			} else {
				$result_arr['tables'][$idx]['table'] = $table_name;
				$result_arr['tables'][$idx]['error'] = "Incorrectly configured input array. $table_name cannot be found";
			}
		}

		return $result_arr;
	}

	/**
	 * Main Function to import Data Array to the database
	 *
	 * @param array $data_array - See manual for more details
	 * @param string $mode - commit or test
	 * @return array
	 */
	public function importData( $data_array, $mode = 'commit'  ) {	
		$this->run_mode = $mode;
		//validate the array. 
		foreach ( $data_array['tables'] as $tnode){
			if (isset($tnode['name'])) {
				$table_cfg = $this->model_tool_table_relationships->find_table_cfg( $tnode['name'] );
				if ( $table_cfg ) {
					$this->_process_import_table($tnode['name'] , $table_cfg, $tnode);
				} else {
					$this->_status2array('error', 'Unknown table '. $tnode['name'] .' requested. Exit this node');
					continue;
				}
			} else {
				$this->_status2array('error', 'Incorrect structure of main Array node. Only table nodes are expected');
			}

		}
		return $this->status_arr;
	}

	/**
	 * Specific Data Array conversion to XML format
	 * @param array  $data_array
	 * @param string $file_output
	 * @return int|string
	 */
	public function array2XML ( $data_array, $file_output = '' ) {
		$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><XMLExport />');
		$this->_array_part2XML($data_array, $xml);

		//format XML to be readable 
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML( $xml->asXML() );
		if ($file_output) {
			return $dom->save($file_output);
		} else {
			return $dom->saveXML();
		}
	}

	/**
	 * Specific CSV format from file
	 * @param $file
	 * @param $delimIndex
	 * @return array
	 */
	public function CSV2ArrayFromFile($file, $delimIndex) {
		$results = array();
		if ( isset($this->csvDelimiters[$delimIndex]) ){
			if ( $this->csvDelimiters[$delimIndex] == '\t' ){
				$delimiter = "\t";
			}
			else{
				$delimiter = $this->csvDelimiters[$delimIndex];
			}
		}
		else{
			$delimiter = ',';
		}

		$results['tables'][] = $this->_csv_file2array($file, $delimiter);
		return $results;
	}

	/**
	 * Specific Data Array conversion to CSV format
	 * @param array $in_array
	 * @param string $fileName
	 * @param int $delimIndex
	 * @param string $format
	 * @param string $enclose
	 * @param string $escape
	 * @param bool $asFile
	 * @return bool|string
	 */
	public function array2CSV($in_array, $fileName, $delimIndex = 0, $format = '.csv', $enclose = '"', $escape = '"', $asFile = false) {

		if ( !is_dir(DIR_DATA) ) {
			$res = mkdir(DIR_DATA);
			chmod(DIR_DATA, 0777);
		}

		if (!is_dir(DIR_DATA) || !is_writable(DIR_DATA)) {
			$this->processError('CSV/TXT Export Error', "Error: Data directory in ".DIR_DATA." is not writable or can not be created!", 'error');
			return false;
		}

		ini_set('max_execution_time', 300);
		if ( isset($this->csvDelimiters[$delimIndex]) ) {
			if ( $this->csvDelimiters[$delimIndex] == '\t' ) {
				$delimiter = "\t";
			} else {
				$delimiter = $this->csvDelimiters[$delimIndex];
			}
		} else {
			$delimiter = ',';
		}

		if ( count($in_array) ){

			$d_name = str_replace('.tar.gz', '', $fileName);
			$dirName = DIR_DATA . $d_name;

			if ( !file_exists($dirName) ) {
				$res = mkdir($dirName);
				chmod($dirName, 0777);
			} else {
				@chmod($dirName, 0777);
				$res = true;
			}

			if ( $res ) {
				foreach ( $in_array['tables'] as $table ) {
					
				    $flat_array = $this->_flatten_array( $table );
				    $col_names = $this->_build_columns( $table );
				    $str = '';

					foreach ( $flat_array as $row ) {
						$column_val = '';
						//for each column add value or set empty.
						for ( $i = 0; $i < count($col_names); $i++ ) {
							if( isset( $row[$col_names[$i]] ) ) {
								$column_val .= $enclose . str_replace($enclose, $escape.$enclose, $row[$col_names[$i]]) . $enclose . $delimiter;
							} else {
								$column_val .=  $enclose . $enclose . $delimiter;
							}
						}
						$str .= $column_val;
						$str = trim($str . $delimiter, $delimiter);
						$str .= "\r\n";
					}

					$str = $enclose . implode($enclose.$delimiter.$enclose, $col_names) . $enclose . "\r\n" . $str;
					file_put_contents($dirName . '/' . $table['name'] . $format, $str);
					@chmod($dirName . '/' . $table['name'] . $format, 0777);
				}
			} else {
				$this->processError('CSV/TXT Export Error', "Error: Can't create directory: " . $dirName, 'error');
				return false;
			}

			$archive = $dirName.'.tar.gz';
			$this->_archive($archive, DIR_DATA, $d_name );

			if ( $asFile ) {
				return $archive;
			}

			return file_get_contents($archive);

		}

		return false;
	}

	/**
	 * @param string $tar_filename
	 * @param string $tar_dir
	 * @param string $filename
	 * @return bool
	 */
	private function _archive($tar_filename, $tar_dir, $filename ) {
		//Archive data to DIR_DATA, delete tmp files in directory 
		//generate errors: No space on device (log to message as error too), No permissons, Others
		//return Success or failed.

		compressTarGZ($tar_filename, $tar_dir.$filename);

		if(!file_exists($tar_filename)){
			$this->processError('Archive error', 'Error: cannot to pack ' . $tar_filename);
			return false;
		}
		@chmod($tar_filename,0777);

		$this->_removeDir( $tar_dir.$filename );
		return true;
	}

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
						chmod($dir . "/" . $obj,0777);
						$err = is_dir($dir . "/" . $obj) ? $this->_removeDir($dir . "/" . $obj) : unlink($dir . "/" . $obj);
						if ( ! $err ) {
							$this->processError('Archive error', "Error: Can't to delete file or directory: '".$dir . "/" . $obj."'.");
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

	/**
	 * generate 1 dimention aray per row of the main table
	 * @param array $data_array
	 * @param string $append
	 * @param bool $root
	 * @return array
	 */
	private function _flatten_array( &$data_array, $append = '', $root = true) {
		$return = array();
		$row_flat = array();
		$sub_level = '';
		$level = 0;
		$t_name = $data_array['name'];
		foreach ($data_array['rows'] as $arow) {
			if ($root) {
				$row_flat = array();
			} else {
				$sub_level = "[$level]";
			}

			$sub_name = "$t_name".$append. $sub_level;
			foreach ($arow as $col_name => $col_val) {
				if ($col_name != 'tables') {
					$row_flat[$sub_name.".".$col_name] = $col_val;
				}
			}
			if ( isset($arow['tables']) ) {
				foreach ($arow['tables'] as $atable) {
					$array_rec = $this->_flatten_array($atable, $append. $sub_level, false);
					$row_flat = array_merge($row_flat, $array_rec);
				}
			}
			//check the main level
			if ($root) {
				$return[] = $row_flat;
				$level=-1;
			}
			$level++;
		}

		if ($root) {
			return $return;
		} else {
			return $row_flat;
		}
	}

	/**
	 * Get flat array from csv file
	 * @param string $file
	 * @param string $delimiter
	 * @param string $enclose
	 * @param string $escape
	 * @return array|bool
	 */
	private function _csv_file2array($file, $delimiter = ',', $enclose = '"', $escape = '"') {
		ini_set('auto_detect_line_endings', true);

		$data = array();

		$row = 0;
		$cols = 0;
		$titles = array();

		if ( $handle = fopen($file, 'r') ) {
			while (($rowData = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
				if (!$cols) {
					$cols = count($rowData);
				}

				$vals = array();
				for ($i = 0; $i < $cols; $i++) {
					$rowData[$i] = str_replace($escape.$enclose, $enclose, $rowData[$i]);
					if ($row == 0) {
						$titles[$i] = $rowData[$i];
						continue;
					}
					$vals[$titles[$i]] = $rowData[$i];
				}

				$data[] = $vals;
				$row++;
			}
			fclose($handle);
			
			$data = array_slice($data, 1);

			$this->nested_array = $this->_build_nested($data);

			$this->_filter_empty($this->nested_array);

			for ($i; $i > 0; $i--)
			{
				$this->_filter_empty($this->nested_array);
			}

			return $this->nested_array;

		} else {
			$this->processError('CSV Import Error', 'Error: Can`t open imported file.');
			return false;
		}
	}

	/**
	 * generate multi dimention aray from flat structure
	 * @param array $flat_array
	 * @return array
	 */
	private function _build_nested( $flat_array ) {
		$md_array = array();

		foreach ($flat_array as $row ) {
			$row_array = array();
			$scope_srt = '';
			$scope_cnt = 0;
			$sub_array = array();
			foreach ( $row as $col_name => $value ) {
				//get action
				if ( $col_name == 'action' ) {
					$row_array[$col_name] = $value;
				}
				//get top level table and fields names
				preg_match('/^(\w+)\.(\w+)$/', $col_name, $matches);

				if (count($matches) == 3) {
					if (!isset($md_array['name'])) {
						$md_array['name'] = $matches[1];
					}
					$row_array[$matches[2]] = $value;
				} else {
					preg_match('/^(\w+)(\[(\d+)\])(.*)/', $col_name, $matches);
					$table_name = $matches[1];
					$scope1 = $matches[2];
					$scope_nbr = $matches[3];
					$ending = $matches[4];
					if(count($matches) ) {
						//possibly we have same scope
						if ($table_name == $scope_srt || empty($scope_srt) ) {
							//do we have more scopes?
							preg_match('/^(\[\d+\])(.*)/', $ending, $more_matches);
							if(count($more_matches)) {
								$sub_array[$scope_nbr][$table_name.$ending] = $value;
							} else {
								//last array node, check if need to process

								if ( is_null($scope_cnt) ) {
									$scope_cnt = 0;
								}

								if ($scope_nbr == $scope_cnt || $scope_cnt+1 == $scope_nbr) {
									$sub_array[$scope_nbr][$table_name.$ending] = $value;
								}
							}
						} else {
							//table changed. Do we call recursive or continue.
							//do we have more scopes?
							preg_match('/^(\[\d+\])(.*)/', $ending, $more_matches);
							if(count($more_matches)) {
								$sub_array[$scope_nbr][$table_name.$ending] = $value;
							} else {
								// new scope, push old scope to main array
								$row_array['tables'][] = $this->_build_nested($sub_array);
								$sub_array = array();
								$sub_array[$scope_nbr][$table_name.$ending] = $value;
							}
						}
					}
					$scope_cnt = $scope_nbr;
					$scope_srt = $table_name;
				}
			}
			//Load final load for what is left out.
			if ( count($sub_array) ) {
				$row_array['tables'][] = $this->_build_nested($sub_array);
			}
			//finished main row
			$md_array['rows'][] = $row_array;
		}

		return $md_array;
	}

	/**
	 * Finds if all columns of the given table are empty.
	 * Remove sub-array if yes, leave untouched otherwise.
	 * @param array $data
	 * @param array|null $parent
	 * @param mixed $parent_key
	 * @param int $i
	 */
	private function _filter_empty( & $data, & $parent = null, $parent_key = null, & $i = 0) {
		ini_set('max_execution_time', 300);
		if ( !empty($data) ) {
			foreach ( $data as $key => & $val ) {
				$i = 0;
				if ( is_array($val) ) {
					if ( $this->_empty($val) )
					{
						unset($data[$key]);
						if ( empty($data) ) {
							unset($parent[$parent_key]);
							if ( count($parent) == 1 && isset($parent['name']) ) {
								unset($parent['name']);
							}

						}
					} else {
						$i++;
						$this->_filter_empty($val, $data, $key);
					}
				}
			}
		} else {

			unset($parent[$parent_key]);
		}
	}

	/**
	 * Return False if some of the elements of the array is not empty.
	 * @param array $data
	 * @return bool
	 */
	private function _empty($data) {
		foreach ( $data as $key => $val ) {
			if ( !empty($val) || (!is_array($val) && $val != '') ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Specific XML file converion to Data Array
	 * @param string $xml_file
	 * @return array
	 */
	public function XML2ArrayFromFile( $xml_file ){
		if (!file_exists( $xml_file )) {
			$this->_status2array('error', "XML file $xml_file does not exists or can not be open.");
			return $this->status_arr;
		}
		return $this->XML2Array( simplexml_load_file($xml_file) );
	}

	/**
	 * Specific XML string converion to Data Array
	 * @param string $xml_str
	 * @return array
	 */
	public function XML2Array( $xml_str ) {
		$ret_array = array();
		if (empty( $xml_str )) {
			$this->_status2array('error', "XML input is empty.");
			return $this->status_arr;
		}

		if ( get_class($xml_str) != 'SimpleXMLElement' ){
			$xml_str = simplexml_load_string($xml_str);
		}

		$ret_array = $this->_XML_part2array($xml_str);

		return $ret_array;
	}

	/**
	 * Process section (table)
	 * @param string $table_name
	 * @param array $request
	 * @param array $table_cfg
	 * @param array $skip_inner_ids
	 * @return array
	 */
	private function _process_section( $table_name, $request, $table_cfg, $skip_inner_ids ) {
		$result_arr = array();
		$result_arr['name'] =  $table_name;
		$result_arr['rows'] = array();
		ADebug::checkpoint('AData::exportData processing ' . $table_name . ' section STARTED');
		//get data for main level 
		$node_data = $this->_get_table_data( $table_name, $table_cfg, $request );

		//remove relation key id if requested. Mostely it is not needed
		if ($skip_inner_ids) {
			for ($i = 0; $i <= count($node_data); $i++) {
				if ($table_cfg['relation_ids']) {
					foreach ($table_cfg['relation_ids'] as $relation_id ) {
						if ($request[$relation_id]) {
							unset($node_data[$i][$relation_id]);
						}
					}
				}
			}
		}

		//process requested nested tables recursevely. 
		if ( is_array($request) && !empty($request['tables']) ){
			//for each key in the data set process all related tables.
			$id_name = $table_cfg['id'];
			if (empty($id_name)) {
				$result_arr['error'] = "Incorrectly configured table. $table_name missing table ID key name";
			} else if ( $id_name == null ) {
				//ID null can not have any children tables
				return array();
				//continue;
			}

			//process children tables for every record
			foreach ($node_data as $row){
				$row_arr = array();
				$row_arr = $row;
				$row_arr['tables'] = array();

				foreach ($request['tables'] as $sub_table_name => $sub_request){
					if ( $sub_table_cfg = $this->model_tool_table_relationships->find_table_cfg( $sub_table_name, $table_cfg ) ){
						//build data for sub table request
						if (!is_array($sub_request)) {
							$sub_request = array();
						}
						//add ID key and value to child request
						if ( isset($sub_table_cfg['switch_to_id']) ) {
							// table connected to node by another ID
							$sub_request[$sub_table_cfg['switch_to_id']] = $row[$sub_table_cfg['switch_to_id']];
						} else {
							$sub_request[$id_name] = $row[$id_name];
						}

						//add all other ids
						//Example: Can pass language_id to limit extract to language
						if ($sub_table_cfg['relation_ids']) {
							foreach ($sub_table_cfg['relation_ids'] as $relation_id ) {
								if ( $request[$relation_id] ) {
									$sub_request[$relation_id] = $request[$relation_id];
								}
							}
						}

						//recurse
						$idx = array_push($row_arr['tables'],  $this->_process_section( $sub_table_name, $sub_request, $sub_table_cfg, $skip_inner_ids));
					} else {
						$row_arr['error'] = "Incorrectly configured input array. $sub_table_name cannot be found";
					}
				}
				array_push($result_arr['rows'], $row_arr);
			}

		} else {
			//last node, just process all 
			foreach ( $node_data as $row ){
				array_push($result_arr['rows'], $row);
			}
		}
		ADebug::checkpoint('AData::exportData processing ' . $table_name . ' section COMPLETED');
		return $result_arr;
	}

	/**
	 * return result for given table and specific range.
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $request
	 * @return null
	 */
	private function _get_table_data($table_name, $table_cfg, $request){
		//Future expansion. Provide date_create and date_updated range within $request to build incremental backup. 
		if ( empty($table_name) || empty ($table_cfg)) {
			return null;
		}

		$sql = 'SELECT * FROM `'. DB_PREFIX . $table_name .'`';

		$sub_sql = '';

		// Special case for Resource library. We have to know exact resource ID that we want to export.
		if ( $table_name == 'resource_library' ) {
			$table_cfg['relation_ids'][] = 'resource_id';
		}

		if ( $table_cfg['relation_ids'] ) {
			foreach ($table_cfg['relation_ids'] as $relation_id ) {
				if ( $request[$relation_id] ) {
					if ( $sub_sql ) {
						$sub_sql .= " AND ";
					}
					$sub_sql .=  '`' . $relation_id . "` = " . (int)$request[$relation_id];
				}
			}
		}

		if ( $table_cfg['special_relation'] ) {
			foreach ($table_cfg['special_relation'] as $sp_field => $sp_value ) {
				$sql_add = '';
				//check if this is relation id to be used for special relation
				if ( in_array($sp_field, $table_cfg['relation_ids']) ) {
					if( $request[$sp_value] ) {
						$sql_add = '`' . $sp_field . "` = '" . $this->db->escape($request[$sp_value]) . "'";
					}
				} else {
					$sql_add = '`' . $sp_field . "` = '" . $this->db->escape($sp_value) . "'";
				}
				if ( $sub_sql ) {
					$sub_sql .= " AND ";
				}
				$sub_sql .= $sql_add;
			}
		}

		$id = $table_cfg['id'];
		if ($id){
			if (empty($request['start_id'])) {
				$request['start_id'] = 0;
			}
			if ( $sub_sql ) {
				$sub_sql .= " AND ";
			}
			$sub_sql .= '`'. $id .'` >= ' . (int)$request['start_id'];
			if ( isset($request['end_id']) && !empty($request['end_id']) ){
				$sub_sql .= ' AND `' . $id . '` <= ' . (int)$request['end_id'];
			}
		}
		//check if special filter provided
		if ($request['filter']) {
			if ( $sub_sql ) {
				$sub_sql .= " ORDER BY ";
			}
			$sub_sql .= $request['filter']['columns'] . " ASC";

		}

		if ( $sub_sql ) {
			$sql = $sql . " WHERE " . $sub_sql;
		}

		return $this->db->query($sql)->rows;
	}

	//####  Import Part ####

	/**
	 * Process each table level recursively
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_arr
	 * @param array $parent_vals
	 * @param bool $action_delete
	 * @return array
	 */
	private function _process_import_table( $table_name, $table_cfg, $data_arr, $parent_vals = array(), $action_delete = false ){

		ADebug::checkpoint('AData::importData processing table ' . $table_name);
		if (!isset($data_arr['rows'])) {
			$this->_status2array('error', 'Incorrect structure of '. $table_name .' node. Row node is expected');
		}

		$new_vals = array();

		foreach ($data_arr['rows'] as $rnode){

			$action = '';
			//Set action for the row
			if ( !$action_delete ) {
				$action = $this->_get_action($table_name, $table_cfg, $rnode);
			} else {
				$action = 'delete';
			}
			//set current scope values
			$new_vals = $parent_vals;

			if (isset($table_cfg['id']) && isset($rnode[$table_cfg['id']]) ) {
				$new_vals[$table_cfg['id']] = $rnode[$table_cfg['id']];
			}
			if (isset($table_cfg['special_relation'])){
				foreach ($table_cfg['special_relation'] as $sp_field => $sp_value ) {
					//check if this is relation id to be used for special relation
					if ( in_array($sp_field, $table_cfg['relation_ids']) ) {
						$new_vals[$sp_field] = $new_vals[$sp_value];
					}
				}
			}
			else if ($table_cfg['relation_ids']) {
				foreach ($table_cfg['relation_ids'] as $relation_id ) {
					if ( isset($rnode[$relation_id]) ) {
						$new_vals[$relation_id] = $rnode[$relation_id];
					}
				}
			}

			//Validate required keys if wrong donot bother with children exit.
			if ( !$this->_validate_action($action, $table_name, $table_cfg, $new_vals) ) {
				continue;
			}

			//Unique case: If this is a resource_map and resource_id is missing we need to create resource library first and get resource_id 
		    if ( $table_name == 'resource_map' && isset($rnode['tables']) && is_array($rnode['tables']) ) {
		    	//only one resource can be mapped at the time. 
		    	$new_table = $rnode['tables'][0];
				$sub_table_cfg = $this->model_tool_table_relationships->find_table_cfg( $new_table['name'], $table_cfg );
				if ( $sub_table_cfg ) {
					if ( $action == 'delete' ) {
						$set_action_delete = true;
					} else {
						$set_action_delete = false;
					}
				    $resource_data = $this->_process_import_table($new_table['name'], $sub_table_cfg, $new_table, $new_vals, $set_action_delete);
				    $new_vals['resource_id'] = $resource_data['resource_id'];

					//Now do the action for the row if any data provided besides keys
					$new_vals = array_merge($new_vals, $this->_do_fromArray($action, $table_name, $table_cfg, $rnode, $new_vals));				    
				} else {
				    $this->_status2array('error', 'Unknown table: "'. $new_table['name'] .'" requested in relation to table ' . $table_name . '. Exit this node');
				}
		    } else {
		    	// all other tables
				//Now do the action for the row if any data provided besides keys
				$new_vals = array_merge($new_vals, $this->_do_fromArray($action, $table_name, $table_cfg, $rnode, $new_vals));

				//locate inner table nodes for recursion
				if ( $table_name != 'resource_map' && isset($rnode['tables']) && is_array($rnode['tables'])) {
					foreach( $rnode['tables'] as $new_table ){

						if ( $action == 'delete' ) {
							$set_action_delete = true;
						} else {
							$set_action_delete = false;
						}
						$sub_table_cfg = $this->model_tool_table_relationships->find_table_cfg( $new_table['name'], $table_cfg );
						if ( $sub_table_cfg ) {
							$this->_process_import_table($new_table['name'], $sub_table_cfg, $new_table, $new_vals, $set_action_delete);
						} else {
							$this->_status2array('error', 'Unknown table: "'. $new_table['name'] .'" requested in relation to table ' . $table_name . '. Exit this node');
							continue;
						}
					}
				}
		    
		    }  	
		}
		//return last row new (updated) values
		return $new_vals;
	}

	/**
	 * Detect action for XML node
	 * <action>insert|update|delete|update_or_insert</action>
	 * Note update_or_insert is best guess action
	 *
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_arr
	 * @return string
	 */
	private function _get_action($table_name, $table_cfg, $data_arr) {
		if( $data_arr['action'] && in_array($data_arr['action'], $this->actions) ){
			return $data_arr['action'];
		} else {
			//get ids required for the table and not special relationship
			if( $table_cfg['id'] && $data_arr[$table_cfg['id']] && !isset($table_cfg['special_relation'])){
				//we have ID, we are not sure if we insert or update. Auto detect
				//To improve performance selection can be added to skip smart insert/update detection if sertain what needs to be done   
				return 'update_or_insert';
			} else if ( $table_cfg['id'] && !$data_arr[$table_cfg['id']] ) {
				return 'insert';
			} else {
				//Note: Insert based on relations keys needs to be validated for insert or update
				return 'update_or_insert';
			}
		}
	}

	/**
	 * validate keys and action for XML node
	 * @param string $action
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $new_vals
	 * @return bool
	 */
	private function _validate_action($action, $table_name, $table_cfg, $new_vals) {
		if ($action == 'delete' || $action == 'update') {
			if( $table_cfg['id'] &&  ( !isset( $new_vals[$table_cfg['id']] ) || $new_vals[$table_cfg['id']] == '' ) ){
				$this->_status2array('error', 'Missing ID for '.$action.' action in table '. $table_name .'. Skipping.');
				return false;
			}
		}
		if ( $action == 'update_or_insert' || $action == 'insert' || $action == 'update'
			|| ( $action == 'delete' && !$table_cfg['id'] ) ) {
			//check that relation key all present
			if (isset($table_cfg['special_relation'])){
				foreach ($table_cfg['special_relation'] as $sp_field => $sp_value ) {
					//check if this is relation id to be used for special relation
					if ( in_array($sp_field, $table_cfg['relation_ids']) ) {
						if ( (!isset($new_vals[$sp_value]) || $new_vals[$sp_value] == '') ) {
							$this->_status2array('error', 'Missing special relation ID '. $sp_value .' for '.$action.' action in table '. $table_name .'. Skipping.');
							return false;
						}
					}
				}
			} else if ($table_cfg['relation_ids']) {

				foreach ($table_cfg['relation_ids'] as $relation_id ) {
					//check if we have required ids in array or from parent nodes

					if ( !isset($new_vals[$relation_id]) || $new_vals[$relation_id] == '' ) {
						$this->_status2array('error', 'Missing relation ID '.$relation_id .' for '.$action.' action in table '. $table_name .'. Skipping.');
						return false;
					}
				}

			}
		}

		return true;
	}

	/**
	 * store status of updates in the array
	 * @param string $status
	 * @param string $message
	 */
	private function _status2array ($status, $message) {
		$this->status_arr[$status][] =  $message;
	}

	/**
	 * Process database from Array
	 * @param string $action
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_row
	 * @param array $parent_vals
	 * @return array
	 */
	private function _do_fromArray($action, $table_name, $table_cfg, $data_row, $parent_vals) {
		$results = array();
		switch ( $action ) {
			case 'update':
				$results = $this->_update_fromArray($table_name, $table_cfg, $data_row, $parent_vals);
				break;

			case 'insert':
				$results = $this->_insert_fromArray($table_name, $table_cfg, $data_row, $parent_vals);
				break;

			case 'update_or_insert':
				$results = $this->_update_or_insert_fromArray($table_name, $table_cfg, $data_row, $parent_vals);
				break;

			case 'delete';
				$results = $this->_delete_fromArray($table_name, $table_cfg, $data_row, $parent_vals);
				break;

			default:
				break;
		}

		return $results;
	}

	/**
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_row
	 * @param array $parent_vals
	 * @return array
	 */
	private function _update_fromArray($table_name, $table_cfg, $data_row, $parent_vals){
		$cols = array();
		$where = array();

		//set ids to where from parent they might not be in there 
		$where = $this->_build_id_columns($table_cfg, $parent_vals);

		foreach ($data_row as $col_name => $col_value){
			if ($col_name == 'tables' || $col_name == 'action') {
				continue;
			}

			if (isset($parent_vals[$col_name]) && $parent_vals[$col_name] != '') {
				//we laready set this above.
				continue;
			}

			// Encrypt column value if encryption is enabled
			if ( $this->dcrypt
				&& $this->dcrypt->active
				&& in_array($table_name, $this->dcrypt->getEcryptedTables())
				&& in_array($col_name, $this->dcrypt->getEcryptedFields($table_name))
			) {
				$encrypted = $this->dcrypt->encrypt_data(array($col_name => $col_value), $table_name);
				$col_value = $encrypted[$col_name];
			}

			if ( $col_name == $table_cfg['id']
				|| (isset($table_cfg['relation_ids']) && in_array($col_name, $table_cfg['relation_ids'])) )
			{
				$where[] = "`" . $col_name . "` = '" . $this->db->escape($col_value) . "'";
				continue;
			}
			$cols[] = "`". $col_name . "` = '" . $this->db->escape($col_value) . "'";
		}

		if ( empty($cols) || empty ($where) ) {
			if ( empty($where) ) {
				$this->_status2array('error', "Update data error in $table_name. Data missing");
			} else {
				$this->_status2array('error', "Warning: Update $table_name. All columns are keys, update action is not allowed. Please use insert.");
			}
			return array();
		}

		$sql = "UPDATE `" . $this->db->table($table_name) . "`";
		$sql .= " SET " . implode(', ', $cols);
		$sql .= " WHERE " . implode(' AND ', $where);

		if ($this->run_mode == 'commit') {
			$this->db->query($sql, TRUE);
		}else{
			$this->_status2array('sql', $sql);
		}

		if ( !empty($this->db->error) ) {
			$this->_status2array('error', "Update data error for $table_name." . $this->db->error);
		} else {
			$this->_status2array('update', "Update for table $table_name done successfully");
		}
		return array();
	}

	/**
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_row
	 * @param array $parent_vals
	 * @return array
	 */
	private function _insert_fromArray($table_name, $table_cfg, $data_row, $parent_vals){
		$return = array();
		$cols = array();

		//set ids to where from parent they might not be in there 
		$cols = $this->_build_id_columns($table_cfg, $parent_vals);

		foreach ($data_row as $col_name => $col_value){
			if ($col_name == 'tables' || $col_name == 'action') {
				continue;
			}

			if ( isset($cols[$col_name]) ) {
				//we laready set this above.
				continue;
			}

			// Encrypt column value if encryption is enabled
			if ( $this->dcrypt
				&& $this->dcrypt->active
				&& in_array($table_name, $this->dcrypt->getEcryptedTables())
				&& in_array($col_name, $this->dcrypt->getEcryptedFields($table_name))
			) {
				$encrypted = $this->dcrypt->encrypt_data(array($col_name => $col_value), $table_name);
				$col_value = $encrypted[$col_name];
			}

			$cols[$col_name] = "`" .$col_name. "` = '" . $this->db->escape($col_value) . "'";

		}

		if ( empty($cols) ) {
			$this->_status2array('error', "Insert data error in $table_name. Data missing");
			return array();
		}

		$sql = "INSERT INTO `" . $this->db->table($table_name) . "`";
		$sql .= " SET " . implode(', ', $cols);

		if ($this->run_mode == 'commit') {
			$this->db->query($sql, TRUE);
			if ( isset($table_cfg['id']) ) {
				$return[$table_cfg['id']] = $this->db->getLastId();
			}
		}else{
			$this->_status2array('sql', $sql);
			if ( isset($table_cfg['id']) ) {
				//id is present for insert
				if ( $data_row[$table_cfg['id']] ) {
					$return[$table_cfg['id']] = $data_row[$table_cfg['id']];
				} else {
					$return[$table_cfg['id']] = 'new_id';
				}			
			}
		}

		if ( !empty($this->db->error) ) {
			$this->_status2array('error', "Insert data error for ". $table_name." " . $this->db->error);
		} else {
			$this->_status2array('insert', "Insert into table ". $table_name." done successfully");
		}
		return $return;
	}

	/**
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_row
	 * @param array $parent_vals
	 * @return array
	 */
	private function _delete_fromArray($table_name, $table_cfg, $data_row, $parent_vals){

		//set ids to where from parent they might not be in there 
		$where = $this->_build_id_columns($table_cfg, $parent_vals);

		if ( in_array($table_name, array('products', 'manufacturers', 'categories')) ) {
			$this->_clear_layouts_tables($table_name, $data_row[$table_cfg['id']]);
		}

		foreach ($data_row as $col_name => $col_value){
			if ($col_name == 'tables' || $col_name == 'action') {
				continue;
			}

			if (isset($parent_vals[$col_name]) && $parent_vals[$col_name] != '') {
				//we laready set this above.
				continue;
			}

			if ( $col_name == $table_cfg['id']
				|| (isset($table_cfg['relation_ids']) && in_array($col_name, $table_cfg['relation_ids']))
			)
			{
				$where[] = "`" . $col_name . "` = '" . $this->db->escape($col_value) . "'";
				continue;
			}
		}
		if ( count ($where) <= 0) {
			$this->_status2array('error', "Delete data error in $table_name. Some key data missing");
			return array();
		}

		$sql = "DELETE FROM `" . $this->db->table($table_name) . "`";
		$sql .= " WHERE " . implode(' AND ', $where);

		if ($this->run_mode == 'commit') {
			$this->db->query($sql, TRUE);
		}else{
			$this->_status2array('sql', $sql);
		}

		if ( !empty($this->db->error) ) {
			$this->_status2array('error', "Delete data error in $table_name." . $this->db->error);
		} else {
			$this->_status2array('delete', "Data deleted from table $table_name successfully");
		}
		return array();
	}

	/**
	 * @param string $table_name
	 * @param array $table_cfg
	 * @param array $data_row
	 * @param array $parent_vals
	 * @return array
	 */
	private function _update_or_insert_fromArray( $table_name, $table_cfg, $data_row, $parent_vals){
		$return = array();
		$where = array();
		$cols = array();

		//set ids to where from parent they might not be in there 
		$where = $this->_build_id_columns($table_cfg, $parent_vals);

		foreach ($data_row as $col_name => $col_value){
			if ($col_name == 'tables' || $col_name == 'action') {
				continue;
			}

			if (isset($parent_vals[$col_name]) && $parent_vals[$col_name] != '') {
				//we laready set this above.
				continue;
			}

			// Encrypt column value if encryption is enabled
			if ( $this->dcrypt
				&& $this->dcrypt->active
				&& in_array($table_name, $this->dcrypt->getEcryptedTables())
				&& in_array($col_name, $this->dcrypt->getEcryptedFields($table_name))
			) {
				$encrypted = $this->dcrypt->encrypt_data(array($col_name => $col_value), $table_name);
				$col_value = $encrypted[$col_name];
			}

			if ( $col_name == $table_cfg['id']
				|| (isset($table_cfg['relation_ids']) && in_array($col_name, $table_cfg['relation_ids'])) ) {
				$where[] = "`" . $col_name . "` = '" . $this->db->escape($col_value) . "'";
				continue;
			}

			// Date validation
			// TODO Add field type to table configuration
			if ( $col_name == 'date_added' || $col_name == 'date_modified' ) {
				if ( (string)$col_value == '0000-00-00 00:00:00' ) {
					$cols[] = "`". $col_name . "` = '" . $this->db->escape($col_value) . "'";
				} else {
					$cols[] = "`". $col_name . "` = '" . date('Y-m-d H:i:s', strtotime($col_value)) . "'";
				}
			} else if ( $col_name == 'date_available' ) {
				if ( (string)$col_value == '0000-00-00' ) {
					$cols[] = "`" . $col_name . "` = '" . $this->db->escape($col_value) . "'";
				}
				else {
					$cols[] = "`" . $col_name . "` = '" . date('Y-m-d', strtotime($col_value)) . "'";
				}
			} else {
				$cols[] = "`" . $col_name . "` = '" . $this->db->escape($col_value) . "'";
			}
		}

		$status = 'insert';

		if ( empty($cols) && empty ($where) ) {
			$this->_status2array('error', "Update or Insert $table_name. No Data to update.");
			return array();
		}
		if ( !empty ($where) ) {
			$check_sql = "SELECT count(*) AS total FROM `" . $this->db->table($table_name) . "` WHERE " . implode(' AND ', $where);
			if ( $this->db->query($check_sql)->row['total'] == 1 ) {
				// We are trying to update table where all columns are keys. We have to skip it.
				if ( empty($cols) ) {
					return array();
				}
				$status = 'update';
			}
		}

		if ( $status == 'update' ) {
			if ( empty($cols) ) {
				$this->_status2array('error', "Update $table_name. No Data to update.");
				return array();
			}
			$sql = "UPDATE `"  . $this->db->table($table_name) . "`";
			$sql .= " SET " . implode(', ', $cols);
			$sql .= " WHERE " . implode(' AND ', $where);
		} else {
			$sql = "INSERT INTO `" . $this->db->table($table_name) . "`";
			$sql .= " SET ";
			$set_cols = array_unique( array_merge($where, $cols) ); 
			$sql .= implode(', ', $set_cols);
		}

		if ($this->run_mode == 'commit') {
			$this->db->query($sql, TRUE);
			if ($status == 'insert' && isset($table_cfg['id']) ) {
				//If special case, no new ID. 
				if(!$table_cfg['on_insert_no_id']){
					$return[$table_cfg['id']] = $this->db->getLastId();
				}
			}
		} else {
			$this->_status2array('sql', $sql);
			if ($status == 'insert' && isset($table_cfg['id']) ) {
				//If special case, no new ID. 
				if(!$table_cfg['on_insert_no_id']){
					$return[$table_cfg['id']] = "new_id";
				}
				//id is present for insert
				if ( $data_row[$table_cfg['id']] ) {
					$return[$table_cfg['id']] = $data_row[$table_cfg['id']];
				}
			}
		}

		if ( !empty($this->db->error) ) {
			$this->_status2array('error', "$status data error in $table_name. " . $this->db->error);
		} else {
			$this->_status2array($status, "$status for table $table_name done successfully");
		}
		return $return;
	}

	/**
	 * @param array $table_cfg
	 * @param array $parent_vals
	 * @return array
	 */
	private function _build_id_columns ($table_cfg, $parent_vals) {
		$list = array();
		//set ids from parent they might not be in there 
		if (isset($parent_vals[$table_cfg['id']]) && $parent_vals[$table_cfg['id']] != '') {
			$list[$table_cfg['id']] = "`" . $table_cfg['id'] . "` = '" . $this->db->escape( $parent_vals[$table_cfg['id']] ) . "'";
		}
		if (isset($table_cfg['special_relation'])){
			foreach ($table_cfg['special_relation'] as $sp_field => $sp_value ) {
				//check if this is relation id to be used for special relation
				if ( in_array($sp_field, $table_cfg['relation_ids']) ) {
					if ( isset($parent_vals[$sp_value]) && $parent_vals[$sp_value] != '') {
						$list[$sp_field] = "`" . $sp_field . "` = '" . $this->db->escape( $parent_vals[$sp_value] ) . "'";
					}
				} else {
					$list[$sp_field] = "`" . $sp_field . "` = '" . $sp_value . "'";
				}
			}
		}
		else if (isset($table_cfg['relation_ids'])) {
			foreach ($table_cfg['relation_ids'] as $relation_id ) {
				if ( $relation_id != $table_cfg['id'] && isset($parent_vals[$relation_id]) && $parent_vals[$relation_id] != '') {
					$list[$relation_id] = "`" . $relation_id . "` = '" . $this->db->escape( $parent_vals[$relation_id] ) . "'";
				}
			}
		}
		return $list;
	}

	/**
	 * recursive function to convert nested array to XML
	 * @param $data_array
	 * @param SimpleXMLElement $xml - it is a reference!!!
	 */
	private function _array_part2XML($data_array, $xml) {
		foreach ($data_array as $akey => $aval ) {
			if ( $akey == 'tables' || $akey == 'rows' ) {
				$new_node = $xml->addChild($akey);
				//this is a regular array and we process node for each row
				foreach ($aval as $arow ) {
					$sub_node = $new_node->addChild(substr($akey, 0, -1));
					$this->_array_part2XML($arow, $sub_node);
				}
			} else if ( is_array($aval) ) {
				$new_node = $xml->addChild($akey);
				$this->_array_part2XML($aval, $new_node);
			} else {
				if ( !empty($aval) ) {
					$new_node = $xml->addChild($akey);
					$dom = dom_import_simplexml($new_node);
					$dom->appendChild($dom->ownerDocument->createCDATASection($aval));
				}
				else {
					$new_node = $xml->addChild($akey, $aval);
				}
			}
		}
	}

	/**
	 * recurcive function to convert nested XML to array
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	private function _XML_part2array ($xml) {
		$results = array();
		foreach ($xml->children() as $column){
			/**
			 * @var $column SimpleXMLElement
			 */
			$col_name = $column->getName();
			if ($col_name == "tables" || $col_name == "rows") {
				$results[$col_name] = array();
				$results[$col_name] = $this->_XML_part2array( $column );
			} else if ($col_name == "table" || $col_name == "row") {
				array_push($results, $this->_XML_part2array( $column ));
			} else {
				$results[$col_name] = (string)$column;
			}
		}
		return $results;
	}

	/**
	 * append message to rurrent node
	 * @param SimpleXMLElement $node
	 * @param $ermessage
	 * @param string $type
	 * @return null
	 */
	private function _error2xml( $node, $ermessage, $type = '') {
		$new_node = $node->addChild('error');
		$new_node->addAttribute('type', $type);
		$dom = dom_import_simplexml($new_node);
		$dom->appendChild($dom->ownerDocument->createCDATASection($ermessage));
		return null;
	}

	/**
	 * Build columns based on most available data nodes
	 * @param array $data_array
	 * @return array
	 */
	private function _build_columns( $data_array ){
		$merged_arr = array();
		foreach ($data_array['rows'] as $arow) {
			$merged_arr = $this->_array_merge_replace_recursive($merged_arr, $arow);
		}
		$data_array['rows'] = array();
		$data_array['rows'][] = $merged_arr;
		$flat_columns = $this->_flatten_array( $data_array );
		return array_keys($flat_columns[0]);
	}

	/**
	 * @return array
	 */
	private function _array_merge_replace_recursive() {
		$arrays = func_get_args();
		$base = array_shift($arrays);
		if(!is_array($base)) $base = empty($base) ? array() : array($base);
		foreach($arrays as $append) {
			if(!is_array($append)) $append = array($append);
			foreach($append as $key => $value) {
				if(!array_key_exists($key, $base) and !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if(is_array($value) or is_array($base[$key])) {
					$base[$key] = $this->_array_merge_replace_recursive($base[$key], $append[$key]);
				} else if(is_numeric($key)) {
					if(!in_array($value, $base)) $base[] = $value;
				} else {
					$base[$key] = $value;
				}
			}
		}
		return $base;
	}

	/**
	 * @param string $title
	 * @param string $error
	 * @param string $level
	 * @return string
	 */
	private function processError($title, $error, $level = 'warning') {
		$this->message->{'save'.ucfirst($level)}($title, $error);
		$wrn = new AError($error);
		$wrn->toDebug()->toLog();
		return $error;
	}

	/**
	 * @param string $table_name
	 * @param string $id
	 */
	private function _clear_layouts_tables($table_name, $id) {
		if ( $key = $this->_get_layout_key($table_name) ) {

			$ids = $this->_get_layout_ids($key, $id);

			if ( !empty($ids) ) {
				$this->_clear_pages($ids['page_id']);
				$this->_clear_pages_layouts($ids['page_id']);
				$this->_clear_layouts($ids['layout_id']);
			}
		}
	}

	/**
	 * get "key_param" to be able to get page_id and layout_id for custom layout
	 * @param $table_name
	 * @return bool|string
	 */
	private function _get_layout_key($table_name) {
		switch ( $table_name ) {
			case 'products':
				$key = 'product_id';
				break;
			case 'manufacturers':
				$key = 'manufacturer_id';
				break;
			case 'categories':
				$key = 'path';
				break;
			default:
				$key = false;
				break;
		}
		return $key;
	}

	/**
	 * get page_id and layout_id to be able to delete propper rows from database
	 * @param string $key_param
	 * @param string $key_value
	 * @return array
	 */
	private function _get_layout_ids($key_param, $key_value) {
		$result = $this->db->query(
			"SELECT p.page_id, pl.layout_id FROM " . $this->db->table("pages") . " p
			INNER JOIN " . $this->db->table("pages_layouts") . " pl ON p.page_id = pl.page_id
			WHERE p.key_param = '" . $this->db->escape($key_param) . "'
			AND p.key_value = '" . (int)$key_value . "'"
		);

		if ( $result->num_rows ) {
			return $result->row;
		}
		return array();
	}

	/**
	 * @param int $page_id
	 */
	private function _clear_pages($page_id) {
		$this->db->query(
			"DELETE FROM " . $this->db->table("pages") . " 
			WHERE page_id = '" . (int)$page_id . "'"
		);
		$this->db->query(
			"DELETE FROM " . $this->db->table("page_descriptions") . " 
			WHERE page_id = '" . (int)$page_id . "'"
		);
	}

	/**
	 * @param int $page_id
	 * @return bool
	 */
	private function _clear_pages_layouts($page_id) {
		$this->db->query(
			"DELETE FROM " . $this->db->table("pages_layouts") . " 
			WHERE page_id = '" . (int)$page_id . "'"
		);
		return true;
	}

	/**
	 * @param int $layout_id
	 * @return bool
	 */
	private function _clear_layouts($layout_id) {
		$this->db->query(
			"DELETE FROM " . $this->db->table("layouts") . " 
			WHERE layout_id = '" . (int)$layout_id . "'"
		);
		return true;
	}

}