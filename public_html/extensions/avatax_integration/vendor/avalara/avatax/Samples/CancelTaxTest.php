<?php
require 'vendor/autoload.php';
include 'configuration.php';

// Header Level Elements
// Required Header Level Elements
$serviceURL = $configuration['serviceURL'];
$accountNumber = $configuration['accountNumber'];
$licenseKey = $configuration['licenseKey'];

$taxSvc = new AvaTax\TaxServiceRest($serviceURL, $accountNumber, $licenseKey);
$cancelTaxRequest = new AvaTax\CancelTaxRequest();

// Required Request Parameters
$cancelTaxRequest->setCompanyCode("APITrialCompany");
$cancelTaxRequest->setDocType(AvaTax\DocumentType::$SalesInvoice);
$cancelTaxRequest->setDocCode("INV001");
$cancelTaxRequest->setCancelCode(AvaTax\CancelCode::$DocVoided);

$cancelTaxResult = $taxSvc->cancelTax($cancelTaxRequest);

//Print Results
echo 'CancelTaxTest Result: '.$cancelTaxResult->getResultCode()."\n";
if ($cancelTaxResult->getResultCode() != AvaTax\SeverityLevel::$Success)    // call failed
{
    foreach ($cancelTaxResult->getMessages() as $message) {
        echo $message->getSeverity().": ".$message->getSummary()."\n";
    }
}
?>