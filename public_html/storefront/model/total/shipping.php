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
class ModelTotalShipping extends Model {
	public function getTotal(&$total_data, &$total, &$taxes, &$cust_data) {
		$ship_data = $cust_data['shipping_method'];
		if ($this->cart->hasShipping() && isset( $ship_data ) && $this->config->get('shipping_status')) {
			$total_data[] = array( 
        		'id'         => 'shipping',
        		'title'      => $ship_data['title'] . ':',
        		'text'       => $this->currency->format($ship_data['cost']),
        		'value'      => $ship_data['cost'],
				'sort_order' => $this->config->get('shipping_sort_order'),
				'total_type' => $this->config->get('shipping_total_type')
			);

			if ($ship_data['tax_class_id']) {
				if (!isset($taxes[$ship_data['tax_class_id']])) {
					$taxes[$ship_data['tax_class_id']]['total'] = $ship_data['cost'];
					$taxes[$ship_data['tax_class_id']]['tax'] = $this->tax->calcTotalTaxAmount($ship_data['cost'], $ship_data['tax_class_id']);
				} else {
					$taxes[$ship_data['tax_class_id']]['total'] += $ship_data['cost'];
					$taxes[$ship_data['tax_class_id']]['tax'] += $this->tax->calcTotalTaxAmount($ship_data['cost'], $ship_data['tax_class_id']);
				}
			}
			
			$total += $ship_data['cost'];
		}			
	}
}
?>