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

class ModelTotalTotal extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$cust_data)
    {
        if ($this->config->get('total_status')) {
            //create new instance of language for case when model called from admin-side
            $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
            $language->load($language->language_details['directory']);
            $language->load('total/total');
            $this->load->model('localisation/currency');

            //currency based recalculation for all totals
            $displaySum = array_sum(
                array_map(
                    function($value){
                        return $this->currency->format_number($value);
                    },
                    array_column($total_data,'value')
                )
            );

            $value = max(0, $total);
            $displaySum = $value ? max(0, $displaySum) : 0.00;

            $total_data[] = [
                'id'         => 'total',
                'title'      => $language->get('text_total'),
                'text'       => $this->currency->format($displaySum, '', 1),
                //this is rounded sum in selected currency
                'converted'  => $displaySum,
                //this is rounded sum in default currency
                'value'      => $value,
                'sort_order' => 1000,
                'total_type' => $this->config->get('total_total_type'),
            ];
        }
    }
}