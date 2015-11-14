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

/**
 * Class ModelLocalisationCurrency
 */
class ModelLocalisationCurrency extends Model {
	/**
	 * @param array $data
	 * @return int
	 */
	public function addCurrency($data) {
		$this->db->query("INSERT INTO " . $this->db->table("currencies") . " 
		                    (`title`,
		                     `code`,
		                     `symbol_left`,
		                     `symbol_right`,
		                     `decimal_place`,
		                     `value`,
		                     `status`,
		                     `date_modified`)
						  VALUES ('" . $this->db->escape($data[ 'title' ]) . "',
						          '" . $this->db->escape($data[ 'code' ]) . "',
						          '" . $this->db->escape($data[ 'symbol_left' ]) . "',
						          '" . $this->db->escape($data[ 'symbol_right' ]) . "',
						          '" . $this->db->escape($data[ 'decimal_place' ]) . "',
						          '" . $this->db->escape($data[ 'value' ]) . "',
						          '" . (int)$data[ 'status' ] . "',
						          NOW())");

		$this->cache->delete('currency');
		return $this->db->getLastId();
	}

	/**
	 * @param int $currency_id
	 * @param $data
	 * @return bool
	 */
	public function editCurrency($currency_id, $data) {
		// prevent disabling the only enabled currency in cart
		if (isset($data[ 'status' ]) && !$data[ 'status' ]) {
			$enabled = array();
			$all = $this->getCurrencies();
			foreach ($all as $c) {
				if ($c[ 'status' ] && $c[ 'currency_id' ] != $currency_id) {
					$enabled[ ] = $c;
				}
			}
			if (!$enabled) {
				return false;
			}
		}

		$fields = array( 'title', 'code', 'symbol_left', 'symbol_right', 'decimal_place', 'value', 'status', );
		$update = array( 'date_modified = NOW()' );
		foreach ($fields as $f) {
			if (isset($data[ $f ]))
				$update[ ] = $f." = '" . $this->db->escape($data[ $f ]) . "'";
		}
		if (!empty($update)) {
			$this->db->query("UPDATE " . $this->db->table("currencies") . " 
							  SET " . implode(',', $update) . "
							  WHERE currency_id = '" . (int)$currency_id . "'");
			$this->cache->delete('currency');
		}
		return true;
	}

	/**
	 * @param int $currency_id
	 * @return bool
	 */
	public function deleteCurrency($currency_id) {
		// prevent deleting all currencies
		if ($this->getTotalCurrencies() < 2) {
			return false;
		}
		$this->db->query("DELETE FROM " . $this->db->table("currencies") . " 
						  WHERE currency_id = '" . (int)$currency_id . "'");
		$this->cache->delete('currency');
		return true;
	}

	/**
	 * @param int $currency_id
	 * @return array
	 */
	public function getCurrency($currency_id) {
		$query = $this->db->query("SELECT DISTINCT *
								   FROM " . $this->db->table("currencies") . " 
								   WHERE currency_id = '" . (int)$currency_id . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function getCurrencies($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . $this->db->table("currencies") . " ";

			$sort_data = array(
				'title',
				'code',
				'value',
				'status',
				'date_modified'
			);

			if (isset($data[ 'sort' ]) && in_array($data[ 'sort' ], $sort_data)) {
				$sql .= " ORDER BY " . $data[ 'sort' ];
			} else {
				$sql .= " ORDER BY title";
			}

			if (isset($data[ 'order' ]) && ($data[ 'order' ] == 'DESC')) {
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
		} else {
			$currency_data = $this->cache->get('currency');

			if (!$currency_data) {
				$query = $this->db->query("SELECT *
											FROM " . $this->db->table("currencies") . " 
											ORDER BY title ASC");

				foreach ($query->rows as $result) {
					$currency_data[ $result[ 'code' ] ] = array(
						'currency_id' => $result[ 'currency_id' ],
						'title' => $result[ 'title' ],
						'code' => $result[ 'code' ],
						'symbol_left' => $result[ 'symbol_left' ],
						'symbol_right' => $result[ 'symbol_right' ],
						'decimal_place' => $result[ 'decimal_place' ],
						'value' => $result[ 'value' ],
						'status' => $result[ 'status' ],
						'date_modified' => $result[ 'date_modified' ]
					);
				}

				$this->cache->set('currency', $currency_data);
			}

			return $currency_data;
		}
	}

	/**
	 * @throws AException
	 */
	public function updateCurrencies() {
		if (extension_loaded('curl')) {
			$data = array();

			$query = $this->db->query("SELECT *
									   FROM " . $this->db->table("currencies") . " 
									   WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "'
									        AND date_modified > '" . date(strtotime('-1 day')) . "'");

			foreach ($query->rows as $result) {
				$data[ ] = $this->config->get('config_currency') . $result[ 'code' ] . '=X';
			}

			$url = 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv';
			$connect = new AConnect(true);
			$content = $connect->getData($url);
			$lines = explode("\n", trim($content));

			foreach ($lines as $line) {
				$currency = substr($line, 4, 3);
				$value = substr($line, 11, 6);

				if ((float)$value) {
					$sql = "UPDATE " . $this->db->table("currencies") . " 
									  SET value = '" . (float)$value . "', date_modified = NOW()
									  WHERE code = '" . $this->db->escape($currency) . "'";
					$this->db->query($sql);
				}
			}
			$sql = "UPDATE " . $this->db->table("currencies") . " 
							  SET value = '1.00000',
							      date_modified = NOW()
							  WHERE code = '" . $this->db->escape($this->config->get('config_currency')) . "'";
			$this->db->query($sql);
			$this->cache->delete('currency');
		}
	}

	/**
	 * @return int
	 */
	public function getTotalCurrencies() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->db->table("currencies") . " ");
		return $query->row[ 'total' ];
	}
}
