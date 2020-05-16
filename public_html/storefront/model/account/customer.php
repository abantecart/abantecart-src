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

/**
 * Class ModelAccountCustomer
 *
 * @property ModelCatalogContent $model_catalog_content
 * @property AIM                 $im
 * @property ModelAccountOrder   $model_account_order
 * @property ACustomer $customer
 */
class ModelAccountCustomer extends Model
{
    public $data = array();
    public $error = array();

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addCustomer($data)
    {
        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'customers');
            $key_sql = ", key_id = '".(int)$data['key_id']."'";
        }
        if (!(int)$data['customer_group_id']) {
            $data['customer_group_id'] = (int)$this->config->get('config_customer_group_id');
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
            $data['approved'] = (int)$data['approved'];
        } else {
            if (!$this->config->get('config_customer_approval')) {
                $data['approved'] = 1;
            }
        }

        // delete subscription accounts for given email
        $subscriber = $this->db->query(
            "SELECT customer_id
            FROM ".$this->db->table("customers")."
            WHERE LOWER(`email`) = LOWER('".$this->db->escape($data['email'])."')
                AND customer_group_id IN (SELECT customer_group_id
                                          FROM ".$this->db->table('customer_groups')."
                                          WHERE `name` = 'Newsletter Subscribers')"
        );
        foreach ($subscriber->rows as $row) {
            $this->db->query(
                "DELETE FROM ".$this->db->table("customers")." 
                 WHERE customer_id = '".(int)$row['customer_id']."'"
            );
            $this->db->query(
                "DELETE FROM ".$this->db->table("addresses")." 
                 WHERE customer_id = '".(int)$row['customer_id']."'"
            );
        }

        $salt_key = genToken(8);
        $sql = "INSERT INTO ".$this->db->table("customers")."
              SET	store_id = '".(int)$this->config->get('config_store_id')."',
                    loginname = '".$this->db->escape($data['loginname'])."',
                    firstname = '".$this->db->escape(trim($data['firstname']))."',
                    lastname = '".$this->db->escape(trim($data['lastname']))."',
                    email = '".$this->db->escape($data['email'])."',
                    telephone = '".$this->db->escape($data['telephone'])."',
                    fax = '".$this->db->escape($data['fax'])."',
                    salt = '".$this->db->escape($salt_key)."', 
                    password = '".$this->db->escape(sha1($salt_key.sha1($salt_key.sha1($data['password']))))."',
                    newsletter = '".(int)$data['newsletter']."',
                    customer_group_id = '".(int)$data['customer_group_id']."',
                    approved = '".(int)$data['approved']."',
                    status = '".(int)$data['status']."'".$key_sql.",
                    ip = '".$this->db->escape($data['ip'])."',
                    data = '".$this->db->escape(serialize($data['data']))."',
                    date_added = NOW()";
        $this->db->query($sql);
        $customer_id = $this->db->getLastId();

        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $key_sql = ", key_id = '".(int)$data['key_id']."'";
        }
        $this->db->query("INSERT INTO ".$this->db->table("addresses")." 
                          SET 	customer_id = '".(int)$customer_id."', 
                                firstname = '".$this->db->escape($data['firstname'])."', 
                                lastname = '".$this->db->escape($data['lastname'])."', 
                                company = '".$this->db->escape($data['company'])."', 
                                address_1 = '".$this->db->escape($data['address_1'])."', 
                                address_2 = '".$this->db->escape($data['address_2'])."', 
                                city = '".$this->db->escape($data['city'])."', 
                                postcode = '".$this->db->escape($data['postcode'])."', 
                                country_id = '".(int)$data['country_id']."'".
            $key_sql.",
                                zone_id = '".(int)$data['zone_id']."'");

        $address_id = $this->db->getLastId();
        $this->db->query("UPDATE ".$this->db->table("customers")."
                            SET address_id = '".(int)$address_id."'
                            WHERE customer_id = '".(int)$customer_id."'");

        if (!$data['approved']) {
            $language = new ALanguage($this->registry);
            $language->load($language->language_details['directory']);
            $language->load('account/create');

            if ($data['subscriber']) {
                //notify administrator of pending subscriber approval
                $msg_text = sprintf($language->get('text_pending_subscriber_approval'), $data['firstname'].' '.$data['lastname'], $customer_id);
            } else {
                //notify administrator of pending customer approval
                $msg_text = sprintf($language->get('text_pending_customer_approval'), $data['firstname'].' '.$data['lastname'], $customer_id);
            }
            $msg = new AMessage();
            $msg->saveNotice($language->get('text_new_customer'), $msg_text);
        }

        //enable notification setting for newsletter via email
        if ($data['newsletter']) {
            $sql = "INSERT INTO ".$this->db->table('customer_notifications')."
                        (customer_id, sendpoint, protocol, status, date_added)
                    VALUES
                    ('".$customer_id."',
                    'newsletter',
                    'email',
                    '1',
                    NOW());";
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
        $message_arr = array(
            1 => array(
                'message' => sprintf($language->get($lang_key), $customer_id, $data['firstname'].' '.$data['lastname']),
            ),
        );
        $this->im->send('new_customer', $message_arr, $templateId, $data);
        return $customer_id;
    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function editCustomer($data)
    {
        if (!$data) {
            return false;
        }

        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'customers');
            $key_sql = ", key_id = '".(int)$data['key_id']."'";
        }

        $data['store_name'] = $this->config->get('store_name');

        $language = new ALanguage($this->registry);
        $language->load($language->language_details['directory']);
        $language->load('common/im');

        //update login only if needed
        $loginname = '';
        if (!empty($data['loginname']) && trim($data['loginname']) != $this->customer->getLoginName()){
            $loginname = " loginname = '".$this->db->escape($data['loginname'])."', ";
            $message_arr = array(
                0 => array('message' => sprintf($language->get('im_customer_account_update_login_to_customer'), $data['loginname'])),
            );
            $data['old_loginname'] = $this->customer->getLoginName();
            $this->im->send('customer_account_update', $message_arr, 'storefront_customer_account_update', $data);
        }

        //get existing data and compare
        $current_rec = $this->getCustomer((int)$this->customer->getId());
        foreach ($current_rec as $rec => $val) {
            if ($rec == 'email' && $val != $data['email']) {
                $message_arr = array(
                    0 => array('message' => sprintf($language->get('im_customer_account_update_email_to_customer'), $data['email'])),
                );
                $data['old_email'] = $val;
                $this->im->send('customer_account_update', $message_arr, 'storefront_customer_account_update', $data);
            }
        }

        //trim and remove double whitespaces
        foreach (array('firstname', 'lastname') as $f) {
            $data[$f] = str_replace('  ', ' ', trim($data[$f]));
        }

        if(
            trim($data['firstname']) != $this->customer->getFirstName()
            ||
            trim($data['lastname']) != $this->customer->getLastName()
            ||
            trim($data['telephone']) != $this->customer->getTelephone()
            ||
            trim($data['fax']) != $this->customer->getFax()
        ){
            $message_arr = array(
                0 => array('message' => $language->get('im_customer_account_update_text_to_customer')),
            );
            $this->im->send('customer_account_update', $message_arr, 'storefront_customer_account_update', $data);
        }

        $sql = "UPDATE ".$this->db->table("customers")."
                SET   firstname = '".$this->db->escape($data['firstname'])."',
                      lastname = '".$this->db->escape($data['lastname'])."', ".$loginname."
                      email = '".$this->db->escape($data['email'])."',
                      telephone = '".$this->db->escape($data['telephone'])."',
                      fax = '".$this->db->escape($data['fax'])."'"
            .$key_sql.
            " WHERE customer_id = '".(int)$this->customer->getId()."'";
        $this->db->query($sql);
        return true;
    }

    /**
     * @param array $data
     * @param int   $customer_id
     *
     * @return bool
     */
    public function editCustomerNotifications($data, $customer_id = 0)
    {
        if (!$data) {
            return false;
        }

        if (!$customer_id) {
            $customer_id = (int)$this->customer->getId();
        }

        $upd = array();
        //get only active IM drivers
        $im_protocols = $this->im->getProtocols();
        foreach ($im_protocols as $protocol) {
            if (isset($data[$protocol])) {
                $upd[$protocol] = "`".$this->db->escape($protocol)."` = '".$this->db->escape($data[$protocol])."'";
            }
        }
        //get all columns
        $sql = "SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = '".DB_DATABASE."' 
                    AND TABLE_NAME = '".$this->db->table("customers")."'";
        $result = $this->db->query($sql);
        $columns = array();
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
            $key_sql = ", key_id = '".(int)$data['key_id']."'";
        }

        $sql = "UPDATE ".$this->db->table('customers')."
                SET ".implode(', ', $upd)."\n"
            .$key_sql.
            " WHERE customer_id = '".$customer_id."'";
        $this->db->query($sql);
        return true;
    }

    /**
     * @return array
     */
    public function getCustomerNotificationSettings()
    {
        //get only active IM drivers
        $im_protocols = $this->im->getProtocols();
        $im_settings = array();
        $sql = "SELECT *
                FROM ".$this->db->table('customer_notifications')."
                WHERE customer_id = ".(int)$this->customer->getId();
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            if (!in_array($row['protocol'], $im_protocols)) {
                continue;
            }
            $im_settings[$row['sendpoint']][$row['protocol']] = (int)$row['status'];
        }
        return $im_settings;
    }

    /**
     * @param array $settings
     *
     * @return bool|null
     */
    public function saveCustomerNotificationSettings($settings)
    {

        $customer_id = (int)$this->customer->getId();
        //do not save settings for guests
        if (!$customer_id) {
            return null;
        }

        $sendpoints = array_keys($this->im->sendpoints);
        $im_protocols = $this->im->getProtocols();

        $update = array();
        foreach ($sendpoints as $sendpoint) {
            foreach ($im_protocols as $protocol) {
                $update[$sendpoint][$protocol] = (int)$settings[$sendpoint][$protocol];
            }
        }

        if ($update) {
            $sql = "DELETE FROM ".$this->db->table('customer_notifications')."
                    WHERE customer_id = ".$customer_id;
            $this->db->query($sql);

            foreach ($update as $sendpoint => $row) {
                foreach ($row as $protocol => $status) {
                    $sql = "INSERT INTO ".$this->db->table('customer_notifications')."
                                (customer_id, sendpoint, protocol, status, date_added)
                            VALUES
                            ('".$customer_id."',
                            '".$this->db->escape($sendpoint)."',
                            '".$this->db->escape($protocol)."',
                            '".(int)$status."',
                            NOW());";
                    $this->db->query($sql);
                }
            }
            //for newsletter subscription do changes inside customers table
            //if at least one protocol enabled - set 1, otherwise - 0
            if (has_value($update['newsletter'])) {
                $newsletter_status = 0;
                foreach ($update['newsletter'] as $protocol => $status) {
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
     * @throws AException
     */
    public function editPassword($loginname, $password)
    {
        $salt_key = genToken(8);
        $this->db->query("UPDATE ".$this->db->table("customers")."
                            SET
                                salt = '".$this->db->escape($salt_key)."', 
                                password = '".$this->db->escape(sha1($salt_key.sha1($salt_key.sha1($password))))."'
                            WHERE loginname = '".$this->db->escape($loginname)."'");
        //send IM
        $sql = "SELECT customer_id, firstname, lastname
                FROM ".$this->db->table("customers")."
                WHERE loginname = '".$this->db->escape($loginname)."'";
        $result = $this->db->query($sql);
        $customer_id = $result->row['customer_id'];
        if ($customer_id) {
            $language = new ALanguage($this->registry);
            $language->load($language->language_details['directory']);
            $language->load('common/im');
            $message_arr = array(
                0 => array('message' => $language->get('im_customer_account_update_password_to_customer')),
            );
            $this->im->send('customer_account_update', $message_arr, 'storefront_password_reset_notify', [
                'customer_id' => $customer_id,
                'loginname' => $loginname,
                'firstname' => $result->row['firstname'],
                'lastname' => $result->row['lastname'],
                'store_name' => $this->config->get('store_name')
            ]);
        }
    }

    /**
     * @param int $newsletter
     * @param int $customer_id - optional parameter for unsubscribe page!
     */
    public function editNewsletter($newsletter, $customer_id = 0)
    {
        $customer_id = (int)$customer_id ? (int)$customer_id : (int)$this->customer->getId();
        $this->db->query(
            "UPDATE ".$this->db->table("customers")."
                SET newsletter = '".(int)$newsletter."'
                WHERE customer_id = '".$customer_id."'"
        );
    }

    /**
     * @param $customer_id
     * @param $status
     *
     * @return bool
     */
    public function editStatus($customer_id, $status)
    {
        $customer_id = (int)$customer_id;
        $status = (int)$status;
        if (!$customer_id) {
            return false;
        }
        $this->db->query("UPDATE ".$this->db->table("customers")."
                           SET status = '".(int)$status."'
                           WHERE customer_id = '".$customer_id."'");
        return true;
    }

    /**
     * @param $customer_id
     * @param $data
     *
     * @return bool
     */
    public function updateOtherData($customer_id, $data)
    {
        $customer_id = (int)$customer_id;
        if (!$customer_id) {
            return false;
        }
        $this->db->query("UPDATE ".$this->db->table("customers")."
                           SET data = '".$this->db->escape(serialize($data))."'
                           WHERE customer_id = '".$customer_id."'");
        return true;
    }

    /**
     * @param int $customer_id
     *
     * @return array
     */
    public function getCustomer($customer_id)
    {
        $query = $this->db->query(
            "SELECT *
                FROM ".$this->db->table("customers")."
                WHERE customer_id = '".(int)$customer_id."'");
        $result_row = $this->dcrypt->decrypt_data($query->row, 'customers');
        $result_row['data'] = unserialize($result_row['data']);
        return $result_row;
    }

    /**
     * @param string $email
     * @param bool   $no_subscribers - sign that needed list without subscribers
     *
     * @return int
     */
    public function getTotalCustomersByEmail($email, $no_subscribers = true)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM ".$this->db->table("customers")."
                WHERE LOWER(`email`) = LOWER('".$this->db->escape($email)."')";
        if ($no_subscribers) {
            $sql .= " AND customer_group_id NOT IN
                            (SELECT customer_group_id
                            FROM ".$this->db->table('customer_groups')."
                            WHERE `name` = 'Newsletter Subscribers')";
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    /**
     * Detect type of login configured and select customer
     *
     * @param string $login
     *
     * @return array
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
     */
    public function getCustomerByEmail($email)
    {
        //assuming that data is not encrypted. Can not call these otherwise
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("customers")."
                                    WHERE LOWER(`email`) = LOWER('".$this->db->escape($email)."')");
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
     */
    public function getCustomerByLoginname($loginname)
    {
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("customers")."
                                    WHERE LOWER(`loginname`) = LOWER('".$this->db->escape($loginname)."')");
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
     */
    public function getCustomerByLoginnameAndEmail($loginname, $email)
    {
        $result_row = $this->getCustomerByLoginname($loginname);
        //validate it is correct row by matching decrypted email;
        if (strtolower($result_row['email']) == strtolower($email)) {
            return $result_row;
        } else {
            return array();
        }
    }

    /**
     * @param string $lastname
     * @param string $email
     *
     * @return array
     */
    public function getCustomerByLastnameAndEmail($lastname, $email)
    {
        $query = $this->db->query("SELECT *
                                    FROM ".$this->db->table("customers")."
                                    WHERE LOWER(`lastname`) = LOWER('".$this->db->escape($lastname)."')");
        //validate if we have row with matching decrypted email;
        $result_row = array();
        foreach ($query->rows as $result) {
            if (strtolower($email) == strtolower($this->dcrypt->decrypt_field($result['email'], $result['key_id']))) {
                $result_row = $result;
                break;
            }
        }

        if (count($result_row)) {
            $result_row = $this->dcrypt->decrypt_data($result_row, 'customers');

            return $result_row;
        } else {
            return array();
        }
    }

    /**
     * @param string $loginname
     *
     * @return bool
     */
    public function is_unique_loginname($loginname)
    {
        if (empty($loginname)) {
            return false;
        }
        $query = $this->db->query("SELECT COUNT(*) AS total
                                   FROM ".$this->db->table("customers")."
                                   WHERE LOWER(`loginname`) = LOWER('".$loginname."')");
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
        $this->error = array();
        //If captcha enabled, validate
        if ($this->config->get('config_account_create_captcha')) {
            if ($this->config->get('config_recaptcha_secret_key')) {
                require_once DIR_VENDORS.'/google_recaptcha/autoload.php';
                $recaptcha = new \ReCaptcha\ReCaptcha($this->config->get('config_recaptcha_secret_key'));
                $resp = $recaptcha->verify($data['g-recaptcha-response'],
                    $this->request->getRemoteIP());
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
            //validate only if email login is not allowed
            $login_name_pattern = '/^[\w._-]+$/i';
            if (mb_strlen($data['loginname']) < 5
                || mb_strlen($data['loginname']) > 64
                || !preg_match($login_name_pattern, $data['loginname'])
            ) {
                $this->error['loginname'] = $this->language->get('error_loginname');
                //validate uniqueness of login name
            } else {
                if (!$this->is_unique_loginname($data['loginname'])) {
                    $this->error['loginname'] = $this->language->get('error_loginname_notunique');
                }
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
            $this->error['warning'] = $this->language->get('error_exists');
        }

        if (mb_strlen($data['telephone']) > 32) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if ((mb_strlen($data['address_1']) < 3) || (mb_strlen($data['address_1']) > 128)) {
            $this->error['address_1'] = $this->language->get('error_address_1');
        }

        if ((mb_strlen($data['city']) < 3) || (mb_strlen($data['city']) > 128)) {
            $this->error['city'] = $this->language->get('error_city');
        }
        if ((mb_strlen($data['postcode']) < 3) || (mb_strlen($data['postcode']) > 128)) {
            $this->error['postcode'] = $this->language->get('error_postcode');
        }

        if ($data['country_id'] == 'FALSE') {
            $this->error['country'] = $this->language->get('error_country');
        }

        if ($data['zone_id'] == 'FALSE') {
            $this->error['zone'] = $this->language->get('error_zone');
        }

        //check password length considering html entities (special case for characters " > < & )
        $pass_len = mb_strlen(htmlspecialchars_decode($data['password']));
        if ($pass_len < 4 || $pass_len > 20) {
            $this->error['password'] = $this->language->get('error_password');
        }

        if ($data['confirm'] != $data['password']) {
            $this->error['confirm'] = $this->language->get('error_confirm');
        }

        if ($this->config->get('config_account_id')) {
            $this->load->model('catalog/content');

            $content_info = $this->model_catalog_content->getContent($this->config->get('config_account_id'));

            if ($content_info) {
                if (!isset($data['agree'])) {
                    $this->error['warning'] = sprintf($this->language->get('error_agree'), $content_info['title']);
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

        $this->extensions->hk_ValidateData($this);

        return $this->error;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function validateSubscribeData($data)
    {
        $this->error = array();

        if ($this->config->get('config_recaptcha_secret_key')) {
            require_once DIR_VENDORS.'/google_recaptcha/autoload.php';
            $recaptcha = new \ReCaptcha\ReCaptcha($this->config->get('config_recaptcha_secret_key'));
            $resp = $recaptcha->verify($data['g-recaptcha-response'],
                $this->request->getRemoteIP());
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

        $this->extensions->hk_ValidateData($this);

        return $this->error;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function validateEditData($data)
    {
        $this->error = array();

        //validate loginname only if cannot match email and if it is set. Edit of loginname not allowed
        if ($this->config->get('prevent_email_as_login') && isset($data['loginname'])) {
            //validate only if email login is not allowed
            $login_name_pattern = '/^[\w._-]+$/i';
            if ((mb_strlen($data['loginname']) < 5) || (mb_strlen($data['loginname']) > 64)
                || (!preg_match($login_name_pattern, $data['loginname']))
            ) {
                $this->error['loginname'] = $this->language->get('error_loginname');
                //validate uniqueness of login name
            } else {
                if (!$this->is_unique_loginname($data['loginname'])) {
                    $this->error['loginname'] = $this->language->get('error_loginname_notunique');
                }
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

        if (($this->customer->getEmail() != $data['email']) && $this->getTotalCustomersByEmail($data['email'])) {
            $this->error['warning'] = $this->language->get('error_exists');
        }

        if ($data['telephone'] && (mb_strlen($data['telephone']) < 3 || mb_strlen($data['telephone']) > 32)) {
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

        $this->extensions->hk_ValidateData($this);

        return $this->error;
    }

    /**
     * @return int
     */
    public function getTotalTransactions()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total
                                   FROM `".$this->db->table("customer_transactions")."`
                                   WHERE customer_id = '".(int)$this->customer->getId()."'");

        return (int)$query->row['total'];
    }

    /**
     * @param int $start
     * @param int $limit
     *
     * @return mixed
     */
    public function getTransactions($start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        $query = $this->db->query("SELECT 
                                        t.customer_transaction_id,
                                        t.order_id,
                                        t.section,
                                        t.credit,
                                        t.debit,
                                        t.transaction_type,
                                        t.description,
                                        t.date_added
                                FROM `".$this->db->table("customer_transactions")."` t
                                WHERE customer_id = '".(int)$this->customer->getId()."'
                                ORDER BY t.date_added DESC
                                LIMIT ".(int)$start.",".(int)$limit);

        return $query->rows;
    }

    public function getSubscribersCustomerGroupId()
    {
        $query = $this->db->query(
            "SELECT customer_group_id
            FROM `".$this->db->table("customer_groups")."`
            WHERE `name` = 'Newsletter Subscribers'
            LIMIT 0,1");
        $result = !$query->row['customer_group_id'] ? (int)$this->config->get('config_customer_group_id') : $query->row['customer_group_id'];

        return $result;
    }

    /**
     * @param string $email
     * @param bool $activated
     *
     * @return bool
     */
    public function sendWelcomeEmail($email, $activated)
    {
        if (!$email) {
            return null;
        }
        //build welcome email in text format
        $login_url = $this->html->getSecureURL('account/login');
        $this->language->load('mail/account_create');

        if ($activated) {
            $template = 'storefront_welcome_email_activated';
        } else {
            $template = 'storefront_welcome_email_approval';
        }

        $this->data['mail_template_data']['login_url'] = $login_url;

        $config_mail_logo = $this->config->get('config_mail_logo');
        $config_mail_logo = !$config_mail_logo ? $this->config->get('config_logo') : $config_mail_logo;
        if ($config_mail_logo) {
            if (is_numeric($config_mail_logo)) {
                $r = new AResource('image');
                $resource_info = $r->getResource($config_mail_logo);
                if ($resource_info) {
                    $this->data['mail_template_data']['logo_html'] = html_entity_decode($resource_info['resource_code'], ENT_QUOTES, 'UTF-8');
                }
            } else {
                $this->data['mail_template_data']['logo_uri'] = 'cid:'
                    .md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                    .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION);
            }
        }
        $this->data['mail_template_data']['config_mail_logo'] = $config_mail_logo;
        //backward compatibility. TODO: remove this in 2.0
        if ($this->data['mail_template_data']['logo_uri']) {
            $this->data['mail_template_data']['logo'] = $this->data['mail_template_data']['logo_uri'];
        } else {
            $this->data['mail_template_data']['logo'] = $this->config->get('config_mail_logo');
        }

        $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
        $this->data['mail_template_data']['store_url'] = $this->config->get('config_url');
        $this->data['mail_template_data']['text_project_label'] = project_base();

        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_account_welcome_mail');


        $this->_send_email($email, $template, $this->data['mail_template_data']);

        return true;
    }

    /**
     * @param int $customer_id
     *
     * @return bool
     */
    public function emailActivateLink($customer_id)
    {
        if (!$customer_id) {
            return null;
        }
        $customer_data = $this->getCustomer($customer_id);

        //encrypt token and data
        $enc = new AEncryption($this->config->get('encryption_key'));
        $code = genToken();
        //store activation code
        $customer_data['data']['email_activation'] = $code;
        $this->updateOtherData($customer_id, $customer_data['data']);

        $ac = $enc->encrypt($customer_id.'::'.$code);
        $activate_url = $this->html->getSecureURL('account/login', '&ac='.$ac);

        //build welcome email
        $this->language->load('mail/account_create');

        $this->data['mail_template_data']['activate_url'] = '<a href="'.$activate_url.'">'.$activate_url.'</a>';

        $config_mail_logo = $this->config->get('config_mail_logo');
        $config_mail_logo = !$config_mail_logo ? $this->config->get('config_logo') : $config_mail_logo;
        if ($config_mail_logo) {
            if (is_numeric($config_mail_logo)) {
                $r = new AResource('image');
                $resource_info = $r->getResource($config_mail_logo);
                if ($resource_info) {
                    $this->data['mail_template_data']['logo_html'] = html_entity_decode($resource_info['resource_code'],
                        ENT_QUOTES, 'UTF-8');
                }
            } else {
                $this->data['mail_template_data']['logo_uri'] = 'cid:'
                    .md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                    .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION);
            }
        }

        $this->data['mail_template_data']['config_mail_logo'] = $config_mail_logo;

        //backward compatibility. TODO: remove this in 2.0
        if ($this->data['mail_template_data']['logo_uri']) {
            $this->data['mail_template_data']['logo'] = $this->data['mail_template_data']['logo_uri'];
        } else {
            $this->data['mail_template_data']['logo'] = $config_mail_logo;
        }

        $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
        $this->data['mail_template_data']['store_url'] = $this->config->get('config_url');
        $this->data['mail_template_data']['text_project_label'] = project_base();


        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_account_activation_mail');


        $this->_send_email($customer_data['email'], 'storefront_send_activate_link',$this->data['mail_template_data']);

        return true;
    }

    /**
     * @param string $email
     * @param array $data
     */
    protected function _send_email($email, $template, $data)
    {
        $mail = new AMail($this->config);
        $mail->setTo($email);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));

        $mail->setTemplate($template, $data);

        if (is_file(DIR_RESOURCE.$data['config_mail_logo'])) {
            $mail->addAttachment(DIR_RESOURCE.$data['config_mail_logo'],
                md5(pathinfo($data['config_mail_logo'], PATHINFO_FILENAME))
                .'.'.pathinfo($data['config_mail_logo'], PATHINFO_EXTENSION));
        }
        $mail->send();
    }

    /**
     * @param string $ot - order token
     *
     * @return array
     * @throws AException
     */
    public function parseOrderToken($ot)
    {
        if (!$ot || !$this->config->get('config_guest_checkout')) {
            return array();
        }

        //try to decrypt order token
        $enc = new AEncryption($this->config->get('encryption_key'));
        $decrypted = $enc->decrypt($ot);
        list($order_id, $email) = explode('::', $decrypted);

        $order_id = (int)$order_id;
        if (!$decrypted || !$order_id || !$email) {
            return array();
        }
        $this->load->model('account/order');
        $order_info = $this->model_account_order->getOrder($order_id, '', 'view');

        //compare emails
        if ($order_info['email'] != $email) {
            return array();
        }

        return array($order_id, $email);
    }
}
