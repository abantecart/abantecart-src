<?php
/** @noinspection PhpUndefinedClassInspection */

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ExtensionDefaultStorePickup extends Extension
{
    /**
     * @return bool
     */
    protected function isEnabled()
    {
        return ($this->baseObject->config->get('default_store_pickup_status'));
    }

    protected function removeStoreAddressFromAddressBook()
    {
        $that = $this->baseObject;
        /** @var ModelAccountAddress $mdl */
        $mdl = $that->loadModel('account/address');
        $allAddresses = $mdl->getAddresses();
        $exists = false;
        foreach ($allAddresses as $address) {
            if (
                $address['postcode'] == $that->config->get('config_postcode')
                && $address['country_id'] == $that->config->get('config_country_id')
                && $address['zone_id'] == $that->config->get('config_zone_id')
            ) {
                $id = $address['address_id'];
                $exists = true;
                break;
            }
        }
        if ($exists) {
            $mdl->deleteAddress( $id );
            $that->session->data['shipping_address_id'] = $that->customer->getAddressId();
        }
    }

    public function onControllerPagesCheckoutShipping_ProcessData()
    {
        $that = $this->baseObject;
        if (!$this->isEnabled()) {
            return;
        }
        if ($that->request->post['shipping_method'] != 'default_store_pickup.default_store_pickup'
            || !$that->cart->hasShipping()
        ) {
            $this->removeStoreAddressFromAddressBook();
            return;
        }

        //save store address to customer address list
        /** @var ModelAccountAddress $mdl */
        $mdl = $that->loadModel('account/address');
        $allAddresses = $mdl->getAddresses();
        $exists = false;
        foreach ($allAddresses as $address) {
            if (
                $address['postcode'] == $that->config->get('config_postcode')
                && $address['country_id'] == $that->config->get('config_country_id')
                && $address['zone_id'] == $that->config->get('config_zone_id')
            ) {
                $that->session->data['shipping_address_id'] = $address['address_id'];
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $that->session->data['shipping_address_id'] = $mdl->addAddress(
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

        $that->tax->setZone(
            $that->config->get('config_country_id'),
            $that->config->get('config_zone_id')
        );
    }

    public function onControllerPagesCheckoutGuestStep2_ProcessData()
    {
        $that = $this->baseObject;
        if (!$this->isEnabled()) {
            return;
        }
        if ($that->request->post['shipping_method'] != 'default_store_pickup.default_store_pickup'
            || !$that->cart->hasShipping()
        ) {
            return;
        }

        $that->session->data['guest']['shipping']['firstname'] = $that->session->data['guest']['shipping']['firstname']
            ? : $that->session->data['guest']['firstname'];

        $that->session->data['guest']['shipping']['lastname'] = $that->session->data['guest']['shipping']['lastname']
            ? : $that->session->data['guest']['lastname'];

        $that->session->data['guest']['shipping']['address_1'] = $that->config->get('config_address');
        $that->session->data['guest']['shipping']['postcode'] = $that->config->get('config_postcode');
        $that->session->data['guest']['shipping']['city'] = $that->config->get('config_city');
        $that->session->data['guest']['shipping']['country_id'] = $that->config->get('config_country_id');
        $that->session->data['guest']['shipping']['zone_id'] = $that->config->get('config_zone_id');

        $that->tax->setZone(
            $that->config->get('config_country_id'),
            $that->config->get('config_zone_id')
        );
    }

    public function onControllerResponsesCheckoutPay_InitData()
    {
        $that = $this->baseObject;
        if (!$this->isEnabled()) {
            return;
        }
        if (($that->request->get['shipping_method'] != 'default_store_pickup.default_store_pickup'
                && $that->session->data['fc']['shipping_method']['id'] != 'default_store_pickup.default_store_pickup')
            || !$that->cart->hasShipping()
        ) {
            return;
        }
        if ($that->customer->isLogged()) {
            //save store address to customer address list
            /** @var ModelAccountAddress $mdl */
            $mdl = $that->loadModel('account/address');
            $allAddresses = $mdl->getAddresses();
            $exists = false;
            foreach ($allAddresses as $address) {
                if (
                    $address['postcode'] == $that->config->get('config_postcode')
                    && $address['country_id'] == $that->config->get('config_country_id')
                    && $address['zone_id'] == $that->config->get('config_zone_id')
                ) {
                    $that->request->get['shipping_address_id'] =
                    $that->session->data['fc']['shipping_address_id'] = $address['address_id'];
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $that->request->get['shipping_address_id'] =
                $that->session->data['fc']['shipping_address_id'] =
                    $mdl->addAddress(
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
            $that->session->data['fc']['guest']['shipping']['firstname'] =
                $that->session->data['fc']['guest']['shipping']['firstname']
                    ? : $that->session->data['fc']['guest']['firstname'];

            $that->session->data['fc']['guest']['shipping']['lastname'] =
                $that->session->data['fc']['guest']['shipping']['lastname']
                    ? : $that->session->data['fc']['guest']['lastname'];

            $that->session->data['fc']['guest']['shipping']['address_1'] = $that->config->get('config_address');
            $that->session->data['fc']['guest']['shipping']['postcode'] = $that->config->get('config_postcode');
            $that->session->data['fc']['guest']['shipping']['city'] = $that->config->get('config_city');
            $that->session->data['fc']['guest']['shipping']['country_id'] = $that->config->get('config_country_id');
            $that->session->data['fc']['guest']['shipping']['zone_id'] = $that->config->get('config_zone_id');
        }

        $that->tax->setZone(
            $that->config->get('config_country_id'),
            $that->config->get('config_zone_id')
        );
    }

    public function onControllerPagesCheckoutSuccess_InitData()
    {
        if(!$this->isEnabled()){
            return;
        }

        $that =& $this->baseObject;
        $this->removeStoreAddressFromAddressBook();
        unset($that->session->data['shipping_address_id']);
    }

}
