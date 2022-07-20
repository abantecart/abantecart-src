<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  Modified by WHY2 Support for AbanteCart

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
        $postcode         = str_replace( ' ', '', $address['postcode'] );
        if ($this->config->get('default_local_delivery_status') ) {
            if( $this->config->get('default_local_delivery_postal_codes') ){
                $codes = explode(",",$this->config->get('default_local_delivery_postal_codes'));
                foreach ($codes as $code) {
                    if ( fnmatch( $code,$postcode,FNM_CASEFOLD ) ) {
                        $status = true;
                    }
                }
            } else{
                $status = true;
            }
        } else {
            $status = false;
        }

        if ($this->cart->getSubTotal() < $this->config->get('default_local_delivery_total')) {
            $status = false;
        }

        $method_data = [];

        if ($status) {
            $quote_data = [];

            $quote_data['default_local_delivery'] = [
                'id'           => 'default_local_delivery.default_local_delivery',
                'title'        => $language->get('text_description'),
                'cost'         => $this->tax->calculate(
                    $this->config->get('default_local_delivery_cost'),
                    (int)$this->config->get('default_local_delivery_tax_class_id')
                ),
                'text'         => (float)$this->config->get('default_local_delivery_cost')
                                    ? $this->currency->format((float)$this->config->get('default_local_delivery_cost'))
                                    : $language->get('text_free'),
            ];

            $method_data = [
                'id'         => 'default_local_delivery',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_local_delivery_sort_order'),
                'error'      => false,
            ];
        }

        return $method_data;
    }
}