<?php
/*
 * $Id$
 *
 * AbanteCart, Ideal OpenSource Ecommerce Solution
 * http://www.AbanteCart.com
 *
 * Copyright © 2011-2024 Belavier Commerce LLC
 *
 * This source file is subject to Open Software License (OSL 3.0)
 * License details is bundled with this package in the file LICENSE.txt.
 * It is also available at this URL:
 * <http://www.opensource.org/licenses/OSL-3.0>
 *
 * UPGRADE NOTE:
 * Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 * versions in the future. If you wish to customize AbanteCart for your
 * needs please refer to http://www.AbanteCart.com for more information.
 */

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AIM for Instant Messages
 *
 * @property ACustomer $customer
 * @property ALanguage $language
 * @property ADB $db
 * @property ALog $log
 * @property ALoader $load
 * @property AHtml $html
 * @property ExtensionsAPI $extensions
 * @property ASession $session
 * @property AConfig $config
 * @property ModelAccountCustomer $model_account_customer
 * @property AResponse $response
 * @property  array $admin_sendpoints
 */
class AIM
{
    protected $registry;
    protected $protocols = ['email', 'sms'];
    /**
     * @var array for StoreFront side ONLY!
     * NOTE:
     * each key of array is text_id of sendpoint.
     * To get sendpoint title needs to request language definition in format im_sendpoint_name_{sendpoint_text_id}
     * All sendpoint titles have to be saved in common/im language block for both sides! (admin + storefront)
     * Values of array is language definitions key that stores in the same block. This values can have %s that will be replaced by sendpoint text variables.
     * for ex. message have url to product page. Text will have #storefront#rt=product/product&product_id=%s and customer will receive full url to product.
     * Some sendpoints have few text variables, for ex. order status and order status name
     * For additional sendpoints ( from extensions) you can store language keys wherever you want.
     */
    public $sendpoints = [
        // 0 - storefront (customer) and 1 - Admin (user)
        'newsletter'              => [
            0 => ['text_key' => 'im_newsletter_text_to_customer'],
            1 => [],
        ],
        'product_review'          => [
            0 => [],
            1 => ['text_key' => 'im_product_review_text_to_admin'],
        ],
        'product_out_of_stock'    => [
            0 => [],
            1 => ['text_key' => 'im_product_out_of_stock_admin_text'],
        ],
        'order_update'            => [
            0 => ['text_key' => 'im_order_update_text_to_customer', 'force_send' => ['email']],
            1 => ['text_key' => 'im_order_update_text_to_admin'],
        ],
        'new_order'               => [
            0 => [],
            1 => ['text_key' => 'im_new_order_text_to_admin'],
        ],
        'new_customer'            => [
            0 => [],
            1 => ['text_key' => 'im_new_customer_admin_text'],
        ],
        'customer_account_update' => [
            0 => ['text_key' => 'im_customer_account_update_text_to_customer', 'force_send' => ['email']],
            1 => [],
        ],
        'customer_contact'        => [
            0 => [],
            1 => ['text_key' => 'im_customer_contact_admin_text'],
        ],
        /* TODO: Need to decide how to handle system messages in respect to IM
        'system_messages' => array (
                        0 => '',
                        1 => array('text_key' => 'im_system_messages_admin_text'),
                    ),
        */
    ];

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->registry->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @return array
     */
    public function getProtocols()
    {
        return $this->protocols;
    }

    /**
     * @param string $protocol
     *
     */
    public function removeProtocol($protocol = '')
    {
        unset($this->protocols[$protocol]);
    }

    /**
     * @param string $name
     */
    public function addProtocol($name)
    {
        if ($name && !in_array($name, $this->protocols)) {
            $this->protocols[$name] = $name;
        }
    }

    /**
     * @param $section - can be admin or storefront
     *
     * @return array
     */
    public function getActiveProtocols($section)
    {
        if (!in_array($section, ['storefront', 'admin'])) {
            return [];
        }
        $protocols = [];
        foreach ($this->protocols as $protocol) {
            if ($this->config->get('config_'.$section.'_'.$protocol.'_status')) {
                $protocols[$protocol] = $protocol;
            }
        }
        return $protocols;
    }

    /**
     * Note: method can be called from admin side
     *
     * @param array $filter_arr
     *
     * @return array
     * @throws AException
     */
    public function getIMDriverObjects($filter_arr = ['status' => 1])
    {
        $filter = [
            'category' => 'Communication',
        ];
        //returns all drivers for admin side settings page
        if (has_value($filter_arr['status'])) {
            $filter['status'] = (int) $filter_arr['status'];
        }

        $extensions = $this->extensions->getExtensionsList($filter);
        $driver_list = ['email' => new AMailIM()];

        $active_drivers = [];

        if ($filter_arr['status']) {
            foreach ($this->protocols as $protocol) {
                if ($protocol == 'email') {
                    continue;
                }
                if ($this->config->get('config_storefront_'.$protocol.'_status')) {
                    $active_drivers[] = $this->config->get('config_'.$protocol.'_driver');
                }
            }
        }

        foreach ($extensions->rows as $ext) {
            $driver_txt_id = $ext['key'];

            //skip non-installed
            if (!has_value($this->config->get($driver_txt_id.'_status'))) {
                continue;
            }

            //skip non-active drivers
            if ($filter_arr['status'] && !in_array($driver_txt_id, $active_drivers)) {
                continue;
            }

            //NOTE! all IM drivers MUST have class by these path
            try {
                /** @noinspection PhpIncludeInspection */
                include_once(DIR_EXT.$driver_txt_id.'/core/lib/'.$driver_txt_id.'.php');
            } catch (Exception $e) {

            }
            $classname = preg_replace('/[^a-zA-Z]/', '', $driver_txt_id);

            if (!class_exists($classname)) {
                continue;
            }
            /**
             * @var $driver AMailIM
             */
            $driver = new $classname();
            $driver_list[$driver->getProtocol()] = $driver;
        }
        return $driver_list;
    }

    /**
     * @param string $name
     * @param array $data_array
     *
     * @return bool
     * @throws AException
     */
    public function addSendPoint($name, $data_array)
    {
        if ($name && !in_array($name, $this->sendpoints)) {
            $this->sendpoints[$name] = $data_array;
        } else {
            $error = new AError('SendPoint '.$name.' cannot be added to the list.');
            $error->toLog();
            return false;
        }
        return true;
    }

    /**
     * @param string $sendpoint
     * @param array $msg_details
     * @param string $templateTextId
     * @param array $templateData
     * @param array $attachments
     *
     * @return null
     *
     * $msg_details structure:
     * array(
     * 0 => array(
     * message => 'text',
     * )
     * 1 => array(
     * message => 'text',
     * )
     * );
     * 0 - storefront (customer) and 1 - Admin (user)
     * notes: If message is not provided, message text will be takes from languages based on checkpoint text key.
     * @throws AException
     */
    public function send($sendpoint, $msg_details = [], $templateTextId = '', $templateData = [], $attachments = [])
    {
        $this->load->language('common/im');
        $customer_im_settings = [];
        if (IS_ADMIN !== true) {
            $sendpoints_list = $this->sendpoints;
            //do have storefront sendpoint?
            if (!empty($sendpoints_list[$sendpoint][0])) {
                $this->load->model('account/customer');
                $customer_im_settings = $this->getCustomerNotificationSettings();
            }
            $this->registry->set('force_skip_errors', true);
        } else {
            $sendpoints_list = $this->admin_sendpoints;
            //this method forbid sending notifications to customers from admin-side
            $customer_im_settings = [];
        }
        //check sendpoint
        if (!in_array($sendpoint, array_keys($sendpoints_list))) {
            $error = new AError('IM error: Unrecognized SendPoint '.$sendpoint.'. Nothing sent.');
            $error->toLog();
            return false;
        }

        $sendpoint_data = $sendpoints_list[$sendpoint];

        foreach ($this->protocols as $protocol) {
            $driver = null;

            //check protocol status
            if ($protocol == 'email') {
                //email notifications always enabled
                $protocol_status = 1;
            } else {
                if ((int) $this->config->get('config_storefront_'.$protocol.'_status')
                    || (int) $this->config->get('config_admin_'.$protocol.'_status')
                ) {
                    $protocol_status = 1;
                } else {
                    $protocol_status = 0;
                }
            }

            if (!$protocol_status) {
                continue;
            }

            if ($protocol == 'email') {
                //see AMailAIM class below
                $driver = new AMailIM();
            } else {
                $driver_txt_id = $this->config->get('config_'.$protocol.'_driver');

                //if driver not set - skip protocol
                if (!$driver_txt_id) {
                    continue;
                }

                if (!$this->config->get($driver_txt_id.'_status')) {
                    $error =
                        new AError('Cannot send notification. Communication driver '.$driver_txt_id.' is disabled!');
                    $error->toLog()->toMessages();
                    continue;
                }

                //use safe usage
                $driver_file = DIR_EXT.$driver_txt_id.'/core/lib/'.$driver_txt_id.'.php';
                if (!is_file($driver_file)) {
                    $error = new AError('Cannot find file '.$driver_file.' to send notification.');
                    $error->toLog()->toMessages();
                    continue;
                }
                try {
                    /** @noinspection PhpIncludeInspection */
                    include_once($driver_file);
                    //if class of driver
                    $classname = preg_replace('/[^a-zA-Z]/', '', $driver_txt_id);
                    if (!class_exists($classname)) {
                        $error = new AError('IM-driver '.$driver_txt_id.' load error.');
                        $error->toLog()->toMessages();
                        continue;
                    }

                    $driver = new $classname();
                } catch (Exception $e) {
                }
            }
            //if driver cannot be initialized - skip protocol
            if ($driver === null) {
                continue;
            }

            $store_name = $this->config->get('store_name').": ";
            if (!empty($sendpoint_data[0])) {
                //send notification to customer, check if selected or forced
                $force_arr = $sendpoint_data[0]['force_send'];
                $forced = false;
                if (has_value($force_arr) && in_array($protocol, $force_arr)) {
                    $forced = true;
                }
                if ($customer_im_settings[$sendpoint][$protocol] || $forced) {
                    $message = $msg_details[0]['message'];
                    //check if notification for this protocol and sendpoint are allowed
                    if ($this->config->get('config_storefront_'.$protocol.'_status') || $protocol == 'email') {
                        if (empty($message)) {
                            //send default message. Not recommended
                            $message = $this->language->get($sendpoint_data[0]['text_key']);
                        }
                        $message = $this->_process_message_text($message);

                        $to = $this->getCustomerURI($protocol, (int) $this->customer->getId());
                        if ($message && $to) {
                            //use safe call
                            try {
                                if ($protocol == 'email') {
                                    $driver->send(
                                        $to, $store_name.$message, $templateTextId, $templateData, $attachments
                                    );
                                } else {
                                    $driver->send($to, $store_name.$message);
                                }
                            } catch (Exception $e) {
                            }
                        }
                    }
                }
            }

            if (!empty($sendpoint_data[1])) {
                //send notification to admins
                if ($this->config->get('config_admin_'.$protocol.'_status') || $protocol == 'email') {
                    $message = $msg_details[1]['message'];
                    if (empty($message)) {
                        //send default message. Not recommended
                        $message = $this->language->get($sendpoint_data[1]['text_key']);
                    }
                    $message = $this->_process_message_text($message, true);
                    //NOTE! all admins will receive IMs
                    $to = $this->_get_admin_im_uri($sendpoint, $protocol);
                    if ($message && $to) {
                        //use safe call
                        try {
                            if ($protocol == 'email') {
                                $driver->sendFew(
                                    $to, $store_name.$message, $templateTextId, $templateData, $attachments
                                );
                            } else {
                                $driver->sendFew($to, $store_name.$message);
                            }
                        } catch (Exception $e) {
                        }
                    }
                }
            }

            unset($driver);
        }
        $this->registry->set('force_skip_errors', false);
        return true;
    }

    /**
     * @return array
     * @throws AException
     */
    private function getCustomerNotificationSettings()
    {
        $settings = [];
        $protocols = $this->protocols;
        //TODO: in the future should to create separate configurable sendpoints list for guests
        $sendpoints = $this->sendpoints;
        if ($this->customer && $this->customer->isLogged()) {
            $settings = $this->model_account_customer->getCustomerNotificationSettings();
        } //for guests before order creation
        elseif ($this->session->data['guest']) {
            //get im settings for guest
            $guest_data = $this->session->data['guest'];

            foreach ($sendpoints as $sendpoint => $row) {
                foreach ($protocols as $protocol) {
                    if ($guest_data[$protocol]) {
                        //allow to send notifications only when it allowed by global IM settings
                        if ((int) $this->config->get('config_storefront_'.$protocol.'_status')
                            && (int) $this->config->get('config_im_guest_'.$protocol.'_status')
                        ) {
                            $protocol_status = 1;
                        } else {
                            $protocol_status = 0;
                        }
                        $settings[$sendpoint][$protocol] = $protocol_status;
                    }
                }
            }
        }//for guests after order placement
        elseif ($this->session->data['order_id']) {
            $p = [];
            foreach ($protocols as $protocol) {
                $p[] = $this->db->escape($protocol);
            }

            $sql = "SELECT DISTINCT odt.`type_id`, odt.`name` as protocol, od.data
                    FROM ".$this->db->table('order_data_types')." odt
                    LEFT JOIN ".$this->db->table('order_data')." od
                        ON (od.type_id = odt.type_id AND od.order_id = '".(int) $this->session->data['order_id']."')
                    WHERE odt.`name` IN ('".implode("', '", $p)."')";
            $result = $this->db->query($sql);

            if ($result->rows) {
                foreach ($result->rows as $row) {
                    $guest_data = unserialize($row['data']);
                    foreach ($sendpoints as $sendpoint => $r) {
                        $settings[$sendpoint][$row['protocol']] = (int) $guest_data['status'];
                    }
                }
            }
        }

        return $settings;
    }

    /**
     * @param string $message
     * @param bool $for_admin
     *
     * @return string
     * @throws AException
     */
    protected function _process_message_text($message, $for_admin = false)
    {
        if (!$message) {
            //If no text abort sending
            return '';
        }

        //process formatted url like #storefront#rt=...
        return $this->html->convertLinks($message, '', $for_admin);
    }

    /**
     * @param string $protocol
     * @param int $customer_id
     * @param int $order_id -  to get uri from guest checkout order data
     *
     * @return string
     * @throws AException
     */
    public function getCustomerURI($protocol, $customer_id = 0, $order_id = 0)
    {
        $customer_id = (int) $customer_id;
        $order_id = (int) $order_id ?: (int) $this->session->data['order_id'];

        //for registered customers - get address from database
        if ($customer_id && !$order_id) {
            $sql = "SELECT *
                    FROM ".$this->db->table('customers')."
                    WHERE customer_id=".$customer_id;
            $customer_info = $this->db->query($sql, true);
            return $customer_info->row[$protocol];
        } elseif ($order_id) {
            //if guests - get im-data from order_data tables

            $sql = "SELECT *
                    FROM ".$this->db->table('order_data')." od
                    WHERE `type_id` in ( SELECT DISTINCT type_id
                                         FROM ".$this->db->table('order_data_types')."
                                         WHERE `name`='".$this->db->escape($protocol)."' )
                        AND order_id = '".$order_id."'";
            $result = $this->db->query($sql);

            if ($result->row['data']) {
                $im_info = unserialize($result->row['data']);
                if ($im_info['status'] && $im_info['uri']) {
                    return $im_info['uri'];
                }
            }
            return '';
        } //when guest checkout - take uri from session
        elseif ($this->session->data['guest'][$protocol]) {
            return $this->session->data['guest'][$protocol];
        }

        return '';
    }

    private function _get_admin_im_uri($sendpoint, $protocol)
    {
        $section = IS_ADMIN === true ? 1 : 0;
        $output = [];
        $sql = "SELECT un.*
                FROM ".$this->db->table('user_notifications')." un
                INNER JOIN ".$this->db->table('users')." u
                    ON (u.user_id = un.user_id AND u.status = 1)
                WHERE un.protocol='".$this->db->escape($protocol)."'
                    AND un.sendpoint = '".$this->db->escape($sendpoint)."'
                    AND un.section = '".$section."'
                    AND un.store_id = '".(int) $this->config->get('config_store_id')."'";
        $result = $this->db->query($sql);
        foreach ($result->rows as $row) {
            $uri = trim($row['uri']);
            if ($uri) {
                $output[] = $uri;
            }
        }

        return $output;
    }

}

//email driver for notification class use
final class AMailIM
{
    public $errors = [];
    private $registry;
    /** @var AConfig */
    private $config;
    /** @var ALanguage */
    private $language;
    /** @var ALoader */
    private $load;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->language = $this->registry->get('language');
        $this->config = $this->registry->get('config');
        $this->load = $this->registry->get('load');
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return 'email';
    }

    /**
     * @return string
     * @throws AException
     */
    public function getProtocolTitle()
    {
        $this->load->language('common/im');
        return $this->language->get('im_protocol_email_title');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Email';
    }

    /**
     * @param string $to
     * @param string $text
     * @param string $templateTextId
     * @param array $templateData
     * @param array $attachments - array with attachment arrays ['file' => {full_filename}, 'name' => {name of file} ]
     *
     * @return bool
     * @throws AException|TransportExceptionInterface
     */
    public function send($to, $text, $templateTextId = '', $templateData = [], $attachments = [])
    {
        $this->load->language('common/im');
        $to = trim($to);
        $text = trim($text);
        if (!$to || !$text) {
            return false;
        }

        $mail = new AMail($this->config);
        $mail->setTo($to);
        $mail->setFrom($this->config->get('store_main_email'));
        $mail->setSender($this->config->get('store_name'));
        if (empty($templateTextId)) {
            $mail->setSubject($this->config->get('store_name').' '.$this->language->get('im_text_notification'));
            $mail->setHtml($text);
            $mail->setText($text);
        } else {
            $mail->setTemplate($templateTextId, $templateData, $this->language->getContentLanguageID());
        }
        if ($attachments) {
            foreach ($attachments as $attach) {
                $mail->addAttachment(
                    $attach['file'],
                    $attach['name'] ?? basename($attach['file'])
                );
            }
        }
        $mail->send(true);
        unset($mail);

        return true;
    }

    /**
     * @param array $to
     * @param string $text
     * @param string $templateTextId
     * @param array $templateData
     * @param array $attachments
     *
     * @throws AException|TransportExceptionInterface
     */
    public function sendFew($to, $text, $templateTextId = '', $templateData = [], $attachments = [])
    {
        foreach ($to as $uri) {
            $this->send($uri, $text, $templateTextId, $templateData, $attachments);
        }
    }

    /**
     * @param string $emails - comma separated
     *
     * @return bool
     * @throws AException
     */
    public function validateURI($emails)
    {
        $this->load->language('common/im');
        $this->errors = [];
        $emails = explode(',', $emails);
        foreach ($emails as $email) {
            $email = trim($email);
            if ((mb_strlen($email) > 96) || (!preg_match(EMAIL_REGEX_PATTERN, $email))) {
                $this->errors[] = sprintf($this->language->get('im_error_mail_address'), $email);
            }
        }
        return (!$this->errors);
    }

    /**
     * Function builds form element for storefront side (customer account page)
     *
     * @param AForm $form
     * @param string $value
     *
     * @return stdClass
     */
    public function getURIField($form, $value = '')
    {
        return new stdClass();
    }
}
