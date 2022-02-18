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
if (!IS_ADMIN || !defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultTwilio extends AController
{

    public $data = array();

    public function test()
    {
        $this->registry->set('force_skip_errors', true);
        $this->loadLanguage('default_twilio/default_twilio');
        $this->loadModel('setting/setting');
        require_once(DIR_EXT.'default_twilio/core/lib/Services/Twilio.php');
        require_once(DIR_EXT.'default_twilio/core/lib/Twilio/autoload.php');

        $cfg = $this->model_setting_setting->getSetting('default_twilio', (int)$this->session->data['current_store_id']);
        $AccountSid = $cfg['default_twilio_username'];
        $AuthToken = $cfg['default_twilio_token'];

        $sender = new \Twilio\Rest\Client($AccountSid, $AuthToken);
        $to = preg_replace('/[^0-9\+]/', '', $this->request->get['to']);


        $from = $this->config->get('default_twilio_sender_phone');

        $from = $from ? '+'.ltrim($from, '+') : '';

        $error_message = '';
        try {
            $sender->messages->create(
                $to,
                array(
                    'from' => $from,
                    'body' => 'test message',
                )
            );

        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $this->registry->set('force_skip_errors', false);
        $json = array();

        if (!$error_message) {
            $json['message'] = $this->language->get('text_connection_success');
            $json['error'] = false;
        } else {
            $json['message'] = "Connection to Twilio server can not be established.<br>".$error_message.".<br>Check your server configuration or contact your hosting provider.";
            $json['error'] = true;
        }

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));

    }

}