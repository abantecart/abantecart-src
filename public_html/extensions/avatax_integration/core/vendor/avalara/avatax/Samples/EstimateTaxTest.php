<?php
require 'vendor/autoload.php';
include 'configuration.php';

// Header Level Elements
// Required Header Level Elements
$serviceURL = $configuration['serviceURL'];
$accountNumber = $configuration['accountNumber'];
$licenseKey = $configuration['licenseKey'];

$taxSvc = new AvaTax\TaxServiceRest($serviceURL, $accountNumber, $licenseKey);

// Required Request Parameters
$latitude = 47.627935;
$longitude = -122.51702;
$saleAmount = 10;

$estimateTaxRequest = new AvaTax\EstimateTaxRequest($latitude, $longitude, $saleAmount);
$geoTaxResult = $taxSvc->estimateTax($estimateTaxRequest);

//Print Results
echo 'EstimateTaxTest Result: '.$geoTaxResult->getResultCode()."\n";
if ($geoTaxResult->getResultCode() != AvaTax\SeverityLevel::$Success)    // call failed
{
    foreach ($geoTaxResult->getMessages() as $message) {
        echo $message->getSeverity().": ".$message->getSummary()."\n";
    }
} else {
    foreach ($geoTaxResult->getTaxDetails() as $taxDetail) {
        echo "    "."Jurisdiction: ".$taxDetail->getJurisName()." Tax: ".$taxDetail->getTax();
    }
}
?>