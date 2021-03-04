<?php

class ExtensionCardKnox extends Extension
{

    protected $registry;
    protected $cardknox_data;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }


    //Hook to enable payment details tab in admin
    public function onControllerPagesSaleOrderTabs_UpdateData()
    {
        /**
         * @var $that ControllerPagesSaleOrderTabs
         */
        $that = $this->baseObject;
        $order_id = $that->data['order_id'];
        $order_info = $that->model_sale_order->getOrder($order_id);
        //are we logged in and in admin?
        if (IS_ADMIN && $that->user->isLogged()) {
            if ($order_info['payment_method_key'] != 'cardknox') {
                return null;
            }
            //check if tab is not yet enabled.
            if (in_array('payment_details', $that->data['groups'])) {
                return null;
            }

            $that->data['groups'][] = 'payment_details';
            $that->data['link_payment_details'] = $that->html->getSecureURL('sale/order/payment_details', '&order_id='.$order_id.'&extension=cardknox');
            //reload main view data with updated tab
            $that->view->batchAssign($that->data);
        }
    }

    //Hook to payment details page to show information
    public function onControllerPagesSaleOrder_UpdateData()
    {
        /**
         * @var $that ControllerPagesSaleOrder
         */
        $that = $this->baseObject;
        //are we logged to admin and correct method called?
        if (IS_ADMIN
            && $that->user->isLogged()
            && $this->baseObject_method == 'payment_details'
            && has_value($that->data['order_info']['payment_method_data'])
        ){
            $payment_method_data = unserialize($that->data['order_info']['payment_method_data']);
            if($that->data['order_info']['payment_method_key'] != 'cardknox'){
                return;
            }

            if (has_value($payment_method_data['xResult']) && $payment_method_data['xResult'] == 'A') {
                $that->loadLanguage('cardknox/cardknox');

                // for some reason after language loading 'button_invoice' html object is removed from baseObject->data
                $that->view->assign('button_invoice', $that->html->buildButton(array(
                    'name' => 'btn_invoice',
                    'text' => $that->language->get('text_invoice'),
                )));

                $data = array();
                $data['text_payment_status'] = $that->language->get('cardknox_text_payment_status');
                if (
                    !isset($payment_method_data['xBatch'])
                    && isset($payment_method_data['xCardType'])
                    && $payment_method_data['xCardType'] != 'EBT'
                ) {
                    // show "capture" form
                    $tpl_data = $this->_get_capture_form($data, $payment_method_data);
                } else {
                    // show "refund" form
                    $tpl_data = $this->_get_refund_form($data, $payment_method_data);
                }

                $view = new AView($this->registry, 0);
                $view->batchAssign($that->language->getASet('cardknox/cardknox'));
                $view->batchAssign($tpl_data);
                $that->view->addHookVar('extension_payment_details', $view->fetch('pages/sale/cardknox_payment_details.tpl'));
            }
        }
    }

    /**
     * @param array $data
     * @param array $payment_method_data
     *
     * @return array
     * @throws AException
     */
    private function _get_capture_form($data = array(), $payment_method_data = array())
    {
        /**
         * @var $that ControllerPagesSaleOrder
         */
        $that = $this->baseObject;

        $authAmount = (float)$payment_method_data['xAuthAmount'];

        $data['text_payment_status'] = $that->language->get('cardknox_text_payment_status');
        $data['payment_status'] = $that->language->get('cardknox_text_pending_authorization');
        $data['cardknox_capture_amount'] = $that->html->buildInput(
            array(
                'name'  => 'cardknox_capture_amount',
                'value' => $authAmount,
                'style' => 'no-save',
                'attr'  => 'disabled',
            )
        );
        $data['text_capture_funds'] = $that->language->get('cardknox_text_capture_funds');
        $data['cardknox_capture_submit'] = $that->html->buildElement(array(
            'type' => 'button',
            'text' => $that->language->get('cardknox_text_capture'),
            'name' => 'cardknox_capture_submit',
        ));

        $data['cardknox_capture_action'] = $that->html->getSecureURL(
            'r/extension/cardknox/capture',
            '&order_id='.(int)$that->data['order_info']['order_id'].
            '&currency='.$that->data['currency']['code']
        );
        return $data;
    }

    /**
     * @param array $data
     * @param array $payment_method_data
     * @param int $not_refunded
     *
     * @return array
     * @throws AException
     */
    private function _get_refund_form($data = array(), $payment_method_data = array(), $not_refunded = 0)
    {
        /**
         * @var $that ControllerPagesSaleOrder
         */
        $that = $this->baseObject;
        $alreadyRefunded = (float)$payment_method_data['refunded_amount'];

        $data['payment_status'] = $that->language->get('cardknox_text_processing');
        if (!(float)$alreadyRefunded) {
            $data['cardknox_refund_amount'] = $that->html->buildInput(
                array(
                    'name'  => 'cardknox_refund_amount',
                    'value' => (float)$payment_method_data['xAuthAmount'],
                    'style' => 'no-save',
                )
            );
            $data['text_do_refund'] = $that->language->get('cardknox_text_do_refund');
            $data['cardknox_refund_submit'] = $that->html->buildElement(array(
                'type'  => 'button',
                'text'  => $that->language->get('cardknox_text_refund'),
                'title' => $that->language->get('cardknox_text_refund'),
                'name'  => 'cardknox_refund_submit',
            ));

            $params = '&order_id='.(int)$that->data['order_info']['order_id']
                        .'&currency='.$that->data['currency']['code'];

            $data['cardknox_refund_action'] = $that->html->getSecureURL(
                'r/extension/cardknox/refund',
                $params
            );

        } else {
            $data['payment_status'] = $that->language->get('cardknox_text_refunded');
        }
        $data['text_already_refunded'] = $that->language->get('text_already_refunded');
        $data['error_wrong_amount'] = $that->language->get('error_wrong_amount');

        return $data;
    }

}
