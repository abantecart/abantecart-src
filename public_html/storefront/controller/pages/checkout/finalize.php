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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesCheckoutFinalize extends AController
{
    public $errors = [];

    public function main()
    {
        $this->loadLanguage('checkout/fast_checkout');
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $orderId = (int)$this->session->data['order_id'];
        /** @var ModelAccountOrder $mdl */
        $mdl = $this->loadModel('account/order');
        $orderInfo = $mdl->getOrder($orderId);
        $order_totals = $mdl->getOrderTotals($orderId);
        if ($orderInfo) {
            $orderInfo['order_products'] = $mdl->getOrderProducts($orderId);
        }

        if ($orderId && $this->validate($orderInfo)) {
            //debit transaction
            $this->_debit_transaction($orderId);
            $orderInfo['totals'] = $order_totals;
            $this->view->assign('gaOrderData', AOrder::getGoogleAnalyticsOrderData($orderInfo));

            //clear session before redirect
            $this->clearOrderSession();

            //save order_id into session as processed order to allow one redirect
            $this->session->data['processed_order_id'] = $orderId;

            $this->extensions->hk_ProcessData($this);
            //Redirect back to load new page with cleared shopping cart content
            redirect($this->html->getSecureURL('checkout/fast_checkout_success'));
        } //when validation failed
        elseif ($orderId) {
            $this->session->data['processed_order_id'] = $orderId;
            $this->session->data['processing_order_errors'] = $this->errors;
        }

        //check if payment was processed
        if (!(int)$this->session->data['processed_order_id']) {
            redirect($this->html->getURL('index/home'));
        } else {
            redirect($this->html->getSecureURL('checkout/fast_checkout_success'));
        }
    }

    /**
     * Validating order data for different cases
     *
     * @param array $orderInfo
     *
     * @return bool
     * @throws AException
     */
    protected function validate(array $orderInfo)
    {
        $orderId = $orderInfo['order_id'];
        //when order exists but incomplete by some reasons - mark it as failed
        if ((int)$orderInfo['order_status_id'] == $this->order_status->getStatusByTextId('incomplete')) {
            $newStatusId = $this->order_status->getStatusByTextId('failed');
            /** @var ModelCheckoutOrder $mdl */
            $mdl = $this->loadModel('checkout/order');
            $mdl->confirm($orderId, $newStatusId);
            $this->_debit_transaction($orderId);
            $this->messages->saveWarning(
                sprintf($this->language->get('text_title_failed_order_to_admin'), $orderId),
                $this->language->get('text_message_failed_order_to_admin')
                . ' ' . '#admin#rt=sale/order/details&order_id=' . $orderId
            );
            $text_message = $this->language->get('text_message_failed_order');
            $this->errors[] = $text_message;
        }

        //perform additional custom order validation in extensions
        $this->extensions->hk_ValidateData($this);
        return !($this->errors);
    }

    /**
     * @param $order_id
     *
     * @return bool|null
     * @throws AException
     */
    protected function _debit_transaction($order_id)
    {
        // in default currency
        $amount = $this->session->data['used_balance'];
        if (!$amount) {
            return null;
        }
        $transaction_data = [
            'order_id'         => $order_id,
            'amount'           => $amount,
            'transaction_type' => 'order',
            'created_by'       => $this->customer->getId(),
            'description'      => sprintf($this->language->get('text_applied_balance_to_order'),
                $this->currency->format($this->currency->convert($amount,
                    $this->config->get('config_currency'),
                    $this->session->data['currency']),
                    $this->session->data['currency'], 1),
                $order_id),
        ];

        try {
            $this->customer->debitTransaction($transaction_data);
        } catch (AException $e) {
            $error = new AError(
                'Error: Debit transaction cannot be applied.'
                . var_export($transaction_data, true) . "\n"
                . $e->getMessage() . "\n"
                . $e->getFile());
            $error->toLog()->toMessages();
            return false;
        }
        return true;
    }

    /**
     * Method for purging session data related to order
     */
    protected function clearOrderSession()
    {
        //allow to clear custom data for extensions
        $this->extensions->hk_ProcessData($this, __FUNCTION__);

        $this->cart->clear();
        $this->customer->clearCustomerCart();
        unset(
            $this->session->data['shipping_method'],
            $this->session->data['shipping_method'],
            $this->session->data['shipping_methods'],
            $this->session->data['payment_method'],
            $this->session->data['payment_methods'],
            $this->session->data['guest'],
            $this->session->data['comment'],
            $this->session->data['order_id'],
            $this->session->data['coupon'],
            $this->session->data['used_balance'],
            $this->session->data['used_balance_full'],
        );
    }
}