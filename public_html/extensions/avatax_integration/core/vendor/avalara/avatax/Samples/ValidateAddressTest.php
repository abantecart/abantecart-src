<?php
require 'vendor/autoload.php';
include 'configuration.php';

// Header Level Elements
// Required Header Level Elements
$serviceURL = $configuration['serviceURL'];
$accountNumber = $configuration['accountNumber'];
$licenseKey = $configuration['licenseKey'];

$addressSvc = new AvaTax\AddressServiceRest($serviceURL, $accountNumber, $licenseKey);
$address = new AvaTax\Address();

// Required Request Parameters
$address->setLine1("118 N Clark St");
$address->setCity("Chicago");
$address->setRegion("IL");

// Optional Request Parameters
$address->setLine2("Suite 100");
$address->setLine3("ATTN Accounts Payable");
$address->setCountry("US");
$address->setPostalCode("60602");

$validateRequest = new AvaTax\ValidateRequest();
$validateRequest->setAddress($address);
$validateResult = $addressSvc->Validate($validateRequest);

//Print Results
echo 'ValidateAddressTest Result: '.$validateResult->getResultCode()."\n";
if ($validateResult->getResultCode() != AvaTax\SeverityLevel::$Success)    // call failed
{
    foreach ($validateResult->getMessages() as $message) {
        echo $message->getSeverity().": ".$message->getSummary()."\n";
    }
} else {
    echo $validateResult->getValidAddress()->getLine1()
        ." "
        .$validateResult->getValidAddress()->getCity()
        .", "
        .$validateResult->getValidAddress()->getRegion()
        ." "
        .$validateResult->getValidAddress()->getPostalCode()."\n";

}
?>