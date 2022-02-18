<?php

class TextMarketer
{
    private $url = ''; // url of the service
    private $username = '';
    private $password = '';
    private $message_id, $credits_used;

    function __construct($username, $password, $sandbox = true)
    {
        $this->username = $username;
        $this->password = $password;
        if ($sandbox) {
            $this->url = 'http://sandbox.textmarketer.biz/services/rest/sms';
        } else {
            $this->url = 'http://api.textmarketer.co.uk/services/rest/sms';
        }
    }

    public function getMessageID()
    {
        return $this->message_id;
    }

    public function getCreditsUsed()
    {
        return $this->credits_used;
    }

    // public function to commit the send
    public function send($message, $mobile, $originator)
    {

        //var_dump($this->username, $this->password, $this->url, $message, $mobile, $originator); exit;
        $url_array = array(
            'message'       => $message,
            'mobile_number' => $mobile,
            'originator'    => $originator,
            'username'      => $this->username,
            'password'      => $this->password,
        );

        $url_string = $data = http_build_query($url_array, '', '&');
        // we're using the curl library to make the request
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $url_string);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $responseBody = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        return $this->handleResponse($responseBody, $responseInfo);
    }

    private function handleResponse($body, $info)
    {
        if ($info['http_code'] == 200) { // successful submission
            $xml_obj = simplexml_load_string($body);
            // extract message id and credit usuage
            $this->message_id = (int)$xml_obj->message_id;
            $this->credits_used = (int)$xml_obj->credits_used;
            return true;
        } else {
            $this->message_id = null;
            $this->credits_used = null;
            $xml_obj = @simplexml_load_string($body);
            if ((string)$xml_obj->errors->error) {
                $error_text = "Textmarketer error: \n".(string)$xml_obj->errors[0]->error."\n";
                foreach ($xml_obj->errors[0]->error->attributes() as $a => $b) {
                    $error_text .= $a.'="'.$b."\"\n";
                }
            } else {
                $error_text = "Textmarketer error: API response: \n".var_export($body, true);
            }
            throw new Exception($error_text);

        }
    }
}