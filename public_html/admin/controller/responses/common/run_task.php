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
class ControllerResponsesCommonRunTask extends AController {
	public $data= array();
	    
  	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		//init controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library.tpl');

	}

  	public function getTask() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if(!has_value($this->request->get['task_name'])){
			$this->data['output'] = array(
										'error'=>true,
										'error_text' => 'Error: Do not know what to run.');
		}else{
			$task_obj = new ATaskManager();
			$this->data['output'] = $task_obj->getTaskByName($this->request->get['task_name']);
		}

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		if($this->data['output']){
			$output = AJson::encode($this->data['output']);
		}else{
			$output = array('error'=>true,
							'error_text' => 'Error: Cannot find task "'.$this->request->get['task_name'].'".');
		}

		$this->response->setOutput( $output );
	}
}