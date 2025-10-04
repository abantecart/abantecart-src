<?php /*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
/** @noinspection PhpMultipleClassDeclarationsInspection */
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ModelLocalisationLanguage extends Model
{
    public $errors = [];

    /**
     * @param $data
     *
     * @return int
     * @throws AException
     */
    public function addLanguage($data)
    {
        $this->db->query(
            "INSERT INTO " . $this->db->table("languages") . " 
            SET name = '" . $this->db->escape($data['name']) . "',
                code = '" . $this->db->escape($data['code']) . "',
                locale = '" . $this->db->escape($data['locale']) . "',
                directory = '" . $this->db->escape($data['directory']) . "',
                filename = '" . $this->db->escape($data['directory']) . "',
                sort_order = '" . (int)$data['sort_order'] . "',
                status = '" . (int)$data['status'] . "'"
        );

        $this->cache->remove('localization');
        $language_id = (int)$this->db->getLastId();

        //add menu items for a new language
        $menu = new AMenu_Storefront();
        $menu->addLanguage($language_id);

        //language data is copied/translated in a separate process.
        return $language_id;
    }

    /**
     * @param int $language_id
     * @param array $data
     * @throws AException
     */
    public function editLanguage($language_id, $data)
    {
        $update_data = [];
        foreach ($data as $key => $val) {
            $update_data[] = "`$key` = '" . $this->db->escape($val) . "' ";
        }
        if ($update_data) {
            $this->db->query(
                "UPDATE " . $this->db->table("languages") . " 
                SET " . implode(',', $update_data) . " 
                WHERE language_id = '" . (int)$language_id . "'"
            );
        }

        $this->cache->remove('localization');
    }

    /**
     * @param int $language_id
     *
     * @throws AException
     */
    public function deleteLanguage($language_id)
    {
        $this->db->query(
            "DELETE FROM " . $this->db->table("languages") . " 
            WHERE language_id = '" . (int)$language_id . "'"
        );
        $this->language->deleteAllLanguageEntries((int)$language_id);
        //too many changes and better clear all cache
        $this->cache->remove('*');

        //delete menu items for a given language
        $menu = new AMenu_Storefront();
        $menu->deleteLanguage((int)$language_id);
    }

    /**
     * @param int $language_id
     *
     * @return array
     * @throws AException
     */
    public function getLanguage($language_id)
    {
        $query = $this->db->query(
            "SELECT DISTINCT * 
           FROM " . $this->db->table("languages") . " 
           WHERE language_id = '" . (int)$language_id . "'"
        );
        $result = $query->row;
        if (!$result['image']) {
            if (file_exists(DIR_ROOT . DS . 'admin' . DS . 'language' . DS . $result['directory'] . DS . 'flag.png')) {
                $result['image'] = AUTO_SERVER . 'admin' . DS . 'language' . DS . $result['directory'] . DS . 'flag.png';
            }
        } else {
            $result['image'] = AUTO_SERVER . $result['image'];
        }
        return $result;
    }

    /**
     * @param array $data
     * @param string $mode
     *
     * @return int|array
     * @throws AException
     */
    public function getLanguages($data = [], $mode = 'default')
    {
        $cacheKey = 'localization.language.admin' . md5(json_encode(func_get_args()));
        $cache = $this->cache->pull($cacheKey);
        if ($cache !== false) {
            return $cache;
        }

        $filter = $data['filter'] ?? [];
        if ($mode == 'total_only') {
            $sql = "SELECT count(*) as total FROM " . $this->db->table("languages") . " ";
        } else {
            $sql = "SELECT " . $this->db->getSqlCalcTotalRows() . " * FROM " . $this->db->table("languages") . " ";
        }

        $sql .= " WHERE 1=1";
        if (isset($filter['status'])) {
            $sql .= " AND `status` = '" . (int)$filter['status'] . "' ";
        }

        if (isset($filter['name'])) {
            $sql .= " AND `name` LIKE '%" . $this->db->escape($filter['name'], true) . "%' ";
        }

        if ($data['subsql_filter']) {
            $sql .= " AND " . $data['subsql_filter'];
        }

        //If for total, we're done building the query
        if ($mode == 'total_only') {
            $query = $this->db->query($sql);
            return (int)$query->row['total'];
        }

        $sort_data = [
            'name',
            'code',
            'sort_order',
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order, name";
        }

        if (isset($data['order']) && (strtoupper($data['order']) == 'DESC')) {
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

        $query = $this->db->query($sql);
        $result = $query->rows;
        $totalRows = $this->db->getTotalNumRows();
        $language_data = [];
        foreach ($result as $row) {
            $row['total_num_rows'] = $totalRows;
            if (!$row['image']) {
                if (file_exists(DIR_ROOT . DS . 'admin' . DS . 'language' . DS . $row['directory'] . DS . 'flag.png')) {
                    $row['image'] = 'admin/language/' . $row['directory'] . '/flag.png';
                }
            }
            $language_data[$row['code']] = $row;
        }
        $this->cache->push($cacheKey, $language_data);
        return $language_data;

    }

    /**
     * @param string $task_name
     * @param array $data
     *
     * @return array|bool
     * @throws AException
     */
    public function createTask($task_name, $data = [])
    {

        if (!$task_name) {
            $this->errors[] = 'Can not to create task. Empty task name has been given.';
        }

        //get URIs of recipients
        $tables = $this->getTablesInfo((int)$data['source_language']);
        $task_controller = 'task/localisation/language/translate';

        if (!$tables) {
            $this->errors[] = 'No tables info!';
            return false;
        }

        $total_desc_count = 0;
        foreach ($tables as $table) {
            $total_desc_count += $table['description_count'];
        }

        //numbers of translations per task step
        $divider = 30;
        //timeout in seconds for one item translation
        $time_per_item = 4;
        $tm = new ATaskManager();

        //create a new task
        $task_id = $tm->addTask(
            [
                'name'               => $task_name,
                'starter'            => 1, //admin-side is a starter
                'created_by'         => $this->user->getId(), //get starter id
                'status'             => $tm::STATUS_READY,
                'start_time'         => date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), (int)date('d') + 1, date('Y'))),
                'last_time_run'      => null,
                'progress'           => '0',
                'last_result'        => '1', // think all fine until some failed step sets 0 here
                'run_interval'       => '0',
                //think that task will execute with some connection errors
                'max_execution_time' => ($total_desc_count * $time_per_item * 2),
            ]
        );
        if (!$task_id) {
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }

        $tm->updateTaskDetails($task_id,
            [
                'created_by' => $this->user->getId(),
                'settings'   => [
                    'descriptions_count' => $total_desc_count,
                ],
            ]
        );

        //create steps
        $sort_order = 1;
        $eta = [];
        foreach ($tables as $table_name => $info) {
            if (!$info['primary_keys']) {
                continue;
            }
            $settings = [];
            //get all indexes of descriptions of the table
            $sql = "SELECT " . implode(', ', $info['primary_keys']) . "
                    FROM " . $table_name . "
                    WHERE language_id = " . $data['source_language'];
            $result = $this->db->query($sql);

            if ($divider >= $info['description_count']) {
                $items = [];
                foreach ($result->rows as $row) {
                    foreach ($row as $k => $v) {
                        $items[$k][] = $v;
                    }
                }

                $settings[0] = [
                    'src_language_id'  => (int)$data['source_language'],
                    'language_id'      => (int)$data['language_id'],
                    'translate_method' => $data['translate_method'],
                    'table'            =>
                        [
                            'table_name'  => $table_name,
                            'items_count' => $info['description_count'],
                            'indexes'     => $items,
                        ],
                ];
            } else {
                $slices = array_chunk($result->rows, $divider);
                foreach ($slices as $slice) {
                    $items = [];
                    foreach ($slice as $row) {
                        foreach ($row as $k => $v) {
                            $items[$k][] = $v;
                        }
                    }
                    $settings[] = [
                        'src_language_id'  => (int)$data['source_language'],
                        'language_id'      => (int)$data['language_id'],
                        'translate_method' => $data['translate_method'],
                        'table'            =>
                            [
                                'table_name'  => $table_name,
                                'items_count' => sizeof($slice),
                                'indexes'     => $items,
                            ],
                    ];
                }
            }

            foreach ($settings as $s) {
                $step_id = $tm->addStep(
                    [
                        'task_id'            => $task_id,
                        'sort_order'         => $sort_order,
                        'status'             => 1,
                        'last_time_run'      => null,
                        'last_result'        => '0',
                        //think that task will execute with some connection errors
                        'max_execution_time' => ($time_per_item * $divider * 2),
                        'controller'         => $task_controller,
                        'settings'           => $s,
                    ]
                );
                $eta[$step_id] = $time_per_item * $divider * 2;
                $sort_order++;
            }
        }

        $task_details = $tm->getTaskById($task_id);

        if ($task_details) {
            foreach ($eta as $step_id => $estimate) {
                $task_details['steps'][$step_id]['eta'] = $estimate;
                //remove settings from an output JSON array. We will take it from a database on execution.
                unset($task_details['steps'][$step_id]['settings']);
            }
            return $task_details;
        } else {
            $this->errors[] = 'Can not to get task details for execution';
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }
    }

    /**
     * @param int $srcLanguageId
     * @return array|false
     * @throws AException
     */
    protected function getTablesInfo(int $srcLanguageId = 0)
    {
        if (!$srcLanguageId) {
            return false;
        }

        $dbTableList = $this->language->getLanguageBasedTables(true);
        if (!$dbTableList) {
            return false;
        }

        $output = [];

        $excludes = [
            $this->db->table('languages'),
            $this->db->table('language_definitions'),
            $this->db->table('orders'),
            $this->db->table('fields_history'),
            $this->db->table('order_data_types')
        ];
        foreach ($dbTableList as $dbTableName => $rowCount) {
            if (in_array($dbTableName, $excludes) || !$dbTableName) {
                continue;
            }

            $pKeys = $this->language->getPrimaryKeys($dbTableName);
            $lpk = array_search('language_id', $pKeys);
            if (is_int($lpk)) {
                unset($pKeys[$lpk]);
            }
            $cols2Translate = $this->language->getTranslatableFields($dbTableName);
            $output[$dbTableName] = [
                'primary_keys'      => $pKeys,
                'fields'            => $cols2Translate,
                'row_count'         => $rowCount,
                'description_count' => (int)$rowCount * count($cols2Translate)
            ];
        }
        return $output;
    }
}