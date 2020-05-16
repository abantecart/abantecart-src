<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ExtensionCardconnect extends Extension
{

    protected $r_data;

    protected function _is_enabled()
    {
        return $this->baseObject->config->get('cardconnect_status');
    }

    //Hook to enable payment details tab in admin
    public function onControllerPagesSaleOrderTabs_UpdateData()
    {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        $order_id = $that->data['order_id'];
        //are we logged in and in admin?
        if (IS_ADMIN && $that->user->isLogged()) {
            //check if tab is not yet enabled.
            if (in_array('payment_details', $that->data['groups'])) {
                return null;
            }
            //check if we this order is used cardconnect payment
            $that->loadModel('extension/cardconnect');
            $this->_load_cardconnect_order_data($order_id, $that);
            if (!$this->r_data) {
                return;
            }
            $that->data['groups'][] = 'payment_details';
            $that->data['link_payment_details'] = $that->html->getSecureURL('sale/order/payment_details', '&order_id='.$order_id.'&extension=cardconnect');
            //reload main view data with updated tab
            $that->view->batchAssign($that->data);
        }
    }

    //Hook to payment details page to show information
    public function onControllerPagesSaleOrder_UpdateData()
    {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        if (IS_ADMIN !== true
            || !$that->user->isLogged()
            || $this->baseObject_method != 'payment_details'
        ) {
            return null;
        }

        $order_id = $that->request->get['order_id'];
        //are we logged to admin and correct method called?
        //build HTML to show

        $that->loadLanguage('cardconnect/cardconnect');
        $that->loadModel('extension/cardconnect');
        if (!$this->r_data) {
            //no local cardconnect order data yet. load it.
            $this->_load_cardconnect_order_data($order_id, $that);
        }

        if (!$this->r_data) {
            return null;
        }
        $registry = Registry::getInstance();
        $view = new AView($registry, 0);
        //get remote charge data
        $ch_data = $that->model_extension_cardconnect->getCardconnectCharge($this->r_data['retref']);
        if (!$ch_data) {
            $view->assign('error_warning', "Some error happened!. Check the error log for more details.");
        } else {
            $ch_data['settlement_status'] = $ch_data['setlstat'];
            $ch_data['refunded_formatted'] = $that->currency->format($ch_data['refunded'], strtoupper($ch_data['currency_code']), 1);
            if ($ch_data['authorized'] > 0) {
                $ch_data['authorized_formatted'] = $that->currency->format($ch_data['authorized'], strtoupper($ch_data['currency_code']), 1);
            } else {
                $ch_data['authorized_formatted'] = $that->currency->format($ch_data['amount'], strtoupper($ch_data['currency_code']), 1);
            }
            $ch_data['captured_formatted'] = $that->currency->format($ch_data['captured'], strtoupper($ch_data['currency_code']), 1);

            //check a void status.
            //Not captured and refunded
            if ($ch_data['refunded'] && !$ch_data['captured']) {
                $ch_data['void_status'] = 1;
            }
            if ($ch_data['refunds']->total_count > 0) {
                //get all refund transactions
                foreach ($ch_data['refunds']->data as $refund) {
                    $amount = number_format($refund['amount'], 2);
                    $refunds[] = array(
                        'id'               => $refund['id'],
                        'amount'           => $amount,
                        'amount_formatted' => $that->currency->format($amount, strtoupper($refund['currency']), 1),
                        'currency'         => $refund['currency'],
                        'reason'           => $refund['reason'],
                        'date_added'       => (string)date('m/d/Y H:i:s', $refund['created']),
                        'receipt_number'   => $refund['receipt_number'],
                    );
                }
            }
            $ch_data['balance'] = $ch_data['amount'] + $ch_data['refunded'];
            $ch_data['balance_formatted'] = $that->currency->format($ch_data['balance'], strtoupper($ch_data['currency']), 1);
        }

        $view->assign('order_id', $order_id);
        $view->assign('test_mode', $this->r_data['cardconnect_test_mode']);
        $api_endpoint = 'https://'
                        .($that->config->get('cardconnect_test_mode') ? 'fts-uat.cardconnect.com' : 'fts.cardconnect.com')
                        .'/ui/findauth.jsf';
        $view->assign('external_url', $api_endpoint);
        $view->assign('void_url', $that->html->getSecureURL('r/extension/cardconnect/void'));
        $view->assign('capture_url', $that->html->getSecureURL('r/extension/cardconnect/capture'));
        $view->assign('refund_url', $that->html->getSecureURL('r/extension/cardconnect/refund'));
        $view->assign('cardconnect_order', $ch_data);
        $view->assign('refund', $refunds);
        $view->batchAssign($that->language->getASet('cardconnect/cardconnect'));
        $that->document->addStyle(
            array(
                'href'  => $that->view->templateResource('/stylesheet/cardconnect.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            )
        );
        $that->view->addHookVar('extension_payment_details', $view->fetch('pages/sale/cardconnect_payment_details.tpl'));
    }

    private function _load_cardconnect_order_data($order_id, $that)
    {
        //data already loaded, return
        if ($this->r_data) {
            return null;
        }
        //load local cardconnect data
        $this->r_data = $that->model_extension_cardconnect->getcardconnectOrder($order_id);
    }

    /*
     *
     * custom tpl for product edit page
     *
     *
     * */

    public function onControllerPagesCatalogProduct_InitData()
    {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        if (!$this->_is_enabled($that)) {
            return null;
        }
        if (IS_ADMIN !== true) {
            return;
        }

        $product_id = (int)$that->request->get['product_id'];
        $cardconnect_plan = $that->request->get['cardconnect_plan'];
        $that->load->language('cardconnect/cardconnect');
        $that->load->model('extension/cardconnect');
        if ($product_id && has_value($cardconnect_plan)) {
            if ($cardconnect_plan) {
                //Set up product for subscription
                //update product price with plan price
                //update cardconnect metadata for description
                $ret = $that->model_extension_cardconnect->setProductAsSubscription($product_id, $cardconnect_plan);
                if (array($ret) && $ret['error']) {
                    $that->session->data['warning'] = implode("\n", $ret['error']);
                    header('Location: '.$that->html->getSecureURL('catalog/product/update', '&product_id='.$product_id));
                    exit;
                }
            } else {
                //reset to no plan
                $ret = $that->model_extension_cardconnect->removeProductAsSubscription($product_id);
                if (array($ret) && $ret['error']) {
                    $that->session->data['warning'] = implode("\n", $ret['error']);
                    header('Location: '.$that->html->getSecureURL('catalog/product/update', '&product_id='.$product_id));
                    exit;
                }
            }
        }
    }

    public function onControllerPagesCheckoutConfirm_InitData()
    {
        if (!$this->_is_enabled()) {
            return null;
        }
        $that = $this->baseObject;
        if ($that->session->data['payment_method']['id'] == 'cardconnect') {
            $that->document->addStyle(
                array(
                    'href'  => $that->view->templateResource('/stylesheet/cardconnect.css'),
                    'rel'   => 'stylesheet',
                    'media' => 'screen',
                )
            );
        }
    }
}