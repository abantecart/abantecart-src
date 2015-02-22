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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
class ControllerResponsesSettingSettingQuickForm extends AController {
    public $data = array();
    public $error = array();

    public function main() {
        if (!$this->user->canModify('setting/setting_quick_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

		$output = array('result_text'=>'');

		$this->loadModel('setting/setting');
        $this->loadLanguage('setting/setting');
        $this->loadLanguage('common/header');

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $setting = explode( '-', $this->request->get['active'] );
		$this->data['group'] = $setting[0];
        $this->data['setting_key'] = $setting[1];
	    $this->data['store_id'] = !isset($this->session->data['current_store_id']) ? $setting[2] : $this->session->data['current_store_id'];

	    if(is_int(strpos($this->data['setting_key'],'config_description'))){
	        $this->data['setting_key'] = substr($this->data['setting_key'],0,strrpos($this->data['setting_key'],'_'));
		    $this->request->get['active'] = $this->data['group'].'-'.$setting[1].'-'.$this->data['store_id'];
        }else{
	        $this->request->get['active'] = $this->data['group'].'-'.$this->data['setting_key'].'-'.$this->data['store_id'];
	    }

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST()){
			if( $this->_validateForm($this->data['group']) ) {
				$this->model_setting_setting->editSetting( $this->data['group'], $this->request->post, $this->data['store_id'] );
				$output['result_text'] = $this->language->get('text_success');

				$this->load->library('json');
				$this->response->addJSONHeader();
    			$this->response->setOutput(AJson::encode($output));
			}else{
				$error = new AError('');
				return $error->toJSONResponse('NO_PERMISSIONS_406',
					array('error_text' => $this->error,
						  'reset_value' => true
					));
			}
        }else{
			$this->_getForm();
		}
	    //update controller data
	    $this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _getForm(){

        if (isset($this->error['warning'])) {
			$output['error_text'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['error'] = $this->error;
        $languages = $this->language->getAvailableLanguages();
        foreach ($languages as $lang) {
            $this->data['languages'][$lang['language_id']] = $lang;
        }


        $this->data['action'] = $this->html->getSecureURL('setting/setting_quick_form', '&target=' . $this->request->get['target'].'&active='.$this->request->get['active']);
        $this->data['heading_title'] = $this->language->get('text_edit') . ' ' . $this->language->get('text_setting');
        $form = new AForm('HT');
        $this->data['setting_id'] = (int)$this->request->get['setting_id'];


        $form->setForm(array(
            'form_name' => 'qsFrm',
            'update' => $this->data['update'],
        ));

        $this->data['form']['id'] = 'qsFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'qsFrm',
            'action' => $this->data['action'],
			'attr' => 'class="aform form-horizontal"'
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
        require_once(DIR_CORE . 'lib/config_manager.php');
        $conf_mngr = new AConfigManager();
        $data = $this->model_setting_setting->getSetting($this->data['group'],$this->data['store_id']);


        $this->data['form']['fields'] = $conf_mngr->getFormField($this->data['setting_key'], $form, $data,$this->data['store_id'], $this->data['group']);

		$this->data['form_store_switch'] = $this->html->getStoreSwitcher();

	    $this->data['template_image'] = $this->html->getSecureURL('setting/template_image');

        $this->data['form_title'] = $this->language->get('tab_'.$this->data['group']);
		$this->data['help_url'] = $this->gen_help_url('setting_edit');
		$this->data['active'] = $this->request->get['active'];
		$this->data['title'] = $this->data['heading_title'];
        $this->view->batchAssign($this->data);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->processTemplate('responses/setting/setting_quick_form.tpl');
    }

    private function _validateForm($group) {
        if (!$this->user->canModify('setting/setting_quick_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

		$this->load->library('config_manager');
		$config_mngr = new AConfigManager();
		$result = $config_mngr->validate($group, $this->request->post);
		$this->error = $result['error'];
		$this->request->post = $result['validated']; // for changed data saving

		$this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
