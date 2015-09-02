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
 * Class ModelToolFileUploads
 */
class ModelToolFileUploads extends Model {

	/**
	 * @param array $data
	 * @return array
	 */
	public function getLog($data = array()) {

		if (!isset($data[ 'sort' ])) {
			$data[ 'sort' ] = 'date_added';
		}

		if ($data[ 'offset' ] < 0) {
			$data[ 'offset' ] = 0;
		}

		if ($data[ 'limit' ] < 1) {
			$data[ 'limit' ] = 10;
		}
		$dataset = new ADataset('file_uploads', 'admin');
		$rows = $dataset->getRows(array(), $data[ 'sort' ], $data[ 'limit' ], $data[ 'offset' ]);

		return $rows;
	}

	/**
	 * @param array $filter
	 * @return int
	 */
	public function getTotalRows($filter = array()) {

		if ($filter) {
			$filter[ 'column_name' ] = 'name';
			$filter[ 'operator' ] = 'like';

		}

		$dataset = new ADataset('file_uploads', 'admin');
		$rows = $dataset->getTotalRows($filter);
		return $rows;
	}

	/**
	 * @param int $field_id
	 * @return array
	 */
	public function getField($field_id) {
		$result = $this->db->query('SELECT * FROM ' . $this->db->table('fields') . ' WHERE field_id =	"' . (int)$field_id . '"');

		if ( $result->num_rows ) {

			if ( has_value($result->row['settings']) ) {
				$result->row['settings'] = unserialize($result->row['settings']);
			}

			return $result->row;
		}
		return array();
	}
}
