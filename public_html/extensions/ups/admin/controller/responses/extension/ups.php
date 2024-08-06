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

use UPS\OAuthClientCredentials\ApiException;
use function ups\core\getUPSAccessToken;
use function ups\core\validateAddress;

class ControllerResponsesExtensionUps extends AController
{

    public function main()
    {

    }

    public function test()
    {

        $this->loadLanguage('extension/extensions');

        if (!$this->user->canModify('extension/extensions')) {
            $error = new AError(sprintf($this->language->get('error_permission_modify'), 'extension/extensions'));
            $error->toJSONResponse(
                AC_ERR_USER_ERROR,
                [
                    'error_text' => sprintf(
                        $this->language->get('error_permission_modify'),
                        'extension/extensions'
                    )
                ]
            );
        }

        try {
            $this->request->post['test'] = true;
            getUPSAccessToken($this->registry, $this->request->post);
            /** @var ModelLocalisationCountry $mdl */
            $mdl = $this->loadModel('localisation/country');
            $country = $mdl->getCountry($this->request->post['ups_country']);
            /** @var ModelLocalisationZone $mdl */
            $mdl = $this->loadModel('localisation/zone');
            $zone = $mdl->getZone($this->request->post['ups_country_zone']);

            $address = [
                'PostcodePrimaryLow' => $this->request->post['ups_postcode'],
                'CountryCode'        => $country['iso_code_2'],
                'PoliticalDivision1' => $zone['code'],
                'PoliticalDivision2' => $this->request->post['ups_city'],
                'AddressLine'        => $this->request->post['ups_address'],
            ];
            if($this->request->post['ups_validate_address']) {
                //if all fine - check shipper address
                validateAddress($address);
            }

        } catch (\UPS\AddressValidation\ApiException $e) {
            $error = new AError('Shipper Address is invalid');
            $rBody = json_decode($e->getResponseBody(), true);
            $error->toJSONResponse(
                AC_ERR_USER_ERROR,
                ['error_text' => implode(". ", array_column($rBody['response']['errors'], 'message'))]
            );
            return;
        } catch (ApiException $e) {
            $error = new AError('Cannot obtain UPS token');
            $rBody = json_decode($e->getResponseBody(), true);
            $error->toJSONResponse(
                AC_ERR_USER_ERROR,
                [
                    'error_text' => is_array($rBody['response']['errors'])
                     ? implode(". ", array_column($rBody['response']['errors'], 'message'))
                     : $e->getMessage()
                ]
            );
            return;
        } catch (Exception $e) {
            $error = new AError('App Error');
            $error->toJSONResponse(
                AC_ERR_USER_ERROR,
                [
                    'error_text' => $e->getMessage()
                ]
            );
            return;
        }
        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode(['message' => 'Success! ']));
    }
}