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
class ModelCatalogDownload extends Model {
	public function addDownload($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "downloads
      	                  SET remaining = '" . (int)$data[ 'remaining' ] . "', date_added = NOW()");

		$download_id = $this->db->getLastId();

		if (isset($data[ 'download' ])) {
			$this->db->query("UPDATE " . DB_PREFIX . "downloads
        	                  SET filename = '" . $this->db->escape($data[ 'download' ]) . "',
        	                      mask = '" . $this->db->escape($data[ 'mask' ]) . "'
        	                  WHERE download_id = '" . (int)$download_id . "'");
		}

		foreach ($data[ 'download_description' ] as $language_id => $value) {
			$this->language->replaceDescriptions('download_descriptions',
												 array('download_id' => (int)$download_id),
												 array($language_id => array('name' => $value[ 'name' ] )));
		}

		return $download_id;
	}

	public function editDownload($download_id, $data) {
		if (isset($data[ 'remaining' ])) {
			$this->db->query("UPDATE " . DB_PREFIX . "downloads
								SET remaining = '" . (int)$data[ 'remaining' ] . "'
								WHERE download_id = '" . (int)$download_id . "'");
		}

		if (isset($data[ 'update' ])) {
			$query = $this->db->query("SELECT filename from " . DB_PREFIX . "downloads WHERE download_id = '" . (int)$download_id . "'");
			$filename = $query->row[ 'filename' ];
			$this->db->query("UPDATE " . DB_PREFIX . "order_downloads
      		                  SET `filename` = '" . $this->db->escape($data[ 'download' ]) . "',
      		                       mask = '" . $this->db->escape(basename($data[ 'mask' ])) . "'
      		                  WHERE `filename` = '" . $this->db->escape($filename) . "'");
		}

		if (isset($data[ 'download' ])) {
			$this->db->query("UPDATE " . DB_PREFIX . "downloads
        	                 SET filename = '" . $this->db->escape($data[ 'download' ]) . "',
        	                        mask = '" . $this->db->escape($data[ 'mask' ]) . "'
        	                 WHERE download_id = '" . (int)$download_id . "'");
		}

		if (!empty($data[ 'download_description' ])) {
			foreach ($data[ 'download_description' ] as $language_id => $value) {

				$update = array();
				if (isset($value[ 'name' ])){
					$update['name'] = $value[ 'name' ];
				}
				if ($update) {
						$this->language->replaceDescriptions('download_descriptions',
															 array('download_id' => (int)$download_id),
															 array($language_id => $update) );
				}
			}
		}
	}

	public function deleteDownload($download_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "downloads WHERE download_id = '" . (int)$download_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "download_descriptions WHERE download_id = '" . (int)$download_id . "'");
	}

	public function getDownload($download_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "downloads WHERE download_id = '" . (int)$download_id . "'");

		return $query->row;
	}

	public function getDownloads($data = array(), $mode = 'default') {

		if (!empty($data[ 'content_language_id' ])) {
			$language_id = ( int )$data[ 'content_language_id' ];
		} else {
			$language_id = (int)$this->config->get('storefront_language_id');
		}

		if ($mode == 'total_only') {
			$total_sql = 'count(*) as total';
		}
		else {
			$total_sql = '*';
		}

		$sql = "SELECT $total_sql
				FROM " . DB_PREFIX . "downloads d
                LEFT JOIN " . DB_PREFIX . "download_descriptions dd
                	ON (d.download_id = dd.download_id AND dd.language_id = '" . $language_id . "')";

		if (!empty($data[ 'subsql_filter' ]))
			$sql .= " AND " . $data[ 'subsql_filter' ];

		//If for total, we done bulding the query
		if ($mode == 'total_only') {
			$query = $this->db->query($sql);
			return $query->row[ 'total' ];
		}

		$sort_data = array(
			'name' => 'dd.name',
			'remaining' => 'd.remaining',
		);

		if (isset($data[ 'sort' ]) && in_array($data[ 'sort' ], array_keys($sort_data))) {
			$sql .= " ORDER BY " . $data[ 'sort' ];
		} else {
			$sql .= " ORDER BY dd.name";
		}

		if (isset($data[ 'order' ]) && (strtoupper($data[ 'order' ]) == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data[ 'start' ]) || isset($data[ 'limit' ])) {
			if ($data[ 'start' ] < 0) {
				$data[ 'start' ] = 0;
			}

			if ($data[ 'limit' ] < 1) {
				$data[ 'limit' ] = 20;
			}

			$sql .= " LIMIT " . (int)$data[ 'start' ] . "," . (int)$data[ 'limit' ];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalDownloads($data = array()) {
		return $this->getDownloads($data, 'total_only');
	}

	public function getDownloadDescriptions($download_id) {
		$download_description_data = array();

		$query = $this->db->query("SELECT *
									FROM " . DB_PREFIX . "download_descriptions
									WHERE download_id = '" . (int)$download_id . "'");

		foreach ($query->rows as $result) {
			$download_description_data[ $result[ 'language_id' ] ] = array( 'name' => $result[ 'name' ] );
		}

		return $download_description_data;
	}
}

?>