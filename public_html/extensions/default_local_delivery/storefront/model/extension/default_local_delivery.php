<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Licence details is bundled with this package in the file LICENSE.txt.
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

class ModelExtensionDefaultLocalDelivery extends Model
{
    function getQuote($address)
    {
        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['filename']);
        $language->load('default_local_delivery/default_local_delivery');

        if ($this->config->get('default_local_delivery_status') ) {
            if( $this->config->get('default_local_delivery_postal_codes') ){
                $codes = explode(",",$this->config->get('default_local_delivery_postal_codes'));
                $codes = array_map('trim', $codes);
                if( in_array($address['postcode'], $codes) ){
                    $status = true;
                } else {
                    $status = false;
                }
            }else{
                $status = true;
            }
        } else {
            $status = false;
        }

        if ($this->cart->getSubTotal() < $this->config->get('default_local_delivery_total')) {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $quote_data = array();

            $quote_data['default_local_delivery'] = array(
                'id'           => 'default_local_delivery.default_local_delivery',
                'title'        => $language->get('text_description'),
                'cost'         => (float)$this->config->get('default_local_delivery_cost'),
                'text'         => (float)$this->config->get('default_local_delivery_cost')
                                  ? $this->currency->format((float)$this->config->get('default_local_delivery_cost'))
                                  : $language->get('text_free'),
            );

            $method_data = array(
                'id'         => 'default_local_delivery',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_local_delivery_sort_order'),
                'error'      => false,
            );
        }

        return $method_data;
    }
}