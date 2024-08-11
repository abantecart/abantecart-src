# UPSTrackAlert
# Product Info  The UPS Track Alert API provides best in-class package tracking visibility with near real time event updates for an improved customer experience and stream line logistic management. Updates are pushed to the user as soon as available with no constant polling required, thereby improving operational efficiency.<br /><a href=\"https://developer.ups.com/api/reference/trackalert/product-info\" target=\"_blank\" rel=\"noopener\">Product Info</a>  # Business Values  - **Enhanced Customer Experience**: Near Real-time tracking information increases transparency, leading to higher customer satisfaction and trust. - **Operational Efficiency**: Eliminates the necessity for continuous polling, thus saving computational resources and improving system responsiveness. - **Data-Driven Decision Making**: Access to timely data can help businesses optimize their supply chain and make informed logistics decisions. - **Optimizing Cash Flow Through Near Real-Time Delivery Tracking**: Improve cash flow by knowing the deliveries occurred in near real time. - **Mitigating Fraud and Theft through Near Real-Time Package Status Monitoring**: Reduce fraud and theft by knowing the status of the package.  # Error Codes  | Error Code | HTTP Status | Description                                                                                                                                                                                              | |------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| | VSS000     | 400         | Invalid request and The Subscription request has been rejected.                                                                                                                                          | | VSS002     | 400         | Missing transId.                                                                                                                                                                                         | | VSS003     | 400         | Please enter a valid Transaction ID, The Subscription request has been rejected.                                                                                                                         | | VSS004     | 400         | Missing transactionSrc.                                                                                                                                                                                  | | VSS006     | 400         | Please enter a valid Transaction SRC, The Subscription request has been rejected.                                                                                                                        | | VSS100     | 500         | We're sorry, the system is temporarily unavailable. Please try again later.                                                                                                                              | | VSS110     | 400         | Subscription request is empty or not present. The Subscription request has been rejected.                                                                                                                | | VSS200     | 400         | Tracking Number List is required. The Subscription request has been rejected.                                                                                                                            | | VSS210     | 400         | The Subscription request should have at least one valid tracking number. The Subscription request has been rejected.                                                                                     | | VSS215     | 400         | The 1Z tracking number that was submitted is not a valid CIE 1Z and has been rejected.                                                                                                                   | | VSS220     | 400         | You have submitted over 100 1Z numbers which is not allowed. The entire submission of 1Z numbers has been rejected. Please resubmit your request again using groups of no more than 100 1Z numbers.      | | VSS300     | 400         | Locale is required. The Subscription request has been rejected.                                                                                                                                          | | VSS310     | 400         | Please enter a valid locale. The Subscription request has been rejected.                                                                                                                                 | | VSS400     | 400         | Please enter a valid country code. The Subscription request has been rejected.                                                                                                                           | | VSS500     | 400         | Destination is required. The Subscription request has been rejected.                                                                                                                                     | | VSS600     | 400         | URL is empty or not present. The Subscription request has been rejected.                                                                                                                                 | | VSS610     | 400         | URL is too long. The Subscription request has been rejected.                                                                                                                                             | | VSS700     | 400         | Credential is empty or not present. The Subscription request has been rejected.                                                                                                                          | | VSS800     | 400         | CredentialType is empty or not present. The Subscription request has been rejected.                                                                                                                      | | VSS930     | 400         | Type is missing or invalid, The Subscription request has been rejected.                                                                                                                                  |   # FAQs - **How do I check if a subscription to a 1Z was successful?** >  A message will be sent via the API call with details about any successful and failed subscriptions.  - **I stopped receiving event messages after 2 weeks and my package hasn’t been delivered. Why?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How do I get events that occurred prior to subscription?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How many 1Z tracking numbers can a subscriber subscribe to in one request?** >  A message will be sent via the API call with details about any successful and failed subscriptions.  - **What does this Track Alert code mean?** >  look up all codes on the Track Alert Codes file \"X\" type: exception codes \"I\" type: in transit codes \"M\" and \"MV\" type\": manifest codes \"U\" type: delivery update codes \"D\" type: delivery codes These codes can appear in more than one type (for example, a type X code may also appear with a type D code in the Track Alert message These codes typically appear with a different type code when a package is delivered to a UPS access point, returned to sender, or rerouted. The codes are updated monthly Ask for an updated listed if you discover a new code that does not have a description on the list. The primary or more common codes rarely change.

This PHP package is automatically generated by the [Swagger Codegen](https://github.com/swagger-api/swagger-codegen) project:

- API version: 1.0.0
- Package version: 1.0.8
- Build package: io.swagger.codegen.v3.generators.php.PhpClientCodegen

## Requirements

PHP 5.5 and later

## Installation & Usage
### Composer

To install the bindings via [Composer](http://getcomposer.org/), add the following to `composer.json`:

```
{
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/abantecart/ups-strack-alert.git"
    }
  ],
  "require": {
    "abantecart/ups-strack-alert": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
    require_once('/path/to/UPSTrackAlert/vendor/autoload.php');
```

## Tests

To run the unit tests:

```
composer install
./vendor/bin/phpunit
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Configure OAuth2 access token for authorization: oauth2
$config = UPS\UPSTrackAlert\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

$apiInstance = new UPS\UPSTrackAlert\Request\UPSTrackAlertApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$trans_id = "trans_id_example"; // string | An identifier unique to the request.
$transaction_src = "transaction_src_example"; // string | Identifies the client/source application that is calling.
$type = "type_example"; // string | - 'Standard' - Represents a standard subscription type that provides near real time updates on tracking status.
$body = new \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest(); // \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest | 

try {
    $result = $apiInstance->processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $type, $body);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UPSTrackAlertApi->processSubscriptionTypeForTrackingNumber: ', $e->getMessage(), PHP_EOL;
}
?>
```

## Documentation for API Endpoints

All URIs are relative to *https://wwwcie.ups.com/api/track/{version}*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*UPSTrackAlertApi* | [**processSubscriptionTypeForTrackingNumber**](docs/Api/UPSTrackAlertApi.md#processsubscriptiontypefortrackingnumber) | **POST** /subscription/{type}/package | API to create subscriptions by tracking numbers.

## Documentation For Models

 - [ActivityLocation](docs/Model/ActivityLocation.md)
 - [ActivityStatus](docs/Model/ActivityStatus.md)
 - [Destination](docs/Model/Destination.md)
 - [ErrorMessage](docs/Model/ErrorMessage.md)
 - [Response](docs/Model/Response.md)
 - [TrackSubsServiceErrorResponse](docs/Model/TrackSubsServiceErrorResponse.md)
 - [TrackSubsServiceRequest](docs/Model/TrackSubsServiceRequest.md)
 - [TrackSubsServiceResponse](docs/Model/TrackSubsServiceResponse.md)
 - [TrackingEventRequest](docs/Model/TrackingEventRequest.md)

## Documentation For Authorization


## oauth2

- **Type**: OAuth
- **Flow**: application
- **Authorization URL**: 
- **Scopes**: 


## Author



