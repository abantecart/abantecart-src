<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
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
 * Class ATax
 *
 * @property ASession $session
 * @property AConfig $config
 * @property ACache $cache
 * @property ADB $db
 * @property ACustomer $customer
 */
class ATax
{
    protected $taxes = [];
    /** @var Registry */
    protected $registry;
    /** @var ASession|array */
    protected $customer_data;

    /**
     * @param Registry $registry
     * @param null|array $c_data
     *
     * @throws AException
     */
    public function __construct($registry, &$c_data = null)
    {
        $this->registry = $registry;

        //if nothing is passed (default) use session array. Customer session, can function on storefront only
        if ($c_data == null) {
            $this->customer_data =& $this->session->data;
        } else {
            $this->customer_data =& $c_data;
        }
        $this->customer_data['tax_exempt'] = $this->customer_data['tax_exempt'] ?? false;
        if (isset($this->customer_data['guest']['country_id'])
            || isset($this->customer_data['guest']['shipping']['country_id'])
        ) {
            //take billing address
            if ($this->config->get('config_tax_customer')) {
                $countryId = $this->customer_data['guest']['country_id']
                    ?? $this->customer_data['guest']['shipping']['country_id'];
                $zoneId = $this->customer_data['guest']['zone_id']
                    ?? $this->customer_data['guest']['shipping']['zone_id'];
            } //take shipping address
            else {
                $countryId = $this->customer_data['guest']['shipping']['country_id']
                    ?? $this->customer_data['guest']['country_id'];
                $zoneId = $this->customer_data['guest']['shipping']['zone_id']
                    ?? $this->customer_data['guest']['zone_id'];
            }
        } elseif (
            isset($this->customer_data['shipping_address_id'])
            || isset($this->customer_data['payment_address_id'])
        ) {
            /** @var ModelAccountAddress $mdl */
            $mdl = $this->registry->get('load')->model('account/address', 'storefront');
            //take billing address
            if ($this->config->get('config_tax_customer')) {
                $address = $mdl->getAddress($this->customer_data['payment_address_id']);
            } //take shipping address
            else {
                $address = $mdl->getAddress($this->customer_data['shipping_address_id']);
            }
            $countryId = $address['country_id'] ?? $this->customer_data['country_id'];
            $zoneId = $address['zone_id'] ?? $this->customer_data['zone_id'];
        } elseif ($this->customer_data['country_id'] && $this->customer_data['zone_id']) {
            $countryId = $this->customer_data['country_id'];
            $zoneId = $this->customer_data['zone_id'];
        } else {
            if ($this->config->get('config_tax_store')) {
                $countryId = $this->config->get('config_country_id');
                $zoneId = $this->config->get('config_zone_id');
            } else {
                $countryId = $zoneId = 0;
            }
        }

        $this->setZone($countryId, $zoneId);

        //if guest or registered customer non-exemption and tax rate without exemption mark
        if (!$this->customer_data['customer_group_id']) {
            $this->customer_data['customer_group_id'] = $this->config->get('config_customer_group_id');
        }
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }


    /**
     * Set tax country ID and zone ID for the session
     * Also it loads available taxes for the zone
     *
     * @param int $country_id
     * @param int $zone_id
     *
     * @throws AException
     */
    public function setZone($country_id, $zone_id)
    {
        $country_id = (int)$country_id;
        $zone_id = (int)$zone_id;
        $results = $this->getTaxes($country_id, $zone_id);
        $this->taxes = [];
        foreach ($results as $result) {
            $this->taxes[$result['tax_class_id']][] = [
                'tax_class_id'        => $result['tax_class_id'],
                'rate'                => $result['rate'],
                'rate_prefix'         => $result['rate_prefix'],
                'threshold_condition' => $result['threshold_condition'],
                'threshold'           => $result['threshold'],
                'description'         => $result['description'],
                'tax_exempt_groups'   => unserialize($result['tax_exempt_groups']),
                'priority'            => $result['priority'],
            ];
        }
        $this->customer_data['country_id'] = $country_id;
        $this->customer_data['zone_id'] = $zone_id;
    }

    /**
     * Get available tax classes for country ID and zone ID
     * Storefront use only!!!
     *
     * @param int $country_id
     * @param int $zone_id
     *
     * @return mixed|null
     * @throws AException
     */
    public function getTaxes($country_id, $zone_id)
    {
        $country_id = (int)$country_id;
        $zone_id = (int)$zone_id;

        $language = $this->registry->get('language');
        $language_id = $language->getLanguageID();
        $default_lang_id = $language->getDefaultLanguageID();

        $cache_key = 'localization.tax_class.' . $country_id . '.' . $zone_id . '.lang_' . $language_id;
        $results = $this->cache->pull($cache_key);

        if ($results === false) {
            //Note: Default language text is picked up if no selected language available
            $sql = "SELECT 
                        tr.tax_class_id,
                        tr.rate AS rate, tr.rate_prefix AS rate_prefix, 
                        tr.threshold_condition AS threshold_condition, 
                        tr.threshold AS threshold,
                        tr.tax_exempt_groups AS tax_exempt_groups,
                        COALESCE( td1.title,td2.title) as title,
                        COALESCE( NULLIF(trd1.description, ''),
                                  NULLIF(td1.description, ''),
                                  NULLIF(trd2.description, ''),
                                  NULLIF(td2.description, ''),
                                  COALESCE( td1.title,td2.title)
                        ) as description,
                        tr.priority	
					FROM " . $this->db->table("tax_rates") . " tr
					LEFT JOIN " . $this->db->table("tax_rate_descriptions") . " trd1 
					    ON (tr.tax_rate_id = trd1.tax_rate_id AND trd1.language_id = '" . (int)$language_id . "')
					LEFT JOIN " . $this->db->table("tax_rate_descriptions") . " trd2 
					    ON (tr.tax_rate_id = trd2.tax_rate_id AND trd2.language_id = '" . (int)$default_lang_id . "')
					LEFT JOIN " . $this->db->table("tax_classes") . " tc ON tc.tax_class_id = tr.tax_class_id
					LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td1 
					    ON (tc.tax_class_id = td1.tax_class_id AND td1.language_id = '" . (int)$language_id . "')
					LEFT JOIN " . $this->db->table("tax_class_descriptions") . " td2 
					    ON (tc.tax_class_id = td2.tax_class_id AND td2.language_id = '" . (int)$default_lang_id . "')
					WHERE (tr.zone_id = '0' OR tr.zone_id = '" . $zone_id . "')
						AND tr.location_id IN (
						                SELECT z2l.location_id
										FROM " . $this->db->table("zones_to_locations") . " z2l, 
										        " . $this->db->table("locations") . " l
                                        WHERE z2l.location_id = l.location_id AND z2l.zone_id = '" . $zone_id . "')
					ORDER BY tr.priority";
            $tax_rate_query = $this->db->query($sql);
            $results = $tax_rate_query->rows;
            $this->cache->push($cache_key, $results);
        }

        return $results;
    }

    /**
     * Add Calculated tax based on provided $tax_class_id
     * If $calculate switch passed as false skip tax calculation.
     * This is used in display of product price with tax added or not (based on config_tax )
     *
     * @param float $value
     * @param int $tax_class_id
     * @param bool $calculate
     *
     * @return float
     */
    public function calculate($value, $tax_class_id, $calculate = true, $backward = false)
    {
        if (!$this->customer_data['tax_exempt'] && ($calculate) && (isset($this->taxes[$tax_class_id]))) {
            if ($backward) {
                return (float)$value - $this->calcTotalTaxAmount($value, $tax_class_id, $backward);
            } else {
                return (float)$value + $this->calcTotalTaxAmount($value, $tax_class_id, $backward);
            }
        } else {
            //skip calculation
            return $value;
        }
    }

    /**
     * @param array $tax_rate_info
     * @param int $customer_group_id
     * @return bool
     */
    protected function _is_tax_rate_exempt($tax_rate_info, $customer_group_id)
    {
        //check customer group tax-exempt sign
        if ($this->customer_data['customer_tax_exempt']) {
            return true;
        } else {
            if (in_array($customer_group_id, (array)$tax_rate_info['tax_exempt_groups'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate total applicable tax amount based on the amount provided
     *
     * @param float $amount
     * @param int $tax_class_id
     *
     * @return float
     */
    public function calcTotalTaxAmount($amount, $tax_class_id, $backward = false)
    {
        $total_tax_amount = 0.0;
        if (isset($this->taxes[$tax_class_id])) {
            foreach ($this->taxes[$tax_class_id] as $tax_rate) {
                $total_tax_amount += $this->calcTaxAmount($amount, $tax_rate, $backward);
            }
        }
        return $total_tax_amount;
    }

    /**
     * Calculate applicable tax amount based on the amount and tax rate record provided
     *
     * @param float $amount
     * @param array $tax_rate
     *
     * @return float
     */
    public function calcTaxAmount($amount, $tax_rate = [], $backward = false)
    {
        $amount = (float)$amount;
        $tax_amount = 0.0;
        if (!$this->customer_data['tax_exempt']
            && !$this->_is_tax_rate_exempt($tax_rate, $this->customer_data['customer_group_id'])
            && !empty($tax_rate)
            && isset($tax_rate['rate'])
        ) {
            //Validate tax class rules if condition present and see if applicable
            if ($tax_rate['threshold_condition'] && is_numeric($tax_rate['threshold'])) {
                if (!$this->_compare($amount, $tax_rate['threshold'], $tax_rate['threshold_condition'])) {
                    //does not match. get out
                    return 0.0;
                }
            }

            if ($tax_rate['rate_prefix'] == '$') {
                //this is absolute value rate in default currency
                $tax_amount = (float)$tax_rate['rate'];
            } else {
                //This is percent based rate
                if ($backward) {
                    $tax_amount = ($amount / (100 + (float)$tax_rate['rate'])) * (float)$tax_rate['rate'];
                } else {
                    $tax_amount = $amount * (float)$tax_rate['rate'] / 100;
                }
            }
        }
        return $tax_amount;
    }

    /**
     * Get array with applicable rates for tax class based on the provided amount
     * Array returns Absolute and Percent rates in separate arrays
     *
     * @param float $amount
     * @param int $tax_class_id
     *
     * @return array
     * @since 1.2.7
     *
     */
    public function getApplicableRates($amount, $tax_class_id)
    {
        $rates = [];
        if (isset($this->taxes[$tax_class_id])) {
            foreach ($this->taxes[$tax_class_id] as $tax_rate) {
                if (!empty($tax_rate) && isset($tax_rate['rate'])) {
                    if ($tax_rate['threshold_condition'] && is_numeric($tax_rate['threshold'])) {
                        if (!$this->_compare($amount, $tax_rate['threshold'], $tax_rate['threshold_condition'])) {
                            continue;
                        }
                    }
                    if ($tax_rate['rate_prefix'] == '$') {
                        $rates['absolute'][] = $tax_rate['rate'];
                    } else {
                        $rates['percent'][] = $tax_rate['rate'];
                    }
                }
            }
        }
        return $rates;
    }

    /**
     * @param int $tax_class_id
     *
     * @return array
     */
    public function getDescription($tax_class_id)
    {
        return $this->taxes[$tax_class_id] ?? [];
    }

    /**
     * @param int $tax_class_id
     *
     * @return bool
     */
    public function has($tax_class_id)
    {
        return isset($this->taxes[$tax_class_id]);
    }

    /**
     * @param float $value1
     * @param float $value2
     * @param string $operator
     *
     * @return bool
     */
    protected function _compare($value1, $value2, $operator)
    {
        switch ($operator) {
            case 'eq':
                return ($value1 == $value2);
            case 'ne':
                return ($value1 != $value2);
            case 'le':
                return ($value1 <= $value2);
            case 'ge':
                return ($value1 >= $value2);
            case 'lt':
                return ($value1 < $value2);
            case 'gt':
                return ($value1 > $value2);
            default:
                return false;
        }
    }
}