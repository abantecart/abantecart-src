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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ModelCatalogProduct extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addProduct($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "products
                            SET model = '" . $this->db->escape($data['model']) . "',
                                sku = '" . $this->db->escape($data['sku']) . "',
                                location = '" . $this->db->escape($data['location']) . "',
                                quantity = '" . preformatInteger($data['quantity']) . "',
                                minimum = '" . preformatInteger($data['minimum']) . "',
                                maximum = '" . preformatInteger($data['maximum']) . "',
                                subtract = '" . (int)$data['subtract'] . "',
                                stock_status_id = '" . (int)$data['stock_status_id'] . "',
                                date_available = '" . $this->db->escape($data['date_available']) . "',
                                manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
                                shipping = '" . (int)$data['shipping'] . "',
                                ship_individually = '" . (int)$data['ship_individually'] . "',
                                free_shipping = '" . (int)$data['free_shipping'] . "',
                                shipping_price = '" . preformatFloat($data['shipping_price'], $this->language->get('decimal_point')) . "',
                                price = '" . preformatFloat($data['price'], $this->language->get('decimal_point')) . "',
                                cost = '" . preformatFloat($data['cost'], $this->language->get('decimal_point')) . "',
                                weight = '" . preformatFloat($data['weight'], $this->language->get('decimal_point')) . "',
                                weight_class_id = '" . (int)$data['weight_class_id'] . "',
                                length = '" . preformatFloat($data['length'], $this->language->get('decimal_point')) . "',
                                width = '" . preformatFloat($data['width'], $this->language->get('decimal_point')) . "',
                                height = '" . preformatFloat($data['height'], $this->language->get('decimal_point')) . "',
                                length_class_id = '" . (int)$data['length_class_id'] . "',
                                status = '" . (int)$data['status'] . "',
                                tax_class_id = '" . (int)$data['tax_class_id'] . "',
                                sort_order = '" . (int)$data['sort_order'] . "',
                                date_added = NOW()");

		$product_id = $this->db->getLastId();
		// if new product
		if (!is_int(key($data['product_description']))) {
			foreach ($data['product_description'] as $field => $value) {
				$update[(int)$this->session->data['content_language_id']][$field] = $value;
			}
			$this->language->replaceDescriptions('product_descriptions',
				array('product_id' => (int)$product_id),
				$update);
		} else { // if cloning
			foreach ($data['product_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_descriptions
											SET product_id = '" . (int)$product_id . "',
												language_id = '" . (int)$language_id . "',
												name = '" . $this->db->escape($value['name']) . "',
												meta_keywords = '" . $this->db->escape($value['meta_keywords']) . "',
												meta_description = '" . $this->db->escape($value['meta_description']) . "',
												description = '" . $this->db->escape($value['description']) . "'");
			}
			reset($data['product_description']);
		}


		if ($data['featured']) {
			$this->setFeatured($product_id, true);
		}

		if ($data['keyword']) {
			$seo_key = $data['keyword'];
		} else {
			//Default behavior to save SEO URL keword from product name in default language
			$languages = $this->language->getAvailableLanguages();
			$default_lang_id = $languages[$this->config->get('config_storefront_language')]['language_id'];

			if (!is_int(key($data['product_description']))) { // when creates
				$seo_key = SEOEncode($data['product_description']['name']);
			}else{ // when clones
				$seo_key = SEOEncode($data['product_description'][$default_lang_id]['name']);
			}
			//Check if key is unique  
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_aliases
									   WHERE keyword = '" . $this->db->escape($seo_key) . "'");
			if ($query->num_rows) {
				$seo_key .= '_' . $product_id;
			}
		}

		$this->db->query("INSERT INTO " . DB_PREFIX . "url_aliases
						  SET query = 'product_id=" . (int)$product_id . "',
						  keyword = '" . $this->db->escape($seo_key) . "'");

		if ($data['product_tags']) {
			$tags = explode(',', $data['product_tags']);
			foreach ($tags as &$tag) {
				$tag = trim($tag);
			}
			unset($tag);
			$tags = array_unique($tags);
			foreach ($tags as $tag) {
				$tag = trim($tag);
				if ($tag) {
					$this->language->addDescriptions('product_tags',
						array('product_id' => (int)$product_id,
							'tag' => $tag),
						array((int)$this->session->data['content_language_id'] => array('tag' => $tag)));
				}
			}
		}
		$this->cache->delete('product');
		return $product_id;
	}

	public function addProductDiscount($product_id, $data) {
		$data['price'] = str_replace(" ", "", $data['price']);
		if(!empty($data['date_start'])){
		$data['date_start'] = dateDisplay2ISO($data['date_start'],$this->language->get('date_format_short'));
		}
		if(!empty($data['date_end'])){
			$data['date_end'] = dateDisplay2ISO($data['date_end'],$this->language->get('date_format_short'));
		}
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "product_discounts
				SET product_id = '" . (int)$product_id . "',
					customer_group_id = '" . (int)$data['customer_group_id'] . "',
					quantity = '" . preformatInteger($data['quantity']) . "',
					priority = '" . (int)$data['priority'] . "',
					price = '" . preformatFloat($data['price']) . "',
					date_start = '" . $this->db->escape($data['date_start']) . "',
					date_end = '" . $this->db->escape($data['date_end']) . "'");
		$id = $this->db->getLastId();
		$this->cache->delete('product');
		return $id;
	}

	public function addProductSpecial($product_id, $data) {
		$data['price'] = str_replace(" ", "", $data['price']);
		if(!empty($data['date_start'])){
			$data['date_start'] = dateDisplay2ISO($data['date_start'],$this->language->get('date_format_short'));
		}
		if(!empty($data['date_end'])){
			$data['date_end'] = dateDisplay2ISO($data['date_end'],$this->language->get('date_format_short'));
		}

		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "product_specials
			SET product_id = '" . (int)$product_id . "',
				customer_group_id = '" . (int)$data['customer_group_id'] . "',
				priority = '" . (int)$data['priority'] . "',
				price = '" . preformatFloat($data['price'], $this->language->get('decimal_point')) . "',
				date_start = '" . $this->db->escape($data['date_start']) . "',
				date_end = '" . $this->db->escape($data['date_end']) . "'");
		$id = $this->db->getLastId();
		$this->cache->delete('product');
		return $id;
	}

	public function updateProduct($product_id, $data) {
		$fields = array(
			"model",
			"sku",
			"location",
			"quantity",
			"minimum",
			"maximum",
			"subtract",
			"stock_status_id",
			"date_available",
			"manufacturer_id",
			"shipping",
			"ship_individually",
			"free_shipping",
			"shipping_price",
			"price",
			"cost",
			"weight",
			"weight_class_id",
			"length",
			"width",
			"height",
			"length_class_id",
			"status",
			"tax_class_id",
			"sort_order");
		$preformat_fields = array(
			"shipping_price",
			"price",
			"cost",
			"weight",
			"length",
			"width",
			"height");

		$update = array('date_modified = NOW()');
		foreach ($fields as $f) {
			if (isset($data[$f])) {
				if (in_array($f, $preformat_fields)) {
					$data[$f] = preformatFloat($data[$f], $this->language->get('decimal_point'));
				}
				$update[] = "$f = '" . $this->db->escape($data[$f]) . "'";
			}
		}
		if (!empty($update)) {
			$this->db->query("UPDATE `" . DB_PREFIX . "products` SET " . implode(',', $update) . " WHERE product_id = '" . (int)$product_id . "'");
		}

		if (!empty($data['product_description'])) {
			foreach ($data['product_description'] as $field => $value) {

				$fields = array('name', 'description', 'meta_keywords', 'meta_description');
				$update = array();
				foreach ($fields as $f) {
					if ($f == $field) {
						$update[$f] = $value;
					}
				}

				if (!empty($update)) {
					$this->language->replaceDescriptions('product_descriptions',
						array('product_id' => (int)$product_id),
						array((int)$this->session->data['content_language_id'] => $update));
				}
			}
		}
		if (isset($data['featured'])) {
			$this->setFeatured($product_id, ($data['featured'] ? true : false));
		}

		if (isset($data['keyword'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_aliases
							WHERE query = 'product_id=" . (int)$product_id . "'");
			if ($data['keyword']) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_aliases
									SET keyword = '" . $this->db->escape($data['keyword']) . "',
										query = 'product_id=" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_tags'])) {
			$language_id = $this->session->data['content_language_id'];
			$tags = explode(',', $data['product_tags']);

			foreach ($tags as &$tag) {
				$tag = trim($tag);
			}
			
			$this->language->replaceMultipleDescriptions('product_tags',
						array( 'product_id' => (int)$product_id ),
						array((int)$language_id => array('tag' => array_unique($tags)) ));
		}

		$this->cache->delete('product');
	}

	/**
	 * @param int $product_discount_id
	 * @param array $data
	 */
	public function updateProductDiscount($product_discount_id, $data) {
		$fields = array("customer_group_id", "quantity", "priority", "price", "date_start", "date_end",);
		if (isset($data['price'])) {
			$data['price'] = preformatFloat($data['price'], $this->language->get('decimal_point'));
		}
		if(!empty($data['date_start'])){
			$data['date_start'] = dateDisplay2ISO($data['date_start'],$this->language->get('date_format_short'));
		}
		if(!empty($data['date_end'])){
			$data['date_end'] = dateDisplay2ISO($data['date_end'],$this->language->get('date_format_short'));
		}
		$update = array();
		foreach ($fields as $f) {
			if (isset($data[$f]))
				$update[] = "$f = '" . $this->db->escape($data[$f]) . "'";
		}
		if (!empty($update)) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product_discounts`
								SET " . implode(',', $update) . "
								WHERE product_discount_id = '" . (int)$product_discount_id . "'");
		}
		$this->cache->delete('product');
	}

	public function updateProductSpecial($product_special_id, $data) {
		$fields = array("customer_group_id", "priority", "price", "date_start", "date_end",);
		if (isset($data['price'])) {
			$data['price'] = preformatFloat($data['price'], $this->language->get('decimal_point'));
		}
		if(!empty($data['date_start'])){
			$data['date_start'] = dateDisplay2ISO($data['date_start'],$this->language->get('date_format_short'));
		}
		if(!empty($data['date_end'])){
			$data['date_end'] = dateDisplay2ISO($data['date_end'],$this->language->get('date_format_short'));
		}

		$update = array();
		foreach ($fields as $f) {
			if (isset($data[$f]))
				$update[] = "$f = '" . $this->db->escape($data[$f]) . "'";
		}
		if (!empty($update)) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product_specials` SET " . implode(',', $update) . " WHERE product_special_id = '" . (int)$product_special_id . "'");
		}
		$this->cache->delete('product');
	}

	public function updateProductLinks($product_id, $data) {

		if (isset($data['product_store'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_stores WHERE product_id = '" . (int)$product_id . "'");
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "products_to_stores SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_download'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_downloads WHERE product_id = '" . (int)$product_id . "'");
			foreach ($data['product_download'] as $download_id) {
				if ((int)$download_id)
					$this->db->query("INSERT INTO " . DB_PREFIX . "products_to_downloads SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_categories WHERE product_id = '" . (int)$product_id . "'");
			foreach ($data['product_category'] as $category_id) {
				if ((int)$category_id)
					$this->db->query("INSERT INTO " . DB_PREFIX . "products_to_categories SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "products_related WHERE product_id = '" . (int)$product_id . "'");
			foreach ($data['product_related'] as $related_id) {
				if ((int)$related_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "products_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "products_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "products_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
				}
			}
		}
		$this->cache->delete('product');
	}

	public function addProductOption($product_id, $data) {

		$am = new AAttribute_Manager();
		$attribute = $am->getAttribute($data['attribute_id']);
		if ($attribute) {
			$data['element_type'] = $attribute['element_type'];
			$data['required'] = $attribute['required'];
		}

		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "product_options
                (product_id,
                 attribute_id,
                 element_type,
                 required,
                 sort_order,
                 group_id,
                 status)
            VALUES ('" . (int)$product_id . "',
                '" . (int)$data['attribute_id'] . "',
                '" . $this->db->escape($data['element_type']) . "',
                '" . (int)$data['required'] . "',
                '" . (int)$data['sort_order'] . "',
                '" . (int)$data['group_id'] . "',
                '" . (int)$data['status'] . "')");
		$product_option_id = $this->db->getLastId();

		if (!empty($data['option_name'])) {
			$attributeDescriptions = array(
				$this->session->data['content_language_id'] => $data['option_name'],
			);
		} else {
			$attributeDescriptions = $am->getAttributeDescriptions($data['attribute_id']);
		}
		foreach ($attributeDescriptions as $language_id => $name) {
			$this->language->replaceDescriptions('product_option_descriptions',
				array('product_option_id' => (int)$product_option_id,
					'product_id' => (int)$product_id),
				array($language_id => array('name' => $name)));
		}

		//add empty option value for single value attributes
		$elements_with_options = HtmlElementFactory::getElementsWithOptions();
		if (!in_array($data['element_type'], $elements_with_options)) {
			$this->insertProductOptionValue($product_id, $product_option_id, '', '', array());
		}

		$this->cache->delete('product');
		return $product_option_id;
	}

	public function deleteProductOption($product_id, $product_option_id) {

		$am = new AAttribute_Manager();
		$attribute = $am->getAttributeByProductOptionId($product_option_id);
		$group_attribute = $am->getAttributes(array(), 0, $attribute['attribute_id']);
		if (count($group_attribute)) {
			//delete children options/values
			$children = $this->db->query(
				"SELECT product_option_id FROM " . DB_PREFIX . "product_options
                WHERE product_id = '" . (int)$product_id . "'
                    AND group_id = '" . (int)$product_option_id . "'");
			foreach ($children->rows as $g_attribute) {
				$this->_deleteProductOption($product_id, $g_attribute['product_option_id']);
			}
		}

		$this->_deleteProductOption($product_id, $product_option_id);

		$this->cache->delete('product');
	}

	private function _deleteProductOption($product_id, $product_option_id) {
		$values = $this->getProductOptionValues($product_id, $product_option_id);
		foreach ($values as $v) {
			$this->deleteProductOptionValue($product_id, $v['product_option_value_id']);
		}

		$this->db->query(
			"DELETE FROM " . DB_PREFIX . "product_options
		    WHERE product_id = '" . (int)$product_id . "'
		        AND product_option_id = '" . (int)$product_option_id . "'");
		$this->db->query(
			"DELETE FROM " . DB_PREFIX . "product_option_descriptions
		    WHERE product_id = '" . (int)$product_id . "'
		        AND product_option_id = '" . (int)$product_option_id . "'");
	}

	//Add new product option value and value descriptions for all global atributes langauges or current language 
	public function addProductOptionValueAndDescription($product_id, $option_id, $data) {
		if (empty($product_id) || empty($option_id) || empty($data)) {
			return null;
		}

		$attribute_value_id = $data['attribute_value_id'];
		if (is_array($data['attribute_value_id'])) {
			$attribute_value_id = '';
		}

		$am = new AAttribute_Manager();
		//build grouped attributes if this is a parent attribute
		if (is_array($data['attribute_value_id'])) {
			//add children option values from global attributes
			$groupData = array();
			foreach ($data['attribute_value_id'] as $child_option_id => $attribute_value_id) {
				#special serialized data for grouped options
				$groupData[] = array('attr_id' => $child_option_id, 'attr_v_id' => $attribute_value_id);
			}
			$data['grouped_attribute_data'] = serialize($groupData);
		}

		$pd_opt_val_id = $this->insertProductOptionValue($product_id, $option_id, $attribute_value_id, '', $data);

		//Build options value descriptions
		if (is_array($data['attribute_value_id'])) {
			//add children option values description from global attributes
			$group_description = array();
			$descr_names = array();
			foreach ($data['attribute_value_id'] as $child_option_id => $attribute_value_id) {
				#special insert for grouped options
				foreach ($am->getAttributeValueDescriptions($attribute_value_id) as $language_id => $name) {
					$group_description[$language_id][] = array('attr_v_id' => $attribute_value_id, 'name' => $name);
					$descr_names[$language_id][] = $name;
				}
			}

			// Insert generic merged name
			foreach ($descr_names as $language_id => $name) {
				if (count($group_description[$language_id])) {
					$grouped_names = serialize($group_description[$language_id]);
				}
				$this->insertProductOptionValueDescriptions($product_id, $pd_opt_val_id, implode(' / ', $name), $language_id, $grouped_names);
			}

		} else {
			if (!$data['attribute_value_id']) {
				//We save custom option value for current language
				$valueDescriptions = array(
					$this->session->data['content_language_id'] => $data['name'],
				);
			} else {
				//We have global attributes, copy option value text from there.
				$valueDescriptions = $am->getAttributeValueDescriptions($data['attribute_value_id']);
			}
			foreach ($valueDescriptions as $language_id => $name) {
				$this->insertProductOptionValueDescriptions($product_id, $pd_opt_val_id, $name, $language_id);
			}
		}

		$this->cache->delete('product');
		return $pd_opt_val_id;
	}

	public function getProductOptionValueDescriptions($product_id, $pd_opt_val_id, $language_id) {
		if (empty($product_id) || empty($pd_opt_val_id) || empty($language_id)) {
			return null;
		}
		return $this->db->query(
			"SELECT *
            FROM " . DB_PREFIX . "product_option_value_descriptions
            WHERE product_option_value_id = '" . (int)$pd_opt_val_id . "'
                AND product_id = '" . (int)$product_id . "'
                AND language_id = '" . (int)$language_id . "' ");
	}

	public function insertProductOptionValueDescriptions($product_id, $pd_opt_val_id, $name, $language_id, $grp_attr_names = null) {
		if (empty($product_id) || empty($pd_opt_val_id) || empty($language_id)) {
			return null;
		}

		$this->language->replaceDescriptions('product_option_value_descriptions',
			array('product_option_value_id' => (int)$pd_opt_val_id,
				'product_id' => (int)$product_id),
			array($language_id => array('name' => $name,
				'grouped_attribute_names' => $grp_attr_names
			)));

		return $this->db->getLastId();
	}

	public function updateProductOptionValueDescriptions($product_id, $pd_opt_val_id, $name, $language_id, $grp_attr_names = null) {
		if (empty($product_id) || empty($pd_opt_val_id) || empty($language_id)) {
			return null;
		}
		$this->language->replaceDescriptions('product_option_value_descriptions',
			array('product_option_value_id' => (int)$pd_opt_val_id,
				'product_id' => (int)$product_id),
			array($language_id => array('name' => $name,
				'grouped_attribute_names' => $grp_attr_names
			)));

		return $pd_opt_val_id;
	}

	public function deleteProductOptionValueDescriptions($product_id, $pd_opt_val_id, $language_id = '') {
		if (empty($product_id) || empty($pd_opt_val_id)) {
			return null;
		}
		if ($language_id) {
			$add_language = " AND language_id = '" . (int)$language_id . "'";
		}
		$this->db->query(
			"DELETE FROM " . DB_PREFIX . "product_option_value_descriptions
            WHERE product_id = '" . (int)$product_id . "'
                AND product_option_value_id = '" . (int)$pd_opt_val_id . "'" . $add_language);
	}

	public function insertProductOptionValue($product_id, $option_id, $attribute_value_id, $pd_opt_val_id, $data) {
		if (empty($product_id) || empty($option_id)) {
			return null;
		}
		$this->db->query(
			"INSERT INTO " . DB_PREFIX . "product_option_values
	        SET product_option_id = '" . (int)$option_id . "',
	            product_id = '" . (int)$product_id . "',
                group_id = '" . (int)$pd_opt_val_id . "',
	            sku = '" . $this->db->escape($data['sku']) . "',
	            quantity = '" . $this->db->escape($data['quantity']) . "',
	            subtract = '" . $this->db->escape($data['subtract']) . "',
	            price = '" . preformatFloat($data['price'], $this->language->get('decimal_point')) . "',
	            prefix = '" . $this->db->escape($data['prefix']) . "',
	            weight = '" . preformatFloat($data['weight'], $this->language->get('decimal_point')) . "',
	            weight_type = '" . $this->db->escape($data['weight_type']) . "',
	            attribute_value_id = '" . $this->db->escape($attribute_value_id) . "',
	            grouped_attribute_data = '" . $this->db->escape($data['grouped_attribute_data']) . "',
	            sort_order = '" . (int)$data['sort_order'] . "'");
		return $this->db->getLastId();
	}

	/*
	*  Update singe product option value
	*/
	public function updateProductOptionValue($pd_opt_val_id, $attribute_value_id, $data) {
		if (empty($pd_opt_val_id) || empty($data)) {
			return null;
		}
		//If se have grouped (parent/child) options save no main attribute id
		if (is_array($attribute_value_id)) {
			$attribute_value_id = '';
		}

		$this->db->query(
			"UPDATE " . DB_PREFIX . "product_option_values
	        SET sku = '" . $this->db->escape($data['sku']) . "',
	            quantity = '" . $this->db->escape($data['quantity']) . "',
	            subtract = '" . $this->db->escape($data['subtract']) . "',
	            price = '" . $this->db->escape($data['price']) . "',
	            prefix = '" . $this->db->escape($data['prefix']) . "',
	            weight = '" . preformatFloat($data['weight'], $this->language->get('decimal_point')) . "',
	            weight_type = '" . $this->db->escape($data['weight_type']) . "',
	            attribute_value_id = '" . $this->db->escape($attribute_value_id) . "',
	            grouped_attribute_data = '" . $this->db->escape($data['grouped_attribute_data']) . "',
	            sort_order = '" . (int)$data['sort_order'] . "'
	        WHERE product_option_value_id = '" . (int)$pd_opt_val_id . "'  ");
		return $pd_opt_val_id;
	}

	/*
	*	Update product option value and value descriptions for set langauge
	*/
	public function updateProductOptionValueAndDescription($product_id, $pd_opt_val_id, $data, $language_id) {
		$attribute_value_id = $data['attribute_value_id'];
		if (is_array($data['attribute_value_id'])) {
			$attribute_value_id = '';
		}

		$am = new AAttribute_Manager();
		//build grouped attributes if this is a parent attribute
		if (is_array($data['attribute_value_id'])) {
			//update children option values from global attributes
			$groupData = array();
			foreach ($data['attribute_value_id'] as $child_option_id => $attr_val_id) {
				#special serialized data for grouped options
				$groupData[] = array('attr_id' => $child_option_id, 'attr_v_id' => $attr_val_id);
			}
			$data['grouped_attribute_data'] = serialize($groupData);
		}

		$this->updateProductOptionValue($pd_opt_val_id, $attribute_value_id, $data);

		if (is_array($data['attribute_value_id'])) {
			//update children option values description from global attributes
			$group_description = array();
			$descr_names = array();
			foreach ($data['attribute_value_id'] as $child_option_id => $attr_val_id) {
				#special insert for grouped options
				foreach ($am->getAttributeValueDescriptions($attr_val_id) as $lang_id => $name) {
					if ($language_id == $lang_id) {
						$group_description[$language_id][] = array('attr_v_id' => $attr_val_id, 'name' => $name);
						$descr_names[$language_id][] = $name;
					}
				}
			}
			// Insert generic merged name
			foreach ($descr_names as $lang_id => $name) {
				if ($language_id == $lang_id && count($group_description[$language_id])) {
					$group_description[$language_id][] = $name;
					$grouped_names = serialize($group_description[$language_id]);
					$this->updateProductOptionValueDescriptions($product_id, $pd_opt_val_id, implode(' / ', $name), $language_id, $grouped_names);
				}
			}

		} else {

			if (!$data['attribute_value_id']) {

				$exist = $this->getProductOptionValueDescriptions($product_id, $pd_opt_val_id, $language_id);
				if ($exist->num_rows) {
					$this->updateProductOptionValueDescriptions($product_id, $pd_opt_val_id, $data['name'], $language_id);
				} else {
					$this->insertProductOptionValueDescriptions($product_id, $pd_opt_val_id, $data['name'], $language_id);
				}

			} else {
				$valueDescriptions = $am->getAttributeValueDescriptions($data['attribute_value_id']);
				foreach ($valueDescriptions as $lang_id => $name) {
					if ($language_id == $lang_id) {
						//Update only language that we currently work with
						$this->updateProductOptionValueDescriptions($product_id, $pd_opt_val_id, $name, $language_id);
					}
				}
			}
		}

		$this->cache->delete('product');
	}

	public function deleteProductOptionValue($product_id, $pd_opt_val_id, $language_id = 0) {
		if (empty($product_id) || empty($pd_opt_val_id)) {
			return null;
		}

		$this->_deleteProductOptionValue($product_id, $pd_opt_val_id, $language_id);

		//delete children values
		$children = $this->db->query(
			"SELECT product_option_value_id FROM " . DB_PREFIX . "product_option_values
            WHERE product_id = '" . (int)$product_id . "'
                AND group_id = '" . (int)$pd_opt_val_id . "'");
		foreach ($children->rows as $g_attribute) {
			$this->_deleteProductOptionValue($product_id, $g_attribute['product_option_value_id'], $language_id);
		}

		$this->cache->delete('product');
	}

	public function _deleteProductOptionValue($product_id, $pd_opt_val_id, $language_id) {
		if (empty($product_id) || empty($pd_opt_val_id)) {
			return null;
		}

		if ($language_id) {
			$add_language = " AND language_id = '" . (int)$language_id . "'";
		}

		$this->db->query(
			"DELETE FROM " . DB_PREFIX . "product_option_value_descriptions
            WHERE product_id = '" . (int)$product_id . "'
                AND product_option_value_id = '" . (int)$pd_opt_val_id . "'" . $add_language);

		//Delete product_option_values that have no values left in descriptions
		$sql = "DELETE FROM `" . DB_PREFIX . "product_option_values`
    	    	WHERE product_option_value_id = '" . (int)$pd_opt_val_id . "' AND product_option_value_id not in 
    	    	( SELECT product_option_value_id FROM `" . DB_PREFIX . "product_option_value_descriptions`
    	    			 WHERE product_id = '" . (int)$product_id . "' 
    	    			 AND product_option_value_id = '" . (int)$pd_opt_val_id . "')";
		$this->db->query($sql);
		//get product resources
		$rm = new AResourceManager();
		$resources = $rm->getResourcesList(array(
			'object_name' => 'product_option_value',
			'object_id' => (int)$pd_opt_val_id));
		foreach ($resources as $r) {
			$rm->unmapResource(
				'product_option_value',
				$pd_opt_val_id,
				$r['resource_id']);
		}
	}

	/**
	 * @param int $product_id
	 * @return bool
	 */
	public function copyProduct($product_id) {
		if (empty($product_id)) return false;

		$sql = "SELECT DISTINCT *, p.product_id
				FROM " . DB_PREFIX . "products p
				LEFT JOIN " . DB_PREFIX . "product_descriptions pd
					ON (p.product_id = pd.product_id AND
								pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
				WHERE p.product_id = '" . (int)$product_id . "'";
		$query = $this->db->query($sql);

		if ($query->num_rows) {
			$data = $query->row;
			$data = array_merge($data, array('product_description' => $this->getProductDescriptions($product_id)));
			foreach ($data['product_description'] as $lang => $desc) {
				$data['product_description'][$lang]['name'] .= ' ( Copy )';
			}
			$data = array_merge($data, array('product_option' => $this->getProductOptions($product_id)));
			$data['keyword'] = '';

			$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
			$data = array_merge($data, array('product_download' => $this->getProductDownloads($product_id)));
			$data = array_merge($data, array('product_category' => $this->getProductCategories($product_id)));
			$data = array_merge($data, array('product_store' => $this->getProductStores($product_id)));
			$data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
			$data = array_merge($data, array('product_tags' => $this->getProductTags($product_id)));

			//set status to off for cloned product
			$data['status'] = 0;

			//get product resources
			$rm = new AResourceManager();
			$resources = $rm->getResourcesList(array('object_name' => 'products', 'object_id' => $product_id));

			$new_product_id = $this->addProduct($data);

			foreach ($data['product_discount'] as $item) {
				$this->addProductDiscount($new_product_id, $item);
			}
			foreach ($data['product_special'] as $item) {
				$this->addProductSpecial($new_product_id, $item);
			}

			$this->updateProductLinks($new_product_id, $data);
			$this->_clone_product_options($new_product_id, $data);

			foreach ($resources as $r) {
				$rm->mapResource(
					'products',
					$new_product_id,
					$r['resource_id']
				);
			}
			$this->cache->delete('product');
			return $data['name'];
		}

		return false;
	}

	/**
	 * @param int $product_id
	 * @param array $data
	 */
	private function _clone_product_options($product_id, $data) {
		//Do not use before close review.
		//Note: This is done only after product clonning. This is not to be used on existing product.
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_options WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_descriptions WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_values WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_descriptions WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				$sql = "INSERT INTO " . DB_PREFIX . "product_options
						SET product_id = '" . (int)$product_id . "',
							sort_order = '" . (int)$product_option['sort_order'] . "'";
				if ($product_option['attribute_id']) {
					$sql .= ", attribute_id = '" . (int)$product_option['attribute_id'] . "'";
				}
				if ($product_option['group_id']) {
					$sql .= ", group_id = '" . (int)$product_option['group_id'] . "'";
				}
				if ($product_option['element_type']) {
					$sql .= ", element_type = '" . $this->db->escape($product_option['element_type']) . "'";
				}
				if ($product_option['required']) {
					$sql .= ", required = '" . (int)$product_option['required'] . "'";
				}
				$this->db->query($sql);
				$product_option_id = $this->db->getLastId();

				foreach ($product_option['language'] as $language_id => $language) {
					$this->language->replaceDescriptions('product_option_descriptions',
						array('product_option_id' => (int)$product_option_id,
							'product_id' => (int)$product_id),
						array($language_id => array('name' => $language['name'])));
				}

				if (isset($product_option['product_option_value'])) {
					//get product resources
					$rm = new AResourceManager();
					foreach ($product_option['product_option_value'] as $pd_opt_vals) {
						$pd_opt_vals['price'] = str_replace(" ", "", $pd_opt_vals['price']);

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_values
											SET product_option_id = '" . (int)$product_option_id . "',
												product_id = '" . (int)$product_id . "',
												sku = '" . $this->db->escape($pd_opt_vals['sku']) . "',
												quantity = '" . (int)$pd_opt_vals['quantity'] . "',
												subtract = '" . (int)$pd_opt_vals['subtract'] . "',
												price = '" . preformatFloat($pd_opt_vals['price'], $this->language->get('decimal_point')) . "',
												prefix = '" . $this->db->escape($pd_opt_vals['prefix']) . "',
												attribute_value_id = '" . $this->db->escape($pd_opt_vals['attribute_value_id']) . "',
	            								grouped_attribute_data = '" . $this->db->escape($pd_opt_vals['grouped_attribute_data']) . "',
	            								group_id = '" . $this->db->escape($pd_opt_vals['group_id']) . "',
												sort_order = '" . (int)$pd_opt_vals['sort_order'] . "'");

						$pd_opt_val_id = $this->db->getLastId();
						// clone resources of option value
						if ($pd_opt_vals['product_option_value_id']) {
							$resources = $rm->getResourcesList(array(
								'object_name' => 'product_option_value',
								'object_id' => $pd_opt_vals['product_option_value_id']));
							foreach ($resources as $r) {
								$rm->mapResource(
									'product_option_value',
									$pd_opt_val_id,
									$r['resource_id']
								);
							}
						}

						foreach ($pd_opt_vals['language'] as $language_id => $lang_data) {
							$grouped_attribute_names = serialize($lang_data['children_options_names']);

							$this->language->replaceDescriptions('product_option_value_descriptions',
								array('product_option_value_id' => (int)$pd_opt_val_id,
									'product_id' => (int)$product_id),
								array($language_id => array('name' => $lang_data['name'],
									'grouped_attribute_names' => $grouped_attribute_names
								)));
						}
					}
				}
			}
		}
		$this->cache->delete('product');
	}

	/**
	 * @param int $product_id
	 */
	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "products WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_descriptions WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_options WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_descriptions WHERE product_id = '" . (int)$product_id . "'");
		$sql = "SELECT product_option_value_id FROM " . DB_PREFIX . "product_option_values WHERE product_id = '" . (int)$product_id . "'";
		$result = $this->db->query($sql);
		$rm = new AResourceManager();
		foreach ($result->rows as $row) {
			$pd_opt_val_id = $row['product_option_value_id'];
			$resources = $rm->getResourcesList(array(
				'object_name' => 'product_option_value',
				'object_id' => (int)$pd_opt_val_id));
			foreach ($resources as $r) {
				$rm->unmapResource(
					'product_option_value',
					$pd_opt_val_id,
					$r['resource_id']);
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_values WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_descriptions WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discounts WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_downloads WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_categories WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "reviews WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_stores WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_aliases WHERE query = 'product_id=" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tags WHERE product_id='" . (int)$product_id . "'");

		$lm = new ALayoutManager();
		$lm->deletePageLayout('pages/product/product', 'product_id', (int)$product_id);

		$this->cache->delete('product');
	}

	public function deleteProductDiscount($product_discount_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discounts WHERE product_discount_id = '" . (int)$product_discount_id . "'");
		$this->cache->delete('product');
	}

	public function deleteProductSpecial($product_special_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_specials WHERE product_special_id='" . (int)$product_special_id . "'");

		$this->cache->delete('product');
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, p.product_id, COALESCE(pf.product_id, 0) as featured,
										(SELECT keyword
										 FROM " . DB_PREFIX . "url_aliases
										 WHERE query = 'product_id=" . (int)$product_id . "') AS keyword
									FROM " . DB_PREFIX . "products p
									LEFT JOIN " . DB_PREFIX . "products_featured pf ON pf.product_id = p.product_id
									LEFT JOIN " . DB_PREFIX . "product_descriptions pd
											ON (p.product_id = pd.product_id
													AND pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
									WHERE p.product_id = '" . (int)$product_id . "'");

		return $query->row;
	}

	public function getProductDiscount($product_discount_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "product_discounts
									WHERE product_discount_id = '" . (int)$product_discount_id . "'");
		return $query->row;
	}

	public function getProductSpecial($product_special_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "product_specials
									WHERE product_special_id = '" . (int)$product_special_id . "'");
		return $query->row;
	}

	public function setFeatured($product_id, $act = true) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_featured WHERE product_id='" . (int)$product_id . "'");
		if ($act) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "products_featured SET product_id = '" . (int)$product_id . "'");
		}
	}

	public function addFeatured($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_featured");

		if (isset($data['featured_product'])) {
			foreach ($data['featured_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "products_featured SET product_id = '" . (int)$product_id . "'");
			}
		}
	}

	public function getFeaturedProducts() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "products_featured");

		$featured = array();

		foreach ($query->rows as $product) {
			$featured[] = $product['product_id'];
		}
		return $featured;
	}

	public function getProductsByKeyword($keyword) {
		if ($keyword) {
			$query = $this->db->query(
				"SELECT *, p.product_id
				FROM " . DB_PREFIX . "products p
				LEFT JOIN " . DB_PREFIX . "product_descriptions pd
						ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "')
				WHERE (LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'
								OR LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%')");

			return $query->rows;
		} else {
			return array();
		}
	}

	public function getProductsByCategoryId($category_id, $mode = 'default') {
		$query = $this->db->query(
			"SELECT *, p.product_id
			FROM " . DB_PREFIX . "products p
			LEFT JOIN " . DB_PREFIX . "product_descriptions pd
				ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('storefront_language_id') . "' )
			LEFT JOIN " . DB_PREFIX . "products_to_categories p2c ON (p.product_id = p2c.product_id)
			WHERE p2c.category_id = '" . (int)$category_id . "'
			ORDER BY pd.name ASC");
		if ($mode == 'total_only') {
			return $query->num_rows;
		}
		return $query->rows;
	}

	public function getProductDescriptions($product_id, $language_id = 0) {
		$product_description_data = array();
		$language_id = (int)$language_id;

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "product_descriptions
									WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name' => $result['name'],
				'meta_keywords' => $result['meta_keywords'],
				'meta_description' => $result['meta_description'],
				'description' => $result['description']
			);
		}

		return $language_id ? $product_description_data[$language_id] : $product_description_data;
	}

	/**
	 * check attribute before add to product options
	 * cant add attribute that is already in group attribute that assigned to product
	 *
	 * @param $product_id
	 * @param $attribute_id
	 * @return bool
	 */
	public function isProductGroupOption($product_id, $attribute_id) {
		$product_option = $this->db->query(
			"SELECT COUNT(*) as total FROM " . DB_PREFIX . "product_options
			WHERE product_id = '" . (int)$product_id . "'
				AND attribute_id = '" . (int)$attribute_id . "'
				AND group_id != 0
			ORDER BY sort_order");
		return $product_option->row['total'];
	}

	public function getProductOptionByAttributeId($attribute_id, $group_id) {
		$product_option = $this->db->query(
			"SELECT product_option_id FROM " . DB_PREFIX . "product_options
            WHERE attribute_id = '" . (int)$attribute_id . "'
                AND group_id = '" . (int)$group_id . "'
            ORDER BY sort_order");

		return $product_option->row['product_option_id'];
	}

	/*
	*	Get single option data
	*/
	/**
	 * @param int $product_id
	 * @param int $option_id
	 * @return array|null
	 */
	public function getProductOption($product_id, $option_id = 0) {
		$product_option = $this->db->query(
			"SELECT *
			FROM " . DB_PREFIX . "product_options
			WHERE product_id = '" . (int)$product_id . "'
				AND product_option_id = '" . (int)$option_id . "'
			ORDER BY sort_order");

		$product_option_description = $this->db->query(
			"SELECT *
			FROM " . DB_PREFIX . "product_option_descriptions
			WHERE product_option_id = '" . (int)$option_id . "'");

		foreach ($product_option_description->rows as $result) {
			$product_option_description_data[$result['language_id']] = array('name' => $result['name']);
		}

		if ($product_option->num_rows) {
			$row = $product_option->row;
			$row['language'] = $product_option_description_data;
			return $row;
		} else {
			return null;
		}
	}

	public function updateProductOption($product_option_id, $data) {
		$fields = array("sort_order", "status", "required");
		$update = array();
		foreach ($fields as $f) {
			if (isset($data[$f]))
				$update[] = "$f = '" . $this->db->escape($data[$f]) . "'";
		}
		if (!empty($update)) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product_options`
								SET " . implode(',', $update) . "
								WHERE product_option_id = '" . (int)$product_option_id . "'");
		}

		if (!empty($data['name'])) {

			$language_id = $this->session->data['content_language_id'];

			$this->language->replaceDescriptions('product_option_descriptions',
				array('product_option_id' => (int)$product_option_id),
				array((int)$language_id => array('name' => $data['name'])));

		}

		$this->cache->delete('product');
	}

	/*
	* Main method to get complete options data for product
	*/

	public function getProductOptions($product_id, $group_id = 0) {
		$product_option_data = array();

		$group_select = '';
		if (is_int($group_id)) {
			$group_select = "AND group_id = '" . (int)$group_id . "'";
		}

		$product_option = $this->db->query("SELECT *
											 FROM " . DB_PREFIX . "product_options
											 WHERE product_id = '" . (int)$product_id . "' "
				. $group_select .
				" ORDER BY sort_order");

		foreach ($product_option->rows as $product_option) {
			$option_data = array();
			$option_data = $this->getProductOption($product_id, $product_option['product_option_id']);
			$option_data['product_option_value'] = $this->getProductOptionValues($product_id, $product_option['product_option_id']);
			$product_option_data[] = $option_data;
		}

		return $product_option_data;
	}

	/*
	*	Main function to be called to update option values.
	*/
	public function updateProductOptionValues($product_id, $option_id, $data) {
		if (!is_array($data['product_option_value_id']) || !$option_id || !$product_id) {
			return null;
		}
		$language_id = $this->session->data['content_language_id'];

		foreach ($data['product_option_value_id'] as $opt_val_id => $status) {
			$option_value_data = array(
				'attribute_value_id' => $data['attribute_value_id'][$opt_val_id],
				'grouped_attribute_data' => $data['grouped_attribute_data'][$opt_val_id],
				'name' => $data['name'][$opt_val_id],
				'sku' => $data['sku'][$opt_val_id],
				'quantity' => $data['quantity'][$opt_val_id],
				'subtract' => $data['subtract'][$opt_val_id],
				'price' => $data['price'][$opt_val_id],
				'prefix' => $data['prefix'][$opt_val_id],
				'sort_order' => $data['sort_order'][$opt_val_id],
				'weight' => $data['weight'][$opt_val_id],
				'weight_type' => $data['weight_type'][$opt_val_id],
			);

			//Check if new, delete or update
			if ($status == 'delete' && strpos($opt_val_id, 'new') === FALSE) {
				//delete this option value for all languages
				$this->deleteProductOptionValue($product_id, $opt_val_id);
			} else if ($status == 'new') {
				// Need to create new oprion value
				$this->addProductOptionValueAndDescription($product_id, $option_id, $option_value_data);
			} else {
				//Existing need to update
				$this->updateProductOptionValueAndDescription($product_id, $opt_val_id, $option_value_data, $language_id);
			}
		}

		$this->cache->delete('product');

	}

	public function getProductOptionValue($product_id, $option_value_id) {
		$result = array();

		$product_option_value = $this->db->query(
			"SELECT *
            FROM " . DB_PREFIX . "product_option_values
            WHERE product_id = '" . (int)$product_id . "'
                AND product_option_value_id = '" . (int)$option_value_id . "'
                AND group_id = 0
            ORDER BY sort_order");

		$option_value = $product_option_value->row;

		$value_description_data = array();
		$children_description_data = array();
		$value_description = $this->db->query(
			"SELECT *
		    FROM " . DB_PREFIX . "product_option_value_descriptions
		    WHERE product_option_value_id = '" . (int)$option_value['product_option_value_id'] . "'");

		foreach ($value_description->rows as $description) {
			//regular option value name
			$value_description_data[$description['language_id']]['name'] = $description['name'];
			//get children (grouped options) individual names array
			if ($description['grouped_attribute_names']) {
				$value_description_data[$description['language_id']]['children_options_names'] = unserialize($description['grouped_attribute_names']);
			}
		}

		$result = array(
			'product_option_value_id' => $option_value['product_option_value_id'],
			'language' => $value_description_data,
			'sku' => $option_value['sku'],
			'quantity' => $option_value['quantity'],
			'subtract' => $option_value['subtract'],
			'price' => $option_value['price'],
			'prefix' => $option_value['prefix'],
			'weight' => $option_value['weight'],
			'weight_type' => $option_value['weight_type'],
			'attribute_value_id' => $option_value['attribute_value_id'],
			'grouped_attribute_data' => $option_value['grouped_attribute_data'],
			'sort_order' => $option_value['sort_order']
		);

		//get children (grouped options) data
		$child_option_values = unserialize($result['grouped_attribute_data']);
		if (count($child_option_values) && is_array($child_option_values)) {
			$result['children_options'] = array();
			foreach ($child_option_values as $child_value) {
				$result['children_options'][$child_value['attr_id']] = (int)$child_value['attr_v_id'];
			}
		}

		return $result;
	}

	public function getProductOptionValues($product_id, $option_id) {

		$result = array();

		$product_option_value = $this->db->query(
			"SELECT product_option_value_id FROM " . DB_PREFIX . "product_option_values
            WHERE product_id = '" . (int)$product_id . "'
                AND product_option_id = '" . (int)$option_id . "'
            ORDER BY sort_order");

		foreach ($product_option_value->rows as $option_value) {
			$result[] = $this->getProductOptionValue($product_id, $option_value['product_option_value_id']);
		}

		return $result;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "product_discounts
									WHERE product_id = '" . (int)$product_id . "'
									ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "product_specials
									WHERE product_id = '" . (int)$product_id . "'
									ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "products_to_downloads
									WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "products_to_stores
									WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "products_to_categories
									WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "products_related
									WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getProductTags($product_id, $language_id = '') {
		$language_id = (int)$language_id;
		$product_tag_data = array();

		$query = $this->db->query("SELECT *
								   FROM " . DB_PREFIX . "product_tags
								   WHERE product_id = '" . (int)$product_id . "'");

		$tag_data = array();

		foreach ($query->rows as $result) {
			$tag_data[$result['language_id']][] = $result['tag'];
		}

		foreach ($tag_data as $language => $tags) {
			$product_tag_data[$language] = implode(',', $tags);
		}

		if ($language_id) {
			return $product_tag_data[$language_id];
		} else {
			return $product_tag_data;
		}
	}

	public function getProducts($data = array(), $mode = 'default') {

		if (!empty($data['content_language_id'])) {
			$language_id = ( int )$data['content_language_id'];
		} else {
			$language_id = (int)$this->config->get('storefront_language_id');
		}

		if ($data || $mode == 'total_only') {

			$filter = (isset($data['filter']) ? $data['filter'] : array());

			if ($mode == 'total_only') {
				$sql = "SELECT COUNT(*) as total
						FROM " . DB_PREFIX . "products p
						LEFT JOIN " . DB_PREFIX . "product_descriptions pd
							ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')";
			} else {
				$sql = "SELECT *, p.product_id
						FROM " . DB_PREFIX . "products p
						LEFT JOIN " . DB_PREFIX . "product_descriptions pd
							ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')";
			}

			if (isset($filter['category']) && !is_null($filter['category'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "products_to_categories p2c ON (p.product_id = p2c.product_id)";
			}

			$sql .= 'WHERE 1=1 ';

			if (!empty($data['subsql_filter'])) {
				$sql .= " AND " . $data['subsql_filter'];
			}

			if (isset($filter['match']) && !is_null($filter['match'])) {
				$match = $filter['match'];
			}

			if (isset($filter['keyword']) && !is_null($filter['keyword'])) {
				$keywords = explode(' ', $filter['keyword']);

				if ($match == 'any') {
					$sql .= " AND (";
					foreach ($keywords as $k => $keyword) {
						$sql .= $k > 0 ? " OR" : "";
						$sql .= " (LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'";
						$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'";
						$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%')";
					}
					$sql .= " )";
				} else if ($match == 'all') {
					$sql .= " AND (";
					foreach ($keywords as $k => $keyword) {
						$sql .= $k > 0 ? " AND" : "";
						$sql .= " (LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'";
						$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%'";
						$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(mb_strtolower($keyword)) . "%')";
					}
					$sql .= " )";
				} else if ($match == 'exact') {
					$sql .= " AND (LCASE(pd.name) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword'])) . "%'";
					$sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword'])) . "%'";
					$sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(mb_strtolower($filter['keyword'])) . "%')";
				}
			}

			if (isset($filter['pfrom']) && !is_null($filter['pfrom'])) {
				$sql .= " AND p.price >= '" . (float)$filter['pfrom'] . "'";
			}
			if (isset($filter['pto']) && !is_null($filter['pto'])) {
				$sql .= " AND p.price <= '" . (float)$filter['pto'] . "'";
			}
			if (isset($filter['category']) && !is_null($filter['category'])) {
				$sql .= " AND p2c.category_id = '" . (int)$filter['category'] . "'";
			}
			if (isset($filter['status']) && !is_null($filter['status'])) {
				$sql .= " AND p.status = '" . (int)$filter['status'] . "'";
			}

			//If for total, we done bulding the query
			if ($mode == 'total_only') {
				$query = $this->db->query($sql);
				return $query->row['total'];
			}

			$sort_data = array(
				'name' => 'pd.name',
				'model' => 'p.model',
				'quantity' => 'p.quantity',
				'price' => 'p.price',
				'status' => 'p.status',
				'sort_order' => 'p.sort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], array_keys($sort_data))) {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];
			} else {
				$sql .= " ORDER BY pd.name";
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
		} else {
			$product_data = $this->cache->get('product', $language_id);

			if (!$product_data) {
				$query = $this->db->query("SELECT *, p.product_id
											FROM " . DB_PREFIX . "products p
											LEFT JOIN " . DB_PREFIX . "product_descriptions pd
												ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')
											ORDER BY pd.name ASC");
				$product_data = $query->rows;
				$this->cache->set('product', $product_data, $language_id);
			}

			return $product_data;
		}
	}


	public function getTotalProducts($data = array()) {
		return $this->getProducts($data, 'total_only');
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "products
      								WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "products
      								WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "products
      								WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "products
      								WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "product_to_option
      								WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "products_to_downloads
      								WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total
      								FROM " . DB_PREFIX . "products
      								WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}
}
