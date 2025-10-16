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

class ExtensionDefaultStorePickup extends Extension
{
    /**
     * @return bool
     * @throws AException
     */
    const SHP_TXT_ID = 'default_store_pickup.default_store_pickup';

    protected function isEnabled()
    {
        return ($this->baseObject->config->get('default_store_pickup_status'));
    }

    protected function removeStoreAddressFromAddressBook()
    {
        $id = 0;
        $that = $this->baseObject;
        /** @var ModelAccountAddress $mdl */
        $mdl = $that->loadModel('account/address');
        $allAddresses = $mdl->getAddresses();
        foreach ($allAddresses as $address) {
            if ($address['postcode'] == $that->config->get('config_postcode')
                && $address['country_id'] == $that->config->get('config_country_id')
                && $address['zone_id'] == $that->config->get('config_zone_id')
                && $address['address_1'] == $that->config->get('config_address')
            ) {
                $id = $address['address_id'];
                break;
            }
        }
        if ($id) {
            $mdl->deleteAddress($id);
            $that->session->data['shipping_address_id'] = $that->customer->getAddressId();
        }
    }

    public function onControllerResponsesCheckoutPay_InitData()
    {
        $that = $this->baseObject;
        if (!$this->isEnabled()
            || !isset($that->request->get['shipping_method'])
            || $this->baseObject_method == '__construct'
        ) {
            return;
        }

        if (($that->request->get['shipping_method'] != self::SHP_TXT_ID
                && $that->session->data['fc']['shipping_method']['id'] != self::SHP_TXT_ID)
            || !$that->cart->hasShipping()
            || $this->baseObject_method == 'success'
        ) {
            return;
        }

        //if switch from pickup-store to another - recover first filled shipping address
        if( isset($that->request->get['shipping_method'])
            && $that->request->get['shipping_method'] != self::SHP_TXT_ID
            && $that->session->data['fc']['shipping_method']['id'] == self::SHP_TXT_ID
            && $that->session->data['fc']['guest']['prior_shipping_address']
        ){
            $that->session->data['fc']['guest']['shipping'] = $that->session->data['fc']['guest']['prior_shipping_address'];
            unset(
                $that->session->data['fc']['guest']['shipping']['zone_id'],
                $that->session->data['fc']['guest']['shipping']['zone'],
                $that->session->data['fc']['guest']['shipping']['zone_code'],
                $that->session->data['fc']['guest']['shipping']['country_id'],
                $that->session->data['fc']['guest']['shipping']['country'],
                $that->session->data['fc']['guest']['shipping']['iso_code_2'],
                $that->session->data['fc']['guest']['shipping']['iso_code_3'],
                $that->session->data['fc']['guest']['prior_shipping_address']
            );
            return;
        }

        if ($that->customer->isLogged()) {
            //save store address to customer address list
            /** @var ModelAccountAddress $mdl */
            $mdl = $that->loadModel('account/address');
            $allAddresses = $mdl->getAddresses();
            $exists = false;
            foreach ($allAddresses as $address) {
                if ($address['postcode'] == $that->config->get('config_postcode')
                    && $address['country_id'] == $that->config->get('config_country_id')
                    && $address['zone_id'] == $that->config->get('config_zone_id')
                    && $address['address_1'] == $that->config->get('config_address')
                ) {
                    $that->request->get['shipping_address_id']
                        = $that->session->data['fc']['shipping_address_id']
                        = $address['address_id'];
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $that->request->get['shipping_address_id']
                    = $that->session->data['fc']['shipping_address_id']
                    = $mdl->addAddress(
                    [
                        'address_1'  => $that->config->get('config_address'),
                        'country_id' => $that->config->get('config_country_id'),
                        'zone_id'    => $that->config->get('config_zone_id'),
                        'postcode'   => $that->config->get('config_postcode'),
                        'firstname'  => $that->customer->getFirstName(),
                        'lastname'   => $that->customer->getLastName(),
                        'city'       => $that->config->get('config_city'),
                    ]
                );
            }
        } else {
            $sGuest = $that->session->data['fc']['guest'];
            $sGuest['prior_shipping_address'] = $sGuest['shipping'];
            $sGuest['shipping']['firstname'] = $sGuest['shipping']['firstname'] ?: $sGuest['firstname'];
            $sGuest['shipping']['lastname'] = $sGuest['shipping']['lastname'] ?: $sGuest['lastname'];
            $sGuest['shipping']['address_1'] = $that->config->get('config_address');
            $sGuest['shipping']['postcode'] = $that->config->get('config_postcode');
            $sGuest['shipping']['city'] = $that->config->get('config_city');
            $sGuest['shipping']['country_id'] = $that->config->get('config_country_id');
            $that->load->model('localisation/country');
            $countryInfo = $that->model_localisation_country->getCountry($sGuest['shipping']['country_id']);
            $sGuest['shipping']['country'] = $countryInfo['name'];
            $sGuest['shipping']['iso_code_2'] = $countryInfo['iso_code_2'];
            $sGuest['shipping']['iso_code_3'] = $countryInfo['iso_code_3'];

            $sGuest['shipping']['zone_id'] = $that->config->get('config_zone_id');
            $that->load->model('localisation/zone');
            $zoneInfo = $that->model_localisation_zone->getZone($sGuest['shipping']['zone_id']);
            $sGuest['shipping']['zone'] = $zoneInfo['name'];
            $sGuest['shipping']['zone_code'] = $zoneInfo['code'];
            $that->session->data['fc']['guest'] = array_merge((array)$that->session->data['fc']['guest'], $sGuest);
        }

        $that->tax->setZone(
            $that->config->get('config_country_id'),
            $that->config->get('config_zone_id')
        );
    }

    public function onControllerPagesCheckoutFinalize_InitData()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $that =& $this->baseObject;
        $this->removeStoreAddressFromAddressBook();
        unset($that->session->data['shipping_address_id']);
    }

}
