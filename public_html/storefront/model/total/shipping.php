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
class ModelTotalShipping extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method']) && $this->config->get('shipping_status')) {
			$total_data[] = array( 
        		'id'         => 'shipping',
        		'title'      => $this->session->data['shipping_method']['title'] . ':',
        		'text'       => $this->currency->format($this->session->data['shipping_method']['cost']),
        		'value'      => $this->session->data['shipping_method']['cost'],
				'sort_order' => $this->config->get('shipping_sort_order')
			);

			if ($this->session->data['shipping_method']['tax_class_id']) {
				if (!isset($taxes[$this->session->data['shipping_method']['tax_class_id']])) {
					$taxes[$this->session->data['shipping_method']['tax_class_id']] = $this->session->data['shipping_method']['cost'] / 100 * $this->tax->getRate($this->session->data['shipping_method']['tax_class_id']);
				} else {
					$taxes[$this->session->data['shipping_method']['tax_class_id']] += $this->session->data['shipping_method']['cost'] / 100 * $this->tax->getRate($this->session->data['shipping_method']['tax_class_id']);
				}
			}
			
			$total += $this->session->data['shipping_method']['cost'];
		}			
	}
}
?>