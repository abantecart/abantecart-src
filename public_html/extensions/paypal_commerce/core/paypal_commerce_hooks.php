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
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

/**
 * Class ExtensionPaypalCommerce
 */
class ExtensionPaypalCommerce extends Extension
{
    protected $registry;
    protected $r_data;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    protected function _is_enabled($that)
    {
        return $that->config->get('paypal_commerce_status');
    }

    public static function getBnCode()
    {
        return 'QWJhbnRlQ2FydF9TUA==';
    }


    public static function getPartnerClientId()
    {
        return 'QWYxUnZvbEEtOHVFeGppRnJ6c0w5S28wR2I5Z0NIX0lSUjhlUUhkOHZScnE5TDdEbjVYUkxXMnVKQzFvWnozOGVGOUlyS3NOQS1jR2huNmY=';
    }

    //Hook to extension edit in the admin
    public function onControllerPagesExtensionExtensions_InitData()
    {
        $that = $this->baseObject;
        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN === true && $current_ext_id == 'paypal_commerce' && $this->baseObject_method == 'edit') {
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $that->loadModel('extension/paypal_commerce');

            //update webhooks after onboarding
            if ($that->request->get['onboarded']) {
                try {
                    $mdl->updateWebHooks();
                }catch(Exception|Error $e){
                    $that->log->write(
                        'Paypal Commerce Error: Cannot to update webhooks. ' . $e->getMessage(),
                    );
                }
            } else if ($that->request->get['disconnect']) {
                //delete webhooks before disconnect
                $mdl->deleteWebHooks();
                $settings = [
                    'paypal_commerce_client_id'     => '',
                    'paypal_commerce_client_secret' => '',
                    'paypal_commerce_test_mode'     => '',
                    'paypal_commerce_onboarding'    => ''
                ];
                $that->loadLanguage('paypal_commerce/paypal_commerce');
                /** @var ModelSettingSetting $mdl */
                $mdl = $that->loadModel('setting/setting');
                $mdl->editSetting(
                    'paypal_commerce',
                    $settings,
                    (int)$that->session->data['current_store_id']
                );
                foreach ($settings as $k => $v) {
                    $that->config->set($k, '');
                }

                $that->session->data['success'] = $that->language->get('text_disconnect_success');
            }

            //add gears as background when test mode is enabled
            if ($that->config->get('paypal_commerce_client_id') && $that->config->get('paypal_commerce_test_mode')) {
                $that->view->addHookVar(
                    'extension_toolbar_buttons',
                    '<script type="application/javascript">
    $(document).ready(function(){
        $("div.panel-body.panel-body-nopadding.tab-content.col-xs-12").addClass("status_test");
    })
</script>'
                );

            }
        }
    }

    //Hook to extension edit in the admin
    public function onControllerPagesExtensionExtensions_UpdateData()
    {
        $that = $this->baseObject;
        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN === true && $current_ext_id == 'paypal_commerce' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" target="_blank" href="https://www.paypal.com" title="Visit paypal">
                        <i class="fa fa-external-link fa-lg"></i>
                    </a>';
            $that->view->addHookVar('extension_toolbar_buttons', $html);
            $that->document->addStyle(
                [
                    'href'  => $that->view->templateResource('/css/paypal_commerce.css'),
                    'rel'   => 'stylesheet',
                    'media' => 'screen',
                ]
            );

            $dir_template = DIR_EXT
                . 'paypal_commerce' . DS
                . DIR_EXT_ADMIN
                . DIR_EXT_TEMPLATE
                . $that->config->get('admin_template')
                . DS . "template"
                . DS . "responses"
                . DS . "extension"
                . DS . "paypal_commerce_connect.tpl";
            $that->view->batchAssign($that->language->getASet('paypal_commerce/paypal_commerce'));

            $connected = ($that->config->get('paypal_commerce_client_id') && $that->config->get('paypal_commerce_onboarding'));

            $data = [];
            $data['test_mode'] = $that->config->get('paypal_commerce_test_mode');
            $data['disconnect_url'] = $that->html->getSecureURL('extension/extensions/edit', '&extension=paypal_commerce&disconnect=true');
            /** @var ModelToolMPAPI $mpMdl */
            $mpMdl = $that->loadModel('tool/mp_api');
            $data['connect_url'] = $mpMdl->getMPURL() . '?rt=index/paypal_onboarding'
                . '&abc_onboard_url=' . base64_encode($that->html->getSecureURL('extension/paypal_commerce/onboard'))
                . '&nonce=' . getNonce(UNIQUE_ID)
                . '&store_id=' . (int)$that->session->data['current_store_id'];

            //see if we are connected yet to paypal
            if ($connected) {
                $data['connected'] = true;
            }

            $that->view->batchAssign($data);
            $html = $that->view->fetch($dir_template);
            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }
    }

    //Hook to extension edit in the admin
    public function onControllerPagesExtensionExtensions_ValidateData()
    {
        /** @var ControllerPagesExtensionExtensions $that */
        $that =& $this->baseObject;
        if ($that->error || $that->request->get['extension'] != 'paypal_commerce') {
            return;
        }
        if (isset($that->request->post['paypal_commerce_status'])) {
            $that->config->set(
                'paypal_commerce_status',
                $that->request->post['paypal_commerce_status']
            );
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $that->loadModel('extension/paypal_commerce');
            $secretKey = $that->config->get('paypal_commerce_test_mode')
                ? $that->config->get('paypal_commerce_sk_test')
                : $that->config->get('paypal_commerce_sk_live');
            if ($secretKey) {
                try {
                    $mdl->updateWebHooks();
                } catch (Exception|Error $e) {
                    $that->error['webhooks_status'] = 'Updating Paypal Webhooks EndPoints: '
                        . $e->getMessage() . '(' . $e->getCode() . ')';
                }
            }
        }
    }

    //Hook to extension edit in the admin
    public function onControllerResponsesListingGridExtension_UpdateData()
    {
        /** @var ControllerResponsesListingGridExtension $that */
        $that =& $this->baseObject;
        if ($that->request->get['id'] == 'paypal_commerce'
            && isset($that->request->post['paypal_commerce_status'])
        ) {
            $that->config->set(
                'paypal_commerce_status',
                $that->request->post['paypal_commerce_status']
            );
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $that->loadModel('extension/paypal_commerce');
            $secretKey = $that->config->get('paypal_commerce_test_mode')
                ? $that->config->get('paypal_commerce_sk_test')
                : $that->config->get('paypal_commerce_sk_live');
            if ($secretKey) {
                try {
                    $mdl->updateWebHooks();
                } catch (Exception $e) {
                    $that->log->write(
                        'Updating Paypal Webhooks EndPoints: ' . $e->getMessage() . '(' . $e->getCode() . ')'
                    );
                }
            }
        }
    }

    //Hook to enable payment details tab in admin
    public function onControllerPagesSaleOrderTabs_UpdateData()
    {
        /** @var ControllerPagesSaleOrderTabs $that */
        $that =& $this->baseObject;
        $order_id = $that->data['order_id'];
        //are we logged in and in admin?
        if (IS_ADMIN && $that->user->isLogged()) {
            //check if tab is not yet enabled.
            if (in_array('payment_details', $that->data['groups'])) {
                return null;
            }
            //check if we this order is used paypal payment
            $that->loadModel('extension/paypal_commerce');
            $this->_load_paypal_order_data($order_id, $that);

            if (!$this->r_data || !$this->r_data['charge_id']) {
                return;
            }
            $this->r_data['settings'] = $this->r_data['settings'] ? unserialize($this->r_data['settings']) : [];

            $that->data['groups'][] = 'payment_details';
            $that->data['link_payment_details'] = $that->html->getSecureURL(
                'sale/order/payment_details',
                '&order_id=' . $order_id
                . '&extension=paypal_commerce'
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
            if ($that->request->get['extension'] != 'paypal_commerce') {
                return null;
            }

            //build HTML to show
            $that->loadLanguage('paypal_commerce/paypal_commerce');
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $that->loadModel('extension/paypal_commerce');
            if (!$this->r_data) {
                //no local paypal order data yet. load it.
                $this->_load_paypal_order_data($order_id, $that);
            }

            if (!$this->r_data) {
                return null;
            }
            $data = $refunds = [];
            $view = new AView($this->registry, 0);
            //get remote charge data
            $chargeData = $mdl->getPaypalCharge($this->r_data['charge_id']);
            if (!$chargeData) {
                $view->assign(
                    'error_warning',
                    "Some error happened!. Check the error log for more details."
                );
            } elseif ($chargeData instanceof stdClass) {
                $data['transaction_id'] = $this->r_data['transaction_id'];
                $data['amount_refunded'] = 0;
                $amt = 0;
                $currencyCode = '';
                if ($chargeData->intent == 'AUTHORIZE') {
                    foreach ($chargeData->purchase_units[0]->payments->authorizations as $auth) {
                        $amt += $auth->amount->value;
                        $currencyCode = $auth->amount->currency_code;
                    }
                    $data['amount_authorized'] = round($amt, 2);
                    $data['amount_authorized_formatted'] = $that->currency->format($amt, $currencyCode, 1);
                    $amt = 0;
                    if ($chargeData->purchase_units[0]->payments->captures) {
                        foreach ($chargeData->purchase_units[0]->payments->captures as $capt) {
                            if ($capt->status == 'PARTIALLY_REFUNDED') {
                                $data['amount_refunded'] += $capt->amount->value;
                            } else {
                                $amt += $capt->amount->value;
                            }
                            $currencyCode = $capt->amount->currency_code;
                        }
                    }
                    $data['amount_captured'] = round($amt, 2);
                    $data['amount_captured_formatted'] = $that->currency->format($amt, $currencyCode, 1);
                } else {
                    foreach ($chargeData->purchase_units[0]->payments->captures as $capt) {
                        $amt += $capt->amount->value;
                        $currencyCode = $capt->amount->currency_code;
                    }
                    $data['amount_captured'] = round($amt, 2);
                    $data['amount_captured_formatted'] = $that->currency->format($amt, $currencyCode, 1);
                    $data['captured'] = 1;
                }

                if ($chargeData->purchase_units[0]->payments->refunds) {
                    $amt = 0;
                    foreach ($chargeData->purchase_units[0]->payments->refunds as $refund) {
                        $amt += $refund->amount->value;
                        $currencyCode = $refund->amount->currency_code;
                        $amount = round((float)$refund->amount->value, 2);
                        $refunds[] = [
                            'id'               => $refund->id,
                            'amount'           => $amount,
                            'amount_formatted' => $that->currency->format($amount, strtoupper($currencyCode), 1),
                            'currency'         => $currencyCode,
                            'reason'           => $refund->reason,
                            'date_added'       => (string)date(
                                $that->language->get('date_format_short') . " " . $that->language->get('time_format'),
                                strtotime($refund->create_time)
                            ),
                            'receipt_number'   => $refund->id,
                        ];
                    }
                    $data['refunded'] = true;
                    $data['amount_refunded'] = round($amt, 2);
                }

                if ($data['amount_authorized'] == $data['amount_captured']) {
                    $data['captured'] = 1;
                }
                //check a void status.
                //Not captured and refunded
                if ($data['refunded'] && !$data['captured']
                    || $chargeData->purchase_units[0]->payments->authorizations[0]->status == 'VOIDED') {
                    $data['void_status'] = 1;
                }

                $data['balance'] = $data['amount_captured'] - $data['amount_refunded'];
                $data['balance_formatted'] = $that->currency->format(
                    $data['balance'],
                    strtoupper($currencyCode),
                    1
                );
            }

            $view->assign('order_id', $order_id);
            $view->assign('test_mode', $this->r_data['paypal_commerce_test_mode']);
            $view->assign('void_url', $that->html->getSecureURL('r/extension/paypal_commerce/void'));
            $view->assign('capture_url', $that->html->getSecureURL('r/extension/paypal_commerce/capture'));
            $view->assign('refund_url', $that->html->getSecureURL('r/extension/paypal_commerce/refund'));
            $view->assign('paypal_order', $data);
            $view->assign('refund', $refunds);

            $view->batchAssign($that->language->getASet('paypal_commerce/paypal_commerce'));
            $this->baseObject->view->addHookVar(
                'extension_payment_details',
                $view->fetch('pages/sale/paypal_commerce_payment_details.tpl')
            );
        }
    }

    private function _load_paypal_order_data($order_id, $that)
    {
        //data already loaded, return
        if ($this->r_data) {
            return null;
        }
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $that->model_extension_paypal_commerce;
        //load local paypal data
        $this->r_data = $mdl->getPaypalOrder($order_id);
    }

    public function onControllerCommonHeader_InitData()
    {
        if (IS_ADMIN) {
            $that =& $this->baseObject;
            $that->loadLanguage('paypal_commerce/paypal_commerce');
        }
    }

    public function onControllerPagesProductProduct_UpdateData()
    {
        if (IS_ADMIN) { return; }
        $that =& $this->baseObject;

        $payLaterMessage = html_entity_decode($that->config->get('paypal_commerce_pay_later_product_message'));
        $payLaterMessage = str_replace('ENTER_VALUE_HERE','%s',$payLaterMessage);
        if(!str_contains($payLaterMessage,'%s')){
            return;
        }
        $payLaterMessage = sprintf(
            $payLaterMessage,
            $that->data['price_num'] * ($that->request->get['quantity'] ?: $that->data['minimum'] ?: 1)
        );
        $payLaterMessage .= '<script src="https://www.paypal.com/sdk/js?client-id='.$that->config->get('paypal_commerce_client_id')
            .'&components=messages,buttons" data-namespace="PayPalSDK"></script>';
        $that->view->addHookVar('extended_product_options', $payLaterMessage);
    }

    public function onControllerPagesCheckoutCart_UpdateData()
    {
        if (IS_ADMIN) { return; }
        $that =& $this->baseObject;

        $payLaterMessage = html_entity_decode($that->config->get('paypal_commerce_pay_later_checkout_message'));
        $payLaterMessage = str_replace('ENTER_VALUE_HERE','%s',$payLaterMessage);
        if(!str_contains($payLaterMessage,'%s')){
            return;
        }
        $totals = $that->view->getData('totals');
        foreach ($totals as $total) {
            if($total['id'] == 'total'){
                $totalAmount = $total['value'];
            }
        }
        $payLaterMessage = sprintf(
            $payLaterMessage,
            $totalAmount
        );
        $payLaterMessage .= '<script src="https://www.paypal.com/sdk/js?client-id='.$that->config->get('paypal_commerce_client_id')
            .'&components=messages,buttons" data-namespace="PayPalSDK"></script>';
        $that->view->addHookVar('pre_top_cart_buttons', $payLaterMessage);
    }

}
