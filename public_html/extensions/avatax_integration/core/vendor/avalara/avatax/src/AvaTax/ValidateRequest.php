<?php
/**
 * ValidateRequest.class.php
 */

/**
 * Data wrapper used internally to pass arguments within {@link AddressServiceRest#validate}. End users should not need to use this class.
 *
 * <pre>
 * <b>Example:</b>
 * $svc = new AddressServiceRest($url, $account, $license);
 *
 * $address = new Address();
 * $address->setLine1("900 Winslow Way");
 * $address->setCity("Bainbridge Island");
 * $address->setRegion("WA");
 * $address->setPostalCode("98110");
 *
 * ValidateRequest validateRequest = new ValidateRequest();
 * validateRequest.setAddress(address);
 *
 * ValidateResult result = svc.validate(validateRequest);
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Address
 */

namespace AvaTax;

class ValidateRequest
{
    private $Address; //The address to validate

    public function __construct($address = null)
    {
        $this->setAddress($address);
    }

    public function setAddress(&$value)
    {
        $this->Address = $value;
        return $this;
    }

    public function getAddress()
    {
        return $this->Address;
    }

}

?>
