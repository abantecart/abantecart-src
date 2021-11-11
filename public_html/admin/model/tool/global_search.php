<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ModelToolGlobalSearch extends Model
{
    /**
     * registry to provide access to cart objects
     *
     * @var object Registry
     */
    public $registry;
    public $data = [];
    /**
     * commands available in the system
     *
     * @var array
     */
    public $commands;

    /**
     * array with descriptions of controller for search
     *
     * @var array
     */
    public $results_controllers = [
        "commands"           => [],
        "orders"             => [
            'alias'    => 'order',
            'id'       => 'order_id',
            'page'     => 'sale/order/details',
            'response' => '',
        ],
        "customers"          => [
            'alias'    => 'customer',
            'id'       => 'customer_id',
            'page'     => 'sale/customer/update',
            'response' => '',
        ],
        "product_categories" => [
            'alias'    => 'category',
            'id'       => 'category_id',
            'page'     => 'catalog/category/update',
            'response' => '',
        ],
        "products"           => [
            'alias'    => 'product',
            'id'       => 'product_id',
            'page'     => 'catalog/product/update',
            'response' => '',
        ],
        "reviews"            => [
            'alias'    => 'review',
            'id'       => 'review_id',
            'page'     => 'catalog/review/update',
            'response' => '',
        ],
        "manufacturers"      => [
            'alias'    => 'brand',
            'id'       => 'manufacturer_id',
            'page'     => 'catalog/manufacturer/update',
            'response' => '',
        ],
        "languages"          => [
            'alias'        => 'language',
            'id'           => 'language_definition_id',
            'extra_fields' => ['language_id'],
            'page'         => 'localisation/language_definition_form/update',
            'response'     => 'localisation/language_definition_form/update',
        ],
        "pages"              => [
            'alias'    => 'information',
            'id'       => ['page_id', 'layout_id', 'tmpl_id'],
            'page'     => 'design/layout',
            'response' => '',
        ],
        "settings"           => [
            'alias'    => 'setting',
            'id'       => ['setting_id', 'active', 'store_id'],
            'page'     => 'setting/setting',
            'response' => 'setting/setting_quick_form',
        ],
        "messages"           => [
            'alias'    => 'information',
            'id'       => 'msg_id',
            'page'     => 'tool/message_manager',
            'response' => '',
        ],
        "extensions"         => [
            'alias'    => 'extension',
            'id'       => 'extension',
            'page'     => 'extension/extensions/edit',
            'page2'    => 'total/%s',
            'response' => '',
        ],
        "downloads"          => [
            'alias'    => 'download',
            'id'       => 'download_id',
            'page'     => '',
            'response' => 'product/product/buildDownloadForm',
        ],
        "contents"           => [
            'alias'    => 'content',
            'id'       => 'content_id',
            'page'     => 'design/content/update',
            'response' => '',
        ],
    ];

    public function __construct($registry)
    {
        parent::__construct($registry);

        $text_data = $this->language->getASet('common/action_commands');
        $keys = preg_grep("/^command.*/", array_keys($text_data));
        foreach ($keys as $key) {
            $this->commands[$key] = $text_data[$key];
        }
    }

    /**
     * function returns list of accessible search categories
     *
     * @param string $keyword
     *
     * @return array
     */
    public function getSearchSources($keyword = '')
    {
        $search_categories = [];
        // limit of keyword length
        if (mb_strlen($keyword) >= 1) {
            foreach ($this->results_controllers as $k => $item) {
                $search_categories[$k] = $item['alias'];
            }
        }
        return $search_categories;
    }

    /**
     * function returns total counts of search results
     *
     * @param string $search_category
     * @param string $keyword
     *
     * @return int
     * @throws AException
     */
    public function getTotal($search_category, $keyword)
    {
        // two variants of needles for search: with and without html-entities
        $needle = $this->db->escape(mb_strtolower(htmlentities($keyword, ENT_QUOTES)), true);
        $needle2 = $this->db->escape(mb_strtolower($keyword), true);

        $language_id = (int) $this->config->get('storefront_language_id');

        $all_languages = $this->language->getActiveLanguages();
        $current_store_id = $this->language->getContentLanguageID();
        $search_languages = [];
        foreach ($all_languages as $l) {
            $search_languages[] = (int) $l['language_id'];
        }

        $output = [];

        switch ($search_category) {
            case 'commands' :
                $output = $this->_possibleCommands($needle, 'total');
                break;
            case 'product_categories' :
                $sql = "SELECT count(*) as total
                        FROM ".$this->db->table("category_descriptions")." c 
                        WHERE (LOWER(c.name) LIKE '%".$needle."%'
                                OR LOWER(c.name) LIKE '%".$needle2."%' )
                        AND c.language_id IN (".(implode(",", $search_languages)).");";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;

            case 'languages' :
                $sql = "SELECT count(*) as total
                        FROM ".$this->db->table("language_definitions")." l
                        WHERE (LOWER(l.language_value) LIKE '%".$needle."%'
                                OR LOWER(l.language_value) LIKE '%".$needle2."%'
                                OR LOWER(l.language_key) LIKE '%".$needle."%'
                                OR LOWER(l.language_key) LIKE '%".str_replace(' ', '_', $needle)."%' )
                            AND l.language_id IN (".implode(",", $search_languages).")";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;

            case 'products' :
                $sql = "SELECT a.product_id
                        FROM ".$this->db->table("products")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b 
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE LOWER(a.model) LIKE '%".$needle."%' OR LOWER(a.model) LIKE '%".$needle2."%'
                        UNION
                        SELECT product_id
                        FROM ".$this->db->table("product_descriptions")." pd1
                        WHERE ( LOWER(pd1.name) LIKE '%".$needle."%' 
                                OR LOWER(pd1.name) LIKE '%".$needle2."%' )
                            AND pd1.language_id	IN (".(implode(",", $search_languages)).")
                        UNION
                        SELECT DISTINCT a.product_id
                        FROM ".$this->db->table("product_option_value_descriptions")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b 
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(a.name) LIKE '%".$needle."%' 
                                    OR LOWER(a.name) LIKE '%".$needle2."%' )
                                AND a.language_id IN (".(implode(",", $search_languages)).")
                        UNION
                        SELECT DISTINCT ov.product_id
                        FROM ".$this->db->table("product_option_values")." ov
                        WHERE ( LOWER(ov.sku) LIKE '%".$needle."%' 
                                OR LOWER(ov.sku) LIKE '%".$needle2."%' )
                        UNION
                        SELECT DISTINCT a.product_id
                        FROM ".$this->db->table("product_tags")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b 
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(a.tag) LIKE '%".$needle."%' 
                                OR LOWER(a.tag) LIKE '%".$needle2."%' )
                            AND a.language_id = ".$language_id;

                $result = $this->db->query($sql);
                if ($result->num_rows) {
                    foreach ($result->rows as $row) {
                        $output [$row ['product_id']] = 0;
                    }
                }
                $output = sizeof($output);
                break;

            case 'reviews' :
                $sql = "SELECT DISTINCT product_id
                        FROM ".$this->db->table("reviews")." r
                        WHERE (LOWER(`text`) LIKE '%".$needle."%')
                                OR (LOWER(r.`author`) LIKE '%".$needle."%') ";

                $result = $this->db->query($sql);
                if ($result->num_rows) {
                    foreach ($result->rows as $row) {
                        $output [$row ['product_id']] = 0;
                    }
                }
                $output = sizeof($output);
                break;

            case "manufacturers" :
                $sql = "SELECT count(*) as total
                        FROM ".$this->db->table("manufacturers")." 
                        WHERE (LOWER(name) LIKE '%".$needle."%')";

                $result = $this->db->query($sql);
                $output = $result->row ['total'];

                break;
            case "orders" :
                $sql = "SELECT COUNT(DISTINCT order_id) as total
                        FROM ".$this->db->table("orders")." 
                        WHERE ((LOWER(invoice_prefix) LIKE '%".$needle."%')
                            OR (LOWER(firstname) LIKE '%".$needle."%')
                            OR (LOWER(lastname) LIKE '%".$needle."%')
                            OR (LOWER(email) LIKE '".$needle."%')
                            OR (LOWER(shipping_address_1) LIKE '%".$needle."%')
                            OR (LOWER(shipping_address_2) LIKE '%".$needle."%')
                            OR (LOWER(payment_address_1) LIKE '%".$needle."%')
                            OR (LOWER(payment_address_2) LIKE '%".$needle."%')
                            OR order_id= '".(int) $needle."'
                            )
                        AND language_id = ".$language_id;
                $result = $this->db->query($sql);
                $output = $result->row ['total'];

                break;
            case "customers" :
                $sql = "SELECT COUNT(customer_id) as total
                        FROM ".$this->db->table("customers")." 
                        WHERE ((LOWER(firstname) LIKE '%".$needle."%')
                            OR (LOWER(lastname) LIKE '%".$needle."%')
                            OR (LOWER(email) LIKE '%".$needle."%')
                            )";

                $result = $this->db->query($sql);
                $output = $result->row ['total'];

                break;
            case "pages" :
                $sql = "SELECT COUNT(DISTINCT p.page_id) as total
                        FROM ".$this->db->table("pages")." p 
                        LEFT JOIN ".$this->db->table("page_descriptions")." b 
                            ON (p.page_id = b.page_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE
                            ((LOWER(b.name) LIKE '%".$needle."%')
                            OR (LOWER(b.title) LIKE '%".$needle."%')
                            OR (LOWER(b.keywords) LIKE '%".$needle."%'))";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;

            case "settings" :
                $sql = "SELECT count(*) as total
                        FROM ".$this->db->table("settings")." s
                        LEFT JOIN ".$this->db->table("extensions")." e ON s.`group` = e.`key`
                        LEFT JOIN ".$this->db->table("language_definitions")." l
                                        ON l.language_key LIKE CONCAT(s.`key`,'%')
                        WHERE (LOWER(`value`) LIKE '%".$needle."%')
                                OR
                                (LOWER(s.`key`) LIKE '%".$needle."%')
                            AND s.`store_id` ='".( int ) $current_store_id."'
                        UNION
                        SELECT COUNT(s.setting_id) as total
                        FROM ".$this->db->table("language_definitions")." l
                        LEFT JOIN ".$this->db->table("settings")." s 
                            ON l.language_key = CONCAT('entry_',REPLACE(s.`key`,'config_',''))
                        WHERE (LOWER(l.language_value) LIKE '%".$needle."%'
                                OR LOWER(l.language_value) LIKE '%".$needle."%'
                                OR LOWER(l.language_key) LIKE '%".$needle."%' )
                            AND block='setting_setting'
                            AND l.language_id ='".$language_id."'
                            AND s.`store_id` ='".( int ) $current_store_id."'
                            AND setting_id > 0 ";
                $result = $this->db->query($sql);
                $output = 0;
                foreach ($result->rows as $row) {
                    $output += (int) $row['total'];
                }
                break;
            case "messages" :
                $sql = "SELECT COUNT(DISTINCT msg_id) as total
                        FROM ".$this->db->table("messages")." 
                        WHERE (LOWER(`title`) LIKE '%".$needle."%' 
                                OR LOWER(`message`) LIKE '%".$needle."%')";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;
            case "extensions" :
                $sql = "SELECT COUNT( DISTINCT `key`) as total
                        FROM ".$this->db->table("extensions")." 
                        WHERE LOWER(`key`) LIKE '%".$needle."%' AND `type` <> 'total'";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;
            case "downloads" :
                $sql = "SELECT COUNT( DISTINCT d.download_id) as total
                        FROM ".$this->db->table("downloads")." d
                        RIGHT JOIN ".$this->db->table("download_descriptions")." dd
                            ON (d.download_id = dd.download_id 
                                AND dd.language_id IN (".(implode(",", $search_languages))."))
                        WHERE (LOWER(`name`) LIKE '%".$needle."%')";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;
            case "contents" :
                $sql = "SELECT COUNT( DISTINCT c.content_id) as total
                        FROM ".$this->db->table("contents")." c
                        RIGHT JOIN ".$this->db->table("content_descriptions")." cd
                            ON (c.content_id = cd.content_id 
                                AND cd.language_id IN (".(implode(",", $search_languages))."))
                        WHERE 
                            (LOWER(`name`) LIKE '%".$needle."%')
                            OR (LOWER(`title`) LIKE '%".$needle."%')
                            OR (LOWER(`description`) LIKE '%".$needle."%')
                            OR (LOWER(`content`) LIKE '%".$needle."%')
                        ";
                $result = $this->db->query($sql);
                $output = $result->row ['total'];
                break;
            default :
                $this->data['result'] = [];
                $this->extensions->hk_ProcessData(
                    $this, 'getTotal',
                    ['search_category' => $search_category, 'term' => $needle]
                );
                if (!$this->data['result']) {
                    $output = 0;
                } else {
                    $output = (int) $this->data['result'];
                }
                break;
        }

        return $output;
    }

    /**
     * function returns search results in JSON format
     *
     * @param string $search_category
     * @param string $keyword
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    public function getResult($search_category, $keyword, $mode = 'listing')
    {
        $language_id = (int) $this->config->get('storefront_language_id');

        // two variants of needles for search: with and without html-entities
        $needle = $this->db->escape(mb_strtolower(htmlentities($keyword, ENT_QUOTES)));
        $needle2 = $this->db->escape(mb_strtolower($keyword));

        $page = (int) $this->request->get_or_post('page');
        $rows = (int) $this->request->get_or_post('rows');

        if ($page) {
            $offset = ($page - 1) * $rows;
            $rows_count = $rows;
        } else {
            $offset = 0;
            $rows_count = $mode == 'listing' ? 10 : 3;
        }

        $all_languages = $this->language->getActiveLanguages();
        $current_store_id = (int) $this->session->data['current_store_id'];
        $search_languages = [];
        foreach ($all_languages as $l) {
            $search_languages[] = (int) $l['language_id'];
        }

        switch ($search_category) {
            case 'commands' :
                $result = array_slice($this->_possibleCommands($needle), $offset, $rows_count);
                break;

            case 'product_categories' :
                $sql = "SELECT DISTINCT
                            c.category_id,
                            c.name as title,
                            c.name as text,
                            c.meta_keywords as text2,
                            c.meta_description as text3,
                            c.description as text4
                        FROM ".$this->db->table("category_descriptions")." c 
                        WHERE (LOWER(c.name) LIKE '%".$needle."%'
                                OR LOWER(c.name) LIKE '%".$needle2."%' )
                            AND c.language_id IN (".(implode(",", $search_languages)).")
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case 'languages' :
                $sql = "SELECT l.language_definition_id, 
                                l.language_key as title, 
                                CONCAT_WS('  ',l.language_key,l.language_value) as text, 
                                language_id
                        FROM ".$this->db->table("language_definitions")." l
                        WHERE ( LOWER(l.language_value) LIKE '%".$needle."%'
                                OR LOWER(l.language_value) LIKE '%".$needle2."%'
                                OR l.language_key LIKE '%".str_replace(' ', '_', $needle)."%'
                        )
                            AND l.language_id IN (".(implode(",", $search_languages)).")
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case 'products' :

                $sql = "SELECT DISTINCT a.product_id, b.name as title, a.model as text
                        FROM ".$this->db->table("products")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b 
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE LOWER(a.model) LIKE '%".$needle."%' 
                            OR LOWER(a.sku) LIKE '%".$needle."%' ";
                if ($needle != $needle2) {
                    $sql .= " OR LOWER(a.model) LIKE '%".$needle2."%' OR LOWER(a.sku) LIKE '%".$needle2."%' ";
                }
                $sql .= "
                        UNION
                        SELECT DISTINCT pd1.product_id, pd1.name as title, pd1.name as text
                        FROM ".$this->db->table("product_descriptions")." pd1
                        WHERE ( LOWER(pd1.name) LIKE '%".$needle."%'
                        ";
                if ($needle != $needle2) {
                    $sql .= " OR LOWER(pd1.name) LIKE '%".$needle2."%' ";
                }
                $sql .= " )
                            AND pd1.language_id IN (".(implode(",", $search_languages)).") ";
                //seek by option value sku
                $sql .= " UNION
                        SELECT DISTINCT a.product_id, CONCAT(pd1.name,' -> ',b.name) as title, pd1.name as text
                        FROM ".$this->db->table("product_option_values")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." pd1
                            ON (pd1.product_id = a.product_id 
                                AND pd1.language_id IN (".(implode(",", $search_languages))."))
                        LEFT JOIN ".$this->db->table("product_option_value_descriptions")." b 
                            ON (b.product_option_value_id = a.product_option_value_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(a.sku) LIKE '%".$needle."%' ";
                if ($needle != $needle2) {
                    $sql .= " OR LOWER(a.sku) LIKE '%".$needle2."%' ";
                }
                $sql .= ") ";

                //seek by option name
                $sql .= " UNION
                        SELECT DISTINCT a.product_id, b.name as title, a.name as text
                        FROM ".$this->db->table("product_option_descriptions")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b 
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(a.name) LIKE '%".$needle."%' ";
                if ($needle != $needle2) {
                    $sql .= " OR LOWER(a.name) LIKE '%".$needle2."%' ";
                }
                $sql .= ")
                            AND a.language_id IN (".(implode(",", $search_languages)).")
                        UNION
                        SELECT DISTINCT a.product_id, b.name as title, a.name as text
                        FROM ".$this->db->table("product_option_value_descriptions")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(a.name) LIKE '%".$needle."%' ";
                if ($needle != $needle2) {
                    $sql .= " OR LOWER(a.name) LIKE '%".$needle2."%' ";
                }
                $sql .= " )
                            AND a.language_id IN (".(implode(",", $search_languages)).")
                        UNION
                        SELECT a.product_id, b.name as title, a.tag as text
                        FROM ".$this->db->table("product_tags")." a
                        LEFT JOIN ".$this->db->table("product_descriptions")." b 
                            ON (b.product_id = a.product_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( a.tag LIKE '".$needle."%' ";
                if ($needle != $needle2) {
                    $sql .= " OR a.tag LIKE '".$needle2."%' ";
                }
                $sql .= " )
                            AND a.language_id IN (".(implode(",", $search_languages)).")
                        LIMIT ".$offset.",".$rows_count;

                $result = $this->db->query($sql);
                $table = [];
                if ($result->num_rows) {
                    foreach ($result->rows as $row) {
                        if (!isset($table [$row ['product_id']])) {
                            $table [$row ['product_id']] = $row;
                        }
                    }
                }
                $result = $table;
                break;

            case "reviews" :
                $sql = "SELECT DISTINCT review_id, r.`text`, pd.`name` as title
                        FROM ".$this->db->table("reviews")." r
                        LEFT JOIN ".$this->db->table("product_descriptions")." pd
                            ON (pd.product_id = r.product_id 
                                AND pd.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(r.`text`) LIKE '%".$needle."%'
                                OR LOWER(r.`author`) LIKE '%".$needle."%' ";
                if ($needle != $needle2) {
                    $sql .= " OR LOWER(r.`text`) LIKE '%".$needle2."%'
                              OR LOWER(r.`author`) LIKE '%".$needle2."%' ";
                }
                $sql .= ") LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;
            case "manufacturers" :
                $sql = "SELECT manufacturer_id, `name` as text, `name` as title
                        FROM ".$this->db->table("manufacturers")." 
                        WHERE (LOWER(name) LIKE '%".$needle."%' 
                                OR LOWER(name) LIKE '%".$needle2."%' )
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;
            case "orders" :
                $sql = "SELECT order_id, 
                                CONCAT('order #', order_id) as title,
                                CONCAT(invoice_prefix,' ',firstname,' ',lastname,' ',email)  as text
                        FROM ".$this->db->table("orders")." 
                        WHERE ((LOWER(invoice_prefix) LIKE '%".$needle."%')
                                    OR (LOWER(firstname) LIKE '%".$needle."%')
                                    OR (LOWER(lastname) LIKE '%".$needle."%')
                                    OR (LOWER(email) LIKE '%".$needle."%')
                                    OR (LOWER(shipping_address_1) LIKE '%".$needle."%')
                                    OR (LOWER(shipping_address_2) LIKE '%".$needle."%')
                                    OR (LOWER(payment_address_1) LIKE '%".$needle."%')
                                    OR (LOWER(payment_address_2) LIKE '%".$needle."%')
                                    OR order_id= '".(int) $needle."')
                                AND language_id = ".$language_id."
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case "customers" :
                $customer_needle = '+"'.implode('" +"', explode(' ', $needle)).'"';
                $sql = "SELECT customer_id, 
                                CONCAT('".($mode == 'listing' ? "customer: " : "")."', firstname,' ',lastname) as title,
                                CONCAT(firstname,' ',lastname,' ',email)  as text
                        FROM ".$this->db->table("customers")." 
                        WHERE (
                                MATCH(firstname, lastname) AGAINST ('".$customer_needle."' IN BOOLEAN MODE)
                                OR  (LOWER(email) LIKE '%".$needle."%')
                                )
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;
            case "pages" :
                $sql = "SELECT DISTINCT p.page_id,
                                b.name as title,
                                CONCAT(b.name, ' ',b.title, ' ',b.keywords) as text,
                                pl.layout_id, l.template_id as tmpl_id
                        FROM ".$this->db->table("pages")." p 
                        LEFT JOIN ".$this->db->table("page_descriptions")." b 
                            ON (p.page_id = b.page_id 
                                AND b.language_id IN (".(implode(",", $search_languages))."))
                        LEFT JOIN ".$this->db->table("pages_layouts")." pl
                            ON (pl.page_id = p.page_id
                                AND pl.layout_id IN 
                                    (SELECT layout_id
                                     FROM ".$this->db->table("layouts")." 
                                     WHERE template_id = '".$this->config->get('config_storefront_template')."'
                                            AND layout_type='1'))
                        LEFT JOIN ".$this->db->table("layouts")." l 
                            ON  l.layout_id = pl.layout_id
                        WHERE (LOWER(b.name) LIKE '%".$needle."%')
                                OR (LOWER(b.title) LIKE '%".$needle."%')
                                OR (LOWER(b.keywords) LIKE '%".$needle."%')
                        LIMIT ".$offset.",".$rows_count;

                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case "settings" :

                $sql = "SELECT DISTINCT setting_id,
                                CONCAT(`group`,'-',s.`key`,'-',s.store_id) as active,
                                s.store_id,
                                COALESCE(l.language_value,s.`key`) as title,
                                COALESCE(l.language_value,s.`key`) as text,
                                e.`key` as extension, e.`type` as type
                        FROM ".$this->db->table("settings")." s
                        LEFT JOIN ".$this->db->table("extensions")." e 
                            ON s.`group` = e.`key`
                        LEFT JOIN ".$this->db->table("language_definitions")." l
                            ON l.language_key LIKE CONCAT(s.`key`,'%')
                        WHERE (LOWER(s.`value`) LIKE '%".$needle."%'
                                OR s.`key` LIKE '%".str_replace(' ', '_', $needle)."%' ";

                if ($needle != $needle2) {
                    $sql .= " OR LOWER(s.`value`) LIKE '%".$needle2."%'
                                OR s.`key` LIKE '%".str_replace(' ', '_', $needle)."%' ";
                }

                $sql .= " ) AND s.`key` NOT IN ('encryption_key', 'config_ssl')
                            AND s.`store_id` ='".( int ) $current_store_id."'
                        UNION
                        SELECT s.setting_id,
                                CONCAT(s.`group`,'-',s.`key`,'-',s.store_id) as active,
                                s.store_id,
                                CONCAT(`group`,' -> ',COALESCE( l.language_value,s.`key` )) as title,
                        CONCAT_WS(' >> ',l.language_value) as text, '', 'core'
                        FROM ".$this->db->table("language_definitions")." l
                        LEFT JOIN ".$this->db->table("settings")." s
                            ON l.language_key = CONCAT('entry_',REPLACE(s.`key`,'config_',''))
                        WHERE ( LOWER(l.language_value) LIKE '%".$needle."%'
                                OR l.language_key LIKE '%".str_replace(' ', '_', $needle)."%' ";

                if ($needle != $needle2) {
                    $sql .= "   OR LOWER(l.language_value) LIKE '%".$needle2."%'
                                OR l.language_key LIKE '%".str_replace(' ', '_', $needle2)."%' ";
                }

                $sql .= " )
                            AND block='setting_setting' AND l.language_id ='".$language_id."'
                            AND s.`store_id` ='".( int ) $current_store_id."'
                            AND setting_id > 0
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $rows = $result->rows;
                $result = [];
                foreach ($rows as $row) {
                    if (!isset($result[$row['setting_id']])) {
                        //remove all text between span tags
                        $regex = '/<span(.*)span>/';
                        $row['title'] = str_replace(
                            ["	", "  ", "\n"],
                            "",
                            strip_tags(preg_replace($regex, '', $row['title']))
                        );
                        $row['text'] = !$row['text'] ? $row['title'] : $row['text'];
                        $row['text'] = str_replace(
                            ["	", "  ", "\n"],
                            "",
                            strip_tags(preg_replace($regex, '', $row['text']))
                        );
                        $result[$row['setting_id']] = $row;
                    }
                }
                $result = array_values($result);
                break;
            case "messages" :
                $sql = "SELECT DISTINCT msg_id, title as title, `message` as text
                        FROM ".$this->db->table("messages")." 
                        WHERE ( LOWER(`title`) LIKE '%".$needle."%'
                                OR LOWER(`message`) LIKE '%".$needle."%'
                                OR LOWER(`title`) LIKE '%".$needle2."%'
                                OR LOWER(`message`) LIKE '%".$needle2."%' )
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case "extensions" :
                $sql = "SELECT DISTINCT `key` as extension, `key` as title, `key` as text
                        FROM ".$this->db->table("extensions")." e
                        WHERE ( LOWER(`key`) LIKE '%".$needle."%'
                                OR LOWER(`key`) LIKE '%".str_replace(' ', '_', $needle)."%' )
                                    AND `type` <> 'total'
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case "downloads" :
                $sql = "SELECT DISTINCT d.download_id, name as title, name  as text
                        FROM ".$this->db->table("downloads")." d
                        LEFT JOIN ".$this->db->table("download_descriptions")." dd
                            ON (d.download_id = dd.download_id 
                                AND dd.language_id IN (".(implode(",", $search_languages))."))
                        WHERE ( LOWER(dd.name) LIKE '%".$needle."%' )
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            case "contents" :
                $sql = "SELECT DISTINCT c.content_id, name as title, name  as text
                        FROM ".$this->db->table("contents")." c
                        RIGHT JOIN ".$this->db->table("content_descriptions")." cd
                            ON (c.content_id = cd.content_id 
                                AND cd.language_id IN (".(implode(",", $search_languages))."))
                        WHERE (LOWER(`name`) LIKE '%".$needle."%')
                                OR (LOWER(`title`) LIKE '%".$needle."%')
                                OR (LOWER(`description`) LIKE '%".$needle."%')
                                OR (LOWER(`content`) LIKE '%".$needle."%')
                        LIMIT ".$offset.",".$rows_count;
                $result = $this->db->query($sql);
                $result = $result->rows;
                break;

            default :
                $this->data['result'] = [];
                $this->extensions->hk_ProcessData(
                    $this,
                    'getResult',
                    [
                        'search_category' => $search_category,
                        'term' => $needle,
                        'limit' => 3
                    ]
                );
                if (!$this->data['result']) {
                    $result = [];
                } else {
                    $result = $this->data['result'];
                }
                break;
        }

        if ($mode == 'listing') {
            if ($search_category == 'commands') {
                $result = $this->_prepareCommandsResponse($result);
            } else {
                $result = $this->_prepareResponse(
                    $keyword,
                    $this->results_controllers[$search_category]['page'],
                    $this->results_controllers[$search_category]['id'],
                    $result
                );
            }
        }
        foreach ($result as &$row) {
            $row['controller'] = $this->results_controllers[$search_category]['page'];

            //shorten text for suggestion
            if ($mode != 'listing') {
                $dec_text = htmlentities($row['text'], ENT_QUOTES);
                $len = mb_strlen($dec_text);
                if ($len > 100) {
                    $ellipsis = '...';
                    $row['text'] = mb_substr($dec_text, 0, 100).$ellipsis;
                }
            }
        }
        $output ["result"] = $result;
        $output ['search_category'] = $search_category;

        return $output;
    }

    /**
     * function prepares array with search results for json encoding
     *
     * @param string $keyword
     * @param string $rt
     * @param string|array $key_field (s)
     * @param array $table
     *
     * @return array
     * @throws AException
     */
    private function _prepareResponse($keyword = '', $rt = '', $key_field = '', $table = [])
    {
        $output = [];
        if (!$rt || !$key_field || !$keyword) {
            return null;
        }

        $tmp = [];
        $text = '';
        if ($table && is_array($table)) {
            foreach ($table as $row) {
                //let's extract  and colorize keyword in row
                foreach ($row as $key => $field) {
                    $field_decoded = htmlentities($field, ENT_QUOTES);

                    // if keyword found
                    $pos = mb_stripos($field_decoded, $keyword);
                    if (is_int($pos) && $key != 'title') {
                        $row ['title'] = '<span class="search_res_title">'.strip_tags($row ['title'])."</span>";
                        $start = $pos < 50 ? 0 : ($pos - 50);
                        $keyword_len = mb_strlen($keyword);
                        $field_len = mb_strlen($field_decoded);
                        $ellipsis = ($field_len - $keyword_len > 10) ? '...' : '';
                        // before founded word
                        $text .= $ellipsis.mb_substr($field_decoded, $start, $pos);
                        // founded word
                        $len = ($field_len - ($pos + $keyword_len)) > 50 ? 50 : $field_len;
                        // after founded word
                        $text .= mb_substr($field_decoded, ($pos + $keyword_len), $len).$ellipsis;

                        $row ['text'] = $text;
                        break;
                    }
                }

                // exception for extension settings
                $temp_key_field = $key_field;
                $url = $rt;

                if ($rt == 'setting/setting' && !empty($row['extension'])) {
                    $temp_key_field = $this->results_controllers['extensions']['id'];
                    if ($row['type'] == 'total') { //for order total extensions
                        $url = sprintf($this->results_controllers['extensions']['page2'], $row['extension']);
                    } else {
                        $url = $this->results_controllers['extensions']['page'];
                    }
                }

                if (is_array($temp_key_field)) {
                    foreach ($temp_key_field as $var) {
                        $url .= "&".$var."=".$row [$var];
                    }
                } else {
                    $url .= "&".$temp_key_field."=".$row [$temp_key_field];
                }
                $tmp ['type'] = $row['type'];
                $tmp ['href'] = $this->html->getSecureURL($url);
                $tmp ['text'] =
                    '<a href="'.$tmp ['href'].'" target="_blank" title="'.$row ['text'].'">'.$row ['title'].'</a>';
                $output [] = $tmp;
            }
        } else {
            $this->load->language('tool/global_search');
            $output [0] = ["text" => $this->language->get('no_results_message')];
        }
        return $output;
    }

    private function _prepareCommandsResponse($table = [])
    {
        $output = [];
        foreach ($table as $row) {
            $tmp = [];
            $tmp ['text'] = '<a href="'.$row['url'].'" target="_blank" title="'.$row['text'].'">'.$row['title'].'</a>';
            $output [] = $tmp;
        }
        return $output;
    }

    /**
     * function to get possible commands for the look up
     *
     * @param string $keyword
     * @param string $mode ('total')
     *
     * @return array|int
     */
    private function _possibleCommands($keyword, $mode = '')
    {
        $commands_obj = new AdminCommands();
        $this->commands = $commands_obj->commands;
        $result = $commands_obj->getCommands($keyword);

        if ($mode == 'total') {
            return ($result && isset($result['found_actions']) ? count($result['found_actions']) : 0);
        }

        $ret = [];
        if (isset($result['found_actions'])) {
            foreach ($result['found_actions'] as $command) {
                $ret[] = [
                    'text'  => $result['command']." ".$command['title']." ".$result['request'],
                    'title' => $result['command']." ".$command['title']." ".$result['request'],
                    'url'   => $command['url'],
                ];
            }
        }
        return $ret;
    }

}
