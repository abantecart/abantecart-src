<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ModelCatalogCategory extends Model {
	public function addCategory($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "categories
						  SET parent_id = '" . (int)$data['parent_id'] . "',
						      sort_order = '" . (int)$data['sort_order'] . "',
						      status = '" . (int)$data['status'] . "',
						      date_modified = NOW(),
						      date_added = NOW()");
	
		$category_id = $this->db->getLastId();
		
		foreach ($data['category_description'] as $language_id => $value) {
			$this->language->replaceDescriptions('category_descriptions',
											 array('category_id' => (int)$category_id),
											 array($language_id => array(
												 						'name' => $value['name'],
												 						'meta_keywords' => $value['meta_keywords'],
												 						'meta_description' => $value['meta_description'],
												 						'description' => $value['description']
											 )) );
		}
		
		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "categories_to_stores SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if ($data['keyword']) {
			$seo_key = SEOEncode($data['keyword'],'category_id',$category_id);
		}else {
			//Default behavior to save SEO URL keword from category name in default language
			/**
			 * @var ALanguageManager
			 */
			$seo_key = SEOEncode( $data['category_description'][$this->language->getDefaultLanguageID()]['name'],
								'category_id',
								$category_id );
		}
		if($seo_key){
			$this->language->replaceDescriptions('url_aliases',
												array('query' => "category_id=" . (int)$category_id),
												array((int)$this->session->data['content_language_id'] => array('keyword'=>$seo_key)));
		}else{
			$this->db->query("DELETE
							FROM " . DB_PREFIX . "url_aliases
							WHERE query = 'category_id=" . (int)$category_id . "'
								AND language_id = '".(int)$this->session->data['content_language_id']."'");
		}

		$this->cache->delete('category');

		return $category_id;
	}

	public function editCategory($category_id, $data) {

		$fields = array('parent_id', 'sort_order', 'status');
		$update = array('date_modified = NOW()');
		foreach ( $fields as $f ) {
			if ( isset($data[$f]) )
				$update[] = "$f = '".$this->db->escape($data[$f])."'";
		}
		if ( !empty($update) ) $this->db->query("UPDATE " . DB_PREFIX . "categories SET ". implode(',', $update) ." WHERE category_id = '" . (int)$category_id . "'");

		if ( !empty($data['category_description']) ) {
			foreach ($data['category_description'] as $language_id => $value) {
				$update = array();
				if ( isset($value['name']) ){
					$update["name"] = $value['name'];
				}
				if ( isset($value['description']) ){
					$update["description"] = $value['description'];
				}
				if ( isset($value['meta_keywords']) ){
					$update["meta_keywords"] = $value['meta_keywords'];
				}
				if ( isset($value['meta_description']) ){
					$update["meta_description"] = $value['meta_description'];
				}
				if ( !empty($update) ){
					// insert or update
					$this->language->replaceDescriptions('category_descriptions',
														 array('category_id' => (int)$category_id),
														 array($language_id => $update) );
				}
			}
		}

		if (isset($data['category_store'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "categories_to_stores WHERE category_id = '" . (int)$category_id . "'");
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "categories_to_stores SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$data['keyword'] =  SEOEncode($data['keyword']);
			if($data['keyword']){
			$this->language->replaceDescriptions('url_aliases',
												array('query' => "category_id=" . (int)$category_id),
												array((int)$this->session->data['content_language_id'] => array('keyword' => $data['keyword'])));
			}else{
				$this->db->query("DELETE
								FROM " . DB_PREFIX . "url_aliases
								WHERE query = 'category_id=" . (int)$category_id . "'
									AND language_id = '".(int)$this->session->data['content_language_id']."'");
			}

		}

		$this->cache->delete('category');

	}
	
	public function deleteCategory($category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "categories WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_descriptions WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "categories_to_stores WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_aliases WHERE query = 'category_id=" . (int)$category_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_categories WHERE category_id = '" . (int)$category_id . "'");
		
		//delete children categories
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "categories WHERE parent_id = '" . (int)$category_id . "'");
		$lm = new ALayoutManager();
		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
			$lm->deletePageLayout('pages/product/category','path',$result['category_id']);
		}
		$lm->deletePageLayout('pages/product/category','path',$category_id);
		$this->cache->delete('category');
	} 

	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT *,
										(SELECT keyword
										FROM " . DB_PREFIX . "url_aliases
										WHERE query = 'category_id=" . (int)$category_id . "'
											AND language_id='".(int)$this->session->data['content_language_id']."' ) AS keyword
									FROM " . DB_PREFIX . "categories
									WHERE category_id = '" . (int)$category_id . "'");
		
		return $query->row;
	}

	public function getCategories($parent_id) {
		$language_id = $this->session->data['content_language_id'];
		$category_data = $this->cache->get('category.' . $parent_id, $language_id);

		if (!$category_data) {
			$category_data = array();

			$query = $this->db->query("SELECT *
										FROM " . DB_PREFIX . "categories c
										LEFT JOIN " . DB_PREFIX . "category_descriptions cd
										ON (c.category_id = cd.category_id)
										WHERE c.parent_id = '" . (int)$parent_id . "'
											AND cd.language_id = '" . (int)$language_id . "'
										ORDER BY c.sort_order, cd.name ASC");

			foreach ($query->rows as $result) {
				$category_data[] = array(
					'category_id' => $result['category_id'],
					'parent_id' => $result['parent_id'],
					'name'        => $this->getPath($result['category_id'], $language_id),
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				);

				$category_data = array_merge($category_data, $this->getCategories($result['category_id']));
			}

			$this->cache->set('category.' . $parent_id, $category_data, $language_id );
		}

		return $category_data;
	}
	
	public function getCategoriesData($data, $mode = 'default') {

		if ( $data['language_id'] ) {
			$language_id = (int)$data['language_id'];
		} else {
			$language_id = (int)$this->session->data['content_language_id'];
		}

		if ( $data['store_id'] ) {
			$store_id = (int)$data['store_id'];
		} else {
			$store_id = (int)$this->config->get('config_store_id');
		}

		if ($mode == 'total_only') {
			$total_sql = 'count(*) as total';
		}
		else {
			$total_sql = "*,
						  c.category_id,
						  (SELECT count(*) as cnt
						  	FROM ".$this->db->table('products_to_categories')." p
						  	WHERE p.category_id = c.category_id) as products_count ";
		}
        $where = (isset($data['parent_id']) ? "WHERE c.parent_id = '" . (int)$data['parent_id'] . "'" : '' );
		$sql = "SELECT ". $total_sql ."
				FROM " . $this->db->table('categories')." c
				LEFT JOIN " . $this->db->table('category_descriptions')." cd
					ON (c.category_id = cd.category_id AND cd.language_id = '" . $language_id . "')
				INNER JOIN " . $this->db->table('categories_to_stores')." cs				
					ON (c.category_id = cs.category_id AND cs.store_id = '" . $store_id . "')
				" . $where;

		if ( !empty($data['subsql_filter']) ) {
			$sql .= ($where ? " AND " : 'WHERE ').$data['subsql_filter'];
		}

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
		    $query = $this->db->query($sql);
		    return $query->row['total'];
		}

		$sort_data = array(
		    'name' => 'cd.name',
		    'status' => 'c.status',
		    'sort_order' => 'c.sort_order'
		);	
		
		if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data)) ) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY c.sort_order, cd.name ";
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
		$category_data = array();
		foreach ($query->rows as $result) {
			$category_data[] = array(
				'category_id' => $result['category_id'],
				'name'        => $this->getPath($result['category_id'], $language_id),
				'basename'    => $result['name'],
				'status'  	  => $result['status'],
				'sort_order'  => $result['sort_order'],
				'products_count'=>$result['products_count']

			);
		}		
		return $category_data;
	}

	public function getParents() {
		$query = $this->db->query(
			"SELECT DISTINCT c.parent_id, cd.name
			 FROM " . DB_PREFIX . "categories c
			 LEFT JOIN " . DB_PREFIX . "categories c1 ON (c.parent_id = c1.category_id)
			 LEFT JOIN " . DB_PREFIX . "category_descriptions cd ON (c1.category_id = cd.category_id)
			 WHERE cd.language_id = '" . (int)$this->session->data['content_language_id'] . "'
			 ORDER BY c.sort_order, cd.name ASC");
		$result = array();
		foreach ( $query->rows as $r ) {
			$result[$r['parent_id']] = $r['name'];
		}

		return $result;
	}

	public function getLeafCategories() {
		$query = $this->db->query(
			"SELECT t1.category_id as category_id FROM " . DB_PREFIX . "categories AS t1 LEFT JOIN " . DB_PREFIX . "categories as t2
			 ON t1.category_id = t2.parent_id WHERE t2.category_id IS NULL");
		$result = array();
		foreach ( $query->rows as $r ) {
			$result[$r['category_id']] = $r['category_id'];
		}

		return $result;
	}

	public function getPath($category_id) {
		$language_id = (int)$this->session->data['content_language_id'];
		$query = $this->db->query("SELECT name, parent_id
		                            FROM " . DB_PREFIX . "categories c
		                            LEFT JOIN " . DB_PREFIX . "category_descriptions cd
		                                ON (c.category_id = cd.category_id)
		                            WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . $language_id . "'
		                            ORDER BY c.sort_order, cd.name ASC");
		
		$category_info = $query->row;
		
		if ($category_info['parent_id']) {
			return $this->getPath($category_info['parent_id'], $language_id) . $this->language->get('text_separator') . $category_info['name'];
		} else {
			return $category_info['name'];
		}
	}
	
	public function getCategoryDescriptions($category_id) {
		$category_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_descriptions WHERE category_id = '" . (int)$category_id . "'");
		
		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'meta_keywords'    => $result['meta_keywords'],
				'meta_description' => $result['meta_description'],
				'description'      => $result['description']
			);
		}
		
		return $category_description_data;
	}	

	public function getCategoryStores($category_id) {
		$category_store_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "categories_to_stores WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}
		
		return $category_store_data;
	}
	
	public function getTotalCategories($data = array()) {
		return $this->getCategoriesData($data, 'total_only');
	}
}
