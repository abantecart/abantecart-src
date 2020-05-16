<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
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

class ModelExtensionDefaultFlatRateShipping extends Model
{
    function getQuote($address)
    {
        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('default_flat_rate_shipping/default_flat_rate_shipping');
        $status = false;
        $method_data = array();

        if ($this->config->get('default_flat_rate_shipping_status')) {
            $default_cost = $this->config->get('default_flat_rate_shipping_default_cost');
            $default_tax_class_id = (int)$this->config->get('default_flat_rate_shipping_default_tax_class_id');
            $default_status = $this->config->get('default_flat_rate_shipping_default_status');
            //get location_id
            $sql = "SELECT location_id
					FROM ".$this->db->table('zones_to_locations')."
					WHERE country_id = '".(int)$address['country_id']."'
						AND (zone_id = '".(int)$address['zone_id']."')";
            $result = $this->db->query($sql);
            $customer_location_id = (int)$result->row['location_id'];
            if ($customer_location_id) {
                $cost = $this->config->get('default_flat_rate_shipping_cost_'.$customer_location_id);
                $location_status = $this->config->get('default_flat_rate_shipping_status_'.$customer_location_id);
                if (!$location_status && !$default_status) {
                    $status = false;
                } elseif ($location_status && $cost) {
                    $tax_class_id = $this->config->get('default_flat_rate_shipping_tax_class_id_'.$customer_location_id);
                    $status = true;
                } else {
                    //if cost not set - use default cost
                    $customer_location_id = 0;
                    $status = $default_status;
                }
            }
            //if cost not set or unknown location - try use default settings
            if (!$customer_location_id) {
                if (empty($default_cost) || !$default_status) {
                    $status = false;
                } //use default settings for other locations
                else {
                    $status = true;
                    $cost = $default_cost;
                    $tax_class_id = $default_tax_class_id;
                }
            }
        }

        if (!$status) {
            return $method_data;
        }

        $quote_data = array();
        //Process all products shipped together with not special shipping settings on a product level
        if (count($this->cart->basicShippingProducts()) > 0) {
            $quote_data['default_flat_rate_shipping'] = array(
                'id'           => 'default_flat_rate_shipping.default_flat_rate_shipping',
                'title'        => $language->get('text_description'),
                'cost'         => $cost,
                'tax_class_id' => (int)$tax_class_id,
                'text'         => $this->currency->format($this->tax->calculate($cost,
                    $tax_class_id,
                    (bool)$this->config->get('config_tax'))),
            );
        }

        $special_ship_products = $this->cart->specialShippingProducts();
        foreach ($special_ship_products as $product) {
            //check if free or fixed shipping
            if ($product['free_shipping']) {
                $fixed_cost = 0;
            } else {
                if ($product['shipping_price'] > 0) {
                    $fixed_cost = $product['shipping_price'];
                    //If ship individually count every quantity
                    if ($product['ship_individually']) {
                        $fixed_cost = $fixed_cost * $product['quantity'];
                    }
                } else {
                    $fixed_cost = $cost;
                }
            }
            //merge data and accumulate shipping cost
            if (isset($quote_data['default_flat_rate_shipping'])) {
                $quote_data['default_flat_rate_shipping']['cost'] = $quote_data['default_flat_rate_shipping']['cost'] + $fixed_cost;
                if ($quote_data['default_flat_rate_shipping']['cost'] > 0) {
                    $quote_data['default_flat_rate_shipping']['text'] = $this->currency->format(
                        $this->tax->calculate(
                            $quote_data['default_flat_rate_shipping']['cost'],
                            $tax_class_id,
                            (bool)$this->config->get('config_tax')
                        )
                    );
                } else {
                    $quote_data['default_flat_rate_shipping']['text'] = $language->get('text_free');
                }
            } else {
                $quote_data['default_flat_rate_shipping'] = array(
                    'id'           => 'default_flat_rate_shipping.default_flat_rate_shipping',
                    'title'        => $language->get('text_description'),
                    'cost'         => $fixed_cost,
                    'tax_class_id' => $tax_class_id,
                    'text'         => '',
                );
                if ($fixed_cost > 0) {
                    $quote_data['default_flat_rate_shipping']['text'] = $this->currency->format(
                        $this->tax->calculate($fixed_cost,
                            $tax_class_id,
                            (bool)$this->config->get('config_tax')
                        )
                    );
                } else {
                    $quote_data['default_flat_rate_shipping']['text'] = $language->get('text_free');
                }
            }
        }

        if ($quote_data) {
            $method_data = array(
                'id'         => 'default_flat_rate_shipping',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_flat_rate_shipping_sort_order'),
                'error'      => false,
            );
        }

        return $method_data;
    }
}
