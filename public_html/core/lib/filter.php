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

final class AFilter {

    private $registry;
    private $data = array();
    private $method;

    //NOTE: Filter class automaticaly gets values from POST or GET on construct
    public function __construct($filter_conf) {
        $this->registry = Registry::getInstance();
        //build the a filter instance
        $this->method = $filter_conf['method'];
        if (empty($this->method)) {
            //Default data input method is post
            $this->method = 'post';
        }

        //Build data request parameters
        $this->data['page'] = $this->request->{$this->method}['page']; // get the requested page
        $this->data['page'] = $this->data['page'] ? $this->data['page'] : 1;

        $this->data['rows'] = $this->request->{$this->method}['limit']; // get how many rows we want to have into the grid
        $this->data['rows'] = $this->data['rows'] ? $this->data['rows'] : $this->request->{$this->method}['rows'];
        $this->data['rows'] = $this->data['rows'] ? $this->data['rows'] : 10;

		$this->data['sidx'] = $this->request->{$this->method}['sidx']; // get index row - i.e. user click to sort
        $this->data['sidx'] = $this->data['sidx'] ? $this->data['sidx'] : 'sort_order';
        $this->data['sord'] = $this->request->{$this->method}['sord']; // get the direction of sorting
        $this->data['sord'] = $this->data['sord'] ? strtoupper($this->data['sord']) : 'DESC';
        $this->data['_search'] = $this->request->{$this->method}['_search'];
        $this->data['filters'] = $this->request->{$this->method}['filters'];
        //If this data is provided in the input override values of the request.
		if (sizeof($filter_conf['input_params'])) {
			foreach ($filter_conf['input_params'] as $k=>$v) {
				if (has_value($v)) {
					$this->data[$k] = $v;
				}
			}
		}

        //Build Filter Data for result output and model query
        $this->data['filter_data'] = array(
            'sort' => $this->data['sidx'],
            'order' => $this->data['sord'],
            'limit' => $this->data['rows'],
            'start' => ($this->data['page'] - 1) * $this->data['rows'],
            'content_language_id' => $this->session->data['content_language_id'],
        );

        //Validate fileds that are allowed and build expected filter parameters
        if (sizeof($filter_conf['filter_params'])) {

        	//support table name extension in fields 
        	//check if we have associative array
        	if ( is_assoc($filter_conf['filter_params']) ) {
        		$keys_arr = array_keys($filter_conf['filter_params']);
        	}
        	else{
        		$keys_arr = $filter_conf['filter_params'];
        	}
	        $fl_str = array();
            foreach ($keys_arr as $kk=>$filter) {
                $value = isset($this->request->{$this->method}[$filter]) ? $this->request->{$this->method}[$filter] : FALSE;
                if ($value == '') {
                    $value = NULL;
                }
                if (isset($value) && !is_null($value)) {
                    $this->data['filter_data']['filter'][$filter] = $value;
                    
                    if( is_assoc($filter_conf['filter_params']) ){
                    	//support table name extension in fields 
                    	$field_name = $filter_conf['filter_params'][$filter];
                    	if (strpos($field_name, '.')) {
                        	$parts = explode('.', $field_name);
                        	$fl_str[$kk] = ' ' . $parts[0] . '.`' . $parts[1] . '`';
                    	} else {
                        	$fl_str[$kk] = ' `' . $field_name . '`';
                    	}
                    } else {
                    	$fl_str[$kk] .= " `" . $filter . "`";
                    }
                    $fl_str[$kk] .= " = '" . $this->db->escape($value) . "' ";
                }
            }
            $this->data['filter_string'] = implode(' AND ',$fl_str);
        }
        
        $allowedSortDirection = array('ASC', 'DESC');
        if (!in_array($this->data['sord'], $allowedSortDirection)) {
            $this->data['sord'] = 'DESC';
        }

        //Optional advanced filtering based on jQgrid filtering/search format and data formated from JSON string input
        $adv_filter_str = $filter_conf['additional_filter_string'];
        $grid_lib = new AGrid($this->method, $this->data);
        $adv_filter_str = $grid_lib->filter($adv_filter_str, $filter_conf['grid_filter_params']);
        $this->data['filter_data']['subsql_filter'] = $adv_filter_str;
        //Done Setting Filter Data
    }

    //Culculate total number of pages based on total result and rows (result set to show)
    public function calcTotalPages($total_result) {
        if ($total_result > 0 && $this->data['rows'] > 0) {
            $total_pages = ceil($total_result / $this->data['rows']);
        } else {
            $total_pages = 0;
        }
        return $total_pages;
    }

    // Return SQL Like string for allowed input parameters
    public function getFilterString() {
		return $this->data['filter_string'];
    }

    public function getParam($param_name) {
        return $this->data[$param_name];
    }

    public function getFilterParam($param_name) {
        return $this->data['filter_data']['filter'][$param_name];
    }

    public function getFilterData() {
        return $this->data['filter_data'];
    }
	
	//Build URI based on current filter params used
    public function buildFilterURI( $exclude_list = array() ) {
        $uri = '';
        $process_array = array();
        //add input params from different parts of filter    
        foreach (array_keys( $this->data ) as $param) {
        	//skip some params.
        	if ( in_array($param, array('filter_data','filter_string','page')) ) {
        		continue;
        	}
        
            if ( has_value($this->data[$param]) ) {
                $process_array[$param] = $this->data[$param];
            }
        }   
        $filter_arr = $this->data['filter_data']['filter'];
        if ( $filter_arr ) {
	        foreach (array_keys( $filter_arr ) as $param) {
	            if ( has_value($filter_arr[$param]) ) {
	                $process_array[$param] = $filter_arr[$param];
	            }
	        }       
        }
        
        //sort array to mintain the order
        asort($process_array);                 
        $uri = $this->html->buildURI($process_array, $exclude_list);                             
        return $uri;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }
}

/**
 * Class AGrid
 * @property ALoader $load
 * @property ARequest $request
 */
final class AGrid {
	/**
	 * @var Registry
	 */
	private $registry;
    private $method;
    private $search;
    private $filters;

	/**
	 * @param string $method
	 * @param array $data
	 */
	public function __construct($method, $data) {
        $this->registry = Registry::getInstance();
        $this->method = $method;
        if (empty($this->method)) {
            //Default data input method is post
            $this->method = 'post';
        }
        //Set data from GET/POST or input
        $this->search = $this->request->{$this->method}['_search'];
        if ($data['_search']) {
            $this->search = $data['_search'];
        }
        $this->filters = $this->request->{$this->method}['filters'];
        if ($data['filters']) {
            $this->filters = $data['filters'];
        }
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

	/**
	 * @param string $adv_filter_str
	 * @param array $allowedFields
	 * @return string
	 */
	public function filter($adv_filter_str, $allowedFields) {

        $allowedOperations = array('AND', 'OR');
        $search_param = array();

        if (isset($this->search) && $this->search == 'true') {

            $this->load->library('json');
            $searchData = AJson::decode(htmlspecialchars_decode($this->filters), true);
            $op = $searchData['groupOp'];
            
            if (!in_array($op, $allowedOperations)) {
                $op = $allowedOperations[0];
            }

            if ($searchData['rules']) {
                foreach ($searchData['rules'] as $rule) {

            		// $allowedFields can be simple or key based array
            		$field_name = '';
            		if ( is_assoc($allowedFields) ) {
            			if( !array_key_exists($rule['field'], $allowedFields) ) continue;
            			$field_name = $allowedFields[$rule['field']];
            		} else {
            			if (!in_array($rule['field'], $allowedFields)) continue;
            			$field_name = $rule['field'];
            		}
					//support table name extension in fields
                    if (strpos($field_name, '.')) {
                        $parts = explode('.', $field_name);
                        $str = $parts[0] . '.`' . $parts[1] . '`';
                    } else {
                        $str = '`' . $field_name . '`';
                    }

                    switch ($rule['op']) {
                        case 'eq' :
                            $str .= " = '" . $this->db->escape($rule['data']) . "' ";
                            break;
                        case 'ne' :
                            $str .= " != '" . $this->db->escape($rule['data']) . "' ";
                            break;
                        case 'bw' :
                            $str = "LOWER(" . $str . ")";
                            $rule['data'] = mb_strtolower($rule['data']);
                            $str .= " LIKE '" . $this->db->escape($rule['data']) . "%' ";
                            break;
                        case 'cn' :
                            $str = "LOWER(" . $str . ")";
		                    $rule['data'] = mb_strtolower($rule['data']);
                            $str .= " LIKE '%" . $this->db->escape($rule['data']) . "%' ";
                            break;
                        default:
                            $str .= " = '" . $this->db->escape($rule['data']) . "' ";
                    }
                    $search_param[] = $str;
                }
            }
        }

        if (!empty($search_param)) {
            $adv_filter_str .= (!empty($adv_filter_str) ? ' AND ' : '') . implode(" $op ", $search_param);
        }

        return $adv_filter_str;
    }

}