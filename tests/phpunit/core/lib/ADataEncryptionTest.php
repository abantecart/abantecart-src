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

use ACache;
use ADataEncryption;
use ADB;
use AException;
use ASSLEncryption;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use phpunit\AbanteCartTestBootstrap;

/**
 * Data encryption is configured by constants (DATA_ENCRYPTION_ENABLED etc.),
 * so every test runs in a separate process and defines them on its own.
 * Keys are passed via environment variables (see ASSLEncryption), so no key files are needed.
 *
 * @property ADB $db
 * @property ACache $cache
 */
#[RunTestsInSeparateProcesses]
class ADataEncryptionTest extends AbanteCartTestBootstrap
{
    private const KEY_NAME = 'phpunit_data_key';
    private const KEY_NAME_2 = 'phpunit_data_key2';

    /** @var object log collector */
    private $logSpy;
    private $keyIds = [];
    private $envVars = [];

    protected function setUp(): void
    {
        parent::bootstrap();

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
    }

    protected function tearDown(): void
    {
        foreach ($this->envVars as $name) {
            putenv($name);
        }
        if ($this->keyIds) {
            $this->db->query(
                "DELETE FROM " . $this->db->table('encryption_keys') . "
                WHERE key_id IN (" . implode(',', array_map('intval', $this->keyIds)) . ")"
            );
            $this->cache->remove('encryption');
        }
    }

    /**
     * Enables data encryption, registers two keys in the database and puts them into the environment
     */
    private function enableEncryption(): void
    {
        if (defined('DATA_ENCRYPTION_ENABLED') && !DATA_ENCRYPTION_ENABLED) {
            $this->markTestSkipped('Data encryption is disabled in the configuration');
        }
        if (ASSLEncryption::getKeysStorage() == ASSLEncryption::STORAGE_FILE) {
            $this->markTestSkipped('Test keys are passed via environment, but ENCRYPTION_KEYS_STORAGE is "file"');
        }
        if (!defined('DATA_ENCRYPTION_ENABLED')) {
            define('DATA_ENCRYPTION_ENABLED', true);
        }

        $table = $this->db->table('encryption_keys');
        $this->db->query(
            "DELETE FROM " . $table . "
            WHERE key_name IN ('" . self::KEY_NAME . "', '" . self::KEY_NAME_2 . "')"
        );
        $ssl = new ASSLEncryption();
        foreach ([self::KEY_NAME, self::KEY_NAME_2] as $keyName) {
            $keys = $ssl->generate_ssl_key_pair(
                ['private_key_type' => OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048]
            );
            foreach (['public', 'private'] as $type) {
                $var = ASSLEncryption::getEnvVarName($keyName, $type);
                putenv($var . '=' . base64_encode($keys[$type]));
                $this->envVars[] = $var;
            }
            $this->db->query(
                "INSERT INTO " . $table . " SET key_name = '" . $keyName . "', status = 1, comment = 'phpunit'"
            );
            $this->keyIds[$keyName] = (int)$this->db->getLastId();
        }
        $this->cache->remove('encryption');
    }

    private function customerRow(): array
    {
        return [
            'customer_id' => 999999,
            'firstname'   => 'John',
            'lastname'    => 'Smith',
            'email'       => 'john.smith@example.com',
            'telephone'   => '+1 555 0100',
            'fax'         => '+1 555 0101',
        ];
    }

    // ---------------------------------------------------------------
    // disabled encryption
    // ---------------------------------------------------------------

    public function testDisabledEncryptionReturnsDataAsIs()
    {
        if (defined('DATA_ENCRYPTION_ENABLED') && DATA_ENCRYPTION_ENABLED) {
            $this->markTestSkipped('Data encryption is enabled in the configuration');
        }
        if (!defined('DATA_ENCRYPTION_ENABLED')) {
            define('DATA_ENCRYPTION_ENABLED', false);
        }

        $dcrypt = new ADataEncryption();
        $row = $this->customerRow();

        $this->assertFalse($dcrypt->active);
        $this->assertSame($row, $dcrypt->encrypt_data($row, 'customers'));
        $this->assertSame($row, $dcrypt->decrypt_data($row, 'customers'));
        $this->assertSame('value', $dcrypt->encrypt_field('value'));
        $this->assertSame('value', $dcrypt->decrypt_field('value'));
        $this->assertSame('', $dcrypt->postfix('customers'));
    }

    // ---------------------------------------------------------------
    // tables and fields configuration
    // ---------------------------------------------------------------

    public function testEncryptedTablesConfiguration()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $this->assertTrue($dcrypt->active);
        $this->assertSame(['orders', 'customers', 'addresses'], $dcrypt->getEncryptedTables());
        $this->assertSame('order_id', $dcrypt->getEncryptedTableID('orders'));
        $this->assertSame('customer_id', $dcrypt->getEncryptedTableID('customers'));
        $this->assertSame('address_id', $dcrypt->getEncryptedTableID('addresses'));
        $this->assertSame('', $dcrypt->getEncryptedTableID('products'));

        $this->assertSame(['telephone', 'fax', 'email'], $dcrypt->getEncryptedFields('customers'));
        $this->assertSame([], $dcrypt->getEncryptedFields('products'));

        $postfix = defined('ENCRYPTED_POSTFIX') ? ENCRYPTED_POSTFIX : '_enc';
        $this->assertSame($postfix, $dcrypt->postfix('orders'));
        $this->assertSame('', $dcrypt->postfix('products'));
    }

    public function testAddEncryptedFieldsAppendsToList()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $dcrypt->addEncryptedFields('customers', ['loginname', 'email']);
        $this->assertSame(['telephone', 'fax', 'email', 'loginname'], $dcrypt->getEncryptedFields('customers'));

        $dcrypt->addEncryptedFields('customers', []);
        $this->assertCount(4, $dcrypt->getEncryptedFields('customers'));
    }

    public function testAddEncryptedTables()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $dcrypt->addEncryptedTables([
            'phpunit_table' => ['id' => 'row_id', 'fields' => ['secret']],
            'customers'     => ['id' => 'wrong_id', 'fields' => []],
        ]);

        $this->assertContains('phpunit_table', $dcrypt->getEncryptedTables());
        $this->assertSame('row_id', $dcrypt->getEncryptedTableID('phpunit_table'));
        $this->assertSame(['secret'], $dcrypt->getEncryptedFields('phpunit_table'));
        //existing table must not be overwritten
        $this->assertSame('customer_id', $dcrypt->getEncryptedTableID('customers'));
        $this->assertStringContainsString("Can't add existing table customers", implode("\n", $this->logSpy->lines));
    }

    public function testDeprecatedAliases()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $this->assertSame($dcrypt->getEncryptedTables(), $dcrypt->getEcryptedTables());
        $this->assertSame($dcrypt->getEncryptedTableID('orders'), $dcrypt->getEcryptedTableID('orders'));
        $this->assertSame($dcrypt->getEncryptedFields('orders'), $dcrypt->getEcryptedFields('orders'));
        $dcrypt->addEcryptedTables(['phpunit_table' => ['id' => 'row_id', 'fields' => ['secret']]]);
        $this->assertSame('row_id', $dcrypt->getEncryptedTableID('phpunit_table'));
    }

    // ---------------------------------------------------------------
    // rows encryption
    // ---------------------------------------------------------------

    public function testEncryptDecryptDataRoundTrip()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);
        $row = $this->customerRow();

        $encrypted = $dcrypt->encrypt_data($row, 'customers');

        $this->assertSame($this->keyIds[self::KEY_NAME], $encrypted['key_id']);
        foreach (['email', 'telephone', 'fax'] as $field) {
            $this->assertNotSame('', $encrypted[$field]);
            $this->assertNotSame($row[$field], $encrypted[$field]);
        }
        foreach (['customer_id', 'firstname', 'lastname'] as $field) {
            $this->assertSame($row[$field], $encrypted[$field]);
        }

        //key_id from database comes as a string
        $encrypted['key_id'] = (string)$encrypted['key_id'];
        $decrypted = $dcrypt->decrypt_data($encrypted, 'customers');
        unset($decrypted['key_id']);
        $this->assertSame($row, $decrypted);
    }

    public function testRowIsDecryptedWithItsOwnKey()
    {
        $this->enableEncryption();
        $row = $this->customerRow();
        $row['key_id'] = $this->keyIds[self::KEY_NAME_2];

        //instance with another default key must use key_id of the row
        $dcrypt = new ADataEncryption(self::KEY_NAME);
        $encrypted = $dcrypt->encrypt_data($row, 'customers');
        $this->assertSame($this->keyIds[self::KEY_NAME_2], $encrypted['key_id']);

        $this->assertSame($row['email'], $dcrypt->decrypt_data($encrypted, 'customers')['email']);

        //without key_id the default key is used, so the value cannot be decrypted
        //(wrong RSA key gives random bytes because of the OpenSSL implicit rejection, not '*****')
        unset($encrypted['key_id']);
        $this->assertNotSame($row['email'], $dcrypt->decrypt_data($encrypted, 'customers')['email']);
    }

    public function testDecryptEmptyRow()
    {
        //e.g. $query->row of the query without result
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $this->assertSame([], $dcrypt->decrypt_data([], 'orders'));
        $this->assertSame(['key_id' => null], $dcrypt->decrypt_data(['key_id' => null], 'orders'));
    }

    public function testEmptyTableName()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $this->assertSame([], $dcrypt->encrypt_data($this->customerRow(), ''));
        $this->assertSame([], $dcrypt->decrypt_data($this->customerRow(), ''));
    }

    public function testEncryptDataOfSingleColumn()
    {
        //see ADataset/AData import - row without key_id
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $encrypted = $dcrypt->encrypt_data(['email' => 'a@b.c'], 'customers');
        $this->assertSame($this->keyIds[self::KEY_NAME], $encrypted['key_id']);
        $this->assertSame('a@b.c', $dcrypt->decrypt_data($encrypted, 'customers')['email']);
    }

    // ---------------------------------------------------------------
    // fields encryption
    // ---------------------------------------------------------------

    public function testEncryptDecryptField()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $encrypted = $dcrypt->encrypt_field('john.smith@example.com');
        $this->assertNotSame('john.smith@example.com', $encrypted);
        $this->assertSame('john.smith@example.com', $dcrypt->decrypt_field($encrypted));

        $keyId = $this->keyIds[self::KEY_NAME_2];
        $encrypted = $dcrypt->encrypt_field('+1 555 0100', $keyId);
        $this->assertSame('+1 555 0100', $dcrypt->decrypt_field($encrypted, $keyId));
        $this->assertNotSame('+1 555 0100', $dcrypt->decrypt_field($encrypted));
    }

    public function testUnknownKeyIdOnEncryptThrowsException()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $this->expectException(AException::class);
        $dcrypt->encrypt_field('value', 987654);
    }

    public function testUnknownKeyIdOnDecryptThrowsException()
    {
        $this->enableEncryption();
        $dcrypt = new ADataEncryption(self::KEY_NAME);

        $this->expectException(AException::class);
        $dcrypt->decrypt_data(['key_id' => 987654, 'email' => 'xxx'], 'customers');
    }

    public function testMissingDefaultKey()
    {
        if (defined('DATA_ENCRYPTION_KEYPAIR')) {
            $this->markTestSkipped('DATA_ENCRYPTION_KEYPAIR is defined in the configuration');
        }
        $this->enableEncryption();

        //must not fail on construct
        $dcrypt = new ADataEncryption();
        $this->assertTrue($dcrypt->active);

        //decryption reports the problem, but does not break the page
        $this->assertSame('*****', $dcrypt->decrypt_field(base64_encode('anything')));
        $this->assertStringContainsString('Can not locate default key', implode("\n", $this->logSpy->lines));

        //encryption must not save the data unencrypted
        $this->expectException(AException::class);
        $dcrypt->encrypt_field('value');
    }

    public function testDefaultKeyFromConfiguration()
    {
        if (defined('DATA_ENCRYPTION_KEYPAIR')) {
            $this->markTestSkipped('DATA_ENCRYPTION_KEYPAIR is defined in the configuration');
        }
        $this->enableEncryption();
        define('DATA_ENCRYPTION_KEYPAIR', self::KEY_NAME);

        $dcrypt = new ADataEncryption();
        $encrypted = $dcrypt->encrypt_data($this->customerRow(), 'customers');
        $this->assertSame($this->keyIds[self::KEY_NAME], $encrypted['key_id']);
        $this->assertSame('john.smith@example.com', $dcrypt->decrypt_data($encrypted, 'customers')['email']);
    }
}
