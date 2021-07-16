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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ControllerPagesToolImportExport
 *
 * @property ModelToolImportProcess $model_tool_import_process
 */
class ControllerPagesToolImportExport extends AController
{

    public $tabs = ['import', 'export'];
    public $error;
    public $success = '';
    public $data = [];
    /**
     * @var AData
     */
    private $handler;
    /**
     * @var array()
     */
    private $tables;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        if ($this->session->data['import']) {
            $this->tabs = array_merge(['import_wizard'], $this->tabs);
        }
        $this->handler = new AData();
        $this->loadModel('tool/import_process');
        $this->tables = $this->model_tool_import_process->importTableCols();
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        if ($this->request->get['active'] == 'import_wizard' && $this->session->data['import']) {
            redirect($this->html->getSecureURL('tool/import_export/import_wizard'));
        }

        $this->loadLanguage('tool/import_export');
        $this->document->setTitle($this->language->get('import_export_title'));

        $this->data['title'] = $this->language->get('import_export_title');
        $this->data['text_tables'] = $this->language->get('text_tables');
        $this->data['text_options'] = $this->language->get('text_options');

        $this->data['tabs'] = $this->tabs;

        if (isset($this->request->get['active']) && strpos($this->request->get['active'], '-') !== false) {
            $this->request->get['active'] =
                substr($this->request->get['active'], 0, strpos($this->request->get['active'], '-'));
        }
        $this->data['active'] =
            isset($this->request->get['active']) && in_array($this->request->get['active'], $this->data['tabs']) ?
                $this->request->get['active'] : 'import';

        if (!$this->data['active']) {
            $this->data['active'] = 'import';
        }

        foreach ($this->data['tabs'] as $tab) {
            $this->data['tab_'.$tab] = $this->language->get('tab_'.$tab);
            $this->data['link_'.$tab] = $this->html->getSecureURL('p/tool/import_export', '&active='.$tab);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL('tool/import_export'),
                'text'    => $this->language->get('import_export_title'),
                'current' => true,
            ]
        );

        $this->view->assign('help_url', $this->gen_help_url($this->data['active']));

        $this->getForm();

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $this->data['error_warning'] = $this->error;
        }
        $this->data['success'] = $this->success;

        $this->view->batchAssign($this->data);

        $this->processTemplate("pages/tool/{$this->data['active']}.tpl");

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function getForm()
    {
        $form = new AForm('ST');

        $form->setForm(
            [
                'form_name' => $this->data['active'].'Frm',
            ]
        );

        $this->data['form']['id'] = $this->data['active'].'Frm';

        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('tab_'.$this->data['active']),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );

        switch ($this->data['active']) {
            case 'import':
                $this->data['action'] = $this->html->getSecureURL('tool/import_upload');

                $this->data['text_load_file'] = $this->language->get('text_load_file');
                $this->data['text_file_field'] = $this->language->get('text_file_field');

                $fileForm = new AForm('ST');

                $fileForm->setForm(
                    [
                        'form_name' => 'file_import_form',
                    ]
                );

                $this->data['file_form_open'] = $fileForm->getFieldHtml(
                    [
                        'type'   => 'form',
                        'name'   => 'file_import_form',
                        'action' => $this->data['action'],
                        'attr'   => 'class="aform form-horizontal"',
                    ]
                );

                $this->data['file_field'] = $fileForm->getFieldHtml(
                    [
                        'type' => 'file',
                        'name' => 'imported_file',
                    ]
                );

                $this->data['file_submit'] = $fileForm->getFieldHtml(
                    [
                        'type'  => 'button',
                        'name'  => 'file_submit',
                        'text'  => $this->language->get('text_load'),
                        'style' => 'button1',
                    ]
                );

                $options['text']['delimiter'] = $this->language->get('text_csv_delimiter').'<br/>'.$this->language->get(
                        'text_export_note_csv_delimiter'
                    );
                $options['item']['delimiter'] = $fileForm->getFieldHtml(
                    [
                        'type'    => 'selectbox',
                        'name'    => 'options[delimiter]',
                        'value'   => 0,
                        'options' => [',', ';', 'TAB', '|'],
                    ]
                );

                $this->data['options'] = $options;

                break;

            case 'export':
                $sections = $this->handler->getSections();

                $this->data['action'] = $this->html->getSecureURL('p/tool/export_upload');

                $this->data['form']['fields'] = $this->_build_table_fields($form, (array) $sections);

                $this->data['text_range_from'] = $this->language->get('text_id_range_from');
                $this->data['text_to'] = $this->language->get('text_to');
                break;
        }

        $options = [];

        $options['text']['file_format'] = $this->language->get('text_file_format');
        $options['item']['file_format'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'options[file_format]',
                'value'   => 0,
                'options' => [
                    'csv' => '&nbsp;&nbsp;CSV&nbsp;&nbsp;',
                    'txt' => 'TXT (delimited)',
                    'xml' => '&nbsp;&nbsp;XML&nbsp;&nbsp;',
                ],
            ]
        );

        $options['text']['file_name'] = $this->language->get('text_file_name');
        $options['item']['file_name'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'options[file_name]',
                'value' => 'export_'.date('dmY_His'),
                'style' => 'large-field',
            ]
        );

        $options['text']['delimiter'] =
            $this->language->get('text_csv_delimiter').'<br/>'.$this->language->get('text_export_note_csv_delimiter');
        $options['item']['delimiter'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'options[delimiter]',
                'value'   => 0,
                'options' => [',', ';', 'TAB'],
            ]
        );

        $this->data['form']['options'] = $options;

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => $this->data['active'].'Frm',
                'action' => $this->data['action'],
                'attr'   => 'class="aform form-horizontal"',
            ]
        );
    }

    /**
     * @param AForm $form
     * @param array $data
     *
     * @return array
     * @throws AException
     */
    protected function _build_table_fields($form, $data)
    {
        $result = [];

        foreach ($data as $table_name => $val) {
            $result[$table_name]['main'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => 'section_'.$table_name,
                    'value' => $table_name,
                    'style' => 'section_input',
                ]
            );

            $columns_data = $this->handler->getTableColumns($table_name);
            $columns = [];
            foreach ($columns_data as $column) {
                $columns[$column['Field']] = $column['Field'];
            }

            $result[$table_name]['filter']['columns'] = $form->getFieldHtml(
                [
                    'type'    => 'selectbox',
                    'name'    => 'data['.$table_name.'][filter][columns]',
                    'value'   => 0,
                    'options' => $columns,
                ]
            );

            $name = 'data['.$table_name.']';

            if (isset($val['children'])) {
                $name .= '[tables]';
                $children = $this->_get_table_children($form, $val['children'], $name);
                $result[$table_name]['children'] = $children;
            }
        }

        $this->data['text_no_children'] = $this->language->get('text_no_children');
        ksort($result);

        return $result;
    }

    /**
     * @param AForm $form
     * @param array $data
     * @param string $name
     *
     * @return array
     * @throws AException
     */
    private function _get_table_children($form, $data, $name)
    {
        $children = [];
        foreach ($data as $key => $val) {
            $new_name = $name.'['.$key.']';
            $children[$key]['name'] = $new_name;
            $children[$key]['field'] = $form->getFieldHtml(
                [
                    'type'  => 'checkbox',
                    'name'  => $new_name,
                    'value' => $key,
                    'style' => '',
                ]
            );

            if (isset($val['children'])) {
                $new_name .= '[tables]';
                $children = array_merge($children, $this->_get_table_children($form, $val['children'], $new_name));
            }
        }
        return $children;
    }

    public function reset()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        unset($this->session->data['import']);
        redirect($this->html->getSecureURL('tool/import_export', '&active=import'));
    }

    public function import_wizard()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $import_data = $this->session->data['import'];
        if ($import_data['format'] == 'internal') {
            redirect($this->html->getSecureURL('tool/import_export/internal_import'));
        }

        if (empty($import_data)) {
            $this->session->data['error'] = $this->language->get('error_data_corrupted');
            $this->main();
            return;
        }
        $this->data['file_format'] = $this->request->get['file_format'] == 'internal' ? 'internal' : 'external';

        $this->handler = new AData();

        $this->data['map'] = $this->request->post ? : $this->session->data['import_map'];
        if ($this->request->post['serialized_map']) {
            $this->data['map'] = unserialize(base64_decode($this->request->post['serialized_map']));
        }
        if ($this->request->is_POST() && $this->validateWizardRequest($this->data['map'])) {
            //all good get count and confirm the import
            $this->session->data['import_map'] = $this->data['map'];
            //present mapping for export
            $this->data['serialized_map'] = $this->html->buildElement(
                [
                    'type'  => 'textarea',
                    'name'  => 'serialized_map',
                    'value' => base64_encode(serialize($this->data['map'])),
                    'attr'  => 'rows="20" cols="300" readonly',
                ]
            );
            $this->data['request_count'] = (int) $import_data['request_count'];
            $this->data['import_ready'] = true;

            //urls for creating and running task
            $this->data['form']['build_task_url'] = $this->html->getSecureURL('r/tool/import_process/buildTask');
            $this->data['form']['complete_task_url'] = $this->html->getSecureURL('r/tool/import_process/complete');
            $this->data['form']['abort_task_url'] = $this->html->getSecureURL('r/tool/import_process/abort');
            $this->data['back_url'] = $this->html->getSecureURL('tool/import_export/import_wizard');
            $this->data['form']['schedule_url'] = $this->html->getSecureURL('tool/import_export/schedule_import');
        } else {
            if ($this->data['map'] && $this->validateWizardRequest($this->data['map'])) {
                $this->data['import_ready'] = false;
            }
        }

        if (isset($this->session->data['success'])) {
            $this->success = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->loadLanguage('tool/import_export');
        $this->document->setTitle($this->language->get('import_wizard_title'));
        $this->data['title'] = $this->language->get('import_wizard_title');

        $this->data['tabs'] = $this->tabs;
        $this->data['active'] = 'import_wizard';
        foreach ($this->data['tabs'] as $tab) {
            $this->data['tab_'.$tab] = $this->language->get('tab_'.$tab);
            $this->data['link_'.$tab] = $this->html->getSecureURL('p/tool/import_export', '&active='.$tab);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL('tool/import_export'),
                'text'    => $this->language->get('import_export_title'),
                'current' => true,
            ]
        );

        $this->getForm();
        $form = new AForm('ST');

        $form->setForm(
            [
                'form_name' => 'importWizardFrm',
            ]
        );
        $this->data['form']['id'] = 'importWizardFrmFrm';

        $this->data['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'importWizardFrmFrm',
                'action' => $this->html->getSecureURL('tool/import_export/import_wizard'),
                'attr'   => 'class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );
        $this->data['form']['serialized_map'] = $form->getFieldHtml(
            [
                'type'  => 'textarea',
                'name'  => 'serialized_map',
                'value' => '',
                'attr'  => 'rows="20" cols="300"',
            ]
        );

        $this->data['reset_url'] = $this->html->getSecureURL('p/tool/import_export/reset');

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $this->data['error_warning'] = $this->error;
        }
        $this->data['success'] = $this->success;

        //get sample row
        $this->data['cols'] = $this->data['data'] = [];
        if ($import_data['file_type'] == 'csv') {
            ini_set('auto_detect_line_endings', true);
            if ($fh = fopen($import_data['file'], 'r')) {
                $this->data['cols'] = fgetcsv($fh, 0, $import_data['delimiter']);
                $this->data['data'] = fgetcsv($fh, 0, $import_data['delimiter']);
            }
        }

        $this->data['tables'] = $this->tables;
        $this->view->batchAssign($this->data);

        $this->view->assign('help_url', $this->gen_help_url($this->data['active']));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->processTemplate("pages/tool/import_wizard.tpl");

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function internal_import()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $import_data = $this->session->data['import'];
        if (empty($import_data)) {
            $this->session->data['error'] = $this->language->get('error_data_corrupted');
            $this->main();
            return;
        }

        $this->loadLanguage('tool/import_export');

        if (isset($this->session->data['success'])) {
            $this->success = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $this->document->setTitle($this->language->get('import_wizard_title'));
        $this->data['title'] = $this->language->get('import_wizard_title');

        $this->data['tabs'] = $this->tabs;
        $this->data['active'] = 'import_wizard';
        foreach ($this->data['tabs'] as $tab) {
            $this->data['tab_'.$tab] = $this->language->get('tab_'.$tab);
            $this->data['link_'.$tab] = $this->html->getSecureURL('p/tool/import_export', '&active='.$tab);
        }

        $this->document->initBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'    => $this->html->getSecureURL('tool/import_export'),
                'text'    => $this->language->get('import_export_title'),
                'current' => true,
            ]
        );

        $this->getForm();
        $form = new AForm('ST');

        $form->setForm(
            [
                'form_name' => 'internalImportFrm',
            ]
        );
        $this->data['form']['id'] = 'internalImportFrm';

        $this->data['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'internalImport',
                'action' => $this->html->getSecureURL('tool/import_export/schedule_import'),
                'attr'   => 'class="aform form-horizontal"',
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button1',
            ]
        );
        $this->data['form']['schedule'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'import_schedule',
                'text' => $this->language->get('button_schedule_import'),
            ]
        );
        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]
        );
        //urls for creating and running task
        $this->data['form']['build_task_url'] = $this->html->getSecureURL('r/tool/import_process/buildTask');
        $this->data['form']['complete_task_url'] = $this->html->getSecureURL('r/tool/import_process/complete');
        $this->data['form']['abort_task_url'] = $this->html->getSecureURL('r/tool/import_process/abort');
        $this->data['back_url'] = $this->html->getSecureURL('tool/import_export/import_wizard');
        $this->data['reset_url'] = $this->html->getSecureURL('p/tool/import_export/reset');

        if (isset($this->session->data['error'])) {
            $this->data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $this->data['error_warning'] = $this->error;
        }
        $this->data['success'] = $this->success;

        //get sample row
        $this->data['cols'] = $this->data['data'] = [];
        if ($import_data['file_type'] == 'csv') {
            ini_set('auto_detect_line_endings', true);
            if ($fh = fopen($import_data['file'], 'r')) {
                $this->data['cols'] = fgetcsv($fh, 0, $import_data['delimiter']);
                $this->data['data'] = fgetcsv($fh, 0, $import_data['delimiter']);
            }
        }

        $this->data['request_count'] = $import_data['request_count'];
        $this->view->batchAssign($this->data);

        $this->view->assign('help_url', $this->gen_help_url($this->data['active']));
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->processTemplate("pages/tool/internal_import.tpl");

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function schedule_import()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $file_format = $this->session->data['import']['format'];
        if ($file_format == 'internal') {
            $imp_data = $this->session->data['import'];
        } else {
            $imp_data = array_merge($this->session->data['import_map'], $this->session->data['import']);
        }

        if (!$imp_data) {
            redirect($this->html->getSecureURL('tool/import_export'));
        }

        $this->loadLanguage('tool/import_export');
        if ($this->_validate_task()) {
            $this->loadModel('tool/import_process');

            $imp_data['store_id'] = $this->session->data['current_store_id'];
            $imp_data['language_id'] = $this->language->getContentLanguageID();

            $task_details = $this->model_tool_import_process->createTask('import_wizard_'.date('Ymd-H:i:s'), $imp_data);
            if (!$task_details) {
                $this->session->data['error'] = implode('<br>', $this->model_tool_import_process->errors);
            } else {
                $this->session->data['success'] = sprintf(
                    $this->language->get('text_success_scheduled'),
                    $this->html->getSecureURL('tool/task')
                );
            }
            redirect(
                $this->html->getSecureURL(
                    'tool/import_export/'
                        .($file_format == 'internal' ? 'internal_import' : 'import_wizard')
                )
            );
        }
    }

    protected function _validate_task()
    {
        if (!$this->user->canModify('sale/contact')) {
            $this->error = $this->language->get('error_permission');
        }

        if (($this->session->data['import']['format'] != 'internal' && !$this->session->data['import_map'])
            || !$this->session->data['import']
        ) {
            $this->error = $this->language->get('error_data_corrupted');
        }

        $this->extensions->hk_ValidateData($this);
        return !$this->error;
    }

    /**
     * Import router method
     */
    public function import()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $import_data = $this->session->data['import'];
        if (empty($import_data)) {
            $this->session->data['error'] = $this->language->get('error_data_corrupted');
            $this->main();
            return;
        }

        if ($import_data['file_type'] != 'csv') {
            $this->session->data['error'] = $this->language->get('error_file_format');
            $this->main();
            return;
        }

        if (!$this->data['results']) {
            $this->session->data['error'] = $this->language->get('error_data_corrupted');
            $this->main();
        }
    }

    /**
     * @param array $post
     *
     * @return bool
     * @throws AException
     */
    protected function validateWizardRequest($post)
    {
        if (!$post['table']
            || !isset($this->tables[$post['table']])
            || empty($post[$post['table']."_fields"])
            || !is_array($post[$post['table']."_fields"])
        ) {
            $this->error = $this->language->get('error_table_selection');
            return false;
        }

        $cols = $this->tables[$post['table']]['columns'] ?? [];
        foreach ($cols as $id => $data) {
            if ($data['required'] && !in_array($id, $post[$post['table']."_fields"])) {
                $this->error = sprintf($this->language->get('error_required_selection'), $id);
                return false;
            }
        }
        return true;
    }
}