<?php
namespace AvaTax;

/**
 * EstimateTaxResult.class.php
 */

class EstimateTaxResult extends BaseResult implements JsonSerializable
{
    /**
     * Returns composite rate and total tax for location, with an array of jurisdictional details.
     */
    private $Rate;
    private $Tax;
    private $TaxDetails = array();

    public function __construct($resultCode, $rate, $tax, $taxdetails, $messages)
    {
        $this->ResultCode = $resultCode;
        $this->TaxDetails = $taxdetails;
        $this->Rate = $rate;
        $this->Tax = $tax;
        $this->Messages = $messages;
    }

    //Helper function to decode result objects from Json responses to specific objects.
    public static function parseResult($jsonString)
    {
        $object = json_decode($jsonString);
        $taxdetails = array();
        $messages = array();
        $resultcode = null;
        $rate = null;
        $tax = null;

        if (property_exists($object, "Rate")) {
            $rate = $object->Rate;
        }
        if (property_exists($object, "Tax")) {
            $tax = $object->Tax;
        }
        if (property_exists($object, "ResultCode")) {
            $resultcode = $object->ResultCode;
        }
        if (property_exists($object, "TaxDetails")) {
            $taxdetails = TaxDetail::parseTaxDetails("{\"TaxDetails\": ".json_encode($object->TaxDetails)."}");
        }
        if (property_exists($object, "Messages")) {
            $messages = Message::parseMessages("{\"Messages\": ".json_encode($object->Messages)."}");
        }

        return new self($resultcode, $rate, $tax, $taxdetails, $messages);
    }

    public function jsonSerialize()
    {
        return array(
            'Rate'       => $this->getRate(),
            'Tax'        => $this->getTax(),
            'TaxDetails' => $this->getTaxDetails(),
            'ResultCode' => $this->getResultCode(),
            'Messages'   => $this->getMessages(),
        );
    }

    /**
     * Method returning array of matching {@link ValidAddress}'s.
     *
     * @return array
     */
    public function getRate()
    {
        return $this->Rate;
    }

    public function getTax()
    {
        return $this->Tax;
    }

    public function getTaxDetails()
    {
        return $this->TaxDetails;
    }



    /**
     * @var string
     */
    //private $TransactionId;
    /**
     * @var string must be one of the values defined in {@link SeverityLevel}.
     */
    private $ResultCode = 'Success';
    /**
     * @var array of Message.
     */
    private $Messages = array();

    /**
     * Accessor
     *
     * @return string
     */
    //public function getTransactionId() { return $this->TransactionId; }
    /**
     * Accessor
     *
     * @return string
     */
    public function getResultCode()
    {
        return $this->ResultCode;
    }

    /**
     * Accessor
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->Messages;
    }

    //@author:swetal

}

?>