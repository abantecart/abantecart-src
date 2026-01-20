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
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

use phpunit\AbanteCartTestBootstrap;

/**
 * Class SystemCheckTest
 *
 * @property ALoader             $load
 * @property ModelToolBackup     $model_tool_backup
 * @property ModelSettingSetting $model_setting_setting
 */
class taskTest extends AbanteCartTestBootstrap
{
    protected $task_details;

    protected function setUp(): void
    {
        parent::setUp();
        parent::bootstrap();

        $this->load->model('tool/backup');
        $this->task_details = $this->model_tool_backup->createBackupTask(
            'manual_backup',
            [
                'compress_backup' => 1,
                'table_list'      =>
                    [
                        'cba_customers',
                        'cba_addresses',
                    ],
            ]
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('api', ['task_api_key', '12345']);

    }

    protected function tearDown(): void
    {
        $tm = new ATaskManager();
        $tm->deleteTask((int)$this->task_details['task_id']);
        parent::tearDown();
    }

    public function testTaskRunCLI():void
    {
        $task_id = $this->task_details['task_id'];
        exec('php '.DIR_ROOT.'/task_cli.php run --task_id='.$task_id, $output);
        list($step_id1, $step_id2) = array_keys($this->task_details['steps']);
        $expected = [
            "Running: Task ID ".$task_id.":",
            "Finished running: Task ID ".$task_id.":",
        ];

        $this->assertEquals($expected, $output);
    }

    public function testTaskStepRunCLI():void
    {
        $task_id = $this->task_details['task_id'];
        $step_id1 = array_key_first($this->task_details['steps']);
        exec('php '.DIR_ROOT.'/task_cli.php run --task_id='.$task_id.' --step_id='.$step_id1, $output);

        $expected = [
            "Force starting step!",
            "Running: Task ID ".$task_id." Step ID ".$step_id1.":",
            "Finished running: Task ID ".$task_id." Step ID ".$step_id1.":",
        ];

        $this->assertEquals($expected, $output);
    }

}