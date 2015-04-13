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
class ModelTotalBalance extends Model {
	public function getTotal(&$total_data, &$total, &$taxes, &$cust_data) {
		if ($this->config->get('balance_status')) {
			if((float)$cust_data['used_balance']){
				$total_data[] = array(
					'id'         => 'balance',
					'title'      => $this->language->get('text_balance_checkout'),
					'text'       => '-'.$this->currency->format($cust_data['used_balance']),
					'value'      => - $this->session->data['used_balance'],
					'sort_order' => 999,
					'total_type' => 'balance'
				);
				$total -= $cust_data['used_balance'];
			}
		}
	}
}