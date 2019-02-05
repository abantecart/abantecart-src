<?php

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class ModelExtensionDefaultAuthorizeNet extends Model
{
    public $error = array();

    /**
     * @return AnetAPI\MerchantAuthenticationType
     */
    protected function getAccess()
    {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($this->config->get('default_authorizenet_api_login_id'));
        $merchantAuthentication->setTransactionKey($this->config->get('default_authorizenet_api_transaction_key'));

        return $merchantAuthentication;
    }

    /**
     * @param int $order_id
     *
     * @return bool
     */
    public function getAuthorizeNetOrder($order_id)
    {
        $qry = $this->db->query("SELECT ao.*
                                FROM ".$this->db->table("authorizenet_orders")." ao
                                WHERE ao.order_id = '".(int)$order_id."' 
                                LIMIT 1");
        if ($qry->num_rows) {
            $order = $qry->row;
            return $order;
        } else {
            return false;
        }
    }

    /**
     * @param $ch_id
     *
     * @return array|null
     */
    public function getAuthorizeNetTransaction($ch_id)
    {
        if (!has_value($ch_id)) {
            return array();
        }

        try {

            $merchantAuthentication = $this->getAccess();

            $request = new AnetAPI\GetTransactionDetailsRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setTransId($ch_id);

            $controller = new AnetController\GetTransactionDetailsController($request);

            $endpoint_url = $this->config->get('default_authorizenet_test_mode')
                ? \net\authorize\api\constants\ANetEnvironment::SANDBOX
                : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
            $response = $controller->executeWithApiResponse($endpoint_url);

            if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
                $transaction = $response->getTransaction();

                $output['transId'] = $transaction->getTransId();
                $output['cardNumber'] = $transaction->getPayment()->getCreditCard()->getCardNumber();
                $output['authAmount'] = $transaction->getAuthAmount();
                $output['settleAmount'] = $transaction->getSettleAmount();
                $output['transactionStatus'] = $transaction->getTransactionStatus();
                return $output;
            } else {
                return $this->processApiResponse($response, false);
            }
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);

            return null;
        }
    }

    /**
     * @param \net\authorize\api\contract\v1\TransactionResponseType $api_response
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
            $output['error'] = $errors[0]->getErrorText().' ('.$errors[0]->getErrorCode().')';
            $output['code'] = $errors[0]->getErrorCode();
        } else {
            $messages = $api_response->getMessages();
            if (!is_array($messages)) {
                $messages = $messages->getMessage();
            }
            if ($messages) {
                $output['error'] = $messages[0]->getText().' ('.$messages[0]->getCode().')';
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