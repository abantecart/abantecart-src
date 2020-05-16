<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

/**
 * Class ControllerResponsesToolExtensionsStore
 *
 * @property ModelToolMPAPI $model_tool_mp_api
 */
class ControllerResponsesToolExtensionsStore extends AController
{

    public function main()
    {
    }

    public function connect()
    {

        //we get token back
        $mp_token = $this->request->get_or_post('mp_token');
        $html = "";
        if ($mp_token) {
            //save token and return
            $this->loadModel('setting/setting');
            $setting = array('mp_token' => $mp_token);
            $this->model_setting_setting->editSetting('api', $setting);

            $html = "
				<script type='text/javascript'>
				window.parent.reload_page();
				</script>
			";
        }

        $this->response->setOutput($html);
    }

    public function disconnect()
    {
        $return = '';
        $mp_token = $this->config->get('mp_token');
        if ($mp_token) {
            $this->loadModel('tool/mp_api');
            //disconnect remote marketplace fist 
            $result = $this->model_tool_mp_api->disconnect($mp_token);
            if ($result['status'] == 1) {
                //reset token localy
                $this->loadModel('setting/setting');
                $setting = array('mp_token' => '');
                $this->model_setting_setting->editSetting('api', $setting);
                $return = 'success';
                unset($this->session->data['ready_to_install']);
            } else {
                $return = 'error';
            }
        }
        //sucess all the time
        $this->response->setOutput($return);
    }

    public function install()
    {
        //we get extension_key back
        $extension_key = $this->request->get_or_post('extension_key');

        if ($extension_key) {
            //ready to install
            $url = $this->html->getSecureURL('tool/package_installer/download', '&extension_key='.$extension_key);
        } else {
            $url = $this->html->getSecureURL('extension/extensions_store', '&purchased_only=1');
        }

        $html = "
				<script type='text/javascript'>
				window.top.location.href = '".$url."';
				</script>
		";

        $this->response->setOutput($html);
    }
}