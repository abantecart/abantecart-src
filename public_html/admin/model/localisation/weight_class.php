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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelLocalisationWeightClass extends Model
{
    public function addWeightClass($data)
    {
        $this->db->query("INSERT INTO ".$this->db->table("weight_classes")."
							SET value = '".(float)$data['value']."',
								iso_code = UPPER('".$this->db->escape($data['iso_code'])."')"
        );

        $weight_class_id = $this->db->getLastId();
        foreach ($data['weight_class_description'] as $language_id => $value) {
            $this->language->replaceDescriptions('weight_class_descriptions',
                array('weight_class_id' => (int)$weight_class_id),
                array(
                    $language_id => array(
                        'title' => $value['title'],
                        'unit'  => $value['unit'],
                    ),
                ));
        }

        $this->cache->remove('localization');
        return $weight_class_id;
    }

    public function editWeightClass($weight_class_id, $data)
    {
        if (isset($data['value']) || isset($data['iso_code'])) {
            $sql = "UPDATE ".$this->db->table("weight_classes")."
					SET ";
            $inc = array();
            if (isset($data['value'])) {
                $inc[] = "value = '".(float)$data['value']."'";
            }
            if (isset($data['iso_code'])) {
                $inc[] = "iso_code = UPPER('".$this->db->escape($data['iso_code'])."')";
            }
            $sql .= implode(", ", $inc);
            $sql .= " WHERE weight_class_id = '".(int)$weight_class_id."'";
            $this->db->query($sql);
        }

        if (isset($data['weight_class_description'])) {
            foreach ($data['weight_class_description'] as $language_id => $value) {
                $update = array();
                if (isset($value['title'])) {
                    $update["title"] = $value['title'];
                }
                if (isset($value['unit'])) {
                    $update["unit"] = $value['unit'];
                }
                if (!empty($update)) {
                    $this->language->replaceDescriptions('weight_class_descriptions',
                        array('weight_class_id' => (int)$weight_class_id),
                        array($language_id => $update));
                }
            }
        }

        $this->cache->remove('localization');
    }

    /**
     * @param int $weight_class_id
     */
    public function deleteWeightClass($weight_class_id)
    {
        $this->db->query("DELETE FROM ".$this->db->table("weight_classes")." 
						WHERE weight_class_id = '".(int)$weight_class_id."'");
        $this->db->query("DELETE FROM ".$this->db->table("weight_class_descriptions")." 
						WHERE weight_class_id = '".(int)$weight_class_id."'");
        $this->cache->remove('localization');
    }

    /**
     * @param array $data
     *
     * @return false|array
     */
    public function getWeightClasses($data = array())
    {
        if (!empty($data['content_language_id'])) {
            $language_id = ( int )$data['content_language_id'];
        } else {
            $language_id = (int)$this->language->getContentLanguageID();
        }

        if ($data) {
            $sql = "SELECT *, wc.weight_class_id
					FROM ".$this->db->table("weight_classes")." wc
					LEFT JOIN ".$this->db->table("weight_class_descriptions")." wcd
						ON (wc.weight_class_id = wcd.weight_class_id 
							AND wcd.language_id = '".$language_id."') ";

            $sort_data = array(
                'title',
                'unit',
                'value',
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY ".$data['sort'];
            } else {
                $sql .= " ORDER BY title";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }
            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }
                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }
                $sql .= " LIMIT ".(int)$data['start'].",".(int)$data['limit'];
            }
            $query = $this->db->query($sql);
            return $query->rows;
        } else {
            $cache_key = 'localization.weight_class.lang_'.$language_id;
            $weight_class_data = $this->cache->pull($cache_key);
            if ($weight_class_data === false) {
                $query = $this->db->query(
                    "SELECT *, wc.weight_class_id
					FROM ".$this->db->table("weight_classes")." wc
					LEFT JOIN ".$this->db->table("weight_class_descriptions")." wcd
						ON (wc.weight_class_id = wcd.weight_class_id AND wcd.language_id = '".$language_id."')");
                $weight_class_data = $query->rows;
                $this->cache->push($cache_key, $weight_class_data);
            }
            return $weight_class_data;
        }
    }

    /**
     * @param int $weight_class_id
     * @param int $language_id
     *
     * @return array
     */
    public function getWeightClass($weight_class_id, $language_id = 0)
    {
        $language_id = (int)$language_id;
        if (!$language_id) {
            $language_id = (int)$this->language->getContentLanguageID();
        }
        $query = $this->db->query("SELECT *, wc.weight_class_id
									FROM ".$this->db->table("weight_classes")." wc
									LEFT JOIN ".$this->db->table("weight_class_descriptions")." wcd
										ON (wc.weight_class_id = wcd.weight_class_id 
											AND wcd.language_id = '".$language_id."')
									WHERE wc.weight_class_id = '".(int)$weight_class_id."'");
        return $query->row;
    }

    /**
     * @param string $unit
     * @param int    $language_id
     *
     * @return array
     */
    public function getWeightClassDescriptionByUnit($unit, $language_id = 0)
    {
        $language_id = (int)$language_id;
        if (!$language_id) {
            $language_id = (int)$this->language->getContentLanguageID();
        }
        $query = $this->db->query("SELECT *
									FROM ".$this->db->table("weight_class_descriptions")." 
									WHERE unit = '".$this->db->escape($unit)."'
										AND language_id = '".$language_id."'");
        if ($query->num_rows) {
            return $query->row;
        } else {
            //TODO: remove this in 2.0
            $query = $this->db->query("SELECT *
									FROM ".$this->db->table("weight_class_descriptions")." 
									WHERE unit = '".$this->db->escape($unit)."'");
            return $query->row;
        }
    }

    /**
     * @param     $iso_code
     * @param int $language_id
     *
     * @return array
     */
    public function getWeightClassByCode($iso_code, $language_id = 0)
    {
        $language_id = (int)$language_id;
        if (!$language_id) {
            $language_id = (int)$this->language->getContentLanguageID();
        }
        $query = $this->db->query("SELECT *, wc.weight_class_id
									FROM ".$this->db->table("weight_classes")." wc
									LEFT JOIN ".$this->db->table("weight_class_descriptions")." wcd
										ON (wc.weight_class_id = wcd.weight_class_id 
											AND wcd.language_id = '".$language_id."')
									WHERE wc.iso_code = '".$this->db->escape($iso_code)."'");
        return $query->row;
    }

    /**
     * @param int $weight_class_id
     *
     * @return array
     */
    public function getWeightClassDescriptions($weight_class_id)
    {
        $weight_class_data = array();
        $query = $this->db->query("SELECT *
									FROM ".$this->db->table("weight_class_descriptions")." 
									WHERE weight_class_id = '".(int)$weight_class_id."'");
        foreach ($query->rows as $row) {
            $weight_class_data[$row['language_id']] = $row;
        }
        return $weight_class_data;
    }

    /**
     * @return int
     */
    public function getTotalWeightClasses()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total
									FROM ".$this->db->table("weight_classes"));
        return (int)$query->row['total'];
    }
}