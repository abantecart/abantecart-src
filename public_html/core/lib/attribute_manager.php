<?php /** @noinspection SqlResolve */
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2023 Belavier Commerce LLC

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
 * Class to handle access to global attributes
 *
 * @property ASession $session
 * @property ExtensionsApi $extensions
 * @property AHtml $html
 */
class AAttribute_Manager extends AAttribute
{
    public $error = [];

    public static $allowedProductOptionFieldTypes = ['I', 'T', 'S', 'M', 'R', 'C', 'G', 'H', 'U', 'B', 'D'];
    public static $allowedDownloadAttributeFieldTypes = ['I', 'T', 'S', 'M', 'R', 'C'];

    public function __construct($attribute_type = '', $language_id = 0)
    {
        parent::__construct($attribute_type, $language_id);
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AAttribute_Manager');
        }
    }

    public function clearCache()
    {
        $this->cache->remove('attribute');
        $this->cache->remove('attributes');
    }

    /**
     * @param int $attribute_id
     *
     * @throws AException
     */
    public function deleteAttribute($attribute_id)
    {
        $this->db->query(
            "DELETE FROM ".$this->db->table("global_attributes")."
            WHERE attribute_id = '".$this->db->escape($attribute_id)."' "
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("global_attributes_descriptions")."
            WHERE attribute_id = '".$this->db->escape($attribute_id)."' "
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("global_attributes_values")." 
            WHERE attribute_id = '".$this->db->escape($attribute_id)."' "
        );
        $this->db->query(
            "DELETE FROM ".$this->db->table("global_attributes_value_descriptions")." 
            WHERE attribute_id = '".$this->db->escape($attribute_id)."' "
        );

        $this->clearCache();
    }

    /**
     * @param array $data
     *
     * @return bool|int
     * @throws AException
     */
    public function addAttribute($data)
    {
        if (!$data['name']) {
            return false;
        }
        $language_id = $this->session->data['content_language_id'];

        $this->db->query(
            "INSERT INTO ".$this->db->table("global_attributes")."
                SET attribute_type_id = '".$this->db->escape($data['attribute_type_id'])."',
                    attribute_group_id = '".$this->db->escape($data['attribute_group_id'])."',
                    attribute_parent_id = '".$this->db->escape($data['attribute_parent_id'])."',
                    element_type = '".$this->db->escape($data['element_type'])."',
                    sort_order = '".$this->db->escape($data['sort_order'])."',
                    required = '".$this->db->escape($data['required'])."',
                    settings = '".$this->db->escape(serialize($data['settings']))."',
                    status = '".$this->db->escape($data['status'])."',
                    regexp_pattern = '".$this->db->escape($data['regexp_pattern'])."'");

        $attribute_id = $this->db->getLastId();
        // insert descriptions for used content language and translate
        $this->language->replaceDescriptions(
            'global_attributes_descriptions',
             ['attribute_id' => (int)$attribute_id],
             [
                 $language_id => [
                    'name'        => $data['name'],
                    'error_text'  => $data['error_text'],
                    'placeholder' => $data['placeholder'],
                 ],
             ]
        );

        if ($data['values']) {
            foreach ((array)$data['values'] as $valueData) {
                $attribute_value_id = $this->addAttributeValue(
                    $attribute_id,
                    $valueData
                );
                $this->addAttributeValueDescription(
                    $attribute_id,
                    $attribute_value_id,
                    $language_id,
                    $valueData['value']
                );
            }
        }

        $this->clearCache();
        return $attribute_id;
    }

    /**
     * @param int $attribute_id
     * @param array $data
     *
     * @throws AException
     */
    public function updateAttribute($attribute_id, $data)
    {
        //Note: update is done per 1 language
        $language_id = $this->language->getContentLanguageID();
        $fields = [
            'attribute_type_id',
            'attribute_group_id',
            'attribute_parent_id',
            'element_type',
            'required',
            'sort_order',
            'settings',
            'status',
            'regexp_pattern',
        ];
        $elements_with_options = HtmlElementFactory::getElementsWithOptions();
        $attribute = $this->getAttribute($attribute_id, $language_id);

        //check if we change element type and clean options if it does not require it
        if (isset($data['element_type']) && $data['element_type'] != $attribute['element_type']) {
            if (!in_array($data['element_type'], $elements_with_options)) {
                $sql = "DELETE FROM ".$this->db->table("global_attributes_values")."
                        WHERE attribute_id = '".(int)$attribute_id."'";
                $this->db->query($sql);
                $sql = "DELETE FROM ".$this->db->table("global_attributes_value_descriptions")."
                        WHERE attribute_id = '".(int)$attribute_id."'";
                $this->db->query($sql);
            }
        }

        if (has_value($data['settings'])) {
            $data['settings'] = serialize($data['settings']);
        }

        $update = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $update[] = $f." = '".$this->db->escape($data[$f])."'";
            }
        }
        if (!empty($update)) {
            $sql = "UPDATE ".$this->db->table("global_attributes")."
                    SET ".implode(',', $update)."
                    WHERE attribute_id = '".(int)$attribute_id."'";
            $this->db->query($sql);
        }

        $update = [];
        if (isset($data['name'])) {
            $update['name'] = $data['name'];
        }
        if (isset($data['error_text'])) {
            $update['error_text'] = $data['error_text'];
        }
        if (isset($data['placeholder'])) {
            $update['placeholder'] = $data['placeholder'];
        }

        $this->language->replaceDescriptions('global_attributes_descriptions',
                                             ['attribute_id' => (int)$attribute_id],
                                             [$language_id => $update]
        );

        $insertedValues = [];
        //Update Attribute Values
        if (!empty($data['values']) && in_array($data['element_type'], $elements_with_options)) {
            foreach ($data['values'] as $attrValueId => $valueData) {
                //Check if new or update
                if ($data['attribute_value_ids'][$attrValueId] == 'delete') {
                    //delete the description
                    $this->deleteAllAttributeValueDescriptions($attrValueId);
                    //delete value if no other language
                    $this->deleteAttributeValues($attrValueId);
                } else {
                    if (str_starts_with($attrValueId,'new')) {
                        // New need to create
                        $attribute_value_id = $this->addAttributeValue( $attribute_id,$valueData );
                        $insertedValues[$attribute_value_id] = [
                            'id'         => $attribute_value_id,
                            'value'      => $valueData,
                            'sort_order' => $data['sort_orders'][$attrValueId],
                        ];
                        if ($attribute_value_id) {
                            $this->addAttributeValueDescription(
                                $attribute_id,
                                $attribute_value_id,
                                $language_id,
                                $valueData['value']
                            );
                        }
                    } else {
                        //Existing need to update
                        $this->updateAttributeValue( $attrValueId, $valueData );
                        $this->updateAttributeValueDescription($attribute_id, $attrValueId, $language_id, $valueData['value']);
                    }
                }
            }
        }

        $this->clearCache();
        return $insertedValues ?: true;
    }

    /**
     * @param int $attribute_id
     * @param array $data
     *
     * @return bool|int
     * @throws AException
     */
    public function addAttributeValue($attribute_id, $data = [])
    {
        if (!$attribute_id || !$data) {
            return false;
        }
        $upd = [];
        $allowed = ['txt_id', 'price_modifier', 'price_prefix', 'sort_order'];
        foreach($data as $key => $value) {
            if(!in_array($key, $allowed)) {
                continue;
            }
            if($key == 'sort_order'){
                $value = (int)$value;
            }if($key == 'price_modifier'){
                $value = (float)$value;
            }
            if($key == 'txt_id'){
                $upd[] = $key . " = ".($value ? "'" . $this->db->escape($value) . "'" : " NULL ");
            }else {
                $upd[] = $key . " = '" . $this->db->escape($value) . "'";
            }
        }

        $sql = "INSERT INTO ".$this->db->table("global_attributes_values")." 
                SET attribute_id = '".(int)$attribute_id."',
                " . implode(', ', $upd);
        $this->db->query($sql);
        return $this->db->getLastId();

    }

    /**
     * @param int $attribute_value_id
     *
     * @return bool
     * @throws AException
     */
    public function deleteAttributeValues($attribute_value_id)
    {
        if (empty($attribute_value_id)) {
            return false;
        }
        //Delete global_attributes_values that have no values left
        $sql = "DELETE FROM ".$this->db->table("global_attributes_values")."
                WHERE attribute_value_id = '".(int)$attribute_value_id."'
                    AND attribute_value_id NOT IN
                        (SELECT attribute_value_id FROM ".$this->db->table("global_attributes_value_descriptions")."
                         WHERE attribute_value_id = '".$attribute_value_id."')";
        $this->db->query($sql);
        $this->clearCache();
        return true;
    }

    /**
     * @param int $attribute_value_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function updateAttributeValue( $attribute_value_id, $data = [] )
    {
        if (empty($attribute_value_id)) {
            return false;
        }

        $allowed = ['txt_id', 'price_modifier', 'price_prefix', 'sort_order'];
        $sql = "UPDATE ".$this->db->table("global_attributes_values")." 
                SET ";
        $upd = [];
        foreach($data as $key => $value) {
            if(!in_array($key, $allowed)) {
                continue;
            }
            if($key == 'sort_order'){
                $value = (int)$value;
            }
            if($key == 'txt_id'){
                $upd[] = $key . " = ".($value ? "'" . $this->db->escape($value) . "'" : " NULL ");
            }else {
                $upd[] = $key . " = '" . $this->db->escape($value) . "'";
            }
        }
        $sql .= implode(', ', $upd)
                ." WHERE attribute_value_id = '".(int)$attribute_value_id."'";
        $this->db->query($sql);
        $this->clearCache();
        return true;
    }

    /**
     * @param int $attribute_id
     * @param int $attribute_value_id
     * @param int $language_id
     * @param string $value
     *
     * @return bool
     * @throws AException
     */
    public function addAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value)
    {
        if (empty($attribute_id) || empty($attribute_value_id) || empty($language_id)) {
            return false;
        }

        $this->language->replaceDescriptions('global_attributes_value_descriptions',
                                             ['attribute_id' => (int)$attribute_id, 'attribute_value_id' => (int)$attribute_value_id],
                                             [$language_id => ['value' => $value]]
        );

        $this->clearCache();
        return true;
    }

    /**
     * @param int $attribute_id
     * @param int $attribute_value_id
     * @param int $language_id
     * @param string $value
     *
     * @return bool
     * @throws AException
     */
    public function updateAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value)
    {
        if (empty($attribute_id) || empty($attribute_value_id) || empty($language_id)) {
            return false;
        }

        //Delete and add operation
        $this->deleteAttributeValueDescription($attribute_value_id, $language_id);
        $this->addAttributeValueDescription($attribute_id, $attribute_value_id, $language_id, $value);

        $this->clearCache();
        return true;
    }

    /**
     * @param int $attribute_value_id
     *
     * @return bool
     * @throws AException
     */
    public function deleteAllAttributeValueDescriptions($attribute_value_id)
    {
        if (empty($attribute_value_id)) {
            return false;
        }
        $this->language->deleteDescriptions(
            'global_attributes_value_descriptions',
            ['attribute_value_id' => (int)$attribute_value_id]
        );
        $this->clearCache();
        return true;
    }

    /**
     * @param int $attribute_value_id
     * @param int $language_id
     *
     * @return bool
     * @throws AException
     */
    public function deleteAttributeValueDescription($attribute_value_id, $language_id)
    {
        if (empty($attribute_value_id) || empty($language_id)) {
            return false;
        }

        $this->language->deleteDescriptions(
            'global_attributes_value_descriptions',
            [
                'attribute_value_id' => (int)$attribute_value_id,
                'language_id'        => (int)$language_id,
            ]
        );
        $this->clearCache();
        return true;
    }

    /**
     * @param int $group_id
     *
     * @void
     * @throws AException
     */
    public function deleteAttributeGroup($group_id)
    {

        $this->db->query("DELETE FROM ".$this->db->table("global_attributes_groups")."
                          WHERE attribute_group_id = '".(int)$group_id."' ");
        $this->db->query("DELETE FROM ".$this->db->table("global_attributes_groups_descriptions")."
                           WHERE attribute_group_id = '".(int)$group_id."' ");
        $this->db->query(
            "UPDATE ".$this->db->table("global_attributes")."
                SET attribute_group_id = ''
                 WHERE attribute_group_id = '".(int)$group_id."' ");
        $this->clearCache();
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addAttributeGroup($data)
    {
        $this->db->query(
            "INSERT INTO ".$this->db->table("global_attributes_groups")."
             SET sort_order = '".(int)$data['sort_order']."',
                 status = '".(int)$data['status']."' "
        );

        $group_id = $this->db->getLastId();
        $language_id = $this->session->data['content_language_id'];

        $this->language->replaceDescriptions(
            'global_attributes_groups_descriptions',
            ['attribute_group_id' => (int)$group_id],
            [$language_id => ['name' => $data['name']]]
        );
        $this->clearCache();
        return $group_id;
    }

    /**
     * @param int $group_id
     * @param array $data
     *
     * @throws AException
     */
    public function updateAttributeGroup($group_id, $data)
    {

        $fields = ['sort_order', 'status'];
        $update = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $update[] = $f." = '".(int)$data[$f]."'";
            }
        }
        if (!empty($update)) {
            $this->db->query(
                "UPDATE ".$this->db->table("global_attributes_groups")."
                    SET ".implode(',', $update)."
                    WHERE attribute_group_id = '".(int)$group_id."'");
        }

        if (!empty($data['name'])) {
            $language_id = $this->session->data['content_language_id'];
            $this->language->replaceDescriptions(
                'global_attributes_groups_descriptions',
                ['attribute_group_id' => (int)$group_id],
                [$language_id => ['name' => $data['name']]]
            );

        }
        $this->clearCache();
    }

    /**
     * Get details about given group for attributes
     *
     * @param int $group_id
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getAttributeGroup($group_id, $language_id = 0)
    {

        if (!$language_id) {
            $language_id = $this->config->get('storefront_language_id');
        }

        $query = $this->db->query("
            SELECT gag.*, gagd.name
            FROM ".$this->db->table("global_attributes_groups")." gag
            LEFT JOIN ".$this->db->table("global_attributes_groups_descriptions")." gagd
                ON ( gag.attribute_group_id = gagd.attribute_group_id 
                    AND gagd.language_id = '".(int)$language_id."' )
            WHERE gag.attribute_group_id = '".(int)$group_id."'"
        );

        if ($query->num_rows) {
            return $query->row;
        } else {
            return [];
        }
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getAttributeGroups($data = [])
    {

        if (!$data['language_id']) {
            $data['language_id'] = $this->config->get('storefront_language_id');
        }

        $sql = "SELECT gag.*, gagd.name
                FROM ".$this->db->table("global_attributes_groups")." gag
                LEFT JOIN ".$this->db->table("global_attributes_groups_descriptions")." gagd
                    ON ( gag.attribute_group_id = gagd.attribute_group_id 
                        AND gagd.language_id = '".(int)$data['language_id']."' )";

        $sort_data = [
            'gagd.name',
            'gag.sort_order',
            'gag.status',
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY ".$data['sort'];
        } else {
            $sql .= " ORDER BY gag.sort_order, gagd.name ";
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
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function getTotalAttributeGroups($data = [])
    {

        if (!$data['language_id']) {
            $data['language_id'] = $this->config->get('storefront_language_id');
        }

        $sql = "SELECT gag.*, gagd.name
                FROM ".$this->db->table("global_attributes_groups")." gag
                LEFT JOIN ".$this->db->table("global_attributes_groups_descriptions")." gagd
                    ON ( gag.attribute_group_id = gagd.attribute_group_id 
                        AND gagd.language_id = '".(int)$data['language_id']."' )";

        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    /**
     * @param int $attribute_id
     * @param int $language_id
     *
     * @return array|null
     * @throws AException
     */
    public function getAttribute($attribute_id, $language_id = 0)
    {

        if (!$language_id) {
            $language_id = $this->session->data['content_language_id'];
        }

        $query = $this->db->query(
            "SELECT ga.*, gad.name, gad.error_text, gad.placeholder
             FROM ".$this->db->table("global_attributes")." ga
                LEFT JOIN ".$this->db->table("global_attributes_descriptions")." gad
                    ON ( ga.attribute_id = gad.attribute_id AND gad.language_id = '".(int)$language_id."' )
             WHERE ga.attribute_id = '".(int)$attribute_id."'");
        if ($query->num_rows) {
            return $query->row;
        } else {
            return [];
        }
    }

    /**
     * @param int $attribute_id
     *
     * @return array
     * @throws AException
     */
    public function getAttributeDescriptions($attribute_id)
    {
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("global_attributes_descriptions")."
                                    WHERE attribute_id = '".$this->db->escape($attribute_id)."'");
        $result = [];
        foreach ($query->rows as $row) {
            $result[$row['language_id']] = [
                'name'        => $row['name'],
                'error_text'  => $row['error_text'],
                'placeholder' => $row['placeholder'],
            ];
        }
        return $result;
    }

    /**
     * @param int $attribute_id
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getAttributeValues($attribute_id, $language_id = 0)
    {
        if (!$language_id) {
            $language_id = $this->language->getContentLanguageID();
        }
        $query = $this->db->query(
            "SELECT ga.*, gad.value
            FROM ".$this->db->table("global_attributes_values")." ga
            LEFT JOIN ".$this->db->table("global_attributes_value_descriptions")." gad
               ON ( ga.attribute_value_id = gad.attribute_value_id AND gad.language_id = '".(int)$language_id."' )
            WHERE ga.attribute_id = '".$this->db->escape($attribute_id)."'
            ORDER BY sort_order"
        );
        return $query->rows;
    }

    /**
     * @param int $attribute_value_id
     *
     * @return array
     * @throws AException
     */
    public function getAttributeValueDescriptions($attribute_value_id)
    {
        $query = $this->db->query("
            SELECT *
            FROM ".$this->db->table("global_attributes_value_descriptions")."
            WHERE attribute_value_id = '".$this->db->escape($attribute_value_id)."'"
        );
        $result = [];
        foreach ($query->rows as $row) {
            $result[$row['language_id']] = $row['value'];
        }
        return $result;
    }

    /**
     * @param array $data
     * @param int $language_id
     * @param null|int $attribute_parent_id
     * @param string $mode
     *
     * @return array|int
     * @throws AException
     */
    public function getAttributes($data = [], $language_id = 0, $attribute_parent_id = null, $mode = 'default')
    {

        if (!$language_id) {
            $language_id = $this->session->data['content_language_id'];
        }

        //Prepare filter config
        $filter_params = ['attribute_parent_id', 'status'];
        if (!has_value($data['attribute_type_id'])) {
            $filter_params[] = 'attribute_type_id'; // to prevent ambiguous fields in sql query
        }
        //Build query string based on GET params first
        $filter_form = new AFilter(['method' => 'get', 'filter_params' => $filter_params]);
        //Build final filter
        $grid_filter_params = ['name' => 'gad.name', 'type_name' => 'gatd.type_name'];
        $filter_grid = new AFilter(
            [
            'method'                   => 'post',
            'grid_filter_params'       => $grid_filter_params,
            'additional_filter_string' => $filter_form->getFilterString(),
            ]
        );
        $filter_data = $filter_grid->getFilterData();
        $data = array_merge($filter_data, $data);

        if ($mode == 'total_only') {
            $total_sql = 'count(*) as total';
        } else {
            $total_sql = "ga.*, gad.name, gad.error_text, gad.placeholder, gatd.type_name ";
        }

        $sql = "SELECT ".$total_sql."
                FROM ".$this->db->table("global_attributes")." ga
                LEFT JOIN ".$this->db->table("global_attributes_descriptions")." gad
                    ON ( ga.attribute_id = gad.attribute_id AND gad.language_id = '".(int)$language_id."' )
                LEFT JOIN ".$this->db->table("global_attributes_type_descriptions")." gatd
                    ON ( gatd.attribute_type_id = ga.attribute_type_id AND gatd.language_id = '".(int)$language_id."' )
                WHERE 1=1 ";
        if (!empty($data['search'])) {
            $sql .= " AND ".$data['search'];
        }
        if (!empty($data['subsql_filter'])) {
            $sql .= " AND ".$data['subsql_filter'];
        }
        if (empty($data['search']) && !is_null($attribute_parent_id)) {
            $sql .= " AND ga.attribute_parent_id = '".(int)$attribute_parent_id."' ";
        }

        if (!empty($data['attribute_type_id'])) {
            $sql .= " AND ga.attribute_type_id = ".(int)$data['attribute_type_id'];
        }

        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return $query->row['total'];
        }

        $sort_data = [
            'name'       => 'gad.name',
            'sort_order' => 'ga.sort_order',
            'status'     => 'ga.status',
            'type_name'  => 'gatd.type_name',
        ];

        if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
            $sql .= " ORDER BY ".$sort_data[$data['sort']];
        } else {
            $sql .= " ORDER BY ga.sort_order, gad.name ";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (has_value($data['start']) || has_value($data['limit'])) {
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
    }

    /**
     * @param array $data
     * @param int $language_id
     * @param null $attribute_parent_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalAttributes($data = [], $language_id = 0, $attribute_parent_id = null)
    {
        return $this->getAttributes($data, $language_id, $attribute_parent_id, 'total_only');
    }

    /**
     * @return array
     * @throws AException
     */
    public function getLeafAttributes()
    {
        $query = $this->db->query(
            "SELECT t1.attribute_id as attribute_id
                FROM ".$this->db->table("global_attributes")." AS t1
                LEFT JOIN ".$this->db->table("global_attributes")." as t2
                    ON t1.attribute_id = t2.attribute_parent_id
                WHERE t2.attribute_id IS NULL");
        $result = [];
        foreach ($query->rows as $r) {
            $result[$r['attribute_id']] = $r['attribute_id'];
        }

        return $result;
    }

    /**
     * common method for external validation of attribute
     *
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function validateAttributeCommonData($data = [])
    {
        $this->error = [];
        $this->load->language('catalog/attribute');
        // required
        if (empty($data['attribute_type_id'])) {
            $this->error['attribute_type'] = $this->language->get('error_required').': "attribute_type_id"';
        }
        // required
        if ((mb_strlen($data['name']) < 2) || (mb_strlen($data['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_attribute_name');
        }
        // not required
        if (mb_strlen($data['error_text']) > 255) {
            $this->error['error_text'] = $this->language->get('error_error_text');
        }
        // required
        if (empty($data['element_type'])) {
            $this->error['element_type'] = $this->language->get('error_required').': "element_type"';
        }
        if (has_value($data['regexp_pattern'])) {
            if (@preg_match($data['regexp_pattern'], "AbanteCart") === false) {
                $this->error['regexp_pattern'] = $this->language->get('error_regexp_pattern');
            }
        }
        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);
        return $this->error;
    }

    /**
     * @param int $attrId
     * @param array $data
     * @return array
     * @throws AException
     */
    public function validateAttributeValues(int $attrId, array $data)
    {

        $attrId = (int)$attrId;
        $this->error = [];
        $this->load->language('catalog/attribute');

        $txtIds = array_filter(array_map('trim', array_column($data, 'txt_id')));
        if( count($txtIds) != count(array_unique($txtIds)) ){
            $this->error['txt_id'] = $this->language->get('error_not_unique');
        }

        if(!$this->error && $txtIds) {
            $sql = "SELECT gv.*, gad.name 
                    FROM `" . $this->db->table("global_attributes_values") . "` gv
                    LEFT JOIN  `" . $this->db->table("global_attributes_descriptions") . "` gad
                        ON (gad.attribute_id = gv.attribute_id 
                            AND gad.language_id = '".(int)$this->language->getContentLanguageID()."')
                    WHERE `txt_id` IN ('" . implode("','", $txtIds) . "')";
            if ($attrId) {
                $sql .= " AND gv.attribute_id <> " . (int)$attrId;
            }
            $exists = $this->db->query($sql);
            if($exists->num_rows){
                $this->error['txt_id'] = $this->language->get('error_not_unique')." (";
                $dd = [];
                foreach($exists->rows as $row) {
                    $dd[] = '<a target="_blank" href="'.$this->html->getSecureUrl('catalog/attribute/update', '&attribute_id='.$row['attribute_id']).'">'.$row['name'].'</a>';
                }
                $this->error['txt_id'] .= implode(', ', $dd)." )";
            }
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);
        return $this->error;
    }
}