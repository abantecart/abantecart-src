<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

final class ARequest
{
    public $get = [];
    public $post = [];
    public $cookie = [];
    public $files = [];
    public $server = [];

    private $http;
    private $version;
    private $browser;
    private $browser_version;
    private $platform;
    private $device_type;

    public function __construct()
    {
        $_GET = $this->clean($_GET);
        $_POST = $this->clean($_POST);
        $_COOKIE = $this->clean($_COOKIE);
        $_FILES = $this->clean($_FILES);
        $_SERVER = $this->clean($_SERVER);

        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        //check if there is any encrypted data
        if (isset($this->get['__e']) && has_value($this->get['__e'])) {
            $this->get = array_replace_recursive($this->get, $this->decodeURI($this->get['__e']));
        }
        if (isset($this->post['__e']) && has_value($this->post['__e'])) {
            $this->post = array_replace_recursive($this->post, $this->decodeURI($this->post['__e']));
        }
        $this->_detectBrowser();
    }

    //todo: Include PHP module filter to process input params. http://us3.php.net/manual/en/book.filter.php
    /**
     * function returns variable value from $_GET first
     *
     * @param string $key
     *
     * @return string | null
     */
    public function get_or_post($key)
    {
        if (isset($this->get[$key])) {
            return $this->get[$key];
        } else {
            if (isset($this->post[$key])) {
                return $this->post[$key];
            }
        }
        return null;
    }

    /**
     * function returns variable value from $_POST first
     *
     * @param string $key
     *
     * @return string | null
     */
    public function post_or_get($key)
    {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        } else {
            if (isset($this->get[$key])) {
                return $this->get[$key];
            }
        }
        return null;
    }

    /**
     * Prevent hacks and non-browser requests with non-encoded data.
     *
     * @param string|array $data
     *
     * @return array|string
     */
    public function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $key = $this->clean($key);
                $data[$key] = $this->clean($value);
                //check route and forbid if it's wrong
                if ($key == 'rt' && preg_match('/[^A-Za-z0-9_\/]/', $data[$key])) {
                    http_response_code(403);
                    exit('Forbidden');
                }
            }
        } else {
            $data = trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
        }
        return $data;
    }

    /**
     * @param string - base64 $uri
     *
     * @return array
     */
    public function decodeURI($uri)
    {
        $params = [];
        $open_uri = base64_decode($uri);

        $split_parameters = explode('&', $open_uri);
        for ($i = 0; $i < count($split_parameters); $i++) {
            $final_split = explode('=', $split_parameters[$i]);
            $params[$final_split[0]] = $final_split[1];
        }
        //clean data before return
        return $this->clean($params);
    }

    private function _detectBrowser()
    {

        $nua = strtolower($_SERVER['HTTP_USER_AGENT']??'');

        $agent['http'] = isset($nua) ? $nua : "";
        $agent['version'] = 'unknown';
        $agent['browser'] = 'unknown';
        $agent['platform'] = 'unknown';
        $agent['device_type'] = '';

        $oss = ['win', 'mac', 'linux', 'unix'];
        foreach ($oss as $os) {
            if (strstr($agent['http'], $os)) {
                $agent['platform'] = $os;
                break;
            }
        }

        $browsers = ["mozilla", "msie", "gecko", "firefox", "konqueror", "safari", "netscape", "navigator", "opera", "mosaic", "lynx", "amaya", "omniweb"];

        for ($i = 0; $i < count($browsers); $i++) {
            if (strlen(stristr($nua, $browsers[$i])) > 0) {
                $agent["browser"] = $browsers[$i];
                break;
            }
        }

        //http://en.wikipedia.org/wiki/List_of_user_agents_for_mobile_phones - list of useragents
        $devices = ["iphone", "android", "blackberry", "ipod", "ipad", "htc", "symbian", "webos", "opera mini", "windows phone os", "iemobile"];

        for ($i = 0; $i < count($devices); $i++) {
            if (stristr($nua, $devices[$i])) {
                $agent["device_type"] = $devices[$i];
                break;
            }
        }

        $this->browser = $agent['browser'];
        $this->device_type = $agent['device_type'];
        $this->http = $agent['http'];
        $this->platform = $agent['platform'];
        $this->version = $agent['version'];

    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getBrowserVersion()
    {
        return $this->browser_version;
    }

    public function getDeviceType()
    {
        return $this->device_type;
    }

    public function getHttp()
    {
        return $this->http;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getRemoteIP()
    {

        if(isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
        { //if user behind cloudflare proxy
            $ip = $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }elseif(!empty($this->server['HTTP_CLIENT_IP']))
        {
            $ip = $this->server['HTTP_CLIENT_IP'];
        }elseif(!empty($this->server['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $this->server['HTTP_X_FORWARDED_FOR'];
        }else
        {
            $ip = $this->server['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @return bool
     */
    public function is_POST()
    {
        return ($this->server['REQUEST_METHOD'] == 'POST');
    }

    /**
     * @return bool
     */
    public function is_GET()
    {
        return ($this->server['REQUEST_METHOD'] == 'GET');
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function deleteCookie($name)
    {
        if (empty($name)) {
            return false;
        }
        setCookieOrParams(
            $name,
            null,
            [
                'lifetime' => -1,
                'path'     => dirname($this->server['PHP_SELF'])
            ]
        );
        unset($this->cookie[$name], $_COOKIE[$name]);
        return true;
    }
}