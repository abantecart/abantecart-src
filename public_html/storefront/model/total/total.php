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
            $converted_sum = 0;
            foreach ($total_data as $total_record) {
                $converted_sum += $this->currency->format_number($total_record['value']);
            }
            //if there is a conversion fractional loss, adjust total base currency price. 
            //This is not ideal solution, need to address in the future. 
            $converted_total = $this->currency->format_number($total);
            if ($converted_total != $converted_sum) {
                $curr = $this->currency->getCurrency();
                //calculate adjusted total without rounding
                $total = $converted_sum / $curr['value'];
            }

            //currency display value
            $converted_sum_txt = $this->currency->format(max(0, $converted_sum), '', 1);

            $total_data[] = array(
                'id'         => 'total',
                'title'      => $language->get('text_total'),
                'text'       => $converted_sum_txt,
                'converted'  => $converted_sum,
                'value'      => max(0, $total),
                'sort_order' => 1000,
                'total_type' => $this->config->get('total_total_type'),
            );
        }
    }
}