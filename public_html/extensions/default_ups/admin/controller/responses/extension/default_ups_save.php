<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionDefaultUpsSave extends AController {
	private $required_fields = array(
			'default_ups_key',
			'default_ups_username',
			'default_ups_password',
			'default_ups_city',
			'default_ups_state',
			'default_ups_country',
            'default_ups_weight_code',
            'default_ups_weight_class',
            'default_ups_length_class',
            'default_ups_length',
            'default_ups_height',
            'default_ups_width',
		);
    public function main(){}
    public function update() {

        $this->loadLanguage('extension/extensions');

        if (!$this->user->canModify('extension/extensions')) {
            $this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'extension/extensions') );
            return null;
        }
        foreach($this->required_fields as $fld){
            if( isset( $this->request->post[$fld] ) && trim($this->request->post[$fld])==''){
                $this->response->setOutput( sprintf($this->language->get('error_required_field'), 'extension/extensions') );
                return null;
            }
        }

        $store_id = isset($this->request->post['store_id']) ? (int)$this->request->post['store_id'] : $this->request->get['store_id'];
        $store_id = is_null($store_id) ? $this->config->get('config_store_id') : $store_id;
        $this->request->post['store_id'] = $store_id;


        if(isset($this->request->post['default_ups_weight_code']) && !in_array($this->request->post['default_ups_weight_code'], array('lb','kgs'))){
            $this->response->setOutput( 'Error: kgs or lb only!' );
            return null;
        }
        $this->extension_manager->editSetting('default_ups', $this->request->post);

    }
}
