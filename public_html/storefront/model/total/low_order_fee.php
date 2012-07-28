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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ModelTotalLowOrderFee extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('low_order_fee_status') && $this->cart->getSubTotal() && ($this->cart->getSubTotal() < $this->config->get('low_order_fee_total'))) {
			$this->load->language('total/low_order_fee');
		 	
			$this->load->model('localisation/currency');
			
			$total_data[] = array( 
        		'id'         => 'low_order_fee',
        		'title'      => $this->language->get('text_low_order_fee'),
        		'text'       => $this->currency->format($this->config->get('low_order_fee_fee')),
        		'value'      => $this->config->get('low_order_fee_fee'),
				'sort_order' => $this->config->get('low_order_fee_sort_order')
			);
			
			if ($this->config->get('low_order_fee_tax_class_id')) {
				if (!isset($taxes[$this->config->get('low_order_fee_tax_class_id')])) {
					$taxes[$this->config->get('low_order_fee_tax_class_id')] = $this->config->get('low_order_fee_fee') / 100 * $this->tax->getRate($this->config->get('low_order_fee_tax_class_id'));
				} else {
					$taxes[$this->config->get('low_order_fee_tax_class_id')] += $this->config->get('low_order_fee_fee') / 100 * $this->tax->getRate($this->config->get('low_order_fee_tax_class_id'));
				}
			}
			
			$total += $this->config->get('low_order_fee_fee');
		}
	}
}
?>