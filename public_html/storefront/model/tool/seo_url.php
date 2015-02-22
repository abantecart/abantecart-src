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
class ModelToolSeoUrl extends Model {
	public function rewrite($link) {
		if ($this->config->get('enable_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));
		
			$url = '';
			$data = array();
			parse_str($url_data['query'], $data);
			
			foreach ($data as $key => $value) {
				if (($key == 'product_id') || ($key == 'manufacturer_id') || ($key == 'content_id')) {
					$query = $this->db->query("SELECT *
											   FROM " . DB_PREFIX . "url_aliases
											   WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'
											   	AND language_id='".(int)$this->config->get('storefront_language_id')."'");
				
					if ($query->num_rows) {
						$url .= '/' . $query->row['keyword'];
						unset($data[$key]);
					}					
				} elseif ($key == 'path' || $key == 'category_id') {
						if($key == 'path'){
							$value = explode('_',$value);
							end($value);
							$value = current($value);
						}

						$sql = "SELECT *
								FROM " . DB_PREFIX . "url_aliases
								WHERE `query` = 'category_id=" . $this->db->escape($value) . "'
									AND language_id='".(int)$this->config->get('storefront_language_id')."'";
						
						$query = $this->db->query($sql);
						if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
						}					

					
					unset($data[$key]);
				}
			}
		
			if ($url) {
				unset($data['rt']);
			
				$query = '';
			
				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}
					
					if ($query) {
						$query = '?' . trim($query, '&');
					}
				}

				return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;
			} else {
				return $link;
			}
		} else {
			return $link;
		}		
	}
}
?>