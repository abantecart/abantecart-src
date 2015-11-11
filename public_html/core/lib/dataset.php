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
 * Class ADataset
 */
final class ADataset {
	/**
	 * @var ADB
	 */
	private $db;
	
	/**
	 * inner id for dataset 
	 * @var integer
	 */
	private $dataset_id = 0;
	/**
	 * array with dataset definition (column names, types etc)
	 * @var array
	 */
	private $columnset = array ();
	/**
	 * array of available data types 
	 * @var array
	 */
	private $column_type_checklist = array ('integer', 'float', 'varchar', 'text', 'timestamp', 'boolean' );
	/**
	 * inner array for existing column definition check
	 * @var array
	 */
	private $check_columnset = array (); // custom array
	/**
	 * string search condition for methods getRows, updateRows, deleteRows.
	 * If it's not empty getRows returns filtered dataset rows.  
	 * @var string
	 */
	private $search_condition = '';
	
	/**
	 * registry to provide access to cart objects
	 *
	 * @var object Registry
	 */
	private $registry;

	/**
	 * @param string $dataset_name
	 * @param string $dataset_key (optional)
	 * @throws AException
	 */
	public function __construct($dataset_name = '', $dataset_key = '', $mode = '') {
		
		$this->registry = Registry::getInstance ();

		$this->db = $this->registry->get('db');
		
		// if dataset_name given - let's get dataset_id
		if ($dataset_name) {
			$result = $this->db->query ( "SELECT dataset_id 
										  FROM " . $this->db->table("datasets") . " 
										  WHERE dataset_name = '" . $this->db->escape ( $dataset_name ) . "'
												" . ($dataset_key ? "AND dataset_key='" . $this->db->escape ( $dataset_key ) . "'" : "") . " LIMIT 1" );
			
			$this->dataset_id = $result->row ['dataset_id'] ? $result->row ['dataset_id'] : 0;
			// if dataset already exists - extract it's column definitions
			if ($this->dataset_id) {
				$this->_getColumnSet ();
			} else {
				if($dataset_name && $mode != 'silent'){
					throw new AException ( AC_ERR_LOAD, 'Error: Dataset with given name ' . $dataset_name . ' and key ' . $dataset_key . ' does not exists.' );
				}
			}
		}
	}
	
	// create new dataset 
	/**
	 * @param string $dataset_name
	 * @param string $dataset_key
	 * @throws AException
	 */
	public function createDataset($dataset_name, $dataset_key = '') {
		
		$result = $this->db->query ( "SELECT * 
										FROM " . $this->db->table("datasets") . " 
										WHERE dataset_name = '" . $this->db->escape ( $dataset_name ) . "'
										AND dataset_key = '" . $this->db->escape ( $dataset_key ) . "';" );
		if ($result->num_rows) {
			//dataset exists. get an ID
			$this->dataset_id = $result->rows[0]['dataset_id'];		
		} else {		
			$this->db->query ( "INSERT INTO " . $this->db->table("datasets") . " (dataset_name,dataset_key) 
							VALUES ('" . $this->db->escape($dataset_name) . "'
							,'" . ($dataset_key ? $this->db->escape($dataset_key) : "") . "')" );
		
			$this->dataset_id = (int)$this->db->getLastId();
		}
	}

	/**
	 * Function for creating new columns in dataset tables. If key "dataset_column_old_name" presents in array and not empty function updates existing column definition
	 *
	 * @param array $new_columnset array ("dataset_column_name"=>"","dataset_column_type"=>"","dataset_column_sort_order"=>"" [, "dataset_column_old_name"=>"",])
	 * @throws AException
	 * @return boolean
	 */
	public function defineColumns($new_columnset = array()) {
		
		if (! $this->dataset_id) {
			throw new AException ( AC_ERR_LOAD, 'Error: Could not define columns! dataset id is null.' );
		}
		
		$column_checklist = array ('name', 'type');
		// if $new_columnset[] contain array key 'old_name' it mean that column must be update (functional for future, for example for upgrading extension dataset)
		// write columnset definitions
		$existing_column_names = array ();
		if ($new_columnset) {
			if ($this->columnset) {
				foreach ( $this->columnset as $id => $columns ) {
					$existing_column_names [$id] = $columns ['dataset_column_name'];
				}
			}
			$i=0;
			foreach ( $new_columnset as $column_definition ) {
				// checks
				if (! is_array ( $column_definition )) {
					throw new AException ( AC_ERR_LOAD, 'Error: Could not write dataset columns! column definition is not array.' );
				}
				//check keys of definition
				if (!array_intersect( array_keys ( $column_definition ), $column_checklist )) {
					throw new AException ( AC_ERR_LOAD, 'Error: Could not write dataset column definition! Definition format error.');
				}
				// check column type
				if (! in_array ( $column_definition ['type'], $this->column_type_checklist )) {
					throw new AException ( AC_ERR_LOAD, 'Error: Could not update dataset column definition! Column type error. Type: ' . $column_definition ['type'] );
				}
				
				$column_definition ['name'] = $this->db->escape ( $column_definition ['name'] );
				$column_definition ['sort_order'] = isset($column_definition ['sort_order']) ? ( int ) $column_definition ['sort_order'] : $i;
				
				// insert new column
				if (! in_array ( $column_definition ['name'], $existing_column_names ) && empty ( $column_definition ['old_name'] )) {
					unset ( $column_definition ['old_name'] );
					$sql_query = "INSERT INTO " . $this->db->table("dataset_definition") . " (dataset_id, dataset_column_name, dataset_column_type, dataset_column_sort_order)
	  									VALUES ('" . $this->dataset_id . "',
	  									        '" . $column_definition['name']  . "',
	  									        '" . $column_definition['type']  . "',
	  									        '" . $column_definition['sort_order']  . "' );\n";
					$this->db->query ( $sql_query );
					$dataset_column_id = $this->db->getLastId();

					//after insert of column need to insert empty values for data consistency
					$sql_query = "SELECT DISTINCT dv.row_id
								  FROM ". $this->db->table('dataset_values')." dv
								  INNER JOIN ". $this->db->table('dataset_definition')." dd ON dd.dataset_column_id = dv.dataset_column_id
								  WHERE dd.dataset_id = '".$this->dataset_id."' AND dv.row_id>0";
					$res = $this->db->query($sql_query);
					if($res->num_rows){
						foreach($res->rows as $r){
							$this->db->query( "INSERT INTO ". $this->db->table('dataset_values')." (dataset_column_id, row_id)
												VALUES ('".$dataset_column_id."','".$r['row_id']."')");
						}
					}
					// update new column
				} else {
					// if old name present - update column definition.
					//if column type will change - just change it, old values will not move to another column of dataset_values. User need update it by himself.
					if (! empty ( $column_definition ['old_name'] ) && ! empty ( $column_definition ['name'] )) {
						$column_id = ( int ) array_search ( (! isset ( $column_definition ['old_name'] ) ? $column_definition ['name'] : $column_definition ['old_name']), $existing_column_names );
						$sql_query = "UPDATE " . $this->db->table("dataset_definition") . " ";
						$sql_query .= "SET dataset_column_name= '" . $column_definition ['name'] . "', ";
						$sql_query .= "dataset_column_type= '" . $column_definition ['type'] . "', ";
						$sql_query .= "dataset_column_sort_order= '" . $column_definition ['sort_order'] . "'";
						$sql_query .= "WHERE dataset_column_id=" . $column_id . ";";
						$this->db->query ( $sql_query );
					}
				}//if new name is empty do nothing
				$i++;
			}
			//	reset $this->columnset
			$this->_getColumnSet ();
		}
		return true;
	}
	
	/** Function gets columns definitions and writes to public var $columnset
	 * @return bool
	 */
	private function _getColumnSet() {
		if (! $this->dataset_id) {
			return false;
		}
		$this->columnset = array ();

		$result = $this->db->query ( "SELECT *
										FROM " . $this->db->table("dataset_definition") . " 
										WHERE dataset_id = '" . $this->dataset_id . "'
										ORDER BY dataset_column_sort_order, dataset_column_id" );

		if ($result->num_rows) {
			foreach ( $result->rows as $row ) {
				$this->columnset [$row ['dataset_column_id']] = $row;
			}
		}
		return true;
	}

	/**
	 * Function adds new dataset rows to the end of dataset table
	 *
	 * parameter $row_values looks like array (array("some_column_name"=>"some_value", "some_column_name"=>"some_value"),
	 * array("some_column_name"=>"some_value", "some_column_name"=>"some_value"))
	 *
	 * @param array $row_values
	 * @throws AException
	 * @return boolean
	 */
	public function addRows($row_values = array()) {
		unset($row_values['row_id']);
		if (! $this->dataset_id || ! $this->columnset) {
			return false;
		}
		
		if (! $row_values || ! is_array ( $row_values )) {
			throw new AException ( AC_ERR_LOAD, 'Error: nothing to write. Array values is empty' );
		}
		
		// make new array for checks and type casting					
		foreach ( $this->columnset as $columns ) {
			$this->check_columnset [$columns ['dataset_column_name']] = $columns;
		}
		// if nedd to add one row
		if(!is_array(current($row_values))){
			$row_values = array($row_values);
		}

		foreach ( $row_values as $dataset_row ) {
			if (! is_array ( $dataset_row )) {
				throw new AException ( AC_ERR_LOAD, 'Error: dataset row is not an array: ' . $dataset_row );
			}
			
			if (array_diff_key ( $dataset_row, $this->check_columnset )) {
				throw new AException ( AC_ERR_LOAD, 'Error: Valueset contain column name which not defined in dataset:' . implode ( ", ", array_keys ( array_diff_key ( $dataset_row, $this->check_columnset ) ) ) );
			}
		}
		
		//get last row number of dataset
		$result = $this->db->query ( "SELECT MAX(row_id) as rownum FROM " . $this->db->table("dataset_values") . " WHERE dataset_column_id IN (" . implode ( ",", array_keys ( $this->columnset ) ) . ")" );
		$row_id = ( int ) $result->row ['rownum'] + 1;
		// let's write
		foreach ( $row_values as $dataset_row ) {
			
			foreach ( $dataset_row as $col_name => $value ) {
				
				$query = "INSERT INTO " . $this->db->table("dataset_values") . " (dataset_column_id, 
																		value_" . $this->columnset [$this->check_columnset [$col_name] ['dataset_column_id']] ['dataset_column_type'] . ",
																		row_id)
		    						VALUES ('" . ( int ) $this->check_columnset [$col_name] ['dataset_column_id'] . "', 
		    								'" . $this->db->escape ( $value ) . "',
		    								'" . $row_id . "')";
				$this->db->query ( $query );
			}
			$row_id ++;
		}
		
		return true;
	}

	/**
	 * Function set properties for dataset
	 *
	 * @param array $properties
	 * @internal param array $property ("property_name"=>"property_value")
	 * @return boolean
	 */
	public function setDatasetProperties($properties = array()) {
		if (! $this->dataset_id || ! $properties || ! is_array ( $properties )) {
			return false;
		}
		
		foreach ( $properties as $name => $value ) {
			$value = ( string ) $value;
			if (strlen ( $name ) > 255 || strlen ( $value ) > 255) {
				continue;
			}
			$query = "DELETE FROM " . $this->db->table("dataset_properties") . " WHERE dataset_id=" . $this->dataset_id . " AND dataset_property_name = '" . $this->db->escape ( $name ) . "' ;";
			$this->db->query ( $query );
			$query = "INSERT INTO " . $this->db->table("dataset_properties") . " VALUES (DEFAULT," . $this->dataset_id . ",'" . $this->db->escape ( $name ) . "','" . $this->db->escape ( $value ) . "');";
			$this->db->query ( $query );
		}
		return true;
	}


	/**
	 * @param string $property_name
	 * @return boolean|array
	 */
	public function getDatasetProperties($property_name = '') {
		if (!$this->dataset_id ) {
			return false;
		}
		
		$output = array ();
		$query = "SELECT dataset_property_name, dataset_property_value 
				  FROM " . $this->db->table("dataset_properties") . " 
				  WHERE dataset_id = " . (int)$this->dataset_id . " 
				  ".($property_name ? " AND dataset_property_name = '".$this->db->escape($property_name)."'" : "");
		$result = $this->db->query ( $query );
		$rows = $result->rows;
		if ($rows) {
			foreach ( $rows as $row ) {
				$output [$row ['dataset_property_name']] = $row ['dataset_property_value'];
			}
		}
		return $output;
	}

	/**
	 * Function set Column properties. It may be checks for value of column cell or some limits etc
	 *
	 * @param string $column_name
	 * @param array $properties $property ("property_name"=>"property_value")
	 * @throws AException
	 * @return boolean
	 */
	public function setColumnProperties($column_name = '', $properties = array()) {
		
		if (! $this->dataset_id || ! $properties || ! is_array ( $properties ) || ! $column_name) {
			return false;
		}
		
		if (! $this->columnset) {
			throw new AException ( AC_ERR_LOAD, 'Error: Could not set property for column! Column definitions is empty.' );
		}
		
		foreach ( $this->columnset as $cols ) {
			if ($cols ['dataset_column_name'] == $column_name) {
				$column_id = $cols ['dataset_column_id'];
			}
		}
		if (! $column_id) {
			throw new AException ( AC_ERR_LOAD, 'Error: Could not set property for column! Column definition is not exists.' );
		}
		
		foreach ( $properties as $name => $value ) {
			$value = ( string ) $value;
			if (strlen ( $name ) > 255 || strlen ( $value ) > 255) {
				continue;
			}
			
			$query = "DELETE FROM " . $this->db->table("dataset_column_properties") . " WHERE dataset_column_id=" . ( int ) $column_id . " AND dataset_column_property_name='" . $this->db->escape ( $name ) . "';";
			$this->db->query ( $query );
			
			$query = "INSERT INTO " . $this->db->table("dataset_column_properties") . " VALUES (" . ( int ) $column_id . ",'" . $this->db->escape ( $name ) . "','" . $this->db->escape ( $value ) . "');";
			$this->db->query ( $query );
		}
		return true;
	}
	
	/**
	 * @return boolean|array
	 */
	public function getColumnsProperties() {
		if (! $this->dataset_id || ! $this->columnset) {
			return false;
		}
		
		$output = null;
		$query = "SELECT dd.dataset_id, dd.dataset_column_name, dcp.dataset_column_property_name, dcp.dataset_column_property_value 
				  FROM " . $this->db->table("dataset_definition") . " dd
				  LEFT JOIN " . $this->db->table("dataset_column_properties") . " dcp ON dcp.dataset_column_id = dd.dataset_column_id
				  WHERE dd.dataset_id = " . $this->dataset_id . ";";
		$result = $this->db->query ( $query );
		$rows = $result->rows;
		if ($rows) {
			foreach ( $rows as $row ) {
				if($row ['dataset_column_property_name']){
					$output [$row ['dataset_column_name']] [$row ['dataset_column_property_name']] = $row ['dataset_column_property_value'];
				}
			}
		}
		return $output;
	}

	/**
	 * This method is analog SELECT of SQL.
	 * 
	 * @param array $column_list
	 * @param string $order_by
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getRows($column_list = array(), $order_by = 'row_id:ASC', $limit = 1000, $offset = 0) {
		
		if (! $this->dataset_id || ! $this->columnset) {
			return array();
		}
		$column_list_id = array();
		if (! $column_list) { // if column list is empty - select all columns of table
			$column_list = array ();
		} else {
			if (! is_array ( $column_list )) {
				$column_list = array ($column_list );
			}
			foreach ( $column_list as $colname ) {
				$column_list_id [] = $this->_getColumnIdByName ( $colname );
			}
		}
		
		$order_by = ( string ) $order_by;
		if(strpos($order_by,":")!==false){
			list ( $order_name, $order_direction ) = explode ( ":", $order_by );
		}else{
			list ( $order_name, $order_direction ) = explode ( " ", $order_by );
		}

		
		$order_name = trim ( $order_name );
		$order_direction = trim ( strtoupper ( $order_direction ) );
		
		
		$sort_value_column = $sort_column_id = '';
		if ($order_name != 'row_id') {
			$sort_column_id = $this->_getColumnIdByName ( $order_name );
			$sort_value_column = $sort_column_id ? ", value_" . $this->columnset [$sort_column_id] ['dataset_column_type'] : '';
		}
		
		$limit = ( int ) $limit;
		$offset = ( int ) $offset;
		
		// first of all we need to know whats row number needed 
		$query = "SELECT DISTINCT dv.row_id " . $sort_value_column . "
  					FROM " . $this->db->table("dataset_values") . " dv
  					LEFT JOIN  " . $this->db->table("dataset_definition") . " dd ON dd.dataset_column_id = dv.dataset_column_id
  					WHERE dd.dataset_id = '" . $this->dataset_id . "' " . ($sort_column_id ? "AND dv.dataset_column_id = '" . $sort_column_id . "'" : '') . " 
  					" . ($this->search_condition ? " AND " . $this->search_condition : '');
		
		$sql = $this->db->query ( $query );
		$result = $sql->rows;

		$row_ids = array ();
		if ($result) {
			foreach ( $result as $value ) {
				$row_ids [] = $value ['row_id'];
			}
		}
		if ($row_ids) { //then gets values of rows
		 	$query = "SELECT  dd.dataset_column_name, dd.dataset_column_id, dv.value_integer,
								dv.value_float, dv.value_varchar, dv.value_boolean,
								CASE WHEN dv.value_timestamp='0000-00-00 00:00:00' THEN '' ELSE dv.value_timestamp END as value_timestamp,
								dv.value_text, dv.row_id
	  				  FROM " . $this->db->table("dataset_values") . " dv
	  				  LEFT JOIN  " . $this->db->table("dataset_definition") . " dd ON dd.dataset_column_id = dv.dataset_column_id
	  				  WHERE dd.dataset_id = '" . $this->dataset_id . "' 
	  				  " . ($column_list_id ? "AND dv.dataset_column_id in ('" . implode ( ",", $column_list_id ) . "'" : '') . "
	  				  AND dv.row_id in (" . implode ( ",", $row_ids ) . ")
	  				  AND dv.dataset_column_id in (" . implode ( ",", array_keys ( $this->columnset ) ) . ")
	  				  ORDER BY dv.row_id, dd.dataset_column_id";

			// reset search condition
			if ($this->search_condition) {
				$this->search_condition = '';
			}
			$dataset_values = $this->db->query ( $query );
			$result = $dataset_values->rows;

		}
		$order_name = $order_name == 'row_id' ? '' : $order_name;
		return $this->_createTable ( $result, $column_list , array($order_name, $order_direction, $limit, $offset));
	}

	/**
	 * This method return total rows count of dataset.
	 * @param array $filter
	 * @return integer
	 */
	public function getTotalRows($filter=array()) {

		if (! $this->dataset_id || ! $this->columnset) {
			return false;
		}
		if(!$filter){
		$query = "SELECT COUNT(DISTINCT dv.row_id) as cnt
  					FROM " . $this->db->table("dataset_values") . " dv
  					LEFT JOIN  " . $this->db->table("dataset_definition") . " dd ON dd.dataset_column_id = dv.dataset_column_id
  					WHERE dd.dataset_id = '" . $this->dataset_id . "'";

		$sql = $this->db->query ( $query );
		$total = $sql->row['cnt'];
		}else{
			$rows = $this->searchRows(array('column_name'=>$filter['column_name'], 'operator'=>$filter['operator'],'value'=>$filter['value']),null,10000);
			$total = sizeof($rows);
		}
		return $total;
	}
	
	/**
	 * funtion create table from dataset values and returns multiarray
	 * @param array $dataset_values
	 * @param array $column_names
	 * @param array $order_by
	 * @return array|bool
	 */
	private function _createTable($dataset_values = array(), $column_names = array(), $order_by= array()) {
		if (! $dataset_values || ! $this->columnset) {
			return array();
		}
		if($order_by){
			list($order_name,$order_direction,$limit,$offset) = $order_by;
		}
		$output = array ();
		
		if (is_array ( $dataset_values )) {
			foreach ( $dataset_values as $row ) {
				// then build order for resorting
				if($order_name && $row['dataset_column_name']==$order_name){
					$index[$row ['row_id']] = $row ["value_" . $this->columnset [$row ['dataset_column_id']] ['dataset_column_type']];
				}

				if(in_array($row ['dataset_column_name'],$column_names) || !$column_names){
					if(!isset($row ["value_" . $this->columnset [$row ['dataset_column_id']] ['dataset_column_type']])){
						$warning = new AWarning('Dataset inconsistency data issue detected. Dataset ID: '.$this->dataset_id.'. Column_name: '.$row ['dataset_column_name'].' Column data type: '.$this->columnset [$row ['dataset_column_id']] ['dataset_column_type']);
						$warning->toDebug();
					}
					$output [$row ['row_id']] [$row ['dataset_column_name']] = $row ["value_" . $this->columnset [$row ['dataset_column_id']] ['dataset_column_type']];
				}
			}

			// resort index (row_id)
			if($order_name){
                $order = $order_direction=='DESC' ? SORT_DESC : SORT_ASC;
                array_multisort($index,$order,$output);
			}
			// limit-offset
			if((int)$limit){
				$offset = (int)$offset;
				$offset = $offset<0 ? 0 : $offset;

				$num_rows = sizeof($output);
				$limit = $limit>$num_rows ? $num_rows : $limit;
				if($offset>=$num_rows){
					return array();
				}
				$output = array_slice($output, $offset,$limit);
			}
		}
		return $output;
	}
	
	/**
	 * method deletes dataset rows by condition
	 * 
	 * @param array $condition array("column_name"=>string, "operator"=>string,"value"=>string )
	 * @return boolean
	 */
	public function deleteRows($condition) {
		if (! $this->dataset_id || ! $this->columnset) {
			return false;
		}
		
		$this->_buildSQLSearch ( $condition );
		
		if (! $this->search_condition) {
			return false;
		}
		
		// first of all we need to know whats row number needed 
		$query = "SELECT DISTINCT dv.row_id 
  				  	FROM " . $this->db->table("dataset_values") . " dv
  				  	WHERE " . $this->search_condition;
		
		$sql = $this->db->query ( $query );
		$result = $sql->rows;
		$row_ids = array ();
		if ($result) {
			foreach ( $result as $value ) {
				$row_ids [] = $value ['row_id'];
			}
		}
		if ($row_ids) {
			$query = "DELETE FROM " . $this->db->table("dataset_values") . " 
			 			WHERE row_id in (" . implode ( ",", $row_ids ) . ") AND dataset_column_id in (" . implode ( ",", array_keys ( $this->columnset ) ) . ")";
			$this->db->query ( $query );
		}
		// return deleted rows count
		return sizeof($row_ids);
	}

	/**
	 * @param array $condition array("column_name"=>string, "operator"=>string,"value"=>string )
	 * @param array $new_values array("column_name"=>"value")
	 * @throws AException
	 * @return string
	 */
	public function updateRows($condition, $new_values) {
		if (! $this->dataset_id || ! $this->columnset || ! is_array ( $new_values )) {
			return false;
		}
		// set sql condition
		$this->_buildSQLSearch ( $condition );
		
		// first of all we need to know whats row number needed 
		$query = "SELECT DISTINCT dv.row_id 
  				  	FROM " . $this->db->table("dataset_values") . " dv
  				  	WHERE " . $this->search_condition;
		
		$sql = $this->db->query ( $query );
		$result = $sql->rows;
		$row_ids = array ();
		if ($result) {
			foreach ( $result as $value ) {
				$row_ids [] = $value ['row_id'];
			}
		}
		
		if (! $row_ids) {
			return false;
		}
		//check new value
		foreach ( $new_values as $column_name => $column_value ) {
			
			$column_id = $this->_getColumnIdByName ( $column_name );
			if (! $column_id) {
				throw new AException ( AC_ERR_LOAD, "Error: Could not update column " . $column_name . " because it's not present in dataset column definitions!" );
			}
			// check new value			
			switch ($this->columnset [$column_id] ['dataset_column_type']) {
				case 'integer' :
					$column_value = ( int ) $column_value;
					break;
				case 'float' :
					$column_value = ( float ) $column_value;
					break;
				case 'varchar' :
				case 'text' :
					$column_value = $column_value ? $this->db->escape ( $column_value ) : "";
					
					break;
				case 'boolean' :
					$column_value = $column_value ? '1' : '0';
					break;
				case 'timestamp' :
					$date = date_parse ( $column_value );
					if ($date ['errors']) {
						$column_value = false;
					}
					break;
				default :
					$column_value = '';
			
			}

			$sql = "UPDATE " . $this->db->table("dataset_values") . " 
					SET value_" . $this->columnset [$column_id] ['dataset_column_type'] . " = '" . $column_value . "'
					WHERE dataset_column_id = " . $column_id . " AND  row_id in (" . implode ( ", ", $row_ids ) . ")";

			$this->db->query ( $sql );
		}
		// return updated rows count
		return sizeof($row_ids);
	}

	/**
	 * Function returns rows of dataset table by given search condition
	 * @param array $condition
	 * @param string $order_by
	 * @param int $limit
	 * @param int $offset
	 * @internal param array $array $condition array("column_name"=>string, "operator"=>string,"value"=>string )
	 * @return array|bool
	 */
	public function searchRows($condition = array(), $order_by = 'row_id:ASC', $limit = 1000, $offset = 0) {
		if (! $this->dataset_id || ! $this->columnset) {
			return false;
		}

		$this->_buildSQLSearch ( $condition );
		return $this->getRows ( array (), $order_by, $limit, $offset );
	}

	/**
	 * function build search SQL condition by given condition array
	 * @param array $condition array("column_name"=>string, "operator"=>string,"value"=>string )
	 * @throws AException
	 * @return string
	 */
	private function _buildSQLSearch($condition = array()) {
		
		$condition ['operator'] = strtoupper ( $condition ['operator'] );
		//check column name
		$column_id = ( int ) $this->_getColumnIdByName ( $condition ['column_name'] );
		switch ($this->columnset [$column_id] ['dataset_column_type']) {
			case 'integer' :
				$condition ['value'] = ( int ) $condition ['value'];
				break;
			case 'float' :
				$condition ['value'] = ( float ) $condition ['value'];
				break;
			case 'varchar' :
			case 'text' :
				$condition ['value'] = ! in_array ( $condition ['operator'], array ('=', 'LIKE' ) ) ? false : $this->db->escape ( $condition ['value'] );
				$condition ['value'] = $condition ['operator'] == 'LIKE' ? "%" . $condition ['value'] . "%" : $condition ['value'];
				
				break;
			case 'boolean' :
				$condition ['value'] = ! in_array ( $condition ['operator'], array ('=', '<>' ) ) ? false : ($condition ['value'] ? '1' : '0');
				break;
			case 'timestamp' :
				$date = date_parse ( $condition ['value'] );
				if ($date ['errors']) {
					$condition ['value'] = false;
				}
				break;
			default :
				$condition ['value'] = $condition ['column_name'] = false;
		
		}
		// if column type is not string and compare is "LIKE" - error
		if ($condition ['operator'] == 'LIKE' && ! in_array ( $this->columnset [$column_id] ['dataset_column_type'], array ('varchar', 'text' ) )) {
		
		}
		
		$operators = array ("=", ">", "<", "<>", "LIKE" );
		if (! $condition ['column_name'] || ! $column_id || $condition ['value'] === false || ! in_array ( $condition ['operator'], $operators )) {
			throw new AException ( AC_ERR_LOAD, 'Error: Could not use ' . $condition ['operator'] . ' as compare operator in search or check column type!' );
		}
		
		$this->search_condition = " ( dv.dataset_column_id = '" . $column_id . "' AND dv.value_" . $this->columnset [$column_id] ['dataset_column_type'] . " " . $condition ['operator'] . " '" . $condition ['value'] . "') ";
		return true;
	}

	/**
	 * drop dataset with values and columnset
	 * @internal param int $dataset_id
	 * @return boolean
	 */
	public function dropDataset() {
		if (! $this->dataset_id) {
			return false;
		}
		
		if ($this->columnset) {
			$this->db->query ( "DELETE FROM " . $this->db->table("dataset_values") . " WHERE dataset_column_id in (" . implode ( ", ", array_keys ( $this->columnset ) ) . ");" );
			$this->db->query ( "DELETE FROM " . $this->db->table("dataset_column_properties") . " WHERE dataset_column_id in (" . implode ( ", ", array_keys ( $this->columnset ) ) . ");" );
		}
		
		$this->db->query ( "DELETE FROM " . $this->db->table("dataset_properties") . " WHERE dataset_id = " . $this->dataset_id . ";" );
		$this->db->query ( "DELETE FROM " . $this->db->table("dataset_definition") . " WHERE dataset_id = " . $this->dataset_id . ";" );
		$this->db->query ( "DELETE FROM " . $this->db->table("datasets") . " WHERE dataset_id = " . $this->dataset_id . ";" );
		
		$this->dataset_id = 0;
		$this->columnset = array ();
		
		return true;
	}

	/**
	 * @param string $column_name
	 * @return int | boolean
	 */
	private function _getColumnIdByName($column_name = '') {
		if (! $this->dataset_id) {
			return false;
		}
		
		if ($this->columnset) {
			foreach ( $this->columnset as $id => $column ) {
				if ($column_name == $column ['dataset_column_name']) {
					return $id;
				}
			}
		}
		return false;
	}
	
	/**
	 * Method returns column definition of dataset
	 * 
	 * @return array
	 */
	public function getColumnDefinitions() {
		if ( $this->dataset_id) {
			if (! $this->columnset) {
				$this->_getColumnSet ();
				return $this->columnset;
			} else {
				return $this->columnset;
			}
		} else {
			return null;
		}
	}

	/**
	 * @param string $data
	 */
	public function loadXML($data) {
		// Input possible with XML string, File or both.
		// We process both one at a time. XML string processed first
		

		if ($data ['xml']) {
			$xml_obj = simplexml_load_string ( $data ['xml'] );
			if (! $xml_obj) {
				$err = "Failed loading XML data string";
				foreach ( libxml_get_errors () as $error ) {
					$err .= "  " . $error->message;
				}
				$error = new AError ( $err );
				$error->toLog ()->toDebug ();
			} else {
				$this->_processXML ( $xml_obj );
			}
		}
		
		if ($data ['file'] && is_file ( $data ['file'] )) {
			$xml_obj = simplexml_load_file ( $data ['file'] );
			if (! $xml_obj) {
				$err = "Failed loading XML file " . $data ['file'];
				foreach ( libxml_get_errors () as $error ) {
					$err .= "  " . $error->message;
				}
				$error = new AError ( $err );
				$error->toLog ()->toDebug ();
			} else {
				$this->_processXML ( $xml_obj );
			}
		}
	}

	/**
	 * @param simplexmlElement $xml_obj
	 */
	private function _processXML($xml_obj) {

		$xml = $xml_obj->xpath ( '/datasets' );
		$datasets = $xml;
		//process each layout 
		foreach ( $datasets as $dataset ) {
			$dataset = $dataset->dataset;
			/* Determine an action tag in all parent elements. 
			* Action can be insert, update and delete		       
		    *   ->>> action = insert 
			*		Mean that we will try create new dataset with column definitions and insert rows in it  
		    *  ->>> action = update (default)
			*		Before loading the dataset, determin if same dataset exists with same name and key comdination. 
			*		If does exists, write new over existing
		    *  ->>> action = delete 
			*		Delete all that contains in dataset (values, definitions, properties and dataset)					
			*		NOTE: Parent level delete action is cascaded to all children elements
			*/
			
			if (! $dataset->action) {
				$dataset->action = 'update';
			}
			
			if (in_array ( $dataset->action, array ("update", "delete" ) )) {
				$this->__construct ( $dataset->dataset_name, $dataset->dataset_key );
				$this->dropDataset ();
				
				if ($dataset->action == "delete") {
					continue;
				}
			}
			if (in_array ( $dataset->action, array ("insert", "update" ) )) {
				if ($dataset->dataset_name) {
					$this->__construct ( $dataset->dataset_name, $dataset->dataset_key );
					$this->createDataset ( $dataset->dataset_name, $dataset->dataset_key );
				}
			}
			
			// check creating
			if (! $this->dataset_id) {
				continue;
			}
			
			// set dataset definition if needed
			if ($dataset->dataset_definition && $dataset->dataset_definition->column_definition) {
				$definitions = array ();
				$i = 0;
				foreach ( $dataset->dataset_definition->column_definition as $column_definition ) {

					$definitions [$i] ['name'] = ( string ) $column_definition->column_name;
					$definitions [$i] ['type'] = ( string ) $column_definition->column_type;
					if(( int ) $column_definition->column_sort_order){
						$definitions [$i] ['sort_order'] = ( int ) $column_definition->column_sort_order;
					}
					$i ++;
				}
				
				$this->defineColumns ( $definitions );
			}
			
			// set dataset properties if needed
			if ($dataset->dataset_properties && $dataset->dataset_properties->dataset_property) {
				$properties = array ();
				foreach ( $dataset->dataset_properties->dataset_property as $property ) {
					$properties [( string ) $property->dataset_property_name] = ( string ) $property->dataset_property_value;
				}
				$this->setDatasetProperties ( $properties );
			}
			
			// set column properties if needed
			if ($dataset->column_properties && $dataset->column_properties->column_property) {
				$properties = array ();
				foreach ( $dataset->column_properties->column_property as $property ) {
					$properties [( string ) $property->column_property_name] = ( string ) $property->column_property_value;
					$this->setColumnProperties ( ( string ) $property->column_name, $properties );
				}
			}
			
			// operate with dataset rows
			if ($dataset->dataset_rows && $dataset->dataset_rows->dataset_row) {
				$row_values = array ();
				foreach ( $dataset->dataset_rows->dataset_row as $row ) {
					if ($row->cell) {
						foreach ( $row->cell as $cell ) {
							$row_values [] [( string ) $cell->column_name] = ( string ) $cell->value;
						}
					}
				}
				$this->addRows ( $row_values );
			}
		
		} // end of loop
	}
}
