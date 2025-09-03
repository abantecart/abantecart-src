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

class ModelAccountAddress extends Model
{
    public $error = [];

    public function __construct($registry)
    {
        parent::__construct($registry);
        //this list can be changed from hook beforeModelModelCheckoutOrder
        $this->data['address_column_list'] = [
            'company'     => 'string',
            'firstname'   => 'string',
            'lastname'    => 'string',
            'address_1'   => 'string',
            'address_2'   => 'string',
            'postcode'    => 'string',
            'city'        => 'string',
            'zone_id'     => 'int',
            'country_id'  => 'int',
            'customer_id' => 'int',
        ];
    }

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

        $customerId = (int)$data['customer_id'] ?: (int)$this->customer->getId();
        if (!$customerId) {
            throw new AException(AC_ERR_USER_ERROR, 'Cannot add new address. Customer ID is unknown');
        }

        //encrypt customer data
        $insertArr = [ 'customer_id' => '`customer_id` = '.$customerId ];
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $insertArr['key_id'] = "`key_id` = " . (int)$data['key_id'];
        }

        //prepare data to insert into customer table
        foreach ($this->data['address_column_list'] as $key => $dataType) {
            if (!isset($data[$key]) || isset($insertArr[$key])) {
                continue;
            }
            if ($dataType == 'int') {
                $value = (int)$data[$key];
            } elseif ($dataType == 'float') {
                $value = (float)$data[$key];
            } elseif ($dataType == 'string') {
                $value = $this->db->escape(trim($data[$key]));
            } else {
                $value = $this->db->escape(serialize($data[$key]));
            }
            $insertArr[$key] = "`" . $key . "` = '" . $value . "'";
        }
        //prepare extended fields values
        $extFields = [];
        //merge generic column list for both tables (customer + address) to avoid double saving
        $colNames = array_keys($this->data['address_column_list']);

        foreach ($data as $key => $value) {
            if (in_array($key, ['csrftoken', 'csrfinstance'])) {
                continue;
            }
            if (in_array($key, $colNames)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (in_array($subKey, $colNames)) {
                        continue;
                    }
                    $extFields[$key][$subKey] = $subValue;
                }
            } else {
                $extFields[$key] = $value;
            }
        }
        if ($extFields) {
            $insertArr['ext_fields'] = "`ext_fields` = '" . json_encode($extFields, JSON_UNESCAPED_UNICODE) . "'";
        }
        $this->db->query(
            "INSERT INTO `" . $this->db->table("addresses") . "`
            SET " . implode(', ', $insertArr)
        );
        $address_id = $this->db->getLastId();

        if (isset($data['default']) && $data['default'] == '1') {
            $this->db->query(
                "UPDATE " . $this->db->table("customers") . "
                SET address_id = '" . (int)$address_id . "'
                WHERE customer_id = '" . $customerId . "'"
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
        $address_id = (int)$address_id;
        if (!$address_id || !$data) {
            return false;
        }

        $updateArr = [];

        //encrypt customer data
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $updateArr[] = "`key_id` = '" . (int)$data['key_id'] . "'";
        }

        foreach ($this->data['address_column_list'] as $key => $dataType) {
            if (!isset($data[$key])) {
                continue;
            }
            if ($dataType == 'int') {
                $value = (int)$data[$key];
            } elseif ($dataType == 'float') {
                $value = (float)$data[$key];
            } elseif ($dataType == 'string') {
                $value = $this->db->escape(trim($data[$key]));
            } else {
                $value = $this->db->escape(serialize($data[$key]));
            }
            $updateArr[] = "`" . $key . "` = '" . $value . "'";
        }

        $extFields = array_diff(
            array_keys($data),
            array_merge(
                array_keys($this->data['address_column_list']),
                ['customer_id', 'address_id', 'csrftoken', 'csrfinstance', 'default']
            )
        );
        if ($extFields) {
            //get prior data to prevent overriding
            $priorData = $this->getAddress($address_id);
            $extArray = (array)$priorData['ext_fields'];
            foreach ($extFields as $extFieldName) {
                $extArray[$extFieldName] = $data[$extFieldName];
            }
            $updateArr[] = "ext_fields = '" . $this->db->escape(json_encode($extArray)) . "'";
        }

        if ($updateArr) {
            $this->db->query(
                "UPDATE " . $this->db->table("addresses") . "
                SET " . implode(', ', $updateArr) . "
                WHERE address_id  = '" . (int)$address_id . "' 
                    AND customer_id = '" . (int)$this->customer->getId() . "'"
            );
        }

        if (isset($data['default'])) {
            $this->db->query(
                "UPDATE " . $this->db->table("customers") . "
                    SET address_id = '" . (int)$address_id . "'
                    WHERE customer_id = '" . (int)$this->customer->getId() . "'"
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
            "DELETE FROM " . $this->db->table("addresses") . "
            WHERE address_id = '" . (int)$address_id . "' 
                AND customer_id = '" . (int)$this->customer->getId() . "'"
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
             FROM " . $this->db->table("addresses") . "
             WHERE address_id = '" . (int)$address_id . "' 
                AND customer_id = '" . (int)$this->customer->getId() . "'"
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
            FROM " . $this->db->table("addresses") . "
            WHERE customer_id = '" . (int)$this->customer->getId() . "'"
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
            FROM " . $this->db->table("addresses") . "
            WHERE customer_id = '" . (int)$this->customer->getId() . "'"
        );
        return (int)$query->row['total'];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     * @deprecated since 1.4.3
     */
    public function validateAddressData($data)
    {
        $this->error = [];
        if (mb_strlen($data['firstname']) < 1 || mb_strlen($data['firstname']) > 32) {
            $this->error['firstname'] = $this->language->get('error_firstname', 'account/address');
        }

        if (mb_strlen($data['lastname']) < 1 || mb_strlen($data['lastname']) > 32) {
            $this->error['lastname'] = $this->language->get('error_lastname', 'account/address');
        }

        if (mb_strlen($data['address_1']) < 3 || mb_strlen($data['address_1']) > 128) {
            $this->error['address_1'] = $this->language->get('error_address_1', 'account/address');
        }

        if (mb_strlen($data['city']) < 3 || mb_strlen($data['city']) > 128) {
            $this->error['city'] = $this->language->get('error_city', 'account/address');
        }

        if (mb_strlen($data['postcode']) < 3 || mb_strlen($data['postcode']) > 10) {
            $this->error['postcode'] = $this->language->get('error_postcode', 'account/address');
        }

        if ($data['country_id'] == 'FALSE' || $data['country_id'] == '') {
            $this->error['country'] = $this->language->get('error_country', 'account/address');
        }

        if ($data['zone_id'] == 'FALSE' || $data['zone_id'] == '') {
            $this->error['zone'] = $this->language->get('error_zone', 'account/address');
        }

        if (!$this->error && (int)$data['zone_id'] !== 0) {
            $sql = "SELECT * 
                    FROM " . $this->db->table("zones") . "
                    WHERE country_id = '" . (int)$data['country_id'] . "'
                        AND zone_id = '" . (int)$data['zone_id'] . "';";
            $result = $this->db->query($sql);
            if (!$result->num_rows) {
                $this->error['zone'] = $this->language->get('error_zone', 'account/address');
            }
        }

        if (count($this->error)) {
            $this->error['warning'] = $this->language->get('gen_data_entry_error');
        }

        $this->extensions->hk_ValidateData($this, ['address' => $data]);

        return $this->error;
    }

    /**
     * @param array $address
     *
     * @return array
     * @throws AException
     */
    protected function _build_address_data($address)
    {
        $addressArr = $this->dcrypt->decrypt_data($address, 'addresses');

        /** @var ModelLocalisationCountry $cMdl */
        $cMdl = $this->load->model('localisation/country');
        /** @var ModelLocalisationZone $zMdl */
        $zMdl = $this->load->model('localisation/zone');
        $countryInfo = $cMdl->getCountry($addressArr['country_id']);
        $zoneInfo = $zMdl->getZone($addressArr['zone_id']);

        $output = $addressArr;
        $output['ext_fields'] = json_decode($output['ext_fields'], true);
        $output['country'] = $countryInfo['name'] ?? '';
        $output['zone'] = $zoneInfo['name'] ?? '';
        $output['code'] = $zoneInfo['code'] ?? '';
        $output['format'] = $countryInfo['address_format'] ?? DEFAULT_ADDRESS_FORMAT;
        //backward compatibility. Todo: remove in 1.5.*
        $output['address_format'] = $output['format'];

        $output['iso_code_2'] = $countryInfo['iso_code_2'] ?? '';
        $output['iso_code_3'] = $countryInfo['iso_code_3'] ?? '';
        return $output;
    }
}