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
 * Class ALength
 */
class ALength
{
    protected $lengths = array();
    /**
     * @var ADB
     */
    protected $db;
    /**
     * @var AConfig
     */
    protected $config;

    public $predefined_lengths = array(
        'cm' => array(
            'length_class_id' => 1,
            'value'           => 1.00000000,
            'iso_code'        => 'CMET',
            'language_id'     => 1,
            'title'           => 'Centimeter',
            'unit'            => 'cm',
        ),
        'mm' => array(
            'length_class_id' => 2,
            'value'           => 10.00000000,
            'iso_code'        => 'MMET',
            'language_id'     => 1,
            'title'           => 'Millimeter',
            'unit'            => 'mm',
        ),
        'in' => array(
            'length_class_id' => 3,
            'value'           => 0.39370000,
            'iso_code'        => 'INCH',
            'language_id'     => 1,
            'title'           => 'Inch',
            'unit'            => 'in',
        ),
    );
    public $predefined_length_ids = array();

    /**
     * @param $registry Registry
     */
    public function __construct($registry)
    {
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');

        $cache = $registry->get('cache');
        $language_id = (int)$registry->get('language')->getLanguageID();
        $cache_key = 'localization.length_classes.lang_'.$language_id;
        $cache_data = $cache->pull($cache_key);
        if ($cache_data !== false) {
            $this->lengths = $cache_data;
        } else {
            $sql = "SELECT *, mc.length_class_id
					FROM ".$this->db->table("length_classes")." mc
					LEFT JOIN ".$this->db->table("length_class_descriptions")." mcd
						ON (mc.length_class_id = mcd.length_class_id)
					WHERE mcd.language_id = '".$language_id."'";
            $length_class_query = $this->db->query($sql);
            foreach ($length_class_query->rows as $row) {
                if (!$row['unit']) {
                    $error = new AError('Error! Empty unit of length class ID '.$row['length_class_id']);
                    $error->code = 'Core ALength class Error';
                    $error->toLog()->toMessages();
                    continue;
                }
                $this->lengths[strtolower($row['unit'])] = $row;
            }
            $cache->push($cache_key, $this->lengths);
        }
        foreach ($this->predefined_lengths as $unit => $length) {
            $this->predefined_length_ids[] = $length['length_class_id'];
        }
        $this->lengths = array_merge($this->lengths, $this->predefined_lengths);
    }

    /**
     * convert length unit based
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
        if ($unit_from == $unit_to || is_null($unit_to) || is_null($unit_from)) {
            return $value;
        }
        if (empty($value)) {
            return 0.0;
        }

        if (isset($this->lengths[strtolower($unit_from)])) {
            $from = $this->lengths[strtolower($unit_from)]['value'];
        } else {
            $from = 0;
        }

        if (isset($this->lengths[strtolower($unit_to)])) {
            $to = $this->lengths[strtolower($unit_to)]['value'];
        } else {
            $to = 0;
        }

        return $value * ($to / $from);
    }

    /**
     * convert length id based
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
        if (isset($this->lengths[$unit]['unit'])) {
            return number_format($value, 2, $decimal_point, $thousand_point).$this->lengths[$unit]['unit'];
        } else {
            return number_format($value, 2, $decimal_point, $thousand_point);
        }
    }

    /**
     * convert format id based
     *
     * @param float  $value
     * @param int    $length_class_id
     * @param string $decimal_point
     * @param string $thousand_point
     *
     * @return string
     */
    public function formatByID($value, $length_class_id, $decimal_point = '.', $thousand_point = ',')
    {
        return $this->format($value, $this->getUnit($length_class_id), $decimal_point, $thousand_point);
    }

    /**
     * get length unit code based on $length_class_id
     *
     * @param int $length_class_id
     *
     * @return string
     */
    public function getUnit($length_class_id)
    {
        foreach ($this->lengths as $lth) {
            if (isset($lth[$length_class_id])) {
                return $lth['unit'];
            }
        }
        return '';
    }

    /**
     * get length_class_id based on unit code
     *
     * @param string $length_unit
     *
     * @return string
     */
    public function getClassID($length_unit)
    {
        if (isset($this->lengths[$length_unit])) {
            return $this->lengths[$length_unit]['length_class_id'];
        } else {
            return '';
        }
    }

    /**
     * @param int $length_class_id
     *
     * @return bool
     */
    public function getCodeById($length_class_id)
    {
        foreach ($this->lengths as $lng) {
            if ($lng['length_class_id'] == $length_class_id) {
                return $lng['iso_code'];
            }
        }
        return false;
    }
}
