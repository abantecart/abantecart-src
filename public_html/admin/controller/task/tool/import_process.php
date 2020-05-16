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
 * Class ControllerTaskToolImportProcess
 *
 * @property ModelToolImportProcess $model_tool_import_process
 */
class ControllerTaskToolImportProcess extends AController
{
    public $data = array();
    protected $success_count = 0;
    protected $failed_count = 0;

    public function processRows()
    {
        list($task_id, $step_id,) = func_get_args();
        $this->load->library('json');
        //for aborting process
        ignore_user_abort(false);
        session_write_close();

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->success_count = 0;
        $this->failed_count = 0;
        $result = $this->_process($task_id, $step_id);
        if (!$this->success_count) {
            $result = false;
        }
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $output = array('result' => $result);
        if ($result) {
            $output['message'] = $this->success_count.' rows processed success. ';
            if ($this->failed_count) {
                $output['message'] .= $this->failed_count.' rows processed with error.';
            }
        } else {
            $output['error_text'] = $this->failed_count.' rows processed with error.';
        }
        $this->response->setOutput(AJson::encode($output));
    }

    private function _process($task_id, $step_id)
    {

        if (!$task_id || !$step_id) {
            $error_text = 'Cannot run task step. Task_id (or step_id) has not been set.';
            $this->_return_error($error_text);
        }

        $tm = new ATaskManager();
        $task_info = $tm->getTaskById($task_id);
        //get setting with import details
        $import_details = $task_info['settings']['import_data'];
        $file_format = $import_details['format'];
        $step_info = $tm->getTaskStep($task_id, $step_id);
        if (!$step_info['settings']) {
            $error_text = "Cannot run task #".$task_id." step #".$step_id.". Can not locate settings for the step.";
            $this->_return_error($error_text);
        }
        //record the start
        $tm->updateStep($step_id, array('last_time_run' => date('Y-m-d H:i:s')));

        $return = array();
        $start = $step_info['settings']['start'];
        $stop = $step_info['settings']['stop'];
        $filename = $import_details['file'];
        $type = $import_details['table'];
        $delimiter = $import_details['delimiter'];

        $step_result = false;
        $step_failed_count = 0;

        //read records from source file
        if ($file_format == 'internal') {
            $a_data = new AData();
            $a_data->setLogFile(DIR_LOGS."import_".$task_id.".txt");
            //import each row separately
            for ($i = $start; $i <= $stop; $i++) {
                $csv_array = $a_data->CSV2ArrayFromFile($filename, array_search($delimiter, $a_data->csvDelimiters), $i, 1);
                if ($csv_array) {
                    $results = $a_data->importData($csv_array);
                } else {
                    $results = array('error' => true);
                }

                if (isset($results['error'])) {
                    $step_failed_count++;
                } else {
                    $this->success_count++;
                }
            }
        } else {
            // new import process
            $records = $this->readFileSeek($filename, $delimiter, '"', $start, ($stop - $start));
            if (count($records)) {
                //process column names
                $columns = $records[0];

                //skip header and process each record
                array_shift($records);
                $this->loadModel('tool/import_process');
                $step_failed_count = 0;

                foreach ($records as $index => $rowData) {
                    $vals = array();
                    //check if we match row data count to header

                    if (count($rowData) != count($columns)) {
                        //incomplete row. Exit
                        $return[] = "Error: incomplete data in row number: ".$index." with: ".$rowData[0];
                        $step_failed_count++;
                        continue;
                    }

                    for ($i = 0; $i <= count($columns); $i++) {
                        $vals[$columns[$i]] = $rowData[$i];
                    }

                    //main driver to process data and import
                    $method = "process_".$type."_record";
                    try {
                        $result = $this->model_tool_import_process->$method($task_id, $vals, $import_details);
                    } catch (AException $e) {
                        $return[] = "Import Error row number {$index} with {$rowData[0]}: ".$e->getMessage();
                        $result = false;
                    }

                    if ($result) {
                        $this->success_count++;
                    } else {
                        $step_failed_count++;
                    }
                }
                if (!empty($return)) {
                    $imp_log = new ALog(DIR_LOGS.$type."_import_{$task_id}.txt");
                    foreach ($return as $message) {
                        $imp_log->write($message);
                    }
                }
            } else {
                //if nothing to todo
                return false;
            }
        }

        //update task details
        $this->failed_count = $this->failed_count + $step_failed_count;
        $task_settings = $task_info['settings'];
        if ($file_format == 'internal') {
            $task_settings['logfile'] = "import_".$task_id.".txt";
        } else {
            $task_settings['logfile'] = $type.'_import_'.$task_id.'.txt';
        }
        $task_settings['success_count'] = (int)$task_info['settings']['success_count'] + $this->success_count;
        $task_settings['failed_count'] = (int)$task_info['settings']['failed_count'] + $this->failed_count;

        $tm->updateTaskDetails($task_id, array('settings' => $task_settings));
        //sends always true as result
        $tm->updateStep($step_id, array('last_result' => $this->failed_count ? false : true));
        //all done, clear cache
        $this->cache->remove('*');
        //return always true fo import process only. we think one failed row cannot block task
        return true;
    }

    protected function readFileSeek($source, $delimiter, $enclosure = '"', $line_num = 1, $range = 1)
    {
        if (!$source) {
            return array();
        }

        ini_set('auto_detect_line_endings', true);
        $fh = fopen($source, 'r');
        if (!$fh || !is_resource($fh)) {
            return array();
        }

        $lineNo = 0;
        $startLine = $line_num;
        $endLine = $line_num + $range;
        //always return first line with header
        $buffer = array(0 => fgetcsv($fh, 0, $delimiter, $enclosure));
        while (($data = fgetcsv($fh, 0, $delimiter, $enclosure)) !== false) {
            if ($lineNo >= $startLine) {
                $buffer[] = $data;
            }
            if ($lineNo == $endLine) {
                break;
            }
            $lineNo++;
        }
        fclose($fh);
        return $buffer;
    }

    protected function _return_error($error_text)
    {
        $error = new AError($error_text);
        $error->toLog()->toDebug();
        return $error->toJSONResponse('APP_ERROR_402',
            array(
                'error_text'  => $error_text,
                'reset_value' => true,
            ));
    }

}
