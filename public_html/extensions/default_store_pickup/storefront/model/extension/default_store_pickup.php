<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ModelExtensionDefaultStorePickup extends Model
{
    function getQuote($address)
    {
        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('default_store_pickup/default_store_pickup');

        if ( !$this->config->get('default_store_pickup_status')
             || !$this->config->get('config_country_id')
             || !$this->config->get('config_zone_id')
        ) {
            return [];
        }

        $quote_data = [];
        $quote_data['default_store_pickup'] = [
            'id'           => 'default_store_pickup.default_store_pickup',
            'title'        => $language->get('text_description'),
            'cost'         => 0.00,
            'tax_class_id' => 0,
            'text'         => $language->get('text_free'),
        ];

        return [
            'id'         => 'default_store_pickup',
            'title'      => $language->get('text_title'),
            'quote'      => $quote_data,
            'sort_order' => $this->config->get('default_store_pickup_sort_order'),
            'error'      => false,
        ];
    }
}