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
class ModelToolInstallUpgradeHistory extends Model {


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
		$dataset = new ADataset('install_upgrade_history', 'admin');
		$rows = $dataset->getRows(array(), $data[ 'sort' ], $data[ 'limit' ], $data[ 'offset' ]);


		return $rows;
	}

	public function getTotalRows($filter = array()) {

		if ($filter) {
			$filter[ 'column_name' ] = 'name';
			$filter[ 'operator' ] = 'like';

		}

		$dataset = new ADataset('install_upgrade_history', 'admin');
		$rows = $dataset->getTotalRows($filter);
		return $rows;
	}
}

?>