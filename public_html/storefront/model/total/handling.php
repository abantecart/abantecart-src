<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelTotalHandling extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data)
    {
        $subTotal = 0;
        if ($this->config->get('handling_status') && $total_data) {
            $subTotalThreshold = 0;
            $feeTaxId = $this->config->get('handling_tax_class_id');

            foreach($total_data as $t){
                if($t['id'] == 'subtotal'){
                    $subTotal = $t['value'];
                    break;
                }
            }
            $subTotal = $subTotal ?: $total;

            if ($this->config->get('handling_prefix') == '%') {
                $fee = $subTotal * (float) $this->config->get('handling_fee') / 100.00;
            } else {
                $fee = (float) $this->config->get('handling_fee');
            }

            $feePerPayment = unserialize($this->config->get('handling_per_payment'));

            if (is_array($feePerPayment)) {
                $selectedPayment = $cust_data['payment_method']['id'] ?? '';
                foreach ($feePerPayment['handling_payment'] as $i => $paymentId) {
                    if ($selectedPayment == $paymentId) {
                        if ($subTotal > (float) $feePerPayment['handling_payment_subtotal'][$i]) {
                            $subTotalThreshold = (float) $feePerPayment['handling_payment_subtotal'][$i];
                            if ($feePerPayment['handling_payment_prefix'][$i] == '%') {
                                if ((float) $feePerPayment['handling_payment_fee'][$i] > 0) {
                                    $fee = $subTotal * (float) $feePerPayment['handling_payment_fee'][$i]/ 100.00;
                                }
                            } else {
                                $fee = (float) $feePerPayment['handling_payment_fee'][$i];
                            }
                            break;
                        }
                    }
                }
            }
            // if fee for payment is not set - use default fee
            $subTotalThreshold = $subTotalThreshold ?: (float) $this->config->get('handling_total');

            if ($subTotal > $subTotalThreshold && $fee > 0) {
                //create new instance of language for case when model called from admin-side
                $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
                $language->load($language->language_details['directory']);
                $language->load('total/handling');
                $this->load->model('localisation/currency');
                $total_data[] = [
                    'id'         => 'handling',
                    'title'      => $language->get('text_handling'),
                    'text'       => $this->currency->format($fee),
                    'value'      => $fee,
                    'sort_order' => $this->config->get('handling_sort_order'),
                    'total_type' => $this->config->get('handling_fee_total_type'),
                ];
                if ($feeTaxId) {
                    if (!isset($taxes[$feeTaxId])) {
                        $taxes[$feeTaxId]['total'] = $fee;
                        $taxes[$feeTaxId]['tax'] =
                            $this->tax->calcTotalTaxAmount($fee, $feeTaxId);
                    } else {
                        $taxes[$feeTaxId]['total'] += $fee;
                        $taxes[$feeTaxId]['tax'] += $this->tax->calcTotalTaxAmount(
                            $fee, $feeTaxId
                        );
                    }
                }
                $total += $fee;
            }
        }
    }
}
