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

/**
 * Class to handle access to global attributes
 * 
 */
/**
 * @property ALanguageManager $language
 * @property ADB $db
 * @property ACache $cache
 * @property AConfig $config
 */
class AAttribute {
	/**
     * @var registry - access to application registry
     */
    protected $registry;
    private $attributes = array();
    private $attribute_types = array();
	
	public function __construct($attribute_type = '', $language_id = 0 ) {
		$this->registry = Registry::getInstance();
        $this->errors = array();
        $this->_load_attribute_types();
        //Preload the data with attributes for given $attribute type
        if ( $attribute_type ) {
        	$this->_load_attributes( $this->getAttributeTypeID($attribute_type), $language_id );
        }
	}

    /**
     * @param  $key - key to load data from registry
     * @return mixed  - data from registry
     */
	public function __get($key) {
		return $this->registry->get($key);
	}

    /**
     * @param  string $key - key to save data in registry
     * @param  mixed $value - key to save data in registry
	 * @return mixed  - data from registry
     */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

    private function _load_attribute_types() {
		//Load attrribute types from DB or cache.
        $cache_name = 'attribute.types';
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $attribute_types = $this->cache->get($cache_name,'',(int)$this->config->get('config_store_id'));
        if (!empty($attribute_types)) {
            $this->attribute_types = $attribute_types;
            return;
        }
        $query = $this->db->query( "SELECT at.*
									FROM `".DB_PREFIX."global_attributes_types` at
									WHERE at.status = 1 order by at.sort_order" );

        if ( !$query->num_rows ) {
            return;
        }

        $this->cache->set($cache_name, $query->rows,'',(int)$this->config->get('config_store_id'));

        $this->attribute_types = $query->rows;
	}

	/**
	 * load all the attributes for specified type
	 * @param $attribute_type_id
	 * @param int $language_id
	 */
	private function _load_attributes( $attribute_type_id, $language_id = 0 ) {
		//Load attributes from DB or cache. If load from DB, cache.
		// group attribute and sort by attribute_group_id (if any) and sort by attribute inside the group.

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $cache_name = 'attributes.'.$attribute_type_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $attributes = $this->cache->get($cache_name, (int)$language_id, (int)$this->config->get('config_store_id'));
        if (!empty($attributes)) {
            $this->attributes = $attributes;
            return;
        }

        $query = $this->db->query("
            SELECT ga.*, gad.name
            FROM `".DB_PREFIX."global_attributes` ga
                LEFT JOIN `".DB_PREFIX."global_attributes_descriptions` gad ON ( ga.attribute_id = gad.attribute_id AND gad.language_id = '" . (int)$language_id . "' )
            WHERE ga.attribute_type_id = '" . $this->db->escape( $attribute_type_id ) . "'
                AND ga.status = 1
            ORDER BY ga.sort_order"
        );

        if ( !$query->num_rows ) {
            return;
        }

        $this->cache->set($cache_name, $query->rows, (int)$language_id, (int)$this->config->get('config_store_id'));

        $this->attributes = $query->rows;
	}

    /**
     * Get details about given group for attributes
	 * @param $group_id
	 * @param int $language_id
	 * @return null
	 */
	public function getActiveAttributeGroup( $group_id, $language_id = 0 ) {

        if ( !$language_id ) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $query = $this->db->query("
            SELECT gag.*, gagd.name
            FROM `".DB_PREFIX."global_attributes_groups` gag
            LEFT JOIN `".DB_PREFIX."global_attributes_groups_descriptions` gagd ON ( gag.attribute_group_id = gagd.attribute_group_id AND gagd.language_id = '" . (int)$language_id . "' )
            WHERE gag.attribute_group_id = '" . $this->db->escape( $group_id ) . "' AND gag.status = 1
            ORDER BY gag.sort_order"
        );

	    if ( $query->num_rows ) {
            return $query->row;
	    } else {
		    return array();
	    }
	}


    /**
     * Get array of all available attribute types
	 * @return array
     */
    public function getAttributeTypes( ) {
		return $this->attribute_types;
	}


    /**
     * @param string $type
	 * @return null | int
     * Get attribute id based on attribute type_key
     */
    public function getAttributeTypeID( $type ) {
        foreach ( $this->attribute_types as $attribute_type ) {
            if ( $attribute_type['type_key']  == $type ) {
            	return $attribute_type['attribute_type_id'];
            }
		}
		return null;
	}

    /**
     * @param  $attribute_id
     * Returns total count of choldren for the atribute. No children retunrs 0
     */
    public function totalChildren( $attribute_id ) {
	    $sql = "SELECT count(*) as total_count FROM " . DB_PREFIX . "global_attributes
            WHERE attribute_parent_id = '" . (int)$attribute_id . "'";
        $attribute_data = $this->db->query( $sql );
        return $attribute_data->rows[0]['total_count'];
	}

	/**
	 * load all the attributes for specified type
	 * @param $attribute_type
	 * @param int $language_id - Language id. default 0 (english)
	 * @param int $attribute_parent_id  - Parent attribute ID if any. Default 0 (parent)
	 * @return array
	 */
	public function getAttributesByType( $attribute_type, $language_id = 0, $attribute_parent_id = 0 ) {
		if ( empty($this->attributes) ) {
			$this->_load_attributes( $this->getAttributeTypeID($attribute_type), $language_id );
		}
		if ( $attribute_parent_id == 0 ) {
			return $this->attributes; 
		} else {
			$children = array();
        	foreach ( $this->attributes as $attribute ) {
        	    if ( $attribute['attribute_parent_id']  == $attribute_parent_id ) {
                    $children[] = $attribute;
        	    }
			}	
			return $children;
		}		
	}

    /**
     * get attribute connected to option
     *
     * @param $option_id
     * @return null
     */
    public function getAttributeByProductOptionId( $option_id ) {
	    $sql = "SELECT attribute_id FROM " . DB_PREFIX . "product_options
            WHERE product_option_id = '" . (int)$option_id . "'
                AND attribute_id != 0
                ";
        $attribute_id = $this->db->query( $sql );
        if ( $attribute_id->num_rows ) {
            return $this->getAttribute($attribute_id->row['attribute_id']);
        } else {
            return null;
        }
    }

	/**
	 * @param $attribute_id - load attribute with id=$attribute_id
	 * @param int $language_id - Language id. default 0 (english)
	 * @return null | array
	 */
	public function getAttribute( $attribute_id, $language_id = 0 ) {
		if ( empty($this->attributes) ) {
			return null;
		}

        foreach ( $this->attributes as $attribute ) {
       		if ( $attribute['attribute_id']  == $attribute_id ) {
        	    return $attribute;
        	}
		}
		return null;
	}

	/**
	 * @param $attribute_id - load all the attribute values and descriptions for specified attribute id
	 * @param int $language_id - Language id. default 0 (english)
	 * @return array
	 */
	public function getAttributeValues( $attribute_id, $language_id = 0 ) {
		//get attrib values
        $cache_name = 'attribute.values.'.$attribute_id.'.'.$language_id;
        $cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
        $attribute_vals = $this->cache->get($cache_name,'',(int)$this->config->get('config_store_id'));
        if (!empty($attribute_vals)) {
            return $attribute_vals;
        }

        $query = $this->db->query("
            SELECT gav.sort_order, gav.attribute_value_id, gavd.*
            FROM `".DB_PREFIX."global_attributes_values` gav
                LEFT JOIN `".DB_PREFIX."global_attributes_value_descriptions` gavd ON ( gav.attribute_value_id = gavd.attribute_value_id AND gavd.language_id = '" . (int)$language_id . "' )
            WHERE gav.attribute_id = '" . $this->db->escape( $attribute_id ) . "'
            order by gav.sort_order"
        );

        if ( !$query->num_rows ) {
            return array();
        }

        $this->cache->set($cache_name, $query->rows,'',(int)$this->config->get('config_store_id'));
        return $query->rows;		
	}

}