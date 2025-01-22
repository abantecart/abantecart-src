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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerTaskLocalisationLanguage extends AController
{
    public function main(){ }

    public function translate(...$args)
    {
        $isTranslated = false;
        list($task_id, $step_id,) = $args;
        $this->load->library('json');

        if (!$task_id || !$step_id) {
            $error_text = 'Cannot run task step. Task_id (or step_id) has not been set.';
            $this->_return_error($error_text);
        }

        $tm = new ATaskManager();
        $step_info = $tm->getTaskStep($task_id, $step_id);

        if ($step_info['sort_order'] == 1) {
            $tm->updateTask(
                $task_id,
                [
                    'last_time_run' => date('Y-m-d H:i:s'),
                ]
            );
        }

        if (!$step_info) {
            $error_text = 'Cannot run task step. Looks like task_id '.$task_id.' does not contain step_id '.$step_id;
            $this->_return_error($error_text);
        }

        $tm->updateStep($step_id, ['last_time_run' => date('Y-m-d H:i:s')]);

        if (!$step_info['settings']) {
            $error_text = 'Cannot run task step_id'.$step_id.'. Unknown settings for it.';
            $this->_return_error($error_text);
        }

        //send emails in loop and update task's step info for restarting if step or task failed
        $stepSettings = $step_info['settings'];
        $table_name = $stepSettings['table']['table_name'];
        $dstLanguageId = $stepSettings['language_id'];
        $srcLanguageId = $stepSettings['src_language_id'];

        $pKeys = $this->language->getPrimaryKeys($table_name);
        $pKeys = array_merge($pKeys, array_keys($stepSettings['table']['indexes']));
        $pKeys = array_unique($pKeys);

        $specificSql = '';
        foreach ($pKeys as $pk) {
            if ($pk == 'language_id') {
                continue;
            }
            foreach ($stepSettings['table']['indexes'] as $k => $v) {
                $specificSql .= " AND `".$k."` IN ('".implode("', '", $v)."')";
            }
        }

        //do translate only when items presents
        if ($specificSql) {
            $isTranslated = $this->language->cloneLanguageRows(
                $table_name,
                $pKeys,
                $dstLanguageId,
                $srcLanguageId,
                $specificSql,
                $stepSettings['translate_method']
            );
        }

        if ($isTranslated) {
            //update task details to show them at the end
            $tm->updateTaskDetails(
                $task_id,
                [
                    //set 1 as "admin"
                    'created_by' => 1,
                    'settings'   => [],
                ]
            );
            $step_result = true;
        } else {
            $step_result = false;
        }

        $tm->updateStep($step_id, ['last_result' => $step_result]);

        if (!$step_result) {
            $this->_return_error('Some errors during step run. See log for details.');
        }

        $this->response->setOutput(AJson::encode(['result' => true, 'message' => $isTranslated]));
    }

    protected function _return_error($error_text)
    {
        $this->response->setOutput(AJson::encode(['result' => false, 'error_text' => $error_text]));
    }

}
