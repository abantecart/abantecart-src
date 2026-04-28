<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
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

    protected function applyStorePaypalSettings($that, int $storeId): void
    {
        /** @var ModelSettingSetting $settingMdl */
        $settingMdl = $that->loadModel('setting/setting');
        $settings = $settingMdl->getSetting('paypal_commerce', $storeId);
        foreach ((array)$settings as $key => $value) {
            $that->config->set($key, $value);
        }
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
            $storeId = (int)($that->request->get_or_post('store_id')
                ?: $that->session->data['current_store_id']);
            $that->session->data['current_store_id'] = $storeId;
            $this->applyStorePaypalSettings($that, $storeId);
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
                redirect(
                    $that->html->getSecureURL(
                        'extension/extensions/edit',
                        '&extension=paypal_commerce&store_id=' . $storeId
                    )
                );
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
                    $storeId
                );
                foreach ($settings as $k => $v) {
                    $that->config->set($k, '');
                }

                $that->session->data['success'] = $that->language->get('text_disconnect_success');
                redirect(
                    $that->html->getSecureURL(
                        'extension/extensions/edit',
                        '&extension=paypal_commerce&store_id=' . $storeId
                    )
                );
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
            $storeId = (int)($that->request->get_or_post('store_id')
                ?: $that->session->data['current_store_id']);
            $that->session->data['current_store_id'] = $storeId;
            $this->applyStorePaypalSettings($that, $storeId);
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
            $data['disconnect_url'] = $that->html->getSecureURL(
                'extension/extensions/edit',
                '&extension=paypal_commerce&store_id=' . $storeId . '&disconnect=true'
            );
            /** @var ModelToolMPAPI $mpMdl */
            $mpMdl = $that->loadModel('tool/mp_api');
            $extConfig = getExtensionConfigXml('paypal_commerce');
            $data['connect_url'] = $mpMdl->getMPURL() . '?rt=index/paypal_onboarding'
                . '&abc_onboard_url=' . base64_encode($that->html->getSecureURL('extension/paypal_commerce/onboard'))
                . '&nonce=' . getNonce(UNIQUE_ID)
                . '&pp_version= ' . $extConfig->version
                . '&abc_version= ' . VERSION
                . '&store_id=' . $storeId;

            //see if we are connected yet to paypal
            if ($connected) {
                $data['connected'] = true;
                $data['connected_account'] = $that->config->get('paypal_commerce_payer_id');
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
        $storeId = (int)($that->request->get_or_post('store_id')
            ?: $that->session->data['current_store_id']);
        $that->session->data['current_store_id'] = $storeId;
        $this->applyStorePaypalSettings($that, $storeId);
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
            $storeId = (int)($that->request->get_or_post('store_id')
                ?: $that->session->data['current_store_id']);
            $that->session->data['current_store_id'] = $storeId;
            $this->applyStorePaypalSettings($that, $storeId);
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
        $order_id = (int)$that->data['order_id'];
        //are we logged in and in admin?
        if (IS_ADMIN && $that->user->isLogged()) {
            //check if the tab is not yet enabled.
            if (in_array('payment_details', $that->data['groups'])) {
                return null;
            }
            //check if we this order is used PayPal payment
            $that->loadModel('extension/paypal_commerce');
            $this->_load_paypal_order_data($order_id, $that);

            if (!$this->r_data || !$this->r_data['charge_id']) {
                return;
            }
            $this->r_data['settings'] = $this->r_data['settings']
                ? unserialize($this->r_data['settings'])
                : [];

            $that->data['groups'][] = 'payment_details';
            $that->data['link_payment_details'] = $that->html->getSecureURL(
                'sale/order/payment_details',
                '&order_id=' . $order_id
                . '&extension=paypal_commerce'
            );
            //reload main view data with an updated tab
            $that->view->batchAssign($that->data);
        }
    }

    //Hook to the payment details page to show information
    public function onControllerPagesSaleOrder_UpdateData()
    {
        $that = $this->baseObject;
        $order_id = $that->request->get['order_id'];
        //are we logged to admin and correct method called?
        if (IS_ADMIN && $that->user->isLogged() && $this->baseObject_method == 'payment_details') {
            if ($that->request->get['extension'] != 'paypal_commerce') {
                return null;
            }

            // Ensure PayPal client is initialized with credentials of the order's store.
            $that->loadModel('sale/order');
            $orderInfo = $that->model_sale_order->getOrder($order_id);
            if (isset($orderInfo['store_id'])) {
                $this->applyStorePaypalSettings($that, (int)$orderInfo['store_id']);
            }

            //build HTML to show
            $that->loadLanguage('paypal_commerce/paypal_commerce');
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $that->loadModel('extension/paypal_commerce');
            // Re-init API client after store-specific settings were applied above.
            // Model instance can be created earlier in request with another store config.
            $mdl->__construct($this->registry);
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
                    "Some error happened. Check the error log for more details."
                );
            } else {
                $data['transaction_id'] = $this->r_data['transaction_id'];
                $data['amount_refunded'] = 0;
                $amt = 0;
                $currencyCode = '';
                $priorCaptures = $chargeData->getPurchaseUnits()[0]->getPayments()->getCaptures();
                $priorAuthorizations = $chargeData->getPurchaseUnits()[0]->getPayments()->getAuthorizations();
                if ($chargeData->getIntent() == 'AUTHORIZE') {
                    foreach ($priorAuthorizations as $auth) {
                        $amt += (float) $auth->getAmount()->getValue();
                        $currencyCode = $auth->getAmount()->getCurrencyCode();
                    }
                    $data['amount_authorized'] = round($amt, 2);
                    $data['amount_authorized_formatted'] = $that->currency->format($amt, $currencyCode, 1);
                    $amt = 0.0;
                    if ($priorCaptures) {
                        foreach ($priorCaptures as $capt) {
                            if ($capt->getStatus() == 'PARTIALLY_REFUNDED') {
                                $data['amount_refunded'] += (float) $capt->getAmount()->getValue();
                            } else {
                                $amt += (float) $capt->getAmount()->getValue();
                            }
                            $currencyCode = $capt->getAmount()->getCurrencyCode();
                        }
                    }
                    $data['amount_captured'] = round($amt, 2);
                    $data['amount_captured_formatted'] = $that->currency->format($amt, $currencyCode, 1);
                } else {
                    foreach ($priorCaptures as $capt) {
                        $amt += (float) $capt->getAmount()->getValue();
                        $currencyCode = $capt->getAmount()->getCurrencyCode();
                    }
                    $data['amount_captured'] = round($amt, 2);
                    $data['amount_captured_formatted'] = $that->currency->format($amt, $currencyCode, 1);
                    $data['captured'] = 1;
                }
                $priorRefunds = $chargeData->getPurchaseUnits()[0]->getPayments()?->getRefunds();
                if ($priorRefunds) {
                    $amt = 0;
                    foreach ($priorRefunds as $refund) {
                        $amt += (float)$refund->getAmount()->getValue();
                        $currencyCode = $refund->getAmount()->getCurrencyCode();
                        $amount = round((float) $refund->getAmount()->getValue(), 2);
                        $refunds[] = [
                            'id'               => $refund->getId(),
                            'amount'           => $amount,
                            'amount_formatted' => $that->currency->format($amount, strtoupper($currencyCode), 1),
                            'currency'         => $currencyCode,
                            'reason'           => $refund->reason,
                            'date_added'       => (string) date(
                                $that->language->get('date_format_short') . " " . $that->language->get('time_format'),
                                strtotime($refund->create_time)
                            ),
                            'receipt_number'   => $refund->getId(),
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
                if ($data['refunded'] && !$data['captured'] || $priorAuthorizations[0]?->getStatus() == 'VOIDED'){
                    $data['void_status'] = 1;
                }

                $data['balance'] = $data['amount_captured'] - $data['amount_refunded'];
                $data['balance_formatted'] = $that->currency->format(
                    $data['balance'],
                    strtoupper($currencyCode),
                    1
                );

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
    }

    /**
     * @param string $order_id
     * @param object $that
     *
     * @return void
     * @throws AException
     */
    private function _load_paypal_order_data($order_id, $that)
    {
        //data already loaded, return
        if ($this->r_data) {
            return;
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
        //do nothing when the product is out-of-stock
        if(!$that->view->getData('can_buy')){
            return;
        }

        //clean cart of checkout process first to avoid amount mismatch
        unset($that->session->data['fc']['cart']);
        $view = new AView(Registry::getInstance());
        $data['show_buttons'] = $that->config->get('paypal_commerce_show_buttons_product');
        $data['fast_checkout_buy_now_status'] = $that->config->get('fast_checkout_buy_now_status');
        $data['buynow_url'] = $that->view->getData('buynow_url');

        $that->loadLanguage('paypal_commerce/paypal_commerce');
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $that->load->model('extension/paypal_commerce');
        $data['client_token'] = $mdl->getClientToken();
        $data['bn_code'] = ExtensionPaypalCommerce::getBnCode();
        $data['intent'] = $that->config->get('paypal_commerce_transaction_type');
        $data['enabled_components'] = unserialize($that->config->get('paypal_commerce_enabled_components')) ?: ['buttons'];
        $data['enabled_funding'] = unserialize($that->config->get('paypal_commerce_enabled_funding')) ?: [];
        $data['create_quick_order_url'] = $that->html->getSecureURL('r/extension/paypal_commerce/createQuickOrder');
        $data['prepare_checkout_url'] = $that->html->getSecureURL('r/extension/paypal_commerce/prepareCheckout');

        $productInfo = $that->view->getData('product_info');
        $data['product_id'] = $productInfo['product_id'];
        $data['product_name'] = $productInfo['name'];
        $data['return_url'] = $data['cancel_url'] = $that->html->getSEOURL(
            'product/product',
            '&product_id=' . $productInfo['product_id']
        );

        $data['capture_order_url'] = $that->html->getSecureURL('r/extension/paypal_commerce/captureOrder');
        $data['action'] = $that->html->getSecureURL('r/extension/paypal_commerce/send');
        $data['pageType'] = "product";
        $view->batchAssign($data);

        $ppButtons = $view->fetch('responses/paypal_commerce_buy_now.tpl');
        $that->view->addHookVar('buttons', $ppButtons);

        $payLaterMessage = html_entity_decode($that->config->get('paypal_commerce_pay_later_product_message'));
        $payLaterMessage = str_replace('ENTER_VALUE_HERE','%s',$payLaterMessage);
        if(!str_contains($payLaterMessage,'%s')){
            return;
        }
        $payLaterMessage = sprintf(
            $payLaterMessage,
            $that->data['price_num'] * ($that->request->get['quantity'] ?: $that->data['minimum'] ?: 1)
        );
        $that->view->addHookVar('extended_product_options', '<div style="margin: auto">' . $payLaterMessage.'</div>');
    }

    public function onControllerPagesCheckoutCart_UpdateData()
    {
        if (IS_ADMIN) { return; }
        $that =& $this->baseObject;
        //clean cart of checkout process first
        unset($that->session->data['fc']['cart']);
        $canBuy = true;
        foreach($that->data['products'] as $product){
            $stockCheckout = $product['stock_checkout'] ?? $that->config->get('config_stock_checkout');
            if( $product['stock'] <= 0 && $stockCheckout < 1){
                $canBuy = false;
            }else{
                $canBuy = true;
                break;
            }
        }
        if(!$canBuy){
            return;
        }

        $view = new AView(Registry::getInstance());
        $data['show_buttons'] = $that->config->get('paypal_commerce_show_buttons_cart');
        $that->loadLanguage('paypal_commerce/paypal_commerce');
        /** @var ModelExtensionPaypalCommerce $mdl */
        $mdl = $that->load->model('extension/paypal_commerce');
        $data['client_token'] = $mdl->getClientToken();
        $data['bn_code'] = ExtensionPaypalCommerce::getBnCode();
        $data['intent'] = $that->config->get('paypal_commerce_transaction_type');
        $data['enabled_components'] = unserialize($that->config->get('paypal_commerce_enabled_components')) ?: ['buttons'];
        $data['enabled_funding'] = unserialize($that->config->get('paypal_commerce_enabled_funding')) ?: [];
        $data['create_quick_order_url'] = $that->html->getSecureURL('r/extension/paypal_commerce/createQuickOrder');
        $data['prepare_checkout_url'] = $that->html->getSecureURL('r/extension/paypal_commerce/prepareCheckout');
        $data['return_url'] = $data['cancel_url'] = $that->html->getSEOURL('checkout/cart');
        $data['capture_order_url'] = $that->html->getSecureURL('r/extension/paypal_commerce/captureOrder');
        $data['action'] = $that->html->getSecureURL('r/extension/paypal_commerce/send');
        $data['pageType'] = $data['placement'] = 'cart';
        $view->batchAssign($data);
        /** @see public_html/extensions/paypal_commerce/storefront/view/default/template/responses/paypal_commerce_buy_now.tpl */
        $ppButtons = $view->fetch('responses/paypal_commerce_buy_now.tpl');
        $that->view->addHookVar('post_top_cart_buttons', $ppButtons);

        $plConfig = json_decode(
            html_entity_decode($that->config->get('paypal_commerce_pay_later_message_config'), ENT_QUOTES, 'UTF-8'),
            true
        );

        if ($plConfig && $plConfig['cart']['status'] == 'enabled') {
            $payLaterMessage = html_entity_decode($that->config->get('paypal_commerce_pay_later_cart_message'));
            $payLaterMessage = str_replace('ENTER_VALUE_HERE', '%s', $payLaterMessage);
            if (str_contains($payLaterMessage, '%s')) {
                $payLaterMessage = sprintf(
                    $payLaterMessage,
                    $that->cart->getFinalTotal()
                );
            }
            $that->view->addHookVar('pre_top_cart_buttons', '<div style="margin: auto">' . $payLaterMessage.'</div>');
        }
    }

    /**
     * make PayPal as a pre-selected payment method
     *
     * @return void
     */
    public function onControllerPagesCheckoutFastCheckout_InitData()
    {
        $that = & $this->baseObject;
        if($this->baseObject_method == '__construct' && $that->session->data['paypal']['payment_method']){
            $that->session->data['fc']['payment_method'] = $that->session->data['paypal']['payment_method'];
            unset($that->session->data['paypal']['payment_method']);
        }
    }
}