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
class ControllerPagesExtensionExtensionSummary extends AController {
	public $data = array();
  	public function main() {
		//Load input argumets for gid settings
	    $this->data = func_get_arg(0);
          //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->loadLanguage('extension/extensions');

	    $extension = $this->request->get['extension'];

	    if($extension && !$this->data['extension_info']){
		    $this->data['extension_info'] = $this->extensions->getExtensionInfo($extension);
	    }

	    $icon_ext_img_url = HTTP_CATALOG . 'extensions/' . $extension . '/image/icon.png';
        $icon_ext_dir = DIR_EXT . $extension . '/image/icon.png';
        $icon = (is_file($icon_ext_dir) ? $icon_ext_img_url : RDIR_TEMPLATE . 'image/default_extension.png');

	    $this->data['extension_info']['icon'] = $icon;
        $this->data['extension_info']['name'] = $this->language->get($extension . '_name');

        $datetime_format = $this->language->get('date_format_short').' '.$this->language->get('time_format');

        if($this->data['extension_info']['date_installed']){
            $this->data['extension_info']['installed'] = dateISO2Display($this->data['extension_info']['date_installed'], $datetime_format );
        }
        if($this->data['extension_info']['date_added']){
            $this->data['extension_info']['date_added'] =  dateISO2Display($this->data['extension_info']['date_added'], $datetime_format );
        }


	    if (isset($this->session->data['extension_updates'][$extension])) {

            $this->data['upgrade_button'] = $this->html->buildElement(
                                array(  'type'=> 'button',
                                        'name' => 'btn_upgrade',
                                        'id' => 'upgradenow',
                                        'href' => AEncryption::addEncoded_stid($this->session->data['extension_updates'][$extension]['url']),
                                        'text' => $this->language->get('button_upgrade')
                                ));
        }

        $this->data['extension_info']['license'] = $this->data['extension_info']['license_key'];

        $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/extension/extension_summary.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}