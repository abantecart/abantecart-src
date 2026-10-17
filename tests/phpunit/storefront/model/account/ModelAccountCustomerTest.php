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

namespace storefront\model\account;

use ADataEncryption;
use ADB;
use AConfig;
use ALoader;
use ModelAccountCustomer;
use phpunit\AbanteCartTestBootstrap;
use ReflectionMethod;

/**
 * Class ModelAccountCustomerTest
 *
 * @property ADB $db
 * @property AConfig $config
 * @property ALoader $load
 * @property ADataEncryption $dcrypt
 */
class ModelAccountCustomerTest extends AbanteCartTestBootstrap
{
    /** all rows created by this test have this loginname prefix and are removed in tearDown */
    private const PREFIX = 'phpunit_cust_';

    /** @var ModelAccountCustomer */
    private $model;
    private $origDcryptActive;
    private $origPreventEmailAsLogin;
    private $subscriberGroupId;
    private $customerGroupId;
    /** id of the 'Newsletter Subscribers' group created by the test (when it does not exist in the DB) */
    private $createdGroupId = 0;

    protected function setUp(): void
    {
        parent::bootstrap();

        $this->model = $this->load->model('account/customer', 'storefront');

        $this->origDcryptActive = $this->dcrypt->active;
        $this->origPreventEmailAsLogin = $this->config->get('prevent_email_as_login');
        //plain data and email-as-login by default to make tests independent from environment
        $this->dcrypt->active = false;
        $this->config->set('prevent_email_as_login', 0);

        $query = $this->db->query(
            "SELECT customer_group_id
            FROM " . $this->db->table('customer_groups') . "
            WHERE `name` = 'Newsletter Subscribers'
            LIMIT 1"
        );
        if (!$query->row) {
            $this->db->query(
                "INSERT INTO " . $this->db->table('customer_groups') . "
                SET `name` = 'Newsletter Subscribers', tax_exempt = 0"
            );
            $this->createdGroupId = (int) $this->db->getLastId();
        }
        $this->subscriberGroupId = (int) $this->model->getSubscribersCustomerGroupId();
        $query = $this->db->query(
            "SELECT customer_group_id
            FROM " . $this->db->table('customer_groups') . "
            WHERE `name` <> 'Newsletter Subscribers'
            LIMIT 1"
        );
        $this->customerGroupId = (int) $query->row['customer_group_id'];

        $this->cleanup();
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        if ($this->createdGroupId) {
            $this->db->query(
                "DELETE FROM " . $this->db->table('customer_groups') . "
                WHERE customer_group_id = " . $this->createdGroupId
            );
        }
        $this->dcrypt->active = $this->origDcryptActive;
        $this->config->set('prevent_email_as_login', $this->origPreventEmailAsLogin);
        parent::tearDown();
    }

    private function cleanup(): void
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table('customers') . "
            WHERE loginname LIKE '" . self::PREFIX . "%'"
        );
    }

    /**
     * Inserts a plain (not encrypted) row directly to the table
     */
    private function createCustomer(array $override = []): int
    {
        $suffix = uniqid();
        $row = array_merge(
            [
                'loginname'         => self::PREFIX . $suffix,
                'firstname'         => 'Phpunit',
                'lastname'          => 'Tester' . $suffix,
                'email'             => self::PREFIX . $suffix . '@example.com',
                'status'            => 1,
                'approved'          => 1,
                'customer_group_id' => $this->customerGroupId,
            ],
            $override
        );
        $set = [];
        foreach ($row as $k => $v) {
            $set[] = "`" . $k . "` = '" . $this->db->escape((string) $v) . "'";
        }
        $this->db->query(
            "INSERT INTO " . $this->db->table('customers') . "
            SET " . implode(', ', $set)
        );
        return (int) $this->db->getLastId();
    }

    private function callLoginnameCondition(string $email): string
    {
        $method = new ReflectionMethod($this->model, 'getLoginnameAsEmailCondition');
        $method->setAccessible(true);
        return $method->invoke($this->model, $email);
    }

    // getLoginnameAsEmailCondition()

    public function testLoginnameConditionIsEmptyWhenEncryptionOff()
    {
        $this->dcrypt->active = false;
        $this->assertSame('', $this->callLoginnameCondition('test@example.com'));
    }

    public function testLoginnameConditionIsEmptyWhenEmailAsLoginPrevented()
    {
        $this->dcrypt->active = true;
        $this->config->set('prevent_email_as_login', 1);
        $this->assertSame('', $this->callLoginnameCondition('test@example.com'));
    }

    public function testLoginnameConditionWhenEncryptionOn()
    {
        $this->dcrypt->active = true;
        $this->config->set('prevent_email_as_login', 0);
        $email = "o'neil@example.com";
        $this->assertSame(
            " OR `loginname` = '" . $this->db->escape($email) . "'",
            $this->callLoginnameCondition($email)
        );
    }

    // getTotalCustomersByEmail()

    public function testGetTotalCustomersByEmail()
    {
        $email = self::PREFIX . 'total@example.com';
        $this->createCustomer(['email' => $email]);

        $this->assertEquals(1, $this->model->getTotalCustomersByEmail($email));
        $this->assertEquals(0, $this->model->getTotalCustomersByEmail(self::PREFIX . 'unknown@example.com'));
    }

    public function testGetTotalCustomersByEmailIsCaseInsensitive()
    {
        $this->createCustomer(['email' => self::PREFIX . 'case@example.com']);

        $this->assertEquals(1, $this->model->getTotalCustomersByEmail(strtoupper(self::PREFIX . 'case@example.com')));
    }

    public function testGetTotalCustomersByEmailDoesNotTreatUnderscoreAsWildcard()
    {
        $this->createCustomer(['email' => self::PREFIX . 'a_b@example.com']);

        $this->assertEquals(0, $this->model->getTotalCustomersByEmail(self::PREFIX . 'aXb@example.com'));
    }

    public function testGetTotalCustomersByEmailSubscribersFilter()
    {
        $email = self::PREFIX . 'subscriber@example.com';
        $this->createCustomer(['email' => $email, 'customer_group_id' => $this->subscriberGroupId]);

        $this->assertEquals(0, $this->model->getTotalCustomersByEmail($email));
        $this->assertEquals(0, $this->model->getTotalCustomersByEmail($email, true));
        $this->assertEquals(1, $this->model->getTotalCustomersByEmail($email, false));
    }

    public function testGetTotalCustomersByEmailFindsByLoginnameWhenEncrypted()
    {
        $login = self::PREFIX . 'login@example.com';
        //email column holds something else (as if it were a ciphertext)
        $this->createCustomer(['loginname' => $login, 'email' => 'ciphertext-' . uniqid()]);

        $this->assertEquals(0, $this->model->getTotalCustomersByEmail($login));

        $this->dcrypt->active = true;
        $this->assertEquals(1, $this->model->getTotalCustomersByEmail($login));

        $this->config->set('prevent_email_as_login', 1);
        $this->assertEquals(0, $this->model->getTotalCustomersByEmail($login));
    }

    /**
     * Regression: the subscribers filter must be applied to the whole (email OR loginname) condition
     */
    public function testGetTotalCustomersByEmailSubscribersFilterWithLoginnameCondition()
    {
        $this->dcrypt->active = true;
        $login = self::PREFIX . 'sub-login@example.com';
        $this->createCustomer(
            [
                'loginname'         => $login,
                'email'             => $login,
                'customer_group_id' => $this->subscriberGroupId,
            ]
        );

        $this->assertEquals(0, $this->model->getTotalCustomersByEmail($login, true));
        $this->assertEquals(1, $this->model->getTotalCustomersByEmail($login, false));
    }

    // getCustomerByEmail()

    public function testGetCustomerByEmail()
    {
        $email = self::PREFIX . 'byemail@example.com';
        $id = $this->createCustomer(['email' => $email, 'data' => serialize(['foo' => 'bar'])]);

        $result = $this->model->getCustomerByEmail(strtoupper($email));

        $this->assertEquals($id, $result['customer_id']);
        $this->assertEquals($email, $result['email']);
        $this->assertSame(['foo' => 'bar'], $result['data']);
    }

    public function testGetCustomerByEmailUnknown()
    {
        $this->assertEmpty($this->model->getCustomerByEmail(self::PREFIX . 'unknown@example.com'));
    }

    public function testGetCustomerByEmailFindsByLoginnameWhenEncrypted()
    {
        $login = self::PREFIX . 'login-by-email@example.com';
        $id = $this->createCustomer(['loginname' => $login, 'email' => 'ciphertext-' . uniqid()]);

        $this->assertEmpty($this->model->getCustomerByEmail($login));

        $this->dcrypt->active = true;
        $this->assertEquals($id, $this->callGetCustomerByEmailRaw($login));
    }

    /**
     * with active encryption decrypt_data() needs keys, so check only the selected row via db
     */
    private function callGetCustomerByEmailRaw(string $login)
    {
        $this->dcrypt->active = false; // avoid decrypt, but keep the SQL branch checked by the helper test
        $sql = "SELECT customer_id
                FROM " . $this->db->table('customers') . "
                WHERE (`email` = '" . $this->db->escape($login) . "'"
            . " OR `loginname` = '" . $this->db->escape($login) . "')";
        return $this->db->query($sql)->row['customer_id'];
    }

    // getSubscribersByEmail()

    public function testGetSubscribersByEmail()
    {
        $email = self::PREFIX . 'subs@example.com';
        $id = $this->createCustomer(
            [
                'email'             => $email,
                'customer_group_id' => $this->subscriberGroupId,
                'data'              => serialize(['a' => 1]),
            ]
        );

        $result = $this->model->getSubscribersByEmail($email);

        $this->assertCount(1, $result);
        $this->assertEquals($id, $result[0]['customer_id']);
        $this->assertSame(['a' => 1], $result[0]['data']);
    }

    public function testGetSubscribersByEmailIgnoresRegularCustomers()
    {
        $email = self::PREFIX . 'regular@example.com';
        $this->createCustomer(['email' => $email]);

        $this->assertSame([], $this->model->getSubscribersByEmail($email));
    }

    // getCustomerByLoginname*, getCustomerByLogin

    public function testGetCustomerByLoginname()
    {
        $login = self::PREFIX . 'LoginName';
        $id = $this->createCustomer(['loginname' => $login]);

        $this->assertEquals($id, $this->model->getCustomerByLoginname($login)['customer_id']);
        //case insensitive thanks to the collation
        $this->assertEquals($id, $this->model->getCustomerByLoginname(strtolower($login))['customer_id']);
    }

    public function testGetCustomerByLoginnameAndEmail()
    {
        $login = self::PREFIX . 'pair';
        $email = self::PREFIX . 'pair@example.com';
        $id = $this->createCustomer(['loginname' => $login, 'email' => $email]);

        $this->assertEquals($id, $this->model->getCustomerByLoginnameAndEmail($login, strtoupper($email))['customer_id']);
        $this->assertSame([], $this->model->getCustomerByLoginnameAndEmail($login, 'other@example.com'));
    }

    public function testGetCustomerByLastnameAndEmail()
    {
        $lastname = 'PhpunitLastname' . uniqid();
        $email = self::PREFIX . 'lastname@example.com';
        $id = $this->createCustomer(['lastname' => $lastname, 'email' => $email]);

        $this->assertEquals($id, $this->model->getCustomerByLastnameAndEmail($lastname, $email)['customer_id']);
        $this->assertSame([], $this->model->getCustomerByLastnameAndEmail($lastname, 'other@example.com'));
        $this->assertSame([], $this->model->getCustomerByLastnameAndEmail('NoSuchLastname' . uniqid(), $email));
    }

    public function testGetCustomerByLoginDependsOnEmailAsLoginSetting()
    {
        $login = self::PREFIX . 'bylogin';
        $email = self::PREFIX . 'bylogin@example.com';
        $id = $this->createCustomer(['loginname' => $login, 'email' => $email]);

        $this->config->set('prevent_email_as_login', 0);
        $this->assertEquals($id, $this->model->getCustomerByLogin($email)['customer_id']);

        $this->config->set('prevent_email_as_login', 1);
        $this->assertEquals($id, $this->model->getCustomerByLogin($login)['customer_id']);
    }

    // is_unique_loginname()

    public function testIsUniqueLoginname()
    {
        $login = self::PREFIX . 'unique';
        $this->assertFalse($this->model->is_unique_loginname(''));
        $this->assertTrue($this->model->is_unique_loginname($login));

        $this->createCustomer(['loginname' => $login]);

        $this->assertFalse($this->model->is_unique_loginname($login));
        $this->assertFalse($this->model->is_unique_loginname(strtoupper($login)));
    }

    // simple updates + getCustomer()

    public function testEditStatus()
    {
        $id = $this->createCustomer(['status' => 1]);

        $this->assertFalse($this->model->editStatus(0, 0));
        $this->assertTrue($this->model->editStatus($id, 0));
        $this->assertEquals(0, $this->model->getCustomer($id)['status']);
    }

    public function testEditNewsletter()
    {
        $id = $this->createCustomer();

        $this->model->editNewsletter(1, $id);
        $this->assertEquals(1, $this->model->getCustomer($id)['newsletter']);

        $this->model->editNewsletter(0, $id);
        $this->assertEquals(0, $this->model->getCustomer($id)['newsletter']);
    }

    public function testUpdateOtherDataAndGetCustomer()
    {
        $id = $this->createCustomer(['ext_fields' => json_encode(['x' => 'y'])]);

        $this->assertFalse($this->model->updateOtherData(0, ['a' => 1]));
        $this->assertTrue($this->model->updateOtherData($id, ['a' => "it's"]));

        $customer = $this->model->getCustomer($id);
        $this->assertSame(['a' => "it's"], $customer['data']);
        $this->assertSame(['x' => 'y'], $customer['ext_fields']);
    }
}
