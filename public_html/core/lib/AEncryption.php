<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

final class AEncryption
{
    private string $key;

    /**
     * @param string $key
     */
    function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Encode function
     *
     * @param string $value
     *
     * @return string
     * @throws AException
     */
    function encrypt($value)
    {
        if (!$this->key) {
            return $value;
        }

        $enc_str = '';
        if (!$this->isOpenSslSupported()) {
            //non openssl basic encryption
            for ($i = 0; $i < strlen($value); $i++) {
                $char = substr($value, $i, 1);
                $keyChar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
                $char = chr(ord($char) + ord($keyChar));
                $enc_str .= $char;
            }
            $enc_str = base64_encode($enc_str);
        } else {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $enc_str = base64_encode(openssl_encrypt($value, 'aes-256-cbc', $this->key, 0, $iv) . '::' . $iv);
        }
        return str_replace('==', '', strtr($enc_str, '+/', '-_'));
    }

    /**
     * Decode function
     *
     * @param string $encryptedValue
     *
     * @return string
     * @throws AException
     */
    function decrypt($encryptedValue)
    {
        if (!$this->key) {
            return $encryptedValue;
        }

        $str = '';
        $encryptedValue = base64_decode(strtr($encryptedValue, '-_', '+/') . '==');
        if (!$this->isOpenSslSupported()) {
            //non openssl basic decryption
            for ($i = 0; $i < strlen($encryptedValue); $i++) {
                $char = substr($encryptedValue, $i, 1);
                $keyChar = substr($this->key, ($i % strlen($this->key)) - 1, 1);
                $char = chr(ord($char) - ord($keyChar));
                $str .= $char;
            }
        } else {
            list($encrypted_data, $iv) = explode('::', $encryptedValue, 2);
            $str = openssl_decrypt($encrypted_data, 'aes-256-cbc', $this->key, 0, $iv);
        }
        return trim($str);
    }

    /**
     * @return bool
     * @throws AException
     */
    private function isOpenSslSupported()
    {
        if (!function_exists('openssl_encrypt')) {
            $error_text = 'OpenSSL php-library did not load. '
                . 'It is recommended to enable PHP openssl for system to function properly.';
            $registry = Registry::getInstance();
            $log = $registry->get('log');
            if (!is_object($log) || !method_exists($log, 'write')) {
                $log = new ALog(DIR_SYSTEM . 'logs' . DS . 'error.txt');
                $registry->set('log', $log);
            }
            $log->write($error_text);
            return false;
        }
        return true;
    }

    /**
     * @deprecated
     * Deprecated!!! Do not use as this function will be removed in v2.0!!!!!
     * MD5-based encoding used for passwords, only used for older passwords in upgraded/migrated stores
    */
    static function getHash($keyword)
    {
        if (!defined('SALT')) {
            //backwards compatibility for extensions prior to 1.2.8
            return md5($keyword . 'SALT');
        } else {
            return md5($keyword . SALT);
        }
    }
}
