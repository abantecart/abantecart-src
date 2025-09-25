<?php
namespace Avalara;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Base AvaTaxClient object that handles connectivity to the AvaTax v2 API server.
 * This class is overridden by the descendant AvaTaxClient which implements all the API methods.
 */
class AvaTaxClientBase
{
  /**
     * @var Client     The Guzzle client to use to connect to AvaTax
     */
    protected $client;

    /**
     * @var array      The authentication credentials to use to connect to AvaTax
     */
    protected $auth;

    /**
     * @var string      The application name as reported to AvaTax
     */
    protected $appName;

    /**
     * @var string      The application version as reported to AvaTax
     */
    protected $appVersion;

    /**
     * @var string      The machine name as reported to AvaTax
     */
    protected $machineName;

    /**
     * @var string      The root URL of the AvaTax environment to contact
     */
    protected $environment;

    /**
     * @var bool        The setting for whether the client should catch exceptions
     */
    protected $catchExceptions;

    /**
     * @var LoggerInterface The logger instance which is intended to be used for logging puposes      
     */
    protected $logger;

    /**
     * @var bool        The setting for whether the request and response body should be logged or not
     */
    protected $logRequestAndResponseBody;

    /**
     * Construct a new AvaTaxClient
     *
     * @param string $appName      Specify the name of your application here.  Should not contain any semicolons.
     * @param string $appVersion   Specify the version number of your application here.  Should not contain any semicolons.
     * @param string $machineName  Specify the machine name of the machine on which this code is executing here.  Should not contain any semicolons.
     * @param string $environment  Indicates which server to use; acceptable values are "sandbox" or "production", or the full URL of your AvaTax instance.
     * @param array $guzzleParams  Extra parameters to pass to the guzzle HTTP client (http://docs.guzzlephp.org/en/latest/request-options.html)
     *
     * @throws \Exception
     */
    public function __construct($appName, $appVersion, $machineName="", $environment="", $guzzleParams = [], ?LoggerInterface $logger = null, $logRequestAndResponseBody = false)
    {
        // app name and app version are mandatory fields.
        if ($appName == "" || $appName == null || $appVersion == "" || $appVersion == null) {
            throw new \Exception('appName and appVersion are mandatory fields!');
        }

        // machine name is nullable, but must be empty string to avoid error when concat in client string.
        if ($machineName == null) {
            $machineName = "";
        }

        // assign client header params to current client object
        $this->appVersion = $appVersion;
        $this->appName = $appName;
        $this->machineName = $machineName;
        $this->environment = $environment;
        $this->catchExceptions = true;

        // Determine startup environment
        $env = 'https://rest.avatax.com';
        if ($environment == "sandbox") {
            $env = 'https://sandbox-rest.avatax.com';
        } else if ((substr($environment, 0, 8) == 'https://') || (substr($environment, 0, 7) == 'http://')) {
            $env = $environment;
        }

        // Prevent overriding the base_uri
        $guzzleParams['base_uri'] = $env;

        // Configure the HTTP client
        $this->client = new Client($guzzleParams);
        $this->logger = $logger;
        $this->logRequestAndResponseBody = $logRequestAndResponseBody;
    }

    /**
     * Configure this client to use the specified username/password security settings
     *
     * @param  string          $username   The username for your AvaTax user account
     * @param  string          $password   The password for your AvaTax user account
     * @return AvaTaxClient
     */
    public function withSecurity($username, $password)
    {
        $this->auth = [$username, $password];
        return $this;
    }

    /**
     * Configure this client to use Account ID / License Key security
     *
     * @param  int             $accountId      The account ID for your AvaTax account
     * @param  string          $licenseKey     The private license key for your AvaTax account
     * @return AvaTaxClient
     */
    public function withLicenseKey($accountId, $licenseKey)
    {
        $this->auth = [$accountId, $licenseKey];
        return $this;
    }

    /**
     * Configure this client to use bearer token
     *
     * @param  string          $bearerToken     The private bearer token for your AvaTax account
     * @return AvaTaxClient
     */
    public function withBearerToken($bearerToken)
    {
        $this->auth = [$bearerToken];
        return $this;
    }

    /**
     * Configure the client to either catch web request exceptions and return a message or throw the exception
     *
     * @param bool $catchExceptions
     * @return AvaTaxClient
     */
    public function withCatchExceptions($catchExceptions = true)
    {
        $this->catchExceptions = $catchExceptions;
        return $this;
    }

    /**
     * Return the client object, for extended class(es) to retrive the client object
     *
     * @return AvaTaxClient
     */
    public function getClient()
    {
        return $this;
    }


    /**
     * Make a single REST call to the AvaTax v2 API server
     *
     * @param string $apiUrl           The relative path of the API on the server
     * @param string $verb             The HTTP verb being used in this request
     * @param string $guzzleParams     The Guzzle parameters for this request, including query string and body parameters
     * @param string $apiversion       API Version of the request
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    protected function restCall($apiUrl, $verb, $guzzleParams, $apiversion='',$headerParams=null)
    {
        // Populate the log object with request details
        $logModel = new LogInformation();
        $logModel-> populateRequestInfo($verb, $apiUrl, $guzzleParams, $this->logRequestAndResponseBody);
        
        // Set authentication on the parameters
        if (count($this->auth) == 2) {
            if (!isset($guzzleParams['auth'])) {
                $guzzleParams['auth'] = $this->auth;
            }
            $guzzleParams['headers'] = [
                'Accept' => 'application/json',
                'X-Avalara-Client' => "{$this->appName}; {$this->appVersion}; PhpRestClient; {$apiversion}; {$this->machineName}"
            ];
        } else {
            $guzzleParams['headers'] = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->auth[0],
                'X-Avalara-Client' => "{$this->appName}; {$this->appVersion}; PhpRestClient; {$apiversion}; {$this->machineName}"
            ];
        }
        if (isset($headerParams) )
        {
            $guzzleParams['headers']= $guzzleParams['headers']+$headerParams;
        }
        // Check the client config, if set - update guzzleParams['timeout'] 
        if (isset($this->client->getConfig()['timeout']) ) {
            $guzzleParams['timeout'] = $this->client->getConfig()['timeout'];
        }
        // Set default 1200 second timeout, if guzzleParams['timeout'] is not set
        if (!isset($guzzleParams['timeout'])) {
            $guzzleParams['timeout'] = 1200;
        }
        
        // Check the client config, if set - update guzzleParams['connect_timeout'] 
        if (isset($this->client->getConfig()['connect_timeout'])) {
            $guzzleParams['connect_timeout'] = $this->client->getConfig()['connect_timeout'];
        }
        // Set default 1200 second timeout, if guzzleParams['connect_timeout'] is not set
        if (!isset($guzzleParams['connect_timeout'])) {
            $guzzleParams['connect_timeout'] = 1200;
        }
        
        // Contact the server
        try {
            $logModel->setStartTime(microtime(true));
            $response = $this->client->request($verb, $apiUrl, $guzzleParams);
            $body = $response->getBody();

            $length = 0;
            
            $contentLength = $response->getHeader('Content-Length');
            if ($contentLength!=null)
            {
                $length=$contentLength[0];
            } 
            $code=$response->getStatusCode();
            $contentTypes =  $response->getHeader('Content-Type');
            
            if ( in_array ("text/csv", $contentTypes)) {
                return $body;
            }

            if ( in_array ("application/json", $contentTypes))
            {
                if (($contentLength != null and $length == 0 and intdiv($code , 100) == 2) || $code == 204 ){
                        return null;                
                }
            }
            $JsonBody = json_decode($body);
            if (is_null($JsonBody)) {
                if (json_last_error() === JSON_ERROR_SYNTAX) {
                    $errorMsg = 'The response is in unexpected format. The response is: ';
                    // populate exception details in log object
                    $logModel->populateErrorInfoWithMessageAndBody($errorMsg, $response);
				    throw new \Exception($errorMsg . $JsonBody);
			    }
                $logModel->populateResponseInfo($body, $response);
                return $body;
            } else {
                $logModel->populateResponseInfo($JsonBody, $response);
                return $JsonBody;
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // populate exception details in log object
            $errorContents = $e->getResponse()->getBody()->getContents();
            $logModel->populateErrorInfo($e, $errorContents);
            if (!$this->catchExceptions) {
                throw $e;
            }
            return $errorContents;
        } finally {
            // log the error / info details
            if(!is_null($this->logger)) {
                if(!is_null($logModel-> statusCode) && $logModel-> statusCode < 400) {
                    $this->logger->info(json_encode($logModel));
                } else {
                    $this->logger->error(json_encode($logModel));
                }
            }
        }
    }
}
