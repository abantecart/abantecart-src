<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

class ControllerPagesAccountAddress extends AController
{
    public $error = [];

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('account/address');
            redirect($this->html->getSecureURL('account/login'));
        }
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));
        $this->getList();
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        unset($this->session->data['success']);
    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->validateForm($this->request->post)) {
            $this->data['address_id'] = $this->model_account_address->addAddress($this->request->post);
            $this->session->data['success'] = $this->language->get('text_insert');
            $this->extensions->hk_ProcessData($this, __FUNCTION__, $this->data);
            redirect($this->html->getSecureURL('account/address'));
        }

        $this->getForm();
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->is_POST() && $this->validateForm($this->request->post)) {
            $this->model_account_address->editAddress(
                (int)$this->request->get['address_id'],
                $this->request->post
            );

            if (isset($this->session->data['shipping_address_id'])
                && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])
            ) {
                unset(
                    $this->session->data['shipping_methods'],
                    $this->session->data['shipping_method']
                );
                $this->tax->setZone((int)$this->request->post['country_id'], (int)$this->request->post['zone_id']);
            }

            if (isset($this->session->data['payment_address_id'])
                && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])
            ) {
                unset(
                    $this->session->data['payment_methods'],
                    $this->session->data['payment_method']
                );
            }
            $this->session->data['success'] = $this->language->get('text_update');
            $this->extensions->hk_ProcessData($this, __FUNCTION__, $this->data);
            redirect($this->html->getSecureURL('account/address'));
        }

        $this->getForm();
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function delete()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));
        $address_id = (int)$this->request->get['address_id'];
        if ($address_id && $this->validateDelete($address_id)) {
            $this->model_account_address->deleteAddress($address_id);

            if (isset($this->session->data['shipping_address_id'])
                && ($address_id == $this->session->data['shipping_address_id'])
            ) {
                unset(
                    $this->session->data['shipping_address_id'],
                    $this->session->data['shipping_methods'],
                    $this->session->data['shipping_method']
                );
            }

            if (isset($this->session->data['payment_address_id'])
                && ($address_id == $this->session->data['payment_address_id'])
            ) {
                unset(
                    $this->session->data['payment_address_id'],
                    $this->session->data['payment_methods'],
                    $this->session->data['payment_method']
                );
            }

            $this->session->data['success'] = $this->language->get('text_delete');
            $this->extensions->hk_ProcessData($this, __FUNCTION__);
            redirect($this->html->getSecureURL('account/address'));
        }

        $this->getList();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getList()
    {
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/address'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);

        $results = $this->model_account_address->getAddresses();
        $addresses = [];
        foreach ($results as $result) {
            $addressId = (int)$result['address_id'];
            $formated_address = $this->customer->getFormattedAddress((array)$result, $result['format']);
            $edit = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $this->language->get('button_edit'),
                    'href' => $this->html->getSecureURL('account/address/update', '&address_id=' . $addressId)
                ]
            );
            $delete = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $this->language->get('button_delete'),
                    'href' => $this->html->getSecureURL('account/address/delete', '&address_id=' . $addressId)
                ]
            );
            $addresses[] = [
                'address_id'    => $addressId,
                'address'       => $formated_address,
                'button_edit'   => $edit,
                'button_delete' => $delete,
                'default'       => ($this->customer->getAddressId() == $addressId),
            ];
        }

        $this->view->assign('addresses', $addresses);

        $insert = $this->html->buildElement(
            [
                'type' => 'button',
                'name' => 'insert',
                'text' => $this->language->get('button_new_address'),
                'href' => $this->html->getSecureURL('account/address/insert'),
            ]
        );
        $this->view->assign('button_insert', $insert);

        $back = $this->html->buildElement(
            [
                'type' => 'button',
                'name' => 'back',
                'text' => $this->language->get('button_back'),
                'href' => $this->html->getSecureURL('account/account'),
            ]
        );
        $this->view->assign('button_back', $back);

        $this->processTemplate('pages/account/addresses.tpl');
    }

    protected function getForm()
    {
        $addressId = (int)$this->request->get['address_id'];
        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/account'),
                'text'      => $this->language->get('text_account'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('account/address'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        if (!$addressId) {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSecureURL('account/address/insert'),
                    'text'      => $this->language->get('text_edit_address'),
                    'separator' => $this->language->get('text_separator'),
                ]
            );
        } else {
            $this->document->addBreadcrumb(
                [
                    'href'      => $this->html->getSecureURL('account/address/update', 'address_id=' . $addressId),
                    'text'      => $this->language->get('text_edit_address'),
                    'separator' => $this->language->get('text_separator'),
                ]
            );
        }

        if ($addressId && $this->request->is_GET()) {
            $addressInfo = $this->model_account_address->getAddress($addressId);
        } else {
            $addressInfo = [];
        }

        $formTxtId = 'AddressFrm';
        $form = new AForm();
        $form->setForm(['form_name' => $formTxtId]);

        if (!$addressId) {
            $action = $this->html->getSecureURL('account/address/insert');
        } else {
            $action = $this->html->getSecureURL('account/address/update', '&address_id=' . $addressId);
        }
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => $formTxtId,
                'action' => $action,
                'csrf'   => true,
            ]
        );

        $form->loadFromDb($formTxtId);
        $formElements = $form->getFormElements();
        $this->data['zone_id'] = $this->request->post['zone_id']
            ?? $addressInfo['zone_id']
            ?? $this->config->get('config_zone_id');

        $this->data['country_id'] = $this->request->post['country_id']
            ?? $addressInfo['country_id']
            ?? $this->config->get('config_country_id');

        $this->data['error_warning'] = $this->error['warning'];
        foreach ($formElements as $group => $elements) {
            foreach ($elements as $name => $element) {
                //error messages
                $this->data['error_' . $name] = $this->error[$name];
                $this->data['entry_' . $name] = $element->display_name ?: $this->language->get('entry_' . $name);

                $elmValue = $this->request->post[$name]
                    ?: $addressInfo[$name]
                        //take extended fields value
                        ?: $addressInfo['ext_fields'][$name];

                if ($name == 'country_id') {
                    $element->value = $this->data['country_id'];
                } elseif ($name == 'zone_id') {
                    $element->value = $this->data['country_id'];
                    $element->zone_value = $this->data['zone_id'];
                    //set zone_id as value for select[option]
                    $element->submit_mode = 'id';
                    //show only zone selector
                    $element->zone_only = true;
                } elseif ($name == 'default') {
                    $checked = $this->request->post['default'] ?? ($this->customer->getAddressId() == $addressId);
                    $element->checked = $checked ? 1 : 0;
                } elseif ($element->type == 'checkbox') {
                    $element->checked = $element->value == $elmValue;
                } else {
                    $element->value = $elmValue;
                }

                $this->data['form']['fields'][$group][$name] = $element;
            }
        }

        $this->data['form']['back'] = $form->getFieldHtml(
            [
                'type' => 'button',
                'name' => 'back',
                'text' => $this->language->get('button_back'),
                'href' => $this->html->getSecureURL('account/address'),
            ]
        );
        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'id'   => 'submit_button',
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ]
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/account/address.tpl');
    }

    protected function validateForm(array &$data)
    {
        if (!$this->csrftoken->isTokenValid()) {
            $this->error['warning'] = $this->language->get('error_unknown');
        }

        /** @var ModelAccountAddress $mdl */
        $mdl = $this->loadModel('account/address');
        $data['address_id'] = (int)$this->request->get['address_id'];
        $this->error = $mdl->validateAddressData($data);
        if (!$this->error) {
            $form = new AForm();
            $form->loadFromDb('AddressFrm');
            $fList = $form->getFields();
            if ($fList) {
                foreach ($fList as $fName => $f) {
                    //if the field is checkbox and not present in the post-data - set it null
                    if (in_array($f['element_type'], ['C', 'G']) && !isset($data[$fName])) {
                        $data[$fName] = null;
                    }
                }
            }
        }

        $this->extensions->hk_ValidateData($this, ['indata' => $data]);

        return (!$this->error);
    }

    protected function validateDelete(int $addressId)
    {
        if ($this->model_account_address->getTotalAddresses() == 1) {
            $this->error['warning'] = $this->language->get('error_delete');
        }

        if ($this->customer->getAddressId() == $addressId) {
            $this->error['warning'] = $this->language->get('error_default');
        }
        $this->extensions->hk_ValidateData($this, ['address_id' => $addressId]);
        return (!$this->error);
    }
}