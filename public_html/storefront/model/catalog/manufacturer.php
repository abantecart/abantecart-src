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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ModelCatalogManufacturer extends Model {
	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "manufacturers m
									LEFT JOIN " . DB_PREFIX . "manufacturers_to_stores m2s ON (m.manufacturer_id = m2s.manufacturer_id)
									WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "'
										AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
	
		return $query->row;	
	}
	
	public function getManufacturers() {
		$manufacturer = $this->cache->get( 'manufacturer','', (int)$this->config->get('config_store_id') );
		if (!$manufacturer) {
			$query = $this->db->query( "SELECT *
										FROM " . DB_PREFIX . "manufacturers m
										LEFT JOIN " . DB_PREFIX . "manufacturers_to_stores m2s ON (m.manufacturer_id = m2s.manufacturer_id)
										WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
										ORDER BY sort_order, LCASE(m.name) ASC");
			$manufacturer = $query->rows;
			$this->cache->set('manufacturer', $manufacturer, '', (int)$this->config->get('config_store_id'));
		}
		return $manufacturer;
	}
	public function getManufacturerByProductId($product_id) {
			$query = $this->db->query( "SELECT *
										FROM " . DB_PREFIX . "manufacturers m
										RIGHT JOIN " . DB_PREFIX . "products p ON (m.manufacturer_id = p.manufacturer_id)
										WHERE p.product_id = '" . (int)$product_id . "'");
		return $query->rows;
	}
}
?>