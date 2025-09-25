# AvaTax-REST-V2-PHP-SDK

This GitHub repository is the PHP SDK for Avalara's world-class tax service, AvaTax.  It uses the AvaTax REST v2 API, which is a fully REST implementation and provides a single client for all AvaTax functionality.  For more information about AvaTax REST v2, please visit [Avalara's Developer Network](http://developer.avalara.com/) or view the [online Swagger documentation](https://sandbox-rest.avatax.com/swagger/ui/index.html).

# Build Status

Packagist

[![Packagist](https://img.shields.io/packagist/v/avalara/avataxclient.svg?style=plastic)](https://packagist.org/packages/avalara/avataxclient)

Travis-CI

[![Travis](https://api.travis-ci.org/avadev/AvaTax-REST-V2-PHP-SDK.svg?branch=master&style=plastic)](https://travis-ci.org/avadev/AvaTax-REST-V2-PHP-SDK)

# Installing the PHP SDK

The AvaTax PHP SDK is available as a Composer package.

To use the AvaTax PHP SDK from Composer:
* Add a `composer.json` file to your project and link to AvaTax:

```json
{
    "require": {
        "avalara/avataxclient": "*"
    }
}
```

* Run `composer install` to download the latest version.

# Using the PHP SDK

The PHP SDK uses a fluent interface to define a connection to AvaTax and to make API calls to calculate tax on transactions.  Here's an example of connecting to the API.

```php
<?php

// Include the AvaTaxClient library
require __DIR__ . '/vendor/autoload.php';
use Avalara\AvaTaxClient;

// Create a new client
$client = new Avalara\AvaTaxClient('phpTestApp', '1.0', 'localhost', 'sandbox');
$client->withSecurity('myUsername', 'myPassword');

// If I am debugging, I can call 'Ping' to see if I am connected to the server
$p = $client->ping();
echo('<h2>Ping</h2>');
echo('<pre>' . json_encode($p, JSON_PRETTY_PRINT) . '</pre>');
if ($p->authenticated == true) {
    echo '<p>Authenticated!</p>';
}

// Create a simple transaction for $100 using the fluent transaction builder
$tb = new Avalara\TransactionBuilder($client, "DEFAULT", Avalara\DocumentType::C_SALESINVOICE, 'ABC');
$t = $tb->withAddress('SingleLocation', '123 Main Street', null, null, 'Irvine', 'CA', '92615', 'US')
    ->withLine(100.0, 1, null, "P0000000")
    ->create();
echo('<h2>Transaction #1</h2>');
echo('<pre>' . json_encode($t, JSON_PRETTY_PRINT) . '</pre>');

// Now, let's create a more complex transaction!
$tb = new Avalara\TransactionBuilder($client, "DEFAULT", Avalara\DocumentType::C_SALESINVOICE, 'ABC');
$t = $tb->withAddress('ShipFrom', '123 Main Street', null, null, 'Irvine', 'CA', '92615', 'US')
    ->withAddress('ShipTo', '100 Ravine Lane', null, null, 'Bainbridge Island', 'WA', '98110', 'US')
    ->withLine(100.0, 1, null, "P0000000")
    ->withLine(1234.56, 1, null, "P0000000")
    ->withExemptLine(50.0, null, "NT")
    ->withLine(2000.0, 1, null, "P0000000")
    ->withLineAddress(Avalara\TransactionAddressType::C_SHIPFROM, "123 Main Street", null, null, "Irvine", "CA", "92615", "US")
    ->withLineAddress(Avalara\TransactionAddressType::C_SHIPTO, "1500 Broadway", null, null, "New York", "NY", "10019", "US")
    ->withLine(50.0, 1, null, "FR010000")
    ->create();
echo('<h2>Transaction #2</h2>');
echo('<pre>' . json_encode($t, JSON_PRETTY_PRINT) . '</pre>');

?>
```
# How to enable logging in the PHP SDK
* SDK uses PSR-3, a common interface used for logging capabilities in PHP.
* Client would implement the binding on their end like Monolog, Analog etc to enable logging.
* By default there is no logging enabled.
* All the attributes which are part of log message are in **LogObject.php**
* To enable or disable logging of request and response object, there is a boolean variable **logRequestAndResponseBody** passed as constructor argument. Default is set to **FALSE**
* Output of logging is in **JSON** format.


Logging could be enabled on client side by adding logging library like Monolog. This could be done by adding dependency and version in composer.json
```
"require": {
        ....
        ....
        "monolog/monolog": "^3.2"
    },
``` 
By just adding the above configuration, the logging framework will recognise the binding. Now, we will have to provide logging object as contructor parameter to AvaTaxClient. This could be done as below:
```
// Include the packages/classes we would need to create the logger object

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\PsrLogMessageProcessor;
```

The following example shows how we can add configuration to display logs at console (stdout)
```
$stream_handler = new StreamHandler("php://stdout");

$stream_handler->setFormatter(new JsonFormatter());

// Follow PSR-3 specificaiton.
$psrProcessor = new PsrLogMessageProcessor();

$logger = new Logger('appLogger', [$stream_handler], [
    $psrProcessor,
  ]);

// Create a new client
$client = new Avalara\AvaTaxClient('phpTestApp', '1.0', 'localhost', 'sandbox',[], $logger, true);
```

This should add logging to SDK and the logs would be displayed on console. If we want to use other configurations where we want logs to be stored in files etc then the handler(StreamHandler in above case) would require changes accordingly.