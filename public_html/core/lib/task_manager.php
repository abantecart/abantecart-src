<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ATaskManager
 *
 * @link http://docs.abantecart.com/pages/developer/tasks_processing.html
 */
class ATaskManager
{
    public $errors = []; // errors during process
    /** @var ADB */
    protected $db;
    protected $starter;
    /**
     * @var ALog
     */
    protected $logger;
    /**
     * @var string - can be 'html' for running task.php directly from browser,
     *                      'ajax' - for running task by ajax-requests
     *                      and 'cli' - bash shell run
     */
    private $mode;

    protected $run_log = [];
    /**
     * @var string can be 'simple' or 'detailed'
     */
    protected $log_level = 'simple';
    const STATUS_DISABLED = 0;
    const STATUS_READY = 1;
    const STATUS_RUNNING = 2;
    const STATUS_FAILED = 3;
    const STATUS_SCHEDULED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_INCOMPLETE = 6;

    /**
     * @param string $mode Can be "html"("ajax") or "cli". Needed for run log format
     *
     * @throws AException
     */
    public function __construct(string $mode = 'html')
    {
        $this->mode = in_array($mode, ['html', 'ajax', 'cli']) ? $mode : 'html';
        // who is initiator of process, admin or storefront
        $this->starter = IS_ADMIN === true ? 1 : 0;
        $this->logger = new ALog(DIR_LOGS . 'task_log.txt');
        $this->db = Registry::getInstance()->get('db');
    }

    public function setRunLogLevel($level = 'simple')
    {
        $this->log_level = $level;
    }

    public function runTasks($options = [])
    {
        $this->run_log = [];
        if ($options['force-all']) {
            $allTasks = $this->getTasks();
            foreach ($allTasks as $t) {
                if ($t['status'] != static::STATUS_RUNNING) {
                    $this->updateTask((int)$t['task_id'], ['status' => static::STATUS_READY]);
                }
            }
        }
        $task_list = $this->getReadyTasks();
        // run loop tasks
        foreach ($task_list as $task) {
            //check interval and skip task
            $this->toLog('Task_id: ' . $task['task_id'] . " state - running.");
            if ($task['interval'] > 0
                && (time() - dateISO2Int($task['last_time_run']) >= $task['interval'] || is_null($task['last_time_run']))
            ) {
                $this->toLog('Task_id: ' . $task['task_id'] . ' skipped.');
                continue;
            }
            $task_settings = unserialize($task['settings']);

            $this->runSteps((int)$task['task_id'], (array)$task_settings);
            $this->detectAndSetTaskStatus((int)$task['task_id']);
            $this->toLog('Task_id: ' . $task['task_id'] . ' state - finished.');
        }
    }

    /**
     * @param int $task_id
     *
     * @return bool
     * @throws AException
     */
    public function runTask(int $task_id)
    {
        $task = $this->getReadyTasks($task_id);
        if (!$task) {
            return false;
        }

        $this->toLog('Task_id: ' . $task_id . ' state - running.');

        //check interval and skip task
        //check if task ran first time or
        if ($task['interval'] > 0
            && (!$task['last_time_run'] || time() - dateISO2Int($task['last_time_run']) >= $task['interval'])
        ) {
            $this->toLog('Warning: task_id ' . $task_id . ' skipped. Task interval.');
            return false;
        }
        $task_settings = unserialize($task['settings']);
        $task_result = $this->runSteps($task_id, (array)$task_settings);
        $this->detectAndSetTaskStatus($task_id);
        $this->toLog('Task_id: ' . $task_id . ' state - finished.');
        return $task_result;
    }

    /**
     * @param int $task_id
     *
     * @return array
     * @throws AException
     */
    protected function getReadyTasks(int $task_id = 0)
    {
        //get list only ready tasks for needed start-side (sf, admin or both)
        $sql = "SELECT *
                FROM " . $this->db->table('tasks') . " t
                WHERE t.status = " . self::STATUS_READY . "
                    AND t.starter IN ('" . $this->starter . "','2')
                    " . ($task_id ? " AND t.task_id = " . $task_id : '');
        $result = $this->db->query($sql);
        return $task_id ? $result->row : $result->rows;
    }

    /**
     * @param int $task_id
     * @param int $step_id
     * @return bool
     */
    public function canStepRun(int $task_id, int $step_id)
    {
        if (!$step_id || !$task_id) {
            return false;
        }

        $all_steps = $this->getTaskSteps($task_id);
        if (!$all_steps) {
            return false;
        }
        foreach ($all_steps as $step) {
            if ($step['step_id'] == $step_id) {
                break;
            }
            //do not allow run step if previous step failed and interrupted task
            if ($step['status'] == self::STATUS_FAILED && $step['settings']['interrupt_on_step_fault'] === true) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $task_id
     * @param int $step_id
     *
     * @return bool
     */
    public function isLastStep(int $task_id, int $step_id)
    {
        if (!$step_id || !$task_id) {
            $this->toLog('Error: Tried to check is step_id: ' . $step_id . ' of task_id: ' . $task_id . " last, but fail!");
            return false;
        }

        $all_steps = $this->getTaskSteps($task_id);
        if (!$all_steps) {
            $this->toLog(
                'Error: Tried to check is step_id: ' . $step_id . ' of task_id: ' . $task_id . " last, but steps list empty!"
            );
            return false;
        }

        $last_step = array_pop($all_steps);
        if ($last_step['step_id'] == $step_id && $last_step['task_id'] == $task_id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $step_details
     *
     * @return bool
     * @throws AException
     */
    public function runStep(array $step_details)
    {

        $task_id = (int)$step_details['task_id'];
        $step_id = (int)$step_details['step_id'];
        if (!$step_id || !$task_id) {
            return false;
        }

        //change status to active
        $this->updateStepState(
            $step_id,
            [
                'last_time_run' => date('Y-m-d H:i:s'),
                //change status of step to "running" while it run
                'status'        => self::STATUS_READY,
            ]
        );

        try {
            $dd = new ADispatcher($step_details['controller'], [$task_id, $step_id, $step_details['settings']]);
            // waiting for result array from step's controller
            $response = $dd->dispatchGetOutput();

            //check is result have json-formatted string
            $json = json_decode($response, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $response = $json;
            }
            $result = (isset($response['result']) && $response['result']);
            if ($result) {
                $response_message = $response['message'] ?? '';
            } else {
                $response_message = $response['error_text'] ?? '';
            }
        } catch (Exception $e) {
            $response_message = $e->getMessage();
            $this->logger->write($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            $result = false;
        }

        $this->updateStepState(
            $step_id,
            [
                'result' => (int)$result,
                'status' => ($result ? self::STATUS_COMPLETED : self::STATUS_FAILED),
            ]
        );

        if (!$result) {
            //write to AbanteCart log
            $error_msg = 'Task_id: ' . $task_id . ' : step_id: ' . $step_id . ' - Failed. ' . $response_message;
            $this->logger->write($error_msg . "\n step details:\n" . var_export($step_details, true));
            //write to task log
            $this->toLog($error_msg, 0);
        } else {
            //write to task log
            $this->toLog('Task_id: ' . $task_id . ' : step_id: ' . $step_id . '. ' . $response_message, 1);
        }
        return $result;
    }

    /**
     * @param int $task_id
     * @return void
     * @throws AException
     */
    public function detectAndSetTaskStatus(int $task_id)
    {
        $all_steps = $this->getTaskSteps($task_id);
        $completed_cnt = 0;
        $task_status = 0;
        foreach ($all_steps as $step) {
            if (!$step['status']) {
                continue;
            }
            //if one step failed - task failed
            if ($step['status'] == self::STATUS_FAILED) {
                $task_status = self::STATUS_FAILED;
                break;
            }
            if ($step['status'] == self::STATUS_COMPLETED) {
                $completed_cnt++;
            }
        }

        if (!$task_status) {
            if ($completed_cnt == sizeof($all_steps)) {
                $task_status = self::STATUS_COMPLETED;
            } else {
                $task_status = self::STATUS_INCOMPLETE;
            }
        }

        $this->updateTask($task_id, ['status' => $task_status]);
    }

    /**
     * @param int $task_id
     * @param array $task_settings - for future. it can be a reference for callback
     *
     * @return bool
     * @throws AException
     */
    protected function runSteps(int $task_id, array $task_settings = [])
    {
        if (!$task_id) {
            return false;
        }

        $this->updateTaskState(
            $task_id,
            [
                'status'        => self::STATUS_RUNNING,
                'last_time_run' => date('Y-m-d H:i:s'),
            ]
        );

        //get steps
        $steps = $this->getReadyTaskSteps($task_id);
        $task_result = true;
        // total count of steps to calculate percentage (for future)
        $steps_count = sizeof($steps);
        $k = 0;
        foreach ($steps as $step_details) {
            $step_result = $this->runStep((array)$step_details);
            if (!$step_result) {
                $task_result = false;
                //interrupt task process when step failed
                if ($step_details['interrupt_on_step_fault'] === true) {
                    break;
                }
            } else {
                if ($this->log_level == 'detailed') {
                    $this->run_log[] = 'Step #' . $step_details['step_id'] . ' result: success';
                }
            }
            $this->updateTaskState($task_id, ['progress' => ceil($k * 100 / $steps_count)]);
            $k++;
        }

        return $task_result;
    }

    /**
     * @param int $task_id
     * @param array $state
     *
     * @return bool
     * @throws AException
     */
    protected function updateTaskState(int $task_id, array $state = [])
    {
        if (!$task_id) {
            return false;
        }

        $filedList = [
            'last_result',
            'last_time_run',
            'status',
            'progress',
        ];
        $data = [];
        foreach ($filedList as $fld_name) {
            if (isset($state[$fld_name])) {
                $data[$fld_name] = $state[$fld_name];
            }
        }
        return $this->updateTask($task_id, $data);
    }

    /**
     * @param int $step_id
     * @param array $state
     *
     * @return bool
     * @throws AException
     */
    protected function updateStepState(int $step_id, array $state = [])
    {
        $fieldList = [
            'task_id',
            'last_result',
            'last_time_run',
            'status',
        ];
        $data = [];
        foreach ($fieldList as $fld_name) {
            if (isset($state[$fld_name])) {
                $data[$fld_name] = $state[$fld_name];
            }
        }
        return $this->updateStep($step_id, $data);
    }

    /**
     * @param string $message
     * @param int|null $msg_code - can be 0 - fail, 1 -success
     *
     * @void
     */
    public function toLog(string $message, ?int $msg_code = 1)
    {
        if (!$message) {
            return;
        }
        if ($this->mode == 'html') {
            $this->run_log[] = '<i style="color: ' . ($msg_code ? 'green' : 'red') . '">' . $message . "</i>";
        } else {
            $this->run_log[] = $message;
        }
        $this?->logger?->write($message);
    }

    /**
     * @param array $data
     *
     * @return int
     * @throws AException
     */
    public function addTask(array $data)
    {
        if (!$data) {
            $this->errors[] = 'Error: Can not to create task. Empty data given.';
            return false;
        }
        // check
        $sql = "SELECT *
                FROM " . $this->db->table('tasks') . "
                WHERE name = '" . $this->db->escape($data['name']) . "'";
        $res = $this->db->query($sql);
        if ($res->num_rows) {
            $this->deleteTask((int)$res->row['task_id']);
            $this->toLog('Error: Task with name "' . $data['name'] . '" is already exists. Override!');
        }

        $sql = "INSERT INTO " . $this->db->table('tasks') . "
                        (`name`,
                        `starter`,
                        `status`,
                        `start_time`,
                        `last_time_run`,
                        `progress`,
                        `last_result`,
                        `run_interval`,
                        `max_execution_time`,
                        `date_added`)
                VALUES ('" . $this->db->escape($data['name']) . "',
                        '" . (int)$data['starter'] . "',
                        '" . (int)$data['status'] . "',
                        " . $this->db->stringOrNull($data['start_time']) . ",
                        " . $this->db->stringOrNull($data['last_time_run']) . ",
                        '" . (int)$data['progress'] . "',
                        '" . (int)$data['last_result'] . "',
                        '" . (int)$data['run_interval'] . "',
                        '" . (int)$data['max_execution_time'] . "',
                        NOW())";
        $this->db->query($sql);
        $task_id = $this->db->getLastId();
        if (has_value($data['created_by']) || has_value($data['settings'])) {
            $this->updateTaskDetails($task_id, $data);
        }
        return $task_id;
    }

    /**
     * @param int $task_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function updateTask(int $task_id, array $data = [])
    {
        $task_id = (int)$task_id;
        if (!$task_id) {
            return false;
        }

        $tableFields = [
            'name'               => 'string',
            'starter'            => 'int',
            'status'             => 'int',
            'start_time'         => 'timestamp',
            'last_time_run'      => 'timestamp',
            'progress'           => 'int',
            'last_result'        => 'int',
            'run_interval'       => 'int',
            'max_execution_time' => 'int'
        ];
        $update = [];
        foreach ($tableFields as $fld_name => $fld_type) {
            if (!isset($data[$fld_name])) {
                continue;
            }
            switch ($fld_type) {
                case 'int':
                    $value = (int)$data[$fld_name];
                    break;
                case 'timestamp':
                    $value = $this->db->stringOrNull($data[$fld_name]);
                    break;
                default:
                    $value = "'" . $this->db->escape($data[$fld_name]) . "'";
            }
            $update[] = $fld_name . " = " . $value;
        }

        if (!$update) {
            return false;
        }

        $sql = "UPDATE " . $this->db->table('tasks') . "
                SET " . implode(', ', $update) . "
                WHERE task_id = " . (int)$task_id;
        $this->db->query($sql);

        if (isset($data['created_by']) || isset($data['settings'])) {
            $this->updateTaskDetails($task_id, $data);
        }
        return true;
    }

    /**
     * function insert or update task details
     *
     * @param       $task_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function updateTaskDetails($task_id, $data = [])
    {
        $task_id = (int)$task_id;
        if (!$task_id) {
            return false;
        }

        if (!is_string($data['settings'])) {
            $data['settings'] = serialize($data['settings']);
        }

        $sql = "SELECT *
                FROM " . $this->db->table('task_details') . "
                WHERE task_id = " . $task_id;
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            $sql = "UPDATE " . $this->db->table('task_details') . "
                    SET settings = '" . $this->db->escape($data['settings']) . "'
                    WHERE task_id = " . $task_id;
        } else {
            $sql = "INSERT INTO " . $this->db->table('task_details') . "
                        (task_id, created_by, settings)
                     VALUES (   " . $task_id . ",
                                '" . $this->db->escape($data['created_by'] ?? 1) . "',
                                '" . $this->db->escape($data['settings']) . "'
                            )";
        }
        $this->db->query($sql);
        return true;
    }

    /**
     * @param array $data
     *
     * @return bool|int
     * @throws AException
     */
    public function addStep(array $data = [])
    {
        if (!$data) {
            $this->errors[] = 'Error: Can not to create task\'s step. Empty data given.';
            return false;
        }
        $data['settings'] = !is_string($data['settings']) ? serialize($data['settings']) : $data['settings'];
        $sql = "INSERT INTO " . $this->db->table('task_steps') . "
                (`task_id`,
                `sort_order`,
                `status`,
                `last_time_run`,
                `last_result`,
                `max_execution_time`,
                `controller`,
                `settings`)
                VALUES (
                        '" . (int)$data['task_id'] . "',
                        '" . (int)$data['sort_order'] . "',
                        '" . (int)$data['status'] . "',
                        " . $this->db->stringOrNull($data['last_time_run']) . ",
                        '" . (int)$data['last_result'] . "',
                        '" . (int)$data['max_execution_time'] . "',
                        '" . $this->db->escape($data['controller']) . "',
                        '" . $this->db->escape($data['settings']) . "'
                        )";
        $this->db->query($sql);
        return $this->db->getLastId();
    }

    /**
     * @param int $step_id
     * @param array $data
     *
     * @return bool
     * @throws AException
     */
    public function updateStep(int $step_id, array $data = [])
    {
        if (!$step_id) {
            return false;
        }

        $fieldList = [
            'task_id'            => 'int',
            'starter'            => 'int',
            'status'             => 'int',
            'sort_order'         => 'int',
            'last_time_run'      => 'timestamp',
            'last_result'        => 'int',
            'max_execution_time' => 'int',
            'controller'         => 'string',
            'settings'           => 'string'
        ];
        $update = [];
        foreach ($fieldList as $fld_name => $fld_type) {
            if (isset($data[$fld_name])) {
                switch ($fld_type) {
                    case 'int':
                        $value = (int)$data[$fld_name];
                        break;
                    case 'timestamp':
                        $value = $this->db->stringOrNull($data[$fld_name]);
                        break;
                    default:
                        $value = "'" . $this->db->escape($data[$fld_name]) . "'";
                }
                $update[] = $fld_name . " = " . $value;
            }
        }
        if (!$update) { //if nothing to update
            return false;
        }

        $sql = "UPDATE " . $this->db->table('task_steps') . "
                SET " . implode(', ', $update) . "
                WHERE step_id = " . $step_id;
        $this->db->query($sql);
        return true;
    }

    /**
     * @param int $task_id
     * @throws AException
     */
    public function deleteTask(int $task_id)
    {

        $sql[] = "DELETE FROM " . $this->db->table('task_steps') . " WHERE task_id = '" . $task_id . "'";
        $sql[] = "DELETE FROM " . $this->db->table('task_details') . " WHERE task_id = '" . $task_id . "'";
        $sql[] = "DELETE FROM " . $this->db->table('tasks') . " WHERE task_id = '" . $task_id . "'";
        foreach ($sql as $q) {
            $this->db->query($q);
        }
    }

    /**
     * @param int $step_id
     * @throws AException
     */
    public function deleteStep(int $step_id)
    {
        $sql = "DELETE FROM " . $this->db->table('task_steps') . " WHERE step_id = '" . $step_id . "'";
        $this->db->query($sql);
    }

    /**
     * @param int $task_id
     *
     * @return array
     * @throws AException
     */
    public function getTaskById(int $task_id)
    {
        if (!$task_id) {
            return [];
        }
        $sql = "SELECT *
                FROM " . $this->db->table('tasks') . " t
                LEFT JOIN " . $this->db->table('task_details') . " td 
                    ON td.task_id = t.task_id
                WHERE t.task_id = '" . $task_id . "'";
        $result = $this->db->query($sql);
        $output = $result->row;
        if ($output) {
            $output['steps'] = $this->getTaskSteps((int)$output['task_id']);
        }

        if ($output['settings']) {
            $output['settings'] = unserialize($output['settings']);
        }

        return $output;
    }

    /**
     * @param string $task_name
     *
     * @return array
     * @throws AException
     */
    public function getTaskByName(string $task_name)
    {
        $task_name = $this->db->escape($task_name);
        if (!$task_name) {
            return [];
        }

        $sql = "SELECT *
                FROM " . $this->db->table('tasks') . " t
                LEFT JOIN " . $this->db->table('task_details') . " td 
                    ON td.task_id = t.task_id
                WHERE t.name = '" . $task_name . "'";
        $result = $this->db->query($sql);
        $output = $result->row;
        if ($output) {
            $output['steps'] = $this->getReadyTaskSteps((int)$output['task_id']);
        }

        if ($output['settings']) {
            $output['settings'] = unserialize($output['settings']);
        }

        return $output;
    }

    /**
     * @param int $task_id
     *
     * @return array
     */
    public function getTaskSteps(int $task_id)
    {
        if (!$task_id) {
            return [];
        }
        $output = [];
        try {
            $sql = "SELECT *
                    FROM " . $this->db->table('task_steps') . "
                    WHERE task_id = " . $task_id . "
                    ORDER BY sort_order";
            $result = $this->db->query($sql);
            $memory_limit = getMemoryLimitInBytes();
            //if limitation not set  - think as unlimited
            $memory_limit = $memory_limit == -1 ? 100000000000000000000000 : $memory_limit;
            foreach ($result->rows as $row) {
                $used = memory_get_usage();
                if ($memory_limit - $used <= 204800) {
                    $this->logger->write(
                        'Error: Task Manager Memory overflow! '
                        . 'To Get all Steps of Task you should to increase memory_limit_size in your php.ini' . PHP_EOL
                        . ' Memory_limit: ' . $memory_limit . ', memory_used: ' . $used
                    );
                }
                $row['settings'] = $row['settings'] ? unserialize($row['settings']) : '';
                $output[(string)$row['step_id']] = $row;
            }
        } catch (Exception $e) {
            $this->logger->write(
                'Error: Task Manager Memory overflow! '
                . 'To Get all Steps of Task you should to increase memory_limit_size in your php.ini'
                . PHP_EOL
                . $e->getMessage()
            );
        }
        return $output;
    }

    /**
     * @param int $task_id
     * @param int $step_id
     *
     * @return array
     * @throws AException
     */
    public function getTaskStep(int $task_id, int $step_id)
    {
        if (!$task_id || !$step_id) {
            return [];
        }

        $sql = "SELECT *
                FROM " . $this->db->table('task_steps') . "
                WHERE task_id = " . $task_id . " AND step_id = " . $step_id;
        $result = $this->db->query($sql);
        $output = $result->row;
        if ($output) {
            $output['settings'] = $output['settings'] ? unserialize($output['settings']) : '';
        }
        return $output;
    }

    /**
     * @param int $task_id
     *
     * @return array
     */
    public function getReadyTaskSteps(int $task_id)
    {
        if (!$task_id) {
            return [];
        }

        $all_steps = $this->getTaskSteps($task_id);
        $steps = [];
        foreach ($all_steps as $step) {
            //skip all steps that not scheduled
            if ($step['status'] != self::STATUS_READY) {
                continue;
            }
            $steps[$step['step_id']] = $step;
        }
        return $steps;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getTotalTasks(array $data = [])
    {
        $sql = "SELECT COUNT(*) as total
                FROM " . $this->db->table('tasks');
        $sql .= ' WHERE 1=1 ';

        if (!empty($data['subsql_filter'])) {
            $sql .= " AND " . $data['subsql_filter'];
        }

        if (has_value($data['filter']['name'])) {
            $sql .= " AND (LCASE(t.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter']['name'])) . "%'";
        }

        $result = $this->db->query($sql);
        return $result->row['total'];
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    public function getTasks(array $data = [])
    {

        $sql = "SELECT td.*, t.*
                FROM " . $this->db->table('tasks') . " t
                LEFT JOIN " . $this->db->table('task_details') . " td 
                    ON td.task_id = t.task_id
                WHERE 1=1 ";

        if (!empty($data['subsql_filter'])) {
            $sql .= " AND " . $data['subsql_filter'];
        }

        if (has_value($data['filter']['name'])) {
            $sql .= " AND (LCASE(t.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter']['name'])) . "%')";
        }

        $sort_data = [
            'name'          => 't.name',
            'status'        => 't.status',
            'start_time'    => 't.start_time',
            'date_modified' => 't.date_modified',
        ];

        if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $sort_data[$data['sort']];
        } else {
            $sql .= " ORDER BY t.date_modified";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $result = $this->db->query($sql);
        $output = $result->rows;
        foreach ($output as &$row) {
            if ($row['settings']) {
                $row['settings'] = unserialize($row['settings']);
            }

            //check is task stuck
            if ($row['status'] == self::STATUS_RUNNING
                && $row['max_execution_time'] > 0
                && (time() - dateISO2Int($row['last_time_run'])) > $row['max_execution_time']
            ) {
                //mark task as stuck
                $row['status'] = -1;
            }
        }
        unset($row);

        return $output;
    }

    /**
     * @return array
     */
    public function getRunLog()
    {
        return $this->run_log;
    }
}