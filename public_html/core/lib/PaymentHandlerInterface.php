<?php
/**
 * AbanteCart, Ideal Open Source Ecommerce Solution
 * http://www.abantecart.com
 *
 * Copyright 2011-2020 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details is bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 * Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 * versions in the future. If you wish to customize AbanteCart for your
 * needs please refer to http://www.abantecart.com for more information.
 */

interface PaymentHandlerInterface
{

    public function id() : string ;
    public function details() : array;

    public function is_available($payment_address) : bool;

    public function getErrors();

    public function processPayment(int $order_id, array $data = array()) : array;

    public function validatePaymentDetails(array $data) : array;

    public function callback(array $data = array());

}