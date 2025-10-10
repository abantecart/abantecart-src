<?php
namespace Avalara;

// This is an example for users to create an extension class on the AvaTaxClient to hold additional config/information
class ClientExtensionExample extends AvaTaxClient
{
    
    // any additional client config/info 
    protected $clientConfig;


    /**
     * Construct a new AvaTaxClient
     *
     * @param string $appName      Specify the name of your application here.  Should not contain any semicolons.
     * @param string $appVersion   Specify the version number of your application here.  Should not contain any semicolons.
     * @param string $machineName  Specify the machine name of the machine on which this code is executing here.  Should not contain any semicolons.
     * @param string $environment  Indicates which server to use; acceptable values are "sandbox" or "production", or the full URL of your AvaTax instance.
     * @param array $guzzleParams  Extra parameters to pass to the guzzle HTTP client (http://docs.guzzlephp.org/en/latest/request-options.html)
     */
    public function __construct($appName, $appVersion, $machineName="", $environment="", $guzzleParams = [], $clientConfig = null)
    {
        parent::__construct($appName, $appVersion, $machineName, $environment, $guzzleParams);
        $this->clientConfig = $clientConfig;
    }

    public function echoAddedConfig()
    {
        return $this->clientConfig;
    }
}

?>
