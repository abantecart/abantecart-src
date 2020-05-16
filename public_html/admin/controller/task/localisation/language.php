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

class ControllerTaskLocalisationLanguage extends AController
{
    public $data = array();

    public function main()
    {
    }

    public function translate()
    {
        list($task_id, $step_id,) = func_get_args();
        $this->load->library('json');

        if (!$task_id || !$step_id) {
            $error_text = 'Cannot run task step. Task_id (or step_id) has not been set.';
            $this->_return_error($error_text);
        }

        $tm = new ATaskManager();
        $step_info = $tm->getTaskStep($task_id, $step_id);

        if ($step_info['sort_order'] == 1) {
            $tm->updateTask($task_id,
                array(
                    'last_time_run' => date('Y-m-d H:i:s'),
                )
            );
        }

        if (!$step_info) {
            $error_text = 'Cannot run task step. Looks like task_id '.$task_id.' does not contain step_id '.$step_id;
            $this->_return_error($error_text);
        }

        $tm->updateStep($step_id, array('last_time_run' => date('Y-m-d H:i:s')));

        if (!$step_info['settings']) {
            $error_text = 'Cannot run task step_id'.$step_id.'. Unknown settings for it.';
            $this->_return_error($error_text);
        }

        //send emails in loop and update task's step info for restarting if step or task failed
        $step_settings = $step_info['settings'];
        $table_name = $step_settings['table']['table_name'];
        $dst_language_id = $step_settings['language_id'];
        $src_language_id = $step_settings['src_language_id'];

        $pkeys = $this->language->getPrimaryKeys($table_name);
        $pkeys = array_merge($pkeys, array_keys($step_settings['table']['indexes']));
        $pkeys = array_unique($pkeys);

        $specific_sql = '';
        foreach ($pkeys as $pk) {
            if ($pk == 'language_id') {
                continue;
            }
            foreach ($step_settings['table']['indexes'] as $k => $v) {
                $specific_sql .= " AND `".$k."` IN ('".implode("', '", $v)."')";
            }
        }

        //do translate only when items presents
        if ($specific_sql) {
            $translate_result = $this->language->cloneLanguageRows($table_name,
                $pkeys,
                $dst_language_id,
                $src_language_id,
                $specific_sql,
                $step_settings['translate_method']);
        }

        if ($translate_result) {
            //update task details to show them at the end
            $tm->updateTaskDetails($task_id,
                array(
                    //set 1 as "admin"
                    'created_by' => 1,
                    'settings'   => array(),
                ));
            $step_result = true;
        } else {
            $step_result = false;
        }

        $tm->updateStep($step_id, array('last_result' => $step_result));

        if (!$step_result) {
            $this->_return_error('Some errors during step run. See log for details.');
        }

        $this->response->setOutput(AJson::encode(array('result' => true, 'message' => $translate_result)));
    }

    private function _return_error($error_text)
    {
        $this->response->setOutput(AJson::encode(array('result' => false, 'error_text' => $error_text)));
    }

}
