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

class ModelTotalSubTotal extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data)
    {
        if ($this->config->get('sub_total_status')) {
            //create new instance of language for case when model called from admin-side
            $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
            $language->load($language->language_details['directory']);
            $language->load('total/sub_total');

            //currency based recalculation for all products to avoid fractional loss
            $subtotal = $taxAmount = $subTotalWithTax = $convertedSubTotal = $convertedTaxAmount = $convertedSubTotalWithTax = 0;
            $products = $this->cart->getProducts() + $this->cart->getVirtualProducts();
            $decPlace = (int)$this->currency->getCurrency()['decimal_place'];

            foreach ($products as $product) {
                $price = $product['price'] ?: $product['amount'];
                $convertedPrice = round($this->currency->convert(
                    $price,
                    $this->config->get('config_currency'),
                    $this->currency->getCode()
                ),$decPlace);
                $subtotal += ($price * $product['quantity']);
                $convertedSubTotalWithTax += $product['quantity']
                    *
                    round(
                        $this->tax->calculate( $convertedPrice, $product['tax_class_id'] )
                        ,$decPlace
                    );

                $subTotalWithTax += $product['quantity']
                    *
                    round(
                        $this->tax->calculate( $price, $product['tax_class_id']),
                        $decPlace
                    );

                $taxAmount += $product['quantity'] * $this->tax->calcTotalTaxAmount($price, $product['tax_class_id']);

                $convertedTaxAmount += $product['quantity']
                    *
                    round(
                        $this->tax->calcTotalTaxAmount($convertedPrice, $product['tax_class_id'] ),
                        $decPlace
                    );
            }

            if($this->config->get('config_tax')) {
                $subtotal = max(
                    ($subTotalWithTax - $taxAmount),
                    $subtotal
                );
            }

            //currency display value
            //in case when current currency is not default - get text based on
            if($this->config->get('config_currency') != $this->currency->getCode()){
                $subtotal = max(
                    $subtotal,
                    $this->currency->convert(
                        ($convertedSubTotalWithTax - $convertedTaxAmount),
                        $this->currency->getCode(),
                        $this->config->get('config_currency')
                    )
                );

                $converted_sum_txt = $this->currency->format(
                    ($convertedSubTotalWithTax-$convertedTaxAmount),
                    $this->currency->getCode(),
                    1
                );
            }else {
                //if current currency is default - just format total amount without conversion
                $converted_sum_txt = $this->currency->format(
                    max(0, $subtotal),
                    '',
                    1
                );
            }

            $total_data[] = [
                'id'         => 'subtotal',
                'title'      => $language->get('text_sub_total'),
                'text'       => $converted_sum_txt,
                'converted'  => $convertedSubTotal,
                'value'      => $subtotal,
                'sort_order' => $this->config->get('sub_total_sort_order'),
                'total_type' => $this->config->get('sub_total_total_type'),
            ];
            $total += $this->cart->getSubTotal();
        }
    }
}
