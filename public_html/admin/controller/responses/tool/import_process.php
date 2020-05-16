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

if (defined('IS_DEMO') && IS_DEMO) {
    header('Location: static_pages/demo_mode.php');
}

/**
 * Class ControllerResponsesSaleContact
 *
 * @property ModelToolImportProcess $model_tool_import_process
 */
class ControllerResponsesToolImportProcess extends AController
{
    public $data = array();
    public $errors = array();

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('tool/import_export');
    }

    public function buildTask()
    {
        $this->data['output'] = array();
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $file_format = $this->session->data['import']['format'];

        if ($this->request->is_POST() && $this->_validate()) {
            if ($file_format == 'internal') {
                $imp_data = $this->session->data['import'];
            } else {
                $imp_data = array_merge($this->session->data['import_map'], $this->session->data['import']);
            }

            $imp_data['store_id'] = $this->session->data['current_store_id'];
            $imp_data['language_id'] = $this->language->getContentLanguageID();

            $this->loadModel('tool/import_process');
            $task_details = $this->model_tool_import_process->createTask('import_wizard_'.date('Ymd-H:i:s'), $imp_data);
            $task_api_key = $this->config->get('task_api_key');

            if (!$task_details) {
                $this->errors = array_merge($this->errors, $this->model_tool_import_process->errors);
                $error = new AError("File Import Error: \n ".implode(' ', $this->errors));
                return $error->toJSONResponse('APP_ERROR_402',
                    array(
                        'error_text'  => implode(' ', $this->errors),
                        'reset_value' => true,
                    )
                );
            } elseif (!$task_api_key) {
                $error = new AError('files import error');
                return $error->toJSONResponse('APP_ERROR_402',
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
            $error = new AError(implode('<br>', $this->errors));
            return $error->toJSONResponse('VALIDATION_ERROR_406',
                array(
                    'error_text'  => implode('<br>', $this->errors),
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

    public function complete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/import_export');

        $task_id = (int)$this->request->post['task_id'];
        if (!$task_id) {
            return null;
        }

        //check task result
        $tm = new ATaskManager();
        $task_info = $tm->getTaskById($task_id);
        $task_result = $task_info['last_result'];
        if ($task_result) {
            $tm->deleteTask($task_id);
            $result_text = sprintf($this->language->get('text_complete_import'), (int)$task_info['settings']['total_rows_count']);
            if ((int)$task_info['settings']['failed_count']) {
                $result_text .= ' '.sprintf($this->language->get('text_complete_import_with_errors'), (int)$task_info['settings']['failed_count']);
            }
        }

        $log_file = $task_info['settings']['logfile'];
        if (is_file(DIR_LOGS.$log_file)) {
            $result_text .= '<br>'.sprintf($this->language->get('text_see_log'),
                    $this->html->getSecureURL('tool/error_log', '&filename='.$log_file), $log_file);
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode(array(
            'result'      => $task_result,
            'result_text' => $result_text,
        ))
        );
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
            return $error->toJSONResponse('APP_ERROR_402',
                array(
                    'error_text'  => $error_text,
                    'reset_value' => true,
                ));
        }

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode(array(
            'result'      => true,
            'result_text' => $result_text,
        ))
        );
    }

    protected function _validate()
    {
        if (!$this->user->canModify('sale/contact')) {
            $this->errors['warning'] = $this->language->get('error_permission');
            return false;
        }

        if (
            ($this->session->data['import']['format'] != 'internal' && !$this->session->data['import_map'])
            || !$this->session->data['import']
        ) {
            $this->errors['warning'] = $this->language->get('error_data_corrupted');
            return false;
        }

        return true;
    }

}
