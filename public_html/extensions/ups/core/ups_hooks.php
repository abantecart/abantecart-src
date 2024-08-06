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

class ExtensionUps extends Extension
{
    //disabled until shipping API will work
//    public function beforeModelCheckoutOrder_confirm()
//    {
//
//        $order_id = func_get_arg(0);
//        $order_status_id = func_get_arg(1);
//        /** @var ModelCheckoutOrder $that */
//        $that = $this->baseObject;
//        //if we do not create shipment at this point - skip
////        if ($order_status_id != $that->config->get('dhl_intl_manifest_order_status_id')) {
////            return null;
////        }
//
//        $order_info = $that->getOrder($order_id);
//        if (!$order_info
//            || $order_info['order_status_id'] != 0
//            || strpos($order_info['shipping_method_key'], 'ups.') === false) {
//            return null;
//        }
//
//        /**
//         * @var ModelExtensionUps $mdl
//         */
//        $mdl = $that->load->model('extension/ups');
//
//        $result = $mdl->createShipment($order_info);
//        if (!$result) {
//            $that->messages->saveWarning('Order ID ' . $order_id . ' Shipment Creation Warning',
//                implode("\n", $mdl->errors)
//            );
//            $that->log->write(
//                'Order ID '
//                . $order_id . ' Shipment Creation Warning: '
//                . implode("\n", $mdl->errors)
//            );
//        }
//
//    }

}