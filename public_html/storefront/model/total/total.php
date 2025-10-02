<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ModelTotalTotal extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data)
    {
        if ($this->config->get('total_status')) {
            //create a new instance of language for a case when model called from admin-side
            $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
            $language->load($language->language_details['directory']);
            $language->load('total/total');

            //currency-based recalculation for all totals
            $totalValue = $convertedSum = 0;
            $currencyCode = $this->currency->getCode();
            $defaultCurrencyCode = $this->config->get('config_currency');
            foreach ($total_data as $total_record) {
                $totalValue += (float)$total_record['value'];
                if($currencyCode == $defaultCurrencyCode) {
                    $convertedSum = $totalValue;
                }else {
                    $convertedSum += preformatFloat($total_record['text'], $language->get('decimal_point'));
                }
            }
            $convertedSumText = $this->currency->format(max(0, $convertedSum), '', 1);
            $total_data[] = [
                'id'         => 'total',
                'title'      => $language->get('text_total'),
                'text'       => $convertedSumText,
                'converted'  => $convertedSum,
                'value'      => max(0, $totalValue),
                'sort_order' => 1000,
                'total_type' => $this->config->get('total_total_type'),
            ];
        }
    }
}