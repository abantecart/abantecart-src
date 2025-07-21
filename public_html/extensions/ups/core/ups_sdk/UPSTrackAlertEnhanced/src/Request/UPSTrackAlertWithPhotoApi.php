<?php
/**
 * UPSTrackAlertWithPhotoApi
 * PHP version 5
 *
 * @category Class
 * @package  UPS\UPSTrackAlertEnhanced
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * UPS Track Alert API with Photo
 *
 * # Product Info  The UPS Track Alert API provides best in-class package tracking visibility with near real time event updates for an improved customer experience and stream line logistic management. Updates are pushed to the user as soon as available with no constant polling required, thereby improving operational efficiency. For more information on the UPS Track Alert API with Photo, please visit the <a href=\"https://developer.ups.com/api/reference/trackalertenhanced/product-info\" target=\"_blank\" rel=\"noopener\">Product Info</a> page. <br/><p>Try out UPS APIs with example requests using Postman. Explore API documentation and sample applications through GitHub.</p>  <a href=\"https://god.gw.postman.com/run-collection/29542085-7513df2c-af1b-4e5c-8b5d-5797d03a6a44?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D29542085-7513df2c-af1b-4e5c-8b5d-5797d03a6a44%26entityType%3Dcollection%26workspaceId%3D7e7595f0-4829-4f9a-aee1-75c126b9d417\" target=\"_blank\" rel=\"noopener noreferrer\">   <img src=\"https://run.pstmn.io/button.svg\" alt=\"Run In Postman\" style=\"width: 128px; height: 32px;\"></a> <a href=\"https://github.com/UPS-API\" target=\"_blank\" rel=\"noopener noreferrer\">   <img src=\"https://www.ups.com/assets/resources/webcontent/images/gitHubButton.svg\" alt=\"Open in GitHub\" style=\"width: 128px; height: 32px;\"> </a>  # Business Values  - **Enhanced Customer Experience**: Near real-time tracking information increases transparency, leading to higher customer satisfaction and trust. - **Operational Efficiency**: Eliminates the necessity for continuous polling, thus saving resources and improving system responsiveness. - **Data-Driven Decision Making**: Access to near real-time data can help businesses optimize their supply chain and make informed logistics decisions. - **Optimizing Cash Flow Through Near Real-Time Delivery Tracking**: Improve cash flow by knowing that deliveries occurred in near real time. - **Mitigating Fraud and Theft through Near Real-Time Package Status Monitoring**: Reduce fraud and theft by knowing any time something happens to your packages.  # How does it work once I am setup as a user? You submit up to 100 UPS 1Z package tracking numbers to the API at a time, using OAUTH in a JSON format.  Your submission needs to include the URL where Track Alert will send a message with any events that occur to your 1Z package for the next 14 days.  This saves you the effort of polling UPS to determine what is the current status of your package.  # FAQs     - **When can I expect a delivery photo?** >  Delivery photos are only available with residential deliveries in package release areas. Additional message with the delivery photo will be sent, if and when the photo is made available.  - **How do I check if a subscription to a 1Z was successful?** >  Once 1Z package tracking number(s) is submitted to Track Alert, you will receive a response confirming successful and unsuccessful 1Z's.    - **I stopped receiving event messages after 2 weeks and my package hasn’t been delivered. Why?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How do I get events that occurred prior to subscription?** >  Track Alert does not retain any history.  You should use the UPS Track API to receive history about your package.    - **How many 1Z tracking numbers can a subscriber subscribe to in one request?** >  You can subscribe to up to 100 1Z in each submission to the API.  A reply message will be sent via the API with details showing successful and unsuccessful 1Z's submissions.  - **What types of event data does Track Alert provide?** >  In addition to the expected local dates and times when the event occurred (including GMT date and time), you will receive details about the event that include status-type, status-code, status-description and status-description code.     Status types are:       M and MV = manifest information,        X = exception information (something out of the normal happened to your package, but it may still be delivered on time),        I = in-progress (on the way or moving normally through the UPS network),        U = update (there is an update to your package, normally the scheduled delivery has been updated, but it may still be delivered on time)       D = delivery information (loaded on delivery vehicle, out for delivery, delivered)   Status codes are a 2-character code that provide details about the event.  There is a list of these codes and their translations elsewhere on this portal.   Status descriptions are a very brief (a few words) describing the status code.   Status-description code is a overly simplified description of the event.  This description is intended for those who do not understand transportation.  - **What does the message look like?** >  This is what a message looks like for an event that is sent to your URL.  Not every field will have a value for every message.  We have converted the JSON format message to text format for clarity.     Those fields are:      1Z package tracking number      scheduled delivery date (this field maybe updated, example '20240905')     actual delivery date (this field is blank until the delivery event occurs)     actual delivery time (this field is blank until the delivery event occurs)     activity location city     activity location state/province     activity location postal code (this field is blank until the delivery event occurs)     activity location country     activity status type     activity status code     activity status description     activity status description code     local activity date     local activity time     GMT activity date     GMT activity time     delivery start time (example '150000')     delivery end time (example '170000')     delivery time description (example 'estimated delivery window' or 'end of day')     delivery photo (this field is only available for enhanced users)  - **Can I test this process?** >  Yes, there are two test 1Z's that you can submit, and resubmit that will send several events spaced 1 second apart.  Those two test 1Z's are 1ZCIETST0111111114 and 1ZCIETST0422222228.  Please ensure to use UPS Production CIE(https://wwwcie.ups.com/api/track/{version}). You can submit these 1Z's as often as you like. (no stress testing please.)   # Error Codes  | Error Code | HTTP Status |Description                                                                                                                                                                                              | |------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| | VSS000     | 400         | Invalid request and The Subscription request has been rejected.                                                                                                                                          | | VSS002     | 400         | Missing transId.                                                                                                                                                                                         | | VSS003     | 400         | Please enter a valid Transaction ID, The Subscription request has been rejected.                                                                                                                         | | VSS004     | 400         | Missing transactionSrc.                                                                                                                                                                                  | | VSS006     | 400         | Please enter a valid Transaction SRC, The Subscription request has been rejected.                                                                                                                        | | VSS100     | 500         | We're sorry, the system is temporarily unavailable. Please try again later.                                                                                                                              | | VSS110     | 400         | Subscription request is empty or not present. The Subscription request has been rejected.                                                                                                                | | VSS200     | 400         | Tracking Number List is required. The Subscription request has been rejected.                                                                                                                            | | VSS210     | 400         | The Subscription request should have at least one valid tracking number. The Subscription request has been rejected.                                                                                     | | VSS215     | 400         | The 1Z tracking number that was submitted is not a valid CIE 1Z and has been rejected.                                                                                                                   | | VSS220     | 400         | You have submitted over 100 1Z numbers which is not allowed. The entire submission of 1Z numbers has been rejected. Please resubmit your request again using groups of no more than 100 1Z numbers.      | | VSS300     | 400         | Locale is required. The Subscription request has been rejected.                                                                                                                                          | | VSS310     | 400         | Please enter a valid locale. The Subscription request has been rejected.                                                                                                                                 | | VSS400     | 400         | Please enter a valid country code. The Subscription request has been rejected.                                                                                                                           | | VSS500     | 400         | Destination is required. The Subscription request has been rejected.                                                                                                                                     | | VSS600     | 400         | URL is empty or not present. The Subscription request has been rejected.                                                                                                                                 | | VSS610     | 400         | URL is too long. The Subscription request has been rejected.                                                                                                                                             | | VSS700     | 400         | Credential is empty or not present. The Subscription request has been rejected.                                                                                                                          | | VSS800     | 400         | CredentialType is empty or not present. The Subscription request has been rejected.                                                                                                                      | | VSS930     | 400         | Type is missing or invalid, The Subscription request has been rejected.
 *
 * OpenAPI spec version: 1.0.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.71
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace UPS\UPSTrackAlertEnhanced\Request;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use UPS\UPSTrackAlertEnhanced\ApiException;
use UPS\UPSTrackAlertEnhanced\Configuration;
use UPS\UPSTrackAlertEnhanced\HeaderSelector;
use UPS\UPSTrackAlertEnhanced\ObjectSerializer;

/**
 * UPSTrackAlertWithPhotoApi Class Doc Comment
 *
 * @category Class
 * @package  UPS\UPSTrackAlertEnhanced
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class UPSTrackAlertWithPhotoApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation processSubscriptionTypeForTrackingNumber
     *
     * API to create subscriptions by tracking numbers.
     *
     * @param   $trans_id An identifier unique to the request. (required)
     * @param   $transaction_src Identifies the client/source application that is calling. (required)
     * @param   $version version (required)
     * @param  \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceRequest $body body (optional)
     *
     * @throws \UPS\UPSTrackAlertEnhanced\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceResponse
     */
    public function processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $version, $body = null)
    {
        list($response) = $this->processSubscriptionTypeForTrackingNumberWithHttpInfo($trans_id, $transaction_src, $version, $body);
        return $response;
    }

    /**
     * Operation processSubscriptionTypeForTrackingNumberWithHttpInfo
     *
     * API to create subscriptions by tracking numbers.
     *
     * @param   $trans_id An identifier unique to the request. (required)
     * @param   $transaction_src Identifies the client/source application that is calling. (required)
     * @param   $version version (required)
     * @param  \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceRequest $body (optional)
     *
     * @throws \UPS\UPSTrackAlertEnhanced\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function processSubscriptionTypeForTrackingNumberWithHttpInfo($trans_id, $transaction_src, $version, $body = null)
    {
        $returnType = '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceResponse';
        $request = $this->processSubscriptionTypeForTrackingNumberRequest($trans_id, $transaction_src, $version, $body);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if (!in_array($returnType, ['string','integer','bool'])) {
                    $content = json_decode($content);
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 405:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation processSubscriptionTypeForTrackingNumberAsync
     *
     * API to create subscriptions by tracking numbers.
     *
     * @param   $trans_id An identifier unique to the request. (required)
     * @param   $transaction_src Identifies the client/source application that is calling. (required)
     * @param   $version version (required)
     * @param  \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceRequest $body (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function processSubscriptionTypeForTrackingNumberAsync($trans_id, $transaction_src, $version, $body = null)
    {
        return $this->processSubscriptionTypeForTrackingNumberAsyncWithHttpInfo($trans_id, $transaction_src, $version, $body)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation processSubscriptionTypeForTrackingNumberAsyncWithHttpInfo
     *
     * API to create subscriptions by tracking numbers.
     *
     * @param   $trans_id An identifier unique to the request. (required)
     * @param   $transaction_src Identifies the client/source application that is calling. (required)
     * @param   $version version (required)
     * @param  \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceRequest $body (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function processSubscriptionTypeForTrackingNumberAsyncWithHttpInfo($trans_id, $transaction_src, $version, $body = null)
    {
        $returnType = '\UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceResponse';
        $request = $this->processSubscriptionTypeForTrackingNumberRequest($trans_id, $transaction_src, $version, $body);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'processSubscriptionTypeForTrackingNumber'
     *
     * @param   $trans_id An identifier unique to the request. (required)
     * @param   $transaction_src Identifies the client/source application that is calling. (required)
     * @param   $version version (required)
     * @param  \UPS\UPSTrackAlertEnhanced\UPSTrackAlertEnhanced\TrackSubsServiceRequest $body (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function processSubscriptionTypeForTrackingNumberRequest($trans_id, $transaction_src, $version, $body = null)
    {
        // verify the required parameter 'trans_id' is set
        if ($trans_id === null || (is_array($trans_id) && count($trans_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $trans_id when calling processSubscriptionTypeForTrackingNumber'
            );
        }
        // verify the required parameter 'transaction_src' is set
        if ($transaction_src === null || (is_array($transaction_src) && count($transaction_src) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $transaction_src when calling processSubscriptionTypeForTrackingNumber'
            );
        }
        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling processSubscriptionTypeForTrackingNumber'
            );
        }

        $resourcePath = '/subscription/enhanced/package';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // header params
        if ($trans_id !== null) {
            $headerParams['transId'] = ObjectSerializer::toHeaderValue($trans_id);
        }
        // header params
        if ($transaction_src !== null) {
            $headerParams['transactionSrc'] = ObjectSerializer::toHeaderValue($transaction_src);
        }

        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }

        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['application/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['application/json'],
                ['application/json']
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            // \stdClass has no __toString(), so we should encode it manually
            if ($httpBody instanceof \stdClass && $headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($httpBody);
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires OAuth (access token)
        if ($this->config->getAccessToken() !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
