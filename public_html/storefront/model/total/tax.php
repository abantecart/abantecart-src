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
class ModelTotalTax extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('tax_status')) {
			foreach ($taxes as $key => $value) {
				if ($value > 0) {
					$tax_classes = $this->tax->getDescription($key);
					
					foreach ($tax_classes as $tax_class) {
						$rate = $this->tax->getRate($key);
						
						$tax = $value * ($tax_class['rate'] / $rate);
						
						$total_data[] = array(
	    					'id'         => 'tax',
	    					'title'      => $tax_class['description'] . ':',
	    					'text'       => $this->currency->format($tax),
	    					'value'      => $tax,
							'sort_order' => $this->config->get('tax_sort_order')
	    				);
			
						$total += $tax;
					}
				}
			}
		}
	}
}
?>