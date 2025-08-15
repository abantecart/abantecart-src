<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection PhpUndefinedClassInspection */

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
 * Class ModelAccountAddress
 *
 * @property ModelLocalisationCountry $model_localisation_country
 * @property ModelLocalisationZone $model_localisation_zone
 */
class ModelAccountAddress extends Model
{
    public $error = [];

    /**
     * @param $data
     *
     * @return int
     * @throws AException
     */
    public function addAddress($data = [])
    {
        if (!$data) {
            return false;
        }
        //encrypt customer data
        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $key_sql = ", key_id = '".(int) $data['key_id']."'";
        }
        $customerId = (int)$data['customer_id'] ?: (int) $this->customer->getId();
        if(!$customerId){
            throw new AException(AC_ERR_USER_ERROR,'Cannot add new address. Customer ID is unknown');
        }
        $this->db->query(
            "INSERT INTO `".$this->db->table("addresses")."`
            SET customer_id = '".$customerId."',
                company = '".$this->db->escape($data['company'])."',
                firstname = '".$this->db->escape($data['firstname'])."',
                lastname = '".$this->db->escape($data['lastname'])."',
                address_1 = '".$this->db->escape($data['address_1'])."',
                address_2 = '".$this->db->escape($data['address_2'])."',
                postcode = '".$this->db->escape($data['postcode'])."',
                city = '".$this->db->escape($data['city'])."',
                zone_id = '".(int) $data['zone_id']."',
                country_id = '".(int) $data['country_id']."'".$key_sql
        );

        $address_id = $this->db->getLastId();

        if (isset($data['default']) && $data['default'] == '1') {
            $this->db->query(
                "UPDATE ".$this->db->table("customers")."
                SET address_id = '".(int) $address_id."'
                WHERE customer_id = '".$customerId."'"
            );
        }

        return $address_id;
    }

    /**
     * @param int $address_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editAddress($address_id, $data)
    {
        $address_id = (int) $address_id;
        if (!$address_id || !$data) {
            return false;
        }

        //encrypt customer data
        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $key_sql = ", key_id = '".(int) $data['key_id']."'";
        }

        $columns = [
            'company' => 'string',
            'firstname' => 'string',
            'lastname' => 'string',
            'address_1' => 'string',
            'address_2' => 'string',
            'postcode' => 'string',
            'city' => 'string',
            'zone_id' => 'integer',
            'country_id' => 'integer'
        ];

        $updArray = [];
        foreach($columns as $column => $type)
        {
            if(isset($data[$column])) {
                $updArray[] = $column . " = '" . ($type == 'string' ? $this->db->escape($data[$column]) : intval($data[$column])). "'";
            }
        }

        $extFields = array_diff(
            array_keys($data),
            array_merge(array_keys($columns), ['customer_id','address_id','csrftoken','csrfinstance','default'] )
        );
        if($extFields) {
            foreach($extFields as $extFieldName) {
                $extArray[$extFieldName] = $data[$extFieldName];
            }
            $updArray[] = "ext_fields = '".$this->db->escape(json_encode($extArray))."'";
        }

        if($updArray) {
            $this->db->query(
                "UPDATE " . $this->db->table("addresses") . "
                SET ". implode(', ', $updArray) ." " . $key_sql . "
                WHERE address_id  = '" . (int)$address_id . "' 
                    AND customer_id = '" . (int)$this->customer->getId() . "'"
            );
        }

        if (isset($data['default'])) {
            $this->db->query(
                "UPDATE ".$this->db->table("customers")."
                    SET address_id = '".(int) $address_id."'
                    WHERE customer_id = '".(int) $this->customer->getId()."'"
            );
        }
        return true;
    }

    /**
     * @param int $address_id
     *
     * @throws AException
     */
    public function deleteAddress($address_id)
    {
        $this->db->query(
            "DELETE FROM ".$this->db->table("addresses")."
            WHERE address_id = '".(int) $address_id."' 
                AND customer_id = '".(int) $this->customer->getId()."'"
        );
    }

    /**
     * @param int $address_id
     *
     * @return array|bool
     * @throws AException
     */
    public function getAddress($address_id)
    {
        $address_query = $this->db->query(
            "SELECT DISTINCT *
             FROM ".$this->db->table("addresses")."
             WHERE address_id = '".(int) $address_id."' 
                AND customer_id = '".(int) $this->customer->getId()."'"
        );

        if ($address_query->num_rows) {
            return $this->_build_address_data($address_query->row);
        } else {
            return false;
        }
    }

    /**
     * @return array
     * @throws AException
     */
    public function getAddresses()
    {
        $address_data = [];

        $query = $this->db->query(
            "SELECT *
            FROM ".$this->db->table("addresses")."
            WHERE customer_id = '".(int) $this->customer->getId()."'"
        );

        foreach ($query->rows as $result) {
            $address_data[] = $this->_build_address_data($result);
        }
        return $address_data;
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalAddresses()
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
            FROM ".$this->db->table("addresses")."
            WHERE customer_id = '".(int) $this->customer->getId()."'"
        );
        return (int) $query->row['total'];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function validateAddressData($data)
    {
        $this->error = [];
        if (mb_strlen($data['firstname']) < 1 || mb_strlen($data['firstname']) > 32) {
            $this->error['firstname'] = $this->language->get('error_firstname','account/address');
        }

        if (mb_strlen($data['lastname']) < 1 || mb_strlen($data['lastname']) > 32) {
            $this->error['lastname'] = $this->language->get('error_lastname','account/address');
        }

        if (mb_strlen($data['address_1']) < 3 || mb_strlen($data['address_1']) > 128) {
            $this->error['address_1'] = $this->language->get('error_address_1','account/address');
        }

        if (mb_strlen($data['city']) < 3 || mb_strlen($data['city']) > 128) {
            $this->error['city'] = $this->language->get('error_city','account/address');
        }

        if (mb_strlen($data['postcode']) < 3 || mb_strlen($data['postcode']) > 10) {
            $this->error['postcode'] = $this->language->get('error_postcode','account/address');
        }

        if ($data['country_id'] == 'FALSE' || $data['country_id'] == '' ) {
            $this->error['country'] = $this->language->get('error_country','account/address');
        }

        if ($data['zone_id'] == 'FALSE' || $data['zone_id'] == '') {
            $this->error['zone'] = $this->language->get('error_zone','account/address');
        }

        if (!$this->error && (int) $data['zone_id'] !== 0) {
            $sql = "SELECT * 
                    FROM ".$this->db->table("zones")."
                    WHERE country_id = '".(int) $data['country_id']."'
                        AND zone_id = '".(int) $data['zone_id']."';";
            $result = $this->db->query($sql);
            if (!$result->num_rows) {
                $this->error['zone'] = $this->language->get('error_zone','account/address');
            }
        }

        if (count($this->error)) {
            $this->error['warning'] = $this->language->get('gen_data_entry_error');
        }

        $this->extensions->hk_ValidateData($this, [ 'address' => $data ] );

        return $this->error;
    }

    /**
     * @param array $address_row
     *
     * @return array
     * @throws AException
     */
    protected function _build_address_data($address_row)
    {
        $addr_row = $this->dcrypt->decrypt_data($address_row, 'addresses');

        $this->load->model('localisation/country');
        $this->load->model('localisation/zone');
        $country_row = $this->model_localisation_country->getCountry($addr_row['country_id']);

        if ($country_row) {
            $country = $country_row['name'];
            $iso_code_2 = $country_row['iso_code_2'];
            $iso_code_3 = $country_row['iso_code_3'];
            $address_format = $country_row['address_format'];
        } else {
            $country = '';
            $iso_code_2 = '';
            $iso_code_3 = '';
            $address_format = '';
        }

        $zone_row = $this->model_localisation_zone->getZone($addr_row['zone_id']);

        if ($zone_row) {
            $zone = $zone_row['name'];
            $code = $zone_row['code'];
        } else {
            $zone = '';
            $code = '';
        }
        $output = $addr_row;
        $output['ext_fields'] = json_decode($output['ext_fields'], JSON_PRETTY_PRINT);
        $output['country'] = $country;
        $output['zone'] = $zone;
        $output['code'] = $code;
        $output['format'] = $address_format;
        $output['iso_code_2'] = $iso_code_2;
        $output['iso_code_3'] = $iso_code_3;
        return $output;
    }
}