<?php
/**
 * UPSTrackAlertApi
 * PHP version 5
 *
 * @category Class
 * @package  UPS\UPSTrackAlert
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * UPS Track Alert API
 *
 * # Product Info  The UPS Track Alert API provides best in-class package tracking visibility with near real time event updates for an improved customer experience and stream line logistic management. Updates are pushed to the user as soon as available with no constant polling required, thereby improving operational efficiency.<br /><a href=\"https://developer.ups.com/api/reference/trackalert/product-info\" target=\"_blank\" rel=\"noopener\">Product Info</a>  # Business Values  - **Enhanced Customer Experience**: Near Real-time tracking information increases transparency, leading to higher customer satisfaction and trust. - **Operational Efficiency**: Eliminates the necessity for continuous polling, thus saving computational resources and improving system responsiveness. - **Data-Driven Decision Making**: Access to timely data can help businesses optimize their supply chain and make informed logistics decisions. - **Optimizing Cash Flow Through Near Real-Time Delivery Tracking**: Improve cash flow by knowing the deliveries occurred in near real time. - **Mitigating Fraud and Theft through Near Real-Time Package Status Monitoring**: Reduce fraud and theft by knowing the status of the package.  # Error Codes  | Error Code | HTTP Status | Description                                                                                                                                                                                              | |------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------| | VSS000     | 400         | Invalid request and The Subscription request has been rejected.                                                                                                                                          | | VSS002     | 400         | Missing transId.                                                                                                                                                                                         | | VSS003     | 400         | Please enter a valid Transaction ID, The Subscription request has been rejected.                                                                                                                         | | VSS004     | 400         | Missing transactionSrc.                                                                                                                                                                                  | | VSS006     | 400         | Please enter a valid Transaction SRC, The Subscription request has been rejected.                                                                                                                        | | VSS100     | 500         | We're sorry, the system is temporarily unavailable. Please try again later.                                                                                                                              | | VSS110     | 400         | Subscription request is empty or not present. The Subscription request has been rejected.                                                                                                                | | VSS200     | 400         | Tracking Number List is required. The Subscription request has been rejected.                                                                                                                            | | VSS210     | 400         | The Subscription request should have at least one valid tracking number. The Subscription request has been rejected.                                                                                     | | VSS215     | 400         | The 1Z tracking number that was submitted is not a valid CIE 1Z and has been rejected.                                                                                                                   | | VSS220     | 400         | You have submitted over 100 1Z numbers which is not allowed. The entire submission of 1Z numbers has been rejected. Please resubmit your request again using groups of no more than 100 1Z numbers.      | | VSS300     | 400         | Locale is required. The Subscription request has been rejected.                                                                                                                                          | | VSS310     | 400         | Please enter a valid locale. The Subscription request has been rejected.                                                                                                                                 | | VSS400     | 400         | Please enter a valid country code. The Subscription request has been rejected.                                                                                                                           | | VSS500     | 400         | Destination is required. The Subscription request has been rejected.                                                                                                                                     | | VSS600     | 400         | URL is empty or not present. The Subscription request has been rejected.                                                                                                                                 | | VSS610     | 400         | URL is too long. The Subscription request has been rejected.                                                                                                                                             | | VSS700     | 400         | Credential is empty or not present. The Subscription request has been rejected.                                                                                                                          | | VSS800     | 400         | CredentialType is empty or not present. The Subscription request has been rejected.                                                                                                                      | | VSS930     | 400         | Type is missing or invalid, The Subscription request has been rejected.                                                                                                                                  |   # FAQs - **How do I check if a subscription to a 1Z was successful?** >  A message will be sent via the API call with details about any successful and failed subscriptions.  - **I stopped receiving event messages after 2 weeks and my package hasn’t been delivered. Why?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How do I get events that occurred prior to subscription?** >  Each 1Z subscription is valid for 14 days If the package has not been delivered within 14 days, you must resubscribe to the 1Z to continue receiving updates/events.  - **How many 1Z tracking numbers can a subscriber subscribe to in one request?** >  A message will be sent via the API call with details about any successful and failed subscriptions.  - **What does this Track Alert code mean?** >  look up all codes on the Track Alert Codes file \"X\" type: exception codes \"I\" type: in transit codes \"M\" and \"MV\" type\": manifest codes \"U\" type: delivery update codes \"D\" type: delivery codes These codes can appear in more than one type (for example, a type X code may also appear with a type D code in the Track Alert message These codes typically appear with a different type code when a package is delivered to a UPS access point, returned to sender, or rerouted. The codes are updated monthly Ask for an updated listed if you discover a new code that does not have a description on the list. The primary or more common codes rarely change.
 *
 * OpenAPI spec version: 1.0.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.50
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace UPS\UPSTrackAlert\Request;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use UPS\UPSTrackAlert\ApiException;
use UPS\UPSTrackAlert\Configuration;
use UPS\UPSTrackAlert\HeaderSelector;
use UPS\UPSTrackAlert\ObjectSerializer;

/**
 * UPSTrackAlertApi Class Doc Comment
 *
 * @category Class
 * @package  UPS\UPSTrackAlert
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class UPSTrackAlertApi
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
     * @param  string $trans_id An identifier unique to the request. (required)
     * @param  string $transaction_src Identifies the client/source application that is calling. (required)
     * @param  string $type - &#x27;Standard&#x27; - Represents a standard subscription type that provides near real time updates on tracking status. (required)
     * @param  \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest $body body (optional)
     *
     * @throws \UPS\UPSTrackAlert\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse
     */
    public function processSubscriptionTypeForTrackingNumber($trans_id, $transaction_src, $type, $body = null)
    {
        list($response) = $this->processSubscriptionTypeForTrackingNumberWithHttpInfo($trans_id, $transaction_src, $type, $body);
        return $response;
    }

    /**
     * Operation processSubscriptionTypeForTrackingNumberWithHttpInfo
     *
     * API to create subscriptions by tracking numbers.
     *
     * @param  string $trans_id An identifier unique to the request. (required)
     * @param  string $transaction_src Identifies the client/source application that is calling. (required)
     * @param  string $type - &#x27;Standard&#x27; - Represents a standard subscription type that provides near real time updates on tracking status. (required)
     * @param  \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest $body (optional)
     *
     * @throws \UPS\UPSTrackAlert\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function processSubscriptionTypeForTrackingNumberWithHttpInfo($trans_id, $transaction_src, $type, $body = null)
    {
        $returnType = '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse';
        $request = $this->processSubscriptionTypeForTrackingNumberRequest($trans_id, $transaction_src, $type, $body);

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
                        '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlert\UPSTrackAlert\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceErrorResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 405:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlert\UPSTrackAlert\Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 429:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceErrorResponse',
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
     * @param  string $trans_id An identifier unique to the request. (required)
     * @param  string $transaction_src Identifies the client/source application that is calling. (required)
     * @param  string $type - &#x27;Standard&#x27; - Represents a standard subscription type that provides near real time updates on tracking status. (required)
     * @param  \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest $body (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function processSubscriptionTypeForTrackingNumberAsync($trans_id, $transaction_src, $type, $body = null)
    {
        return $this->processSubscriptionTypeForTrackingNumberAsyncWithHttpInfo($trans_id, $transaction_src, $type, $body)
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
     * @param  string $trans_id An identifier unique to the request. (required)
     * @param  string $transaction_src Identifies the client/source application that is calling. (required)
     * @param  string $type - &#x27;Standard&#x27; - Represents a standard subscription type that provides near real time updates on tracking status. (required)
     * @param  \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest $body (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function processSubscriptionTypeForTrackingNumberAsyncWithHttpInfo($trans_id, $transaction_src, $type, $body = null)
    {
        $returnType = '\UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceResponse';
        $request = $this->processSubscriptionTypeForTrackingNumberRequest($trans_id, $transaction_src, $type, $body);

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
     * @param  string $trans_id An identifier unique to the request. (required)
     * @param  string $transaction_src Identifies the client/source application that is calling. (required)
     * @param  string $type - &#x27;Standard&#x27; - Represents a standard subscription type that provides near real time updates on tracking status. (required)
     * @param  \UPS\UPSTrackAlert\UPSTrackAlert\TrackSubsServiceRequest $body (optional)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function processSubscriptionTypeForTrackingNumberRequest($trans_id, $transaction_src, $type, $body = null)
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
        // verify the required parameter 'type' is set
        if ($type === null || (is_array($type) && count($type) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $type when calling processSubscriptionTypeForTrackingNumber'
            );
        }

        $resourcePath = '/subscription/{type}/package';
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
        if ($type !== null) {
            $resourcePath = str_replace(
                '{' . 'type' . '}',
                ObjectSerializer::toPathValue($type),
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
