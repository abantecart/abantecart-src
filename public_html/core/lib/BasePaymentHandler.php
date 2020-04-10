<?php

/**
 *
 * Class to handle authorizenet payment transaction
 *
 * @property AConfig $config
 * @property ALoader $load
 * @property ALanguage $language
 * @property ACart $cart
 * @property ACurrency $currency
 * @property ModelExtensionAuthorizeNet $model_extension_authorizenet
 */
class BasePaymentHandler implements PaymentHandlerInterface
{
    /**
     * @var Registry
     */
    public $registry;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var bool
     */
    protected $recurring_billing;

    /**
     * BasePaymentHandler constructor.
     *
     * @param Registry $registry
     */
    public function __construct( Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    public function recurring_billing()
    {
        return $this->recurring_billing;
    }

    /**
     * @return string
     */
    public function id():string
    {
        return (string)$this->id;
    }

    public function is_available($payment_address):bool
    {
        $this->load->model('extension/'.$this->id());
        $details = $this->{'model_extension_'.$this->id()}->getMethod($payment_address);
        if ($details) {
            return true;
        } else {
            return false;
        }
    }

    public function details():array
    {
    }

    public function validatePaymentDetails(array $data = array()):array
    {
    }

    public function processPayment(int $order_id, array $data = array()):array
    {
    }

    public function getErrors()
    {
        // TODO: Implement getErrors() method.
    }

    public function callback(array $data = [])
    {
        // TODO: Implement callback() method.
    }
}