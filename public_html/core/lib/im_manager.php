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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class AIMManager
 *
 * @property Registry $registry
 */
class AIMManager extends AIM
{

    public $errors = array();
    //add sendpoints list for admin-side
    /**
     * @var array
     * * NOTE:
     * each key of array is text_id of sendpoint.
     * To get sendpoint title needs to request language definition in format im_sendpoint_name_{sendpoint_text_id}
     * All core/default sendpoint 'text_key' saved in common/im language block for both sides (admin & storefront)
     * Values of array is language definitions key that stores in the same block. This values can have %s that will be replaced by sendpoint text variables.
     * for ex. message have url to product page. Text will have #storefront#rt=product/product&product_id=%s and customer will receive full url to product.
     * Some sendpoints have few text variables, for ex. order status and order status name
     * For additional sendpoints ( from extensions) you can store language keys wherever you want.
     *
     */
    public $admin_sendpoints = array(
        'order_update'              => array(
            0 => array('text_key' => 'im_order_update_text_to_customer'),
            1 => array(),
        ),
        'account_update'            => array(
            0 => array(),
            1 => array('text_key' => 'im_account_update_text_to_admin', 'force_send' => array('email')),
        ),
        'customer_account_approved' => array(
            0 => array('text_key' => 'im_customer_account_approved_text_to_customer', 'force_send' => array('email')),
            1 => array(),
        ),
        'customer_account_update'   => array(
            0 => array('text_key' => 'im_customer_account_update_text_to_customer', 'force_send' => array('email')),
            1 => array(),
        ),
        /* TODO: Need to decide how to handle system messages in respect to IM
        'system_messages' 			=> array (
                0 => array(),
                1 => array('text_key' => 'im_system_messages_text_to_admin'),
        */
    );

    //NOTE: This class is loaded in INIT for admin only
    public function __construct()
    {
        parent::__construct();
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to access class AIMManager');
        }
    }

    /**
     * @param string $name
     * @param array  $data_array
     *
     * @return bool
     */
    public function addAdminSendPoint($name, $data_array)
    {
        if ($name && !in_array($name, $this->admin_sendpoints)) {
            $this->admin_sendpoints[$name] = $data_array;
        } else {
            $error = new AError('Admin SendPoint '.$name.' cannot be added to the list.');
            $error->toLog();
            return false;
        }
        return true;
    }

    public function send($sendpoint, $text_vars = array(), $templateTextId='', $templateData = [])
    {
        return parent::send($sendpoint, $text_vars, $templateTextId=$templateTextId, $templateData = $templateData);
    }

    /**
     * @param int    $customer_id
     * @param string $sendpoint
     * @param array  $msg_details - structure:
     *                            array(
     *                            message => 'text',
     *                            );
     *                            notes: If message is not provided, message text will be takes from languages based on checkpoint text key.
     *
     * @return array|bool
     */
    public function sendToCustomer($customer_id, $sendpoint, $msg_details = array(), $templateTextId = '', $templateData = [])
    {
        if (!$customer_id) {
            return array();
        }
        $this->load->language('common/im');
        $sendpoints_list = $this->admin_sendpoints;
        $customer_im_settings = $this->getCustomerIMSettings($customer_id);
        $this->registry->set('force_skip_errors', true);

        //check sendpoint
        if (!in_array($sendpoint, array_keys($sendpoints_list))) {
            $error = new AError('IM error: Invalid SendPoint '.$sendpoint.' was used in IM class. Nothing sent.');
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
            } elseif ((int)$this->config->get('config_storefront_'.$protocol.'_status')
                || (int)$this->config->get('config_admin_'.$protocol.'_status')
            ) {
                $protocol_status = 1;
            } else {
                $protocol_status = 0;
            }

            if (!$protocol_status) {
                continue;
            }

            if ($protocol == 'email') {
                //see AMailAIM class in im.php
                $driver = new AMailIM();
            } else {
                $driver_txt_id = $this->config->get('config_'.$protocol.'_driver');

                //if driver not set - skip protocol
                if (!$driver_txt_id) {
                    continue;
                }

                if (!$this->config->get($driver_txt_id.'_status')) {
                    $error = new AError('Cannot send notification. Communication driver '.$driver_txt_id.' is disabled!');
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
            //send notification to customer
            if (!empty($sendpoint_data[0])) {
                //send notification to customer, check if selected or forced
                $force_arr = $sendpoint_data[0]['force_send'];
                $forced = false;
                if (has_value($force_arr) && in_array($protocol, $force_arr)) {
                    $forced = true;
                }
                if ($customer_im_settings[$sendpoint][$protocol] || $forced) {
                    //check is notification for this protocol and sendpoint allowed
                    if ($this->config->get('config_storefront_'.$protocol.'_status') || $protocol == 'email') {
                        $message = $msg_details[0]['message'];
                        if (empty($message)) {
                            //send default message. Not recommended
                            $message = $this->language->get($sendpoint_data[0]['text_key']);
                        }
                        $message = $this->_process_message_text($message);
                        $to = $this->getCustomerURI($protocol, $customer_id);
                        if ($message && $to) {
                            //use safe call
                            try {
                                if ($protocol == 'email') {
                                    $driver->send($to, $store_name.$message, $templateTextId, $templateData);
                                } else {
                                    $driver->send($to, $store_name.$message);
                                }
                            } catch (Exception $e) {
                                $this->log->write($e->getMessage());
                            }
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
     * @param int   $order_id
     * @param array $msg_details - structure:
     *                           array(
     *                           message => 'text',
     *                           );
     *                           notes: If message is not provided, message text will be takes from languages based on checkpoint text key.
     *
     * @return array|bool
     */
    public function sendToGuest($order_id, $msg_details = array(), $templateTextId = '', $templateData = [])
    {
        if (!$order_id) {
            return array();
        }
        $this->load->language('common/im');
        $this->registry->set('force_skip_errors', true);

        foreach ($this->protocols as $protocol) {
            $driver = null;

            //check protocol status
            if ($protocol == 'email') {
                //email notifications always enabled
                $protocol_status = 1;
            } elseif ((int)$this->config->get('config_storefront_'.$protocol.'_status')
                || (int)$this->config->get('config_admin_'.$protocol.'_status')
            ) {
                $protocol_status = 1;
            } else {
                $protocol_status = 0;
            }

            if (!$protocol_status) {
                continue;
            }

            if ($protocol == 'email') {
                //see AMailAIM class in im.php
                $driver = new AMailIM();
            } else {
                $driver_txt_id = $this->config->get('config_'.$protocol.'_driver');

                //if driver not set - skip protocol
                if (!$driver_txt_id) {
                    continue;
                }

                if (!$this->config->get($driver_txt_id.'_status')) {
                    $error = new AError('Cannot send notification. Communication driver '.$driver_txt_id.' is disabled!');
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
            //check is notification for this protocol and sendpoint allowed
            if ($this->config->get('config_storefront_'.$protocol.'_status') || $protocol == 'email') {
                $message = $msg_details[0]['message'];
                if (empty($message)) {
                    continue;
                }
                $message = $this->_process_message_text($message);
                $to = $this->getCustomerURI($protocol, 0, $order_id);
                if ($message && $to) {
                    //use safe call
                    try {
                        if ($protocol == 'email') {
                            $driver->send($to, $store_name.$message, $templateTextId, $templateData);
                        } else {
                            $driver->send($to, $store_name.$message);
                        }
                    } catch (Exception $e) {
                    }
                }
            }
            unset($driver);
        }
        $this->registry->set('force_skip_errors', false);
        return true;
    }

    public function getCustomerIMSettings($customer_id)
    {
        if (!$customer_id) {
            return array();
        }

        //get only active IM drivers
        $im_protocols = $this->getProtocols();
        $im_settings = array();
        $sql = "SELECT *
				FROM ".$this->db->table('customer_notifications')."
				WHERE customer_id = ".(int)$customer_id;
        $result = $this->db->query($sql);

        foreach ($result->rows as $row) {
            if (!in_array($row['protocol'], $im_protocols)) {
                continue;
            }
            $im_settings[$row['sendpoint']][$row['protocol']] = (int)$row['status'];
        }
        return $im_settings;
    }

    public function getUserIMs($user_id, $store_id)
    {
        $user_id = (int)$user_id;
        $store_id = (int)$store_id;
        if (!$user_id) {
            return array();
        }

        $sql = "SELECT *
				FROM ".$this->db->table('user_notifications')."
				WHERE user_id=".$user_id."
					AND store_id = '".$store_id."'
				ORDER BY `sendpoint`, `protocol`";
        $result = $this->db->query($sql);

        $output = array();
        foreach ($result->rows as $row) {
            $section = (int)$row['section'] ? 'admin' : 'storefront';
            $sendpoint = $row['sendpoint'];
            unset($row['sendpoint']);
            $output[$section][$sendpoint][] = $row;
        }
        return $output;
    }

    /**
     * @param int    $user_id
     * @param string $sendpoint
     * @param array  $msg_details
     *
     * @return null

    $msg_details structure:
     * array(
     * message => 'text',
     * );
     * notes: If message is not provided, message text will be takes from languages based on checkpoint text key.
     */
    public function sendToUser($user_id, $sendpoint, $msg_details = array(), $templateTextId = '', $templateData = [])
    {
        if (!$user_id) {
            return array();
        }

        $this->load->language('common/im');

        $sendpoints_list = $this->admin_sendpoints;
        $user_im_settings = $this->getUserSendPointSettings($user_id, 'admin', $sendpoint, 0);
        $this->registry->set('force_skip_errors', true);

        //check sendpoint
        if (!in_array($sendpoint, array_keys($sendpoints_list))) {
            $error = new AError('IM error: Invalid SendPoint '.$sendpoint.' was used in IM class. Nothing sent.');
            $error->toLog()->toMessages();
            return false;
        }
        $sendpoint_data = $sendpoints_list[$sendpoint];

        foreach ($this->protocols as $protocol) {
            $driver = null;

            //check protocol status
            if ($protocol == 'email') {
                //email notifications always enabled
                $protocol_status = 1;
            } elseif ((int)$this->config->get('config_admin_'.$protocol.'_status')) {
                $protocol_status = 1;
            } else {
                $protocol_status = 0;
            }

            if (!$protocol_status) {
                continue;
            }

            if ($protocol == 'email') {
                //see AMailAIM class in im.php
                $driver = new AMailIM();
            } else {
                $driver_txt_id = $this->config->get('config_'.$protocol.'_driver');

                //if driver not set - skip protocol
                if (!$driver_txt_id) {
                    continue;
                }

                if (!$this->config->get($driver_txt_id.'_status')) {
                    $error = new AError('Cannot send notification. Communication driver '.$driver_txt_id.' is disabled!');
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
            //send notification to user
            if (!empty($sendpoint_data[1])) {

                if ($user_im_settings[$protocol]) {
                    $to = $user_im_settings[$protocol];
                    //check is notification for this protocol and sendpoint allowed
                    $message = $msg_details[1]['message'];
                    if (empty($message)) {
                        //send default message. Not recommended
                        $message = $this->language->get($sendpoint_data[1]['text_key']);
                    }
                    $message = $this->_process_message_text($message);

                    if ($message) {
                        //use safe call
                        try {
                            if ($protocol == 'email') {
                                $driver->send($to, $store_name.$message, $templateTextId, $templateData);
                            } else {
                                $driver->send($to, $store_name.$message);
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

    public function getUserSendPointSettings($user_id, $section, $sendpoint, $store_id)
    {
        $user_id = (int)$user_id;
        $store_id = (int)$store_id;

        if (!$user_id || !$sendpoint || !in_array($section, array('admin', 'storefront', ''))) {
            return array();
        }

        $sql = "SELECT *
				FROM ".$this->db->table('user_notifications')." un
				INNER JOIN ".$this->db->table('users')." u
					ON (u.user_id = un.user_id AND u.status = 1)
				WHERE un.user_id=".$user_id."
					AND un.store_id = '".$store_id."'
					AND un.sendpoint = '".$this->db->escape($sendpoint)."'";
        if ($section != '') {
            $sql .= " AND un.section = '".($section == 'admin' ? 1 : 0)."'";
        }

        $sql .= "ORDER BY un.protocol";
        $result = $this->db->query($sql);

        $output = array();
        foreach ($result->rows as $row) {
            if (!$output[$row['protocol']]) {
                $output[$row['protocol']] = $row['uri'];
            }
        }
        return $output;
    }

    public function validateUserSettings($settings)
    {
        $this->errors = array();
        if (!$settings) {
            return null;
        }
        //get all installed drivers
        $drivers = $this->getIMDriverObjects(array('status' => ''));
        $supported_protocols = array_keys($drivers);
        foreach ($settings as $protocol => $uri) {

            //ignore non-supported protocols
            if (!in_array($protocol, $supported_protocols) || !$uri) {
                continue;
            }
            /**
             * @var AMailIM $driver
             */
            $driver = $drivers[$protocol];
            if (!$driver->validateURI($uri)) {
                $this->errors[$protocol] = implode('<br>', $driver->errors);
            }
        }

        if ($this->errors) {
            return false;
        } else {
            return true;
        }
    }

    public function saveIMSettings($user_id, $section, $sendpoint, $store_id, $settings = array())
    {

        $user_id = (int)$user_id;
        $store_id = (int)$store_id;
        $settings = (array)$settings;
        if (!$user_id || !$sendpoint || !in_array($section, array('admin', 'storefront', ''))) {
            return false;
        }

        foreach ($settings as $protocol => $uri) {
            $sql = "DELETE FROM ".$this->db->table('user_notifications')."
				WHERE user_id=".$user_id."
					AND store_id = '".$store_id."'";
            if ($section != '') {
                $sql .= " AND section = '".($section == 'admin' ? 1 : 0)."'";
            }
            $sql .= " AND sendpoint = '".$this->db->escape($sendpoint)."'
					AND protocol='".$this->db->escape($protocol)."'";

            $this->db->query($sql);

            $sections = $section ? array($section) : array('admin', 'storefront');
            foreach ($sections as $s) {
                $s = $s == 'admin' ? 1 : 0;
                $sql = "INSERT INTO ".$this->db->table('user_notifications')."
					(user_id, store_id, section, sendpoint, protocol, uri, date_added)
					VALUES ('".$user_id."',
							'".$store_id."',
							'".$s."',
							'".$this->db->escape($sendpoint)."',
							'".$this->db->escape($protocol)."',
							'".$this->db->escape($uri)."',
							NOW())";
                $this->db->query($sql);
            }
        }

        return true;
    }

    public function getIMDriversList()
    {
        $filter = array(
            'category' => 'Communication',
        );
        $extensions = $this->extensions->getExtensionsList($filter);
        $driver_list = array();
        foreach ($extensions->rows as $ext) {
            $driver_txt_id = $ext['key'];

            if ($this->config->get($driver_txt_id.'_status') === null) {
                continue;
            }

            //NOTE! all IM drivers MUST have class by these path
            try {
                /** @noinspection PhpIncludeInspection */
                include_once(DIR_EXT.$driver_txt_id.'/core/lib/'.$driver_txt_id.'.php');
            } catch (AException $e) {
            }
            $classname = preg_replace('/[^a-zA-Z]/', '', $driver_txt_id);

            if (!class_exists($classname)) {
                continue;
            }
            /**
             * @var AMailIM $driver
             */
            $driver = new $classname();
            $driver_list[$driver->getProtocol()][$driver_txt_id] = $driver->getName();
        }
        return $driver_list;
    }

}
