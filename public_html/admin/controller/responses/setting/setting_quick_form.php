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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
class ControllerResponsesSettingSettingQuickForm extends AController {
    public $data = array();
    private $error = array();
    private $fields = array('language_key', 'language_value', 'block', 'section');

 
    public function main() {
        if (!$this->user->canModify('setting/setting_quick_form')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadModel('setting/setting');
        $this->loadLanguage('setting/setting');
        $this->loadLanguage('common/header');

        $this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $setting = explode( '-', $this->request->get['active'] );
        $group = $setting[0];
        $setting_key = $setting[1];
	    $store_id = !isset($this->request->get['store_id']) ? $setting[2] : $this->request->get['store_id'];

	    if(is_int(strpos($setting_key,'config_description'))){
	        $setting_key = substr($setting_key,0,strrpos($setting_key,'_'));
		    $this->request->get['active'] = $group.'-'.$setting[1].'-'.$store_id;
        }else{
	        $this->request->get['active'] = $group.'-'.$setting_key.'-'.$store_id;
	    }

        $this->document->setTitle($this->language->get('heading_title'));
        if (($this->request->server['REQUEST_METHOD'] == 'POST' && $this->_validateForm($group)) ) {
            $this->model_setting_setting->editSetting( $group, $this->request->post, $store_id );
            $this->view->assign('success', $this->language->get('text_success'));
        }


        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
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
        $form = new AForm('ST');
        $this->data['setting_id'] = (int)$this->request->get['setting_id'];


        $dispatch = $this->dispatch('responses/common/form_collector', array('form_id' => 'qsFrm', 'target' => $this->request->get['target'],'success_script' => 'CKEditor(\'destroy\'); CKEditor(\'add\');') );
        $this->data['form_collector'] = $dispatch->dispatchGetOutput();


        $this->document->addBreadcrumb(array(
            'href' => $this->data['action'],
            'text' => $this->data['heading_title'],
            'separator' => ' :: '
        ));

        $form->setForm(array(
            'form_name' => 'qsFrm',
            'update' => $this->data['update'],
        ));

        $this->data['form']['id'] = 'qsFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'qsFrm',
            'action' => $this->data['action'],
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
        $data = $this->model_setting_setting->getSetting($group,$store_id);
        $this->data['form']['fields'] = $conf_mngr->getFormField($setting_key, $form, $data,$store_id, $group);

        if(in_array($setting_key, array('config_logo','config_icon'))){

            $this->data['rl'] = $this->html->getSecureURL('common/resource_library', '&type=image&mode=url');
            $resource = new AResource('image');
            if($setting_key == 'config_logo'){
                $logo = $this->dispatch(
                    'responses/common/resource_library/get_resource_html_single',
                    array('type' => 'image',
                        'wrapper_id' => 'config_logo',
                        'resource_id' => $resource->getIdFromHexPath(str_replace('image/', '', $data['config_logo'])),
                        'field' => 'config_logo'));
                $this->data['form']['fields']['logo'] .= $logo->dispatchGetOutput();
            }else{
                $icon = $this->dispatch(
                    'responses/common/resource_library/get_resource_html_single',
                    array('type' => 'image',
                        'wrapper_id' => 'config_icon',
                        'resource_id' => $resource->getIdFromHexPath(str_replace('image/', '', $data['config_icon'])),
                        'field' => 'config_icon'));
                $this->data['form']['fields']['icon'] = $icon->dispatchGetOutput();
            }

        }

        $this->loadModel('setting/store');
        $results = $this->model_setting_store->getStores();
        $stores = array();
        $stores[0] = $this->language->get('text_default');
        foreach ($results as $result) {
            $stores[$result['store_id']] = $result['alias'];
        }

        $this->data['store_selector'] = $this->html->buildSelectbox(array(
            'type' => 'selectbox',
            'name' => 'store_switcher',
            'value' => $store_id,
            'options' => $stores
        ));

	    $this->data['template_image'] = $this->html->getSecureURL('setting/template_image');

        $this->view->assign('form_title', $this->language->get('tab_'.$setting[0]) );
        $this->view->assign('help_url', $this->gen_help_url('setting_edit'));
        $this->view->assign('active', $this->request->get['active']);
        $this->view->batchAssign($this->data);
        $this->view->setTemplate('responses/setting/setting_quick_form.tpl');
        $this->view->render();
        if($this->data['form']['fields']){
            $output['html'] = $this->view->getOutput();
        }else{
            $output['html'] ='';
        }
        $output['title'] = $this->data['heading_title'];

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($output));

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
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

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
