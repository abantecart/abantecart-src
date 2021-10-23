<?php
/** @noinspection SqlDialectInspection */

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelToolTableRelationships extends Model
{
    private $tables_data = [];
    private $sections = [];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->_load_tables_configs();
    }

    private function _load_tables_configs()
    {
        $this->sections['resource_library'] = [
            'id'           => 'resource_id',
            'relation_ids' => ['type_id'],
            'children'     => [
                'resource_descriptions' => [
                    'id'           => null,
                    'relation_ids' => ['resource_id', 'language_id'],
                ],
                'resource_map'          => [
                    'id'           => null,
                    'relation_ids' => ['resource_id'],
                ],
            ],
        ];

        $this->sections['resource_types'] = [
            'id'       => 'type_id',
            'children' => [
                'resource_library' => $this->sections['resource_library'],
            ],
        ];
        $this->sections['categories'] = [
            'id'       => 'category_id',
            'children' => [
                'category_descriptions' => [
                    'id'           => null,
                    'relation_ids' => ['category_id', 'language_id'],
                ],
                'categories_to_stores'  => [
                    'id'           => null,
                    'relation_ids' => ['category_id', 'store_id'],
                ],
                /* Special case, no matching field name */
                'resource_map'          => [
                    'id'               => 'resource_id',
                    'on_insert_no_id'  => true,
                    'relation_ids'     => ['resource_id', 'object_id'],
                    'special_relation' => ['object_name' => 'categories', 'object_id' => 'category_id'],
                    'children'         => [
                        /* Note: Issue to connect to resource_library automatically */
                        'resource_library' => $this->sections['resource_library'],
                    ],
                ],
            ],
        ];
        $this->sections['products'] = [
            'id'       => 'product_id',
            'children' => [
                'product_descriptions'   => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'language_id'],
                ],
                'products_featured'      => [
                    'id'           => null,
                    'relation_ids' => ['product_id'],
                ],
                'products_related'       => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'related_id'],
                ],
                'products_to_categories' => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'category_id'],
                ],
                'products_to_downloads'  => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'download_id'],
                ],
                'products_to_stores'     => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'store_id'],
                ],
                'product_discounts'      => [
                    'id'           => 'product_discount_id',
                    'relation_ids' => ['product_id'],
                ],
                'product_options'        => [
                    'id'           => 'product_option_id',
                    'relation_ids' => ['product_id'],
                    'children'     => [
                        'product_option_descriptions' => [
                            'id'           => null,
                            'relation_ids' => ['product_id', 'language_id', 'product_option_id'],
                        ],
                        'product_option_values'       => [
                            'id'           => 'product_option_value_id',
                            'relation_ids' => ['product_id', 'product_option_id'],
                            'children'     => [
                                'product_option_value_descriptions' => [
                                    'id'           => null,
                                    'relation_ids' => ['product_id', 'language_id', 'product_option_value_id'],
                                ],
                            ],
                        ],
                    ],
                ],
                'product_specials'       => [
                    'id'           => 'product_special_id',
                    'relation_ids' => ['product_id'],
                ],
                /*
                * All 3 columns of the table are primary keys
                * So we can't update it. Only insert or delete
                */
                'product_tags'           => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'tag', 'language_id'],
                ],
                /* Special case, no matching field name */
                'resource_map'           => [
                    'id'               => 'resource_id',
                    'relation_ids'     => ['resource_id', 'object_id'],
                    'special_relation' => ['object_name' => 'products', 'object_id' => 'product_id'],
                    'children'         => [
                        'resource_library' => $this->sections['resource_library'],
                    ],
                ],
                'reviews'                => [
                    'id'           => null,
                    'relation_ids' => ['product_id', 'review_id', 'customer_id'],
                ],
            ],
        ];
        $this->sections['orders'] = [
            'id'       => 'order_id',
            'children' => [
                'order_downloads' => [
                    'id'           => null,
                    'relation_ids' => ['order_id', 'order_download_id'],
                ],
                'order_history'   => [
                    'id'           => null,
                    'relation_ids' => ['order_id', 'order_history_id'],
                ],
                'order_options'   => [
                    'id'           => null,
                    'relation_ids' => ['order_id', 'order_option_id'],
                ],
                'order_products'  => [
                    'id'           => null,
                    'relation_ids' => ['order_id', 'order_product_id'],
                ],
                'order_totals'    => [
                    'id'           => null,
                    'relation_ids' => ['order_id', 'order_total_id'],
                ],
            ],
        ];
        $this->sections['manufacturers'] = [
            'id'       => 'manufacturer_id',
            'children' => [
                'manufacturers_to_stores' => [
                    'id'           => null,
                    'relation_ids' => ['manufacturer_id', 'store_id'],
                ],
                /* Special case, no matching field name */
                'resource_map'            => [
                    'id'               => 'resource_id',
                    'relation_ids'     => ['resource_id', 'object_id'],
                    'special_relation' => ['object_name' => 'manufacturers', 'object_id' => 'manufacturer_id'],
                    'children'         => [
                        'resource_library' => $this->sections['resource_library'],
                    ],
                ],
            ],
        ];
        $this->sections['customers'] = [
            'id'       => 'customer_id',
            'children' => [
                'addresses' => [
                    'id'           => null,
                    'relation_ids' => ['customer_id', 'address_id'],
                ],
                'reviews'   => [
                    'id'           => null,
                    'relation_ids' => ['customer_id', 'review_id', 'product_id'],
                ],
            ],
        ];

        $this->sections['blocks'] = [
            'id'       => 'block_id',
            'children' => [
                'block_layouts'   => [
                    'id'           => 'instance_id',
                    'relation_ids' => ['block_id', 'instance_id', 'layout_id', 'parent_instance_id', 'custom_block_id'],
                    'children'     => [
                        'layouts' => [
                            'id'           => null,
                            'switch_to_id' => 'layout_id',
                            'relation_ids' => ['layout_id'],
                        ],
                    ],
                ],
                'block_templates' => [
                    'id'           => null,
                    'relation_ids' => ['block_id', 'parent_block_id'],
                ],
                'custom_blocks'   => [
                    'id'           => 'custom_block_id',
                    'relation_ids' => ['block_id'],
                    'children'     => [
                        'block_descriptions' => [
                            'id'           => null,
                            'relation_ids' => ['custom_block_id', 'block_description_id'],
                        ],
                    ],
                ],
            ],
        ];

        $this->sections['downloads'] = [
            'id'       => 'download_id',
            'children' => [
                'download_descriptions' => [
                    'id'           => null,
                    'relation_ids' => ['download_id', 'language_id'],
                ],
            ],
        ];

        // Only "add as it is". Update is left for future enhancements.
        $this->sections['url_aliases'] = [
            'id' => 'url_alias_id',
        ];
    }

    /**
     * check if requested table exist and return its array
     *
     * @param string $table_name
     * @param array $search_in_input
     *
     * @return null|array
     */
    public function find_table_cfg($table_name, $search_in_input = [])
    {
        if (empty($table_name)) {
            return null;
        }

        if (count($search_in_input) <= 0) {
            $search_section = $this->sections;
        } else {
            $search_section = $search_in_input['children'];
        }

        foreach ($search_section as $sectionName => $section) {
            if ($table_name == $sectionName) {
                return $section;
            } else {
                if (is_array($section['children']) && count($section['children']) > 0) {
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
     *
     * @return bool
     */
    public function setSections($sections = [])
    {
        if (!empty($sections)) {
            $this->sections = $sections;
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    private function _build_table_relationship()
    {
        //NOTE: Not used see below in _build_relationship
        $cache_key = 'tables.key.relationship';

        if ($this->cache) {
            $this->tables_data = $this->cache->pull($cache_key);
        }
        if (!$this->tables_data) {
            $sql = "SELECT table_name, column_name, extra 
                    FROM information_schema.columns 
                    WHERE table_schema='".DB_DATABASE."' group by table_name";
            $load_sql = $this->db->query($sql);
            $tables = $load_sql->rows;

            foreach ($tables as $table) {
                if (!trim($table['table_name'])) {
                    continue;
                }
                //get primary keys
                $pkeys = [];
                $sql = "SHOW INDEX FROM ".$table['table_name']."
                        WHERE Key_name = 'PRIMARY'";
                $primary_query = $this->db->query($sql);
                foreach ($primary_query->rows as $value) {
                    $pkeys[] = $value['Column_name'];
                }

                $table_name = $table['table_name'];

                $this->tables_data[$table_name] = [];
                if ($table['extra'] == 'auto_increment') {
                    $this->tables_data[$table_name]['id'] = $table['column_name'];
                } else {
                    $this->tables_data[$table_name]['id'] = null;
                }
                $this->tables_data[$table_name]['relation_ids'] = $pkeys;
            }
            $this->_build_relationship();
            $this->_apply_special_cases();

            if ($this->cache) {
                $this->cache->push($cache_key, $this->tables_data);
            }
        }
    }

    private function _build_relationship()
    {
        //Need to connect tables with relationship IDs.
        //NOT IMPLEMENTED. Issue with logic to detect id and content. Slow and unreliable solution.
        // Looking for better solution to automate or us TRUE relational DB with Postgres ot InnoDB with mysql
    }

    private function _apply_special_cases()
    {
        //Need to connect tables with relationship IDs.
        //NOT IMPLEMENTED. Issue with logic to detect id and content. Slow and unreliable solution.
        // Looking for better solution to automate or us TRUE relational DB with Postgres ot InnoDB with mysql
    }

    /**
     * @param string $table_name
     *
     * @return null|array
     */
    public function get_table_cfg($table_name)
    {
        //NOTE: DO not use. Not implemented yet. 
        if (empty($table_name)) {
            return null;
        }

        if (!$this->tables_data) {
            $this->_build_table_relationship();
        }

        if ($this->tables_data[DB_PREFIX.$table_name]) {
            return $this->tables_data[DB_PREFIX.$table_name];
        } else {
            return null;
        }
    }

    /**
     * @param string $table_name
     *
     * @return array
     * @throws AException
     */
    public function get_table_columns($table_name)
    {
        $sql = 'SHOW COLUMNS FROM `'.$this->db->escape(DB_PREFIX.$table_name).'` FROM `'.DB_DATABASE.'`';

        $results = $this->db->query($sql);
        return $results->rows;
    }

}