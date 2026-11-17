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

// Manual Configuration is required
/*

NOTE: Do not confuse SSL data encryption with signed SSL certificates (HTTPS) used for browser access to sites

Configuration:
Add a key storage location path.
Add the below lines to the /system / config.php file. Change the path to your specific path on your server
define('ENCRYPTION_KEYS_DIR', '/path/to/keys/');

Keys storage.
Keys can be stored as files in ENCRYPTION_KEYS_DIR or in the environment variables (e.g. written on deploy).
Optional storage mode in the /system/config.php:
define('ENCRYPTION_KEYS_STORAGE', 'auto'); // 'auto' (default) - env first, then file; 'env' - env only; 'file' - files only

Environment variables names: AC_ENC_KEY_<KEY_NAME>_PUB and AC_ENC_KEY_<KEY_NAME>_PRV,
where <KEY_NAME> is the key name in upper case with non-alphanumeric characters replaced by "_".
Example for the key "data_enc_key": AC_ENC_KEY_DATA_ENC_KEY_PUB, AC_ENC_KEY_DATA_ENC_KEY_PRV
Value: PEM-key as is (multiline or with "\n") or base64-encoded PEM (single line), e.g.
AC_ENC_KEY_DATA_ENC_KEY_PRV="$(base64 -w0 data_enc_key.prv)"
NOTE: for PHP-FPM the variables must be passed to the pool (clear_env = no or env[...] directives).

NOTES:
1. Keep Key in a secure location with restricted file permissions for root and apache (webserver)
2. There is no key expiration management.
These need to be accounted for in key management procedures


Examples:

1. Generate Keys

$password = "123456";
$conf = array (
'digest_alg'       => 'sha512',
'private_key_type' => OPENSSL_KEYTYPE_RSA,
'private_key_bits' => 2048,
'encrypt_key'      => true
);
$enc = new ASSLEncryption ();
$keys = $enc->generate_ssl_key_pair($conf, $password);
$enc->save_ssl_key_pair($keys, 'key_with_pass');
echo_array($keys);

2. Encrypt

$enc = new ASSLEncryption ('key_with_pass');
$enc_str = $enc->encrypt('test text');
echo $enc_str;

3. Decrypt

$enc = new ASSLEncryption ('', 'key_with_pass', $password);
echo $enc->decrypt($enc_str);


Need help configuring, supporting or extending functionality,
contact www.abantecart.com for forum or paid support
*/

final class ASSLEncryption
{
    const STORAGE_AUTO = 'auto';
    const STORAGE_ENV = 'env';
    const STORAGE_FILE = 'file';
    const ENV_PREFIX = 'AC_ENC_KEY_';

    /** @var Registry */
    private $registry;
    /** @var ALog */
    private $log;
    /** @var AMessage */
    private $message;
    private $pubKey;
    private $prkey;
    private $key_path;
    private $failed_str = "*****";
    public $active = false;

    //To generate new keys, a class can be initiated with no data passed
    function __construct($pubKeyName = '', $prKeyName = '', $passphrase = null)
    {
        $this->registry = Registry::getInstance();
        $this->log = $this->registry->get('log');
        $this->message = $this->registry->get('messages');

        //Validate if SSL PHP support is installed
        if (!function_exists('openssl_pkey_get_public')) {
            $error = "Error: PHP OpenSSL is not available on your server! "
                . "Check if OpenSSL installed for PHP and enabled";
            $this->log->write($error);
            $this->message->saveError('OpenSSL Error', $error);
            return null;
        }

        //construct key storage path
        //NOTE: ENCRYPTION_KEYS_DIR needs to be added into configuration file
        //Suggested: Directory to be secured for read and writing ONLY for users root and Apache (web server).
        if (defined('ENCRYPTION_KEYS_DIR')) {
            $this->key_path = ENCRYPTION_KEYS_DIR;
        } else {
            $this->key_path = DIR_SYSTEM . 'keys' . DS;
        }

        if ($pubKeyName) {
            $this->pubKey = $this->getPublicKey($pubKeyName . '.pub');
        }
        if ($prKeyName) {
            $this->loadPrivateKey($prKeyName . '.prv', $passphrase);
        }
        $this->active = true;
    }

    /**
     * Generate a new Key Private/Public keys pair.
     * Input is $config array with standard openssl_csr_new config args
     * $passphrase is set if you want to have a passphrase to access a private key
     *
     * @param array $config
     * @param null|string $passphrase
     *
     * @return array
     */
    public function generate_ssl_key_pair($config = [], $passphrase = null)
    {
        $default_length = 2048;

        if (!isset($config['private_key_bits'])) {
            $config['private_key_bits'] = $default_length;
        }
        //Set key bits limits
        if ($config['private_key_bits'] < 256) {
            $config['private_key_bits'] = 256;
        } else if ($config['private_key_bits'] > 8192) {
            $config['private_key_bits'] = 8192;
        }

        $config['private_key_type'] = (int) $config['private_key_type'];

        $res = openssl_pkey_new($config);

        //# Do we need to use a passphrase for the key?
        $privateKey = '';
        if (isset($config['encrypt_key']) && $config['encrypt_key']) {
            openssl_pkey_export($res, $privateKey, $passphrase);
        } else {
            openssl_pkey_export($res, $privateKey);
        }

        $publicKey = openssl_pkey_get_details($res);
        $publicKey = $publicKey["key"];

        return [
            'public'  => $publicKey,
            'private' => $privateKey,
        ];
    }

    /**
     * Save Private/Public keys pair to set key_path location
     * Input: Private/Public keys pair array
     *
     * @param array $keys
     * @param string $key_name
     *
     * @return string
     * @throws AException
     */
    public function save_ssl_key_pair($keys = [], $key_name = '')
    {
        if (!file_exists($this->key_path)) {
            $result = mkdir($this->key_path, 0700, true); // create dir with nested folders
        } else {
            $result = true;
        }
        if (!$result) {
            $error = "Error: Can't create directory " . $this->key_path . " for saving SSL keys!";
            $this->log->write($error);
            $this->message->saveError('Create SSL Key Error', $error);
            return $error;
        }
        if (empty($key_name)) {
            $key_name = 'default_key';
        }

        foreach ($keys as $type => $key) {
            $ext = '';
            if ($type == 'private') {
                $ext = '.prv';
            } else {
                if ($type == 'public') {
                    $ext = '.pub';
                }
            }
            $file = $this->key_path . '/' . $key_name . $ext;
            if (file_exists($file)) {
                $error = "Error: Can't create key " . $key_name . "! Already Exists";
                $this->log->write($error);
                $this->message->saveError('Create SSL Key Error', $error);
                return $error;
            }
            $handle = fopen($file, 'w');
            fwrite($handle, $key . "\n");
            fclose($handle);
        }
        return '';
    }

    /**
     * Get a public key based on the key name provided. It is loaded if not yet loaded
     * Key's are stored in the path based on the configuration
     *
     * @param string $key_name
     *
     * @return false|OpenSSLAsymmetricKey
     */
    public function getPublicKey($key_name)
    {
        if (empty($this->pubKey)) {
            $source = $this->getKeySource((string)$key_name, 'public');
            $this->pubKey = $source ? openssl_pkey_get_public($source) : false;
            if (!$this->pubKey) {
                $this->log->write(
                    'Error: SSL public key "' . $key_name . '" cannot be loaded '
                    . '(storage: ' . self::getKeysStorage() . ', env variable: '
                    . self::getEnvVarName(self::stripKeyExtension((string)$key_name), 'public') . ')'
                );
            }
        }
        return $this->pubKey;
    }

    /**
     * Load a private key based on the key name provided.
     * Input: Key name and passphrase (if used)
     * Key's are stored in the path based on the configuration
     * NOTE: Private key value never returned
     *
     * @param string $key_name
     * @param string $passphrase
     *
     * @return bool
     */
    public function loadPrivateKey($key_name, $passphrase = '')
    {
        $source = $this->getKeySource((string)$key_name, 'private');
        $this->prkey = $source ? openssl_pkey_get_private($source, $passphrase) : false;
        if ($this->prkey) {
            return true;
        }
        $this->log->write(
            'Error: SSL private key "' . $key_name . '" cannot be loaded '
            . '(storage: ' . self::getKeysStorage() . ', env variable: '
            . self::getEnvVarName(self::stripKeyExtension((string)$key_name), 'private') . ')'
        );
        return false;
    }

    /**
     * Returns the configured keys storage mode:
     *  'auto' - environment variable first, then file (default)
     *  'env'  - environment variables only
     *  'file' - files in ENCRYPTION_KEYS_DIR only
     *
     * @return string
     */
    public static function getKeysStorage()
    {
        $storage = defined('ENCRYPTION_KEYS_STORAGE')
            ? strtolower((string)constant('ENCRYPTION_KEYS_STORAGE'))
            : self::STORAGE_AUTO;
        return in_array($storage, [self::STORAGE_AUTO, self::STORAGE_ENV, self::STORAGE_FILE], true)
            ? $storage
            : self::STORAGE_AUTO;
    }

    /**
     * Name of the environment variable holding the key.
     * Example: key "data_enc_key" => AC_ENC_KEY_DATA_ENC_KEY_PUB / AC_ENC_KEY_DATA_ENC_KEY_PRV
     *
     * @param string $keyName key name without extension
     * @param string $type 'public' or 'private'
     *
     * @return string
     */
    public static function getEnvVarName(string $keyName, string $type)
    {
        $name = strtoupper(trim((string)preg_replace('/[^A-Za-z0-9]+/', '_', $keyName), '_'));
        return self::ENV_PREFIX . $name . ($type == 'private' ? '_PRV' : '_PUB');
    }

    /**
     * Returns a value for openssl_pkey_get_* functions:
     * PEM-string from the environment or "file://" path.
     * Null if the key is not available in allowed storages.
     *
     * @param string $fileName key name with extension (.pub or .prv)
     * @param string $type 'public' or 'private'
     *
     * @return string|null
     */
    private function getKeySource(string $fileName, string $type)
    {
        $storage = self::getKeysStorage();
        if ($storage != self::STORAGE_FILE) {
            $pem = $this->getEnvKey(self::stripKeyExtension($fileName), $type);
            if ($pem !== null) {
                return $pem;
            }
        }
        if ($storage != self::STORAGE_ENV) {
            $file = $this->key_path . $fileName;
            if (is_file($file) && is_readable($file)) {
                return 'file://' . $file;
            }
        }
        return null;
    }

    /**
     * Reads PEM-key from the environment.
     * Value can be a raw PEM (with real line breaks or with literal "\n")
     * or base64-encoded PEM (handy for single-line .env files and CI secrets)
     *
     * @param string $keyName
     * @param string $type
     *
     * @return string|null
     */
    private function getEnvKey(string $keyName, string $type)
    {
        $varName = self::getEnvVarName($keyName, $type);
        $value = getenv($varName);
        if ($value === false || $value === '') {
            $value = $_ENV[$varName] ?? $_SERVER[$varName] ?? '';
        }
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (!str_contains($value, '-----BEGIN')) {
            $decoded = base64_decode($value, true);
            if ($decoded === false || !str_contains($decoded, '-----BEGIN')) {
                //never write the value itself into the log
                $this->log->write(
                    'Error: environment variable ' . $varName . ' does not contain a valid PEM or base64-encoded PEM key.'
                );
                return null;
            }
            $value = trim($decoded);
        }
        //support of single-line values with escaped line breaks
        return str_replace(['\r\n', '\n'], "\n", $value) . "\n";
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private static function stripKeyExtension(string $fileName)
    {
        return (string)preg_replace('/\.(pub|prv)$/', '', $fileName);
    }

    /**
     * Decrypt value based on private key ONLY
     *
     * @param string $encryptedText
     *
     * @return string
     */
    public function decrypt($encryptedText)
    {
        if (empty($encryptedText)) {
            return '';
        }
        //check if encryption is off or this is not an encrypted string
        if (!$this->active || !base64_decode($encryptedText, true)) {
            return $encryptedText;
        }

        $cleartext = '';
        if (empty($this->prkey)) {
            $error = "Error: SSL Decryption failed! Missing private key";
            $this->log->write($error);
            return $this->failed_str;
        }

        if ((openssl_private_decrypt(base64_decode($encryptedText), $cleartext, $this->prkey)) === true) {
            return $cleartext;
        } else {
            $error = "Error: SSL Decryption based on private key has failed! "
                . "Possibly corrupted encrypted data or wrong key!";
            $this->log->write($error);
            return $this->failed_str;
        }
    }

    /**
     * Encrypt value based on public key ONLY
     *
     * @param string $cleartext
     *
     * @return string
     */
    public function encrypt($cleartext)
    {
        if (empty($cleartext)) {
            return '';
        }
        //check if encryption is off or this is not an encrypted string
        if (!$this->active) {
            return $cleartext;
        }

        $encryptedText = '';
        if (empty($this->pubKey)) {
            $error = "Error: SSL Encryption failed! Missing public key";
            $this->log->write($error);
            return '';
        }

        if ((openssl_public_encrypt($cleartext, $encryptedText, $this->pubKey)) === true) {
            return base64_encode($encryptedText);
        } else {
            $error = "Error: SSL Encryption based on public key has failed! Possibly encryption error or wrong key!";
            $this->log->write($error);
            return '';
        }
    }

    public function getKeyPath()
    {
        return $this->key_path;
    }

}
