<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

class ModelToolInstallUpgradeHistory extends Model
{
    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getLog($data = [])
    {

        if (!isset($data['sort'])) {
            $data['sort'] = 'date_added';
        }

        if ($data['offset'] < 0) {
            $data['offset'] = 0;
        }

        if ($data['limit'] < 1) {
            $data['limit'] = 10;
        }
        $dataset = new ADataset('install_upgrade_history', 'admin');
        return $dataset->searchRows($data['filter'], $data['sort'], $data['limit'], $data['offset']);
    }

    /**
     * @param array $filter
     *
     * @return int
     * @throws AException
     */
    public function getTotalRows($filter = [])
    {
        if (!$filter['column_name']) {
            $filter['column_name'] = 'name';
            $filter['operator'] = 'like';
        }

        $dataset = new ADataset('install_upgrade_history', 'admin');
        return $dataset->getTotalRows($filter);
    }

    /**
     * @return bool
     * @throws AException
     */
    public function deleteData()
    {
        $dataset = new ADataset('install_upgrade_history', 'admin');
        return $dataset->deleteData();
    }
}