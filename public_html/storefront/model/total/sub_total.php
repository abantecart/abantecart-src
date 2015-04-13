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
class ModelTotalSubTotal extends Model {
	public function getTotal(&$total_data, &$total, &$taxes, &$cust_data) {
		if ($this->config->get('sub_total_status')) {
			$this->load->language('total/sub_total');
			
			$total_data[] = array( 
        		'id'         => 'subtotal',
        		'title'      => $this->language->get('text_sub_total'),
        		'text'       => $this->currency->format($this->cart->getSubTotal()),
        		'value'      => $this->cart->getSubTotal(),
				'sort_order' => $this->config->get('sub_total_sort_order'),
				'total_type' => $this->config->get('sub_total_total_type')
			);

			$total += $this->cart->getSubTotal();
		}
	}
}
