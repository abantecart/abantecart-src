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

class ModelToolFormsManager extends Model
{
    public $error = [];

    public function getFormFullInfo(int $formId)
    {
        if (!$formId) {
            return false;
        }
        $result = $this->getForm($formId);
        if ($result) {
            $result['fields'] = $this->getFields($formId);
        }
        return $result;
    }

    /**
     * @param int $formId
     * @return array
     * @throws AException
     */
    public function getForm(int $formId)
    {
        if ($formId) {
            $sql = 'SELECT f.*, fd.language_id, fd.description
				FROM ' . $this->db->table("forms") . ' f
				LEFT JOIN ' . $this->db->table("form_descriptions") . ' fd
				    ON f.form_id = fd.form_id
				WHERE f.form_id = "' . (int)$formId . '"
				    AND fd.language_id = "' . (int)$this->config->get('storefront_language_id') . '"';
            $results = $this->db->query($sql);
            return $results->row;
        }
        return [];
    }

    /**
     * @param int $form_id
     * @return array
     * @throws AException
     */
    public function getFields(int $form_id)
    {
        $languageId = (int)$this->config->get('storefront_language_id');
        $fields = [];
        $query = $this->db->query(
            "SELECT f.*, fd.name, fd.description
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd 
                ON ( f.field_id = fd.field_id AND fd.language_id = '" . $languageId . "' )
            WHERE f.form_id = '" . $form_id . "' AND f.status = 1
            ORDER BY f.sort_order"
        );

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $row['settings'] = $row['settings'] ? (array)unserialize($row['settings']) : [];
                $fieldId = (int)$row['field_id'];
                $fields[$fieldId] = $row;
                $query = $this->db->query(
                    "SELECT *
					FROM " . $this->db->table("field_values") . "
					WHERE field_id = " . $fieldId . " AND language_id = " . $languageId
                );
                if ($query->num_rows) {
                    $fields[$fieldId]['values'] = $query->rows;
                }
            }
        }
        return $fields;
    }

    /**
     * @param int $formId
     * @return array
     * @throws AException
     */
    public function getRequiredFields(int $formId)
    {
        if(!$formId){
            return [];
        }

        $query = $this->db->query("
            SELECT field_id, field_name
            FROM " . $this->db->table("fields") . "
            WHERE form_id = '" . (int)$formId . "'
                AND status = 1 AND required IN ('Y','1')
            ORDER BY sort_order"
        );
        return $query->rows;
    }
}