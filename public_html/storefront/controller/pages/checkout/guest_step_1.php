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

class ControllerPagesCheckoutGuestStep1 extends AController
{
    public $error = array();
    public $data = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //is this an embed mode
        $cart_rt = 'checkout/cart';
        if ($this->config->get('embed_mode') == true) {
            $cart_rt = 'r/checkout/cart/embed';
        }

        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            redirect($this->html->getSecureURL($cart_rt));
        }

        //validate if order min/max are met
        if (!$this->cart->hasMinRequirement() || !$this->cart->hasMaxRequirement()) {
            redirect($this->html->getSecureURL($cart_rt));
        }

        if ($this->customer->isLogged()) {
            redirect($this->html->getSecureURL('checkout/shipping'));
        }

        if (!$this->config->get('config_guest_checkout') || $this->cart->hasDownload()) {
            $this->session->data['redirect'] = $this->html->getSecureURL('checkout/shipping');
            redirect($this->html->getSecureURL('account/login'));
        }
        $_post =& $this->request->post;
        $_session =& $this->session->data;
        if ($this->request->is_POST() && $this->_validate($_post)) {
            $_session['guest']['firstname'] = trim($_post['firstname']);
            $_session['guest']['lastname'] = trim($_post['lastname']);
            $_session['guest']['email'] = trim($_post['email']);
            $_session['guest']['telephone'] = trim($_post['telephone']);
            $_session['guest']['fax'] = trim($_post['fax']);
            $_session['guest']['company'] = trim($_post['company']);
            $_session['guest']['address_1'] = trim($_post['address_1']);
            $_session['guest']['address_2'] = trim($_post['address_2']);
            $_session['guest']['zone_id'] = (int)$_post['zone_id'];
            $_session['guest']['postcode'] = trim($_post['postcode']);
            $_session['guest']['city'] = trim($_post['city']);
            $_session['guest']['country_id'] = (int)$_post['country_id'];

            //IM addresses
            $protocols = $this->im->getProtocols();
            foreach ($protocols as $protocol) {
                if (has_value($_post[$protocol]) && !has_value($_session['guest'][$protocol])) {
                    $_session['guest'][$protocol] = $_post[$protocol];
                }
            }

            $this->tax->setZone($_post['country_id'], $_post['zone_id']);

            $this->loadModel('localisation/country');
            $country_info = $this->model_localisation_country->getCountry($_post['country_id']);

            if ($country_info) {
                $_session['guest']['country'] = $country_info['name'];
                $_session['guest']['iso_code_2'] = $country_info['iso_code_2'];
                $_session['guest']['iso_code_3'] = $country_info['iso_code_3'];
                $_session['guest']['address_format'] = $country_info['address_format'];
            } else {
                $_session['guest']['country'] = '';
                $_session['guest']['iso_code_2'] = '';
                $_session['guest']['iso_code_3'] = '';
                $_session['guest']['address_format'] = '';
            }

            $this->loadModel('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($_post['zone_id']);

            if ($zone_info) {
                $_session['guest']['zone'] = $zone_info['name'];
                $_session['guest']['zone_code'] = $zone_info['code'];
            } else {
                $_session['guest']['zone'] = '';
                $_session['guest']['zone_code'] = '';
            }

            if (isset($_post['shipping_indicator'])) {
                $_session['guest']['shipping']['firstname'] = $_post['shipping_firstname'];
                $_session['guest']['shipping']['lastname'] = $_post['shipping_lastname'];
                $_session['guest']['shipping']['company'] = $_post['shipping_company'];
                $_session['guest']['shipping']['address_1'] = $_post['shipping_address_1'];
                $_session['guest']['shipping']['address_2'] = $_post['shipping_address_2'];
                $_session['guest']['shipping']['zone_id'] = $_post['shipping_zone_id'];
                $_session['guest']['shipping']['postcode'] = $_post['shipping_postcode'];
                $_session['guest']['shipping']['city'] = $_post['shipping_city'];
                $_session['guest']['shipping']['country_id'] = $_post['shipping_country_id'];

                $shipping_country_info = $this->model_localisation_country->getCountry($_post['shipping_country_id']);

                if ($shipping_country_info) {
                    $_session['guest']['shipping']['country'] = $shipping_country_info['name'];
                    $_session['guest']['shipping']['iso_code_2'] = $shipping_country_info['iso_code_2'];
                    $_session['guest']['shipping']['iso_code_3'] = $shipping_country_info['iso_code_3'];
                    $_session['guest']['shipping']['address_format'] = $shipping_country_info['address_format'];
                } else {
                    $_session['guest']['shipping']['country'] = '';
                    $_session['guest']['shipping']['iso_code_2'] = '';
                    $_session['guest']['shipping']['iso_code_3'] = '';
                    $_session['guest']['shipping']['address_format'] = '';
                }

                $shipping_zone_info = $this->model_localisation_zone->getZone($_post['shipping_zone_id']);

                if ($zone_info) {
                    $_session['guest']['shipping']['zone'] = $shipping_zone_info['name'];
                    $_session['guest']['shipping']['zone_code'] = $shipping_zone_info['code'];
                } else {
                    $_session['guest']['shipping']['zone'] = '';
                    $_session['guest']['shipping']['zone_code'] = '';
                }

            } else {
                unset($_session['guest']['shipping']);
            }

            unset($_session['shipping_methods']);
            unset($_session['shipping_method']);
            unset($_session['payment_methods']);
            unset($_session['payment_method']);

            $this->extensions->hk_ProcessData($this);
            redirect($this->html->getSecureURL('checkout/guest_step_2'));
        }

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ));
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL($cart_rt),
                'text'      => $this->language->get('text_cart'),
                'separator' => $this->language->get('text_separator'),
            ));
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('checkout/guest_step_1'),
                'text'      => $this->language->get('text_guest_step_1'),
                'separator' => $this->language->get('text_separator'),
            ));

        $form = new AForm();
        $form->setForm(array('form_name' => 'guestFrm'));
        $this->data['form']['form_open'] = $form->getFieldHtml(
            array(
                'type'   => 'form',
                'name'   => 'guestFrm',
                'action' => $this->html->getSecureURL('checkout/guest_step_1'),
                'csrf'   => true,
            )
        );

        if (isset($_post['firstname'])) {
            $firstname = $_post['firstname'];
        } elseif (isset($_session['guest']['firstname'])) {
            $firstname = $_session['guest']['firstname'];
        } else {
            $firstname = '';
        }

        $this->data['form']['fields']['general']['firstname'] = $form->getFieldHtml(array(
            'type'     => 'input',
            'name'     => 'firstname',
            'value'    => $firstname,
            'required' => true,
        ));

        if (isset($_post['lastname'])) {
            $lastname = $_post['lastname'];
        } elseif (isset($_session['guest']['lastname'])) {
            $lastname = $_session['guest']['lastname'];
        } else {
            $lastname = '';
        }
        $this->data['form']['fields']['general']['lastname'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'lastname',
                'value'    => $lastname,
                'required' => true,
            ));
        if (isset($_post['email'])) {
            $email = $_post['email'];
        } elseif (isset($_session['guest']['email'])) {
            $email = $_session['guest']['email'];
        } else {
            $email = '';
        }

        $this->data['form']['fields']['general']['email'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'email',
                'value'    => $email,
                'required' => true,
            ));
        if (isset($_post['telephone'])) {
            $telephone = $_post['telephone'];
        } elseif (isset($_session['guest']['telephone'])) {
            $telephone = $_session['guest']['telephone'];
        } else {
            $telephone = '';
        }
        $this->data['form']['fields']['general']['telephone'] = $form->getFieldHtml(
            array(
                'type'  => 'input',
                'name'  => 'telephone',
                'value' => $telephone,
            ));
        if (isset($_post['fax'])) {
            $fax = $_post['fax'];
        } elseif (isset($_session['guest']['fax'])) {
            $fax = $_session['guest']['fax'];
        } else {
            $fax = '';
        }
        $this->data['form']['fields']['general']['fax'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'fax',
                'value'    => $fax,
                'required' => false,
            ));

        //get only active IM drivers
        $im_drivers = $this->im->getIMDriverObjects();
        if ($im_drivers) {
            foreach ($im_drivers as $protocol => $driver_obj) {
                if (!is_object($driver_obj) || $protocol == 'email') {
                    continue;
                }

                if (isset($_post[$protocol])) {
                    $uri = $_post[$protocol];
                } elseif (isset($_session['guest'][$protocol])) {
                    $uri = $_session['guest'][$protocol];
                } else {
                    $uri = '';
                }

                $fld = $driver_obj->getURIField($form, $uri);
                $this->data['form']['fields']['general'][$protocol] = $fld;
                $this->data['entry_'.$protocol] = $fld->label_text;
            }
        }

        if (isset($_post['company'])) {
            $company = $_post['company'];
        } elseif (isset($_session['guest']['company'])) {
            $company = $_session['guest']['company'];
        } else {
            $company = '';
        }

        $this->data['form']['fields']['address']['company'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'company',
                'value'    => $company,
                'required' => false,
            ));
        if (isset($_post['address_1'])) {
            $address_1 = $_post['address_1'];
        } elseif (isset($_session['guest']['address_1'])) {
            $address_1 = $_session['guest']['address_1'];
        } else {
            $address_1 = '';
        }
        $this->data['form']['fields']['address']['address_1'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'address_1',
                'value'    => $address_1,
                'required' => true,
            ));

        if (isset($_post['address_2'])) {
            $address_2 = $_post['address_2'];
        } elseif (isset($_session['guest']['address_2'])) {
            $address_2 = $_session['guest']['address_2'];
        } else {
            $address_2 = '';
        }
        $this->data['form']['fields']['address']['address_2'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'address_2',
                'value'    => $address_2,
                'required' => false,
            ));

        if (isset($_post['city'])) {
            $city = $_post['city'];
        } elseif (isset($_session['guest']['city'])) {
            $city = $_session['guest']['city'];
        } else {
            $city = '';
        }

        $this->data['form']['fields']['address']['city'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'city',
                'value'    => $city,
                'required' => true,
            ));

        if (isset($_post['zone_id'])) {
            $zone_id = $_post['zone_id'];
        } elseif (isset($_session['guest']['zone_id'])) {
            $zone_id = $_session['guest']['zone_id'];
        } else {
            $zone_id = 'FALSE';
        }
        $this->view->assign('zone_id', $zone_id);

        $this->data['form']['fields']['address']['zone'] = $form->getFieldHtml(
            array(
                'type'     => 'selectbox',
                'name'     => 'zone_id',
                'required' => true,
            ));

        if (isset($_post['postcode'])) {
            $postcode = $_post['postcode'];
        } elseif (isset($_session['guest']['postcode'])) {
            $postcode = $_session['guest']['postcode'];
        } else {
            $postcode = '';
        }

        $this->data['form']['fields']['address']['postcode'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'postcode',
                'value'    => $postcode,
                'required' => true,
            ));

        if (isset($_post['country_id'])) {
            $country_id = $_post['country_id'];
        } elseif (isset($_session['guest']['country_id'])) {
            $country_id = $_session['guest']['country_id'];
        } else {
            $country_id = $this->config->get('config_country_id');
        }

        $this->loadModel('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        $options = array("FALSE" => $this->language->get('text_select'));
        foreach ($countries as $item) {
            $options[$item['country_id']] = $item['name'];
        }
        $this->data['form']['fields']['address']['country'] = $form->getFieldHtml(
            array(
                'type'     => 'selectbox',
                'name'     => 'country_id',
                'options'  => $options,
                'value'    => $country_id,
                'required' => true,
            ));

        $this->data['form']['shipping_indicator'] = $form->getFieldHtml(
            array(
                'type'       => 'checkbox',
                'name'       => 'shipping_indicator',
                'value'      => 1,
                'checked'    => (isset($_post['shipping_indicator'])
                    ? (bool)$_post['shipping_indicator']
                    : false),
                'label_text' => $this->language->get('text_indicator'),
            ));

        if (isset($_post['shipping_firstname'])) {
            $shipping_firstname = $_post['shipping_firstname'];
        } elseif (isset($_session['guest']['shipping']['firstname'])) {
            $shipping_firstname = $_session['guest']['shipping']['firstname'];
        } else {
            $shipping_firstname = '';
        }
        $this->data['form']['fields']['shipping']['shipping_firstname'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_firstname',
                'value'    => $shipping_firstname,
                'required' => true,
            ));
        if (isset($_post['shipping_lastname'])) {
            $shipping_lastname = $_post['shipping_lastname'];
        } elseif (isset($_session['guest']['shipping']['lastname'])) {
            $shipping_lastname = $_session['guest']['shipping']['lastname'];
        } else {
            $shipping_lastname = '';
        }
        $this->data['form']['fields']['shipping']['shipping_lastname'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_lastname',
                'value'    => $shipping_lastname,
                'required' => true,
            ));
        if (isset($_post['shipping_company'])) {
            $shipping_company = $_post['shipping_company'];
        } elseif (isset($_session['guest']['shipping']['company'])) {
            $shipping_company = $_session['guest']['shipping']['company'];
        } else {
            $shipping_company = '';
        }
        $this->data['form']['fields']['shipping']['shipping_company'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_company',
                'value'    => $shipping_company,
                'required' => false,
            ));
        if (isset($_post['shipping_address_1'])) {
            $shipping_address_1 = $_post['shipping_address_1'];
        } elseif (isset($_session['guest']['shipping']['address_1'])) {
            $shipping_address_1 = $_session['guest']['shipping']['address_1'];
        } else {
            $shipping_address_1 = '';
        }
        $this->data['form']['fields']['shipping']['shipping_address_1'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_address_1',
                'value'    => $shipping_address_1,
                'required' => true,
            ));
        if (isset($_post['shipping_address_2'])) {
            $shipping_address_2 = $_post['shipping_address_2'];
        } elseif (isset($_session['guest']['shipping']['address_2'])) {
            $shipping_address_2 = $_session['guest']['shipping']['address_2'];
        } else {
            $shipping_address_2 = '';
        }
        $this->data['form']['fields']['shipping']['shipping_address_2'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_address_2',
                'value'    => $shipping_address_2,
                'required' => false,
            ));

        if (isset($_post['shipping_city'])) {
            $shipping_city = $_post['shipping_city'];
        } elseif (isset($_session['guest']['shipping']['city'])) {
            $shipping_city = $_session['guest']['shipping']['city'];
        } else {
            $shipping_city = '';
        }
        $this->data['form']['fields']['shipping']['shipping_city'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_city',
                'value'    => $shipping_city,
                'required' => true,
            ));

        if (isset($_post['shipping_zone_id'])) {
            $shipping_zone_id = $_post['shipping_zone_id'];
        } elseif (isset($_session['guest']['shipping']['zone_id'])) {
            $shipping_zone_id = $_session['guest']['shipping']['zone_id'];
        } else {
            $shipping_zone_id = 'FALSE';
        }
        $this->view->assign('shipping_zone_id', $shipping_zone_id);
        $this->data['form']['fields']['shipping']['shipping_zone'] = $form->getFieldHtml(
            array(
                'type'     => 'selectbox',
                'name'     => 'shipping_zone_id',
                'required' => true,
            ));

        if (isset($_post['shipping_postcode'])) {
            $shipping_postcode = $_post['shipping_postcode'];
        } elseif (isset($_session['guest']['shipping']['postcode'])) {
            $shipping_postcode = $_session['guest']['shipping']['postcode'];
        } else {
            $shipping_postcode = '';
        }
        $this->data['form']['fields']['shipping']['shipping_postcode'] = $form->getFieldHtml(
            array(
                'type'     => 'input',
                'name'     => 'shipping_postcode',
                'value'    => $shipping_postcode,
                'required' => true,
            ));

        $options = array("FALSE" => $this->language->get('text_select'));
        foreach ($countries as $item) {
            $options[$item['country_id']] = $item['name'];
        }
        if (isset($_post['shipping_country_id'])) {
            $shipping_country_id = $_post['shipping_country_id'];
        } elseif (isset($_session['guest']['shipping']['country_id'])) {
            $shipping_country_id = $_session['guest']['shipping']['country_id'];
        } else {
            $shipping_country_id = $this->config->get('config_country_id');
        }
        $this->data['form']['fields']['shipping']['shipping_country'] = $form->getFieldHtml(
            array(
                'type'     => 'selectbox',
                'name'     => 'shipping_country_id',
                'options'  => $options,
                'value'    => $shipping_country_id,
                'required' => true,
            ));

        if (isset($_post['shipping_indicator'])) {
            $this->view->assign('shipping_addr', true);
        } elseif (isset($_session['guest']['shipping'])) {
            $this->view->assign('shipping_addr', true);
        } else {
            $this->view->assign('shipping_addr', false);
        }

        $this->view->assign('shipping', $this->cart->hasShipping());
        $this->loadModel('localisation/country');
        $this->view->assign('countries', $this->model_localisation_country->getCountries());

        $this->view->assign('back', $this->html->getSecureURL($cart_rt));

        $this->data['form']['back'] = $form->getFieldHtml(
            array(
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
            ));

        $this->data['form']['continue'] = $form->getFieldHtml(
            array(
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ));

        //fill error messages.
        foreach ($this->data['form']['fields'] as $section => $fields) {
            foreach ($fields as $key => $text) {
                $this->data['error_'.$key] = (string)$this->error[$key];
            }
        }

        //TODO: REMOVE THIS IN 2.0!!!
        // backward compatibility code
        $deprecated = $this->data['form']['fields'];
        foreach ($deprecated as $section => $fields) {
            foreach ($fields as $name => $fld) {
                if (in_array($name, array('country', 'zone'))) {
                    $name .= '_id';
                }
                $this->data['form'][$name] = $fld;
            }
        }
        //end of trick

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/checkout/guest_step_1.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    private function _validate($data)
    {
        if (!$this->csrftoken->isTokenValid()) {
            $this->error['warning'] = $this->language->get('error_unknown');
            return false;
        }
        $data = array_map('trim', $data);

        if ((mb_strlen($data['firstname']) < 3) || (mb_strlen($data['firstname']) > 32)) {
            $this->error['firstname'] = $this->language->get('error_firstname');
        }

        if ((mb_strlen($data['lastname']) < 3) || (mb_strlen($data['lastname']) > 32)) {
            $this->error['lastname'] = $this->language->get('error_lastname');
        }

        if (!preg_match(EMAIL_REGEX_PATTERN, $data['email'])) {
            $this->error['email'] = $this->language->get('error_email');
        }

        if (mb_strlen($data['telephone']) > 32) {
            $this->error['telephone'] = $this->language->get('error_telephone');
        }

        if ((mb_strlen($data['address_1']) < 3) || (mb_strlen($data['address_1']) > 128)) {
            $this->error['address_1'] = $this->language->get('error_address_1');
        }

        if ((mb_strlen($data['city']) < 3) || (mb_strlen($data['city']) > 128)) {
            $this->error['city'] = $this->language->get('error_city');
        }
        if ((mb_strlen($data['postcode']) < 3) || (mb_strlen($data['postcode']) > 10)) {
            $this->error['postcode'] = $this->language->get('error_postcode');
        }

        if ($data['country_id'] == 'FALSE') {
            $this->error['country'] = $this->language->get('error_country');
        }

        if ($data['zone_id'] == 'FALSE') {
            $this->error['zone'] = $this->language->get('error_zone');
        }

        if ($data['shipping_indicator']) {

            if ((mb_strlen($data['shipping_firstname']) < 3) || (mb_strlen($data['shipping_firstname']) > 32)) {
                $this->error['shipping_firstname'] = $this->language->get('error_firstname');
            }

            if ((mb_strlen($data['shipping_lastname']) < 3) || (mb_strlen($data['shipping_lastname']) > 32)) {
                $this->error['shipping_lastname'] = $this->language->get('error_lastname');
            }

            if ((mb_strlen($data['shipping_address_1']) < 3) || (mb_strlen($data['shipping_address_1']) > 128)) {
                $this->error['shipping_address_1'] = $this->language->get('error_address_1');
            }

            if ((mb_strlen($data['shipping_city']) < 3) || (mb_strlen($data['shipping_city']) > 128)) {
                $this->error['shipping_city'] = $this->language->get('error_city');
            }
            if ((mb_strlen($data['shipping_postcode']) < 3) || (mb_strlen($data['shipping_postcode']) > 10)) {
                $this->error['shipping_postcode'] = $this->language->get('error_postcode');
            }

            if ($data['shipping_country_id'] == 'FALSE') {
                $this->error['shipping_country'] = $this->language->get('error_country');
            }

            if ($data['shipping_zone_id'] == 'FALSE') {
                $this->error['shipping_zone'] = $this->language->get('error_zone');
            }

        }

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            $this->error['warning'] = $this->language->get('gen_data_entry_error');
            return false;
        }
    }

}
