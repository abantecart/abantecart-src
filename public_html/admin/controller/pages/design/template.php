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

class ControllerPagesDesignTemplate extends AController {
    
  public function main() {
	$data = array();
    //use to init controller data
    $this->extensions->hk_InitData($this,__FUNCTION__);

	$this->document->addStyle(array(
	  'href' => RDIR_TEMPLATE . 'stylesheet/layouts-manager.css',
	  'rel' => 'stylesheet'
	));
    
    $this->document->setTitle($this->language->get('heading_title'));

    // breadcrumb path
    $this->document->initBreadcrumb(array(
      'href' => $this->html->getSecureURL('index/home'),
      'text' => $this->language->get('text_home'),
    ));
    $this->document->addBreadcrumb(array(
      'href'  => $this->html->getSecureURL('design/template'),
      'text'  => $this->language->get('heading_title'),
      'current' => true,
    ));

    $data['current_url'] = $this->html->getSecureURL('design/template');
	$data['form_store_switch'] = $this->html->getStoreSwitcher();
	$data['help_url'] = $this->gen_help_url('set_storefront_template');
	$this->loadLanguage('setting/setting');
	$data['manage_extensions'] = $this->html->buildElement(
	    	array(
	    			'type' => 'button',
	    			'name' => 'manage_extensions',
	    			'href' => $this->html->getSecureURL('extension/extensions/template'),
	    			'text' => $this->language->get('button_manage_extensions'),
	    			'title' => $this->language->get('button_manage_extensions')
			)
	);

	$data['store_id'] = 0;
	if ($this->request->get['store_id']) {
	    $data['store_id'] = $this->request->get['store_id'];
	} else {
	    $data['store_id'] = $this->config->get('config_store_id');
	}
    
	//check if we have developer tools installed
	$dev_tools = $this->extensions->getExtensionsList(array('search'=>'developer_tools'))->row;

    // get templates
    $data['templates'] = array();

	require_once(DIR_CORE.'lib/config_manager.php');
	$conf_mngr = new AConfigManager();

	//get all enabled templates
	$tmpls = $conf_mngr->getTemplates('storefront');
	$settings = $this->model_setting_setting->getSetting('appearance', $data['store_id']);
	$data['default_template'] = $settings['config_storefront_template'];

	foreach ($tmpls as $tmpl) {
	    $templates[$tmpl] = array(
	    						'name' => $tmpl,
	    						'edit_url' => $this->html->getSecureURL('setting/setting', '&active=appearance&tmpl_id='.$tmpl));

		//button for template cloning
		if( is_null($dev_tools['status']) ){
		    $templates[$tmpl]['clone_url'] = "http://www.abantecart.com/extension-developer-tools";
		} elseif ($dev_tools['status']==1){
		    $templates[$tmpl]['clone_url'] = $this->html->getSecureURL('tool/developer_tools/create', '&template='.$tmpl);
		} else {
		    $templates[$tmpl]['clone_url'] = $this->html->getSecureURL('extension/extensions/edit','&extension=developer_tools');
		}
		//button to extension
		if(!is_dir( 'storefront/view/' . $tmpl) && is_dir( DIR_EXT . $tmpl)) {
		    $templates[$tmpl]['extn_url'] = $this->html->getSecureURL('extension/extensions/edit','&extension='.$tmpl);			
		}
		//set default 
		if($data['default_template'] != $tmpl){
		    $templates[$tmpl]['set_defailt_url'] = $this->html->getSecureURL('design/template/set_default','&tmpl_id='.$tmpl.'&store_id='.$data['store_id']);
		}

        $preview_file = $tmpl . '/image/preview.jpg';
		if ( is_file( DIR_EXT . $preview_file) ) {
            $preview_img = HTTPS_EXT . $preview_file;
        } else if (is_file( 'storefront/view/' . $tmpl . '/image/preview.jpg')) {
			$preview_img = HTTPS_SERVER . 'storefront/view/' . $tmpl . '/image/preview.jpg';
		} else {
			$preview_img = HTTPS_IMAGE . 'no_image.jpg';
		}
		$templates[$tmpl]['preview'] = $preview_img;
	}

	$data['templates'] = $templates;    
    
    // Alert messages
    if (isset($this->session->data['warning'])) {
      $data['error_warning'] = $this->session->data['warning'];
      unset($this->session->data['warning']);
    }
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    }

    $this->view->batchAssign($data);
    $this->processTemplate('pages/design/template.tpl');
    //update controller data
    $this->extensions->hk_UpdateData($this,__FUNCTION__);
  }

  public function set_default() {
    //use to init controller data
    $this->extensions->hk_InitData($this,__FUNCTION__);

	$this->loadModel('setting/setting');

	$store_id = 0;
	if ($this->request->get['store_id']) {
	    $store_id = $this->request->get['store_id'];
	} else {
	    $store_id = $this->config->get('config_store_id');
	}

	if($this->request->get['tmpl_id']) {    
		$this->model_setting_setting->editSetting(	'appearance',
													 array('config_storefront_template' => $this->request->get['tmpl_id']), 
													 $store_id
												  );
		$this->session->data['success'] = $this->language->get('text_success');
	} else {
		$this->session->data['warning'] = $this->language->get('text_error');
	}
	
	$this->redirect($this->html->getSecureURL('design/template'));
	  
    //update controller data
    $this->extensions->hk_UpdateData($this,__FUNCTION__);
  }
  
}
?>