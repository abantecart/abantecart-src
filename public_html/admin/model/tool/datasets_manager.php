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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ModelToolDatasetsManager extends Model {

	/**
	 * @param int $dataset_id
	 * @return array
	 */
	public function getDatasetInfo($dataset_id) {
		$dataset_id = (int)$dataset_id;
		if(!$dataset_id){
			return array();
		}
		$sql = "SELECT dataset_name, dataset_key FROM " . $this->db->table("datasets") . " WHERE dataset_id = ".$dataset_id;
		$result = $this->db->query($sql);
		if(!$result->row['dataset_name']){
			return array();
		}

		$dataset = new ADataset($result->row['dataset_name'], $result->row['dataset_key']);
		$output['dataset_name'] = $result->row['dataset_name'];
		$output['dataset_key'] = $result->row['dataset_key'];
		$output['num_rows'] = sizeof($dataset->getRows());
		$output['dataset_properties'] = $dataset->getDatasetProperties();
		if(!$output['dataset_properties']){
			unset($output['dataset_properties']);
		}
		$cols = $dataset->getColumnDefinitions();
		if($cols){
			foreach($cols as $column){
				$output['dataset_column_definition'][] = array($column['dataset_column_name'] => $column['dataset_column_type']);
			}
		}

		$output['dataset_column_properties'] = $dataset->getColumnsProperties();
		if(!$output['dataset_column_properties']){
			unset($output['dataset_column_properties']);
		}
	return $output;
	}

	public function getDatasets($order_by = '', $limit=10, $offset=0) {
		$limit = (int)$limit ? $limit : 10;
		$offset = (int)$offset ? $offset : 0;
		$order_by = !trim($order_by) ? 'dataset_id' : $order_by;

		$sql = "SELECT *
				FROM ".$this->db->table("datasets") . " 
				ORDER BY ".$this->db->escape($order_by)."
				LIMIT ".$limit." OFFSET ".$offset;
		$result = $this->db->query($sql);
		return $result->rows;
	}

	public function getTotalDatasets( $search = '' ) {
		$sql = "SELECT COUNT(*) as cnt FROM " . $this->db->table("datasets") . " ORDER BY dataset_id";
		$result = $this->db->query($sql);
		return $result->row['cnt'];
	}

}
