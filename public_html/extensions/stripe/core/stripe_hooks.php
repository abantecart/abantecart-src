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

use Stripe\Charge;

class ExtensionStripe extends Extension
{
    protected $registry;
    protected $r_data;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    protected function _is_enabled($that)
    {
        return $that->config->get('stripe_status');
    }

    //Hook to extension edit in the admin
    public function onControllerPagesExtensionExtensions_UpdateData()
    {
        $that = $this->baseObject;
        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN === true && $current_ext_id == 'stripe' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" target="_blank" href="https://www.stripe.com" title="Visit stripe">
                        <i class="fa fa-external-link fa-lg"></i>
                    </a>';
            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }
    }

    //Hook to enable payment details tab in admin
    public function onControllerPagesSaleOrderTabs_UpdateData()
    {
        $that = $this->baseObject;
        $order_id = $that->data['order_id'];
        //are we logged in and in admin?
        if (IS_ADMIN && $that->user->isLogged()) {
            //check if tab is not yet enabled.
            if (in_array('payment_details', $that->data['groups'])) {
                return null;
            }
            //check if we this order is used stripe payment
            $that->loadModel('extension/stripe');
            $this->_load_stripe_order_data($order_id, $that);
            if (!$this->r_data) {
                return;
            }
            $that->data['groups'][] = 'payment_details';
            $that->data['link_payment_details'] = $that->html->getSecureURL(
                'sale/order/payment_details',
                '&order_id=' . $order_id . '&extension=stripe'
            );
            //reload main view data with updated tab
            $that->view->batchAssign($that->data);
        }
    }

    //Hook to payment details page to show information
    public function onControllerPagesSaleOrder_UpdateData()
    {
        $that = $this->baseObject;
        $refunds = [];

        $order_id = $that->request->get['order_id'];
        //are we logged to admin and correct method called?
        if (IS_ADMIN && $that->user->isLogged() && $this->baseObject_method == 'payment_details') {

            if ($that->request->get['extension'] != 'stripe') {
                return null;
            }

            //build HTML to show
            $that->loadLanguage('stripe/stripe');
            /** @var ModelExtensionStripe $mdl */
            $mdl = $that->loadModel('extension/stripe');
            if (!$this->r_data) {
                //no local stripe order data yet. load it.
                $this->_load_stripe_order_data($order_id, $that);
            }

            if (!$this->r_data) {
                return;
            }

            $view = new AView($this->registry, 0);
            //get remote charge data
            $ch_data = $mdl->getStripeCharge($this->r_data['charge_id']);

            if (!$ch_data) {
                $view->assign('error_warning', "Some error happened!. Check the error log for more details.");
            } elseif ($ch_data instanceof Charge) {

                $ch_data['amount'] = round($ch_data['amount'] / 100, 2);
                $ch_data['amount_refunded'] = round($ch_data['amount_refunded'] / 100, 2);
                $ch_data['amount_formatted'] = $that->currency->format(
                    $ch_data['amount'],
                    strtoupper($ch_data['currency']),
                    1
                );

                //check a void status.
                //Not captured and refunded
                if ($ch_data['refunded'] && !$ch_data['captured']) {
                    $ch_data['void_status'] = 1;
                }

                if ($ch_data['refunds']->total_count > 0) {
                    //get all refund transactions

                    foreach ($ch_data['refunds']->data as $refund) {
                        $amount = round($refund['amount'] / 100, 2);
                        $refunds[] = [
                            'id'               => $refund['id'],
                            'amount'           => $amount,
                            'amount_formatted' => $that->currency->format($amount, strtoupper($refund['currency']), 1),
                            'currency'         => $refund['currency'],
                            'reason'           => $refund['reason'],
                            'date_added'       => (string)date('m/d/Y H:i:s', $refund['created']),
                            'receipt_number'   => $refund['receipt_number'],
                        ];
                    }
                }
                $ch_data['balance'] = $ch_data['amount'] - $ch_data['amount_refunded'];
                $ch_data['balance_formatted'] = $that->currency->format(
                    $ch_data['balance'],
                    strtoupper($ch_data['currency']),
                    1
                );
            }

            $view->assign('order_id', $order_id);
            $view->assign('test_mode', $this->r_data['stripe_test_mode']);
            $view->assign(
                'external_url',
                'https://dashboard.stripe.com/'
                    . ($that->config->get('stripe_test_mode') ? 'test/' : '')
                    . 'payments/' . $this->r_data['charge_id']
            );
            $view->assign('void_url', $that->html->getSecureURL('r/extension/stripe/void'));
            $view->assign('capture_url', $that->html->getSecureURL('r/extension/stripe/capture'));
            $view->assign('refund_url', $that->html->getSecureURL('r/extension/stripe/refund'));
            $view->assign('stripe_order', $ch_data);
            $view->assign('refund', $refunds);

            $view->batchAssign($that->language->getASet('stripe/stripe'));
            $this->baseObject->view->addHookVar(
                'extension_payment_details',
                $view->fetch('pages/sale/stripe_payment_details.tpl')
            );
        }

    }

    private function _load_stripe_order_data($order_id, $that)
    {
        //data already loaded, return
        if ($this->r_data) {
            return;
        }
        //load local stripe data
        $this->r_data = $that->model_extension_stripe->getstripeOrder($order_id);
    }

    public function onControllerPagesAccountCreate_InitData()
    {
        $that =& $this->baseObject;
        if ($that->session->data['guest'] && $that->request->is_GET()) {
            $that->request->post = $that->session->data['guest'];
            unset($that->session->data['guest']);
        }
    }
}