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
class ModelTotalHandling extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->config->get('handling_status') && ($this->cart->getSubTotal() < $this->config->get('handling_total'))) {
			$this->load->language('total/handling');
		 	
			$this->load->model('localisation/currency');
			
			$total_data[] = array( 
        		'id'         => 'handling',
        		'title'      => $this->language->get('text_handling'),
        		'text'       => $this->currency->format($this->config->get('handling_fee')),
        		'value'      => $this->config->get('handling_fee'),
				'sort_order' => $this->config->get('handling_sort_order')
			);

			if ($this->config->get('handling_tax_class_id')) {
				if (!isset($taxes[$this->config->get('handling_tax_class_id')])) {
					$taxes[$this->config->get('handling_tax_class_id')] = $this->config->get('handling_fee') / 100 * $this->tax->getRate($this->config->get('handling_tax_class_id'));
				} else {
					$taxes[$this->config->get('handling_tax_class_id')] += $this->config->get('handling_fee') / 100 * $this->tax->getRate($this->config->get('handling_tax_class_id'));
				}
			}
			
			$total += $this->config->get('handling_fee');
		}
	}
}
?>