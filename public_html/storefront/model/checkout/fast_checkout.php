<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ModelCheckoutFastCheckout extends Model
{
    public $data = [];

    /**
     * @param int $order_id
     * @param int $customer_id
     *
     * @throws AException
     */
    public function updateOrderCustomer($order_id, $customer_id)
    {
        $order_id = (int)$order_id;
        $customer_id = (int)$customer_id;

        $sql = "UPDATE ".$this->db->table('orders')."
                SET customer_id=".(int)$customer_id."
                WHERE order_id =".(int)$order_id;
        $this->db->query($sql);
    }

    /**
     * Method adds customer into database with or without address
     *
     * @param $data
     *
     * @return int
     * @throws AException
     */
    public function addCustomer($data)
    {
        //if address present - use core model
        if ($data['address_1'] || $data['address_2']) {
            /** @var ModelAccountCustomer $mdl */
            $mdl = $this->load->model('account/customer');
            return $mdl->addCustomer($data);
        }

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
                AND customer_group_id 
                    IN (SELECT customer_group_id
                        FROM ".$this->db->table('customer_groups')."
                        WHERE `name` = 'Newsletter Subscribers')"
        );
        foreach ($subscriber->rows as $row) {
            $this->db->query(
                "DELETE 
                FROM ".$this->db->table("customers")." 
                WHERE customer_id = '".(int)$row['customer_id']."'"
            );
            $this->db->query(
                "DELETE 
                FROM ".$this->db->table("addresses")." 
                WHERE customer_id = '".(int)$row['customer_id']."'"
            );
        }

        $salt_key = genToken(8);
        $sql = "INSERT INTO ".$this->db->table("customers")."
                SET store_id = '".(int)$this->config->get('config_store_id')."',
                    loginname = '".$this->db->escape($data['loginname'])."',
                    firstname = '".$this->db->escape($data['firstname'])."',
                    lastname = '".$this->db->escape($data['lastname'])."',
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
                    `data` = '".$this->db->escape(serialize($data['data']))."',
                    date_added = NOW()";
        $this->db->query($sql);
        $customer_id = $this->db->getLastId();

        if (!$data['approved']) {
            $language = new ALanguage($this->registry);
            $language->load('account/create');

            //notify administrator of pending customer approval
            $msg_text = sprintf(
                $language->get('text_pending_customer_approval'),
                $data['firstname'].' '.$data['lastname'],
                $customer_id
            );
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
        $language->load('common/im');
        $message_arr = [
            1 => [
                'message' => sprintf($language->get('im_new_customer_text_to_admin'), $customer_id),
            ],
        ];
        $this->im->send('new_customer', $message_arr);

        return $customer_id;
    }

    /**
     * @param int $order_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function updateOrderDetails($order_id, $data = [])
    {
        $order_id = (int)$order_id;
        if (!$order_id) {
            return false;
        }

        $allowed = [
            'telephone',
            'comment'
        ];
        $upd = [];
        foreach ($allowed as $field_name) {
            if (isset($data[$field_name])) {
                $upd[] = "`".$field_name."` = '".$this->db->escape($data[$field_name])."' ";
            }
        }

        $sql = "UPDATE ".$this->db->table('orders')."
                SET ".implode(', ', $upd)."
                WHERE order_id = ".$order_id." AND order_status_id = 0";
        $this->db->query($sql);
        return true;

    }

    /**
     * @param array $data
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function sendEmailActivation($data = [])
    {
        if (!$data || !is_array($data)) {
            return false;
        }

        //build confirmation email
        $language = new ALanguage($this->registry, $data['language_code']);
        $language->setCurrentLanguage();
        $languageId = $language->getLanguageID();
        $language->load('checkout/fast_checkout');
        $language->load('mail/account_create');

        //build welcome email in text format
        $this->language->load('mail/account_create');
        $this->data['mail_template_data'] = [
            'store_name' => $this->config->get('store_name'),
            'login_url'  => $this->html->getSecureURL('account/login'),
            'login'      => $data['loginname'],
            'password'   => $data['password'],
        ];

        $mailLogo = $this->config->get('config_mail_logo_'.$languageId)
                    ?: $this->config->get('config_mail_logo');
        $mailLogo = $mailLogo ?: $this->config->get('config_logo_'.$languageId);
        $mailLogo = $mailLogo ?: $this->config->get('config_logo');

        if ($mailLogo) {
            $result = getMailLogoDetails($mailLogo);
            $this->data['mail_template_data']['logo_uri'] = $result['uri'];
            $this->data['mail_template_data']['logo_html'] = $result['html'];
        }

        $template = 'fast_checkout_welcome_email_guest_registration';

        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_fast_checkout_welcome_mail');

        $mail = new AMail($this->config);
        $mail->setTo($data['email']);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));
        $mail->setTemplate($template, $this->data['mail_template_data']);
        if (is_file(DIR_RESOURCE.$mailLogo)) {
            $mail->addAttachment(DIR_RESOURCE.$mailLogo,
                md5(pathinfo($mailLogo, PATHINFO_FILENAME))
                .'.'.pathinfo($mailLogo, PATHINFO_EXTENSION));
        }
        $mail->send(true);
        return true;
    }

    /**
     * @param array $order_data
     * @param array $downloadInfo
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function emailDownloads($order_data, $downloadInfo)
    {
        if (empty($downloadInfo) || !is_array($downloadInfo) || empty($order_data) || !is_array($order_data)) {
            return false;
        }

        //build confirmation email
        $this->language->load('checkout/fast_checkout');
        $languageId = $this->language->getContentLanguageID() ? : $this->language->getLanguageID();

        $subject = sprintf($this->language->get('fast_checkout_download_subject'), $this->config->get('store_name'));

        if ($downloadInfo['count'] == 1) {
            $text_email_download = $this->language->get('fast_checkout_email_start_download');
            $text_email_download_link = $this->language->get('fast_checkout_button_start_download');
        } else {
            $text_email_download = $this->language->get('fast_checkout_email_order_downloads');
            $text_email_download_link = $this->language->get('fast_checkout_text_order_downloads');
        }
        $email_download_link = $downloadInfo['download_url'];

        $this->data['mail_plain_text'] = $text_email_download."\n\n";
        $this->data['mail_plain_text'] .= $email_download_link."\n\n";
        $this->data['mail_plain_text'] .= $this->language->get('fast_checkout_text_thank_you')."\n";
        $this->data['mail_plain_text'] .= $this->config->get('store_name');

        //build HTML message with the template
        $this->data['mail_template_data']['text_thanks'] = $this->language->get('fast_checkout_text_thank_you');
        $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
        $this->data['mail_template_data']['store_url'] = $this->config->get('config_url').$this->config->get('seo_prefix');
        $this->data['mail_template_data']['text_email_download'] = $text_email_download;
        $this->data['mail_template_data']['text_email_download_link'] = $text_email_download_link;
        $this->data['mail_template_data']['email_download_link'] = $email_download_link;
        $this->data['mail_template_data']['text_project_label'] = project_base();

        $this->data['mail_template'] = 'mail/order_download.tpl';

        $mailLogo = $this->config->get('config_mail_logo_'.$languageId)
                    ?: $this->config->get('config_mail_logo');
        $mailLogo = $mailLogo ?: $this->config->get('config_logo_'.$languageId);
        $mailLogo = $mailLogo ?: $this->config->get('config_logo');

        if ($mailLogo) {
            $result = getMailLogoDetails($mailLogo);
            $this->data['mail_template_data']['logo_uri'] = $result['uri'];
            $this->data['mail_template_data']['logo_html'] = $result['html'];
        }

        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_fast_checkout_guest_download_mail');

        $view = new AView($this->registry, 0);
        $view->batchAssign($this->data['mail_template_data']);
        $html_body = $view->fetch($this->data['mail_template']);

        $mail = new AMail($this->config);
        $mail->setTo($order_data['email']);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));
        $mail->setSubject($subject);
        $mail->setText(html_entity_decode($this->data['mail_plain_text'], ENT_QUOTES, 'UTF-8'));
        if (is_file(DIR_RESOURCE.$mailLogo)) {
            $mail->addAttachment(DIR_RESOURCE.$mailLogo,
                md5(pathinfo($mailLogo, PATHINFO_FILENAME))
                .'.'.pathinfo($mailLogo, PATHINFO_EXTENSION));
        }
        $mail->setHtml($html_body);
        $mail->send();
        return true;
    }

    /**
     * @param string $ot - order token
     *
     * @return array
     * @throws AException
     */
    public function parseOrderToken($ot)
    {
        if (!$ot) {
            return [];
        }

        //try to decrypt order token
        $enc = new AEncryption($this->config->get('encryption_key'));
        $decrypted = $enc->decrypt($ot);
        list($order_id, $email, $sec_token) = explode('::', $decrypted);

        $order_id = (int)$order_id;
        if (!$decrypted || !$order_id || !$email || !$sec_token) {
            return [];
        }
        /** @var ModelAccountOrder $mdl */
        $mdl = $this->load->model('account/order');
        $order_info = $mdl->getOrder($order_id, '', 'view');

        //compare emails
        if ($order_info['email'] != $email) {
            return [];
        }
        //compare security token
        if ($sec_token != $this->getGuestToken($order_id)) {
            return [];
        }
        return [$order_id, $email, $sec_token];
    }

    /**
     * Get order security token for guest
     * @param $order_id
     *
     * @return string
     * @throws AException
     */
    public function getGuestToken($order_id)
    {
        $order_id = (int)$order_id;

        $sql = "SELECT *
                FROM ".$this->db->table('order_data')." od
                WHERE `type_id` in ( SELECT DISTINCT type_id
                                     FROM ".$this->db->table('order_data_types')."
                                     WHERE `name`='guest_token' )
                    AND order_id = '".$order_id."'";
        $query = $this->db->query($sql);
        return $query->rows[0]['data'];
    }

    //Save order security token for guest

    /**
     * @param int $order_id
     * @param string $token
     *
     * @throws AException
     */
    public function saveGuestToken($order_id, $token)
    {
        $order_id = (int)$order_id;

        $sql = "SELECT DISTINCT `type_id`
                FROM ".$this->db->table('order_data_types')."
                WHERE `name`='guest_token'";
        $result = $this->db->query($sql);
        $type_id = $result->rows[0]['type_id'];
        if ($type_id) {
            $sql = "REPLACE INTO ".$this->db->table('order_data')."
                    (`order_id`, `type_id`, `data`, `date_added`)
                    VALUES (".(int)$order_id.", ".(int)$type_id.", '".$this->db->escape($token)."', NOW() )";
            $this->db->query($sql);
        }
    }

    /**
     * @param int $order_id
     * @param int $customer_id
     *
     * @return int
     * @throws AException
     */
    public function getTotalOrderDownloads($order_id, $customer_id)
    {
        if (method_exists($this->download, 'getTotalOrderDownloads')) {
            return $this->download->getTotalOrderDownloads($order_id, $customer_id);
        } else {
            return sizeof($this->getCustomerOrderDownloads($order_id, $customer_id));
        }
    }
}