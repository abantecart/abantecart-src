<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

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

class ModelSettingStore extends Model {
	public function addStore($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "stores
      	                    SET name = '" . $this->db->escape($data['config_name']) . "',
								url = '" . $this->db->escape($data['config_url']) . "',
								`ssl` = '" . (int)$data['config_ssl'] . "'");
		
		$store_id = $this->db->getLastId();

		foreach ($data['store_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "store_descriptions
							SET store_id = '" . (int)$store_id . "',
								language_id = '" . (int)$language_id . "',
								description = '" . $this->db->escape($value['description']) . "'");
		}
		unset($data['store_description']);
		$this->load->model('setting/setting');
		foreach($data as $key=>$value){
			$this->model_setting_setting->editSetting('custom_store',array($key=>$value),$store_id);
		}
		$this->cache->delete('store');
        // add settings of extension of default store to new store settings
        $extension_list = $this->extensions->getEnabledExtensions();
        $sql = "INSERT INTO " . DB_PREFIX . "settings (store_id, `group`, `key`, `value`)
                SELECT '".$store_id."' as store_id, `group`, `key`, `value`
                FROM " . DB_PREFIX . "settings
                WHERE `group` in ('".implode("' ,'",$extension_list)."') AND store_id='0';";
		$this->db->query( $sql );
		return $store_id;
	}
	
	public function editStore($store_id, $data) {

		$fields = array('config_name',
		                'config_url',
		                'config_title',
		                'config_meta_description',
		                'config_storefront_template',
		                'config_country_id',
		                'config_zone_id',
		                'config_storefront_language',
		                'config_currency',
		                'config_tax',
						'config_tax_store',
		                'config_tax_customer',
		                'config_customer_group_id',
		                'config_customer_price',
		                'config_customer_approval',
		                'config_guest_checkout',
		                'config_account_id',
		                'config_checkout_id',
		                'config_stock_display',
		                'config_stock_checkout',
		                'config_catalog_limit',
		                'config_cart_weight',
		                'config_order_status_id',
		                'config_logo',
		                'config_icon',
		                'config_image_thumb_width',
		                'config_image_thumb_height',
		                'config_image_popup_width',
		                'config_image_popup_height',
		                'config_image_category_width',
		                'config_image_category_height',
		                'config_image_product_width',
		                'config_image_product_height',
		                'config_image_additional_width',
		                'config_image_additional_height',
		                'config_image_related_width',
		                'config_image_related_height',
		                'config_image_cart_width',
		                'config_image_cart_height',
		                'config_ssl');

		if ( !empty($data['store_description']) ) {
			foreach ($data['store_description'] as $language_id => $value) {
				
				if ( isset($value['description']) ){
					$exist = $this->db->query( "SELECT *
												FROM " . DB_PREFIX . "store_descriptions
										        WHERE store_id = '" . (int)$store_id . "' AND language_id = '" . (int)$language_id . "' ");
					if($exist->num_rows){
						$this->db->query("UPDATE " . DB_PREFIX . "store_descriptions
										  SET  `description`='" . $this->db->escape($value['description']) . "'
										  WHERE store_id = '" . (int)$store_id . "' AND language_id = '" . (int)$language_id . "' ");
					}else{
						$this->db->query("INSERT INTO " . DB_PREFIX . "store_descriptions
										  (`store_id`, `language_id`, `description`)
										  VALUES ( '" . (int)$store_id . "', '" . (int)$language_id . "', '" . $this->db->escape($value['description']) . "')");
					}
				}
			}
		}
		unset($data['store_description']);

        //TODO: check why we duplicate these 3 variables in stores table?
        //store check by url done via stores table, not settings table
        if ( !empty($data['config_name']) || !empty($data['config_url']) || !empty($data['config_ssl']) ){
            if ( !empty($data['config_name']) ){
                $this->db->query(
                    "UPDATE " . DB_PREFIX . "stores
                	SET  `name`='" . $this->db->escape($data['config_name']) . "'
                	WHERE store_id = '" . (int)$store_id . "' ");
            }
            if ( !empty($data['config_url']) ){
                $this->db->query(
                    "UPDATE " . DB_PREFIX . "stores
                	SET  `url`='" . $this->db->escape($data['config_url']) . "'
                	WHERE store_id = '" . (int)$store_id . "' ");
            }
            if ( !empty($data['config_ssl']) ){
                $this->db->query(
                    "UPDATE " . DB_PREFIX . "stores
                	SET  `ssl`='" . $this->db->escape($data['config_ssl']) . "'
                	WHERE store_id = '" . (int)$store_id . "' ");
            }
        }

		$this->load->model('setting/setting');
		foreach($data as $key=>$value){
			if(!in_array($key,$fields)){ continue; }
			$this->model_setting_setting->editSetting('custom_store',array($key=>$value),$store_id);
		}
		$this->cache->delete('store');
	}
	
	public function deleteStore($store_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "stores WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "settings WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "store_descriptions WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "categories_to_stores WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "products_to_stores WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "contents_to_stores WHERE store_id = '" . (int)$store_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturers_to_stores WHERE store_id = '" . (int)$store_id . "'");
	
		$this->cache->delete('store');
	}	
	
	public function getStore($store_id) {
		$output = array();
		$query = $this->db->query("SELECT DISTINCT *
								   FROM " . DB_PREFIX . "stores st
								   LEFT JOIN " . DB_PREFIX . "settings se ON se.store_id = st.store_id
								   WHERE st.store_id = '" . (int)$store_id . "'");
		if($query->num_rows){
			foreach($query->rows as $row){
				$output[$row['key']] = $row['value'];
			}
		}
		return $output;
	}
	
	public function getStoreDescriptions($store_id) {
		$store_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store_descriptions WHERE store_id = '" . (int)$store_id . "'");
		
		foreach ($query->rows as $result) {
			$store_description_data[$result['language_id']] = array('description' => $result['description']);
		}
		
		return $store_description_data;
	}
	
	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');
	
		if (!isset($store_data)) {
			$query = $this->db->query("SELECT *
										FROM " . DB_PREFIX . "stores
										ORDER BY url");

			$store_data = $query->rows;
		
			$this->cache->set('store', $store_data);
		}
	 
		return $store_data;
	}

	public function getTotalStores() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "stores");
		
		return $query->row['total'];
	}	

	public function getTotalStoresByLanguage($language) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . DB_PREFIX . "settings
      	                            WHERE `key` = 'config_storefront_language' AND  `value` = '" . $this->db->escape($language) . "'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByCurrency($currency) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . DB_PREFIX . "settings
      	                            WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByCountryId($country_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . DB_PREFIX . "settings
      	                            WHERE `key` = 'config_country_id' AND  `value` = '" . (int)$country_id . "'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByZoneId($zone_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . DB_PREFIX . "settings
      	                            WHERE `key` = 'config_zone_id' AND  `value` = '" . (int)$zone_id . "'");
		
		return $query->row['total'];		
	}
	
	public function getTotalStoresByCustomerGroupId($customer_group_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . DB_PREFIX . "settings
      	                            WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "'");
		
		return $query->row['total'];		
	}	
	
	public function getTotalStoresByInformationId($information_id) {
      	$account_query = $this->db->query("SELECT COUNT(*) AS total
      	                                    FROM " . DB_PREFIX . "settings
      	                                    WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "'");
      	
		$checkout_query = $this->db->query("SELECT COUNT(*) AS total
											FROM " . DB_PREFIX . "settings
											WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "'");
		
		return ($account_query->row['total'] + $checkout_query->row['total']);
	}
	
	public function getTotalStoresByOrderStatusId($order_status_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total
      	                            FROM " . DB_PREFIX . "settings
      	                            WHERE `key` = 'config_order_status_id' AND `value` = '" . (int)$order_status_id . "'");
		
		return $query->row['total'];		
	}	
}
?>