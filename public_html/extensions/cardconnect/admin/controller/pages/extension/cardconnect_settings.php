<?php

class ControllerPagesExtensionCardConnectSettings extends AController
{
    private $error = array();
    public $data = array();
    private $errors = array('cardconnect_sk_live', 'cardconnect_sk_test');

    private $fields = array(
        'cardconnect_access_token',
        'cardconnect_test_mode',
        'cardconnect_sk_live',
        'cardconnect_sk_test',
        'cardconnect_settlement',
        'cardconnect_save_cards_limit',
    );

    public function main()
    {

        $this->request->get['extension'] = 'cardconnect';
        $this->loadLanguage('cardconnect/cardconnect');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        $this->document->addStyle(
            array(
                'href'  => $this->view->templateResource('/stylesheet/cardconnect.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            )
        );

        //did we get code from cardconnect connect
        /*	if( $this->request->get['access_token'] ) {
                //need to save cardconnect access_token and set live mode
                $settings = array(
                    'cardconnect_access_token' => $this->request->get['access_token'],
                    'cardconnect_test_mode' => 1
                );
                if( $this->request->get['livemode'] ) {
                    $settings['cardconnect_test_mode'] = 0;
                }
                
                $this->model_setting_setting->editSetting('cardconnect', $settings);
                $this->session->data['success'] = $this->language->get('text_connect_success');
                $this->redirect($this->html->getSecureURL('extension/cardconnect_settings'));
            } else if($this->request->get['disconnect']) {
                $this->model_setting_setting->editSetting('cardconnect', array('cardconnect_access_token' => '' ));
                $this->session->data['success'] = $this->language->get('text_disconnect_success');
                $this->redirect($this->html->getSecureURL('extension/cardconnect_settings'));
            }*/

        if ($this->request->is_POST() && $this->_validate()) {
            $this->model_setting_setting->editSetting('cardconnect', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->html->getSecureURL('extension/cardconnect_settings'));
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['success'] = $this->session->data['success'];
        if (isset($this->session->data['success'])) {
            unset($this->session->data['success']);
        }

        $this->data['error'] = array();
        foreach ($this->errors as $f) {
            if (isset ($this->error[$f])) {
                $this->data['error'][$f] = $this->error[$f];
            }
        }
        //error with cardconnect connect?
        if ($this->request->get['error']) {
            $this->data['error'][$this->request->get['error']] = $this->request->get['error_dec'];
        }

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('extension/extensions/payment'),
            'text'      => $this->language->get('text_payment'),
            'separator' => ' :: ',
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('payment/cardconnect'),
            'text'      => $this->language->get('cardconnect_name'),
            'separator' => ' :: ',
            'current'   => true,
        ));

        foreach ($this->fields as $f) {
            if (isset ($this->request->post [$f])) {
                $this->data[$f] = $this->request->post[$f];
            } else {
                $this->data[$f] = $this->config->get($f);
            }
        }

        //if skip connect is selected or API keys are set up
        if ($this->request->get['skip_connect']
            || (!$this->data['cardconnect_access_token'] && ($this->data['cardconnect_sk_test'] || $this->data['cardconnect_sk_live']))
        ) {
            $this->data['skip_connect'] = true;
        }

        $this->data['action'] = $this->html->getSecureURL('extension/cardconnect_settings', '&extension=cardconnect');
        $this->data['disconnect'] = $this->html->getSecureURL('extension/cardconnect_settings', '&extension=cardconnect&disconnect=true');
        $this->data['heading_title'] = $this->language->get('text_edit').$this->language->get('cardconnect_name');
        $this->data['form_title'] = $this->language->get('heading_title');
        $this->data['update'] = $this->html->getSecureURL('r/extension/cardconnect/update');
        $url = base64_encode($this->html->getSecureURL('extension/cardconnect_settings', '&extension=cardconnect'));
        $this->data['connect_url'] = base64_decode('aHR0cHM6Ly9tYXJrZXRwbGFjZS5hYmFudGVjYXJ0LmNvbS9zdHJpcGVfY29ubmVjdC5waHA=');
        $this->data['connect_url'] .= '?clid=ca_5XtCjhqt1xB4wy8bMvr3QVlbtJg2coIs';
        $this->data['connect_url'] .= '&ret='.$url;

        //see if we are connected yet to cardconnect
        $cardconnect_code = true; //$this->config->get('cardconnect_access_token');
        if ($cardconnect_code) {
            //validate the token
            $this->data['connected'] = true;
        } else {
            $this->data['skip_url'] = $this->html->getSecureURL('extension/cardconnect_settings', '&extension=cardconnect&skip_connect=true');
        }
        $form = new AForm('HT');
        $form->setForm(array(
            'form_name' => 'editFrm',
            'update'    => $this->data ['update'],
        ));

        $this->data['form']['form_open'] = $form->getFieldHtml(array(
            'type'   => 'form',
            'name'   => 'editFrm',
            'action' => $this->data ['action'],
            'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
        ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_save'),
        ));

        //cardconnect related settings
        $this->data['test_mode'] = $this->data['cardconnect_test_mode'];
        $this->data['form']['fields']['cardconnect_test_mode'] = $form->getFieldHtml(array(
            'type'  => 'checkbox',
            'name'  => 'cardconnect_test_mode',
            'value' => $this->data['cardconnect_test_mode'],
            'style' => 'btn_switch',
        ));

        $this->data['form']['fields']['cardconnect_sk_test'] = $form->getFieldHtml(array(
            'type'     => 'input',
            'name'     => 'cardconnect_sk_test',
            'value'    => $this->data['cardconnect_sk_test'],
            'required' => true,
        ));
        $this->data['form']['fields']['cardconnect_sk_live'] = $form->getFieldHtml(array(
            'type'     => 'input',
            'name'     => 'cardconnect_sk_live',
            'value'    => $this->data['cardconnect_sk_live'],
            'required' => true,
        ));
        $this->data['form']['fields']['cardconnect_save_cards_limit'] = $form->getFieldHtml(array(
            'type'  => 'input',
            'name'  => 'cardconnect_save_cards_limit',
            'value' => $this->data['cardconnect_save_cards_limit'] ? $this->data['cardconnect_save_cards_limit'] : 5,
        ));

        $settlement = array(
            'auto'    => $this->language->get('cardconnect_settlement_auto'),
            'delayed' => $this->language->get('cardconnect_settlement_delayed'),
        );
        $this->data['form']['fields']['cardconnect_settlement'] = $form->getFieldHtml(array(
            'type'    => 'selectbox',
            'name'    => 'cardconnect_settlement',
            'options' => $settlement,
            'value'   => $this->data['cardconnect_settlement'],
        ));

        //load tabs controller
        $this->data['groups'][] = 'additional_settings';
        $this->data['link_additional_settings'] = $this->data['action'];
        $this->data['active_group'] = 'additional_settings';

        $tabs_obj = $this->dispatch('pages/extension/extension_tabs', array($this->data));
        $this->data['tabs'] = $tabs_obj->dispatchGetOutput();
        unset($tabs_obj);

        $obj = $this->dispatch('pages/extension/extension_summary', array($this->data));
        $this->data['extension_summary'] = $obj->dispatchGetOutput();
        unset($obj);

        $this->view->batchAssign($this->data);
        $this->view->batchAssign($this->language->getASet());
        $this->processTemplate('pages/extension/cardconnect_settings.tpl');

    }

    private function _validate()
    {
        if (!$this->user->canModify('cardconnect/cardconnect')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->request->get['skip_connect']) {
            if (!$this->request->post['cardconnect_sk_live']) {
                $this->error['cardconnect_sk_live'] = $this->language->get('error_cardconnect_sk_live');
            }
            if (!$this->request->post['cardconnect_sk_test']) {
                $this->error['cardconnect_sk_test'] = $this->language->get('error_cardconnect_sk_test');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
