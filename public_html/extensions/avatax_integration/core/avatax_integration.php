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

/** @noinspection PhpMultipleClassDeclarationsInspection */

use Avalara\AddressValidationInfo;
use Avalara\AvaTaxClient;
use Avalara\CreateTransactionModel;
use Avalara\DocumentType;
use Avalara\VoidReasonCode;

class ExtensionAvataxIntegration extends Extension
{

    public $errors = [];
    public $data = [];
    public $totals = [];
    public $postcode = 0;
    protected $controller;
    protected $registry;

    protected $exemptGroups = [];

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->controller = $this->baseObject;
        $this->exemptGroups = [
            ''  => '-----Please Select-----',
            'A' => 'A. Federal government (United States)',
            'B' => 'B. State government (United States)',
            'C' => 'C. Tribe / Status Indian / Indian Band (United States & Canada)',
            'D' => 'D. Foreign diplomat (United States & Canada)',
            'E' => 'E. Charitable or benevolent org (United States & Canada)',
            'F' => 'F. Religious org (United States & Canada)',
            'G' => 'G. Resale (United States & Canada)',
            'H' => 'H. Commercial agricultural production (United States & Canada)',
            'I' => 'I. Industrial production / manufacturer (United States & Canada)',
            'J' => 'J. Direct pay permit (United States)',
            'K' => 'K. Direct mail (United States)',
            'L' => 'L. Other (United States & Canada)',
            'M' => 'M. Educational Organization',
            'N' => 'N. Local government (United States)',
            //'O' => 'Not Used',
            'P' => 'P. Commercial aquaculture (Canada)',
            'Q' => 'Q. Commercial Fishery (Canada)',
            'R' => 'R. Non-resident (Canada)',
        ];
    }

    protected function isEnabled()
    {
        return $this->registry->get('config')->get('avatax_integration_status');
    }

    public function onControllerCommonListingGrid_InitData()
    {
        /** @var ControllerCommonListingGrid $that */
        $that =& $this->baseObject;
        if (in_array($that->data['table_id'], ['customer_grid', 'product_grid'])) {
            if ($that->data['table_id'] == 'customer_grid') {
                $url = $that->html->getSecureURL('sale/avatax_customer_data', '&customer_id=%ID%');
            } else {
                $url = $that->html->getSecureURL('catalog/avatax_integration', '&product_id=%ID%');
            }
            $that->loadLanguage('avatax_integration/avatax_integration');
            $that->data['actions']['dropdown']['children']['avatax_integration'] = [
                'text' => $that->language->get('avatax_integration_name'),
                'href' => $url
            ];
        }
    }

    public function onControllerPagesCatalogProductTabs_InitData()
    {
        /** @var ControllerPagesCatalogProductTabs $that */
        $that =& $this->baseObject;
        $that->loadLanguage('avatax_integration/avatax_integration');

        $this->data = [];
        $this->data['tabs'][] = [
            'href'   => $that->html->getSecureURL(
                'catalog/avatax_integration',
                '&product_id=' . (int)$that->request->get['product_id']
            ),
            'text'   => $that->language->get('avatax_integration_name'),
            'active' => ($that->data['active'] == 'avatax_integration'),
        ];

        $view = new AView(Registry::getInstance(), 0);
        $view->batchAssign($this->data);
        $that->view->addHookVar('extension_tabs', $view->fetch('pages/avatax_integration/tabs.tpl'));
    }

    public function onControllerPagesSaleCustomer_InitData()
    {
        /** @var ControllerPagesSaleCustomer $that */
        $that =& $this->baseObject;
        $that->loadLanguage('avatax_integration/avatax_integration');
        $customer_id = $that->request->get['customer_id'];
        $tabs[] = [
            'href' => $that->html->getSecureURL('sale/avatax_customer_data', '&customer_id=' . $customer_id),
            'text' => $that->language->get('avatax_integration_name'),
        ];
        foreach ($tabs as $tab) {
            $classname = $tab['active'] ? 'active' : '';
            $tab_code = '<li class="' . $classname . '">';
            $tab_code .= '    <a href="' . $tab['href'] . '"><strong>' . $tab['text'] . '</strong></a>';
            $tab_code .= '</li>';
        }
        $that->view->addHookVar('extension_tabs', $tab_code);
    }

    public function onControllerPagesSaleCustomerTransaction_InitData()
    {
        /** @var ControllerPagesSaleCustomerTransaction $that */
        $that =& $this->baseObject;
        $that->loadLanguage('avatax_integration/avatax_integration');
        $customer_id = $that->request->get['customer_id'];
        $tabs[] = [
            'href' => $that->html->getSecureURL('sale/avatax_customer_data', '&customer_id=' . $customer_id),
            'text' => $that->language->get('avatax_integration_name'),
        ];
        foreach ($tabs as $tab) {
            $classname = $tab['active'] ? 'active' : '';
            $tab_code = '<li class="' . $classname . '">';
            $tab_code .= '    <a href="' . $tab['href'] . '"><strong>' . $tab['text'] . '</strong></a>';
            $tab_code .= '</li>';
        }
        $that->view->addHookVar('extension_tabs', $tab_code);
    }

    public function onControllerPagesSaleOrder_UpdateData()
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        if ($this->baseObject_method == 'details') {
            $order_id = (int)$that->request->get['order_id'];
            /** @var ModelSaleOrder $mdl */
            $mdl = $that->load->model('sale/order');
            $order = $mdl->getOrder($order_id);
            if ($order['order_status_id'] == $that->config->get('avatax_integration_status_success_settled')
                || $order['order_status_id'] == $that->config->get('avatax_integration_status_cancel_settled')
            ) {
                $that->view->addHookVar(
                    'order_details',
                    '<div class="alert alert-danger" role="alert">'
                    . 'Avatax is already calculated and documented. Edits to this order will not be reflected on Avatax!'
                    . '</div>'
                );
            }
        }
    }

    public function onControllerPagesSaleOrder_InitData()
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        if ($this->baseObject_method == 'history') {
            /** @var ModelSaleOrder $mdl */
            $mdl = $that->load->model('sale/order');
            $order_id = (int)$that->request->get['order_id'];
            $status_id = $that->request->post['order_status_id'];
            if ($order_id && isset($status_id)) {
                $order = $mdl->getOrder($order_id);
                if ($status_id == $that->config->get('avatax_integration_status_success_settled')) {
                    $order_totals = $mdl->getOrderTotals($order_id);
                    $customer_id = (int)$order['customer_id'];
                    $customerData = [
                        'customer_id' => $customer_id,
                        'order_id'    => $order_id
                    ];
                    $this->getTax($that, $customerData, true, $order_totals);
                } elseif ($status_id == $that->config->get('avatax_integration_status_return_settled')) {
                    $order_totals = $mdl->getOrderTotals($order_id);
                    $customer_id = (int)$order['customer_id'];
                    $customerData = [
                        'customer_id' => $customer_id,
                        'order_id'    => $order_id
                    ];
                    $this->getTax($that, $customerData, true, $order_totals, true);
                } elseif ($status_id == $that->config->get('avatax_integration_status_cancel_settled')) {
                    $customer_id = (int)$order['customer_id'];
                    $customerData = [
                        'customer_id' => $customer_id,
                        'order_id'    => $order_id . '-' . date("His", strtotime($order['date_added']))
                    ];
                    //Cancel Tax
                    $this->cancelTax($customerData);
                }
            }
        }
    }

    public function onControllerResponsesListingGridOrder_UpdateData()
    {
        /** @var ControllerResponsesListingGridOrder $that */
        $that = $this->baseObject;
        if ($this->baseObject_method == 'update_field') {
            if (isset($that->request->post['order_status_id'])) {
                foreach ($that->request->post['order_status_id'] as $key => $value) {
                    $order_id = $key;
                    $status_id = $value;
                }
            }
            if (isset($order_id)
                && isset($status_id)
                && $status_id == $that->config->get('avatax_integration_status_success_settled')
            ) {
                $that->load->model('sale/order');
                $order = $that->model_sale_order->getOrder($order_id);
                $order_totals = $that->model_sale_order->getOrderTotals($order_id);
                $customer_id = $order['customer_id'];
                $cust_data = [];
                $cust_data['customer_id'] = $customer_id;
                $cust_data['order_id'] = $order_id;
                $this->getTax($that, $cust_data, true, $order_totals);
            }
            if (isset($order_id)
                && isset($status_id)
                && $status_id == $that->config->get('avatax_integration_status_return_settled')
            ) {
                $that->load->model('sale/order');
                $order = $that->model_sale_order->getOrder($order_id);
                $order_totals = $that->model_sale_order->getOrderTotals($order_id);
                $customer_id = $order['customer_id'];
                $cust_data = [];
                $cust_data['customer_id'] = $customer_id;
                $cust_data['order_id'] = $order_id;
                $this->getTax($that, $cust_data, true, $order_totals, true);
            }
            if (isset($order_id)
                && isset($status_id)
                && $status_id == $that->config->get('avatax_integration_status_cancel_settled')
            ) {
                $that->load->model('sale/order');
                $order = $that->model_sale_order->getOrder($order_id);
                $customer_id = $order['customer_id'];
                $cust_data = [];
                $cust_data['customer_id'] = $customer_id;
                $cust_data['order_id'] = $order_id . '-' . date("His", strtotime($order['date_added']));
                //Cancel Tax
                $this->cancelTax($cust_data);
            }
        }
    }

    /**
     * @param int $order_id
     *
     * @throws AException
     */
    public function setOrderProductTaxCodes($order_id)
    {
        $that = $this->baseObject;
        $that->load->model('account/order');
        $product_data = $that->model_account_order->getOrderProducts($order_id);
        /** @var ModelExtensionAvataxIntegration $mdl */
        $mdl = $that->load->model('extension/avatax_integration', 'storefront');
        foreach ($product_data as $values) {
            $taxCodeValue = $mdl->getProductTaxCode((int)$values['product_id']);
            $mdl->setOrderProductTaxCode((int)$values['order_product_id'], (string)$taxCodeValue);
        }
    }

    /**
     * @param $total_data
     *
     * @return float|int
     */
    public function calcTotalDiscount($total_data)
    {
        $total_discount = 0;
        foreach ($total_data as $value) {
            if ($value['total_type'] == 'discount' || $value['type'] == 'discount') {
                $total_discount += -1 * $value['value'];
            }
        }
        return $total_discount;
    }

    /**
     * @param ModelTotalAvataxIntegrationTotal | ControllerPagesSaleOrder | ControllerResponsesListingGridOrder $that
     * @param array $customerData
     * @param bool $commit
     * @param int $total_data
     * @param bool $return
     *
     * @return int|false
     * @throws AException
     */
    public function getTax($that, $customerData, $commit = false, $total_data = 0, $return = false)
    {
        $load = $this->registry->get('load');
        $config = $this->registry->get('config');
        $session =& $this->registry->get('session')->data;

        if (IS_ADMIN === true) {
            $order_id = (int)$customerData['order_id'];
        } elseif ($session['avatax_order_id']) {
            $order_id = (int)$session['avatax_order_id'];
        } elseif (isset($session['order_id'])) {
            $order_id = (int)$session['order_id'];
        } else {
            $order_id = 0;
        }

        if (IS_ADMIN === true) {
            $customer = null;
            $customer_id = $customerData['customer_id'];
        } else {
            $customer = $this->registry->get('customer');
            $customer_id = $this->registry->get('customer')?->getId();
        }

        $customerAddress = [];
        /** @var ModelExtensionAvataxIntegration $avataxModel */
        $avataxModel = $load->model('extension/avatax_integration');
        if (IS_ADMIN !== true) {
            /** @var ModelAccountAddress $mdl */
            $mdl = $load->model('account/address');
            if ($config->get('config_tax_customer') == 0) {
                if (!$this->registry->get('customer')->isLogged() && isset($customerData['guest']['shipping'])) {
                    $customerAddress = $customerData['guest']['shipping'];
                } else {
                    $customerAddress = $mdl->getAddress($customerData['shipping_address_id'] ?: $session['shipping_address_id']);
                }
            } elseif ($config->get('config_tax_customer') == 1) {
                if (!$this->registry->get('customer')->isLogged() && isset($customerData['guest'])) {
                    $customerAddress = $customerData['guest'];
                } else {
                    $customerAddress = $mdl->getAddress($customerData['payment_address_id'] ?: $session['payment_address_id']);
                }
            }

            if ($customer && !$customerAddress) {
                $customerAddress = $mdl->getAddress($customer->getAddressId());
            }
        }

        /** @var ModelLocalisationCountry $mdl */
        $mdl = $load->model('localisation/country');
        $temp = $mdl->getCountry($config->get('config_country_id'));
        $originCountry = $temp['iso_code_2'];

        /** @var ModelLocalisationZone $mdl */
        $mdl = $load->model('localisation/zone');
        $temp = $mdl->getZone($config->get('config_zone_id'));
        $originZone = $temp['code'];

        $order = new AOrder($this->registry);
        if (IS_ADMIN) {
            /** @var ModelSaleOrder $mdl */
            $mdl = $load->model('sale/order');
            $order_data = $mdl->getOrder($order_id);
        } else {
            $order_data = $order->loadOrderData($order_id, 'any');
        }

        if ($order_id) {
            if ($order_data['date_added'] && !$return) {
                $date = (new DateTime($order_data['date_added']))->format('Y-m-d');
            } else {
                $date = date('Y-m-d');
            }

            $docCode = $order_id . '-' . date("dmy", strtotime($date));
            $products = $customerData['cart'];
            $docHash = md5(
                $docCode
                . $customerData['cart_key']
                . var_export($products, true)
                . $session['payment_address_id']
                . $session['shipping_address_id']
                . $session['guest']
            );

            if (isset($session['avatax']['getTax'][$docHash])) {
                return $session['avatax']['getTax'][$docHash];
            }
        }

        $accountNumber = $config->get('avatax_integration_account_number');
        $licenseKey = $config->get('avatax_integration_license_key');
        $environment = $config->get('avatax_integration_test_mode') ? 'sandbox' : 'production';

        if (!empty($accountNumber) && !empty($licenseKey)) {
            $client = new Avalara\AvaTaxClient(
                'AbanteCart',
                VERSION,
                SERVER_NAME ?: 'localhost',
                $environment
            );
            $client->withLicenseKey($accountNumber, $licenseKey);

            // Use TransactionBuilder instead of CreateTransactionModel
            $transactionCode = !$order_id
                ? 'ESTIMATE-' . ($customer_id ?: 'GUEST') . '-' . time()
                : 'ORDER-' . $order_id;

            $documentType = $return
                ? Avalara\DocumentType::C_RETURNINVOICE
                : Avalara\DocumentType::C_SALESINVOICE;

            $tb = new Avalara\TransactionBuilder(
                $client,
                $config->get('avatax_integration_company_code'),
                $documentType,
                $customer_id ?: 'guest'
            );

            // Set customer code
            $tb->withTransactionCode($transactionCode);

            // Set addresses
            //merchant address
            $addressLines = array_map('trim', explode(',', $config->get('config_address')));
            $line1 = $addressLines[0];
            $line2 = $addressLines[1];
            $line3 = $addressLines[2];
            unset($addressLines[0], $addressLines[1], $addressLines[2]);
            if ($addressLines) {
                $line3 .= implode(', ', $addressLines);
            }

            $tb->withAddress(
                'ShipFrom',
                $line1,
                $line2,
                $line3,
                $config->get('config_city'),
                $originZone,
                $config->get('config_postcode'),
                $originCountry
            );

            $tb->withAddress(
                'ShipTo',
                $customerAddress['address_1'] ?? $order_data['address_1'] ?? '',
                $customerAddress['address_2'] ?? $order_data['address_2'] ?? '',
                null,
                $customerAddress['city'] ?? $order_data['city'] ?? '',
                $customerAddress['zone_code'] ?? $order_data['payment_zone_code'] ?? '',
                $customerAddress['postcode'] ?? $order_data['postcode'] ?? '',
                $customerAddress['iso_code_2'] ?? $order_data['iso_code_2'] ?? ''
            );

            // Add product lines
            $products = IS_ADMIN
                ? $load->model('sale/order')->getOrderProducts($order_id)
                : $that->cart->getProducts();

            $ln = 1;
            foreach ($products as $product) {
                /** @var ModelCatalogProduct $mdl */
                $mdl = $load->model('catalog/product');
                $productData = $mdl->getProduct($product['product_id']);
                $sku = $productData['sku'] ?: 'PRODUCT-ID-' . $product['product_id'];

                $amount = $return ? -1 * $product['total'] : $product['total'];
                $taxCode = $avataxModel->getProductTaxCode((int)$product['product_id']);

                $tb->withLine($amount, $product['quantity'], $sku, $taxCode, $ln);
                $ln++;
            }

            //add freight item
            //see https://developer.avalara.com/avatax/calculating-tax/
            if (IS_ADMIN === true) {
                list($shp_method,) = explode('.', $order_data['shipping_method_key']);
                /** @var ModelSaleOrder $mdl */
                $mdl = $that->model_sale_order;
                $all_totals = $mdl->getOrderTotals($order_id);
                $shippingCost = 0.0;
                foreach ($all_totals as $t) {
                    if ($t['key'] == 'shipping') {
                        $shippingCost = $t['value'];
                        break;
                    }
                }
            } else {
                list($shp_method,) = explode('.', $session['fc']['shipping_method']['id']);
                $shippingCost = $session['fc']['shipping_method']['cost'];
            }

            // Add a shipping line if applicable
            if ($order_id && $shp_method) {
                $shippingTaxCode = '';
                if ($config->get('avatax_integration_shipping_taxcode_' . $shp_method)) {
                    $shippingTaxCode = $config->get('avatax_integration_shipping_taxcode_' . $shp_method);
                }

                //default tax_code
                $shippingTaxCode = $shippingTaxCode ?: 'FR';
                $tb->withLine(
                    $shippingCost,
                    1,
                    $shp_method,
                    $shippingTaxCode,
                    $ln
                );
                $ln++;
            }

            $customerAvataxSettings = $avataxModel->getCustomerSettings((int)$customer_id);
            if (is_array($customerAvataxSettings)
                && $customerAvataxSettings['exemption_number']
                && $customerAvataxSettings['status'] == 1 //approved
            ) {
                $tb->withExemptionNo($customerAvataxSettings['exemption_number']);
                if (!empty($customerAvataxSettings['entity_use_code'])) {
                    $tb->withEntityUseCode($customerAvataxSettings['entity_use_code']);
                }
            }

            // Set commit flag
            $shouldCommit = $commit
                ||
                ($config->get('avatax_integration_commit_documents')
                    && $order_data['order_status_id'] == $config->get('avatax_integration_status_success_settled')
                    && (!$customerAvataxSettings['exemption_number']
                        || ($customerAvataxSettings['status'] == 1 && $customerAvataxSettings['exemption_number']))
                );
            if ($shouldCommit) {
                $tb->withCommit();
            }

            // Write log if applicable
            if ($config->get('avatax_integration_logging')) {
                $log = new AWarning('AvaTax TransactionBuilder request initiated for: ' . $transactionCode);
                $log->toLog()->toDebug();
            }
            $response = $tb->createOrAdjust();
            // Handle response
            if ($config->get('avatax_integration_logging')) {
                $log = new AWarning('AvaTax response: ' . print_r($response, true));
                $log->toLog()->toDebug();
            }

            if (isset($response->totalTax)) {
                $session['avatax']['getTax'][$docHash] = $response->totalTax;
                foreach ($response->lines as $line) {
                    $lineNumber = $line->lineNumber;
                    $session['avatax']['getTaxLines'][$lineNumber]['tax_amount'] = $line->tax;
                }
                return $response->totalTax;
            } else {
                if (is_string($response)) {
                    $that->log->write('AvaTax response error: ' . $response);
                }
                return -1;
            }
        }
        return false;
    }

    protected function getTransactionHash($tb)
    {

    }

    /**
     * @param array $customerData
     * @throws AException
     */
    public function cancelTax($customerData)
    {
        $that = $this->baseObject;
        $testMode = $that->config->get('avatax_integration_test_mode');
        $accountNumber = $that->config->get('avatax_integration_account_number');
        $licenseKey = $that->config->get('avatax_integration_license_key');
        if ($accountNumber && $licenseKey) {
            // Initialize the client
            $client = new Avalara\AvaTaxClient(
                'AbanteCart',
                VERSION,
                SERVER_NAME ?: 'localhost',
                $testMode ? 'sandbox' : 'production'
            );
            $client->withLicenseKey($accountNumber, $licenseKey);

            $client->voidTransaction(
                $that->config->get('avatax_integration_company_code'),
                $customerData['order_id'],
                VoidReasonCode::C_DOCVOIDED,
                DocumentType::C_SALESINVOICE
            );
        }
    }

    public function onControllerPagesExtensionExtensions_UpdateData()
    {
        $that =& $this->baseObject;

        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN && $current_ext_id == 'avatax_integration' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" target="_blank"'
                . ' href="https://www.avalara.com/integrations/abantecart" title="Visit Avalara">'
                . '<i class="fa fa-external-link fa-lg"></i></a>';
            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }

        if ($this->baseObject_method == 'edit') {
            /** @var ModelSettingSetting $mdl */
            $mdl = $that->loadModel('setting/setting');
            $activateTotalArray = [
                'avatax_integration_total' => [
                    'avatax_integration_total_status' => (int)$that->config->get('avatax_integration_status'),
                ],
            ];
            foreach ($activateTotalArray as $group => $values) {
                $mdl->editSetting($group, $values);
            }
        }
    }

    public function onControllerResponsesListingGridExtension_UpdateData()
    {
        $that = $this->baseObject;
        if ($this->baseObject_method == 'update') {
            if (isset($that->request->post['avatax_integration']['avatax_integration_status'])) {
                $avatax_integration_total_status = null;
                if ($that->request->post['avatax_integration']['avatax_integration_status'] == 1) {
                    $avatax_integration_total_status = 1;
                }
                if ($that->request->post['avatax_integration']['avatax_integration_status'] == 0) {
                    $avatax_integration_total_status = 0;
                }
                /** @var ModelSettingSetting $mdl */
                $mdl = $that->loadModel('setting/setting');
                $activateTotalArray = [
                    'avatax_integration_total' => [
                        'avatax_integration_total_status' => $avatax_integration_total_status,
                    ],
                ];
                foreach ($activateTotalArray as $group => $values) {
                    $mdl->editSetting($group, $values);
                }
            }
        }
    }

    public function onControllerResponsesCheckoutCart_InitData()
    {
        $that =& $this->baseObject;
        if (!$that->customer->isLogged() && !$that->session->data['guest']) {
            $that->config->set('avatax_integration_total_status', 0);
        }
    }

    public function onControllerPagesCheckoutFinalize_InitData()
    {
        $that = $this->baseObject;
        if (isset($that->session->data['order_id'])) {
            $that->session->data['avatax_order_id'] = $that->session->data['order_id'];
        }
    }

    public function onControllerPagesCheckoutFinalize_ProcessData()
    {
        $that = $this->baseObject;
        $that->session->data['avatax_order_id'] = 0;
    }

    public function onControllerPagesCheckoutCart_InitData()
    {
        $that = $this->baseObject;
        $that->session->data['avatax_order_id'] = 0;
        $this->baseObject->config->set('config_shipping_tax_estimate', 0);
    }

    public function onControllerPagesCheckoutAddress_InitData()
    {
        $that = $this->baseObject;
        $that->session->data['avatax_order_id'] = 0;
    }

    /**
     * @param array $addressData
     * @return array
     * @throws AException
     */
    public function validate_address(array $addressData): array
    {
        /** @var ControllerPagesAccountAddress $that */
        $that = $this->baseObject;

        $output = [];
        if (!$addressData) {
            $output['message'] = 'Missing Address Data';
            $output['error'] = true;
            return $output;
        }

        $validCountries = $that->config->get('avatax_integration_address_validation_countries') === 'Both'
            ? ['US', 'CA']
            : explode(',', $that->config->get('avatax_integration_address_validation_countries'));

        if (is_numeric($addressData['address_id'])) {
            /** @var ModelAccountAddress $mdl */
            $mdl = $that->load->model('account/address');
            $addressData = $mdl->getAddress($addressData['address_id']);
        }

        if (!in_array($addressData['iso_code_2'], $validCountries, true)) {
            $output['message'] = 'Avatax: address validation skipped. Country code is out of allowed list.';
            $output['error'] = false;
            return $output;
        }

        try {
            $accountNumber = $that->config->get('avatax_integration_account_number');
            $licenseKey = $that->config->get('avatax_integration_license_key');
            $testMode = $that->config->get('avatax_integration_test_mode') ? 'sandbox' : 'production';

            $client = new AvaTaxClient(
                'AbanteCart',
                VERSION,
                SERVER_NAME,
                $testMode
            );
            $client->withLicenseKey($accountNumber, $licenseKey);

            // Prepare request object
            $request = new AddressValidationInfo();
            $request->line1 = $addressData['address_1'] ?: $addressData['line_1'] ?: '';
            $request->line2 = $addressData['address_2'] ?: $addressData['line_2'] ?: '';
            $request->line3 = $addressData['line_3'] ?: '';
            $request->city = $addressData['city'] ?? '';
            $request->region = $addressData['code'] ?: $addressData['region'] ?: ''; // State/Province code
            $request->country = $addressData['iso_code_2'] ?: $addressData['country'] ?: ''; // Country code (e.g., US, CA)
            $request->postalCode = $addressData['postcode'] ?: $addressData['postalCode'] ?: '';
            $request->textCase = 'Mixed'; // Optional; keeps formatting (can be 'Upper', 'Mixed', or null)

            // Make the API call for address validation
            $response = $client->resolveAddressPost($request);
            $response = is_string($response) ? json_decode($response) : $response;
            // Log the request and response
            if ($that->config->get('avatax_integration_logging') === 1) {
                $requestLog = new AWarning('AvaTax Address Validation request: ' . var_export($request, true));
                $requestLog->toLog()->toDebug();
                $responseLog = new AWarning('AvaTax Address Validation response: ' . var_export($response, true));
                $responseLog->toLog()->toDebug();
            }

            // Analyze the response
            if ($response->validatedAddresses && !$response->messages) {
                $output['error'] = false;
            } else {
                if ($response->messages) {
                    $messages = array_column($response->messages, 'summary');
                } elseif ($response->error) {
                    $messages = array_column($response->error->details, 'message');
                } else {
                    $messages = [];
                }
                $output['message'] = implode("\n", $messages);
                $output['error'] = true;
            }
        } catch (Exception|Error $e) {
            $output['message'] = $e->getMessage();
            $output['error'] = true;
            if ($that->config->get('avatax_integration_logging')) {
                $errorLog = new AWarning('AVALARA API Address Validation Error: ' . $e->getMessage());
                $errorLog->toLog()->toDebug();
            }
        }
        return $output;
    }

    public function onControllerPagesAccountEdit_InitData()
    {
        /** @var ControllerPagesAccountEdit $that */
        $that = $this->baseObject;
        if ($that->request->is_POST() && $that->request->post['exemption_number']) {
            /** @var ModelExtensionAvataxIntegration $mdl */
            $mdl = $that->loadModel('extension/avatax_integration');
            $customer_settings = $mdl->getCustomerSettings($that->customer->getId());
            if (in_array($customer_settings['status'], [0, 2])) {
                $that->loadLanguage('avatax_integration/avatax_integration');
                $mdl->setCustomerSettings(
                    $that->customer->getId(),
                    [
                        'exemption_number' => $that->request->post['exemption_number'],
                        'entity_use_code'  => $that->request->post['entity_use_code'],
                    ]
                );
                $that->messages->saveNotice(
                    $that->language->get('avatax_integration_review_number_title'),
                    $that->language->getAndReplace(
                        'avatax_integration_review_number_message',
                        replaces: $that->customer->getId()
                    ),
                    false
                );
            }
        }
    }

    public function onControllerPagesAccountEdit_UpdateData()
    {
        /** @var ControllerPagesAccountEdit $that */
        $that = $this->baseObject;

        $data = [];
        /** @var ModelExtensionAvataxIntegration $mdl */
        $mdl = $that->loadModel('extension/avatax_integration');
        $that->loadLanguage('avatax_integration/avatax_integration');
        $data['text_tax_exemption'] = $that->language->get('avatax_integration_text_tax_exemption');

        $customer_settings = $mdl->getCustomerSettings($that->customer->getId());
        $data['form'] = ['fields' => []];
        if ($customer_settings['status'] == 1) {
            $data['text_status'] = $that->language->get('avatax_integration_status_approved');
        } else {
            if ($customer_settings['status'] == 0 && $customer_settings['exemption_number']) {
                $data['text_status'] = $that->language->get('avatax_integration_status_pending');
            } elseif ($customer_settings['status'] == 2) {
                $data['text_status'] = $that->language->get('avatax_integration_status_declined');
            }
            if (!$customer_settings['exemption_number'] || $customer_settings['status'] == 2) {
                $form = new AForm();
                $form->setForm(['form_name' => 'AccountFrm']);
                $data['entry_exemption_number'] = $that->language->get('avatax_integration_exemption_number');
                $data['form']['fields']['exemption_number'] = $form->getFieldHtml(
                    [
                        'type'  => 'input',
                        'name'  => 'exemption_number',
                        'value' => $customer_settings['exemption_number'],
                        'style' => 'highlight',
                    ]
                );
                $data['entry_entity_use_code'] = $that->language->get('avatax_integration_entity_use_code');
                $data['form']['fields']['entity_use_code'] = $form->getFieldHtml(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'entity_use_code',
                        'value'   => $customer_settings['entity_use_code'],
                        'options' => $this->exemptGroups,
                    ]
                );
            }
        }

        if ($data['text_status']) {
            $data['entry_status'] = $that->language->get('avatax_integration_status');
        }

        $view = new AView($this->registry, 0);
        $view->batchAssign($data);
        $that->view->addHookVar('customer_attributes', $view->fetch('pages/account/tax_exempt_edit.tpl'));
    }

    public function onControllerPagesAccountCreate_UpdateData()
    {
        $data = [];
        /** @var ControllerPagesAccountCreate $that */
        $that = $this->baseObject;
        /** @var ModelExtensionAvataxIntegration $mdl */
        $mdl = $that->loadModel('extension/avatax_integration');
        $that->loadLanguage('avatax_integration/avatax_integration');

        if ($that->request->is_POST() && $that->data['customer_id']
            && $that->request->post['exemption_number']
            && !$that->errors
        ) {
            $customer_id = (int)$that->data['customer_id'];
            $customer_settings = $mdl->getCustomerSettings($customer_id);
            if (in_array($customer_settings['status'], [0, 2])) {
                $mdl->setCustomerSettings(
                    $customer_id,
                    [
                        'exemption_number' => $that->request->post['exemption_number'],
                        'entity_use_code'  => $that->request->post['entity_use_code'],
                    ]
                );
                $that->messages->saveNotice(
                    $that->language->get('avatax_integration_review_number_title'),
                    $that->language->getAndReplace(
                        'avatax_integration_review_number_message',
                        replaces: $customer_id
                    ),
                    false
                );
            }
            return null;
        }

        $data['text_tax_exemption'] = $that->language->get('avatax_integration_text_tax_exemption');
        $data['form'] = ['fields' => []];
        $form = new AForm();
        $form->setForm(['form_name' => 'AccountFrm']);
        $data['entry_exemption_number'] = $that->language->get('avatax_integration_exemption_number');
        $data['form']['fields']['exemption_number'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'exemption_number',
                'value' => $that->request->post['exemption_number'],
                'style' => 'highlight',
            ]
        );
        $data['entry_entity_use_code'] = $that->language->get('avatax_integration_entity_use_code');
        $data['form']['fields']['entity_use_code'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'entity_use_code',
                'value'   => $that->request->post['entity_use_code'],
                'options' => $this->exemptGroups,
            ]
        );

        $view = new AView($this->registry, 0);
        $view->batchAssign($data);
        $that->view->addHookVar('customer_attributes', $view->fetch('pages/account/tax_exempt_create.tpl'));
    }

    /** @see  ModelAccountAddress::validateAddressData() */
    public function onModelAccountAddress_ValidateData()
    {
        /** @var ModelAccountAddress $that */
        $that = $this->baseObject;
        if ($that->config->get('avatax_integration_status')
            && $that->config->get('avatax_integration_address_validation')
            && !$that->error
        ) {
            $address = func_get_arg(0)['address'];
            if ($address['country_id']) {
                /** @var ModelLocalisationCountry $mdl */
                $mdl = $that->load->model('localisation/country');
                $countryDetails = $mdl->getCountry($address['country_id']);
                if ($countryDetails) {
                    $address['iso_code_2'] = $countryDetails['iso_code_2'];
                }
            }
            if ($address['zone_id']) {
                /** @var ModelLocalisationZone $mdl */
                $mdl = $that->load->model('localisation/zone');
                $zoneDetails = $mdl->getZone($address['zone_id']);
                if ($zoneDetails) {
                    $address['code'] = $zoneDetails['code'];
                }
            }

            $result = $this->validate_address($address);
            if ($result['error']) {
                $that->error['warning'] = $result['message'];
            }
        }
    }
}