<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

if (defined('IS_DEMO') && IS_DEMO) {
    header('Location: static_pages/demo_mode.php');
}

/**
 * Class ControllerResponsesSaleContact
 *
 * @property ModelSaleContact $model_sale_contact
 */
class ControllerResponsesSaleContact extends AController
{
    public $errors = [];

    public function buildTask()
    {
        $this->data['output'] = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->request->is_POST() && $this->_validate()) {
            $this->loadModel('sale/contact');
            $task_details = $this->model_sale_contact->createTask(
                'send_now_'.date('Ymd-H:i:s'),
                $this->request->post
            );
            $task_api_key = $this->config->get('task_api_key');

            if (!$task_details) {
                $this->errors = array_merge($this->errors, $this->model_sale_contact->errors);
                $error = new AError("Mail/Notification Sending Error: \n ".implode(' ', $this->errors));
                $error->toJSONResponse(
                    'APP_ERROR_402',
                    [
                        'error_text'  => implode(' ', $this->errors),
                        'reset_value' => true,
                    ]
                );
                return;
            } elseif (!$task_api_key) {
                $error = new AError('files backup error');
                $error->toJSONResponse(
                    'APP_ERROR_402',
                    [
                        'error_text'  => 'Please set up Task API Key in the settings!',
                        'reset_value' => true,
                    ]
                );
                return;
            } else {
                $task_details['task_api_key'] = $task_api_key;
                $task_details['url'] = HTTPS_SERVER.'task.php';
                $this->data['output']['task_details'] = $task_details;
            }
        } else {
            $error = new AError(implode('<br>', $this->errors));
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                [
                    'error_text'  => implode('<br>', $this->errors),
                    'reset_value' => true,
                ]
            );
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['output']));
    }

    public function complete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $task_id = (int) $this->request->post['task_id'];
        if (!$task_id) {
            return null;
        }

        //check task result
        $tm = new ATaskManager();
        $task_info = $tm->getTaskById($task_id);
        $task_result = $task_info['last_result'];
        if ($task_result) {
            $tm->deleteTask($task_id);
            $result_text = sprintf($this->language->get('text_success_sent'), $task_info['settings']['sent']);
            if (isset($this->session->data['sale_contact_presave'])) {
                unset($this->session->data['sale_contact_presave']);
            }
        } else {
            $result_text = $this->language->get('text_task_failed');
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(
            AJson::encode(
                [
                    'result'      => $task_result,
                    'result_text' => $result_text,
                ]
            )
        );
    }

    public function abort()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $task_id = (int) $this->request->post['task_id'];
        if (!$task_id) {
            return;
        }

        //check task result
        $tm = new ATaskManager();
        $task_info = $tm->getTaskById($task_id);

        if ($task_info) {
            $tm->deleteTask($task_id);
            $result_text = $this->language->get('text_success_abort');
        } else {
            $error_text = 'Task #'.$task_id.' not found!';
            $error = new AError($error_text);
            $error->toJSONResponse(
                'APP_ERROR_402',
                [
                    'error_text'  => $error_text,
                    'reset_value' => true,
                ]
            );
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(
            AJson::encode(
                [
                    'result'      => true,
                    'result_text' => $result_text,
                ]
            )
        );
    }

    public function restartTask()
    {
        $this->data['output'] = [];
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $task_id = (int) $this->request->get_or_post('task_id');
        $task_api_key = $this->config->get('task_api_key');
        $etas = [];
        if ($task_id) {
            $tm = new ATaskManager();

            $steps = $tm->getTaskSteps($task_id);
            foreach ($steps as $step) {
                if (!$step['settings']['to']) {
                    $tm->deleteStep($step['step_id']);
                } else {
                    $tm->updateStep($step['step_id'], ['status' => 1]);
                    $etas[$step['step_id']] = $step['max_execution_time'];
                }
            }

            $task_details = $tm->getTaskById($task_id);
            if (!$task_details || !$task_details['steps']) {
                //remove task when it does not contain steps
                if (!$task_details['steps']) {
                    $tm->deleteTask($task_id);
                }
                $error_text = "Mail/Notification Sending Error: Cannot to restart task #".$task_id.'. Task removed.';
                $error = new AError($error_text);
                $error->toJSONResponse(
                    'APP_ERROR_402',
                    [
                        'error_text'  => $error_text,
                        'reset_value' => true,
                    ]
                );
                return;
            } elseif (!$task_api_key) {
                $error = new AError('files backup error');
                $error->toJSONResponse(
                    'APP_ERROR_402',
                    [
                        'error_text'  => 'Please set up Task API Key in the settings!',
                        'reset_value' => true,
                    ]
                );
                return;
            } else {
                $task_details['task_api_key'] = $task_api_key;
                $task_details['url'] = HTTPS_SERVER.'task.php';
                //change task status
                $task_details['status'] = $tm::STATUS_READY;
                $tm->updateTask($task_id, ['status' => $tm::STATUS_READY]);
            }

            foreach ($etas as $step_id => $eta) {
                $task_details['steps'][$step_id]['eta'] = $eta;
            }

            $this->data['output']['task_details'] = $task_details;
        } else {
            $error = new AError(implode('<br>', $this->errors));
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                [
                    'error_text'  => 'Unknown task ID.',
                    'reset_value' => true,
                ]
            );
            return;
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['output']));
    }

    public function presave()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->session->data['sale_contact_presave'] = $this->request->post ?? [];

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function incomplete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadModel('user/user');
        $this->data = $this->language->getASet('sale/contact');

        $tm = new ATaskManager();
        $incomplete = $tm->getTasks(
            [
                'filter' => [
                    'name' => 'send_now',
                ],
            ]
        );

        $k = 0;
        foreach ($incomplete as $incm_task) {
            //show all incomplete tasks for Top Administrator user group
            if ($this->user->getUserGroupId() != 1) {
                if ($incm_task['starter'] != $this->user->getId()) {
                    continue;
                }
            }
            //define incomplete tasks by last time run
            $max_exec_time = (int) $incm_task['max_execution_time'];
            if (!$max_exec_time) {
                //if no limitations for execution time for task - think it's 2 hours
                //$max_exec_time = 7200;
                $max_exec_time = 7200;
            }
            if (time() - dateISO2Int($incm_task['last_time_run']) > $max_exec_time) {
                //get some info about task, for ex message-text and subject
                $steps = $tm->getTaskSteps($incm_task['task_id']);
                if (!$steps) {
                    $tm->deleteTask($incm_task['task_id']);
                }
                $user_info = $this->model_user_user->getUser($incm_task['starter']);
                $incm_task['starter_name'] = $user_info['username']
                    .' '
                    .$user_info['firstname']
                    .' '.
                    $user_info['lastname'];

                $step = current($steps);
                $step_settings = $step['settings'];
                if ($step_settings['subject']) {
                    $incm_task['subject'] = $step_settings['subject'];
                }
                $incm_task['message'] = mb_substr($step_settings['message'], 0, 300);
                $incm_task['date_added'] = dateISO2Display(
                    $incm_task['date_added'],
                    $this->language->get('date_format_short').' '.$this->language->get('time_format')
                );
                $incm_task['last_time_run'] = dateISO2Display(
                    $incm_task['last_time_run'],
                    $this->language->get('date_format_short').' '.$this->language->get('time_format')
                );
                $incm_task['sent'] = sprintf(
                    $this->language->get('text_sent'), $incm_task['settings']['sent'],
                    $incm_task['settings']['recipients_count']
                );
                $this->data['tasks'][$k] = $incm_task;
            }
            $k++;
        }

        $this->data['restart_task_url'] = $this->html->getSecureURL('r/sale/contact/restartTask');
        $this->data['complete_task_url'] = $this->html->getSecureURL('r/sale/contact/complete');
        $this->data['abort_task_url'] = $this->html->getSecureURL('r/sale/contact/abort');

        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/sale/contact_incomplete.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function _validate()
    {
        if (!$this->user->canModify('sale/contact')) {
            $this->errors['warning'] = $this->language->get('error_permission');
        }
        $post = $this->request->post;
        if ($post['protocol'] == 'email') {
            if (!$post['subject']) {
                $this->errors['subject'] = $this->language->get('error_subject');
            }
        }

        if (!$post['message']) {
            $this->errors['message'] = $this->language->get('error_message_'.$post['protocol']);
        }

        if (!$post['recipient'] && !$post['to'] && !$post['products']) {
            $this->errors['recipient'] = $this->language->get('error_recipients');
        }

        $this->extensions->hk_ValidateData($this);

        return (!$this->errors);
    }

    public function getRecipientsCount()
    {
        $this->loadModel('sale/customer');
        $this->loadModel('sale/order');

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $recipient = $this->request->post['recipient'];
        $protocol = $this->request->post['protocol'];

        $db_filter = [
            'status' => 1,
            'approved' => 1
        ];

        if ($protocol == 'sms') {
            $db_filter['filter']['only_with_mobile_phones'] = 1;
        }

        $newsletter_db_filter = $db_filter;
        $newsletter_db_filter['filter']['newsletter_protocol'] = $protocol;

        $count = 0;
        $emails = [];

        switch ($recipient) {
            case 'all_subscribers':
                $count = $this->model_sale_customer->getTotalAllSubscribers($newsletter_db_filter);
                break;
            case 'only_subscribers':
                $count = $this->model_sale_customer->getTotalOnlyNewsletterSubscribers($newsletter_db_filter);
                break;
            case 'only_customers':
                $count = $this->model_sale_customer->getTotalOnlyCustomers($db_filter);
                break;
            case 'ordered':
                $products = $this->request->post['products'];
                if (is_array($products)) {
                    foreach ($products as $product_id) {
                        $results = $this->model_sale_customer->getCustomersByProduct($product_id);
                        foreach ($results as $result) {
                            $emails[] = trim($result[$protocol]);
                        }
                        //for guests
                        $results = $this->model_sale_order->getGuestOrdersWithProduct($product_id);
                        foreach ($results as $result) {
                            if ($protocol == 'email') {
                                $emails[] = trim($result[$protocol]);
                            } elseif ($protocol == 'sms') {
                                $order_id = (int) $result['order_id'];
                                if (!$order_id) {
                                    continue;
                                }
                                $uri = $this->im->getCustomerURI('sms', 0, $order_id);
                                if ($uri) {
                                    $emails[] = $uri;
                                }
                            }
                        }
                    }
                }
                $count = sizeof(array_unique($emails));
                break;
        }

        if ($count) {
            $text = sprintf($this->language->get('text_attention_recipients_count'), $count);
        } else {
            $text = $this->language->get('error_'.$protocol.'_no_recipients');
        }

        $this->data['output'] = [
            'count' => (int) $count,
            'text'  => $text,
        ];

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['output']));
    }

}
