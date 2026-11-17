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

namespace model;

use ACache;
use ADataEncryption;
use ADB;
use ALoader;
use ModelSaleCustomer;
use phpunit\AbanteCartTestBootstrap;
use Registry;

/**
 * Admin model of customers with enabled data encryption (uses keys configured in the project).
 * Test customers have login name started with "phpunit_" and are removed after each test.
 *
 * @property ADB $db
 * @property ALoader $load
 * @property ACache $cache
 * @property ADataEncryption $dcrypt
 */
class SaleCustomerTest extends AbanteCartTestBootstrap
{
    private const PREFIX = 'phpunit_';

    /** @var ModelSaleCustomer */
    private $model;
    private array $customerIds = [];

    protected function setUp(): void
    {
        // constants can be defined only once per process
        if (!defined('ABC_TEST_ROOT_PATH')) {
            parent::bootstrap();
        } else {
            $this->registry = Registry::getInstance();
        }
        if (!$this->dcrypt->active) {
            $this->markTestSkipped('Data encryption is disabled in the configuration');
        }
        //customers list is filtered by the current store
        $this->registry->get('session')->data['current_store_id'] = 0;
        $this->model = $this->load->model('sale/customer');
        $this->cleanUp();
    }

    protected function tearDown(): void
    {
        $this->cleanUp();
    }

    private function cleanUp(): void
    {
        $ids = $this->customerIds;
        $result = $this->db->query(
            "SELECT customer_id FROM " . $this->db->table('customers') . "
            WHERE loginname LIKE '" . self::PREFIX . "%'"
        );
        $ids = array_unique(array_merge($ids, array_column($result->rows, 'customer_id')));
        foreach ($ids as $id) {
            $this->model->deleteCustomer((int) $id);
        }
        $this->customerIds = [];
        $this->cache->remove('customers');
    }

    private function customerData(array $data = []): array
    {
        $uid = uniqid();
        return array_merge(
            [
                'store_id'          => 0,
                'loginname'         => self::PREFIX . $uid,
                'firstname'         => 'Phpunit',
                'lastname'          => 'Customer',
                'email'             => self::PREFIX . $uid . '@example.com',
                'telephone'         => '+1 555 ' . substr($uid, -6),
                'fax'               => '+1 555 0101',
                'sms'               => '+1777' . substr($uid, -6),
                'newsletter'        => 0,
                'customer_group_id' => (int) $this->config->get('config_customer_group_id'),
                'password'          => 'Phpunit-pass-1',
                'status'            => 1,
                'approved'          => 1,
            ],
            $data
        );
    }

    private function addCustomer(array $data = []): array
    {
        $data = $this->customerData($data);
        $id = (int) $this->model->addCustomer($data);
        $this->assertGreaterThan(0, $id);
        $this->customerIds[] = $id;
        return [$id, $data];
    }

    /** raw (encrypted) record from the database */
    private function rawCustomer(int $id): array
    {
        return $this->db->query(
            "SELECT * FROM " . $this->db->table('customers') . " WHERE customer_id = " . $id
        )->row;
    }

    private function decryptRaw(array $raw, string $field): string
    {
        return (string) $this->dcrypt->decrypt_field($raw[$field], (int) $raw['key_id']);
    }

    // ---------------------------------------------------------------
    // add / edit customer
    // ---------------------------------------------------------------

    public function testAddCustomerEncryptsFieldsOnlyOnce(): void
    {
        [$id, $data] = $this->addCustomer();
        $raw = $this->rawCustomer($id);

        $this->assertGreaterThan(0, (int) $raw['key_id']);
        foreach (['email', 'telephone', 'fax'] as $field) {
            $this->assertNotSame($data[$field], $raw[$field], $field . ' must be stored encrypted');
            //single decryption must give the plain value (no double encryption)
            $this->assertSame($data[$field], $this->decryptRaw($raw, $field), $field);
        }
        //not encrypted fields
        $this->assertSame($data['sms'], $raw['sms']);
        $this->assertSame($data['loginname'], $raw['loginname']);
        //password is hashed
        $this->assertNotSame($data['password'], $raw['password']);
        $this->assertSame(passwordHash($data['password'], $raw['salt']), $raw['password']);

        $customer = $this->model->getCustomer($id);
        $this->assertSame($data['email'], $customer['email']);
        $this->assertSame($data['telephone'], $customer['telephone']);
    }

    public function testEditCustomerKeepsSingleEncryption(): void
    {
        [$id, $data] = $this->addCustomer();
        $newData = array_merge(
            $data,
            [
                'email'     => self::PREFIX . 'edited_' . uniqid() . '@example.com',
                'telephone' => '+1 555 999999',
                'sms'       => '+1777999999',
                'password'  => '',
            ]
        );
        $oldRaw = $this->rawCustomer($id);
        $this->model->editCustomer((string) $id, $newData);

        $raw = $this->rawCustomer($id);
        $this->assertGreaterThan(0, (int) $raw['key_id']);
        $this->assertSame($newData['email'], $this->decryptRaw($raw, 'email'));
        $this->assertSame($newData['telephone'], $this->decryptRaw($raw, 'telephone'));
        $this->assertSame($newData['sms'], $raw['sms']);
        //empty password must not be changed
        $this->assertSame($oldRaw['password'], $raw['password']);

        $customer = $this->model->getCustomer($id);
        $this->assertSame($newData['email'], $customer['email']);
    }

    public function testEditCustomerNotificationsEncryptsWithRecordKey(): void
    {
        [$id] = $this->addCustomer();
        $email = self::PREFIX . 'im_' . uniqid() . '@example.com';

        $this->assertTrue($this->model->editCustomerNotifications($id, ['email' => $email, 'sms' => '+1700000']));
        $raw = $this->rawCustomer($id);
        $this->assertNotSame($email, $raw['email']);
        $this->assertSame($email, $this->decryptRaw($raw, 'email'));
        $this->assertSame('+1700000', $raw['sms']);

        //no IM data - nothing to update, no sql error
        $this->assertTrue($this->model->editCustomerNotifications($id, ['firstname' => 'Nobody']));
        $this->assertSame('Phpunit', $this->rawCustomer($id)['firstname']);

        $this->assertFalse($this->model->editCustomerNotifications(0, ['email' => $email]));
    }

    // ---------------------------------------------------------------
    // editCustomerField
    // ---------------------------------------------------------------

    public function testEditCustomerFieldEncryptsEncryptedField(): void
    {
        [$id] = $this->addCustomer();
        $email = self::PREFIX . 'field_' . uniqid() . '@example.com';

        $this->assertTrue($this->model->editCustomerField($id, 'email', $email));
        $raw = $this->rawCustomer($id);
        $this->assertNotSame($email, $raw['email']);
        $this->assertSame($email, $this->decryptRaw($raw, 'email'));
        $this->assertSame($email, $this->model->getCustomer($id)['email']);
    }

    public function testEditCustomerFieldUpdatesPlainField(): void
    {
        [$id] = $this->addCustomer();

        $this->assertTrue($this->model->editCustomerField($id, 'firstname', "O'Brien"));
        $this->assertTrue($this->model->editCustomerField($id, 'approved', false));
        $this->assertTrue($this->model->editCustomerField($id, 'sms', '+1888'));

        $raw = $this->rawCustomer($id);
        $this->assertSame("O'Brien", $raw['firstname']);
        $this->assertEquals(0, $raw['approved']);
        $this->assertSame('+1888', $raw['sms']);
    }

    public function testEditCustomerFieldPasswordIsHashed(): void
    {
        [$id] = $this->addCustomer();
        $oldRaw = $this->rawCustomer($id);

        $this->assertTrue($this->model->editCustomerField($id, 'password', 'New-pass-2'));
        $raw = $this->rawCustomer($id);
        $this->assertNotSame($oldRaw['salt'], $raw['salt']);
        $this->assertSame(passwordHash('New-pass-2', $raw['salt']), $raw['password']);
    }

    public static function forbiddenFieldsProvider(): array
    {
        return [
            'unknown column'     => ['not_existing_column'],
            'primary key'        => ['customer_id'],
            'salt'               => ['salt'],
            'key_id'             => ['key_id'],
            'date_added'         => ['date_added'],
            'sql injection'      => ['status = 0, firstname'],
            'injection with sql' => ["approved = 0 WHERE 1=1 -- "],
            'empty'              => [''],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('forbiddenFieldsProvider')]
    public function testEditCustomerFieldRejectsForbiddenFields(string $field): void
    {
        [$id] = $this->addCustomer();
        $before = $this->rawCustomer($id);

        $this->assertFalse($this->model->editCustomerField($id, $field, 'hacked'));
        $this->assertSame($before, $this->rawCustomer($id));
    }

    public function testEditCustomerFieldRejectsEmptyCustomer(): void
    {
        $this->assertFalse($this->model->editCustomerField(0, 'firstname', 'x'));
        $this->assertFalse($this->model->editCustomerField('abc', 'firstname', 'x'));
    }

    // ---------------------------------------------------------------
    // addresses
    // ---------------------------------------------------------------

    public function testAddressFieldsEncryption(): void
    {
        [$id] = $this->addCustomer();
        $address = [
            'firstname'  => 'Phpunit',
            'lastname'   => 'Address',
            'company'    => 'Phpunit Co',
            'address_1'  => '1 Test street',
            'address_2'  => '',
            'city'       => 'Testville',
            'postcode'   => '12345',
            'country_id' => (int) $this->config->get('config_country_id'),
            'zone_id'    => (int) $this->config->get('config_zone_id'),
        ];
        $addressId = $this->model->addAddress($id, $address);
        $this->assertGreaterThan(0, $addressId);

        $this->assertTrue($this->model->editAddressField($addressId, 'city', 'New City'));
        $this->assertTrue($this->model->editAddressField($addressId, 'firstname', 'Edited'));

        $raw = $this->db->query(
            "SELECT * FROM " . $this->db->table('addresses') . " WHERE address_id = " . (int) $addressId
        )->row;
        $this->assertNotSame('New City', $raw['city']);
        $this->assertSame('New City', $this->decryptRaw($raw, 'city'));
        $this->assertSame('Edited', $raw['firstname']);

        //forbidden fields
        $this->assertFalse($this->model->editAddressField($addressId, 'customer_id', '1'));
        $this->assertFalse($this->model->editAddressField($addressId, 'city = 1, firstname', 'x'));
        $this->assertFalse($this->model->editAddressField(0, 'city', 'x'));
        $this->assertEquals(
            $id,
            $this->db->query(
                "SELECT customer_id FROM " . $this->db->table('addresses') . " WHERE address_id = " . (int) $addressId
            )->row['customer_id']
        );

        $addresses = $this->model->getAddresses($id);
        $this->assertCount(1, $addresses);
        $this->assertSame('New City', $addresses[0]['city']);
        $this->assertSame('1 Test street', $addresses[0]['address_1']);
        $this->assertSame('Phpunit Co', $addresses[0]['company']);
    }

    // ---------------------------------------------------------------
    // getCustomers filters
    // ---------------------------------------------------------------

    public function testGetCustomersFiltersByNotEncryptedSms(): void
    {
        [$id, $data] = $this->addCustomer();
        $this->addCustomer();

        $rows = $this->model->getCustomers(['filter' => ['sms' => $data['sms']]], 'quick');
        $this->assertSame([$id], array_map('intval', array_column($rows, 'customer_id')));
        $this->assertSame($data['email'], $rows[0]['email']);

        $total = $this->model->getTotalCustomers(['filter' => ['sms' => $data['sms']]]);
        $this->assertSame(1, $total);
    }

    public function testGetCustomersFiltersByEncryptedTelephone(): void
    {
        [$id, $data] = $this->addCustomer();
        [$id2] = $this->addCustomer();

        //telephone is encrypted, so it is filtered after decryption. Narrow selection by loginname
        $rows = $this->model->getCustomers(
            [
                'filter' => [
                    'loginname' => self::PREFIX . '%',
                    'telephone' => $data['telephone'],
                ],
            ]
        );
        $ids = array_map('intval', array_column($rows, 'customer_id'));
        $this->assertContains($id, $ids);
        $this->assertNotContains($id2, $ids);
        $this->assertSame($data['telephone'], $rows[0]['telephone']);
    }

    public function testGetCustomersWithoutEmailFilterHasNoWarnings(): void
    {
        [$id] = $this->addCustomer();
        $rows = $this->model->getCustomers(['filter' => ['customer_id' => $id]]);
        $this->assertCount(1, $rows);
        $this->assertSame(1, $this->model->getTotalCustomers(['filter' => ['customer_id' => $id]]));
    }

    // ---------------------------------------------------------------
    // is_unique_loginname
    // ---------------------------------------------------------------

    public function testIsUniqueLoginname(): void
    {
        $login = self::PREFIX . 'ab_cd_' . substr(uniqid(), -5);
        [$id] = $this->addCustomer(['loginname' => $login]);

        $this->assertFalse($this->model->is_unique_loginname($login));
        //case-insensitive
        $this->assertFalse($this->model->is_unique_loginname(strtoupper($login)));
        //underscore is not a wildcard
        $this->assertTrue($this->model->is_unique_loginname(str_replace('_', 'x', $login)));
        //own login name is allowed
        $this->assertTrue($this->model->is_unique_loginname($login, $id));
        $this->assertTrue($this->model->is_unique_loginname($login, (string) $id));
        //quote must not break sql
        $this->assertTrue($this->model->is_unique_loginname(self::PREFIX . "o'brien"));
        $this->assertFalse($this->model->is_unique_loginname(''));
    }
}
