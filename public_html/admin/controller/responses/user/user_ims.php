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
class ControllerResponsesUserUserIMs extends AController {
	public $data = array();
	public $error = array();

  	public function main() {

	    $this->data = func_get_arg(0);

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$protocols = $this->im->getProtocols();

	    $sendpoints = array_keys($this->im->sendpoints);

		$ims = $this->im->getUserIMs($this->data['user_id'], $this->session->data['current_store_id']);
	    foreach($sendpoints as $sendpoint){
		    $imsettings = $ims[$sendpoint];

		    if(!isset($this->data['sendpoints'][$sendpoint])){
			    $point = array(
					    'text' => $this->language->get('im_sendpoint_name_'.preformatTextID($sendpoint)),
					    'value' => array()
					    );
			}else{
			    $point = $this->data['sendpoints'][$sendpoint];
		    }
		    //mark error sendpoints
		    if(!in_array($sendpoint, $sendpoints)){
			    $point['error'] = true;
			    $this->log->write('IM sendpoint '.$sendpoint.' is not in sendpoints list! ');
		    }

		    foreach($imsettings as $row){
			    if ($row['uri'] && in_array($row['protocol'], $protocols)){
				    $point['value'][] = $row['protocol'];
			    }
		    }

		    $this->data['sendpoints'][$sendpoint] = $point;
	    }

		$this->data['im_settings_url'] = $this->html->getSecureURL('user/user_ims/settings','&user_id='.$this->data['user_id']);


		$this->view->assign('help_url', $this->gen_help_url('user_edit') );
		$this->view->batchAssign( $this->data );

		$this->processTemplate('/responses/user/user_im_list.tpl');

	    //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function settings(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$user_id = $this->request->get['user_id'];
		$this->loadModel('user/user');
		$user_info = $this->model_user_user->getUser($user_id);

		$this->data['user_id'] = $user_id;
		$sendpoint = $this->request->get['sendpoint'];

		$this->data['text_title'] = $this->language->get('im_sendpoint_name_'.preformatTextID($sendpoint)).' '.sprintf($this->language->get('text_notification_for', 'common_im'),$user_info['username']);
		$this->data['action'] = $this->html->getSecureURL('user/user_ims/saveIMSettings', '&user_id=' . $user_id.'&sendpoint='.$sendpoint );

		$form = new AForm('HS');
		$form->setForm(array(
		    'form_name' => 'imsetFrm',
			'update' => $this->data['action'].'&qs=1',
	    ));

        $this->data['form']['id'] = 'imsetFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'imsetFrm',
		    'action' => $this->data['action'],
			'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));



		$protocols = $this->im->getProtocols();
	    $all_sendpoints = array_keys($this->im->sendpoints);

		//mark error sendpoints
	    if(!in_array($sendpoint, $all_sendpoints)){
		    $this->data['error_warning'] = sprintf($this->language->get('error_unknown_sendpoint',$sendpoint));
		    $this->log->write('IM sendpoint '.$sendpoint.' is not in sendpoints list! ');
	    }

		$settings = $this->im->getUserSendPointSettings($this->data['user_id'], $sendpoint, $this->session->data['current_store_id']);

		$this->data['form']['fields']['email'] = $form->getFieldHtml(array(
            'type' => 'input' ,
            'name' => 'settings[email]',
            'value' => $settings['email']
		));

		//build prior email list
		$this->data['admin_emails'] = array();
		$ims = $this->im->getUserIMs($user_id, $this->session->data['current_store_id']);
		foreach($ims as $rows){
			foreach($rows as $row){
				if ($row['protocol'] != 'email' || !$row['uri']){
					continue;
				}
				$this->data['admin_emails'][] = $row['uri'];
			}
		}
		$this->data['admin_emails'][] = $user_info['email'];
		$this->data['admin_emails'][] = $this->config->get('store_main_email');

		$this->data['admin_emails'] = array_unique($this->data['admin_emails']);


		foreach($protocols as $protocol){
		    $uri = $settings[$protocol];
			$this->data['form']['fields'][$protocol] = $form->getFieldHtml(array(
	            'type' => 'input' ,
	            'name' => 'settings['.$protocol.']',
	            'value' => $uri
	        ));
	    }

		$this->view->batchAssign( $this->data );
		$this->processTemplate('/responses/user/user_im_settings.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

	}


	public function saveIMSettings(){

		if (!$this->user->canModify($this->rt)) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
					array('error_text' => sprintf($this->language->get('error_permission_modify'), $this->rt),
							'reset_value' => true
					));
		}


		if(!$this->request->is_POST() || !$this->request->get['user_id'] || !$this->request->get['sendpoint']){
			$this->redirect($this->html->getSecureURL('user/user'));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->im->errors = array();

		if($this->im->validateUserSettings($this->request->post['settings'])){

			$this->im->saveIMSettings(
					$this->request->get['user_id'],
					$this->request->get['sendpoint'],
					$this->session->data['current_store_id'],
					$this->request->post['settings']
			);
			$output['result_text'] = $this->language->get('text_settings_success_saved');

		}else{
			$errors = $this->im->errors;
			$error_text = implode('<br>', $errors);
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array('error_text' => $error_text,
					  'reset_value' => false
				));
		}


        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		if($this->request->get['qs']!=1){
			$this->load->library('json');
			$this->response->addJSONHeader();
			$this->response->setOutput(AJson::encode($output));
		}else{
			$this->response->setOutput($output['result_text']);
		}
	}
}
