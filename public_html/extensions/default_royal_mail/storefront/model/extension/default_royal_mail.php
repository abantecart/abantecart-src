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

/**
 * Class ModelExtensionDefaultRoyalMail
 *
 * @property AWeight $weight
 */
class ModelExtensionDefaultRoyalMail extends Model
{
    public $lang;
    public function __construct($registry)
    {
        parent::__construct($registry);
        //create new instance of language for case when model called from admin-side
        $this->lang = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $this->lang->load($this->lang->language_details['directory']);
        $this->lang->load('default_royal_mail/default_royal_mail');
    }

    function getQuote($address)
    {
        $language = $this->lang;
        $weight = 0;
        if ($this->config->get('default_royal_mail_status')) {
            if (!$this->config->get('default_royal_mail_location_id')) {
                $status = true;
            } else {
                $query = $this->db->query(
                    "SELECT *
                    FROM ".$this->db->table('zones_to_locations')."
                    WHERE location_id = '".(int)$this->config->get('default_royal_mail_location_id')."'
                        AND country_id = '".(int)$address['country_id']."'
                        AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')"
                );
                $status = (bool)$query->num_rows;
            }
        } else {
            $status = false;
        }

        $method_data = [];
        if (!$status) {
            return $method_data;
        }
        $quote_data = [];
        //build array with cost for shipping
        // ids of products without special shipping cost
        $generic_product_ids = $free_shipping_ids = $shipping_price_ids = [];
        $shipping_price_cost = 0; // total shipping cost of product with fixed shipping price
        $cart_products = $this->cart->getProducts();
        foreach ($cart_products as $product) {
            //(exclude free shipping products)
            if ($product['free_shipping']) {
                $free_shipping_ids[] = $product['product_id'];
                continue;
            }
            if ($product['shipping_price'] > 0) {
                $shipping_price_ids[] = $product['product_id'];
                $shipping_price_cost += $product['shipping_price'] * $product['quantity'];
            }
            $generic_product_ids[] = $product['product_id'];
        }

        if ($generic_product_ids) {
            $api_weight_product_ids = array_diff($generic_product_ids, $shipping_price_ids);
            //WHEN ONLY PRODUCTS WITH FIXED SHIPPING PRICES ARE IN BASKET
            if (!$api_weight_product_ids) {
                $cost = $shipping_price_cost;
                $quote_data['default_royal_mail'] = [
                    'id'           => 'default_royal_mail.default_royal_mail',
                    'title'        => $language->get('text_title'),
                    'cost'         => $cost,
                    'tax_class_id' => $this->config->get('default_royal_mail_tax_class_id'),
                    'text'         => $this->currency->format(
                        $this->tax->calculate(
                            $this->currency->convert(
                                $cost,
                                $this->config->get('config_currency'),
                                $this->currency->getCode()
                            ),
                            $this->config->get('default_royal_mail_tax_class_id'),
                            $this->config->get('config_tax')),
                        $this->currency->getCode(),
                        1.0000000),
                ];

                return [
                    'id'         => 'default_royal_mail',
                    'title'      => $language->get('text_title'),
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('default_royal_mail_sort_order'),
                    'error'      => '',
                ];
            }
        } else {
            $api_weight_product_ids = $shipping_price_ids;
        }

        if ($api_weight_product_ids) {
            //get weight non-free shipping products only
            $weight = $this->weight->convert(
                $this->cart->getWeight($api_weight_product_ids),
                $this->config->get('config_weight_class'),
                $this->config->get('default_royal_mail_weight_class')
            );
            $weight = max($weight, 0.1);
        }

        $weight = max($weight, 0.1);

        // FOR CASE WHEN ONLY FREE SHIPPING PRODUCTS IN BASKET
        if (!$api_weight_product_ids && $free_shipping_ids) {
            if ($address['iso_code_2'] == 'GB') {
                $method_name = 'default_royal_mail_'.$this->config->get('default_royal_mail_free_gb');
            } else {
                $method_name = 'default_royal_mail_intl_'.$this->config->get('default_royal_mail_free');
            }
            $text = $language->get($method_name);

            $quote_data[$method_name] = [
                'id'           => 'default_royal_mail.'.$method_name,
                'title'        => $text,
                'cost'         => 0.0,
                'tax_class_id' => $this->config->get('default_royal_mail_tax_class_id'),
                'text'         => $language->get('text_free'),
            ];

            return [
                'id'         => 'default_royal_mail',
                'title'      => $language->get('text_title'),
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_royal_mail_sort_order'),
                'error'      => '',
            ];
        }

        $sub_total = $this->cart->getSubTotal();
        /** UK */
        $methods = ['1st_class', '2nd_class', 'tracked24','tracked48', 'mail24','mail48', 'sameday','special_delivery_guaranteed'];
        foreach($methods as $method) {
            if (!$this->config->get('default_royal_mail_'.$method) || $address['iso_code_2'] != 'GB'){
                continue;
            }
            $this->processMethod(
                [
                    'international' => false,
                    'method'        => $method,
                    'price_zone'    => null,
                    'weight'        => $weight,
                    'sub_total'     => $sub_total,
                    'shipping_cost' => $shipping_price_cost,
                    'generic_product_ids' => $generic_product_ids
                ],
                $quote_data
            );
        }

        //international delivery
        if ($address['iso_code_2'] != 'GB') {
            $customerPriceZone = [];
            $rmZones = ['europe_zone_1','europe_zone_2','europe_zone_3','world_zone_1','world_zone_2','world_zone_3'];
            foreach($rmZones as $rmZone) {
                $cList = unserialize($this->config->get('default_royal_mail_intl_'.$rmZone));
                if(!$cList || !in_array($address['iso_code_2'], $cList)){
                    continue;
                }
                $customerPriceZone[] = $rmZone;
            }
            // if country not found - escape
            if(!$customerPriceZone){
                return [];
            }
            $methods = ['standard_economy','standard_priority','signed','tracked_signed','tracked', 'tracked_heavier'];
            foreach($methods as $method) {
                foreach ($customerPriceZone as $priceZone) {
                    if (!$this->config->get('default_royal_mail_intl_'.$method.'_'.$priceZone)){
                        continue;
                    }

                    $this->processMethod(
                        [
                            'international' => true,
                            'method'        => $method,
                            'price_zone'    => $priceZone,
                            'weight'        => $weight,
                            'sub_total'     => $sub_total,
                            'shipping_cost' => $shipping_price_cost,
                            'generic_product_ids' => $generic_product_ids
                        ],
                        $quote_data
                    );
                }
            }
        }

        if ($quote_data) {
            $shippingTitle = $language->get('text_title');
            if ($this->config->get('default_royal_mail_display_weight')) {
                $shippingTitle .= ' ('.$language->get('default_royal_mail_weight').' '.$this->weight->format($weight,
                        $this->config->get('default_royal_mail_weight_class')).')';
            }
            $method_data = [
                'id'         => 'default_royal_mail.default_royal_mail',
                'title'      => $shippingTitle,
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('default_royal_mail_sort_order'),
                'error'      => false,
            ];
        }

        return $method_data;
    }

    /**
     * @param array $data
     * @param array $quote_data
     * @return void
     * @throws AException
     */
    protected function processMethod(array $data, array &$quote_data)
    {
        extract($data);
        $language = $this->lang;
        $cost = 0.0;
        $compensation = 0.0;

        $methodTxtId = $method;
        if($international) {
            $method = 'intl_'.$method;
            $methodTxtId = $method.'_'.$price_zone;
        }

        $rates = explode(',', $this->config->get('default_royal_mail_'.$methodTxtId.'_rates'));
        foreach ($rates as $rate) {
            list($rateWeight,$ratePrice) = explode(':', $rate);
            if ($rateWeight >= $weight) {
                if (isset($ratePrice)) {
                    $cost = (float)$ratePrice;
                }
                break;
            }
        }

        $rates = explode(',', $this->config->get('default_royal_mail_'.$methodTxtId.'_compensation_rates'));
        foreach ($rates as $rate) {
            list($rateSubtotal,$ratePrice) = explode(':', $rate);
            if ($rateSubtotal >= $sub_total) {
                if (isset($ratePrice)) {
                    $compensation = $ratePrice;
                }
                break;
            }
        }

        if ($cost) {
            $title = $language->get('default_royal_mail_'.$method);

            if ($this->config->get('default_royal_mail_display_insurance') && (float)$compensation) {
                $title .= ' ('.$language->get('default_royal_mail_insurance').' '.$this->currency->format($compensation).')';
            }

            if ($generic_product_ids) {
                $cost += $shipping_price_cost;
            }

            $quote_data['default_royal_mail_'.$methodTxtId] = [
                'id'           => 'default_royal_mail.default_royal_mail_'.$methodTxtId,
                'title'        => $title,
                'cost'         => $cost,
                'tax_class_id' => $this->config->get('default_royal_mail_tax'),
                'text'         => $this->currency->format(
                    $this->tax->calculate(
                        $cost,
                        $this->config->get('default_royal_mail_tax'),
                        $this->config->get('config_tax')
                    )
                ),
            ];
        }
    }
}