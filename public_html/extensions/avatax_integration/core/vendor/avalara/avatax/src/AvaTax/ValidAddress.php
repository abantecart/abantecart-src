<?php
/**
 * ValidAddress.class.php
 */

/**
 * Contains address data; result returned from the {@link AddressServiceRest#validate} address validation service;
 * No behavior - basically a glorified struct.
 *
 * <b>Example:</b>
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
 *  $result = $port->validate($address);
 *  $address = $result->ValidAddress;
 *
 * </pre>
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Address
 */

namespace AvaTax;

class ValidAddress
{

    public $AddressCode;
    public $Line1;
    public $Line2;
    public $Line3;
    public $City;
    public $Region;
    public $PostalCode;
    public $Country = 'US';
    public $County;
    public $FipsCode;
    public $CarrierRoute;
    public $PostNet;
    public $AddressType;

    public function __construct($line1 = null, $line2 = null, $line3 = null, $city = null, $region = null, $postalCode = null, $country = 'US', $county = null, $fipsCode = null, $carrierRoute = null, $postNet = null, $addressType = null)
    {
        $this->Line1 = $line1;
        $this->Line2 = $line2;
        $this->Line3 = $line3;
        $this->City = $city;
        $this->Region = $region;
        $this->PostalCode = $postalCode;
        $this->Country = $country;
        $this->County = $county;
        $this->FipsCode = $fipsCode;
        $this->CarrierRoute = $carrierRoute;
        $this->PostNet = $postNet;
        $this->AddressType = $addressType;
    }

    //Helper function to decode result objects from Json responses to specific objects.
    public static function parseAddress($jsonString)
    {
        $object = json_decode($jsonString);
        return new self(
            $object->Line1,
            isset($object->Line2) ? $object->Line2 : null,
            isset($object->Line3) ? $object->Line3 : null,
            $object->City,
            $object->Region,
            $object->PostalCode,
            $object->Country,
            isset($object->County) ? $object->County : null,
            $object->FipsCode,
            isset($object->CarrierRoute) ? $object->CarrierRoute : null,
            $object->PostNet,
            $object->AddressType);
    }

    public function setLine1($value)
    {
        $this->Line1 = $value;
    }

    public function setLine2($value)
    {
        $this->Line2 = $value;
    }

    public function setLine3($value)
    {
        $this->Line3 = $value;
    }

    public function setCity($value)
    {
        $this->City = $value;
    }

    public function setRegion($value)
    {
        $this->Region = $value;
    }

    public function setPostalCode($value)
    {
        $this->PostalCode = $value;
    }

    public function setCountry($value)
    {
        $this->Country = $value;
    }

    public function setCounty($value)
    {
        $this->County = $value;
    }

    public function setFipsCode($value)
    {
        $this->FipsCode = $value;
    }

    public function setPostNet($value)
    {
        $this->PostNet = $value;
    }

    public function setCarrierRoute($value)
    {
        $this->CarrierRoute = $value;
    }

    public function setAddressType($value)
    {
        $this->AddressType = $value;
    }

    public function getLine1()
    {
        return $this->Line1;
    }

    public function getLine2()
    {
        return $this->Line2;
    }

    public function getLine3()
    {
        return $this->Line3;
    }

    public function getCity()
    {
        return $this->City;
    }

    public function getRegion()
    {
        return $this->Region;
    }

    public function getPostalCode()
    {
        return $this->PostalCode;
    }

    public function getCountry()
    {
        return $this->Country;
    }

    public function getCounty()
    {
        return $this->County;
    }

    public function getFipsCode()
    {
        return $this->FipsCode;
    }

    public function getPostNet()
    {
        return $this->PostNet;
    }

    public function getCarrierRoute()
    {
        return $this->CarrierRoute;
    }

    public function getAddressType()
    {
        return $this->AddressType;
    }

    /**
     * Compares Addresses
     *
     * @access public
     *
     * @param Address
     *
     * @return boolean
     */
    public function equals(&$other)  // fix me after replace
    {
        return $this === $other
        || (
            strcmp($this->Line1, $other->Line1) == 0
            && strcmp($this->Line2, $other->Line2) == 0
            && strcmp($this->Line3, $other->Line3) == 0
            && strcmp($this->City, $other->City) == 0
            && strcmp($this->Region, $other->Region) == 0
            && strcmp($this->PostalCode, $other->PostalCode) == 0
            && strcmp($this->Country, $other->Country) == 0
        );
    }
}

?>
