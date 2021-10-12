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

class ModelToolBackup extends Model
{
    public $errors = [];
    public $backup_filename;
    private $eta = [];
    /**
     * @var int raw size of backup directory/ needed for calculation of eta of compression
     */
    private $est_backup_size = 0;

    /**
     * @param string $sql
     *
     * @throws AException
     */
    public function restore($sql)
    {
        $this->db->query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'"); // to prevent auto increment for 0 value of id
        $qr = explode(";\n", $sql);
        foreach ($qr as $sql) {
            $sql = trim($sql);
            if ($sql) {
                $this->db->query($sql);
            }
        }
        $this->db->query("SET SQL_MODE = ''");
    }

    /**
     * @param string $xml_source - xml as string or full filename to xml-file
     * @param string $mode
     *
     * @return bool
     * @throws AException
     */
    public function load($xml_source, $mode = 'string')
    {
        $xml_obj = null;
        if ($mode == 'string') {
            $xml_obj = simplexml_load_string($xml_source);
        } elseif ($mode == 'file') {
            $xml_obj = simplexml_load_file($xml_source);
        }
        if ($xml_obj) {
            $xmlname = $xml_obj->getName();
            if ($xmlname == 'template_layouts') {
                $load = new ALayoutManager();
                $load->loadXML(['xml' => $xml_source]);
            } elseif ($xmlname == 'datasets') {
                $load = new ADataset();
                $load->loadXML(['xml' => $xml_source]);
            } elseif ($xmlname == 'forms') {
                $load = new AFormManager();
                $load->loadXML(['xml' => $xml_source]);
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * function returns table list of abantecart
     *
     * @return array|bool
     * @throws AException
     */
    public function getTables()
    {
        $table_data = [];
        $prefix_len = strlen(DB_PREFIX);

        $query = $this->db->query("SHOW TABLES FROM `".DB_DATABASE."`", true);
        if (!$query) {
            $sql = "SELECT TABLE_NAME
					FROM information_schema.TABLES
					WHERE information_schema.TABLES.table_schema = '".DB_DATABASE."' ";
            $query = $this->db->query($sql, true);
        }

        if (!$query) {
            return false;
        }

        foreach ($query->rows as $result) {
            $table_name = $result['Tables_in_'.DB_DATABASE];
            //if database prefix present - select only abantecart tables. If not - select all
            if (DB_PREFIX && substr($table_name, 0, $prefix_len) != DB_PREFIX) {
                continue;
            }
            $table_data[] = $result['Tables_in_'.DB_DATABASE];
        }
        return $table_data;
    }

    /**
     * @param array $tables
     * @param bool|true $rl
     * @param bool|false $config
     * @param string $sql_dump_mode
     *
     * @return bool
     * @throws AException
     */
    public function backup($tables, $rl = true, $config = false, $sql_dump_mode = 'data_only')
    {
        $bkp = new ABackup('manual_backup'.'_'.date('Y-m-d-H-i-s'));

        if ($bkp->error) {
            return false;
        }

        // do sql dump
        if (!in_array($sql_dump_mode, ['data_only', 'recreate'])) {
            $sql_dump_mode = 'data_only';
        }
        $bkp->sql_dump_mode = $sql_dump_mode;
        $bkp->dumpTables($tables);

        if ($rl) {
            $bkp->backupDirectory(DIR_RESOURCE, false);
        }
        if ($config) {
            $bkp->backupFile(DIR_ROOT.'/system/config.php', false);
        }
        $result = $bkp->archive(DIR_BACKUP.$bkp->getBackupName().'.tar.gz', DIR_BACKUP, $bkp->getBackupName());
        if (!$result) {
            $this->errors = array_merge($this->errors, $bkp->error);
        } else {
            $this->backup_filename = $bkp->getBackupName();
        }

        return $result;
    }

    /**
     * @param string $task_name
     * @param array $data
     *
     * @return array|bool
     * @throws AException
     */
    public function createBackupTask($task_name, $data = [])
    {
        if (!$task_name) {
            $this->errors[] = 'Can not to create task. Empty task name given';
        }

        //NOTE: remove temp backup dir before process to prevent progressive increment of directory date if some backup-steps will be failed
        $bkp = new ABackup("manual_backup_".date('Ymd_His'));
        $bkp->removeBackupDirectory();
        unset($bkp);

        $tm = new ATaskManager();

        //1. create new task
        $task_id = $tm->addTask(
            [
                'name'               => $task_name,
                //admin-side is starter
                'starter'            => 1,
                'created_by'         => $this->user->getId(),
                // schedule it!
                'status'             => 1,
                'start_time'         => date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), (int) date('d') + 1, date('Y'))),
                'last_time_run'      => '0000-00-00 00:00:00',
                'progress'           => '0',
                'last_result'        => '0',
                'run_interval'       => '0',
                'max_execution_time' => '0',
            ]
        );
        if (!$task_id) {
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }

        $backup_filename = "manual_backup_".date('Ymd_His');

        //create step for table backup
        if ($data['table_list']) {
            //calculate estimate time for dumping of tables
            // get sizes of tables
            $table_list = [];
            foreach ($data['table_list'] as $table) {
                if (!is_string($table)) {
                    continue;
                } // clean
                $table_list[] = $this->db->escape($table);
            }
            $sql = "SELECT SUM(data_length/8 + index_length/8 - data_free/8) AS 'db_size'
                    FROM information_schema.TABLES
                    WHERE information_schema.TABLES.table_schema = '".DB_DATABASE."'
                        AND TABLE_NAME IN ('".implode("','", $table_list)."')";

            $result = $this->db->query($sql);
            $db_size = $result->row['db_size'] * 8; //size in bytes

            // get eta in seconds. 2794843 - "bytes per seconds" of dumping for Pentium(R) Dual-Core CPU E5200 @ 2.50GHz × 2
            $eta = ceil($db_size / 2794843) * 4;
            $max_eta = ini_get('max_execution_time');
            $eta = $eta < $max_eta ? $max_eta : $eta;

            $step_id = $tm->addStep(
                [
                    'task_id'            => $task_id,
                    'sort_order'         => 1,
                    'status'             => 1,
                    'last_time_run'      => '0000-00-00 00:00:00',
                    'last_result'        => '0',
                    'max_execution_time' => $eta,
                    'controller'         => 'task/tool/backup/dumptables',
                    'settings'           => [
                        'table_list'    => $data['table_list'],
                        'sql_dump_mode' => $data['sql_dump_mode'],
                        'backup_name'   => $backup_filename,
                    ],
                ]
            );

            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                $this->eta[$step_id] = $eta;
                $this->est_backup_size += ceil($db_size * 1.61); // size of sql-file of output
            }
        }

        //create step for content-files backup
        if ($data['backup_code']) {
            //calculate estimate time for copying of code

            $dirs_size = $this->getCodeSize();

            //// get eta in seconds. 28468838 - "bytes per seconds" of coping of files for SATA III hdd
            $eta = ceil($dirs_size / 28468838);
            $max_eta = ini_get('max_execution_time');
            $eta = $eta < $max_eta ? $max_eta : $eta;

            $step_id = $tm->addStep(
                [
                    'task_id'            => $task_id,
                    'sort_order'         => 2,
                    'status'             => 1,
                    'last_time_run'      => '0000-00-00 00:00:00',
                    'last_result'        => '0',
                    'max_execution_time' => $eta,
                    'controller'         => 'task/tool/backup/backupCodeFiles',
                    'settings'           => [
                        'interrupt_on_step_fault' => false,
                        'backup_name'             => $backup_filename,
                    ],
                ]
            );

            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                $this->eta[$step_id] = $eta;
                $this->est_backup_size += $dirs_size;
            }
        }
        //create step for content-files backup
        if ($data['backup_content']) {
            //calculate estimate time for copying of content files
            $dirs_size = $this->getContentSize();
            //// get eta in seconds. 28468838 - "bytes per seconds" of coping of files for SATA III hdd
            $eta = ceil($dirs_size / 28468838);
            $max_eta = ini_get('max_execution_time');
            $eta = $eta < $max_eta ? $max_eta : $eta;

            $step_id = $tm->addStep(
                [
                    'task_id'            => $task_id,
                    'sort_order'         => 3,
                    'status'             => 1,
                    'last_time_run'      => '0000-00-00 00:00:00',
                    'last_result'        => '0',
                    'max_execution_time' => $eta,
                    'controller'         => 'task/tool/backup/backupContentFiles',
                    'settings'           => [
                        'interrupt_on_step_fault' => false,
                        'backup_name'             => $backup_filename,
                    ],
                ]
            );

            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                $this->eta[$step_id] = $eta;
                $this->est_backup_size += $dirs_size;
            }
        }

        //create last step for compressing backup
        if ($data['compress_backup']) {
            //// get eta in seconds. 18874368 - "bytes per seconds" of gz-compression, level 1 on
            // AMD mobile Athlon XP2400+ 512 MB RAM Linux 2.6.12-rc4 gzip 1.3.3
            $eta = ceil($this->est_backup_size / 18874368);
            $max_eta = ini_get('max_execution_time');
            $eta = $eta < $max_eta ? $max_eta : $eta;

            $step_id = $tm->addStep(
                [
                    'task_id'            => $task_id,
                    'sort_order'         => 4,
                    'status'             => 1,
                    'last_time_run'      => '0000-00-00 00:00:00',
                    'last_result'        => '0',
                    'max_execution_time' => $eta,
                    'controller'         => 'task/tool/backup/compressbackup',
                    'settings'           => [
                        'interrupt_on_step_fault' => false,
                        'backup_name'             => $backup_filename,
                    ],
                ]
            );
            if (!$step_id) {
                $this->errors = array_merge($this->errors, $tm->errors);
                return false;
            } else {
                $this->eta[$step_id] = $eta;
            }
        }

        $task_details = $tm->getTaskById($task_id);
        if ($task_details) {
            foreach ($this->eta as $step_id => $eta) {
                $task_details['steps'][$step_id]['eta'] = $eta;
            }
            return $task_details;
        } else {
            $this->errors[] = 'Can not to get task details for execution';
            $this->errors = array_merge($this->errors, $tm->errors);
            return false;
        }
    }

    /**
     * @param array $table_list
     *
     * @return array
     * @throws AException
     */
    public function getTableSizes($table_list = [])
    {
        $tables = [];
        foreach ($table_list as $table) {
            if (!is_string($table)) {
                continue;
            } // clean
            $tables[] = $this->db->escape($table);
        }

        $sql = "SELECT TABLE_NAME AS 'table_name',
                    table_rows AS 'num_rows', (data_length + index_length - data_free) AS 'size'
                FROM information_schema.TABLES
                WHERE information_schema.TABLES.table_schema = '".DB_DATABASE."'
                    AND TABLE_NAME IN ('".implode("','", $tables)."')	";
        $result = $this->db->query($sql);
        $output = [];
        foreach ($result->rows as $row) {
            if ($row['size'] > 1048576) {
                $text = round(($row['size'] / 1048576), 1).'Mb';
            } else {
                $text = round($row['size'] / 1024, 1).'Kb';
            }

            $output[$row['table_name']] = [
                'bytes' => $row['size'],
                'text'  => $text,
            ];
        }

        return $output;
    }

    /**
     * @return int
     */
    public function getCodeSize()
    {
        $all_dirs = scandir(DIR_ROOT);
        $content_dirs = [
            // black list
            '.',
            '..',
            'resources',
            'image',
            'download',
        ];
        $dirs_size = 0;
        foreach ($all_dirs as $d) {
            //skip content directories
            if (in_array($d, $content_dirs)) {
                continue;
            }
            $item = DIR_ROOT.'/'.$d;
            if (is_dir($item)) {
                $dirs_size += $this->_get_directory_size($item);
            } elseif (is_file($item)) {
                $dirs_size += filesize($item);
            }
        }
        return $dirs_size;
    }

    /**
     * @return int
     */
    public function getContentSize()
    {
        $content_dirs = [
            // white list
            'resources',
            'image',
            'download',
        ];
        $dirs_size = 0;
        foreach ($content_dirs as $d) {
            $dirs_size += $this->_get_directory_size(DIR_ROOT.'/'.$d);
        }
        return $dirs_size;
    }

    /**
     * @param string $dir
     *
     * @return int
     */
    private function _get_directory_size($dir)
    {
        $count_size = 0;
        $count = 0;
        $dir_array = scandir($dir);
        foreach ($dir_array as $filename) {
            //skip backup, cache and logs
            if (is_int(strpos($dir."/".$filename, '/backup'))
                || is_int(strpos($dir."/".$filename, '/cache'))
                || is_int(strpos($dir."/".$filename, '/logs'))
            ) {
                continue;
            }

            if ($filename != ".." && $filename != ".") {
                if (is_dir($dir."/".$filename)) {
                    $new_dir_size = $this->_get_directory_size($dir."/".$filename);
                    $count_size = $count_size + $new_dir_size;
                } else {
                    if (is_file($dir."/".$filename)) {
                        $count_size = $count_size + filesize($dir."/".$filename);
                        $count++;
                    }
                }
            }
        }
        return $count_size;
    }

}
