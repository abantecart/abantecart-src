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
class ModelTotalLowOrderFee extends Model {
	public function getTotal(&$total_data, &$total, &$taxes, &$cust_data) {
		$conf_tax_id = $this->config->get('low_order_fee_tax_class_id');
		$conf_low_fee = $this->config->get('low_order_fee_fee');
	
		if ($this->config->get('low_order_fee_status') && $this->cart->getSubTotal() && ($this->cart->getSubTotal() < $this->config->get('low_order_fee_total'))) {
			$this->load->language('total/low_order_fee');
		 	
			$this->load->model('localisation/currency');
			
			$total_data[] = array( 
        		'id'         => 'low_order_fee',
        		'title'      => $this->language->get('text_low_order_fee'),
        		'text'       => $this->currency->format($conf_low_fee),
        		'value'      => $conf_low_fee,
				'sort_order' => $this->config->get('low_order_fee_sort_order'),
				'total_type' => $this->config->get('low_order_fee_total_type')
			);
			
			if ($conf_tax_id) {
				if (!isset($taxes[$conf_tax_id])) {
					$taxes[$conf_tax_id]['total'] = $conf_low_fee;
					$taxes[$conf_tax_id]['tax'] = $this->tax->calcTotalTaxAmount($conf_low_fee, $conf_tax_id);					
				} else {
					$taxes[$conf_tax_id]['total'] += $conf_low_fee;
					$taxes[$conf_tax_id]['tax'] += $this->tax->calcTotalTaxAmount($conf_low_fee, $conf_tax_id);					
				}
			}

			$total += $conf_low_fee;
		}
	}
}
?>