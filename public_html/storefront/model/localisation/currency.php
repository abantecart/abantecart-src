<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2016 Belavier Commerce LLC

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
class ModelLocalisationCurrency extends Model {
	public function getCurrencies() {
		$currency_data = $this->cache->pull('localization.currency');

		if ($currency_data === false) {
			$query = $this->db->query("SELECT * FROM " . $this->db->table("currencies") . " ORDER BY title ASC");
	
			foreach ($query->rows as $result) {
      			$currency_data[$result['code']] = array(
        			'currency_id'   => $result['currency_id'],
        			'title'         => $result['title'],
        			'code'          => $result['code'],
					'symbol_left'   => $result['symbol_left'],
					'symbol_right'  => $result['symbol_right'],
					'decimal_place' => $result['decimal_place'],
					'value'         => $result['value'],
					'status'        => $result['status'],
					'date_modified' => $result['date_modified']
      			);
    		}	

			$this->cache->push('localization.currency', $currency_data);
		}

		return $currency_data;	
	}	

}