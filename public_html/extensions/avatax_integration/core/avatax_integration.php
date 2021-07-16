<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

/** @noinspection PhpUndefinedClassInspection */

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

require 'vendor/autoload.php';

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
        if ($that->data['table_id'] == 'customer_grid') {
            $that->loadLanguage('avatax_integration/avatax_integration');
            $that->data['actions']['dropdown']['children']['avatax_integration'] = [
                'text' => $that->language->get('avatax_integration_name'),
                'href' => $that->html->getSecureURL('sale/avatax_customer_data', '&customer_id=%ID%'),
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
                '&product_id='.$that->request->get['product_id']
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
        $avatax_tab[] = [
            'href' => $that->html->getSecureURL('sale/avatax_customer_data', '&customer_id='.$customer_id),
            'text' => $that->language->get('avatax_integration_name'),
        ];
        foreach ($avatax_tab as $tab) {
            if ($tab['active']) {
                $classname = 'active';
            } else {
                $classname = '';
            }

            $tab_code = '<li class="'.$classname.'">';
            $tab_code .= '    <a href="'.$tab['href'].'"><strong>'.$tab['text'].'</strong></a>';
            $tab_code .= '</li>';
        }
        $that->view->addHookVar('extension_tabs', $tab_code);
    }

    public function onControllerPagesSaleCustomerTransaction_InitData()
    {
        $that =& $this->baseObject;
        $that->loadLanguage('avatax_integration/avatax_integration');
        $customer_id = $that->request->get['customer_id'];
        $avatax_tab[] = [
            'href' => $that->html->getSecureURL('sale/avatax_customer_data', '&customer_id='.$customer_id),
            'text' => $that->language->get('avatax_integration_name'),
        ];
        foreach ($avatax_tab as $tab) {
            if ($tab['active']) {
                $classname = 'active';
            } else {
                $classname = '';
            }

            $tab_code = '<li class="'.$classname.'">';
            $tab_code .= '    <a href="'.$tab['href'].'"><strong>'.$tab['text'].'</strong></a>';
            $tab_code .= '</li>';
        }
        $that->view->addHookVar('extension_tabs', $tab_code);
    }

    public function onControllerPagesSaleCustomerTabs_InitData()
    {
        $that = &$this->baseObject;
        $that->loadLanguage('avatax_integration/avatax_integration');

        $this->data = [];
        $this->data['tabs'][] = [
            'href'   => $that->html->getSecureURL(
                'catalog/avatax_integration',
                '&product_id='.$that->request->get['product_id']
            ),
            'text'   => $that->language->get('avatax_integration_name'),
            'active' => ($that->data['active'] == 'avatax_integration'),
        ];

        $view = new AView(Registry::getInstance(), 0);
        $view->batchAssign($this->data);
        $that->view->addHookVar('extension_tabs', $view->fetch('pages/avatax_integration/tabs.tpl'));
    }

    public function onControllerPagesSaleOrder_UpdateData()
    {
        $that = $this->baseObject;
        if ($this->baseObject_method == 'details') {
            $order_id = $that->request->get['order_id'];
            $that->load->model('sale/order');
            $order = $that->model_sale_order->getOrder($order_id);
            if ($order['order_status_id'] == $that->config->get('avatax_integration_status_success_settled')
                || $order['order_status_id'] == $that->config->get('avatax_integration_status_cancel_settled')
            ) {
                $that->view->addHookVar(
                    'order_details',
                    '<div class="alert alert-danger" role="alert">'
                    .'Avatax is already calculated and documented. Edits to this order will not be reflected on Avatax!'
                    .'</div>'
                );
            }
        }
    }

    public function onControllerPagesSaleOrder_InitData()
    {
        /** @var ControllerPagesSaleOrder $that */
        $that = $this->baseObject;
        if ($this->baseObject_method == 'history') {
            if (isset($that->request->post['order_status_id'])) {
                $order_id = $that->request->get['order_id'];
                $status_id = $that->request->post['order_status_id'];
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
                $cust_data['order_id'] = $order_id.'-'.date("His", strtotime($order['date_added']));
                //Cancel Tax
                $this->cancelTax($cust_data);
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
                $cust_data['order_id'] = $order_id.'-'.date("His", strtotime($order['date_added']));
                //Cancel Tax
                $this->cancelTax($cust_data);
            }
        }
    }

    public function onControllerPagesCheckoutGuestStep3_UpdateData()
    {
        $that = $this->baseObject;

        if ($that->config->get('avatax_integration_status')
            && $that->config->get('avatax_integration_address_validation')
        ) {
            $address_info = $that->session->data['guest'];
            $address_info['address_id'] = 'guest';
            $res = $this->validate_address($address_info);
            if ($res['error']) {
                $that->loadLanguage('avatax_integration/avatax_integration');
                $that->view->assign(
                    'error_warning',
                    $that->language->get('avatax_integration_address_validation_error')
                );
            }
            $that->view->addHookVar('payment_method', '&nbsp;');
        }

        if (isset($that->session->data['order_id'])) {
            $this->setOrderProductTaxCodes($that->session->data['order_id']);
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
            $taxCodeValue = $mdl->getProductTaxCode($values['product_id']);
            $mdl->setOrderProductTaxCode($values['order_product_id'], $taxCodeValue);
        }
    }

    public function onControllerPagesCheckoutConfirm_UpdateData()
    {
        $that = $this->baseObject;
        if ($that->config->get('avatax_integration_status')
            && $that->config->get('avatax_integration_address_validation')
        ) {
            if (!$that->customer->isLogged() && $that->session->data['guest']) {
                $address_info = $that->session->data['guest'];
                $address_info['address_id'] = 'guest';
            } else {
                $address_id = $that->session->data['shipping_address_id']
                    ? : $that->session->data['payment_address_id'];
                $address_info = ['address_id' => $address_id];
            }
            $res = $this->validate_address($address_info);
            if ($res['error']) {
                $that->loadLanguage('avatax_integration/avatax_integration');
                $that->view->assign(
                    'error_warning',
                    $that->language->get('avatax_integration_address_validation_error')
                );
            }
            $that->view->addHookVar('payment_method', '&nbsp;');
        }
        if (isset($that->session->data['order_id'])) {
            $this->setOrderProductTaxCodes($that->session->data['order_id']);
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
     * @param      $cust_data
     * @param bool $commit
     * @param int $total_data
     * @param bool $return
     *
     * @return int|false
     * @throws AException
     */
    public function getTax($that, $cust_data, $commit = false, $total_data = 0, $return = false)
    {
        $docHash = '';
        $load = $this->registry->get('load');
        $config = $this->registry->get('config');
        $session = $this->registry->get('session')->data['fc'] && $config->get('fast_checkout_status')
            ? $this->registry->get('session')->data['fc']
            : $this->registry->get('session')->data;
        $order_id = 0;

        if (IS_ADMIN === true) {
            $order_id = $cust_data['order_id'];
        } else {
            if (isset($session['avatax_order_id'])) {
                $order_id = $session['avatax_order_id'];
            }
        }

        if (IS_ADMIN === true) {
            $customer = null;
            $customer_id = $cust_data['customer_id'];
        } else {
            $customer = $this->registry->get('customer');
            $customer_id = $this->registry->get('customer') ? $this->registry->get('customer')->getId() : null;
        }
        $customerAddress = [];
        /** @var ModelExtensionAvataxIntegration $avataxModel */
        $avataxModel = $load->model('extension/avatax_integration');
        if (IS_ADMIN !== true) {
            /** @var ModelAccountAddress $mdl */
            $mdl = $load->model('account/address');
            //when shipping address take for taxes
            if ($config->get('config_tax_customer') == 0) {
                //for guest
                if (!$this->registry->get('customer')->isLogged()
                    && isset($cust_data['guest']['shipping'])
                ) {
                    $customerAddress = $cust_data['guest']['shipping'];
                }
                //for registered customer
                else{
                    $customerAddress = $mdl->getAddress($cust_data['shipping_address_id'] ?: $session['shipping_address_id']);
                }
            }
            //when payment address takes for taxes
            elseif ($config->get('config_tax_customer') == 1) {
                //for guest
                if (!$this->registry->get('customer')->isLogged()
                    && isset($cust_data['guest'])
                ) {
                    $customerAddress = $cust_data['guest'];
                }//for registered customer
                else{
                    $customerAddress = $mdl->getAddress($cust_data['payment_address_id'] ?: $session['payment_address_id']);
                }
            }

            if ($customer && !$customerAddress) {
                $customerAddress = $mdl->getAddress($customer->getAddressId());
            }
        }
        //Store Country iso_code_2 data
        /** @var ModelLocalisationCountry $mdl */
        $mdl = $load->model('localisation/country');
        $temp = $mdl->getCountry($config->get('config_country_id'));
        $originCountry = $temp['iso_code_2'];
        //Store Zone value
        /** @var ModelLocalisationZone $mdl */
        $mdl = $load->model('localisation/zone');
        $temp = $mdl->getZone($config->get('config_zone_id'));
        $originZone = $temp['code'];
        //Order data
        $order = new AOrder($this->registry);

        if (IS_ADMIN) {
            /** @var ModelSaleOrder $mdl */
            $mdl = $load->model('sale/order');
            $order_data = $mdl->getOrder($order_id);
        } else {
            $order_data = $order->loadOrderData($order_id, 'any');
        }

        // Header Level Elements
        // Required Header Level Elements
        $serviceURL = $config->get('avatax_integration_service_url');
        $accountNumber = $config->get('avatax_integration_account_number');
        $licenseKey = $config->get('avatax_integration_license_key');

        if (!empty($serviceURL) && !empty($accountNumber) && !empty($licenseKey)) {
            $taxSvc = new AvaTax\TaxServiceRest($serviceURL, $accountNumber, $licenseKey);
            $getTaxRequest = new AvaTax\GetTaxRequest();

            // Document Level Elements
            // Required Request Parameters
            $cust_data['customer_id'] = $customer_id ? : 'guest';
            $getTaxRequest->setCustomerCode($cust_data['customer_id']);
            if ($order_data['date_added'] && $return == false) {
                $date = new DateTime($order_data['date_added']);
                $date = $date->format('Y-m-d');
            } else {
                $date = date('Y-m-d');
            }
            // Best Practice Request Parameters
            $getTaxRequest->setCompanyCode($config->get('avatax_integration_company_code'));
            $getTaxRequest->setClient('AbanteCart');

            $customer_settings = $avataxModel->getCustomerSettings(
                $cust_data['customer_id']
            );
            if ($order_id) {
                $docCode = $order_id.'-'.date("dmy", strtotime($date));
                $docHash = md5(
                    $docCode
                    .serialize($cust_data)
                    .$session['payment_address_id']
                    .$session['shipping_address_id']
                    .$session['guest']
                );

                if ($session['avatax']['getTax'][$docHash]) {
                    return $session['avatax']['getTax'][$docHash];
                }

                $getTaxRequest->setDocDate($date);
                $getTaxRequest->setDocCode($docCode);
                $getTaxRequest->setDetailLevel(AvaTax\DetailLevel::$Tax);
                //commit doc by parameter. ON sf-side it always false
                if ($commit == true) {
                    $getTaxRequest->setCommit(true);
                } //commit doc by settings
                elseif ($config->get('avatax_integration_commit_documents')
                    && ($order_data['order_status_id'] == $config->get('avatax_integration_status_success_settled'))
                    && (!$customer_settings['exemption_number']
                        || $customer_settings['status'] == 1
                        && $customer_settings['exemption_number'])
                ) {
                    $getTaxRequest->setCommit(true);
                } else {
                    $getTaxRequest->setCommit(false);
                }
                $getTaxRequest->setDocType(AvaTax\DocumentType::$SalesInvoice);
            }
            // Situational Request Parameters

            if (is_array($customer_settings)) {
                //if approved
                if ($customer_settings['exemption_number'] && $customer_settings['status'] == 1) {
                    $getTaxRequest->setCustomerUsageType($customer_settings['entity_use_code']);
                    $getTaxRequest->setExemptionNo($customer_settings['exemption_number']);
                }
            }

            if (is_array($total_data)) {
                $getTaxRequest->setDiscount($this->calcTotalDiscount($total_data));
            }
            if ($order_data['date_added'] && $return == true) {
                $date = new DateTime($order_data['date_added']);
                $date = $date->format('Y-m-d');
                $taxOverride = new AvaTax\TaxOverride();
                $taxOverride->setTaxOverrideType("TaxDate");
                $taxOverride->setReason("Adjustment for return");
                $taxOverride->setTaxDate($date);
                // $taxOverride->setTaxAmount("0");
                $getTaxRequest->setTaxOverride($taxOverride);
            }
            // Optional Request Parameters
            $getTaxRequest->setPurchaseOrderNo($order_id);
            //$getTaxRequest->setReferenceCode($order_id);
            $getTaxRequest->setPosLaneCode("09");
            $getTaxRequest->setCurrencyCode($cust_data['CurrencyCode']);

            // Address Data
            $addresses = [];
            $address1 = new AvaTax\Address();
            $address1->setAddressCode("1");
            $address1->setLine1($config->get('config_address'));
            //$address1->setCity('New York');
            $address1->setRegion($originZone);
            $address1->setCountry($originCountry);
            $address1->setPostalCode($config->get('avatax_integration_postal_code'));
            $addresses[] = $address1;
            $address2 = new AvaTax\Address();
            $address2->setAddressCode("2");

            if (isset($order_data['shipping_postcode']) && !empty($order_data['shipping_postcode'])
                && $config->get('config_tax_customer') == 0
            ) {
                $address2->setLine1(
                    $customerAddress['address_1'] ? : $order_data['shipping_address_1']
                );
                $address2->setLine2(
                    $customerAddress['address_2'] ? : $order_data['shipping_address_2']
                );
                $address2->setCity(
                    $customerAddress['city'] ? : $order_data['shipping_city']
                );
                $address2->setRegion(
                    $customerAddress['zone_code'] ? : $order_data['shipping_zone_code']
                );
                $address2->setCountry(
                    $customerAddress['iso_code_2'] ? : $order_data['shipping_iso_code_2']
                );
                $address2->setPostalCode(
                    $customerAddress['postcode'] ? : $order_data['shipping_postcode']
                );
            } elseif (isset($order_data['payment_postcode'])
                && !empty($order_data['payment_postcode'])
                && $config->get('config_tax_customer') == 1
            ) {
                $address2->setLine1(
                    $customerAddress['address_1'] ? : $order_data['payment_address_1']
                );
                $address2->setLine2(
                    $customerAddress['address_2'] ? : $order_data['payment_address_2']
                );
                $address2->setCity(
                    $customerAddress['city'] ? : $order_data['payment_city']
                );
                $address2->setRegion(
                    $customerAddress['zone_code'] ? : $order_data['payment_zone_code']
                );
                $address2->setCountry(
                    $customerAddress['iso_code_2'] ? : $order_data['payment_iso_code_2']
                );
                $address2->setPostalCode(
                    $customerAddress['postcode'] ? : $order_data['payment_postcode']
                );
            } else {
                $address2->setLine1(
                    $customerAddress['address_1'] ? : $order_data['address_1']
                );
                $address2->setLine2(
                    $customerAddress['address_2'] ? : $order_data['address_2']
                );
                $address2->setCity(
                    $customerAddress['city'] ? : $order_data['city']
                );
                $address2->setRegion(
                    $customerAddress['zone_code'] ? : $order_data['zone_code']
                );
                $address2->setCountry(
                    $customerAddress['iso_code_2'] ? : $order_data['iso_code_2']
                );
                $address2->setPostalCode(
                    $customerAddress['postcode'] ? : $order_data['postcode']
                );
            }

            $addresses[] = $address2;

            $getTaxRequest->setAddresses($addresses);

            // Line Data
            // Required Parameters
            $lines = $linesMapping = [];
            //Product model
            if ($order_id) {
                if (!IS_ADMIN) {
                    /** @var ModelAccountOrder $mdl */
                    $mdl = $load->model('account/order');
                } else {
                    /** @var ModelSaleOrder $mdl */
                    $mdl = $load->model('sale/order');
                }
                $product_data = $mdl->getOrderProducts($order_id);
                $counter = 1;
                foreach ($product_data as $values) {
                    $line = new AvaTax\Line();
                    $line->setLineNo($counter);
                    //getting sku
                    /** @var ModelCatalogProduct $mdl */
                    $mdl = $load->model('catalog/product');
                    $tmp = $mdl->getProduct($values['product_id']);
                    $itemCode = $tmp['sku'] ?: $values['product_id'];
                    $line->setItemCode($itemCode);
                    $linesMapping[$counter] = [
                                            'line_type' => 'item',
                                            'item_code' => $itemCode
                    ];

                    $line->setQty($values['quantity']);
                    if ($return == false) {
                        $line->setAmount($values['total']);
                    } else {
                        $line->setAmount(-1 * $values['total']);
                    }
                    $line->setOriginCode("1");
                    $line->setDestinationCode("2");
                    if ($total_data != 0) {
                        $line->setDiscounted(true);
                    }
                    // Best Practice Request Parameters
                    $line->setDescription($values['name']);
                    $line->setTaxCode(
                        $avataxModel->getProductTaxCode($values['product_id'])
                    );
                    $lines[] = $line;
                    $counter++;
                }
            } else {  //In this step we have not Order. Calculate tax by Cart data
                $cart_products = $that->cart->getProducts() + $that->cart->getVirtualProducts();
                $counter = 1;
                foreach ($cart_products as $values) {
                    $line = new AvaTax\Line();
                    $line->setLineNo($counter);
                    $itemCode = $values['key'];
                    $line->setItemCode($itemCode);
                    $linesMapping[$counter] = [
                                                'line_type' => 'item',
                                                'item_code' => $itemCode
                                                ];
                    $line->setQty($values['quantity']);
                    $line->setAmount($values['total']);
                    if ($total_data != 0) {
                        $line->setDiscounted(true);
                    }
                    $line->setOriginCode("1");
                    $line->setDestinationCode("2");
                    // Best Practice Request Parameters
                    $line->setDescription($values['name']);
                    $line->setTaxCode(
                        $avataxModel->getProductTaxCode($values['product_id'])
                    );
                    $lines[] = $line;
                    $counter++;
                }
            }

            //add freight item
            //see https://developer.avalara.com/avatax/calculating-tax/
            if (IS_ADMIN == true) {
                list($shp_method,) = explode('.', $order_data['shipping_method_key']);
                $shp_title = $order_data['shipping_method'];
                /** @var ModelSaleOrder $mdl */
                $mdl = $that->model_sale_order;
                $all_totals = $mdl->getOrderTotals($order_id);
                $shp_cost = 0.0;
                foreach ($all_totals as $t) {
                    if ($t['key'] == 'shipping') {
                        $shp_cost = $t['value'];
                        break;
                    }
                }
            } else {
                list($shp_method,) = explode('.', $session['shipping_method']['id']);
                $shp_title = $session['shipping_method']['title'];
                $shp_cost = $session['shipping_method']['cost'];
            }

            if ($shp_method) {
                $freight_tax_code = '';
                if ($config->get('avatax_integration_shipping_taxcode_'.$shp_method)) {
                    $freight_tax_code = $config->get('avatax_integration_shipping_taxcode_'.$shp_method);
                }

                //default tax_code
                $freight_tax_code = !$freight_tax_code ? 'FR' : $freight_tax_code;
                $line = new AvaTax\Line();
                $line->setLineNo($counter);
                $line->setItemCode($shp_method);
                $linesMapping[$counter] = [
                                        'line_type' => 'shipping',
                                        'item_code' => $shp_method
                                        ];
                $line->setQty(1);
                $line->setAmount($shp_cost);
                if ($total_data != 0) {
                    $line->setDiscounted(true);
                }
                $line->setOriginCode("1");
                $line->setDestinationCode("2");
                // Best Practice Request Parameters
                $line->setDescription($shp_title);
                $line->setTaxCode($freight_tax_code);
                $lines[] = $line;
            }

            $getTaxRequest->setLines($lines);

            //Write Log
            if ($config->get('avatax_integration_logging') == 1) {
                $message = print_r($getTaxRequest, true);
                $warning = new AWarning('AVATAX transaction: '.$message);
                $warning->toLog()->toDebug();
            }
            if ($address1->PostalCode != "" && $address2->PostalCode != "" && !empty($getTaxRequest->Lines)) {
                $getTaxResult = $taxSvc->getTax($getTaxRequest);
                if ($config->get('avatax_integration_logging') == 1) {
                    $message = print_r($getTaxResult, true);
                    $warning = new AWarning('AVATAX result of transaction: '.$message);
                    $warning->toLog()->toDebug();
                }
                //Get Results
                if ($getTaxResult->getResultCode() != AvaTax\SeverityLevel::$Success) {
                    $resultMessage = "Result Code: ".$getTaxResult->getResultCode()." \n ";
                    foreach ($getTaxResult->getMessages() as $message) {
                        $resultMessage .= $message->getSeverity().": ".$message->getSummary()."\n";
                    }
                    $warning = new AWarning("Fault of AVATAX calculation: \n ".$resultMessage);
                    $warning->toDebug();
                    if ($config->get('avatax_integration_logging')) {
                        $warning->toLog()->toDebug();
                    }
                } else {
                    $output = $getTaxResult->getTotalTax();
                    if( $this->registry->get('session')->data['fc'] && $config->get('fast_checkout_status') ){
                        $sess =& $this->registry->get('session')->data['fc'];
                    }else{
                        $sess =& $this->registry->get('session')->data;
                    }
                    $sess['avatax']['getTax'] = $output;
                    $taxLines = $getTaxResult->getTaxLines();

                    foreach($taxLines as $line){
                        /** @var \AvaTax\TaxLine $line */
                        $linesMapping[$line->getLineNo()]['tax_amount'] = $line->getTaxCalculated();
                    }

                    $sess['avatax']['getTaxLines'] = $linesMapping;
                    return $output;
                }
            } else {
                return -1;
            }
        }
        return false;
    }

    /**
     * @param array $cust_data
     */
    public function cancelTax($cust_data)
    {
        $that = $this->baseObject;
        $serviceURL = $that->config->get('avatax_integration_service_url');
        $accountNumber = $that->config->get('avatax_integration_account_number');
        $licenseKey = $that->config->get('avatax_integration_license_key');
        if (!empty($serviceURL) && !empty($accountNumber) && !empty($licenseKey)) {
            $taxSvc = new AvaTax\TaxServiceRest($serviceURL, $accountNumber, $licenseKey);
            $cancelTaxRequest = new AvaTax\CancelTaxRequest();

            // Required Request Parameters
            $cancelTaxRequest->setCompanyCode($that->config->get('avatax_integration_company_code'));
            $cancelTaxRequest->setDocType(AvaTax\DocumentType::$SalesInvoice);
            $cancelTaxRequest->setDocCode($cust_data['order_id']);
            $cancelTaxRequest->setCancelCode(AvaTax\CancelCode::$DocVoided);

            $taxSvc->cancelTax($cancelTaxRequest);
        }
    }

    public function onControllerPagesExtensionExtensions_UpdateData()
    {
        $that =& $this->baseObject;

        $current_ext_id = $that->request->get['extension'];
        if (IS_ADMIN && $current_ext_id == 'avatax_integration' && $this->baseObject_method == 'edit') {
            $html = '<a class="btn btn-white tooltips" target="_blank"'
                .' href="https://www.avalara.com/integrations/abantecart" title="Visit Avalara">'
                .'<i class="fa fa-external-link fa-lg"></i></a>';

            $that->view->addHookVar('extension_toolbar_buttons', $html);
        }

        if ($this->baseObject_method == 'edit') {
            if ($that->config->get('avatax_integration_status') == 1) {
                $avatax_integration_total_status = 1;
            } else {
                $avatax_integration_total_status = 0;
            }
            $that->loadModel('setting/setting');
            $activateTotalArray = [
                'avatax_integration_total' => [
                    'avatax_integration_total_status' => $avatax_integration_total_status,
                ],
            ];
            foreach ($activateTotalArray as $group => $values) {
                $that->model_setting_setting->editSetting($group, $values);
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
                $that->loadModel('setting/setting');
                $activateTotalArray =
                    [
                        'avatax_integration_total' => [
                            'avatax_integration_total_status' => $avatax_integration_total_status,
                        ],
                    ];
                foreach ($activateTotalArray as $group => $values) {
                    $that->model_setting_setting->editSetting($group, $values);
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

    public function onControllerPagesCheckoutSuccess_InitData()
    {
        $that = $this->baseObject;
        if (isset($that->session->data['order_id'])) {
            $that->session->data['avatax_order_id'] = $that->session->data['order_id'];
        }
    }

    public function onControllerPagesCheckoutSuccess_ProcessData()
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

    public function onControllerPagesCheckoutGuestStep1_InitData()
    {
        $that = $this->baseObject;
        $that->session->data['avatax_order_id'] = 0;
    }

    public function onControllerPagesCheckoutGuestStep2_InitData()
    {
        $that = $this->baseObject;
        $that->session->data['avatax_order_id'] = 0;
    }

    public function onControllerPagesCheckoutAddress_InitData()
    {
        $that = $this->baseObject;
        $that->session->data['avatax_order_id'] = 0;
    }

    /**
     * @param array $address_data
     *
     * @return array
     * @throws AException
     */
    public function validate_address($address_data)
    {
        $ret = [];
        if (!is_array($address_data)) {
            $ret['message'] = "Missing Address Data";
            $ret['error'] = true;
            return $ret;
        }

        $that = $this->baseObject;
        require_once DIR_EXTENSIONS.'avatax_integration/core/vendor/autoload.php';

        // Header Level Elements
        // Required Header Level Elements
        $serviceURL = $that->config->get('avatax_integration_service_url');
        $accountNumber = $that->config->get('avatax_integration_account_number');
        $licenseKey = $that->config->get('avatax_integration_license_key');

        $countryForValidate = $that->config->get('avatax_integration_address_validation_countries');
        if ($countryForValidate == 'Both') {
            $countryISO = "US,CA";
        } else {
            $countryISO = $countryForValidate;
        }

        $that->load->model('account/address');
        $addressSvc = new AvaTax\AddressServiceRest($serviceURL, $accountNumber, $licenseKey);
        $address = new AvaTax\Address();
        if ($address_data['address_id'] == 'guest' || !$address_data['address_id']) {
            $customerAddress = $address_data;
        } else {
            $customerAddress = $that->model_account_address->getAddress($address_data['address_id']);
        }

        if (is_int(strpos($countryISO, (string) $customerAddress['iso_code_2']))) {
            // Required Request Parameters
            $address->setLine1($customerAddress['address_1']);
            $address->setCity($customerAddress['city']);
            $address->setRegion($customerAddress['zone_code']);

            // Optional Request Parameters
            $address->setLine2($customerAddress['address_2']);
            $address->setCountry($customerAddress['iso_code_2']);
            $address->setPostalCode($customerAddress['postcode']);
            $validateRequest = new AvaTax\ValidateRequest();
            $validateRequest->setAddress($address);
            $validateResult = $addressSvc->Validate($validateRequest);

            if ($that->config->get('avatax_integration_logging') == 1) {
                $message = print_r($validateRequest, true);
                $warning = new AWarning('AVATAX address validation request: '.$message);
                $warning->toLog()->toDebug();
                $message = print_r($validateResult, true);
                $warning = new AWarning('AVATAX address validation reply: '.$message);
                $warning->toLog()->toDebug();
            }

            if ($validateResult->getResultCode() != AvaTax\SeverityLevel::$Success) {
                $allMessages = "";
                foreach ($validateResult->getMessages() as $message) {
                    $allMessages .= $message->getSummary()."\n";
                }
                $ret['message'] = strtoupper($allMessages);
                $ret['error'] = true;
            } else {
                $ret['error'] = false;
            }
        } else {
            $ret['message'] = "";
            $ret['error'] = false;
        }
        return $ret;
    }

    public function onControllerPagesAccountEdit_InitData()
    {
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
                    sprintf(
                        $that->language->get('avatax_integration_review_number_message'),
                        $that->customer->getId()
                    ),
                    false
                );
            }
        }
    }

    public function onControllerPagesAccountEdit_UpdateData()
    {
        $data = [];
        $that = $this->baseObject;
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
        /**
         * @var ControllerPagesAccountCreate $that
         */
        $that = $this->baseObject;
        /** @var ModelExtensionAvataxIntegration $mdl */
        $mdl = $that->loadModel('extension/avatax_integration');
        $that->loadLanguage('avatax_integration/avatax_integration');

        if ($that->request->is_POST() && $that->data['customer_id']
            && $that->request->post['exemption_number']
            && !$that->errors
        ) {
            $customer_id = $that->data['customer_id'];
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
                    sprintf($that->language->get('avatax_integration_review_number_message'), $customer_id),
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
        $that->view->addHookVar('customer_attributes', $view->fetch('pages/account/tax_exempt_edit.tpl'));
    }

    public function onModelAccountAddress_ValidateData()
    {
        /** @var ModelAccountAddress $that */
        $that = $this->baseObject;
        if ($that->config->get('avatax_integration_status')
            && $that->config->get('avatax_integration_address_validation')
            && !$that->error
        ) {
            $address = func_get_arg(0)['address'];
            if ($that->customer->isLogged()) {
                $session = $this->registry->get('fast_checkout') == true || $this->registry->get('session')->data['fc']
                    ? $this->registry->get('session')->data['fc']
                    : $this->registry->get('session')->data;
                $address['address_id'] = $session['shipping_address_id']
                    ? : $session['payment_address_id'];
            } else {
                $address['address_id'] = 'guest';
            }
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
                    $address['zone_code'] = $zoneDetails['code'];
                }
            }

            $result = $this->validate_address($address);
            if ($result['error']) {
                $that->error['warning'] = $result['message'];
            }
        }
    }
}