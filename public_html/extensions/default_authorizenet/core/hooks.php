<?php

class ExtensionDefaultAuthorizeNet extends Extension
{

    protected $registry;
    protected $r_data;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    //Hook to extension edit in the admin
    public function onControllerPagesExtensionExtensions_UpdateData()
    {
        $that = $this->baseObject;
        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN && $current_ext_id == 'authorizenet' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" '
                        .'target="_blank" '
                        .'href="https://www.authorize.net" '
                        .'title="Visit authorizenet">
                        <i class="fa fa-external-link fa-lg"></i>
                    </a>';
            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }
    }

    //Hook to extension edit in the admin
    public function onControllerPagesSaleOrderSummary_UpdateData()
    {
        $that = $this->baseObject;
        if ( IS_ADMIN !== true) {
            return null;
        }
        $order_info = $that->model_sale_order->getOrder($that->request->get['order_id']);
        if($order_info['payment_method_key'] != 'default_authorizenet'){
            return null;
        }
        $method_info = unserialize($order_info['payment_method_data']);

        $view_order_details = $that->view->getData('order');
        if($method_info) {
            $view_order_details['payment_method'] = $view_order_details['payment_method']
                .'<br>'
                .($method_info['authorizenet_transaction_id']
                    ? 'Transaction ID: '.$method_info['authorizenet_transaction_id'].'('.$method_info['cc_type'].')'
                    : '');
            $that->view->assign('order', $view_order_details);
        }
    }

    //Hook to enable payment details tab in admin
    public function onControllerPagesSaleOrderTabs_UpdateData()
    {
        $that = $this->baseObject;
        $order_id = $that->data['order_id'];
        $order_info = $that->model_sale_order->getOrder($order_id);
        //are we logged in and in admin?
        if (IS_ADMIN && $that->user->isLogged()) {
            if ($order_info['payment_method_key'] != 'default_authorizenet') {
                return null;
            }
            //check if tab is not yet enabled.
            if (in_array('payment_details', $that->data['groups'])) {
                return null;
            }

            $that->data['groups'][] = 'payment_details';
            $that->data['link_payment_details'] = $that->html->getSecureURL(
                'sale/order/payment_details',
                '&order_id='.$order_id.'&extension=default_authorizenet'
            );
            //reload main view data with updated tab
            $that->view->batchAssign($that->data);
        }
    }

    //Hook to payment details page to show information
    public function onControllerPagesSaleOrder_UpdateData()
    {
        $that = $this->baseObject;
        $order_id = $that->request->get['order_id'];
        //are we logged to admin and correct method called?
        if (IS_ADMIN && $that->user->isLogged() && $this->baseObject_method == 'payment_details') {
            //build HTML to show
            $that->loadLanguage('default_authorizenet/default_authorizenet');
            $that->loadModel('extension/default_authorizenet');

            if ( ! $this->r_data) {
                //no local authorizenet order data yet. load it.
                $this->loadAuthorizenetOrderData($order_id, $that);
            }

            if ( ! $this->r_data) {
                $this->baseObject->view->addHookVar('extension_payment_details',
                    '<br><div class="alert alert-danger">AuthorizeNet transaction details not found.</div>');

                return null;
            }

            $view = new AView($this->registry, 0);
            //get remote charge data
            $ch_data = $that->model_extension_default_authorizenet->getAuthorizeNetTransaction($this->r_data['charge_id']);
            if ( ! $ch_data || $ch_data['error']) {
                $view->assign('error_warning', "Transaction ID ".$this->r_data['charge_id']." not found.");
            } else {

                $ch_data['authAmount'] = round($ch_data['authAmount'], 2);
                $ch_data['amount_refunded'] = round($ch_data['amount_refunded'], 2);
                $ch_data['amount_formatted'] = $that->currency->format(
                    $ch_data['authAmount'],
                    strtoupper($ch_data['currency']),
                    1
                );
                if (in_array(
                    $ch_data['transactionStatus'],
                    array(
                        'capturedPendingSettlement',
                        'settledSuccessfully',
                        'refundSettledSuccessfully',
                        'refundPendingSettlement'
                        )
                    )
                ) {
                    $ch_data['captured'] = true;
                }

                if (in_array(
                        $ch_data['transactionStatus'],
                        array('refundSettledSuccessfully', 'refundPendingSettlement')
                    )
                ) {
                    $ch_data['refunded'] = true;
                    $ch_data['settleAmount'] = '';
                }

                if (in_array($ch_data['transactionStatus'], array('settledSuccessfully'))) {
                    $ch_data['can_refund'] = true;
                }

                //check a void status.
                //Not captured and refunded
                if (in_array(
                    $ch_data['transactionStatus'],
                    array('authorizedPendingCapture','capturedPendingSettlement'))
                ){
                    $ch_data['can_void'] = true;
                }

                if ($ch_data['transactionStatus'] == 'voided') {
                    $ch_data['void_status'] = true;
                }
                //if
                if($this->r_data['charge_id'] != $this->r_data['charge_id_previous']){
                    $view->assign('previous_transaction_id', $this->r_data['charge_id_previous']);
                    $orig_transaction = $that
                        ->model_extension_default_authorizenet
                        ->getAuthorizeNetTransaction(
                            $this->r_data['charge_id_previous']
                        );
                    $balance = $orig_transaction['authAmount'] - $ch_data['authAmount'];
                }else {
                       $balance = $ch_data['transactionStatus'] == 'voided'
                        ? 0.0
                        : ($ch_data['authAmount'] - $ch_data['settleAmount'] - $ch_data['amount_refunded']);
                }
                $ch_data['balance_formatted'] = $that->currency->format($balance, strtoupper($ch_data['currency']), 1);
            }

            $view->assign('order_id', $order_id);
            $test_mode = $this->r_data['authorizenet_test_mode'];
            $view->assign('test_mode', $test_mode);
            if ($test_mode) {
                $view->assign(
                    'external_url',
                    'https://sandbox.authorize.net/UI/themes/sandbox/merch.aspx?page=search&transId='
                );
            } else {
                $view->assign('external_url', 'https://dashboard.authorize.net/payments/');
            }

            $view->assign('void_url', $that->html->getSecureURL('r/extension/default_authorizenet/void'));
            $view->assign('capture_url', $that->html->getSecureURL('r/extension/default_authorizenet/capture'));
            $view->assign('refund_url', $that->html->getSecureURL('r/extension/default_authorizenet/refund'));
            $view->assign('authorizenet_order', $ch_data);

            $view->batchAssign($that->language->getASet('default_authorizenet/default_authorizenet'));
            $this->baseObject->view->addHookVar(
                'extension_payment_details', $view->fetch('pages/sale/payment_details.tpl'));
        }
    }

}