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

class ControllerResponsesLocalisationLanguageDescription extends AController
{
    public $errors = array();
    public $data = array();

    public function buildTask()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->data['output'] = array();
        $this->loadLanguage('localisation/language');

        if ($this->_validate()) {
            $this->loadModel('localisation/language');
            $task_name = 'description_translation'.$this->request->post['source_language'].'_'.$this->request->post['language_id'];
            $task_details = $this->model_localisation_language->createTask($task_name, $this->request->post);
            $task_api_key = $this->config->get('task_api_key');

            if (!$task_details) {
                $this->errors = array_merge($this->errors, $this->model_localisation_language->errors);
                $error = new AError('translation task error');
                return $error->toJSONResponse(
                    'APP_ERROR_402',
                    array(
                        'error_text'  => implode(' ', $this->errors),
                        'reset_value' => true,
                    )
                );
            } elseif (!$task_api_key) {
                $error = new AError('translation task error');
                return $error->toJSONResponse(
                    'APP_ERROR_402',
                    array(
                        'error_text'  => 'Please set up Task API Key in the settings!',
                        'reset_value' => true,
                    )
                );
            } else {
                $task_details['task_api_key'] = $task_api_key;
                $task_details['url'] = HTTPS_SERVER.'task.php';
                $this->data['output']['task_details'] = $task_details;
            }

        } else {
            $error = new AError('translation task error');
            return $error->toJSONResponse(
                'APP_ERROR_402',
                array(
                    'error_text'  => implode(' ', $this->errors),
                    'reset_value' => true,
                )
            );
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['output']));

    }

    private function _validate()
    {
        if (!$this->user->canModify('localisation/language')) {
            $this->errors['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['source_language'] || !$this->request->post['translate_method']) {
            $this->errors['warning'] = $this->language->get('error_nothing_to_do');
        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->errors) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * post-trigger of task
     */
    public function complete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $task_id = (int)$this->request->post['task_id'];
        if ($task_id) {
            $tm = new ATaskManager();
            $tm->deleteTask($task_id);
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->cache->remove('*');

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode(array(
            'result'      => true,
            'result_text' => '',
        ))
        );
    }

    public function incomplete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadModel('user/user');
        $this->loadModel('localisation/language');
        $this->data = $this->language->getASet('localisation/language');

        $tm = new ATaskManager();
        $incomplete = $tm->getTasks(array(
            'filter' => array(
                'name' => 'description_translation',
            ),
        ));

        $k = 0;

        foreach ($incomplete as $incm_task) {
            //show all incomplete tasks for Top Administrator user group
            if ($this->user->getUserGroupId() != 1) {
                if ($incm_task['starter'] != $this->user->getId()) {
                    continue;
                }
            }
            //define incomplete tasks by last time run
            $max_exec_time = (int)$incm_task['max_execution_time'];
            if (!$max_exec_time) {
                //if no limitations for execution time for task - think it's 2 hours
                $max_exec_time = 7200;
            }
            if (time() - dateISO2Int($incm_task['last_time_run']) > $max_exec_time) {

                //get some info about task, for ex message-text and subject
                $steps = $tm->getTaskSteps($incm_task['task_id']);
                if (!$steps) {
                    $tm->deleteTask($incm_task['task_id']);
                }
                $user_info = $this->model_user_user->getUser($incm_task['starter']);
                $incm_task['starter_name'] = $user_info['username'].' '.$user_info['firstname'].' '.$user_info['lastname'];
                $step = current($steps);
                $step_settings = $step['settings'];
                $lang = $this->language->getLanguageDetailsByID((int)$step_settings['src_language_id']);
                $from = $lang['name'] ? $lang['name'] : 'unknown';
                $lang = $this->language->getLanguageDetailsByID((int)$step_settings['language_id']);
                $to = $lang['name'] ? $lang['name'] : 'unknown';

                $incm_task['title'] = sprintf($this->language->get('text_task_title'), $from, $to);
                $incm_task['date_added'] = dateISO2Display($incm_task['date_added'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));
                $incm_task['last_time_run'] = dateISO2Display($incm_task['last_time_run'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));

                $this->data['tasks'][$k] = $incm_task;
            }

            $k++;
        }

        $this->data['restart_task_url'] = $this->html->getSecureURL('r/localisation/language_description/restartTask');
        $this->data['complete_task_url'] = $this->html->getSecureURL('r/localisation/language_description/complete');

        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/localisation/language_translate_incomplete.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    public function abort()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $task_id = (int)$this->request->post['task_id'];
        if (!$task_id) {
            return null;
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
            return $error->toJSONResponse(
                'APP_ERROR_402',
                array(
                    'error_text'  => $error_text,
                    'reset_value' => true,
                )
            );
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(
            AJson::encode(
                array(
                    'result'      => true,
                    'result_text' => $result_text,
                )
            )
        );
    }

    public function restartTask()
    {
        $this->data['output'] = array();
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $task_id = (int)$this->request->get_or_post('task_id');
        $task_api_key = $this->config->get('task_api_key');
        $etas = array();
        if ($task_id) {
            $tm = new ATaskManager();

            $steps = $tm->getTaskSteps($task_id);
            foreach ($steps as $step) {
                $tm->updateStep($step['step_id'], array('status' => 1));
                $etas[$step['step_id']] = $step['max_execution_time'];
            }

            $task_details = $tm->getTaskById($task_id);
            if (!$task_details || !$task_details['steps']) {
                //remove task when it does not contain steps
                if (!$task_details['steps']) {
                    $tm->deleteTask($task_id);
                }
                $error_text = "Error: Cannot to restart task #".$task_id.'. Task removed.';
                $error = new AError($error_text);
                return $error->toJSONResponse(
                    'APP_ERROR_402',
                    array(
                        'error_text'  => $error_text,
                        'reset_value' => true,
                    )
                );
            } elseif (!$task_api_key) {
                $error = new AError('files backup error');
                return $error->toJSONResponse(
                    'APP_ERROR_402',
                    array(
                        'error_text'  => 'Please set up Task API Key in the settings!',
                        'reset_value' => true,
                    )
                );
            } else {
                $task_details['task_api_key'] = $task_api_key;
                $task_details['url'] = HTTPS_SERVER.'task.php';
                //change task status
                $task_details['status'] = $tm::STATUS_READY;
                $tm->updateTask($task_id, array('status' => $tm::STATUS_READY));
            }

            foreach ($etas as $step_id => $eta) {
                $task_details['steps'][$step_id]['eta'] = $eta;
            }

            $this->data['output']['task_details'] = $task_details;

        } else {
            $error = new AError(implode('<br>', $this->errors));
            return $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                array(
                    'error_text'  => 'Unknown task ID.',
                    'reset_value' => true,
                )
            );
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode($this->data['output']));

    }

}