<?php
/**
 * ValidateResult.class.php
 */

/**
 * Contains an array of {@link ValidAddress} objects returned by {@link AddressServiceSoap#validate}
 *
 * <pre>
 *  $port = new AddressServiceSoap();
 *
 *  $address = new Address();
 *  $address->setLine1("900 Winslow Way");
 *  $address->setLine2("Suite 130");
 *  $address->setCity("Bainbridge Is");
 *  $address->setRegion("WA");
 *  $address->setPostalCode("98110-2450");
 *
 *  $result = $port->validate($address,TextCase::$Upper);
 *  $addresses = $result->ValidAddresses;
 *  print("Number of addresses returned is ". sizeoof($addresses));
 *
 * </pre>
 *
 * @see       ValidAddress
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Address
 */

namespace AvaTax;

class ValidateResult extends BaseResult implements JsonSerializable
{
    /**
     * Array of matching {@link ValidAddress}'s.
     *
     * @var array
     */
    private $ValidAddress;
    private $ResultCode = 'Success';
    private $Messages = array();

    public function __construct($resultCode, $validaddress, $messages)
    {
        $this->ResultCode = $resultCode;
        $this->ValidAddress = $validaddress;
        $this->Messages = $messages;
    }

    //Helper function to decode result objects from Json responses to specific objects.
    public static function parseResult($jsonString)
    {
        $object = json_decode($jsonString);
        $validaddress = new ValidAddress();
        $messages = array();
        $resultcode = null;

        if (property_exists($object, "ResultCode")) {
            $resultcode = $object->ResultCode;
        }
        if (property_exists($object, "Address")) {
            $validaddress = ValidAddress::parseAddress(json_encode($object->Address));
        }
        if (property_exists($object, "Messages")) {
            $messages = Message::parseMessages("{\"Messages\": ".json_encode($object->Messages)."}");
        }

        return new self($resultcode, $validaddress, $messages);
    }

    public function jsonSerialize()
    {
        return array(
            'ValidAddress' => $this->getValidAddress(),
            'ResultCode'   => $this->getResultCode(),
            'Messages'     => $this->getMessages(),
        );
    }

    public function getValidAddress()
    {
        return $this->ValidAddress;
    }

    public function getResultCode()
    {
        return $this->ResultCode;
    }

    public function getMessages()
    {
        return $this->Messages;
    }

}

?>