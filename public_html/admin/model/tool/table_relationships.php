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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}

class ModelToolTableRelationships extends Model {
	private $tables_data = array();
	private $sections = array();

	public function __construct($registry ) {
		parent::__construct($registry);

		$this->_load_tables_configs();
	}

	private function _load_tables_configs(){


		$this->sections['resource_library'] = array(
			'id' => 'resource_id',
			'relation_ids' => array ( 'type_id' ),
			'children' => array(
				'resource_descriptions' => array(
					'id' =>  null,
					'relation_ids' => array('resource_id', 'language_id')
				),
				'resource_map' => array(
					'id' => null,
					'relation_ids' => array( 'resource_id' )
				),
			),
		);



		$this->sections['resource_types'] = array(
			'id' => 'type_id',
			'children' => array(
				'resource_library' => $this->sections['resource_library']
			)
		);
		$this->sections['categories'] = array(
			'id' => 'category_id',
			'children' => array(
				'category_descriptions' => array(
					'id' =>  null,
					'relation_ids' => array('category_id','language_id')
				),
				'categories_to_stores' => array(
					'id' => null,
					'relation_ids' => array('category_id','store_id')
				),
				/* Special case, no matching field name */
				'resource_map' => array(
					'id' => 'resource_id',
					'on_insert_no_id' => true,
					'relation_ids' => array( 'resource_id', 'object_id' ),
					'special_relation' => array( 'object_name' => 'categories', 'object_id' => 'category_id'),
					'children' => array(
						/* Note: Issue to connect to resource_library automaticaly */
						'resource_library' => $this->sections['resource_library']
					)
				),
			),
		);
		$this->sections['products'] = array(
			'id' => 'product_id',
			'children' => array(
				'product_descriptions' => array(
					'id' => null,
					'relation_ids' => array('product_id','language_id')
				),
				'products_featured' => array(
					'id' => null,
					'relation_ids' => array('product_id')
				),
				'products_related' => array(
					'id' => null,
					'relation_ids' => array('product_id', 'related_id')
				),
				'products_to_categories' => array(
					'id' => null,
					'relation_ids' => array('product_id', 'category_id')
				),
				'products_to_downloads' => array(
					'id' => null,
					'relation_ids' => array('product_id', 'download_id')
				),
				'products_to_stores' => array(
					'id' => null,
					'relation_ids' => array('product_id', 'store_id')
				),
				'product_discounts' => array(
					'id' => 'product_discount_id',
					'relation_ids' => array('product_id')
				),
				'product_options' => array(
					'id' => 'product_option_id',
					'relation_ids' => array('product_id'),
					'children' =>array(
						'product_option_descriptions' => array(
							'id' => null,
							'relation_ids' => array('product_id', 'language_id', 'product_option_id')
						),
						'product_option_values' => array(
							'id' => 'product_option_value_id',
							'relation_ids' => array('product_id', 'product_option_id'),
							'children' => array(
								'product_option_value_descriptions' => array(
									'id' => null,
									'relation_ids' => array('product_id', 'language_id', 'product_option_value_id')
								),
							)
						),
					)
				),
				'product_specials' => array(
					'id' => 'product_special_id',
					'relation_ids' => array('product_id')
				),
				/*
				* All 3 columns of the table are primary keys
				* So we can't update it. Only insert or delete
				*/
				'product_tags' => array(
					'id' => null,
					'relation_ids' => array('product_id', 'tag', 'language_id')
				),
				/* Special case, no matching field name */
				'resource_map' => array(
					'id' => 'resource_id',
					'relation_ids' => array( 'resource_id', 'object_id' ),
					'special_relation' => array( 'object_name' => 'products', 'object_id' => 'product_id'),
					'children' => array(
						'resource_library' => $this->sections['resource_library']
					)
				),
				'reviews' => array(
					'id' => null,
					'relation_ids' => array('product_id', 'review_id', 'customer_id')
				)
			)
		);
		$this->sections['orders'] = array(
			'id' => 'order_id',
			'children' => array(
				'order_downloads' => array(
					'id' => null,
					'relation_ids' => array('order_id', 'order_download_id')
				),
				'order_history' => array(
					'id' => null,
					'relation_ids' => array('order_id', 'order_history_id')
				),
				'order_options' => array(
					'id' => null,
					'relation_ids' => array('order_id', 'order_option_id')
				),
				'order_products' => array(
					'id' => null,
					'relation_ids' => array('order_id', 'order_product_id')
				),
				'order_totals' => array(
					'id' => null,
					'relation_ids' => array('order_id', 'order_total_id')
				),
			)
		);
		$this->sections['manufacturers'] = array(
			'id' => 'manufacturer_id',
			'children' => array(
				'manufacturers_to_stores' => array(
					'id' => null,
					'relation_ids' => array('manufacturer_id', 'store_id')
				),
				/* Special case, no matching field name */
				'resource_map' => array(
					'id' => 'resource_id',
					'relation_ids' => array( 'resource_id', 'object_id' ),
					'special_relation' => array( 'object_name' => 'manufacturers', 'object_id' => 'manufacturer_id'),
					'children' => array(
						'resource_library' => $this->sections['resource_library']
					)
				),
			)
		);
		$this->sections['customers'] = array(
			'id' => 'customer_id',
			'children' => array(
				'addresses' => array(
					'id' => null,
					'relation_ids' => array('customer_id', 'address_id')
				),
				'reviews' => array(
					'id' => null,
					'relation_ids' => array('customer_id', 'review_id', 'product_id')
				)
			),
		);

		$this->sections['blocks'] = array(
			'id' => 'block_id',
			'children' => array(
				'block_layouts' => array(
					'id' => 'instance_id',
					'relation_ids' => array('block_id', 'instance_id', 'layout_id', 'parent_instance_id', 'custom_block_id'),
					'children' => array(
						'layouts' => array(
							'id' => null,
							'switch_to_id' => 'layout_id',
							'relation_ids' => array('layout_id')
						)
					)
				),
				'block_templates' => array(
					'id' => null,
					'relation_ids' => array('block_id', 'parent_block_id')
				),
				'custom_blocks' => array(
					'id' => 'custom_block_id',
					'relation_ids' => array('block_id'),
					'children' => array(
						'block_descriptions' => array(
							'id' => null,
							'relation_ids' => array('custom_block_id', 'block_description_id')
						)
					)
				)
			)
		);

		$this->sections['downloads'] = array(
			'id' => 'download_id',
			'children' => array(
				'download_descriptions' => array(
					'id' => null,
					'relation_ids' => array('download_id', 'language_id')
				)
			)
		);

		// Only "add as it is". Update is left for future enhancements.
		$this->sections['url_aliases'] = array(
			'id' => 'url_alias_id'
		);
	}

	/**
	 * check if requested table exist and return its array
	 * @param string $table_name
	 * @param array $search_in_input
	 * @return null|array
	 */
	public function find_table_cfg ( $table_name, $search_in_input = array() ) {
		if ( empty($table_name) ) {
			return null;
		}

		if (count($search_in_input) <= 0) {
			$search_section = $this->sections;
		}else{
			$search_section = $search_in_input['children'];
		}

		foreach ($search_section as $sectionName => $section){
			if ($table_name == $sectionName) {
				return $section;
			} else {
				if ( is_array($section['children']) && count($section['children']) > 0 ) {
					$found_arr = $this->find_table_cfg($table_name, $section);
					if ($found_arr) {
						return $found_arr;
					}
				}
			}
		}
		return null;
	}

	/**
	 * @param array $sections
	 * @return bool
	 */
	public function setSections($sections = array()){
		if ( !empty($sections) )
		{
			$this->sections = $sections;
			return true;
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function getSections(){
		return $this->sections;
	}

	private function _build_table_relationship() {
		//NOTE: Not used see below in _build_relathionship
		$cache_file = 'tables.key.relationship';

		if($this->cache){
			$this->tables_data = $this->cache->get($cache_file);
		}
		if (!$this->tables_data) {
			$sql = "SELECT table_name, column_name, extra 
			    	FROM information_schema.columns 
			    	WHERE table_schema='" . DB_DATABASE . "' group by table_name";
			$load_sql = $this->db->query($sql);
			$tables = $load_sql->rows;

			foreach ($tables as $table) {
				//get primary keys
				$pkeys = array();
				$sql = "SHOW INDEX FROM " . $table['table_name'] . "
			        	WHERE Key_name = 'PRIMARY'";
				$primary_query = $this->db->query($sql);
				foreach($primary_query->rows as $value) {
					$pkeys[] = $value['Column_name'];
				}

				$table_name = $table['table_name'];

				$this->tables_data[$table_name] = array();
				if ( $table['extra'] == 'auto_increment' ) {
					$this->tables_data[$table_name]['id'] = $table['column_name'];
				} else {
					$this->tables_data[$table_name]['id'] = NULL;
				}
				$this->tables_data[$table_name]['relation_ids'] = $pkeys;

			}
			$this->_build_relathionship();
			$this->_apply_special_cases();

			if($this->cache){
				$this->cache->set($cache_file, $table_data);
			}
		}
	}

	private function _build_relathionship() {
		//Need o connect tables with relationship IDs. 
		//NOT IMPLEMENTED. Issue with logic to detect id and content. Slow and unreliable solution.
		// Looking for better solution to automate or us TRUE relational DB with Postgres ot InnoDB with mysql
	}
	private function _apply_special_cases() {
		//Need o connect tables with relationship IDs.
		//NOT IMPLEMENTED. Issue with logic to detect id and content. Slow and unreliable solution.
		// Looking for better solution to automate or us TRUE relational DB with Postgres ot InnoDB with mysql
	}

	/**
	 * @param string $table_name
	 * @return null|array
	 */
	public function get_table_cfg ( $table_name ) {
		//NOTE: DO not use. Not implemented yet. 
		if ( empty($table_name) ) {
			return null;
		}

		if (!$this->tables_data ) {
			$this->_build_table_relationship();
		}

		if( $this->tables_data[DB_PREFIX.$table_name] ) {
			return $this->tables_data[DB_PREFIX.$table_name];
		} else {
			return null;
		}

	}

	/**
	 * @param string $table_name
	 * @return array
	 */
	public function get_table_columns ( $table_name ) {
		$sql = 'SHOW COLUMNS FROM `' . $this->db->escape(DB_PREFIX.$table_name) . '` FROM `' . DB_DATABASE . '`';

		$results = $this->db->query($sql);
		return $results->rows;
	}

}