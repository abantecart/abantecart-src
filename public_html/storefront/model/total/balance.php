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

class ModelTotalBalance extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data)
    {
        if ($this->config->get('balance_status')) {
            if ((float)$cust_data['used_balance']) {
                if($cust_data['used_balance_full']){
                    $totalValue = $this->currency->format_number(
                        array_sum(
                            array_column($total_data,'value')
                        )
                    );
                }else{
                    $totalValue = $cust_data['used_balance'];
                    $totalValue = $this->currency->convert(
                        $totalValue,
                        $this->config->get('config_currency'),
                        $this->currency->getCode()
                    );
                }

                //create new instance of language for case when model called from admin-side
                $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
                $language->load($language->language_details['filename']);
                $total_data[] = [
                    'id'         => 'balance',
                    'title'      => $language->get('text_balance_checkout'),
                    'text'       => '-'.$this->currency->format($totalValue, '',1),
                    'value'      => -$cust_data['used_balance'],
                    'sort_order' => 999,
                    'total_type' => 'balance',
                ];
                $total -= $cust_data['used_balance'];
            }
        }
    }
}