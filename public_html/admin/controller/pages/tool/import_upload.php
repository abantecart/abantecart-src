<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2017 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesToolImportUpload extends AController {
    /**
     * @var array()
     */
    public $data = array();
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
		$this->extensions->hk_InitData($this,__FUNCTION__);
        $this->loadLanguage('tool/import_export');
        $redirect = $this->html->getSecureURL('tool/import_export', '&active=import');

        if ( !$this->request->is_POST() || !$this->user->canModify('tool/import_export') ) {
            $this->redirect($redirect);
            return $this->dispatch('error/permission');
        }

        if ( empty($this->request->files) ) {
            $this->session->data['error'] = 'File data for export is empty!';
            $this->redirect($redirect);
        }

        if (!$this->validateRequest()) {
            $this->redirect($redirect);
        }

        //All good so far, prepare import
        $this->handler = new AData();
        $file_data = $this->prepare_import();
        if($file_data['error']) {
            $this->session->data['error'] = $file_data['error'];
            $this->redirect($redirect);
        }

        $this->session->data['import'] = $file_data;
        unset($this->session->data['import_map']);
        //internal import format, we can load tasks
        if( $file_data['format'] == 'internal') {
            $this->redirect($this->html->getSecureURL('tool/import_export/import'));
        } else {
            $this->redirect($this->html->getSecureURL('tool/import_export/import_wizard'));
        }
	}

    private function prepare_import(){
        $file = $this->request->files['imported_file'];
        $post = $this->request->post;

        $res = array();
        $res['run_mode'] = isset($post['test_mode']) ? $post['test_mode'] : 'commit';
        $res['delimiter_id'] = $post['options']['delimiter'];
        $res['delimiter'] = $this->handler->csvDelimiters[$post['options']['delimiter']];

        echo_array($file);

        if(in_array($file['type'], array('text/csv', 'application/vnd.ms-excel', 'text/plain', 'application/octet-stream'))){
            #NOTE: 'application/octet-stream' is a solution for Windows OS sending unknown file type
            $res['file_type'] = 'csv';
        } else{
            return array('error' => $this->language->get('error_file_format'));
        }

        //move uploaded file to tmp processing location
        $res['file'] = DIR_DATA . 'import_' . basename($file['tmp_name']) . ".txt";
        $result = move_uploaded_file($file['tmp_name'], $res['file']);
        if ($result === false){
            $error_text = 'Error! Unable to move uploaded file to ' . $res['file'];
            return array('error' => $error_text);
        }

        //detect file format
        if($res['file_type'] == 'csv') {
            ini_set('auto_detect_line_endings', true);
            if ( $fh = fopen($res['file'], 'r') ) {
                $cols = fgetcsv($fh, 0, $res['delimiter']);
                if(count($cols) < 2){
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
                if($count_dots == count($cols)){
                    $res['format'] = 'internal';
                }

            } else {
                return array('error' => $this->language->get('error_data_corrupted'));
            }

            $res['request_count'] = -1; //deduct header
            while(!feof($fh)){
                fgets($fh);
                $res['request_count']++;
            }

            fclose($fh);
        }

        return $res;
    }

	private function validateRequest() {
        $file = $this->request->files['imported_file'];
        $post = $this->request->post;
        if (!in_array($file['type'], $this->file_types)) {
            $this->session->data['error'] = $this->language->get('error_file_format');
            return false;
        } elseif (file_exists($file['tmp_name']) && $file['size'] > 0) {
            return true;
        } elseif (file_exists($file['tmp_name'])) {
            $this->session->data['error'] = $this->language->get('error_file_empty');
            return false;
        } elseif ($file['error'] != 0) {
            $this->session->data['error'] = $this->language->get('error_upload_' . $file['error']);
            return false;
        } else {
            $this->session->data['error'] = 'Request for export is empty!';
            return false;
        }
	}

}