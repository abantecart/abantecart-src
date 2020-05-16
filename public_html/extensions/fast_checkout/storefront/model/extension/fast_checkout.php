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
 * Class ModelExtensionFastCheckout
 *
 * @property ModelAccountOrder    $model_account_order
 * @property ModelAccountCustomer $model_account_customer
 */
class ModelExtensionFastCheckout extends Model
{
    public $data = array();

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
     */
    public function addCustomer($data)
    {
        //if address present - use core model
        if ($data['address_1'] || $data['address_2']) {
            $this->load->model('account/customer');
            return $this->model_account_customer->addCustomer($data);
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
        $subscriber = $this->db->query("SELECT customer_id
										FROM ".$this->db->table("customers")."
										WHERE LOWER(`email`) = LOWER('".$this->db->escape($data['email'])."')
											AND customer_group_id IN (SELECT customer_group_id
																		  FROM ".$this->db->table('customer_groups')."
																		  WHERE `name` = 'Newsletter Subscribers')");
        foreach ($subscriber->rows as $row) {
            $this->db->query("DELETE FROM ".$this->db->table("customers")." WHERE customer_id = '"
                .(int)$row['customer_id']."'");
            $this->db->query("DELETE FROM ".$this->db->table("addresses")." WHERE customer_id = '"
                .(int)$row['customer_id']."'");
        }

        $salt_key = genToken(8);
        $sql = "INSERT INTO ".$this->db->table("customers")."
			  SET	store_id = '".(int)$this->config->get('config_store_id')."',
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
					data = '".$this->db->escape(serialize($data['data']))."',
					date_added = NOW()";
        $this->db->query($sql);
        $customer_id = $this->db->getLastId();

        if (!$data['approved']) {
            $language = new ALanguage($this->registry);
            $language->load('account/create');

            //notify administrator of pending customer approval
            $msg_text =
                sprintf($language->get('text_pending_customer_approval'), $data['firstname'].' '.$data['lastname'],
                    $customer_id);
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
        $message_arr = array(
            1 => array(
                'message' => sprintf($language->get('im_new_customer_text_to_admin'), $customer_id),
            ),
        );
        $this->im->send('new_customer', $message_arr);

        return $customer_id;
    }

    /**
     * @param $data
     *
     * @return int
     */
    public function addAddress($data = array())
    {
        if (!$data || !(int)$data['customer_id']) {
            return false;
        }
        //encrypt customer data
        $key_sql = '';
        if ($this->dcrypt->active) {
            $data = $this->dcrypt->encrypt_data($data, 'addresses');
            $key_sql = ", key_id = '".(int)$data['key_id']."'";
        }

        $this->db->query("INSERT INTO `".$this->db->table("addresses")."`
						  SET customer_id = '".(int)$data['customer_id']."',
							company = '".$this->db->escape($data['company'])."',
							firstname = '".$this->db->escape($data['firstname'])."',
							lastname = '".$this->db->escape($data['lastname'])."',
							address_1 = '".$this->db->escape($data['address_1'])."',
							address_2 = '".$this->db->escape($data['address_2'])."',
							postcode = '".$this->db->escape($data['postcode'])."',
							city = '".$this->db->escape($data['city'])."',
							zone_id = '".(int)$data['zone_id']."',
							country_id = '".(int)$data['country_id']."'".$key_sql);

        $address_id = $this->db->getLastId();
        return $address_id;
    }

    public function updateOrderDetails($order_id, $data = array())
    {
        $order_id = (int)$order_id;
        if (!$order_id) {
            return false;
        }

        $allowed = array(
            'telephone',
            'comment'
        );
        $upd = array();
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
     * @param int $order_id
     * @param int $customer_id
     *
     * @return array
     */
    public function getCustomerOrderDownloads($order_id, $customer_id)
    {
        $customer_id = (int)$customer_id;
        $order_id = (int)$order_id;
        if (!$order_id) {
            return array();
        }
        $sql = "SELECT o.order_id,
					  o.order_status_id,
					  od.download_id,
					  od.status,
					  od.date_added,
					  od.order_download_id,
					  d.activate,
					  od.activate_order_status_id,
					  od.name,
					  od.filename,
					  od.mask,
					  od.remaining_count,
					  od.expire_date,
					  op.product_id,
					  o.email
			   FROM ".$this->db->table("order_downloads")." od
			   INNER JOIN ".$this->db->table("orders")." o ON (od.order_id = o.order_id)
			   LEFT JOIN ".$this->db->table("downloads")." d ON (d.download_id = od.download_id)
			   LEFT JOIN ".$this->db->table("order_products")." op ON (op.order_product_id = od.order_product_id)
			   WHERE o.order_id = '".$order_id."'
			   ".($customer_id ? " AND o.customer_id = '".$customer_id."'" : "")."
			   ORDER BY  o.date_added DESC, od.sort_order ASC ";

        $query = $this->db->query($sql);
        $downloads = array();
        foreach ($query->rows as $download_info) {
            $downloads[$download_info['order_download_id']] = $download_info;
        }
        return $downloads;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function sendEmailActivation($data = array())
    {
        if (!$data || !is_array($data)) {
            return false;
        }

        //build confirmation email
        $language = new ALanguage($this->registry, $data['language_code']);
        $language->load('fast_checkout/fast_checkout');
        $language->load('mail/account_create');

        //build welcome email in text format
        $login_url = $this->html->getSecureURL('account/login');
        $this->language->load('mail/account_create');
        $main_data = [
            'store_name' => $this->config->get('store_name'),
            'login_url'  => $this->html->getSecureURL('account/login'),
            'login'      => $data['loginname'],
            'password'   => $data['password'],
        ];
        $config_mail_logo = $this->config->get('config_mail_logo');
        $config_mail_logo = !$config_mail_logo ? $this->config->get('config_logo') : $config_mail_logo;
        if ($config_mail_logo) {
            if (is_numeric($config_mail_logo)) {
                $r = new AResource('image');
                $resource_info = $r->getResource($config_mail_logo);
                if ($resource_info) {
                    $main_data['logo_html'] = html_entity_decode($resource_info['resource_code'], ENT_QUOTES, 'UTF-8');
                }
            } else {
                $main_data['logo_uri'] = 'cid:'
                    .md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                    .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION);
            }
        }
        $main_data['config_mail_logo'] = $config_mail_logo;
        //backward compatibility. TODO: remove this in 2.0
        if ($main_data['logo_uri']) {
            $main_data['logo'] = $this->data['mail_template_data']['logo_uri'];
        } else {
            $main_data['logo'] = $this->config->get('config_mail_logo');
        }

        $template = 'fast_checkout_welcome_email_guest_registration';

        //allow to change email data from extensions
        $this->extensions->hk_ProcessData($this, 'sf_fast_checkout_welcome_mail');

        $mail = new AMail($this->config);
        $mail->setTo($data['email']);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));
        $mail->setTemplate($template, $main_data);
        if (is_file(DIR_RESOURCE.$config_mail_logo)) {
            $mail->addAttachment(DIR_RESOURCE.$config_mail_logo,
                md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION));
        }
        $mail->send();
        return true;
    }

    public function emailDownloads($order_data, $download)
    {
        if (empty($download) || !is_array($download) || empty($order_data) || !is_array($order_data)) {
            return false;
        }

        //build confirmation email
        $language = new ALanguage($this->registry, $this->language->getLanguageCode());
        $language->load('fast_checkout/fast_checkout');

        $subject = sprintf($this->language->get('fast_checkout_download_subject'), $this->config->get('store_name'));
        $store_logo = md5(pathinfo($this->config->get('config_logo'), PATHINFO_FILENAME)).'.'
            .pathinfo($this->config->get('config_logo'), PATHINFO_EXTENSION);

        if ($download['count'] == 1) {
            $text_email_download = $this->language->get('fast_checkout_email_start_download');
            $text_email_download_link = $this->language->get('fast_checkout_button_start_download');
        } else {
            $text_email_download = $this->language->get('fast_checkout_email_order_downloads');
            $text_email_download_link = $this->language->get('fast_checkout_text_order_downloads');
        }
        $email_download_link = $download['download_url'];

        $this->data['mail_plain_text'] = $text_email_download."\n\n";
        $this->data['mail_plain_text'] .= $email_download_link."\n\n";
        $this->data['mail_plain_text'] .= $this->language->get('fast_checkout_text_thank_you')."\n";
        $this->data['mail_plain_text'] .= $this->config->get('store_name');

        //build HTML message with the template
        $this->data['mail_template_data']['text_thanks'] = $this->language->get('fast_checkout_text_thank_you');
        $this->data['mail_template_data']['logo'] = 'cid:'.$store_logo;
        $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
        $this->data['mail_template_data']['store_url'] = $this->config->get('config_url');
        $this->data['mail_template_data']['text_email_download'] = $text_email_download;
        $this->data['mail_template_data']['text_email_download_link'] = $text_email_download_link;
        $this->data['mail_template_data']['email_download_link'] = $email_download_link;
        $this->data['mail_template_data']['text_project_label'] = project_base();

        $this->data['mail_template'] = 'mail/guest_download.tpl';

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
        $mail->addAttachment(DIR_RESOURCE.$this->config->get('config_logo'));
        $mail->setHtml($html_body);
        $mail->send();
        return true;
    }

    public function parseOrderToken($ot)
    {
        if (!$ot) {
            return array();
        }

        //try to decrypt order token
        $enc = new AEncryption($this->config->get('encryption_key'));
        $decrypted = $enc->decrypt($ot);
        list($order_id, $email, $sec_token) = explode('::', $decrypted);

        $order_id = (int)$order_id;
        if (!$decrypted || !$order_id || !$email || !$sec_token) {
            return array();
        }
        $this->load->model('account/order');
        $order_info = $this->model_account_order->getOrder($order_id, '', 'view');

        //compare emails
        if ($order_info['email'] != $email) {
            return array();
        }
        //compare security token
        if ($sec_token != $this->getGuestToken($order_id)) {
            return array();
        }
        return array($order_id, $email, $sec_token);
    }

    //Get order security token for guest
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
        $token = $query->rows[0]['data'];
        return $token;
    }

    //Save order security token for guest
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

    public function getTotalOrderDownloads($order_id, $customers_id)
    {
        if (method_exists($this->download, 'getTotalOrderDownloads')) {
            return $this->download->getTotalOrderDownloads($order_id, $customers_id);
        } else {
            return $this->_get_total_order_downloads($order_id, $customers_id);
        }
    }

    /**
     * @param int $order_id
     * @param int $customer_id
     *
     * @return mixed
     */
    protected function _get_total_order_downloads($order_id, $customer_id)
    {
        return sizeof($this->getCustomerOrderDownloads($order_id, $customer_id));
    }
}
