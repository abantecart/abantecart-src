<?php
/**
 * Address.class.php
 */

/**
 * Contains address data; Can be passed to {@link AddressServiceRest#validate};
 * Also part of the {@link GetTaxRequest}
 * result returned from the {@link TaxServiceSoap#getTax} tax calculation service;
 * No behavior - basically a glorified struct.
 *
 * <b>Example:</b>
 * <pre>
 *  $port = new AddressServiceRest($url, $account, $license);
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

class Address
{

    public $AddressCode;
    public $Line1;
    public $Line2;
    public $Line3;
    public $City;
    public $Region;
    public $PostalCode;
    public $Country = 'US';
    public $TaxRegionId;
    public $Latitude;
    public $Longitude;

    public function __construct($addressCode = null, $line1 = null, $line2 = null, $line3 = null, $city = null, $region = null, $postalCode = null, $country = 'US', $taxRegionId = null, $latitude = null, $longitude = null)
    {
        $this->AddressCode = $addressCode;
        $this->Line1 = $line1;
        $this->Line2 = $line2;
        $this->Line3 = $line3;
        $this->City = $city;
        $this->Region = $region;
        $this->PostalCode = $postalCode;
        $this->Country = $country;
        $this->TaxRegionId = $taxRegionId;
        $this->Latitude = $latitude;
        $this->Longitude = $longitude;
    }

    public static function parseAddress($jsonString)
    {
        $object = json_decode($jsonString);
        $AddressCode = null;
        $Line1 = null;
        $Line2 = null;
        $Line3 = null;
        $City = null;
        $Region = null;
        $PostalCode = null;
        $Country = null;
        $TaxRegionId = null;
        $Latitude = null;
        $Longitude = null;

        if (property_exists($object, "AddressCode")) {
            $AddressCode = $object->AddressCode;
        }
        if (property_exists($object, "Line1")) {
            $Line1 = $object->Line1;
        }
        if (property_exists($object, "Line2")) {
            $Line2 = $object->Line2;
        }
        if (property_exists($object, "Line3")) {
            $Line3 = $object->Line3;
        }
        if (property_exists($object, "City")) {
            $City = $object->City;
        }
        if (property_exists($object, "Region")) {
            $Region = $object->Region;
        }
        if (property_exists($object, "PostalCode")) {
            $PostalCode = $object->PostalCode;
        }
        if (property_exists($object, "Country")) {
            $Country = $object->Country;
        }
        if (property_exists($object, "TaxRegionId")) {
            $TaxRegionId = $object->TaxRegionId;
        }
        if (property_exists($object, "Latitude")) {
            $Latitude = $object->Latitude;
        }
        if (property_exists($object, "Longitude")) {
            $Longitude = $object->Longitude;
        }

        return new self(
            $AddressCode,
            $Line1,
            $Line2,
            $Line3,
            $City,
            $Region,
            $PostalCode,
            $Country,
            $TaxRegionId,
            $Latitude,
            $Longitude);

    }

    public function setLatitude($value)
    {
        $this->Latitude = $value;
    }

    public function setLongitude($value)
    {
        $this->Longitude = $value;
    }

    public function setAddressCode($value)
    {
        $this->AddressCode = $value;
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

    public function setTaxRegionId($value)
    {
        $this->TaxRegionId = $value;
    }

    public function getLongitude()
    {
        return $this->Longitude;
    }

    public function getLatitude()
    {
        return $this->Latitude;
    }

    public function getAddressCode()
    {
        return $this->AddressCode;
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
        return $this->AddressCode;
    }

    public function getTaxRegionId()
    {
        return $this->TaxRegionId;
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