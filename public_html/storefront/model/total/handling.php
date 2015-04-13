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
class ModelTotalHandling extends Model {
	public function getTotal(&$total_data, &$total, &$taxes, &$cust_data) {

		if ($this->config->get('handling_status')){
			$conf_hndl_subtotal = 0;
			$conf_hndl_tax_id = $this->config->get('handling_tax_class_id');
			$cart_subtotal = $this->cart->getSubTotal();

			if($this->config->get('handling_prefix')=='%'){
				$conf_hndl_fee = $cart_subtotal*(float)$this->config->get('handling_fee')/100.00;
			}else{
				$conf_hndl_fee = (float)$this->config->get('handling_fee');
			}

			$per_payment = unserialize($this->config->get('handling_per_payment'));

			if(is_array($per_payment)){
				$customer_payment = $cust_data['payment_method']['id'];
				foreach($per_payment['handling_payment'] as $i=>$payment_id){
					if($customer_payment==$payment_id){
						if($cart_subtotal<(float)$per_payment['handling_payment_subtotal'][$i]){
							$conf_hndl_subtotal = (float)$per_payment['handling_payment_subtotal'][$i];
							if($per_payment['handling_payment_prefix'][$i]=='%'){
								if((float)$per_payment['handling_payment_fee'][$i]>0){
									$conf_hndl_fee = $cart_subtotal*(float)$per_payment['handling_payment_fee'][$i]/100.00;
								}
							}else{
								$conf_hndl_fee = (float)$per_payment['handling_payment_fee'][$i];
							}
							break;
						}
					}
				}
			}
			// if fee for payment is not set - use default fee
			$conf_hndl_subtotal = !$conf_hndl_subtotal ? (float)$this->config->get('handling_total') : $conf_hndl_subtotal;

			if ($cart_subtotal < $conf_hndl_subtotal && $conf_hndl_fee>0) {

				$this->load->language('total/handling');
				$this->load->model('localisation/currency');
				$total_data[] = array(
					'id'         => 'handling',
					'title'      => $this->language->get('text_handling'),
					'text'       => $this->currency->format($conf_hndl_fee),
					'value'      => $conf_hndl_fee,
					'sort_order' => $this->config->get('handling_sort_order'),
					'total_type' => $this->config->get('handling_fee_total_type')
				);
				if ($conf_hndl_tax_id) {
					if (!isset($taxes[$conf_hndl_tax_id])) {
						$taxes[$conf_hndl_tax_id]['total'] = $conf_hndl_fee;
						$taxes[$conf_hndl_tax_id]['tax'] = $this->tax->calcTotalTaxAmount($conf_hndl_fee, $conf_hndl_tax_id);
					} else {
						$taxes[$conf_hndl_tax_id]['total'] += $conf_hndl_fee;
						$taxes[$conf_hndl_tax_id]['tax'] += $this->tax->calcTotalTaxAmount($conf_hndl_fee, $conf_hndl_tax_id);
					}
				}

				$total += $conf_hndl_fee;
			}
		}
	}
}
