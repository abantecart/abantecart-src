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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelSaleContact
 *
 * @property ModelSettingStore $model_setting_store
 * @property ModelSaleCustomer $model_sale_customer
 * @property ModelSaleOrder    $model_sale_order
 */
class ModelSaleContact extends Model
{
    public $errors = array();
    private $eta = array();

    /**
     * @param string $task_name
     * @param array  $data
     *
     * @return array|bool
     */
    public function createTask($task_name, $data = array())
    {

        if (!$task_name) {
            $this->errors[] = 'Can not to create task. Empty task name has been given.';
        }

        //first of all needs to define recipient count
        $this->load->model('sale/customer');
        $this->load->model('setting/store');
        $store_info = $this->model_setting_store->getStore((int)$this->session->data['current_store_id']);
        if ($store_info) {
            $store_name = $store_info['store_name'];
        } else {
            $store_name = $this->config->get('store_name');
        }

        //get URIs of recipients
        $uris = $subscribers = $task_controller = '';
        if ($data['protocol'] == 'email') {
            list($uris, $subscribers) = $this->_get_email_list($data);
            $task_controller = 'task/sale/contact/sendEmail';

            //if message does not contains html-tags replace line breaks to <br>
            $decoded = html_entity_decode($data['message'], ENT_QUOTES, 'UTF-8');
            if ($decoded == strip_tags($decoded)) {
                $data['message'] = nl2br($data['message']);
            }

        } elseif ($data['protocol'] == 'sms') {
            list($uris, $subscribers) = $this->_get_phone_list($data);
            $task_controller = 'task/sale/contact/sendSms';
        }

        if (!$uris) {
            $this->errors[] = 'No recipients!';
            return false;
        }

        //numbers of emails per task step
        $divider = 10;
        //timeout in seconds for one email send
        $time_per_send = 4;
        $steps_count = ceil(sizeof($uris) / $divider);

        $tm = new ATaskManager();

        //create new task
        $task_id = $tm->addTask(
            array(
                'name'               => $task_name,
                'starter'            => 1, //admin-side is starter
                'created_by'         => $this->user->getId(), //get starter id
                'status'             => $tm::STATUS_READY,
                'start_time'         => date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))),
                'last_time_run'      => '0000-00-00 00:00:00',
                'progress'           => '0',
                'last_result'        => '1', // think all fine until some failed step will set 0 here
                'run_interval'       => '0',
                //think that task will execute with some connection errors
                'max_execution_time' => (sizeof($uris) * $time_per_send * 2),

            )
        );
        if (!$task_id) {
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }

        $tm->updateTaskDetails($task_id,
            array(
                'created_by' => $this->user->getId(),
                'settings'   => array(
                    'recipients_count' => sizeof($uris),
                    'sent'             => 0,
                ),
            )
        );

        //create steps for sending
        $k = 0;
        $sort_order = 1;
        while ($steps_count > 0) {
            $uri_list = array_slice($uris, $k, $divider);
            $step_id = $tm->addStep(array(
                'task_id'            => $task_id,
                'sort_order'         => $sort_order,
                'status'             => 1,
                'last_time_run'      => '0000-00-00 00:00:00',
                'last_result'        => '0',
                //think that task will execute with some connection errors
                'max_execution_time' => ($time_per_send * $divider * 2),
                'controller'         => $task_controller,
                'settings'           => array(
                    'to'          => $uri_list,
                    'subject'     => $data['subject'],
                    'message'     => $data['message'],
                    'store_name'  => $store_name,
                    'subscribers' => $subscribers,
                ),
            ));

            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                // get eta in seconds
                $this->eta[$step_id] = ($time_per_send * $divider);
            }
            $steps_count--;
            $k = $k + $divider;
            $sort_order++;
        }

        $task_details = $tm->getTaskById($task_id);

        if ($task_details) {
            foreach ($this->eta as $step_id => $eta) {
                $task_details['steps'][$step_id]['eta'] = $eta;
                //remove settings from output json array. We will take it from database on execution.
                $task_details['steps'][$step_id]['settings'] = array();
            }
            return $task_details;
        } else {
            $this->errors[] = 'Can not to get task details for execution';
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }

    }

    private function _get_email_list($data)
    {
        $subscribers = $emails = array();
        // All customers by group
        if (isset($data['recipient'])) {
            $results = array();
            if ($data['recipient'] == 'all_subscribers') {
                $all_subscribers = $this->model_sale_customer->getAllSubscribers(
                    array('filter' => array('newsletter_protocol' => 'email'))
                );
                $results = $this->_unify_customer_list('email', $all_subscribers);
                $subscribers = $results;
            } else {
                if ($data['recipient'] == 'only_subscribers') {
                    $only_subscribers = $this->model_sale_customer->getOnlyNewsletterSubscribers(
                        array('filter' => array('newsletter_protocol' => 'email'))
                    );
                    $results = $this->_unify_customer_list('email', $only_subscribers);
                    $subscribers = $results;
                } else {
                    if ($data['recipient'] == 'only_customers') {
                        $only_customers = $this->model_sale_customer->getOnlyCustomers(array('status' => 1, 'approved' => 1));
                        $results = $this->_unify_customer_list('email', $only_customers);
                    }
                }
            }
            foreach ($results as $result) {
                $customer_id = $result['customer_id'];
                $emails[$customer_id] = trim($result['email']);
            }
        }

        // All customers by name/email
        if (isset($data['to']) && is_array($data['to'])) {
            foreach ($data['to'] as $customer_id) {
                $customer_info = $this->model_sale_customer->getCustomer($customer_id);
                if ($customer_info) {
                    $emails[] = trim($customer_info['email']);
                }
            }
        }
        // All customers by product
        if (isset($data['products']) && is_array($data['products'])) {
            $emails = array();
            $this->load->model('sale/order');
            foreach ($data['products'] as $product_id) {
                // fore registered customers
                $results = $this->model_sale_customer->getCustomersByProduct($product_id);
                foreach ($results as $result) {
                    $emails[] = trim($result['email']);
                }
                //guest customers
                $results = $this->model_sale_order->getGuestOrdersWithProduct($product_id);
                foreach ($results as $result) {
                    $emails[] = trim($result['email']);
                }

            }
        }

        // Prevent Duplicates
        $emails = array_unique($emails);

        return array($emails, $subscribers);
    }

    private function _get_phone_list($data)
    {
        $subscribers = $phones = array();
        // All customers by group
        if (isset($data['recipient'])) {
            $results = array();
            if ($data['recipient'] == 'all_subscribers') {
                $all_subscribers = $this->model_sale_customer->getAllSubscribers(
                    array('filter' => array('newsletter_protocol' => 'sms'))
                );
                $results = $this->_unify_customer_list('sms', $all_subscribers);
                $subscribers = $results;
            } else {
                if ($data['recipient'] == 'only_subscribers') {
                    $only_subscribers = $this->model_sale_customer->getOnlyNewsletterSubscribers(
                        array('filter' => array('newsletter_protocol' => 'sms'))
                    );
                    $results = $this->_unify_customer_list('sms', $only_subscribers);
                    $subscribers = $results;
                } else {
                    if ($data['recipient'] == 'only_customers') {
                        $only_customers = $this->model_sale_customer->getOnlyCustomers(array('status' => 1, 'approved' => 1));
                        $results = $this->_unify_customer_list('sms', $only_customers);
                    }
                }
            }
            foreach ($results as $result) {
                $customer_id = $result['customer_id'];
                $phones[$customer_id] = trim($result['sms']);
            }

        }

        // All customers by name/email
        if (isset($data['to']) && is_array($data['to'])) {
            foreach ($data['to'] as $customer_id) {
                $customer_info = $this->model_sale_customer->getCustomer($customer_id);
                if ($customer_info) {
                    $phones[] = trim($customer_info['sms']);
                }
            }
        }
        // All customers by product
        if (isset($data['products']) && is_array($data['products']) && $data['products']) {
            $this->load->model('sale/order');
            foreach ($data['products'] as $product_id) {
                //for registered customers
                $results = $this->model_sale_customer->getCustomersByProduct($product_id);
                foreach ($results as $result) {
                    $phones[] = trim($result['sms']);
                }
                //for guest customers
                $results = $this->model_sale_order->getGuestOrdersWithProduct($product_id);

                foreach ($results as $result) {
                    $order_id = (int)$result['order_id'];
                    if (!$order_id) {
                        continue;
                    }

                    $uri = $this->im->getCustomerURI('sms', 0, $order_id);
                    if ($uri) {
                        $phones[] = $uri;
                    }
                }
            }
        }

        // Prevent Duplicates
        $phones = array_unique($phones);
        return array($phones, $subscribers);
    }

    /**
     * function filters customers list by unique email, to prevent duplicate emails
     *
     * @param string $field_name
     * @param array  $list
     *
     * @return array|bool
     */
    private function _unify_customer_list($field_name = 'email', $list = array())
    {
        if (!is_array($list)) {
            return array();
        }
        $output = array();
        foreach ($list as $c) {
            if (has_value($c[$field_name])) {
                $output[$c[$field_name]] = $c;
            }
        }
        return $output;
    }

}
