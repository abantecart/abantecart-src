<?php
	/*------------------------------------------------------------------------------
   $Id$

   AbanteCart, Ideal OpenSource Ecommerce Solution
   http://www.AbanteCart.com

   Copyright © 2011 Belavier Commerce LLC

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

class ControllerPagesToolFiles extends AController {
	public $data;

	public function download() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if ($this->user->canAccess('tool/files')) {
			$filename = str_replace(array( '../', '..\\', '\\', '/' ), '', $this->request->get[ 'filename' ]);

			$am = new AAttribute($this->request->get[ 'attribute_type' ]);
			$attribute_data = $am->getAttribute($this->request->get['attribute_id']);

			if ( has_value($attribute_data['settings']['directory']) ) {
				$file = DIR_APP_SECTION . 'system/uploads/' . $attribute_data['settings']['directory'] . '/' . $filename;
			} else {
				$file = DIR_APP_SECTION . 'system/uploads/' . $filename;
			}

			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/x-gzip');
				header('Content-Disposition: attachment; filename=' . $filename);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			} else {
				echo 'file does not exists!';
			}
		} else {
			return $this->dispach('error/permission');
		}
	}

}