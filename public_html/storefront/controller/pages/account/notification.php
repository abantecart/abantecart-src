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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesAccountNotification extends AController
{
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/notification');
            redirect($this->html->getSecureURL('account/login'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->loadModel('account/customer');

        if ($this->request->is_POST() && $this->csrftoken->isTokenValid()) {
            $this->model_account_customer->saveCustomerNotificationSettings($this->request->post['settings']);
            $this->session->data['success'] = $this->language->get('text_success');
            redirect($this->html->getSecureURL('account/account'));
        }

        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/account'),
            'text'      => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('account/notification'),
            'text'      => $this->language->get('text_notifications'),
            'separator' => $this->language->get('text_separator'),
        ));

        $form = new AForm();
        $form->setForm(array('form_name' => 'imFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(
            array(
                'type'   => 'form',
                'name'   => 'imFrm',
                'action' => $this->html->getSecureURL('account/notification'),
                'csrf'   => true,
            )
        );

        $protocols = $this->im->getActiveProtocols('storefront');
        $im_drivers = $this->im->getIMDriverObjects();
        //build protocol list
        foreach ($im_drivers as $name => $driver) {
            $this->data['protocols'][$name] = array('name' => $name);

            if (is_object($driver)) {
                $this->data['protocols'][$name]['title'] = $driver->getProtocolTitle();
            }
        }

        $sendpoints = $this->im->sendpoints;
        $all_im_settings = $this->model_account_customer->getCustomerNotificationSettings();
        $customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

        foreach ($sendpoints as $sendpoint => $sendpoint_data) {
            //skip sendpoint for admins and use only storefront => 0
            if (!$sendpoint_data[0]) {
                continue;
            }

            $imsettings = $all_im_settings[$sendpoint];
            $force_arr = $sendpoint_data[0]['force_send'];

            $point = array();
            $point['title'] = $this->language->get('im_sendpoint_name_'.preformatTextID($sendpoint));
            $point['note'] = $this->language->get('im_sendpoint_name_'.preformatTextID($sendpoint).'_note');
            $point['warn'] = '';

            foreach ($protocols as $protocol) {
                $read_only = '';
                $checked = false;
                if ($imsettings[$protocol]) {
                    $checked = true;
                }
                if (!$customer_info[$protocol]) {
                    $read_only = ' disabled readonly ';
                    $checked = false;
                } else {
                    if (has_value($force_arr) && in_array($protocol, $force_arr)) {
                        $read_only = ' disabled readonly ';
                        $checked = true;
                    }
                }
                $point['values'][$protocol] = $form->getFieldHtml(
                    array(
                        'type'    => 'checkbox',
                        'name'    => 'settings['.$sendpoint.']['.$protocol.']',
                        'value'   => '1',
                        'checked' => $checked,
                        'attr'    => $read_only,
                    )
                );

                //adds warning about empty IM address (URI)
                if (!$customer_info[$protocol]) {
                    $point['warn'] .= $this->language->get('im_protocol_'.$protocol.'_empty_warn');
                }
            }

            $this->data['form']['fields']['sendpoints'][$sendpoint] = $point;
        }

        $this->data['form']['continue'] = $form->getFieldHtml(array(
            'type' => 'submit',
            'icon' => 'fa fa-check',
            'name' => $this->language->get('button_continue'),
        ));

        $this->data['back'] = $this->html->getSecureURL('account/account');
        $back = HtmlElementFactory::create(array(
            'type'  => 'button',
            'name'  => 'back',
            'text'  => $this->language->get('button_back'),
            'icon'  => 'fa fa-arrow-left',
            'style' => 'button',
        ));
        $this->data['form']['back'] = $back;
        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/notification.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
