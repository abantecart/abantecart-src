<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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

/** @noinspection PhpMultipleClassDeclarationsInspection */

use ReCaptcha\ReCaptcha;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class ModelAccountCustomer
 *
 * @property AIM $im
 * @property ACustomer $customer
 */
class ModelAccountCustomer extends Model
{
    public $error = [];

    public function __construct($registry)
    {
        parent::__construct($registry);
        //this list can be changed from hook beforeModelModelCheckoutOrder
        $this->data['customer_column_list'] = [
            'store_id'          => 'int',
            'loginname'         => 'string',
            'firstname'         => 'string',
            'lastname'          => 'string',
            'email'             => 'string',
            'telephone'         => 'string',
            'fax'               => 'string',
            'salt'              => 'string',
            'password'          => 'string',
            'newsletter'        => 'int',
            'customer_group_id' => 'int',
            'approved'          => 'int',
            'status'            => 'int',
            'ip'                => 'string',
            'data'              => 'serialize',
        ];
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException|TransportExceptionInterface
     */
    public function addCustomer($data)
    {
        if (!$data) {
            return false;
        }

        $inData = $data;

        if (!(int) $data['customer_group_id']) {
            $data['customer_group_id'] = (int) $this->config->get('config_customer_group_id');
        }
        if (!isset($data['status'])) {
            // if need to activate via email  - disable status
            if ($this->config->get('config_customer_email_activation')) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }
        }
        if (isset($data['approved'])) {
            $data['approved'] = (int) $data['approved'];
        } else {
            if (!$this->config->get('config_customer_approval')) {
                $data['approved'] = 1;
            }
        }

        $insertArr = [];
        if ($this->dcrypt->active) {
            $encData = $this->dcrypt->encrypt_data($data, 'customers');
        } else {
            $encData = $data;
        }

        $data['salt'] = $salt_key = genToken(8);
        $data['password'] = passwordHash($data['password'], $salt_key);
        $data['store_id'] = (int) $this->config->get('config_store_id');

        // delete subscription accounts for given email
        $subscriber = $this->db->query(
            "SELECT customer_id
            FROM " . $this->db->table("customers") . "
            WHERE LOWER(`email`) = LOWER('" . $this->db->escape($encData['email']) . "')
                AND customer_group_id IN (SELECT customer_group_id
                                          FROM " . $this->db->table('customer_groups') . "
                                          WHERE `name` = 'Newsletter Subscribers')"
        );
        foreach ($subscriber->rows as $row) {
            $this->db->query(
                "DELETE FROM " . $this->db->table("customers") . " 
                 WHERE customer_id = '" . (int) $row['customer_id'] . "'"
            );
            $this->db->query(
                "DELETE FROM " . $this->db->table("addresses") . " 
                 WHERE customer_id = '" . (int) $row['customer_id'] . "'"
            );
        }
        //prepare data to insert into the customer table
        foreach ($this->data['customer_column_list'] as $key => $dataType) {
            if (!isset($data[$key])) {
                continue;
            }
            if ($dataType == 'int') {
                $value = (int) $data[$key];
            } elseif ($dataType == 'float') {
                $value = (float) $data[$key];
            } elseif ($dataType == 'string') {
                $value = $this->db->escape(trim($data[$key]));
            } else {
                $value = $this->db->escape(serialize($data[$key]));
            }
            $insertArr[$key] = $value;
        }
        //prepare extended fields values
        $extFields = [];
        /** @var ModelAccountAddress $addressMdl */
        $addressMdl = $this->load->model('account/address');
        $addressColumnList = array_keys($addressMdl->data['address_column_list']);

        //merge generic column list for both tables (customer + address) to avoid double saving
        $colNames = array_merge(
            array_keys($this->data['customer_column_list']),
            $addressColumnList
        );

        foreach ($data as $key => $value) {
            if (in_array($key, ['csrftoken', 'csrfinstance', 'confirm', 'agree', 'captcha', 'recaptcha'])) {
                continue;
            }
            if (in_array($key, $colNames)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (in_array($subKey, $colNames)) {
                        continue;
                    }
                    $extFields[$key][$subKey] = $subValue;
                }
            } else {
                $extFields[$key] = $value;
            }
        }

        if ($extFields) {
            $insertArr['ext_fields'] = $this->db->escape(js_encode($extFields));
        }
        if ($this->dcrypt->active) {
            $insertArr = $this->dcrypt->encrypt_data($insertArr, 'customers');
            $insertArr['key_id'] = (int) $data['key_id'];
        }

        $insertData = [];
        foreach ($insertArr as $k => $v) {
            $insertData[$k] = "`" . $k . "` = '" . $v . "'";
        }

        $sql = "INSERT INTO " . $this->db->table("customers") . "
                SET " . implode(', ', $insertData);
        $this->db->query($sql);
        $customer_id = $this->db->getLastId();

        //remove already saved data from a set
        foreach (array_keys($this->data['customer_column_list']) as $key) {
            if (in_array($key, $addressColumnList)) {
                continue;
            }
            unset($inData[$key]);
        }
        $inData['customer_id'] = $customer_id;
        $address_id = $addressMdl->addAddress($inData);

        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
            SET address_id = '" . (int) $address_id . "'
            WHERE customer_id = '" . $customer_id . "'"
        );

        if (!$data['approved']) {
            $language = new ALanguage($this->registry);
            $language->load($language->language_details['directory']);
            $language->load('account/create');

            $msgTextKey = $data['subscriber']
                //notify administrator of pending subscriber approval
                ? 'text_pending_subscriber_approval'
                //notify administrator of pending customer approval
                : 'text_pending_customer_approval';

            $msg_text = $language->getAndReplace(
                          $msgTextKey,
                replaces: [
                              $data['firstname'] . ' ' . $data['lastname'],
                              $customer_id,
                          ]
            );
            $msg = new AMessage();
            $msg->saveNotice($language->get('text_new_customer'), $msg_text);
        }

        //enable notification setting for newsletter via email
        if ($data['newsletter']) {
            $sql = "INSERT INTO " . $this->db->table('customer_notifications') . "
                        (customer_id, sendpoint, protocol, status, date_added)
                    VALUES
                    ('" . $customer_id . "','newsletter','email','1',NOW());";
            $this->db->query($sql);
        }

        //notify admin
        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('common/im');
        if ($data['subscriber']) {
            $lang_key = 'im_new_subscriber_text_to_admin';
            $templateId = 'storefront_new_subscriber_admin_notify';
        } else {
            $lang_key = 'im_new_customer_text_to_admin';
            $templateId = 'storefront_new_customer_admin_notify';
        }

        // Send additional alert emails about new registered customer
        $emails = array_unique(
            array_map(
                'trim',
                explode(',', $this->config->get('config_alert_emails'))
            )
        );
        $emails = array_filter($emails, function ($email) {
            return preg_match(EMAIL_REGEX_PATTERN, $email);
        });
        foreach ($emails as $email) {
            $this->_send_email($email, $templateId, $data);
        }

        $message_arr = [
            1 => [
                'message' => $language->getAndReplace(
                              $lang_key,
                    replaces: [
                                  $customer_id,
                                  $data['firstname'] . ' ' . $data['lastname'],
                              ]
                ),
            ],
        ];
        $this->im->send('new_customer', $message_arr, $templateId, $data);
        return $customer_id;
    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function editCustomer($data)
    {
        $data = (array) $data;
        if (!$data) {
            return false;
        }

        $data['store_name'] = $this->config->get('store_name');

        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('common/im');

        //update login only if needed
        if ($data['loginname'] && trim($data['loginname']) != $this->customer->getLoginName()) {
            $message_arr = [
                0 => [
                    'message' => $language->getAndReplace(
                                  'im_customer_account_update_login_to_customer',
                        replaces: $data['loginname']
                    ),
                ],
            ];
            $data['old_loginname'] = $this->customer->getLoginName();
            $this->im->send('customer_account_update', $message_arr, 'storefront_customer_account_update', $data);
        }

        //get existing data and compare
        $priorData = $this->getCustomer($this->customer->getId());
        foreach ($priorData as $rec => $val) {
            if ($rec == 'email' && $val != $data['email']) {
                $message_arr = [
                    0 => [
                        'message' => $language->getAndReplace(
                                      'im_customer_account_update_email_to_customer',
                            replaces: $data['email']
                        ),
                    ],
                ];
                $data['old_email'] = $val;
                $this->im->send('customer_account_update', $message_arr, 'storefront_customer_account_update', $data);
            }
        }

        if (
            trim($data['firstname']) != $this->customer->getFirstName()
            || trim($data['lastname']) != $this->customer->getLastName()
            || trim($data['telephone']) != $this->customer->getTelephone()
            || trim($data['fax']) != $this->customer->getFax()
        ) {
            $message_arr = [
                0 => [
                    'message' => $language->get('im_customer_account_update_text_to_customer'),
                ],
            ];
            $this->im->send('customer_account_update', $message_arr, 'storefront_customer_account_update', $data);
        }

        $updateArr = [];
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'customers');
            $updateArr[] = "`key_id` = '" . (int) $data['key_id'] . "'";
        }

        foreach ($this->data['customer_column_list'] as $key => $dataType) {
            if (!isset($data[$key])) {
                continue;
            }
            if ($dataType == 'int') {
                $value = (int) $data[$key];
            } elseif ($dataType == 'float') {
                $value = (float) $data[$key];
            } elseif ($dataType == 'string') {
                $value = $this->db->escape(trim($data[$key]));
            } else {
                $value = $this->db->escape(serialize($data[$key]));
            }
            $updateArr[] = "`" . $key . "` = '" . $value . "'";
        }

        //prepare extended fields values
        $extFields = (array) $priorData['ext_fields'];
        $colNames = array_keys($this->data['customer_column_list']);
        //ignore store_name.
        $colNames[] = 'store_name';

        foreach ($data as $key => $value) {
            if (in_array($key, $colNames)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if (in_array($key, $colNames)) {
                        continue;
                    }
                    $extFields[$key][$subKey] = $subValue;
                }
            } else {
                $extFields[$key] = $value;
            }
        }

        if ($extFields) {
            $updateArr[] = "`ext_fields` = '" . $this->db->escape(js_encode($extFields)) . "'";
        }

        $sql = "UPDATE " . $this->db->table("customers") . "
                SET " . implode(', ', $updateArr) . "
                WHERE customer_id = '" . $this->customer->getId() . "'";
        $this->db->query($sql);
        return true;
    }

    /**
     * @param array $data
     * @param int $customer_id
     *
     * @return bool
     * @throws AException
     */
    public function editCustomerNotifications($data, $customer_id = 0)
    {
        if (!$data) {
            return false;
        }

        if (!$customer_id) {
            $customer_id = $this->customer->getId();
        }

        $upd = [];
        //get only active IM drivers
        $im_protocols = $this->im->getProtocols();
        foreach ($im_protocols as $protocol) {
            if (isset($data[$protocol])) {
                $upd[$protocol] =
                    "`" . $this->db->escape($protocol) . "` = '" . $this->db->escape($data[$protocol]) . "'";
            }
        }
        //get all columns
        $sql = "SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' 
                    AND TABLE_NAME = '" . $this->db->table("customers") . "'";
        $result = $this->db->query($sql);
        $columns = [];
        foreach ($result->rows as $row) {
            $columns[] = $row['column_name'];
        }

        //remove not IM data
        $diff = array_diff($im_protocols, $columns);
        foreach ($diff as $k) {
            unset($data[$k]);
        }

        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'customers');
            $key_sql = ", key_id = '" . (int) $data['key_id'] . "'";
        }

        $sql = "UPDATE " . $this->db->table('customers') . "
                SET " . implode(', ', $upd) . "\n"
            . $key_sql .
            " WHERE customer_id = '" . $customer_id . "'";
        $this->db->query($sql);
        return true;
    }

    /**
     * @return array
     * @throws AException
     */
    public function getCustomerNotificationSettings()
    {
        //get only active IM drivers
        $im_protocols = $this->im->getProtocols();
        $im_settings = [];
        $sql = "SELECT *
                FROM " . $this->db->table('customer_notifications') . "
                WHERE customer_id = " . $this->customer->getId();
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            if (!in_array($row['protocol'], $im_protocols)) {
                continue;
            }
            $im_settings[$row['sendpoint']][$row['protocol']] = (int) $row['status'];
        }
        return $im_settings;
    }

    /**
     * @param array $settings
     *
     * @return bool|null
     * @throws AException
     */
    public function saveCustomerNotificationSettings($settings)
    {
        $customer_id = $this->customer->getId();
        //do not save settings for guests
        if (!$customer_id) {
            return null;
        }

        $sendpoints = array_keys($this->im->sendpoints);
        $im_protocols = $this->im->getProtocols();

        $update = [];
        foreach ($sendpoints as $sendpoint) {
            foreach ($im_protocols as $protocol) {
                $update[$sendpoint][$protocol] = (int) $settings[$sendpoint][$protocol];
            }
        }

        if ($update) {
            $sql = "DELETE FROM " . $this->db->table('customer_notifications') . "
                    WHERE customer_id = " . $customer_id;
            $this->db->query($sql);

            foreach ($update as $sendpoint => $row) {
                foreach ($row as $protocol => $status) {
                    $sql = "INSERT INTO " . $this->db->table('customer_notifications') . "
                                (customer_id, sendpoint, protocol, status, date_added)
                            VALUES
                            ('" . $customer_id . "',
                            '" . $this->db->escape($sendpoint) . "',
                            '" . $this->db->escape($protocol) . "',
                            '" . (int) $status . "',
                            NOW());";
                    $this->db->query($sql);
                }
            }
            //for newsletter subscription do changes inside the customers table
            //if at least one protocol enabled - set 1, otherwise - 0
            if (has_value($update['newsletter'])) {
                $newsletter_status = 0;
                foreach ($update['newsletter'] as $status) {
                    if ($status) {
                        $newsletter_status = 1;
                        break;
                    }
                }
                $this->editNewsletter($newsletter_status, $customer_id);
            }
        }

        return true;
    }

    /**
     * @param string $loginname
     * @param string $password
     *
     * @throws AException|TransportExceptionInterface
     */
    public function editPassword($loginname, $password)
    {
        $salt_key = genToken(8);
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
            SET
                salt = '" . $this->db->escape($salt_key) . "', 
                password = '" . $this->db->escape(passwordHash($password, $salt_key)) . "'
            WHERE loginname = '" . $this->db->escape($loginname) . "'"
        );
        //send IM
        $sql = "SELECT customer_id, firstname, lastname
                FROM " . $this->db->table("customers") . "
                WHERE loginname = '" . $this->db->escape($loginname) . "'";
        $result = $this->db->query($sql);
        $customer_id = $result->row['customer_id'];
        if ($customer_id) {
            $language = new ALanguage($this->registry);
            $language->load($language->language_details['directory']);
            $language->load('common/im');
            $message_arr = [
                0 => [
                    'message' => $language->get('im_customer_account_update_password_to_customer'),
                ],
            ];
            $this->im->send(
                'customer_account_update',
                $message_arr,
                'storefront_password_reset_notify',
                [
                    'customer_id' => $customer_id,
                    'loginname'   => $loginname,
                    'firstname'   => $result->row['firstname'],
                    'lastname'    => $result->row['lastname'],
                    'store_name'  => $this->config->get('store_name'),
                ]
            );
        }
    }

    /**
     * @param int $newsletter
     * @param int $customer_id - optional parameter for unsubscribing page!
     *
     * @throws AException
     */
    public function editNewsletter($newsletter, $customer_id = 0)
    {
        $customer_id = (int) $customer_id ? : $this->customer->getId();
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
            SET newsletter = '" . (int) $newsletter . "'
            WHERE customer_id = '" . $customer_id . "'"
        );
    }

    /**
     * @param $customer_id
     * @param $status
     *
     * @return bool
     * @throws AException
     */
    public function editStatus($customer_id, $status)
    {
        $customer_id = (int) $customer_id;
        $status = (int) $status;
        if (!$customer_id) {
            return false;
        }
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
           SET status = '" . (int) $status . "'
           WHERE customer_id = '" . $customer_id . "'"
        );
        return true;
    }

    /**
     * @param $customer_id
     * @param $data
     *
     * @return bool
     * @throws AException
     */
    public function updateOtherData($customer_id, $data)
    {
        $customer_id = (int) $customer_id;
        if (!$customer_id) {
            return false;
        }
        $this->db->query(
            "UPDATE " . $this->db->table("customers") . "
           SET data = '" . $this->db->escape(serialize($data)) . "'
           WHERE customer_id = '" . $customer_id . "'"
        );
        return true;
    }

    /**
     * @param int $customer_id
     *
     * @return array
     * @throws AException
     */
    public function getCustomer($customer_id)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("customers") . "
            WHERE customer_id = '" . (int) $customer_id . "'"
        );
        $result_row = $this->dcrypt->decrypt_data($query->row, 'customers');
        $result_row['data'] = unserialize($result_row['data']);
        $result_row['ext_fields'] = json_decode($result_row['ext_fields'], true);
        return $result_row;
    }

    /**
     * @param string $email
     * @param bool $no_subscribers - sign that necessary list without subscribers
     *
     * @return int
     * @throws AException
     */
    public function getTotalCustomersByEmail($email, $no_subscribers = true)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM " . $this->db->table("customers") . "
                WHERE LOWER(`email`) = LOWER('" . $this->db->escape($email) . "')";
        if ($no_subscribers) {
            $sql .= " AND customer_group_id NOT IN
                            (SELECT customer_group_id
                            FROM " . $this->db->table('customer_groups') . "
                            WHERE `name` = 'Newsletter Subscribers')";
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    /**
     * Detect a type of login configured and select the customer
     *
     * @param string $login
     *
     * @return array
     * @throws AException
     */
    public function getCustomerByLogin($login)
    {
        if ($this->config->get('prevent_email_as_login')) {
            return $this->getCustomerByLoginname($login);
        } else {
            return $this->getCustomerByEmail($login);
        }
    }

    /**
     * @param string $email
     *
     * @return array
     * @throws AException
     */
    public function getCustomerByEmail($email)
    {
        //assuming that data is not encrypted. Cannot call these otherwise
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("customers") . "
            WHERE LOWER(`email`) = LOWER('" . $this->db->escape($email) . "')"
        );
        $output = $this->dcrypt->decrypt_data($query->row, 'customers');
        if ($output['data']) {
            $output['data'] = unserialize($output['data']);
        }
        return $output;
    }

    /**
     * @param string $loginname
     *
     * @return array
     * @throws AException
     */
    public function getCustomerByLoginname($loginname)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("customers") . "
            WHERE LOWER(`loginname`) = LOWER('" . $this->db->escape($loginname) . "')"
        );
        $output = $this->dcrypt->decrypt_data($query->row, 'customers');
        if ($output['data']) {
            $output['data'] = unserialize($output['data']);
        }
        return $output;
    }

    /**
     * @param string $loginname
     * @param string $email
     *
     * @return array
     * @throws AException
     */
    public function getCustomerByLoginnameAndEmail($loginname, $email)
    {
        $result_row = $this->getCustomerByLoginname($loginname);
        //validate it is a correct row by matching decrypted email;
        if (strtolower($result_row['email']) == strtolower($email)) {
            return $result_row;
        } else {
            return [];
        }
    }

    /**
     * @param string $lastname
     * @param string $email
     *
     * @return array
     * @throws AException
     */
    public function getCustomerByLastnameAndEmail($lastname, $email)
    {
        $query = $this->db->query(
            "SELECT *
            FROM " . $this->db->table("customers") . "
            WHERE LOWER(`lastname`) = LOWER('" . $this->db->escape($lastname) . "')"
        );
        //validate if we have a row with matching decrypted email;
        $result_row = [];
        foreach ($query->rows as $result) {
            if (strtolower($email) == strtolower($this->dcrypt->decrypt_field($result['email'], $result['key_id']))) {
                $result_row = $result;
                break;
            }
        }

        if (count($result_row)) {
            return $this->dcrypt->decrypt_data($result_row, 'customers');
        } else {
            return [];
        }
    }

    /**
     * @param string $loginname
     *
     * @return bool
     * @throws AException
     */
    public function is_unique_loginname($loginname)
    {
        if (empty($loginname)) {
            return false;
        }
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
           FROM " . $this->db->table("customers") . "
           WHERE LOWER(`loginname`) = LOWER('" . $loginname . "')"
        );
        if ($query->row['total'] > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function validateRegistrationData($data)
    {
        $this->error = [];
        //If captcha enabled, validate
        if ($this->config->get('config_account_create_captcha')) {
            if ($this->config->get('config_recaptcha_secret_key')) {
                $recaptcha = new ReCaptcha($this->config->get('config_recaptcha_secret_key'));
                $resp = $recaptcha->verify(
                    $data['g-recaptcha-response'] ? : $data['captcha'] ? : $data['recaptcha'],
                    $this->request->getRemoteIP()
                );
                if (!$resp->isSuccess() && $resp->getErrorCodes()) {
                    $this->error['captcha'] = $this->language->get('error_captcha');
                }
            } else {
                if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $data['captcha'])) {
                    $this->error['captcha'] = $this->language->get('error_captcha');
                }
            }
        }

        if ($this->config->get('prevent_email_as_login')) {
            if (!$this->is_unique_loginname($data['loginname'])) {
                $this->error['loginname'] = $this->language->get('error_loginname_notunique');
            }
        }

        if ($this->getTotalCustomersByEmail($data['email'])) {
            $this->error['email'] = $this->language->get('error_exists');
        }

        $phone = $data['telephone'];
        $form = new AForm();
        $form->loadFromDb('CustomerFrm');
        $telephoneField = $form->getField('telephone');
        $this->data['phone_pattern'] = $telephoneField['regexp_pattern'] ? : DEFAULT_PHONE_REGEX_PATTERN;
        if (mb_strlen($phone) < 3 || mb_strlen($phone) > 32 || !preg_match($this->data['phone_pattern'], $phone)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        //check password length considering html-entities (special case for characters " > < & )
        $pass_len = mb_strlen(htmlspecialchars_decode($data['password']));
        if ($pass_len < 4 || $pass_len > 20) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($data['confirm'] != $data['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        $contentId = (int) $this->config->get('config_account_id');
        $this->data['text_agree'] = '';
        if ($contentId) {
            /** @var ModelCatalogContent $mdl */
            $mdlC = $this->load->model('catalog/content');
            $contentInfo = $mdlC->getContent($contentId);
            if ($contentInfo) {
                if (!isset($data['agree'])) {
                    $this->error['warning'] = $this->language->getAndReplace(
                                  'error_agree',
                        replaces: $contentInfo['title']
                    );
                }
            }
        }

        //validate IM URIs
        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();
        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                /**
                 * @var AMailIM $driver_obj
                 */
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }
                $result = $driver_obj->validateURI($data[$protocol]);
                if (!$result) {
                    $this->error[$protocol] = implode('<br>', $driver_obj->errors);
                }
            }
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);

        return $this->error;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function validateSubscribeData($data)
    {
        $this->error = [];

        if ($this->config->get('config_recaptcha_secret_key')) {
            $recaptcha = new ReCaptcha($this->config->get('config_recaptcha_secret_key'));
            $resp = $recaptcha->verify(
                $data['g-recaptcha-response'],
                $this->request->getRemoteIP()
            );
            if (!$resp->isSuccess() && $resp->getErrorCodes()) {
                $this->error['captcha'] = $this->language->get('error_captcha');
            }
        } else {
            if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $data['captcha'])) {
                $this->error['captcha'] = $this->language->get('error_captcha');
            }
        }

        if ((mb_strlen($data['firstname']) < 1) || (mb_strlen($data['firstname']) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((mb_strlen($data['lastname']) < 1) || (mb_strlen($data['lastname']) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if ((mb_strlen($data['email']) > 96) || (!preg_match(EMAIL_REGEX_PATTERN, $data['email']))) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if ($this->getTotalCustomersByEmail($data['email'])) {
            $this->error['warning'] = $this->language->get('error_subscriber_exists');
        }

        //validate IM URIs
        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();
        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                /**
                 * @var AMailIM $driver_obj
                 */
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }
                $result = $driver_obj->validateURI($data[$protocol]);
                if (!$result) {
                    $this->error[$protocol] = implode('<br>', $driver_obj->errors);
                }
            }
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);

        return $this->error;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function validateEditData($data)
    {
        $this->error = [];

        //validate the loginname only if we cannot match the email and if it is set. Edit of loginname not allowed
        if ($this->config->get('prevent_email_as_login') && isset($data['loginname'])) {
            if (!$this->is_unique_loginname($data['loginname'])) {
                $this->error['loginname'] = $this->language->get('error_loginname_notunique');
            }
        }

        if (($this->customer->getEmail() != $data['email']) && $this->getTotalCustomersByEmail($data['email'])) {
            $this->error['warning'] = $this->language->get('error_exists');
        }

        $phone = $data['telephone'];
        $form = new AForm();
        $form->loadFromDb('CustomerFrm');
        $telephoneField = $form->getField('telephone');
        $this->data['phone_pattern'] = $telephoneField['regexp_pattern'] ? : DEFAULT_PHONE_REGEX_PATTERN;
        if (mb_strlen($phone) < 3 || mb_strlen($phone) > 32 || !preg_match($this->data['phone_pattern'], $phone)) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if (count($this->error) && empty($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('gen_data_entry_error');
        }

        //validate IM URIs
        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();
        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                /**
                 * @var AMailIM $driver_obj
                 */
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }
                $result = $driver_obj->validateURI($data[$protocol]);
                if (!$result) {
                    $this->error[$protocol] = implode('<br>', $driver_obj->errors);
                }
            }
        }

        $this->extensions->hk_ValidateData($this, [__FUNCTION__]);

        return $this->error;
    }

    /**
     * @return int
     * @throws AException
     */
    public function getTotalTransactions()
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS total
           FROM `" . $this->db->table("customer_transactions") . "`
           WHERE customer_id = '" . $this->customer->getId() . "'"
        );

        return (int) $query->row['total'];
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return array
     * @throws AException
     */
    public function getTransactions($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        $query = $this->db->query(
            "SELECT 
                    t.customer_transaction_id,
                    t.order_id,
                    t.section,
                    t.credit,
                    t.debit,
                    t.transaction_type,
                    t.description,
                    t.date_added
            FROM `" . $this->db->table("customer_transactions") . "` t
            WHERE customer_id = '" . $this->customer->getId() . "'
            ORDER BY t.date_added DESC
            LIMIT " . (int) $start . "," . (int) $limit
        );

        return $query->rows;
    }

    public function getSubscribersCustomerGroupId()
    {
        $query = $this->db->query(
            "SELECT customer_group_id
            FROM `" . $this->db->table("customer_groups") . "`
            WHERE `name` = 'Newsletter Subscribers'
            LIMIT 0,1"
        );
        return !$query->row['customer_group_id']
            ? (int) $this->config->get('config_customer_group_id')
            : $query->row['customer_group_id'];
    }

    /**
     * @param string $email
     * @param bool $activated
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function sendWelcomeEmail($email, $activated)
    {
        if (!$email) {
            return null;
        }
        //build welcome email in text format
        $login_url = $this->html->getSecureURL('account/login');
        $this->language->load('mail/account_create');
        $languageId = $this->language->getLanguageID();

        if ($activated) {
            $template = 'storefront_welcome_email_activated';
        } else {
            $template = 'storefront_welcome_email_approval';
        }

        $this->data['mail_template_data']['login_url'] = $login_url;

        $mailLogo = $this->config->get('config_mail_logo_' . $languageId)
            ? : $this->config->get('config_mail_logo');
        $mailLogo = $mailLogo ? : $this->config->get('config_logo_' . $languageId);
        $mailLogo = $mailLogo ? : $this->config->get('config_logo');

        if ($mailLogo) {
            $result = getMailLogoDetails($mailLogo);
            $this->data['mail_template_data']['logo_uri'] = $result['uri'];
            $this->data['mail_template_data']['logo_html'] = $result['html'];
        }

        $this->data['mail_template_data']['config_mail_logo'] = $mailLogo;

        $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
        $this->data['mail_template_data']['store_url'] =
            $this->config->get('config_url') . $this->config->get('seo_prefix');
        $this->data['mail_template_data']['text_project_label'] = project_base();

        //allow changing email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_account_welcome_mail');

        $this->_send_email($email, $template, $this->data['mail_template_data']);

        return true;
    }

    /**
     * @param int $customer_id
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function emailActivateLink($customer_id)
    {
        if (!$customer_id) {
            return null;
        }
        $languageId = $this->language->getLanguageID();
        $customer_data = $this->getCustomer($customer_id);

        //encrypt token and data
        $enc = new AEncryption($this->config->get('encryption_key'));
        $code = genToken();
        //store activation code
        $customer_data['data']['email_activation'] = $code;
        $this->updateOtherData($customer_id, $customer_data['data']);

        $ac = $enc->encrypt($customer_id . '::' . $code);
        $activate_url = $this->html->getSecureURL('account/login', '&ac=' . $ac);

        //build welcome email
        $this->language->load('mail/account_create');

        $this->data['mail_template_data']['activate_url'] = '<a href="' . $activate_url . '">' . $activate_url . '</a>';

        $mailLogo = $this->config->get('config_mail_logo_' . $languageId)
            ? : $this->config->get('config_mail_logo');
        $mailLogo = $mailLogo ? : $this->config->get('config_logo_' . $languageId);
        $mailLogo = $mailLogo ? : $this->config->get('config_logo');

        if ($mailLogo) {
            $result = getMailLogoDetails($mailLogo);
            $this->data['mail_template_data']['logo_uri'] = $result['uri'];
            $this->data['mail_template_data']['logo_html'] = $result['html'];
        }

        $this->data['mail_template_data']['config_mail_logo'] = $mailLogo;

        $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
        $this->data['mail_template_data']['store_url'] =
            $this->config->get('config_url') . $this->config->get('seo_prefix');
        $this->data['mail_template_data']['text_project_label'] = project_base();

        //allow changing email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_account_activation_mail');

        $this->_send_email($customer_data['email'], 'storefront_send_activate_link', $this->data['mail_template_data']);

        return true;
    }

    /**
     * @param string $email
     * @param string $template
     * @param array $data
     *
     * @throws AException|TransportExceptionInterface
     */
    protected function _send_email($email, $template, $data)
    {
        $mail = new AMail($this->config);
        $mail->setTo($email);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setReplyTo($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));

        $mail->setTemplate($template, $data);

        if (is_file(DIR_RESOURCE . $data['config_mail_logo'])) {
            $mail->addAttachment(
                DIR_RESOURCE . $data['config_mail_logo'],
                md5(pathinfo($data['config_mail_logo'], PATHINFO_FILENAME))
                . '.' . pathinfo($data['config_mail_logo'], PATHINFO_EXTENSION)
            );
        }
        $mail->send(true);
    }
}
