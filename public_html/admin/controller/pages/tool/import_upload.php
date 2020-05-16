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

class ControllerPagesToolImportUpload extends AController
{
    /**
     * @var array()
     */
    public $data = array();
    public $errors = array();
    /**
     * @var array()
     */
    public $file_types = array('text/csv', 'application/vnd.ms-excel', 'text/plain', 'application/octet-stream');
    /**
     * @var AData
     */
    private $handler;

    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('tool/import_export');
        $redirect = $this->html->getSecureURL('tool/import_export', '&active=import');

        if (!$this->request->is_POST() || !$this->user->canModify('tool/import_export')) {
            redirect($redirect);
            return $this->dispatch('error/permission');
        }

        if (empty($this->request->files)) {
            $this->session->data['error'] = 'File data for export is empty!';
            redirect($redirect);
        }

        if (!$this->validateRequest()) {
            redirect($redirect);
        }

        //All good so far, prepare import
        $this->handler = new AData();
        $file_data = $this->_prepare_import();
        if ($file_data['error']) {
            $this->session->data['error'] = $file_data['error'];
            redirect($redirect);
        }

        $this->session->data['import'] = $file_data;
        unset($this->session->data['import_map']);

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        //internal import format
        if ($file_data['format'] == 'internal') {
            redirect($this->html->getSecureURL('tool/import_export/internal_import'));
        } else {
            redirect($this->html->getSecureURL('tool/import_export/import_wizard'));
        }
    }

    protected function _prepare_import()
    {
        $file = $this->request->files['imported_file'];
        $post = $this->request->post;

        $res = array();
        $res['run_mode'] = isset($post['test_mode']) ? $post['test_mode'] : 'commit';
        $res['delimiter_id'] = $post['options']['delimiter'];
        $res['delimiter'] = $this->handler->csvDelimiters[$res['delimiter_id']];

        if (in_array($file['type'], array('text/csv', 'application/vnd.ms-excel', 'text/plain', 'application/octet-stream'))) {
            #NOTE: 'application/octet-stream' is a solution for Windows OS sending unknown file type
            $res['file_type'] = 'csv';
        } else {
            return array('error' => $this->language->get('error_file_format'));
        }

        //move uploaded file to tmp processing location
        $res['file'] = DIR_DATA.'import_'.basename($file['tmp_name']).".txt";
        $result = move_uploaded_file($file['tmp_name'], $res['file']);
        if ($result === false) {
            //remove trunk
            unlink($file['tmp_name']);
            $error_text = 'Error! Unable to move uploaded file to '.$res['file'];
            return array('error' => $error_text);
        }

        //detect file format
        if ($res['file_type'] == 'csv') {
            ini_set('auto_detect_line_endings', true);
            if ($fh = fopen($res['file'], 'r')) {
                $cols = fgetcsv($fh, 0, $res['delimiter']);
                if (count($cols) < 2) {
                    return array('error' => $this->language->get('error_csv_import'));
                }
                //do we have internal format or some other
                $res['format'] = 'other';
                $count_dots = 0;
                foreach ($cols as $key) {
                    if (strpos($key, ".") !== false) {
                        $count_dots++;
                    }
                }

                //try to detect file format basing on column names
                $cols_count = count($cols);
                $exclude_col_names = array('action');
                foreach ($exclude_col_names as $exclude_col_name) {
                    if (in_array($exclude_col_name, $cols)) {
                        $cols_count--;
                    }
                }
                if ($count_dots == $cols_count) {
                    $res['format'] = 'internal';
                    list($res['table'],) = explode('.', $cols[0]);
                }
            } else {
                return array('error' => $this->language->get('error_data_corrupted'));
            }
            $res['request_count'] = 0;
            while (fgetcsv($fh, 0, $res['delimiter']) !== false) {
                $res['request_count']++;
            }
            fclose($fh);
        }
        return $res;
    }

    protected function validateRequest()
    {
        $file = $this->request->files['imported_file'];
        $this->errors = array();
        if (!is_dir(DIR_DATA)) {
            mkdir(DIR_DATA, 0755, true);
        }
        if (!is_writable(DIR_DATA)) {
            $this->errors['error'] = sprintf($this->language->get('error_tmp_dir_non_writable'), DIR_DATA);
        } elseif (!in_array($file['type'], $this->file_types)) {
            $this->errors['error'] = $this->language->get('error_file_format');
        } elseif (file_exists($file['tmp_name']) && $file['size'] > 0) {

        } elseif (file_exists($file['tmp_name'])) {
            $this->errors['error'] = $this->language->get('error_file_empty');
        } elseif ($file['error'] != 0) {
            $this->errors['error'] = $this->language->get('error_upload_'.$file['error']);
        } else {
            $this->errors['error'] = $this->language->get('error_empty_request');
        }

        $this->extensions->hk_ValidateData($this, array(__FUNCTION__));
        if ($this->errors) {
            $this->session->data['error'] = $this->errors['error'];
            return false;
        } else {
            return true;
        }
    }

}