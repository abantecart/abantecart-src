<?php

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\contract\v1\ANetApiResponseType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\contract\v1\CustomerProfilePaymentType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\OpaqueDataType;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1\PaymentProfileType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\SettingType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\TransactionResponseType;
use net\authorize\api\controller as AnetController;
use net\authorize\api\controller\CreateTransactionController;

/**
 * Class ModelExtensionAuthorizeNet
 *
 * @property ModelCheckoutOrder $model_checkout_order
 */
class ModelExtensionDefaultAuthorizeNet extends Model
{
    public $error = array();

    /**
     * @return MerchantAuthenticationType
     */
    protected function getAccess()
    {
        $merchantAuthentication = new MerchantAuthenticationType();
        $merchantAuthentication->setName($this->config->get('default_authorizenet_api_login_id'));
        $merchantAuthentication->setTransactionKey($this->config->get('default_authorizenet_api_transaction_key'));
        return $merchantAuthentication;
    }

    /**
     * @param $address
     *
     * @return array
     * @throws AException
     */
    public function getMethod($address)
    {
        //create new instance of language for case when model called from admin-side
        $language = new ALanguage($this->registry, $this->language->getLanguageCode(), 0);
        $language->load($language->language_details['directory']);
        $language->load('default_authorizenet/default_authorizenet');
        if ($this->config->get('default_authorizenet_status')) {
            $query = $this->db->query(
                "SELECT * 
                FROM `".$this->db->table("zones_to_locations")."` 
                WHERE location_id = '".(int)$this->config->get('default_authorizenet_location_id')."' 
                    AND country_id = '".(int)$address['country_id']."' 
                    AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')");

            if ( ! $this->config->get('default_authorizenet_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $payment_data = array();
        if ($status) {
            $payment_data = array(
                'id'         => 'default_authorizenet',
                'title'      => $language->get('text_title'),
                'sort_order' => $this->config->get('default_authorizenet_sort_order'),
            );
        }

        return $payment_data;
    }

    /**
     * @param $pd
     *
     * @return array
     * @throws AException
     */
    public function processPayment($pd)
    {
        $output = array();
        $this->load->model('checkout/order');
        $this->load->language('default_authorizenet/default_authorizenet');
        $order_info = $this->model_checkout_order->getOrder($pd['order_id']);

        try {

            //grab price from order total
            $amount = round($order_info['total'], 2);
            //build charge data array
            $charge_data = array(
                'amount'               => $amount,
                'currency'             => $pd['currency'],
                'description'          => $this->config->get('store_name').' Order #'.$pd['order_id'],
                'statement_descriptor' => 'Order #'.$pd['order_id'],
                'receipt_email'        => $order_info['email'],
                'capture'              => ($this->config->get('default_authorizenet_settlement') == 'auth'
                                            ? false
                                            : true),
            );

            //build cc details
            $cc_details = array(
                'first_name'      => $pd['cc_owner_firstname'],
                'last_name'       => $pd['cc_owner_lastname'],
                'address_line1'   => trim($order_info['payment_address_1']),
                'address_line2'   => trim($order_info['payment_address_2']),
                'address_city'    => $order_info['payment_city'],
                'address_zip'     => $order_info['payment_postcode'],
                'address_state'   => $order_info['payment_zone'],
                'address_country' => $order_info['payment_iso_code_2'],
            );

            if ($order_info['shipping_method']) {
                $charge_data['shipping'] = array(
                    'name'    => $order_info['firstname'].' '.$order_info['lastname'],
                    'phone'   => $order_info['telephone'],
                    'address' => array(
                        'line1'       => $order_info['shipping_address_1'],
                        'line2'       => $order_info['shipping_address_2'],
                        'city'        => $order_info['shipping_city'],
                        'postal_code' => $order_info['shipping_postcode'],
                        'state'       => $order_info['shipping_zone'],
                        'country'     => $order_info['shipping_iso_code_2'],
                    ),
                );
            }

            $charge_data['metadata'] = array();
            $charge_data['metadata']['order_id'] = $pd['order_id'];
            if ($this->customer->getId() > 0) {
                $charge_data['metadata']['customer_id'] = (int)$this->customer->getId();
            }
            $amount = $pd['amount'];

            ADebug::variable('Processing authorizenet payment request: ', $charge_data);

            $payment_details = $pd + $cc_details + $charge_data + $order_info;
            $tr_details = $this->processPaymentByToken($payment_details, $amount);


        } catch (AException $e) {
            $output = array();
            // Something else happened, completely unrelated to AuthorizeNet
            $msg = new AMessage();
            $msg->saveError(
                'Unexpected error in authorizenet payment!',
                'Authorize.Net processing failed.<br>'.$e->getMessage()."(".$e->getCode().")"
            );

            $output['error'] = $e->getMessage();

            return $output;
        }

        //we still have no result. something unexpected happened
        if (empty($tr_details)) {
            $output['error'] = $this->language->get('error_system').'(**)';

            return $output;
        }

        $responseCode = $tr_details['response_code'];
        //we allow only 1 = Approved & 4 = Held for Review
        if ($responseCode == 1 || $responseCode == 4) {
            //get credit cart type from directResponse
            $transaction_id = $tr_details['refTransId'];
            $order_info['transaction_id'] = $transaction_id;
            $card_type = $tr_details['accountType'];

            $message = 'Order id: '.(string)$pd['order_id']."\n";
            $message .= 'Order total: '.(string)$amount."\n";
            $message .= 'Transaction ID: '.(string)$transaction_id."\n";
            $message .= 'Transaction Timestamp: '.(string)date('m/d/Y H:i:s');

            //update authorizenet_transaction_id and CC type in the order table
            $this->db->query(
                "UPDATE ".$this->db->table('orders')."
                    SET payment_method_data = '".$this->db->escape(
                    serialize(array('authorizenet_transaction_id' => $transaction_id, 'cc_type' => $card_type))
                )."'
                    WHERE order_id = '".(int)$pd['order_id']."'"
            );

            //finalize order only if payment is a success
            $this->model_checkout_order->confirm($pd['order_id'],
                $this->config->get('default_authorizenet_status_success_settled'));
            if ($order_info['shipping_method'] == 'Pickup From Store') {
                $comment = '<div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">'.
                    'You will be contacted by an account representative '
                    .'when your order is available for pickup.</div>'."\n";
                $this->model_checkout_order->update($pd['order_id'],
                    $this->config->get('default_authorizenet_status_success_settled'), $comment);
            }
            $order_status = $this->config->get('default_authorizenet_status_success_settled');
            //diff order status for pending review
            if ($responseCode == 4) {
                $order_status = 1;
            }
            $this->model_checkout_order->update($pd['order_id'], $order_status, $message, false);
            $output['paid'] = true;
        } else {
            // Some other error, assume payment declined
            $message = 'Timestamp: '.(string)date('m/d/Y H:i:s')."\n";
            $message .= 'Authorize.net status: '.(string)$tr_details['resultCode']."\n";
            $message .= 'Authorize.net message: '.(string)$tr_details['description']."\n";
            $this->model_checkout_order->update(
                $pd['order_id'],
                $this->config->get('default_authorizenet_status_decline'),
                $message,
                false
            );

            if ($tr_details['error']) {
                $output['error'] = "Payment has failed! ".$tr_details['error'];
                $output['code'] = $tr_details['code'];
            }
        }

        return $output;
    }

    protected function processPaymentByToken($payment_data, $amount)
    {

        $merchantAuthentication = $this->getAccess();
        // Set the transaction's refId
        $refId = 'refpbt'.$payment_data['order_id'];
        // Create the payment object for a payment nonce
        $opaqueData = new OpaqueDataType();
        $opaqueData->setDataDescriptor($payment_data['dataDescriptor']);
        $opaqueData->setDataValue($payment_data['dataValue']);

        // Add the payment data to a paymentType object
        $paymentOne = new PaymentType();
        $paymentOne->setOpaqueData($opaqueData);
        // Create order information
        $order = new OrderType();
        $order->setInvoiceNumber($payment_data['order_id']);
        $order->setDescription($payment_data['description']);
        // Set the customer's Bill To address
        $customerAddress = new CustomerAddressType();
        $customerAddress->setFirstName($payment_data['first_name']);
        $customerAddress->setLastName($payment_data['last_name']);
        $customerAddress->setAddress($payment_data['address_line1'].' '.$payment_data['address_line2']);
        $customerAddress->setCity($payment_data['payment_city']);
        $customerAddress->setState($payment_data['payment_zone']);
        $customerAddress->setZip($payment_data['payment_postcode']);
        $customerAddress->setCountry($payment_data['payment_iso_code_2']);
        $customerAddress->setPhoneNumber($payment_data['telephone']);

        // Set the customer's Ship To address
        $ship_address_obj = new AnetAPI\CustomerAddressType();
        $ship_address_obj->setFirstName($payment_data['first_name']);
        $ship_address_obj->setLastName($payment_data['last_name']);
        $ship_address_obj->setAddress($payment_data['shipping_address_1'].' '.$payment_data['shipping_address_2']);
        $ship_address_obj->setCity($payment_data['shipping_city']);
        $ship_address_obj->setState($payment_data['shipping_zone']);
        $ship_address_obj->setZip($payment_data['shipping_postcode']);
        $ship_address_obj->setCountry($payment_data['shipping_iso_code_2']);
        // Set the customer's identifying information
        $customerData = new CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($this->customer->getId());
        $customerData->setEmail($payment_data['email']);
        // Add values for transaction settings
        $duplicateWindowSetting = new SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");
        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new TransactionRequestType();
        $t_type = $this->config->get('default_authorizenet_settlement') == 'authcapture'
                    ? "authCaptureTransaction"
                    : 'authOnlyTransaction';
        $transactionRequestType->setTransactionType($t_type);
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setShipTo($ship_address_obj);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $solutionID = $this->config->get('default_authorizenet_test_mode') ? 'AAA100302' : 'AAA179397';
        $solution = new AnetAPI\SolutionType();
        $solution->setId($solutionID);
        $transactionRequestType->setSolution($solution);

        /*$transactionRequestType->addToUserFields($merchantDefinedField1);
        $transactionRequestType->addToUserFields($merchantDefinedField2);*/
        // Assemble the complete transaction request
        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $endpoint_url = $this->config->get('default_authorizenet_test_mode')
            ? \net\authorize\api\constants\ANetEnvironment::SANDBOX
            : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
        /**
         * @var AnetApiResponseType $response
         */
        $response = $controller->executeWithApiResponse($endpoint_url);
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            $tresponse = $response->getTransactionResponse();
            if ( ! $tresponse && $this->config->get('default_authorizenet_test_mode')) {
                $this->log->write(var_export($response, true));
            }
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $messages = $tresponse->getMessages();

                    return array(
                        'response_object' => $tresponse,
                        'refId'           => $refId,
                        'refTransId'      => $tresponse->getTransId(),
                        'auth_code'       => $tresponse->getAuthCode(),
                        'accountNumber'   => $tresponse->getAccountNumber(),
                        'accountType'     => $tresponse->getAccountType(),
                        'response_code'   => $tresponse->getResponseCode(),
                        'message_code'    => $messages[0]->getCode(),
                        'description'     => $messages[0]->getDescription(),
                    );
                } else {
                    return $this->processApiResponse($tresponse, false);
                }
                // Or, print errors if the API request wasn't successful
            } else {
                return $this->processApiResponse($tresponse, false);
            }
        }

        return array('error' => 'Error: Method '.__METHOD__.' result. No response returned.');
    }

    /**
     * @param int   $customer_authorizenet_id
     * @param int   $payment_profile_id
     * @param float $amount
     * @param array $payment_data
     *
     * @return array|ANetApiResponseType
     */
    protected function chargeCustomerProfile(
        $customer_authorizenet_id,
        $payment_profile_id,
        $amount,
        $payment_data
    ){
        $merchantAuthentication = $this->getAccess();

        // Set the transaction's refId
        $refId = 'refcpp'.$payment_data['order_id'];
        $profile_2_charge = new CustomerProfilePaymentType();
        $profile_2_charge->setCustomerProfileId($customer_authorizenet_id);
        $payment_profile = new PaymentProfileType();
        $payment_profile->setPaymentProfileId($payment_profile_id);
        $profile_2_charge->setPaymentProfile($payment_profile);
        $t_request_type = new TransactionRequestType();
        $t_type = $this->config->get('default_authorizenet_settlement') == 'authcapture'
                    ? "authCaptureTransaction"
                    : 'authOnlyTransaction';
        $t_request_type->setTransactionType($t_type);
        $t_request_type->setAmount($amount);
        $t_request_type->setProfile($profile_2_charge);

        // Set the customer's Ship To address
        $ship_address_obj = new CustomerAddressType();
        $ship_address_obj->setFirstName($payment_data['first_name']);
        $ship_address_obj->setLastName($payment_data['last_name']);
        $ship_address_obj->setAddress($payment_data['shipping_address_1'].' '.$payment_data['shipping_address_2']);
        $ship_address_obj->setCity($payment_data['shipping_city']);
        $ship_address_obj->setState($payment_data['shipping_zone']);
        $ship_address_obj->setZip($payment_data['shipping_postcode']);
        $ship_address_obj->setCountry($payment_data['shipping_iso_code_2']);

        $t_request_type->setShipTo($ship_address_obj);
        $t_request_type->setPoNumber($payment_data['order_id']);

        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($t_request_type);
        $controller = new CreateTransactionController($request);
        $endpoint_url = $this->config->get('default_authorizenet_test_mode')
            ? \net\authorize\api\constants\ANetEnvironment::SANDBOX
            : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
        $response = $controller->executeWithApiResponse($endpoint_url);
        if ($response != null) {
            if ($response->getMessages()->getResultCode() == 'Ok') {
                /**
                 * @var \net\authorize\api\contract\v1\TransactionResponseType $tresponse
                 */
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $messages = $tresponse->getMessages();

                    return array(
                        'response_object' => $tresponse,
                        'refId'           => $refId,
                        'refTransId'      => $tresponse->getTransId(),
                        'auth_code'       => $tresponse->getAuthCode(),
                        'accountNumber'   => $tresponse->getAccountNumber(),
                        'accountType'     => $tresponse->getAccountType(),
                        'response_code'   => $tresponse->getResponseCode(),
                        'message_code'    => $messages[0]->getCode(),
                        'description'     => $messages[0]->getDescription(),
                    );
                } else {
                    return $this->processApiResponse($tresponse, false);
                }
            } else {
                $tresponse = $response->getTransactionResponse();

                return $this->processApiResponse($tresponse, false);
            }
        }

        return array('error' => 'Error: Method '.__METHOD__.' result. No response returned.');
    }

    /**
     * @param TransactionResponseType | AnetApiResponseType $api_response
     * @param string $mode
     *
     * @return array
     * @throws AException
     */
    private function processApiResponse($api_response, $mode = 'exception')
    {
        $output = array();

            if (method_exists($api_response, 'getErrors') && $api_response->getErrors() != null) {
                $errors = $api_response->getErrors();
                $output['error'] = $errors[0]->getErrorText();
                $output['code'] = $errors[0]->getErrorCode();
            } else {
                $messages = $api_response->getMessages();
                if ( ! is_array($messages)) {
                    $messages = $messages->getMessage();
                }
                if ($messages) {
                    $output['error'] = $messages[0]->getText();
                    $output['code'] = $messages[0]->getCode();
                }
            }


        if ($output) {
            $err = new AError('Authorize.net:'.var_export($output, true));
            $err->toDebug()->toLog();
        }

        if ($output && $mode == 'exception') {
            throw new AException (AC_ERR_LOAD, 'Error: '.$output['error']);
        }

        return $output;
    }

}