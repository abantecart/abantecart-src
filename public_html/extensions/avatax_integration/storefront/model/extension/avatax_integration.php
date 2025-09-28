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
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
/** @noinspection PhpMultipleClassDeclarationsInspection */

use Avalara\AddressValidationInfo;
use Avalara\AvaTaxClient;

class ModelExtensionAvataxIntegration extends Model
{
    /* getProductTaxCode return string value taxCode of product for request tax to Avatax  */
    /**
     * @param int $productId
     * @return mixed|null
     * @throws AException
     */
    public function getProductTaxCode(int $productId): ?string
    {
        $query = $this->db->query(
            "SELECT taxcode_value 
            FROM " . $this->db->table("avatax_product_taxcode_values") . " 
            WHERE product_id=" . (int)$productId . " 
            LIMIT 1"
        );
        if ($query->row['taxcode_value']) {
            return $query->row['taxcode_value'];
        } else {
            return $this->config->get('avatax_integration_default_taxcode');
        }
    }

    /**
     * @param int $orderProductId
     * @param string $value
     * @return void
     * @throws AException
     */
    public function setOrderProductTaxCode(int $orderProductId, string $value): void
    {
        $this->db->query(
            "UPDATE " . $this->db->table("order_products") . " 
            SET taxcode_value='" . $this->db->escape($value) . "' 
            WHERE order_product_id=" . $orderProductId
        );
    }

    /**
     * @param int $customerId
     * @return array
     * @throws AException
     */
    public function getCustomerSettings(int $customerId): array
    {
        $query = $this->db->query(
            "SELECT * 
            FROM " . $this->db->table("avatax_customer_settings_values") . " 
            WHERE customer_id=" . $customerId . " 
            LIMIT 1"
        );
        return $query->row;
    }

    /**
     * @param int $customerId
     * @param array $data
     * @return bool
     * @throws AException
     */
    public function setCustomerSettings(int $customerId, array $data = []): bool
    {
        $sql = "SELECT * 
                FROM " . $this->db->table("avatax_customer_settings_values") . " 
                WHERE customer_id=" . $customerId;
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $sql = "UPDATE " . $this->db->table("avatax_customer_settings_values") . " 
                    SET exemption_number = '" . $this->db->escape($data['exemption_number']) . "',
                        entity_use_code  = '" . $this->db->escape($data['entity_use_code']) . "',
                        status = 0
                    WHERE customer_id=" . $customerId;
        } else {
            $sql = "INSERT INTO " . $this->db->table("avatax_customer_settings_values") . " 
                    SET customer_id=" . $customerId . ",
                        exemption_number = '" . $this->db->escape($data['exemption_number']) . "',
                        entity_use_code  = '" . $this->db->escape($data['entity_use_code']) . "',
                        status = 0";
        }
        $this->db->query($sql);
        return true;
    }

    /**
     * @param array $addressData
     * @return array
     * @throws AException
     */
    public function validateAddress(array $addressData): array
    {
        $output = [];
        if (!$addressData) {
            $output['message'] = 'Missing Address Data';
            $output['error'] = true;
            return $output;
        }

        $validCountries = $this->config->get('avatax_integration_address_validation_countries') === 'Both'
            ? ['US', 'CA']
            : explode(',', $this->config->get('avatax_integration_address_validation_countries'));

        if (is_numeric($addressData['address_id'])) {
            /** @var ModelAccountAddress $mdl */
            $mdl = $this->load->model('account/address','storefront');
            $addressData = $mdl->getAddress($addressData['address_id']);
        }

        if (!in_array($addressData['iso_code_2'] ?? '', $validCountries, true)) {
            $output['message'] = 'Avatax: address validation skipped. Country code is out of allowed list.';
            $output['error'] = false;
            return $output;
        }

        try {
            $accountNumber = $this->config->get('avatax_integration_account_number');
            $licenseKey = $this->config->get('avatax_integration_license_key');
            $testMode = $this->config->get('avatax_integration_test_mode') ? 'sandbox' : 'production';

            $client = new AvaTaxClient(
                'AbanteCart',
                VERSION,
                SERVER_NAME,
                $testMode
            );
            $client->withLicenseKey($accountNumber, $licenseKey);

            // Prepare request object
            $request = new AddressValidationInfo();
            $request->line1 = $addressData['address_1'] ?? '';
            $request->line2 = $addressData['address_2'] ?? '';
            $request->line3 = ''; // Optional, leave blank if not used
            $request->city = $addressData['city'] ?? '';
            $request->region = $addressData['code'] ?? ''; // State/Province code
            $request->country = $addressData['iso_code_2'] ?? ''; // Country code (e.g., US, CA)
            $request->postalCode = $addressData['postcode'] ?? '';
            $request->textCase = 'Mixed'; // Optional; keeps formatting (can be 'Upper', 'Mixed', or null)

            // Make the API call for address validation
            $response = $client->resolveAddressPost($request);
            $response = is_string($response) ? json_decode($response) : $response;
            // Log the request and response
            if ($this->config->get('avatax_integration_logging') === 1) {
                $requestLog = new AWarning('AvaTax Address Validation request: ' . var_export($request, true));
                $requestLog->toLog()->toDebug();
                $responseLog = new AWarning('AvaTax Address Validation response: ' . var_export($response, true));
                $responseLog->toLog()->toDebug();
            }

            // Analyze the response
            if ($response->validatedAddresses && !$response->messages) {
                $output['error'] = false;
            } else {
                $messages = $response->messages
                    ? array_column($response->messages,'summary')
                    : $response->errors;
                $output['message'] = implode("\n", $messages);
                $output['error'] = true;
            }
        } catch (Exception|Error $e) {
            $output['message'] = $e->getMessage();
            $output['error'] = true;
            if ($this->config->get('avatax_integration_logging')) {
                $errorLog = new AWarning('AVALARA API Address Validation Error: ' . $e->getMessage());
                $errorLog->toLog()->toDebug();
            }
        }
        return $output;
    }
}