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

namespace ups\core;

use AException;
use Exception;
use GuzzleHttp\Client;
use ModelSettingSetting;
use Registry;
use UPS\AddressValidation\AddressValidation\XAVRequest;
use UPS\AddressValidation\AddressValidation\XAVRequestAddressKeyFormat;
use UPS\AddressValidation\AddressValidation\XAVRequestWrapper;
use UPS\AddressValidation\ApiException;
use UPS\OAuthClientCredentials\Configuration;
use UPS\OAuthClientCredentials\Request\DefaultApi;


/**
 * @param Registry $registry
 * @param $options
 * @return false|mixed|string|null
 * @throws AException
 * @throws \UPS\OAuthClientCredentials\ApiException
 */
function getUPSAccessToken(Registry $registry, $options = [] )
{

    $config = $registry->get('config');

    if($options['test'] || !$config->get('ups_access_token') || $config->get('ups_access_token_expire') < time()){
        $accNumber = $options['ups_account_number'] ?: $config->get('ups_account_number');
        $clientId = $options['ups_client_id'] ?: $config->get('ups_client_id');
        $password = $options['ups_password'] ?: $config->get('ups_password');

        $configuration = Configuration::getDefaultConfiguration()
            ->setUsername($clientId)
            ->setPassword($password);

        $apiInstance = new DefaultApi(new Client(),$configuration);
        $result = $apiInstance->createToken("client_credentials", $accNumber);

        $store_id = $registry->get('config')->get('current_store_id');
        $db = $registry->get('db');

        $sql = "DELETE FROM ".$registry->get('db')->table("settings")." 
                WHERE `group` = 'ups'
                    AND `key` IN ('ups_access_token','ups_access_token_expire')
                    AND `store_id` = '".$store_id."'";
        $db->query($sql);

        $sql = "INSERT INTO ".$db->table("settings")." 
                    ( `store_id`, `group`, `key`, `value`, `date_added`)
                VALUES (  '".$store_id."',
                          'ups',
                          'ups_access_token',
                          '".$db->escape($result['access_token'])."',
                          NOW()),
                      (  '".$store_id."',
                          'ups',
                          'ups_access_token_expire',
                          '".$db->escape(time() + $result['expires_in'])."',
                          NOW())";
        $db->query($sql);
        $registry->get('cache')->remove('settings');
        return $result['access_token'];
    }else{
        return $config->get('ups_access_token');
    }
}

/**
 * @param array $address
 * @throws AException
 * @throws ApiException
 * @throws \UPS\OAuthClientCredentials\ApiException
 */
function validateAddress(array $address)
{
    $accessToken = getUPSAccessToken( Registry::getInstance());
    $config = \UPS\AddressValidation\Configuration::getDefaultConfiguration()->setAccessToken($accessToken);

    $apiInstance = new \UPS\AddressValidation\Request\DefaultApi( new Client(),$config );

    $body = new XAVRequestWrapper();
    $xavRequest = new XAVRequest();
    $xavRequest->setRegionalRequestIndicator('True');
    $addressKeyFormat = new XAVRequestAddressKeyFormat();
    $addressKeyFormat->setCountryCode($address['CountryCode'])
        ->setPostcodePrimaryLow($address['PostcodePrimaryLow'])
        ->setPoliticalDivision1($address['PoliticalDivision1'])
        ->setPoliticalDivision2($address['PoliticalDivision2'])
        ->setAddressLine([$address['AddressLine']]);

    $xavRequest->setAddressKeyFormat([$addressKeyFormat]);
    $body->setXavRequest( $xavRequest );
    $requestoption = 1;
    $version = "v1";
    $regionalrequestindicator = 'True'   ; //check street level
    $maximumcandidatelistsize = 15;

    $apiInstance->addressValidation($body, $requestoption, $version, $regionalrequestindicator, $maximumcandidatelistsize);
}