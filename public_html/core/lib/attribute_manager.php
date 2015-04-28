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
 * Class to handle access to global attributes
 * @property Asession $session
 */
 
class AAttribute_Manager extends AAttribute {
	
	public function __construct($attribute_type = '', $language_id = 0 ) {
		parent::__construct($attribute_type, $language_id );
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AAttribute_Manager');
		}		
	}

    public function clearCache() {
        $this->cache->delete('attribute.types');
        $this->cache->delete('attribute.groups');
        $this->cache->delete('attributes');
        $this->cache->delete('attribute.values');
    }

	/**
	 * @param int $attribute_id
	 */
	public function deleteAttribute($attribute_id) {

        $this->db->query("DELETE FROM " . $this->db->table("global_attributes") . " 
            WHERE attribute_id = '" . $this->db->escape($attribute_id) . "' ");
        $this->db->query("DELETE FROM " . $this->db->table("global_attributes_descriptions") . " 
            WHERE attribute_id = '" . $this->db->escape($attribute_id) . "' ");
		$this->db->query("DELETE FROM " . $this->db->table("global_attributes_values") . " 
            WHERE attribute_id = '" . $this->db->escape($attribute_id) . "' ");
		$this->db->query("DELETE FROM " . $this->db->table("global_attributes_value_descriptions") . " 
            WHERE attribute_id = '" . $this->db->escape($attribute_id) . "' ");

        $this->clearCache();
    }

	/**
	 * @param array $data
	 * @return bool|int
	 */
	public function addAttribute($data) {
		if(!$data['name']) return false;
	    $language_id = $this->session->data['content_language_id'];
	    
        $this->db->query(
							"INSERT INTO " . $this->db->table("global_attributes") . " 
							 SET attribute_type_id = '" . $this->db->escape($data['attribute_type_id']) . "',
								attribute_group_id = '" . $this->db->escape($data['attribute_group_id']) . "',
								attribute_parent_id = '" . $this->db->escape($data['attribute_parent_id']) . "',
								element_type = '" . $this->db->escape($data['element_type']) . "',
								sort_order = '" . $this->db->escape($data['sort_order']) . "',
								required = '" . $this->db->escape($data['required']) . "',
								settings = '" . $this->db->escape(serialize($data['settings'])) . "',
								status = '" . $this->db->escape($data['status']) . "',
								regexp_pattern = '" . $this->db->escape($data['regexp_pattern']) . "'");

	    $attribute_id = $this->db->getLastId();
		// insert descriptions for used content language and translate 
		$this->language->replaceDescriptions('global_attributes_descriptions',
											 array('attribute_id' => (int)$attribute_id),
											 array($language_id => array(
												 						'name' => $data['name'],
												 						'error_text' => $data['error_text'],
											 								)) );

		if ( !empty($data['values']) ) {
			$data['values'] = array_unique($data['values']);
			foreach ( $data['values'] as $id => $value  ) {
				$attribute_value_id = $this->addAttributeValue($attribute_id, $data['sort_orders'][$id]);
        		$this->addAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value);
			}
		}

        $this->clearCache();
        return $attribute_id;
    }

	/**
	 * @param int $attribute_id
	 * @param array $data
	 */
	public function updateAttribute($attribute_id, $data) {
		//Note: update is done per 1 language 
	    $language_id = $this->session->data['content_language_id'];
        $fields = array( 'attribute_type_id',
                         'attribute_group_id',
                         'attribute_parent_id',
                         'element_type',
                         'required',
                         'sort_order',
						 'settings',
                         'status',
						 'regexp_pattern');
		$elements_with_options = HtmlElementFactory::getElementsWithOptions();
	    $attribute = $this->getAttribute($attribute_id, $language_id);

	    //check if we change element type and clean options if it does not require it
	    if ( isset($data['element_type']) && $data['element_type'] != $attribute['element_type'] ) {
		    if ( !in_array($data['element_type'], $elements_with_options) ) {
			    $sql = "DELETE FROM " . $this->db->table("global_attributes_values") . " 
						WHERE attribute_id = '" . (int)$attribute_id . "'";
			    $this->db->query( $sql );
			    $sql = "DELETE FROM " . $this->db->table("global_attributes_value_descriptions") . " 
						WHERE attribute_id = '" . (int)$attribute_id . "'";
			    $this->db->query( $sql );
		    }
	    }

		if ( has_value($data['settings']) ) {
			$data['settings'] = serialize($data['settings']);
		}

        $update = array();
        foreach ( $fields as $f ) {
            if ( isset($data[$f]) ) {
                $update[] = $f." = '".$this->db->escape($data[$f])."'";
			}
        }
        if ( !empty($update) ) {
	        $sql = "UPDATE " . $this->db->table("global_attributes") . " 
                SET ". implode(',', $update) ."
                WHERE attribute_id = '" . (int)$attribute_id . "'";
            $this->db->query( $sql );
        }

		$update = array();
		if(isset($data['name'])){
			$update['name'] = $data['name'];
		}
		if(isset($data['error_text'])){
			$update['error_text'] = $data['error_text'];
		}

		$this->language->replaceDescriptions('global_attributes_descriptions', 
											 array('attribute_id' => (int)$attribute_id),
											 array($language_id => $update) );

		//Update Attribute Values
	    if ( !empty($data['values']) && in_array($data['element_type'], $elements_with_options) ) {
			foreach ( $data['values'] as $atr_val_id=>$value ) {
	    	//Check if new or update			
		    	if ( $data['attribute_value_ids'][$atr_val_id] == 'delete' ) {
		    		//delete the description
		    		$this->deleteAllAttributeValueDescriptions($atr_val_id);
		    		//delete value if no other language
		    		$this->deleteAttributeValues($atr_val_id);
		    	}
		    	else if ( $data['attribute_value_ids'][$atr_val_id] == 'new') {
		    		// New need to create
		    		$attribute_value_id = $this->addAttributeValue($attribute_id, $data['sort_orders'][$atr_val_id]);
		    		if($attribute_value_id){
		    			$this->addAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value);
		    		}
		    	} else {
		    		//Existing need to update
		    		$this->updateAttributeValue($atr_val_id, $data['sort_orders'][$atr_val_id]);
		    		$this->updateAttributeValueDescription($attribute_id, $atr_val_id, $language_id, $value);
		    	}
	    	}
        }

        $this->clearCache();

    }

	/**
	 * @param int $attribute_id
	 * @param int $sort_order
	 * @return bool|int
	 */
	public function addAttributeValue($attribute_id, $sort_order) {
		if ( empty($attribute_id) ) {
			return false;
		}		
		$sql = "INSERT INTO " . $this->db->table("global_attributes_values") . " 
		  		SET attribute_id = '" . (int)$attribute_id . "',
		  			sort_order = '" . (int)$sort_order . "'";
		$this->db->query( $sql );
		return $this->db->getLastId();		
    	
	}

	/**
	 * @param int $attribute_value_id
	 * @return bool
	 */
	public function deleteAttributeValues($attribute_value_id) {
		if ( empty($attribute_value_id) ) {
			return false;
		}
		//Delete global_attributes_values that have no values left
    	$sql = "DELETE FROM " . $this->db->table("global_attributes_values") . " 
    	    	WHERE attribute_value_id = '" . (int)$attribute_value_id . "' AND attribute_value_id not in 
    	    	( SELECT attribute_value_id FROM " . $this->db->table("global_attributes_value_descriptions") . "  
    	    			 WHERE attribute_value_id = '" . $attribute_value_id . "' )";
    	$this->db->query( $sql );
    	$this->clearCache();
		return true;
	}

	/**
	 * @param int $attribute_value_id
	 * @param int $sort_order
	 * @return bool
	 */
	public function updateAttributeValue($attribute_value_id, $sort_order) {
		if ( empty($attribute_value_id) ) {
			return false;
		}
		
		$sql = "UPDATE " . $this->db->table("global_attributes_values") . " 
						SET sort_order = '" . (int) $sort_order ."'
						WHERE attribute_value_id = '" . (int)$attribute_value_id . "'";
        $this->db->query( $sql );
    	$this->clearCache();
		return true;
	}

	/**
	 * @param int $attribute_id
	 * @param int $attribute_value_id
	 * @param int $language_id
	 * @param string $value
	 * @return bool
	 */
	public function addAttributeValueDescription ($attribute_id, $attribute_value_id, $language_id, $value){
		if ( empty($attribute_id) || empty($attribute_value_id) || empty($language_id) ) {
			return false;
		}

		$this->language->replaceDescriptions('global_attributes_value_descriptions',
											 array('attribute_id' => (int)$attribute_id, 'attribute_value_id' => (int)$attribute_value_id ),
											 array($language_id => array('value' => $value)) );

        $this->clearCache();
		return true;
	}

	/**
	 * @param int $attribute_id
	 * @param int $attribute_value_id
	 * @param int $language_id
	 * @param string $value
	 * @return bool
	 */
	public function updateAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value) {
		if ( empty($attribute_id) || empty($attribute_value_id) || empty($language_id) ) {
			return false;
		}
		
		//Delete and add operation 
		$this->deleteAttributeValueDescription($attribute_value_id, $language_id);
		$this->addAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value);
		
		$this->clearCache();
		return true;
	}

	/**
	 * @param int $attribute_value_id
	 * @return bool
	 */
	public function deleteAllAttributeValueDescriptions($attribute_value_id) {
		if ( empty($attribute_value_id) ) {
			return false;
		}
		$this->language->deleteDescriptions('global_attributes_value_descriptions', 
											 array('attribute_value_id' => (int)$attribute_value_id ));
        $this->clearCache();
		return true;
	}

	/**
	 * @param int $attribute_value_id
	 * @param int $language_id
	 * @return bool
	 */
	public function deleteAttributeValueDescription($attribute_value_id, $language_id) {
		if ( empty($attribute_value_id) || empty($language_id) ) {
			return false;
		}

		$this->language->deleteDescriptions('global_attributes_value_descriptions', 
											 array(	'attribute_value_id' => (int)$attribute_value_id, 
											 		'language_id' => (int)$language_id
											 	  )
											 );
        $this->clearCache();
		return true;
	}

	/**
	 * @param int $group_id
	 * @return mixed
	 */
	public function deleteAttributeGroup($group_id) {

        $this->db->query("DELETE FROM " . $this->db->table("global_attributes_groups") . " 
                          WHERE attribute_group_id = '" . (int)$group_id . "' ");
        $this->db->query( "DELETE FROM " . $this->db->table("global_attributes_groups_descriptions") . " 
                           WHERE attribute_group_id = '" . (int)$group_id . "' ");
        $this->db->query(
            "UPDATE " . $this->db->table("global_attributes") . " 
             SET attribute_group_id = ''
             WHERE attribute_group_id = '" . (int)$group_id . "' ");
        $this->clearCache();
    }

	/**
	 * @param array $data
	 * @return int
	 */
	public function addAttributeGroup($data) {

        $this->db->query(
            "INSERT INTO " . $this->db->table("global_attributes_groups") . " 
             SET sort_order = '" . (int)$data['sort_order'] . "',
                 status = '" . (int)$data['status'] . "' ");

        $group_id = $this->db->getLastId();
        $language_id = $this->session->data['content_language_id'];

		$this->language->replaceDescriptions('global_attributes_groups_descriptions',
										array('attribute_group_id' => (int)$group_id),
										array($language_id=>array( 'name'=>$data['name'])));
        $this->clearCache();
        return $group_id;
    }

	/**
	 * @param int $group_id
	 * @param array $data
	 */
	public function updateAttributeGroup($group_id, $data) {

        $fields = array('sort_order', 'status');
        $update = array();
        foreach ( $fields as $f ) {
            if ( isset($data[$f]) )
                $update[] = $f." = '".(int)$data[$f]."'";
        }
        if ( !empty($update) ) {
            $this->db->query(
                "UPDATE " . $this->db->table("global_attributes_groups") . " 
                SET ". implode(',', $update) ."
                WHERE attribute_group_id = '" . (int)$group_id . "'");
        }

        if ( !empty($data['name']) ) {
            $language_id = $this->session->data['content_language_id'];

			$this->language->replaceDescriptions('global_attributes_groups_descriptions',
											array('attribute_group_id' => (int)$group_id),
											array($language_id=>array( 'name'=>$data['name'])));

        }
        $this->clearCache();
    }

	/**
	 * Get details about given group for attributes
	 * @param int $group_id
	 * @param int $language_id
	 * @return array
	 */
	public function getAttributeGroup( $group_id, $language_id = 0 ) {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $query = $this->db->query("
            SELECT gag.*, gagd.name
            FROM " . $this->db->table("global_attributes_groups") . " gag
                LEFT JOIN " . $this->db->table("global_attributes_groups_descriptions") . " gagd
                	ON ( gag.attribute_group_id = gagd.attribute_group_id AND gagd.language_id = '" . (int)$language_id . "' )
            WHERE gag.attribute_group_id = '" . (int) $group_id . "'"
        );

	    if ( $query->num_rows ) {
            return $query->row;
        } else {
            return array();
        }
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function getAttributeGroups( $data = array() ) {

        if ( !$data['language_id'] ) {
            $data['language_id'] = $this->config->get('storefront_language_id');
        }

        $sql = "SELECT gag.*, gagd.name
            	FROM " . $this->db->table("global_attributes_groups") . " gag
                LEFT JOIN " . $this->db->table("global_attributes_groups_descriptions") . " gagd
                ON ( gag.attribute_group_id = gagd.attribute_group_id AND gagd.language_id = '" . (int)$data['language_id'] . "' )";


        $sort_data = array(
            'gagd.name',
            'gag.sort_order',
            'gag.status',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY gag.sort_order, gagd.name ";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

	/**
	 * @param array $data
	 * @return array
	 */
	public function getTotalAttributeGroups( $data = array() ) {

        if ( !$data['language_id'] ) {
            $data['language_id'] = $this->config->get('storefront_language_id');
        }

        $sql = "SELECT gag.*, gagd.name
            FROM " . $this->db->table("global_attributes_groups") . " gag
                LEFT JOIN " . $this->db->table("global_attributes_groups_descriptions") . " gagd ON ( gag.attribute_group_id = gagd.attribute_group_id AND gagd.language_id = '" . (int)$data['language_id'] . "' )";

        $query = $this->db->query($sql);
        return $query->num_rows;
    }

	/**
	 * @param int $attribute_id
	 * @param int $language_id
	 * @return array|null
	 */
	public function getAttribute( $attribute_id, $language_id = 0 ) {

        if ( !$language_id ) {
            $language_id = $this->session->data['content_language_id'];
        }

        $query = $this->db->query( "SELECT ga.*, gad.name, gad.error_text
									FROM " . $this->db->table("global_attributes") . " ga
										LEFT JOIN " . $this->db->table("global_attributes_descriptions") . " gad
										ON ( ga.attribute_id = gad.attribute_id AND gad.language_id = '" . (int)$language_id . "' )
									WHERE ga.attribute_id = '" . (int)$attribute_id . "'" );
	    if ( $query->num_rows ) {
            return $query->row;
        } else {
            return array();
        }
	}

	/**
	 * @param int $attribute_id
	 * @return array
	 */
	public function getAttributeDescriptions($attribute_id) {
        $query = $this->db->query( "SELECT *
									FROM " . $this->db->table("global_attributes_descriptions") . "
									WHERE attribute_id = '" . $this->db->escape( $attribute_id ) . "'" );
        $result = array();
        foreach ( $query->rows as $row ) {
            $result[ $row['language_id'] ] = array( 'name'=> $row['name'],
													'error_text'=> $row['error_text']
												  );
        }
	    return $result;
	}

	/**
	 * @param int $attribute_id
	 * @param int $language_id
	 * @return array
	 */
	public function getAttributeValues($attribute_id, $language_id = 0) {
        if ( !$language_id ) {
            $language_id = $this->session->data['content_language_id'];
        }
        $query = $this->db->query( "SELECT ga.*, gad.value
									FROM " . $this->db->table("global_attributes_values") . " ga
										LEFT JOIN " . $this->db->table("global_attributes_value_descriptions") . " gad
										ON ( ga.attribute_value_id = gad.attribute_value_id AND gad.language_id = '" . (int)$language_id . "' )
									WHERE ga.attribute_id = '" . $this->db->escape( $attribute_id ) . "'
									ORDER BY sort_order"
        );	
	    return $query->rows;
	}

	/**
	 * @param int $attribute_value_id
	 * @return array
	 */
	public function getAttributeValueDescriptions($attribute_value_id) {
        $query = $this->db->query("
            SELECT *
            FROM " . $this->db->table("global_attributes_value_descriptions") . " 
            WHERE attribute_value_id = '" . $this->db->escape( $attribute_value_id ) . "'"
        );
        $result = array();
        foreach ( $query->rows as $row ) {
            $result[ $row['language_id'] ] = $row['value'];
        }
	    return $result;
	}

	/**
	 * @param array $data
	 * @param int $language_id
	 * @param null|int $attribute_parent_id
	 * @param string $mode
	 * @return array
	 */
	public function getAttributes( $data = array(), $language_id = 0, $attribute_parent_id = null, $mode = 'default' ) {

        if ( !$language_id ) {
            $language_id = $this->session->data['content_language_id'];
        }

		//Prepare filter config
		$filter_params = array('attribute_parent_id', 'status');
		if(!has_value($data['attribute_type_id'])){
			$filter_params[] = 'attribute_type_id'; // to prevent ambigious fields in sql query
		}
		//Build query string based on GET params first
		$filter_form = new AFilter( array( 'method' => 'get', 'filter_params' => $filter_params ) );
		//Build final filter
		$grid_filter_params = array( 'name' => 'gad.name', 'type_name' => 'gatd.type_name' );
		$filter_grid = new AFilter( array( 'method' => 'post',
										   'grid_filter_params' => $grid_filter_params,
										   'additional_filter_string' => $filter_form->getFilterString()
										  ) );
		$filter_data = $filter_grid->getFilterData();
		$data = array_merge($filter_data, $data);

		if ($mode == 'total_only') {
			$total_sql = 'count(*) as total';
		}
		else {
			$total_sql = "ga.*, gad.name, gad.error_text, gatd.type_name ";
		}

        $sql = "SELECT ". $total_sql ."
            	FROM ". $this->db->table("global_attributes") . " ga
                LEFT JOIN ". $this->db->table("global_attributes_descriptions") . " gad
                	ON ( ga.attribute_id = gad.attribute_id AND gad.language_id = '" . (int)$language_id . "' )
				LEFT JOIN ". $this->db->table("global_attributes_type_descriptions") . " gatd
					ON ( gatd.attribute_type_id = ga.attribute_type_id AND gatd.language_id = '" . (int)$language_id . "' )
				WHERE 1=1 ";
        if ( !empty($data['search']) ) {
            $sql .= " AND ".$data['search'];
        }
        if ( !empty($data['subsql_filter']) ) {
            $sql .= " AND ".$data['subsql_filter'];
        }
		if (empty($data['search']) && !is_null($attribute_parent_id) ) {
            $sql .= " AND ga.attribute_parent_id = '".(int)$attribute_parent_id."' ";
        }

		if ( !empty($data['attribute_type_id']) ) {
			$sql .= " AND ga.attribute_type_id = ".(int)$data['attribute_type_id'];
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}

        $sort_data = array(
            'name' => 'gad.name',
            'sort_order' => 'ga.sort_order',
            'status' => 'ga.status',
            'type_name' => 'gatd.type_name',
        );

        if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $sort_data[$data['sort']];
        } else {
            $sql .= " ORDER BY ga.sort_order, gad.name ";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (has_value($data['start']) || has_value($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

	/**
	 * @param array $data
	 * @param int $language_id
	 * @param null $attribute_parent_id
	 * @return int
	 */
	public function getTotalAttributes( $data = array(), $language_id = 0, $attribute_parent_id = null ) {
		return $this->getAttributes($data, $language_id, $attribute_parent_id, 'total_only');
    }

	/**
	 * @return array
	 */
	public function getLeafAttributes() {
		$query = $this->db->query(
			"SELECT t1.attribute_id as attribute_id FROM " . $this->db->table("global_attributes") . " AS t1 LEFT JOIN " . $this->db->table("global_attributes") . " as t2
			 ON t1.attribute_id = t2.attribute_parent_id WHERE t2.attribute_id IS NULL");
		$result = array();
		foreach ( $query->rows as $r ) {
			$result[$r['attribute_id']] = $r['attribute_id'];
		}

		return $result;
	}

	/**
	 * common method for external validation of attribute
	 * @param array $data
	 * @return array
	 */
	public function validateAttributeCommonData($data=array()) {
			$error = array();
			$this->load->language('catalog/attribute');
			// required
			if (empty($data[ 'attribute_type_id' ])) {
				$this->error[ 'attribute_type' ] = $this->language->get('error_required').': "attribute_type_id"';
			}
			// required
			if ((mb_strlen($data[ 'name' ]) < 2) || (mb_strlen($data[ 'name' ]) > 64)) {
				$error[ 'name' ] = $this->language->get('error_attribute_name');
			}
			// not required
			if (mb_strlen($data[ 'error_text' ]) > 255) {
				$error[ 'error_text' ] = $this->language->get('error_error_text');
			}
			// required
			if (empty($data[ 'element_type' ])) {
				$error[ 'element_type' ] = $this->language->get('error_required').': "element_type"';
			}
			if (has_value($data['regexp_pattern'])) {
				if (@preg_match($data[ 'regexp_pattern' ], "AbanteCart") === false) {
					$error[ 'regexp_pattern' ] = $this->language->get('error_regexp_pattern');
				}
			}

			return $error;
	}
}