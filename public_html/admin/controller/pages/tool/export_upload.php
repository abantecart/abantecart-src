<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

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
class ControllerPagesToolExportUpload extends AController {

	public function main()
	{
		$this->extensions->hk_InitData($this,__FUNCTION__);

		if ( $this->request->is_POST() && $this->user->canModify('tool/import_export') )
		{
			if ( empty($this->request->post['data']) )
			{
				$this->session->data['error'] = 'Data for export is empty!';
				$this->redirect($this->html->getSecureURL('tool/import_export', '&active=export'));
			}

			$request = $this->validateRequest($this->request->post['data']);

			$this->data = new AData();	
			$array_new = $this->data->exportData($request);

			if ( !empty($request) ) {
				if ( empty($this->request->post['options']['file_name']) ) {
					$fileName = 'data_export_' . date('mdY_His');
				} else {
					$fileName = $this->request->post['options']['file_name'];
				}

				$result = false;

				switch ($this->request->post['options']['file_format']) {
					case 'csv':
						
						$fileName .= '.tar.gz';
						$result = $this->data->array2CSV($array_new, $fileName, $this->request->post['options']['delimiter']);
						break;

					case 'txt':
						$fileName .= '.tar.gz';
						$result = $this->data->array2CSV($array_new, $fileName, $this->request->post['options']['delimiter'], '.txt');
						break;

					case 'xml':

						$fileName .= '.xml';
						$result = $this->data->array2XML( $array_new );

						break;
						
					default:
						return null;
				}

				if (!headers_sent()) {
					if ( $result ) {
						header('Pragma: public');
						header('Expires: 0');
						header('Content-Description: File Transfer');
						header('Content-Type: application/octet-stream');
						header('Content-Disposition: attachment; filename="' . $fileName . '"');
						header('Content-Transfer-Encoding: binary');

						print($result);exit; // popup window with file upload dialog

					} else {
						$this->session->data['error'] = 'Error during export! Please check errors report.';
					}

					//update controller data
					$this->extensions->hk_UpdateData($this,__FUNCTION__);
					$this->redirect($this->html->getSecureURL('tool/import_export', '&active=export'));
					return null;
				} else {
					exit('Error: Headers already sent out!');
				}
			} else {
				$this->session->data['error'] = 'Request for export is empty!';
				$this->redirect($this->html->getSecureURL('tool/import_export', '&active=export'));
				return null;
			}

		} else {
			$this->redirect($this->html->getSecureURL('tool/import_export', '&active=export'));
			return $this->dispatch('error/permission');
		}
	}

	private function validateRequest($post) {

		$results = array();

		foreach ( $post as $key => $val ) {
			if ( (bool) $val['is_checked'] || (isset($val['tables']) && !empty($val['tables'])) ) {

				if ( $val['start_id'] != '' ) {
					$val['start_id'] = (int) $val['start_id'];
				} else {
					$val['start_id'] = 0;
				}

				if ( $val['end_id'] != '' ) {
					$val['end_id'] = (int) $val['end_id'];
				}
				$results[$key] = $val;
			}
			unset($val);
		}

		return $results;
	}
	
}