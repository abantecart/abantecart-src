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

namespace lib;

use ASSLEncryption;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use phpunit\AbanteCartTestBootstrap;
use ReflectionProperty;

class ASSLEncryptionTest extends AbanteCartTestBootstrap
{
    /** @var array{public:string, private:string}|null */
    private static $keyPair;
    /** @var array{public:string, private:string}|null */
    private static $otherKeyPair;

    /** @var object log collector */
    private $logSpy;
    private $originalLog;
    private $originalMessages;
    private $tmpDir;
    private $envVars = [];

    protected function setUp(): void
    {
        parent::bootstrap();

        $this->originalLog = $this->registry->get('log');
        $this->originalMessages = $this->registry->get('messages');
        $this->logSpy = new class {
            public array $lines = [];

            public function write($message)
            {
                $this->lines[] = (string)$message;
            }
        };
        $this->registry->set('log', $this->logSpy);
        //do not pollute admin messages
        $this->registry->set('messages', new class {
            public function saveError(...$args) { }
        });

        $this->tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'abc_phpunit_keys_' . uniqid() . DIRECTORY_SEPARATOR;

        if (!self::$keyPair) {
            self::$keyPair = $this->generateKeys();
            self::$otherKeyPair = $this->generateKeys();
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->envVars as $name) {
            putenv($name);
            unset($_ENV[$name], $_SERVER[$name]);
        }
        $this->envVars = [];

        if (is_dir($this->tmpDir)) {
            array_map('unlink', glob($this->tmpDir . '*'));
            rmdir($this->tmpDir);
        }

        $this->registry->set('log', $this->originalLog);
        $this->registry->set('messages', $this->originalMessages);
    }

    private function generateKeys(?string $passphrase = null): array
    {
        $config = [
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ];
        if ($passphrase) {
            $config['encrypt_key'] = true;
        }
        return (new ASSLEncryption())->generate_ssl_key_pair($config, $passphrase);
    }

    private function setEnv(string $name, string $value): void
    {
        putenv($name . '=' . $value);
        $this->envVars[] = $name;
    }

    private function setEnvKeys(string $keyName, array $keys, callable $encode = null): void
    {
        $encode = $encode ?: fn($v) => $v;
        $this->setEnv(ASSLEncryption::getEnvVarName($keyName, 'public'), $encode($keys['public']));
        $this->setEnv(ASSLEncryption::getEnvVarName($keyName, 'private'), $encode($keys['private']));
    }

    /**
     * Creates instance which reads key files from the temporary directory
     */
    private function getFileBasedInstance(string $keyName, ?string $passphrase = null): ASSLEncryption
    {
        $enc = new ASSLEncryption();
        (new ReflectionProperty(ASSLEncryption::class, 'key_path'))->setValue($enc, $this->tmpDir);
        $enc->getPublicKey($keyName . '.pub');
        $enc->loadPrivateKey($keyName . '.prv', $passphrase);
        return $enc;
    }

    private function saveKeyFiles(string $keyName, array $keys): void
    {
        $enc = new ASSLEncryption();
        (new ReflectionProperty(ASSLEncryption::class, 'key_path'))->setValue($enc, $this->tmpDir);
        $this->assertSame('', $enc->save_ssl_key_pair($keys, $keyName));
    }

    private function assertRoundTrip(ASSLEncryption $enc, string $value = 'secret@example.com'): void
    {
        $encrypted = $enc->encrypt($value);
        $this->assertNotSame('', $encrypted, 'Encryption failed: ' . implode('; ', $this->logSpy->lines));
        $this->assertNotSame($value, $encrypted);
        $this->assertSame($value, $enc->decrypt($encrypted));
    }

    private function skipIfStorageIs(string $storage): void
    {
        if (ASSLEncryption::getKeysStorage() == $storage) {
            $this->markTestSkipped('ENCRYPTION_KEYS_STORAGE is set to "' . $storage . '" in the configuration');
        }
    }

    // ---------------------------------------------------------------
    // keys generation and saving
    // ---------------------------------------------------------------

    public function testGenerateKeyPair()
    {
        $this->assertStringContainsString('-----BEGIN PUBLIC KEY-----', self::$keyPair['public']);
        $this->assertStringContainsString('PRIVATE KEY-----', self::$keyPair['private']);
    }

    public function testGenerateKeyPairWithPassphrase()
    {
        $keys = $this->generateKeys('phpunit-pass');
        $this->assertStringContainsString('ENCRYPTED', $keys['private']);
        $this->assertNotFalse(openssl_pkey_get_private($keys['private'], 'phpunit-pass'));
    }

    public function testSaveKeyPairCreatesFiles()
    {
        $this->saveKeyFiles('phpunit_saved', self::$keyPair);
        $this->assertFileExists($this->tmpDir . 'phpunit_saved.pub');
        $this->assertFileExists($this->tmpDir . 'phpunit_saved.prv');
    }

    public function testSaveKeyPairDoesNotOverwriteExistingKey()
    {
        $this->saveKeyFiles('phpunit_saved', self::$keyPair);

        $enc = new ASSLEncryption();
        (new ReflectionProperty(ASSLEncryption::class, 'key_path'))->setValue($enc, $this->tmpDir);
        $this->assertNotSame('', $enc->save_ssl_key_pair(self::$otherKeyPair, 'phpunit_saved'));
        $this->assertSame(self::$keyPair['public'] . "\n", file_get_contents($this->tmpDir . 'phpunit_saved.pub'));
    }

    // ---------------------------------------------------------------
    // file storage
    // ---------------------------------------------------------------

    public function testRoundTripWithFileKeys()
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_ENV);
        $this->saveKeyFiles('phpunit_file_key', self::$keyPair);
        $this->assertRoundTrip($this->getFileBasedInstance('phpunit_file_key'));
    }

    public function testRoundTripWithPassphraseProtectedFileKey()
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_ENV);
        $this->saveKeyFiles('phpunit_pass_key', $this->generateKeys('phpunit-pass'));
        $this->assertRoundTrip($this->getFileBasedInstance('phpunit_pass_key', 'phpunit-pass'));
    }

    public function testWrongPassphraseFailsToLoadPrivateKey()
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_ENV);
        $this->saveKeyFiles('phpunit_pass_key', $this->generateKeys('phpunit-pass'));
        $enc = $this->getFileBasedInstance('phpunit_pass_key', 'wrong-pass');

        $this->assertFalse($enc->loadPrivateKey('phpunit_pass_key.prv', 'wrong-pass'));
        $this->assertSame('*****', $enc->decrypt($enc->encrypt('secret')));
    }

    public function testMissingKeyFileIsLogged()
    {
        $enc = $this->getFileBasedInstance('phpunit_absent_key');

        $this->assertFalse($enc->getPublicKey('phpunit_absent_key.pub'));
        $this->assertFalse($enc->loadPrivateKey('phpunit_absent_key.prv'));
        $log = implode("\n", $this->logSpy->lines);
        $this->assertStringContainsString('phpunit_absent_key.pub', $log);
        $this->assertStringContainsString('AC_ENC_KEY_PHPUNIT_ABSENT_KEY_PRV', $log);
    }

    // ---------------------------------------------------------------
    // environment storage
    // ---------------------------------------------------------------

    public static function envEncodersProvider(): array
    {
        return [
            'raw multiline PEM'       => [fn($pem) => $pem],
            'base64 PEM'              => [fn($pem) => base64_encode($pem)],
            'PEM with escaped breaks' => [fn($pem) => str_replace("\n", '\n', trim($pem))],
            'PEM with spaces around'  => [fn($pem) => "  \n" . $pem . "\n  "],
        ];
    }

    #[DataProvider('envEncodersProvider')]
    public function testRoundTripWithEnvKeys(callable $encode)
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_FILE);
        $this->setEnvKeys('phpunit_env_key', self::$keyPair, $encode);

        $this->assertRoundTrip(new ASSLEncryption('phpunit_env_key', 'phpunit_env_key'));
        $this->assertSame([], $this->logSpy->lines);
    }

    public function testEnvKeysCanBeReadFromServerArray()
    {
        //e.g. variables passed by fastcgi_param
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_FILE);
        foreach (['public', 'private'] as $type) {
            $name = ASSLEncryption::getEnvVarName('phpunit_server_key', $type);
            $_SERVER[$name] = base64_encode(self::$keyPair[$type]);
            $this->envVars[] = $name;
        }
        $this->assertRoundTrip(new ASSLEncryption('phpunit_server_key', 'phpunit_server_key'));
    }

    public function testEncryptWithEnvPublicKeyAndDecryptWithFilePrivateKey()
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_FILE);
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_ENV);
        $this->saveKeyFiles('phpunit_mixed_key', self::$keyPair);
        $this->setEnv(
            ASSLEncryption::getEnvVarName('phpunit_mixed_key', 'public'),
            base64_encode(self::$keyPair['public'])
        );
        $this->assertRoundTrip($this->getFileBasedInstance('phpunit_mixed_key'));
    }

    public function testEnvKeyHasPriorityOverFileInAutoMode()
    {
        if (ASSLEncryption::getKeysStorage() != ASSLEncryption::STORAGE_AUTO) {
            $this->markTestSkipped('Only for "auto" keys storage');
        }
        //files contain one pair, env - another one
        $this->saveKeyFiles('phpunit_prio_key', self::$keyPair);
        $this->setEnvKeys('phpunit_prio_key', self::$otherKeyPair, 'base64_encode');

        $enc = $this->getFileBasedInstance('phpunit_prio_key');
        $encrypted = $enc->encrypt('secret');
        $this->assertSame('secret', $enc->decrypt($encrypted));

        openssl_private_decrypt(base64_decode($encrypted), $clear, self::$otherKeyPair['private']);
        $this->assertSame('secret', $clear, 'Value must be encrypted with the key from the environment');
    }

    public function testInvalidEnvValueFallsBackToFileInAutoMode()
    {
        if (ASSLEncryption::getKeysStorage() != ASSLEncryption::STORAGE_AUTO) {
            $this->markTestSkipped('Only for "auto" keys storage');
        }
        $this->saveKeyFiles('phpunit_broken_env', self::$keyPair);
        $this->setEnv(ASSLEncryption::getEnvVarName('phpunit_broken_env', 'public'), 'not a key at all');
        $this->setEnv(ASSLEncryption::getEnvVarName('phpunit_broken_env', 'private'), 'not a key at all');

        $this->assertRoundTrip($this->getFileBasedInstance('phpunit_broken_env'));
        $log = implode("\n", $this->logSpy->lines);
        $this->assertStringContainsString('AC_ENC_KEY_PHPUNIT_BROKEN_ENV_PUB', $log);
        $this->assertStringNotContainsString('not a key at all', $log, 'Env value must not be logged');
    }

    public function testPrivateKeyIsNotWrittenToLog()
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_FILE);
        //PEM with corrupted body
        $lines = explode("\n", self::$keyPair['private']);
        $lines[1] = strrev($lines[1]);
        $this->setEnv(ASSLEncryption::getEnvVarName('phpunit_leak_key', 'private'), implode("\n", $lines));

        $enc = new ASSLEncryption('', 'phpunit_leak_key');
        $this->assertSame('*****', $enc->decrypt(base64_encode('anything')));
        $log = implode("\n", $this->logSpy->lines);
        $this->assertStringContainsString('AC_ENC_KEY_PHPUNIT_LEAK_KEY_PRV', $log);
        $this->assertStringNotContainsString($lines[1], $log);
        $this->assertStringNotContainsString($lines[2], $log);
    }

    // ---------------------------------------------------------------
    // storage modes (constants can be defined once per process)
    // ---------------------------------------------------------------

    #[RunInSeparateProcess]
    public function testStorageModeEnvIgnoresFiles()
    {
        $this->defineStorage(ASSLEncryption::STORAGE_ENV);
        $this->saveKeyFiles('phpunit_file_only', self::$keyPair);

        $enc = $this->getFileBasedInstance('phpunit_file_only');
        $this->assertSame('', $enc->encrypt('secret'));

        $this->setEnvKeys('phpunit_env_only', self::$keyPair, 'base64_encode');
        $this->assertRoundTrip(new ASSLEncryption('phpunit_env_only', 'phpunit_env_only'));
    }

    #[RunInSeparateProcess]
    public function testStorageModeFileIgnoresEnv()
    {
        $this->defineStorage(ASSLEncryption::STORAGE_FILE);
        $this->setEnvKeys('phpunit_env_only', self::$keyPair, 'base64_encode');

        $enc = new ASSLEncryption('phpunit_env_only', 'phpunit_env_only');
        $this->assertFalse($enc->loadPrivateKey('phpunit_env_only.prv'));
        $this->assertSame('', $enc->encrypt('secret'));

        $this->saveKeyFiles('phpunit_file_only', self::$keyPair);
        $this->assertRoundTrip($this->getFileBasedInstance('phpunit_file_only'));
    }

    #[RunInSeparateProcess]
    public function testUnknownStorageModeFallsBackToAuto()
    {
        $this->defineStorage('something-wrong');
        $this->assertSame(ASSLEncryption::STORAGE_AUTO, ASSLEncryption::getKeysStorage());
    }

    #[RunInSeparateProcess]
    public function testStorageModeIsCaseInsensitive()
    {
        $this->defineStorage('ENV');
        $this->assertSame(ASSLEncryption::STORAGE_ENV, ASSLEncryption::getKeysStorage());
    }

    private function defineStorage(string $storage): void
    {
        if (defined('ENCRYPTION_KEYS_STORAGE')) {
            $this->markTestSkipped('ENCRYPTION_KEYS_STORAGE is already defined in the configuration');
        }
        define('ENCRYPTION_KEYS_STORAGE', $storage);
    }

    // ---------------------------------------------------------------
    // helpers and edge cases
    // ---------------------------------------------------------------

    public static function envVarNamesProvider(): array
    {
        return [
            ['data_enc_key', 'public', 'AC_ENC_KEY_DATA_ENC_KEY_PUB'],
            ['data_enc_key', 'private', 'AC_ENC_KEY_DATA_ENC_KEY_PRV'],
            ['data-enc.key', 'public', 'AC_ENC_KEY_DATA_ENC_KEY_PUB'],
            ['MyKey2', 'private', 'AC_ENC_KEY_MYKEY2_PRV'],
            ['__key__', 'public', 'AC_ENC_KEY_KEY_PUB'],
        ];
    }

    #[DataProvider('envVarNamesProvider')]
    public function testGetEnvVarName(string $keyName, string $type, string $expected)
    {
        $this->assertSame($expected, ASSLEncryption::getEnvVarName($keyName, $type));
    }

    public function testDefaultStorageIsAuto()
    {
        if (defined('ENCRYPTION_KEYS_STORAGE')) {
            $this->markTestSkipped('ENCRYPTION_KEYS_STORAGE is defined in the configuration');
        }
        $this->assertSame(ASSLEncryption::STORAGE_AUTO, ASSLEncryption::getKeysStorage());
    }

    public function testEmptyValues()
    {
        $enc = new ASSLEncryption();
        $this->assertSame('', $enc->encrypt(''));
        $this->assertSame('', $enc->decrypt(''));
    }

    public function testDecryptReturnsNotEncryptedValueAsIs()
    {
        $enc = new ASSLEncryption();
        $this->assertSame('plain text!', $enc->decrypt('plain text!'));
    }

    public function testEncryptWithoutPublicKey()
    {
        $this->assertSame('', (new ASSLEncryption())->encrypt('secret'));
        $this->assertStringContainsString('Missing public key', implode("\n", $this->logSpy->lines));
    }

    public function testDecryptWithoutPrivateKey()
    {
        $this->assertSame('*****', (new ASSLEncryption())->decrypt(base64_encode('anything')));
        $this->assertStringContainsString('Missing private key', implode("\n", $this->logSpy->lines));
    }

    public function testDecryptWithWrongPrivateKey()
    {
        $this->skipIfStorageIs(ASSLEncryption::STORAGE_FILE);
        $this->setEnv(ASSLEncryption::getEnvVarName('phpunit_k1', 'public'), self::$keyPair['public']);
        $this->setEnv(ASSLEncryption::getEnvVarName('phpunit_k2', 'private'), self::$otherKeyPair['private']);

        $encrypted = (new ASSLEncryption('phpunit_k1'))->encrypt('secret');
        //NOTE: with PKCS#1 v1.5 padding OpenSSL uses "implicit rejection" (Marvin attack mitigation):
        //decryption with a wrong key returns random bytes instead of failure, so it is not '*****'
        $this->assertNotSame('secret', (new ASSLEncryption('', 'phpunit_k2'))->decrypt($encrypted));
    }
}
