<?php
/**
 * TaxServiceRest.class.php
 */

/**
 *
 * TaxServiceRest reads its configuration values from parameters in the constructor
 *
 * <p>
 * <b>Example:</b>
 * <pre>
 *  $taxService = new TaxServiceRest("https://development.avalara.net","1100012345","1A2B3C4D5E6F7G8");
 * </pre>
 *
 * @author    Avalara
 * @copyright � 2004 - 2011 Avalara, Inc.  All rights reserved.
 * @package   Tax
 *
 */

namespace AvaTax;

class TaxServiceRest
{
    static protected $classmap = array(
        'Address'          => 'Address',
        'ValidAddress'     => 'ValidAddress',
        'Message'          => 'Message',
        'ValidateRequest'  => 'ValidateRequest',
        'ValidateResult'   => 'ValidateResult',
        'Line'             => 'Line',
        'CancelTaxRequest' => 'CancelTaxRequest',
        'CancelTaxResult'  => 'CancelTaxResult',
        'GetTaxRequest'    => 'GetTaxRequest',
        'GetTaxResult'     => 'GetTaxResult',
        'TaxLine'          => 'TaxLine',
        'TaxDetail'        => 'TaxDetail',
        'BaseResult'       => 'BaseResult',
        'TaxOverride'      => 'TaxOverride',
    );
    protected $config = array();

    public function __construct($url, $account, $license)
    {
        $this->config = array(
            'url'     => $url,
            'account' => $account,
            'license' => $license,
        );
    }

    //Voids a document that has already been recorded on the Admin Console.
    public function cancelTax(&$cancelTaxRequest)
    {
        if (!(filter_var($this->config['url'], FILTER_VALIDATE_URL))) {
            throw new Exception("A valid service URL is required.");
        }
        if (empty($this->config['account'])) {
            throw new Exception("Account number or username is required.");
        }
        if (empty($this->config['license'])) {
            throw new Exception("License key or password is required.");
        }

        $url = $this->config['url']."/1.0/tax/cancel";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->config['account'].":".$this->config['license']);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //Some Windows users have had trouble with our SSL Certificates. Uncomment this line to NOT use SSL.

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($cancelTaxRequest));
        $curl_response = curl_exec($curl);
        curl_close($curl);
        return CancelTaxResult::parseResult($curl_response);
    }

    //Calculates tax on a document and/or records that document to the Admin Console.
    public function getTax(&$getTaxRequest)
    {

        if (!(filter_var($this->config['url'], FILTER_VALIDATE_URL))) {
            throw new Exception("A valid service URL is required.");
        }
        if (empty($this->config['account'])) {
            throw new Exception("Account number or username is required.");
        }
        if (empty($this->config['license'])) {
            throw new Exception("License key or password is required.");
        }

        $url = $this->config['url']."/1.0/tax/get";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->config['account'].":".$this->config['license']);

        //Some Windows users have had trouble with our SSL Certificates. Uncomment the following line to NOT use SSL.
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 		

        //Other Windows users may prefer to download the certificate from our site (detail here: ) and manually set the cert path.
        //    To set the path manually, uncomment the following two lines. If you choose to manually set the path, make sure you have commented out the line above 
        //    that tells curl to NOT use SSL.
        //$ca = "C:/curl/curl-ca-bundle.crt";
        //curl_setopt($curl, CURLOPT_CAINFO, $ca);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($getTaxRequest));
        $curl_response = curl_exec($curl);

        curl_close($curl);

        return GetTaxResult::parseResult($curl_response);

    }

    //Estimates a composite tax based on latitude/longitude and total sale amount.
    public function estimateTax(&$estimateTaxRequest)
    {
        if (!(filter_var($this->config['url'], FILTER_VALIDATE_URL))) {
            throw new Exception("A valid service URL is required.");
        }
        if (empty($this->config['account'])) {
            throw new Exception("Account number or username is required.");
        }
        if (empty($this->config['license'])) {
            throw new Exception("License key or password is required.");
        }

        $url = $this->config['url'].'/1.0/tax/'.$estimateTaxRequest->getLatitude().",".$estimateTaxRequest->getLongitude().'/get?saleamount='.$estimateTaxRequest->getSaleAmount();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->config['account'].":".$this->config['license']);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $curl_response = curl_exec($curl);

        return EstimateTaxResult::parseResult($curl_response);

    }

    //There is no explicit ping function in the REST API, so here's an imitation.
    public function ping($msg = "")
    {
        $request = new EstimateTaxRequest("47.627935", "-122.51702", "10");
        return $this->estimateTax($request);

    }
}

?>
