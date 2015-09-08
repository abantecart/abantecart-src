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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ModelToolGlobalSearch extends Model {
	/**
	 * registry to provide access to cart objects
	 *
	 * @var object Registry
	 */
	public $registry;
	
	/**
	 * commans awaialable in the system
	 *
	 * @var array
	 */
	public $commands;
	
	/**
	 * array with descriptions of controller for search
	 * @var array
	 */
	public $results_controllers = array(
		"commands" => array(),
		"orders" => array(
			'alias' => 'order',
			'id' => 'order_id',
			'page' => 'sale/order/update',
			'response' => ''),
		"customers" => array(
			'alias' => 'customer',
			'id' => 'customer_id',
			'page' => 'sale/customer/update',
			'response' => ''),
		"product_categories" => array(
			'alias' => 'category',
			'id' => 'category_id',
			'page' => 'catalog/category/update',
			'response' => ''),
		"products" => array(
			'alias' => 'product',
			'id' => 'product_id',
			'page' => 'catalog/product/update',
			'response' => ''),
		"reviews" => array(
			'alias' => 'review',
			'id' => 'review_id',
			'page' => 'catalog/review/update',
			'response' => ''),
		"manufacturers" => array(
			'alias' => 'brand',
			'id' => 'manufacturer_id',
			'page' => 'catalog/manufacturer/update',
			'response' => ''),
		"languages" => array(
			'alias' => 'language',
			'id' => 'language_definition_id',
			'extra_fields' => array('language_id'),
			'page' => 'localisation/language_definition_form/update',
			'response' => 'localisation/language_definition_form/update'),
		"pages" => array(
			'alias' => 'information',
			'id' => array('page_id', 'layout_id', 'tmpl_id'),
			'page' => 'design/layout',
			'response' => ''),
		"settings" => array(
			'alias' => 'setting',
			'id' => array('setting_id', 'active', 'store_id'),
			'page' => 'setting/setting',
			'response' => 'setting/setting_quick_form'),
		"messages" => array(
			'alias' => 'information',
			'id' => 'msg_id',
			'page' => 'tool/message_manager',
			'response' => ''),
		"extensions" => array(
			'alias' => 'extension',
			'id' => 'extension',
			'page' => 'extension/extensions/edit',
			'page2' => 'total/%s',
			'response' => ''),
		"downloads" => array(
			'alias' => 'download',
			'id' => 'download_id',
			'page' => 'catalog/download/update',
			'response' => '')
	);


	public function __construct($registry) {
		parent::__construct($registry);

		$text_data = $this->language->getASet('common/action_commands');	
		$keys = preg_grep("/^command.*/", array_keys($text_data));
		foreach($keys as $key) {
			$this->commands[$key] = $text_data[$key];
		}
	}

	/**
	 * function returns list of accessible search categories
	 *
	 * @param string $keyword
	 * @return array
	 */
	public function getSearchSources($keyword = '') {
		$search_categories = array();
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
	 * @return int
	 */
	public function getTotal($search_category, $keyword) {
		$needle = $this->db->escape(mb_strtolower(htmlentities($keyword, ENT_QUOTES)));
		$all_languages = $this->language->getActiveLanguages();
		$current_store_id = !isset($this->session->data['current_store_id']) ? 0 : $this->session->data['current_store_id'];
		$search_languages = array();
		foreach($all_languages as $l){
			$search_languages[] = (int)$l['language_id'];
		}

		switch ($search_category) {
			case 'commands' :
				$output = $this->_possibleCommands($needle, 'total');
				break;
			case 'product_categories' :
				$sql = "SELECT count(*) as total
						FROM " . $this->db->table("category_descriptions") . " c 
						WHERE (LOWER(c.name) like '%" . $needle . "%' OR LOWER(c.meta_keywords) like '%" . $needle . "%' 
								OR LOWER(c.meta_description) like '%" . $needle . "%'	OR LOWER(c.description) like '%" . $needle . "%')
						AND c.language_id IN (" . (implode(",", $search_languages)) . ");";
				$result = $this->db->query($sql);
				$output = $result->row ['total'];
				break;

			case 'languages' :
				$sql = "SELECT count(*) as total
						FROM " . $this->db->table("language_definitions") . " l						
						WHERE (LOWER(l.language_value) like '%" . $needle . "%' OR LOWER(l.language_key) like '%" . $needle . "%')
							AND l.language_id IN (" . implode(",", $search_languages) . ")";
				$result = $this->db->query($sql);
				$output = $result->row ['total'];

				break;

			case 'products' :
				$sql = "SELECT a.product_id
						FROM " . $this->db->table("products") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id IN (" . (implode(",", $search_languages)) . "))
						WHERE LOWER(a.model) like '%" . $needle . "%'
						UNION
						SELECT product_id
						FROM " . $this->db->table("product_descriptions") . " 
						WHERE ( ( LOWER(name) like '%" . $needle . "%' )
								OR ( LOWER(meta_keywords) like '%" . $needle . "%' )
								OR ( LOWER(meta_description) like '%" . $needle . "%' )
								OR ( LOWER(description) like '%" . $needle . "%' ))
							AND language_id	IN (" . (implode(",", $search_languages)) . ")
						UNION					
						SELECT DISTINCT a.product_id
						FROM " . $this->db->table("product_option_value_descriptions") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE LOWER(a.name) like '%" . $needle . "%' AND a.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION 						
						SELECT DISTINCT a.product_id
						FROM " . $this->db->table("product_tags") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE LOWER(a.tag) like '%" . $needle . "%' AND a.language_id = " . ( int )$this->config->get('storefront_language_id');

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
						FROM " . $this->db->table("reviews") . " r
						WHERE (LOWER(`text`) like '%" . $needle . "%')  OR (LOWER(r.`author`) LIKE '%" . $needle . "%') ";

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
						FROM " . $this->db->table("manufacturers") . " 
						WHERE (LOWER(name) like '%" . $needle . "%')";

				$result = $this->db->query($sql);
				$output = $result->row ['total'];

				break;
			case "orders" :
				$sql = "SELECT COUNT(DISTINCT order_id) as total
						FROM " . $this->db->table("orders") . " 
						WHERE ((LOWER(invoice_prefix) like '%" . $needle . "%')
							OR (LOWER(firstname) like '%" . $needle . "%')
							OR (LOWER(lastname) like '%" . $needle . "%')
							OR (LOWER(telephone) like '%" . $needle . "%')
							OR (LOWER(fax) like '%" . $needle . "%')
							OR (LOWER(email) like '%" . $needle . "%')
							OR (LOWER(shipping_firstname) like '%" . $needle . "%')
							OR (LOWER(shipping_lastname) like '%" . $needle . "%')
							OR (LOWER(shipping_firstname) like '%" . $needle . "%')
							OR (LOWER(shipping_company) like '%" . $needle . "%')
							OR (LOWER(shipping_address_1) like '%" . $needle . "%')
							OR (LOWER(shipping_address_2) like '%" . $needle . "%')
							OR (LOWER(shipping_city) like '%" . $needle . "%')
							OR (LOWER(shipping_postcode) like '%" . $needle . "%')
							OR (LOWER(shipping_zone) like '%" . $needle . "%')
							OR (LOWER(shipping_country) like '%" . $needle . "%')
							OR (LOWER(shipping_method) like '%" . $needle . "%')
							OR (LOWER(payment_firstname) like '%" . $needle . "%')
							OR (LOWER(payment_lastname) like '%" . $needle . "%')
							OR (LOWER(payment_firstname) like '%" . $needle . "%')
							OR (LOWER(payment_company) like '%" . $needle . "%')
							OR (LOWER(payment_address_1) like '%" . $needle . "%')
							OR (LOWER(payment_address_2) like '%" . $needle . "%')
							OR (LOWER(payment_city) like '%" . $needle . "%')
							OR (LOWER(payment_postcode) like '%" . $needle . "%')
							OR (LOWER(payment_zone) like '%" . $needle . "%')
							OR (LOWER(payment_country) like '%" . $needle . "%')
							OR (LOWER(payment_method) like '%" . $needle . "%')
							OR (LOWER(comment) like '%" . $needle . "%')
							)
						AND language_id = " . ( int )$this->config->get('storefront_language_id');
				$result = $this->db->query($sql);
				$output = $result->row ['total'];

				break;
			case "customers" :
				$sql = "SELECT COUNT(customer_id) as total
						FROM " . $this->db->table("customers") . " 
						WHERE ((LOWER(firstname) like '%" . $needle . "%')
							OR (LOWER(lastname) like '%" . $needle . "%')
							OR (LOWER(telephone) like '%" . $needle . "%')
							OR (LOWER(fax) like '%" . $needle . "%')
							OR (LOWER(email) like '%" . $needle . "%')
							OR (LOWER(cart) like '%" . $needle . "%')
							)";

				$result = $this->db->query($sql);
				$output = $result->row ['total'];

				break;
			case "pages" :
				$sql = "SELECT COUNT(DISTINCT p.page_id) as total
						FROM " . $this->db->table("pages") . " p 
						LEFT JOIN " . $this->db->table("page_descriptions") . " b 
							ON (p.page_id = b.page_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE ((LOWER(p.key_param) like '%" . $needle . "%')
							OR (LOWER(p.key_value) like '%" . $needle . "%')
							OR (LOWER(b.name) like '%" . $needle . "%')
							OR (LOWER(b.title) like '%" . $needle . "%')
							OR (LOWER(b.keywords) like '%" . $needle . "%')
							OR (LOWER(b.description) like '%" . $needle . "%')
							OR (LOWER(b.content) like '%" . $needle . "%')
							)";
				$result = $this->db->query($sql);
				$output = $result->row ['total'];
				break;

			case "settings" :
				$sql = "SELECT count(*) as total
						FROM " . $this->db->table("settings") . " s
						LEFT JOIN " . $this->db->table("extensions") . " e ON s.`group` = e.`key`
						LEFT JOIN " . $this->db->table("language_definitions") . " l
										ON l.language_key like CONCAT(s.`key`,'%')
						WHERE (LOWER(`value`) like '%" . $needle . "%')
								OR
								(LOWER(s.`key`) like '%" . $needle . "%')
							AND s.`store_id` ='".( int )$current_store_id."'
						UNION
						SELECT COUNT(s.setting_id) as total
						FROM " . $this->db->table("language_definitions") . " l
						LEFT JOIN " . $this->db->table("settings") . " s ON l.language_key = CONCAT('entry_',REPLACE(s.`key`,'config_',''))
						WHERE (LOWER(l.language_value) like '%" . $needle . "%'
								OR LOWER(l.language_value) like '%" . $needle . "%'
								OR LOWER(l.language_key) like '%" . $needle . "%' )
							AND block='setting_setting'
							AND l.language_id ='".( int )$this->config->get('storefront_language_id')."'
							AND s.`store_id` ='".( int )$current_store_id."'
							AND setting_id>0";
				$result = $this->db->query($sql);
				foreach($result->rows as $row){
					$output += (int)$row['total'];
				}
				break;
			case "messages" :
				$sql = "SELECT COUNT(DISTINCT msg_id) as total
						FROM " . $this->db->table("messages") . " 
						WHERE (LOWER(`title`) like '%" . $needle . "%' OR LOWER(`message`) like '%" . $needle . "%')";
				$result = $this->db->query($sql);
				$output = $result->row ['total'];
				break;
			case "extensions" :
				$sql = "SELECT COUNT( DISTINCT `key`) as total
						FROM " . $this->db->table("extensions") . " 
						WHERE LOWER(`key`) like '%" . $needle . "%' AND `type` <> 'total'";
				$result = $this->db->query($sql);
				$output = $result->row ['total'];
				break;
			case "downloads" :
				$sql = "SELECT COUNT( DISTINCT d.download_id) as total
						FROM " . $this->db->table("downloads") . " d
						RIGHT JOIN " . $this->db->table("download_descriptions") . " dd
							ON (d.download_id = dd.download_id AND dd.language_id IN (" . (implode(",", $search_languages)) . "))
						WHERE (LOWER(`name`) like '%" . $needle . "%')";
				$result = $this->db->query($sql);
				$output = $result->row ['total'];
				break;
			default :
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
	 * @return array
	 */
	public function getResult($search_category, $keyword, $mode = 'listing') {

		// two variants of needles for search: with and without html-entities
		$needle = $this->db->escape(mb_strtolower(htmlentities($keyword, ENT_QUOTES)));
		$needle2 = $this->db->escape(mb_strtolower($keyword));

		if (isset($this->request->get ['page'])) {
			$offset = (( int )$this->request->get ['page'] - 1) * ( int )$this->request->get ['rows'];
			$rows_count = ( int )$this->request->get ['rows'];
		} elseif (isset($this->request->post ['page'])) {
			$offset = (( int )$this->request->post ['page'] - 1) * ( int )$this->request->post ['rows'];
			$rows_count = ( int )$this->request->post ['rows'];
		} else {
			$offset = 0;
			$rows_count = $mode == 'listing' ? 10 : 3;
		}

		$all_languages = $this->language->getActiveLanguages();
		$current_store_id = !isset($this->session->data['current_store_id']) ? 0 : $this->session->data['current_store_id'];
		$search_languages = array();
		foreach($all_languages as $l){
			$search_languages[] = (int)$l['language_id'];
		}

		switch ($search_category) {
			case 'commands' :
				$result = array_slice($this->_possibleCommands($needle), $offset, $rows_count);
				break;

			case 'product_categories' :
				$sql = "SELECT c.category_id, c.name as title, c.name as text, c.meta_keywords as text2, c.meta_description as text3, c.description as text4
						FROM " . $this->db->table("category_descriptions") . " c 
						WHERE (LOWER(c.name) like '%" . $needle . "%' OR LOWER(c.meta_keywords) like '%" . $needle . "%' 
								OR LOWER(c.meta_description) like '%" . $needle . "%'	OR LOWER(c.description) like '%" . $needle . "%'
							   OR LOWER(c.name) like '%" . $needle2 . "%' OR LOWER(c.meta_keywords) like '%" . $needle2 . "%'
								OR LOWER(c.meta_description) like '%" . $needle2 . "%'	OR LOWER(c.description) like '%" . $needle2 . "%'
								)
						AND c.language_id IN (" . (implode(",", $search_languages)) . ")
						LIMIT " . $offset . "," . $rows_count;
				$result = $this->db->query($sql);
				$result = $result->rows;
				break;

			case 'languages' :
				$sql = "SELECT l.language_definition_id, l.language_key as title, CONCAT_WS('  ',l.language_key,l.language_value) as text, language_id
						FROM " . $this->db->table("language_definitions") . " l						
						WHERE (LOWER(l.language_value) like '%" . $needle . "%'
									OR LOWER(l.language_value) like '%" . $needle2 . "%'
									OR LOWER(l.language_key) like '%" . $needle . "%' )
							AND l.language_id IN (" . (implode(",", $search_languages)) . ")
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;

			case 'products' :

				$sql = "SELECT a.product_id, b.name as title, a.model as text
						FROM " . $this->db->table("products") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE LOWER(a.model) like '%" . $needle . "%' OR LOWER(a.model) like '%" . $needle2 . "%'
						
						UNION
						
						SELECT pd1.product_id, pd1.name as title, pd1.name as text
						FROM " . $this->db->table("product_descriptions") . " pd1
						WHERE ( LOWER(pd1.name) like '%" . $needle . "%' OR LOWER(pd1.name) like '%" . $needle2 . "%' ) AND pd1.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION
						 						
						SELECT pd2.product_id, pd2.name as title, pd2.meta_keywords as text
						FROM " . $this->db->table("product_descriptions") . " pd2
						WHERE ( LOWER(pd2.meta_keywords) like '%" . $needle . "%' OR LOWER(pd2.meta_keywords) like '%" . $needle2 . "%' ) AND pd2.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION 						
						SELECT pd3.product_id, pd3.name as title, pd3.meta_description as text
						FROM " . $this->db->table("product_descriptions") . " pd3
						WHERE ( LOWER(pd3.meta_description) like '%" . $needle . "%' OR LOWER(pd3.meta_description) like '%" . $needle2 . "%')  AND pd3.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION 						
						SELECT pd4.product_id, pd4.name as title, pd4.description as text
						FROM " . $this->db->table("product_descriptions") . " pd4
						WHERE ( LOWER(pd4.description) like '%" . $needle . "%' OR LOWER(pd4.description) like '%" . $needle2 . "%') AND pd4.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION 						
						SELECT a.product_id, b.name as title, a.name as text
						FROM " . $this->db->table("product_option_descriptions") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE ( LOWER(a.name) like '%" . $needle . "%' OR LOWER(a.name) like '%" . $needle2 . "%' ) AND a.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION 						
						SELECT a.product_id, b.name as title, a.name as text
						FROM " . $this->db->table("product_option_value_descriptions") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE ( LOWER(a.name) like '%" . $needle . "%' OR LOWER(a.name) like '%" . $needle2 . "%' ) AND a.language_id IN (" . (implode(",", $search_languages)) . ")
						UNION 						
						SELECT a.product_id, b.name as title, a.tag as text
						FROM " . $this->db->table("product_tags") . " a
						LEFT JOIN " . $this->db->table("product_descriptions") . " b 
							ON (b.product_id = a.product_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE ( LOWER(a.tag) like '%" . $needle . "%' OR LOWER(a.tag) like '%" . $needle2 . "%' ) AND a.language_id IN (" . (implode(",", $search_languages)) . ")
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$table = array();
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
				$sql = "SELECT review_id, r.`text`, pd.`name` as title
						FROM " . $this->db->table("reviews") . " r
						LEFT JOIN " . $this->db->table("product_descriptions") . " pd
							ON (pd.product_id = r.product_id AND pd.language_id	IN (" . (implode(",", $search_languages)) . "))
						WHERE ( LOWER(r.`text`) LIKE '%" . $needle . "%') OR (LOWER(r.`author`) LIKE '%" . $needle . "%' OR LOWER(r.`text`) LIKE '%" . $needle2 . "%') OR (LOWER(r.`author`) LIKE '%" . $needle2 . "%' )
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;
			case "manufacturers" :
				$sql = "SELECT manufacturer_id, `name` as text, `name` as title
						FROM " . $this->db->table("manufacturers") . " 
						WHERE (LOWER(name) like '%" . $needle . "%' OR LOWER(name) like '%" . $needle2 . "%' )
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;
			case "orders" :
				$sql = "SELECT order_id, CONCAT('order #', order_id) as title,
							CONCAT(invoice_prefix,' ',firstname,' ',lastname,' ',telephone,' ',fax,' ',email,' ',shipping_firstname,' ',shipping_lastname,' ',shipping_firstname,' ',
							shipping_company,' ',shipping_address_1,' ',shipping_address_2,' ',shipping_city,' ',shipping_postcode,' ', shipping_zone,' ',shipping_country,' ',
							shipping_method,' ',payment_firstname,' ',payment_lastname,' ',payment_firstname,' ',payment_company,' ',payment_address_1,' ',payment_address_2,' ',
							payment_city,' ',payment_postcode,' ',payment_zone,' ',payment_country,' ',payment_method,' ',comment)  as text 
						FROM " . $this->db->table("orders") . " 
						WHERE ((LOWER(invoice_prefix) like '%" . $needle . "%')
							OR (LOWER(firstname) like '%" . $needle . "%')
							OR (LOWER(lastname) like '%" . $needle . "%')
							OR (LOWER(telephone) like '%" . $needle . "%')
							OR (LOWER(fax) like '%" . $needle . "%')
							OR (LOWER(email) like '%" . $needle . "%')
							OR (LOWER(shipping_firstname) like '%" . $needle . "%')
							OR (LOWER(shipping_lastname) like '%" . $needle . "%')
							OR (LOWER(shipping_firstname) like '%" . $needle . "%')
							OR (LOWER(shipping_company) like '%" . $needle . "%')
							OR (LOWER(shipping_address_1) like '%" . $needle . "%')
							OR (LOWER(shipping_address_2) like '%" . $needle . "%')
							OR (LOWER(shipping_city) like '%" . $needle . "%')
							OR (LOWER(shipping_postcode) like '%" . $needle . "%')
							OR (LOWER(shipping_zone) like '%" . $needle . "%')
							OR (LOWER(shipping_country) like '%" . $needle . "%')
							OR (LOWER(shipping_method) like '%" . $needle . "%')
							OR (LOWER(payment_firstname) like '%" . $needle . "%')
							OR (LOWER(payment_lastname) like '%" . $needle . "%')
							OR (LOWER(payment_firstname) like '%" . $needle . "%')
							OR (LOWER(payment_company) like '%" . $needle . "%')
							OR (LOWER(payment_address_1) like '%" . $needle . "%')
							OR (LOWER(payment_address_2) like '%" . $needle . "%')
							OR (LOWER(payment_city) like '%" . $needle . "%')
							OR (LOWER(payment_postcode) like '%" . $needle . "%')
							OR (LOWER(payment_zone) like '%" . $needle . "%')
							OR (LOWER(payment_country) like '%" . $needle . "%')
							OR (LOWER(payment_method) like '%" . $needle . "%')
							OR (LOWER(comment) like '%" . $needle . "%')
							)
						AND language_id = " . ( int )$this->config->get('storefront_language_id') . "
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;

			case "customers" :
				$sql = "SELECT customer_id, CONCAT('" . ($mode == 'listing' ? "customer: " : "") . "', firstname,' ',lastname) as title,
							CONCAT(firstname,' ',lastname,' ',telephone,' ',fax,' ',email,' ',cart)  as text 
						FROM " . $this->db->table("customers") . " 
						WHERE ((LOWER(firstname) like '%" . $needle . "%')
							OR (LOWER(lastname) like '%" . $needle . "%')
							OR (LOWER(telephone) like '%" . $needle . "%')
							OR (LOWER(fax) like '%" . $needle . "%')
							OR (LOWER(email) like '%" . $needle . "%')
							OR (LOWER(cart) like '%" . $needle . "%')
							)
						LIMIT " . $offset . "," . $rows_count;
				$result = $this->db->query($sql);
				$result = $result->rows;
				break;
			case "pages" :
				$sql = "SELECT p.page_id,
								b.name as title,
								CONCAT(p.key_param, ' ',p.key_value, ' ', b.name, ' ',b.title, ' ',b.keywords, ' ',b.description, ' ',b.content) as text,
								pl.layout_id, l.template_id as tmpl_id
						FROM " . $this->db->table("pages") . " p 
						LEFT JOIN " . $this->db->table("page_descriptions") . " b 
							ON (p.page_id = b.page_id AND b.language_id	IN (" . (implode(",", $search_languages)) . "))
						LEFT JOIN " . $this->db->table("pages_layouts") . " pl
						 	ON (pl.page_id = p.page_id
						 	    AND pl.layout_id IN (SELECT layout_id
						 	                         FROM " . $this->db->table("layouts") . " 
						 	                         WHERE template_id = '" . $this->config->get('config_storefront_template') . "'
						 	                                AND layout_type='1'))
						LEFT JOIN " . $this->db->table("layouts") . " l ON  l.layout_id = pl.layout_id
						WHERE ((LOWER(p.key_param) like '%" . $needle . "%')
							OR (LOWER(p.key_value) like '%" . $needle . "%')
							OR (LOWER(b.name) like '%" . $needle . "%')
							OR (LOWER(b.title) like '%" . $needle . "%')
							OR (LOWER(b.keywords) like '%" . $needle . "%')
							OR (LOWER(b.description) like '%" . $needle . "%')
							OR (LOWER(b.content) like '%" . $needle . "%')
							)
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;

			case "settings" :
				$sql = "SELECT setting_id,
								CONCAT(`group`,'-',s.`key`,'-',s.store_id) as active,
								s.store_id,
								COALESCE(l.language_value,s.`key`) as title,
								COALESCE(l.language_value,s.`key`) as text,
								e.`key` as extension, e.`type` as type
						FROM " . $this->db->table("settings") . " s
						LEFT JOIN " . $this->db->table("extensions") . " e ON s.`group` = e.`key`
						LEFT JOIN " . $this->db->table("language_definitions") . " l
										ON l.language_key like CONCAT(s.`key`,'%')
						WHERE (LOWER(`value`) like '%" . $needle . "%'
								OR LOWER(`value`) like '%" . $needle2 . "%'
								OR LOWER(s.`key`) like '%" . $needle . "%')
							AND s.`store_id` ='".( int )$current_store_id."'
						UNION
						SELECT s.setting_id,
								CONCAT(s.`group`,'-',s.`key`,'-',s.store_id) as active,
								s.store_id,
								CONCAT(`group`,' -> ',COALESCE( l.language_value,s.`key` )) as title,
						CONCAT_WS(' >> ',l.language_value) as text, '', 'core'
						FROM " . $this->db->table("language_definitions") . " l
						LEFT JOIN " . $this->db->table("settings") . " s ON l.language_key = CONCAT('entry_',REPLACE(s.`key`,'config_',''))
						WHERE (LOWER(l.language_value) like '%" . $needle . "%'
								OR LOWER(l.language_value) like '%" . $needle . "%'
								OR LOWER(l.language_key) like '%" . $needle . "%' )
							AND block='setting_setting' AND l.language_id ='".( int )$this->config->get('storefront_language_id')."'
							AND s.`store_id` ='".( int )$current_store_id."'
							AND setting_id>0
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$rows = $result->rows;
				$result=array();
				foreach($rows as $row){
					if(!isset($result[$row['setting_id']])){
						//remove all text between span tags
						$regex = '/<span(.*)span>/';
						$row['title'] = str_replace(array("	","  ","\n"),"",strip_tags(preg_replace($regex, '', $row['title'])));
						$row['text'] = str_replace(array("	","  ","\n"),"",strip_tags(preg_replace($regex, '', $row['text'])));
						$result[$row['setting_id']] = $row;
					}
				}
				$result = array_values($result);
				break;
			case "messages" :
				$sql = "SELECT DISTINCT msg_id, title as title, `message` as text
						FROM " . $this->db->table("messages") . " 
						WHERE ( LOWER(`title`) like '%" . $needle . "%' OR LOWER(`message`) like '%" . $needle . "%' OR LOWER(`title`) like '%" . $needle2 . "%' OR LOWER(`message`) like '%" . $needle2 . "%' )
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;
			case "extensions" :
				$sql = "SELECT DISTINCT `key` as extension, `key` as title, `key` as text
						FROM " . $this->db->table("extensions") . " e						
						WHERE LOWER(`key`) like '%" . $needle . "%' AND `type` <> 'total'
						LIMIT " . $offset . "," . $rows_count;

				$result = $this->db->query($sql);
				$result = $result->rows;
				break;

			case "downloads" :
				$sql = "SELECT d.download_id, name as title, name  as text
						FROM " . $this->db->table("downloads") . " d
						LEFT JOIN " . $this->db->table("download_descriptions") . " dd
							ON (d.download_id = dd.download_id AND dd.language_id IN (" . (implode(",", $search_languages)) . "))
						WHERE ( LOWER(dd.name) like '%" . $needle . "%' OR LOWER(dd.name) like '%" . $needle2 . "%' )
						LIMIT " . $offset . "," . $rows_count;
				$result = $this->db->query($sql);
				$result = $result->rows;
				break;

			default :
				$result = array(0 => array("text" => "no results! "));
				break;
		}

		if ($mode == 'listing') {
			if($search_category == 'commands') {
				$result = $this->_prepareCommandsResponse($result);
			} else {
				$result = $this->_prepareResponse($keyword,
					$this->results_controllers[$search_category]['page'],
					$this->results_controllers[$search_category]['id'],
					$result);
			}
		}
		foreach ($result as &$row) {
			$row['controller'] = $this->results_controllers[$search_category]['page'];
			//shorten text for suggestion
			if ($mode != 'listing') {
				$dectext = htmlentities($row['text'], ENT_QUOTES);
				$len = mb_strlen($dectext);
				if( $len > 100 ) {
						$ellipsis = '...';
						$row['text'] = mb_substr($dectext, 0, 100).$ellipsis;
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
	 * @param string|array $key_field(s)
	 * @param array $table
	 * @return array
	 */
	private function _prepareResponse($keyword = '', $rt = '', $key_field = '', $table = array()) {
		$output = array();
		if (!$rt || !$key_field || !$keyword) {
			return null;
		}

		$tmp = array();
		$text = '';
		if ($table && is_array($table)) {

			foreach ($table as $row) {
				//let's extract  and colorize keyword in row
				foreach ($row as $key => $field) {
					$field_decoded = htmlentities($field, ENT_QUOTES);

					// if keyword found
					$pos = mb_stripos($field_decoded, $keyword);
					if (is_int($pos) && $key != 'title') {
						$row ['title'] = '<span class="search_res_title">' . strip_tags($row ['title']) . "</span>";
						$start = $pos < 50 ? 0 : ($pos - 50);
						$keyword_len = mb_strlen($keyword);
						$field_len = mb_strlen($field_decoded);
						$ellipsis = ($field_len - $keyword_len > 10) ? '...' : '';
						// before founded word
						$text .= $ellipsis . mb_substr($field_decoded, $start, $pos);
						// founded word
						$len = ($field_len - ($pos + $keyword_len)) > 50 ? 50 : $field_len;
						// after founded word
						$text .= mb_substr($field_decoded, ($pos + $keyword_len), $len) . $ellipsis;

						$row ['text'] = $text;
						break;
					}
				}

				// exception for extension settings
				$temp_key_field = $key_field;
				$url = $rt;

				if ($rt == 'setting/setting' && !empty($row['extension'])) {
					$temp_key_field = $this->results_controllers['extensions']['id'];
					if($row['type']=='total'){ //for order total extensions
						$url = sprintf($this->results_controllers['extensions']['page2'],$row['extension']);
					}else{
						$url = $this->results_controllers['extensions']['page'];
					}
				}

				if (is_array($temp_key_field)) {
					foreach ($temp_key_field as $var) {
						$url .= "&" . $var . "=" . $row [$var];
					}
				} else {
					$url .= "&" . $temp_key_field . "=" . $row [$temp_key_field];
				}
				$tmp ['type'] = $row['type'];
				$tmp ['href'] = $this->html->getSecureURL($url);
				$tmp ['text'] = '<a href="' . $tmp ['href'] . '" target="_blank" title="' . $row ['text'] . '">' . $row ['title'] . '</a>';
				$output [] = $tmp;
			}
		} else {
			$this->load->language('tool/global_search');
			$output [0] = array("text" => $this->language->get('no_results_message'));
		}
		return $output;
	}

	private function _prepareCommandsResponse($table = array()) {
		$output = array();
		foreach ($table as $row) {
			$tmp = array();
			$tmp ['text'] = '<a href="' . $row['url'] . '" target="_blank" title="' . $row['text'] . '">' . $row['title'] . '</a>';
			$output [] = $tmp;
		}
		return $output;
	}
	/**
	 * function to get possible commands for the look up
	 *
	 * @param string $keyword
	 * @param string $mode ('total')
	 * @return array
	 */
	private function _possibleCommands($keyword, $mode = '') {
	
		$comds_obj = new AdminCommands();
		$this->commands = $comds_obj->commands;
		$result = $comds_obj->getCommands($keyword);

		if($mode == 'total'){
			return count($result['found_actions']);
		}

		$ret = array();
		if($result['found_actions']) {
			foreach($result['found_actions'] as $comnd) {
				$ret[] = array(
					'text' => $result['command']." ".$comnd['title']." ".$result['request'],
					'title' => $result['command']." ".$comnd['title']." ".$result['request'],
					'url' => $comnd['url']
				);						
			}
		}
		return $ret;
	}

}
