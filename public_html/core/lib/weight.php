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

/**
 * Class AWeight
 */
class AWeight
{
    protected $weights = array();
    /**
     * @var ADB
     */
    protected $db;
    /**
     * @var AConfig
     */
    protected $config;
    // TODO: need to changes this in 2.0. Key must be iso-code instead unit name!
    public $predefined_weights = array(
        'kg' => array(
            'weight_class_id' => 1,
            'value'           => 0.02800000,
            'iso_code'        => 'KILO',
            'language_id'     => 1,
            'title'           => 'Kilogram',
            'unit'            => 'kg',
        ),
        'g'  => array(
            'weight_class_id' => 2,
            'value'           => 28.00000000,
            'iso_code'        => 'GRAM',
            'language_id'     => 1,
            'title'           => 'Gram',
            'unit'            => 'g',
        ),

        'lb' => array(
            'weight_class_id' => 5,
            'value'           => 0.06250000,
            'iso_code'        => 'PUND',
            'language_id'     => 1,
            'title'           => 'Pound',
            'unit'            => 'lb',
        ),
        'oz' => array(
            'weight_class_id' => 6,
            'value'           => 1.00000000,
            'iso_code'        => 'USOU',
            'language_id'     => 1,
            'title'           => 'Ounce',
            'unit'            => 'oz',
        ),
    );
    public $predefined_weight_ids = array();
    protected $language_id;

    /**
     * @param $registry Registry
     */
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
        $cache = $registry->get('cache');
        $this->language_id = (int)$registry->get('language')->getLanguageID();
        $cache_key = 'localization.weight_classes.lang_'.$this->language_id;
        $cache_data = $cache->pull($cache_key);
        if ($cache_data !== false) {
            $this->weights = $cache_data;
        } else {
            $sql = "SELECT *, wc.weight_class_id
					FROM ".$this->db->table("weight_classes")." wc
					LEFT JOIN ".$this->db->table("weight_class_descriptions")." wcd
						ON (wc.weight_class_id = wcd.weight_class_id)
					WHERE wcd.language_id = '".$this->language_id."'";
            $weight_class_query = $this->db->query($sql);
            foreach ($weight_class_query->rows as $row) {
                if (!$row['unit']) {
                    $error = new AError('Error! Empty unit of weight class ID '.$row['weight_class_id']);
                    $error->code = 'Core AWeight class Error';
                    $error->toLog()->toMessages();
                    continue;
                }
                $this->weights[strtolower($row['unit'])] = $row;
            }
            $cache->push($cache_key, $this->weights);
        }

        foreach ($this->predefined_weights as $unit => $weight) {
            $this->predefined_weight_ids[] = $weight['weight_class_id'];
        }

        $this->weights = array_merge($this->weights, $this->predefined_weights);
    }

    /**
     * convert weight unit based
     *
     * @param float  $value
     * @param string $unit_from
     * @param string $unit_to
     *
     * @return float
     * TODO: replace units in parameters with iso_codes in 2.0!!!
     */
    public function convert($value, $unit_from, $unit_to)
    {
        if ($unit_from == $unit_to) {
            return $value;
        }

        if (!isset($this->weights[strtolower($unit_from)]) || !isset($this->weights[strtolower($unit_to)])) {
            return $value;
        } else {
            $from = $this->weights[strtolower($unit_from)]['value'];
            $to = $this->weights[strtolower($unit_to)]['value'];

            return $value * ($to / $from);
        }
    }

    /**
     * convert weight id based
     *
     * @param float $value
     * @param int   $from_id
     * @param int   $to_id
     *
     * @return float
     */

    public function convertByID($value, $from_id, $to_id)
    {
        return $this->convert($value, $this->getUnit($from_id), $this->getUnit($to_id));
    }

    /**
     * convert format unit based
     *
     * @param float  $value
     * @param string $unit
     * @param string $decimal_point
     * @param string $thousand_point
     *
     * @return string
     */
    public function format($value, $unit, $decimal_point = '.', $thousand_point = ',')
    {
        if (isset($this->weights[strtolower($unit)])) {
            return number_format($value, 2, $decimal_point, $thousand_point).$this->weights[strtolower($unit)]['unit'];
        } else {
            return number_format($value, 2, $decimal_point, $thousand_point);
        }
    }

    /**
     * convert format id based
     *
     * @param float  $value
     * @param int    $weight_class_id
     * @param string $decimal_point
     * @param string $thousand_point
     *
     * @return string
     */
    public function formatByID($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',')
    {
        return $this->format($value, $this->getUnit($weight_class_id), $decimal_point, $thousand_point);
    }

    /**
     * get weight unit code based on $weight_class_id
     *
     * @param int $weight_class_id
     *
     * @return string
     */

    public function getUnit($weight_class_id)
    {
        $language_id = $this->language_id;
        $output = array();
        foreach ($this->weights as $wth) {
            if ($wth['weight_class_id'] == $weight_class_id) {
                $output[$wth['language_id']] = $wth['unit'];
            }
        }
        return has_value($output[$language_id]) ? $output[$language_id] : (string)current($output);
    }

    /**
     * get weight_class_id based on unit code
     *
     * @param string $weight_unit
     *
     * @return string|int
     */

    public function getClassIDByUnit($weight_unit)
    {
        if (isset($this->weights[$weight_unit])) {
            return $this->weights[$weight_unit]['weight_class_id'];
        } else {
            //TODO: remove this in 2.0
            //if language was switched try to find weight by unit
            $sql = "SELECT weight_class_id
					FROM ".$this->db->table("weight_class_descriptions")."
					WHERE unit = '".$this->db->escape($weight_unit)."'";
            $result = $this->db->query($sql);
            if ($result->row['weight_class_id']) {
                return $result->row['weight_class_id'];
            }
        }
        return '';
    }

    /**
     * @param string $iso_code
     *
     * @return int|false
     */
    public function getClassIDByCode($iso_code)
    {
        foreach ($this->weights as $w) {
            if (strtolower($w['iso_code']) == strtolower($iso_code)) {
                return $w['weight_class_id'];
            }
        }
        return false;
    }

    /**
     * @deprecated since 1.2.11
     *
     * @param string $weight_unit
     *
     * @return int|string
     */
    public function getClassID($weight_unit)
    {
        return $this->getClassIDByUnit($weight_unit);
    }

    /**
     * @param int $weight_class_id
     *
     * @return string
     */
    public function getCodeById($weight_class_id)
    {
        foreach ($this->weights as $wth) {
            if ($wth['weight_class_id'] == $weight_class_id) {
                return $wth['iso_code'];
            }
        }
        return false;
    }
}
