<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelTotalTax extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data)
    {
        //check if we have customer object 
        $istax_exempt = false;
        if (is_object($this->customer)) {
            $istax_exempt = $this->customer->isTaxExempt();
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $istax_exempt = $cust_data['tax_exempt'];
            $customer_group_id = $cust_data['customer_group_id'];
        }

        if ($istax_exempt) {
            //customer is tax exempt, do nothing
            return;
        }
        if ($this->config->get('tax_status')) {
            foreach ($taxes as $tax_class_id => $subtax) {
                if (!empty($subtax)) {
                    $tax_classes = $this->tax->getDescription($tax_class_id);
                    foreach ($tax_classes as $tax_class) {
                        $tax_amount = 0;
                        //check if we have exempt group for this class 
                        if (is_array($tax_class['tax_exempt_groups']) && count($tax_class['tax_exempt_groups']) > 0) {
                            if (in_array($customer_group_id, $tax_class['tax_exempt_groups'])) {
                                //we found taxt exempt. 
                                continue;
                            }
                        }
                        //This is the same as $subtax['tax'], but we will recalculate
                        $tax_amount = $this->tax->calcTaxAmount($subtax['total'], $tax_class);
                        //round base currency tax amount to 2 decimal place
                        $decimal_place = 2;
                        $tax_amount = round($tax_amount, $decimal_place);
                        if ($tax_amount > 0) {
                            $sort_order = $this->config->get('tax_sort_order');
                            if (is_numeric($tax_class['priority'])) {
                                $sort_order = $sort_order.'.'.$tax_class['priority'];
                            }

                            $total_data[] = array(
                                'id'         => 'tax',
                                'title'      => $tax_class['description'].':',
                                'text'       => $this->currency->format($tax_amount),
                                'value'      => $tax_amount,
                                'sort_order' => $sort_order,
                                'total_type' => $this->config->get('tax_total_type'),
                            );
                        }
                        $total += $tax_amount;
                    }
                }
            }
        }
    }
}

?>