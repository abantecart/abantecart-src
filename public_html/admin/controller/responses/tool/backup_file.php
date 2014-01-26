<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ControllerResponsesToolBackupFile extends AController {
	private $error = array();
		
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->_validate()) {
			
			$this->loadModel('tool/backup');

	        $bkp = $this->model_tool_backup->backup($this->request->post['backup'],$this->request->post['backup_rl'],$this->request->post['backup_config']);
			if($bkp){
				$install_upgrade_history = new ADataset('install_upgrade_history','admin');
				$install_upgrade_history->addRows(array('date_added'=> date("Y-m-d H:i:s",time()),
				                            'name' => 'Manual Backup',
				                            'version' => VERSION,
				                            'backup_file' => $this->model_tool_backup->backup_filename.'.tar.gz',
				                            'backup_date' => date("Y-m-d H:i:s",time()),
				                            'type' => 'backup',
				                            'user' => $this->user->getUsername() ));
			}
			if($this->model_tool_backup->error){
				$this->session->data['error'] = $this->model_tool_backup->error;
				$this->redirect($this->html->getSecureURL('tool/backup'));
			}else{
				$this->loadLanguage('tool/backup');
				$this->session->data['success'] = $this->language->get('text_success_backup');
				$this->redirect($this->html->getSecureURL('tool/install_upgrade_history'));
			}
              //update controller data
            $this->extensions->hk_UpdateData($this,__FUNCTION__);
		} else {
			return $this->dispach('error/permission');
		}
	}

    private function _validate() {
		if (!$this->user->canModify('tool/backup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

	    $this->request->post['backup_rl'] = $this->request->post['backup_rl']=='rl' ? true : false;
	    $this->request->post['backup_config'] = $this->request->post['backup_config']=='config' ? true : false;

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	
}
?>