AvaTax-REST-PHP
=====================
[Other Samples](http://developer.avalara.com/api-docs/api-sample-code)

This is a PHP sample demonstrating the [AvaTax REST API](http://developer.avalara.com/api-docs/rest) methods:
 [tax/get POST](http://developer.avalara.com/api-docs/rest/tax/post/), [tax/get GET](http://developer.avalara.com/api-docs/rest/tax/get), [tax/cancel POST](http://developer.avalara.com/api-docs/rest/tax/cancel), and [address/validate GET](http://developer.avalara.com/api-docs/rest/address-validation).
 
 For more information on the use of these methods and the AvaTax product, please visit our [developer site](http://developer.avalara.com/) or [homepage](http://www.avalara.com/)

Dependencies:
-----------
- PHP 5.3 or later (not tested on versions older than PHP 5.3)

Requirements:
----------
- Authentication requires an valid **Account Number** and **License Key**, which should be entered in the test file (e.g. GetTaxTest.php) you would like to run.
- If you do not have an AvaTax account, a free trial account can be acquired through our [developer site](http://developer.avalara.com/api-get-started)
- Some Windows users have had trouble with cURL automatically using our SSL certificate. The sample code offers 
two workarounds (both in /AvaTaxClasses/classes/TaxServiceRest.php.class). If this is something that presents a challenge
for your system, uncomment ONE of the following two options in that file:

		//Some Windows users have had trouble with our SSL Certificates. Uncomment the following line to NOT use SSL.
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 		
		
		//Other Windows users may prefer to download the certificate from our site (detail here: ) and manually set the cert path.
		//    To set the path manually, uncomment the following two lines. If you choose to manually set the path, make sure you have commented out the line above 
		//    that tells curl to NOT use SSL.
		//$ca = "C:/curl/curl-ca-bundle.crt";
		//curl_setopt($curl, CURLOPT_CAINFO, $ca);

- If you would like to use these core classes as part of a project and manage your depencies through [composer](https://getcomposer.org/), the Avatax classes can be added to your existing project by adding
```
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/avadev/AvaTax-Calc-REST-PHP"
        }
    ],
    "require": {
        "avalara/avatax": "*"
    }
```
to your composer.json file and run `php composer.phar update` from your command line.

Contents:
----------
 
<table>
<th colspan="2" align=left>Sample Files</th>
<tr><td>CancelTaxTest.php</td><td>Demonstrates the <a href="http://developer.avalara.com/api-docs/rest/tax/cancel">CancelTax</a> method used to <a href="http://developer.avalara.com/api-docs/api-reference/canceltax">void a document</a>.</td></tr>
<tr><td>EstimateTaxTest.php</td><td>Demonstrates the <a href="http://developer.avalara.com/api-docs/rest/tax/get">EstimateTax</a> method used for product- and line- indifferent tax estimates.</td></tr>
<tr><td>GetTaxTest.php</td><td>Demonstrates the <a href="http://developer.avalara.com/api-docs/rest/tax/post">GetTax</a> method used for product- and line- specific <a href="http://developer.avalara.com/api-docs/api-reference/gettax">calculation</a>.</td></tr>
<tr><td>PingTest.php</td><td>Uses a hardcoded EstimateTax call to test connectivity and credential information.</td></tr>
<tr><td>ValidateAddressTest.php</td><td>Demonstrates the <a href="http://developer.avalara.com/api-docs/rest/address-validation">ValidateAddress</a> method to <a href="http://developer.avalara.com/api-docs/api-reference/address-validation">normalize an address</a>.</td></tr>
<th colspan="2" align=left>Other Files</th>
<tr><td>AvaTaxClasses/</td><td>Contains the core classes that make the service calls.</td></tr>
<tr><td>.gitattributes</td><td>-</td></tr>
<tr><td>.gitignore</td><td>-</td></tr>
<tr><td>LICENSE.md</td><td>-</td></tr>
<tr><td>README.md</td><td>-</td></tr>
</table>
