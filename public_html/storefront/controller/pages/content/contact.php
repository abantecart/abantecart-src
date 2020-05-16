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

class ControllerPagesContentContact extends AController
{
    public $data = array();
    public $error = array();
    /**
     * @var AForm
     */
    private $form;

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->document->setTitle($this->language->get('heading_title'));
        $this->form = new AForm('ContactUsFrm');
        $this->form->loadFromDb('ContactUsFrm');
        $form = $this->form->getForm();

        if ($this->request->is_POST() && $this->_validate()) {
            $post_data = $this->request->post;
            // move all uploaded files to their directories
            $file_paths = $this->form->processFileUploads($this->request->files);
            $subject = $this->config->get('store_name').' '.sprintf($this->language->get('email_subject'), strip_tags($post_data['first_name']));
            $this->data['mail_template_data']['subject'] = $subject;
            $config_mail_logo = $this->config->get('config_mail_logo');
            $config_mail_logo = !$config_mail_logo ? $this->config->get('config_logo') : $config_mail_logo;
            if ($config_mail_logo) {
                if (is_numeric($config_mail_logo)) {
                    $r = new AResource('image');
                    $resource_info = $r->getResource($config_mail_logo);
                    if ($resource_info) {
                        $this->data['mail_template_data']['logo_html'] = html_entity_decode(
                            $resource_info['resource_code'],
                            ENT_QUOTES, 'UTF-8'
                        );
                    }
                } else {
                    $store_logo = md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                        .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION);
                    $this->data['mail_template_data']['logo_uri'] = 'cid:'.$store_logo;
                }
            }
            //backward compatibility. TODO: remove this in 2.0
            if ($this->data['mail_template_data']['logo_uri']) {
                $this->data['mail_template_data']['logo'] = $this->data['mail_template_data']['logo_uri'];
            } else {
                $this->data['mail_template_data']['logo'] = $config_mail_logo;
            }

            $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
            $this->data['mail_template_data']['store_url'] = $this->config->get('config_url');
            $this->data['mail_template_data']['text_project_label'] = htmlspecialchars_decode(project_base());
            $this->data['mail_template_data']['entry_enquiry'] = $this->data['mail_plain_text'] = $this->language->get('entry_enquiry');
            $this->data['mail_plain_text'] .= "\r\n".$post_data['enquiry']."\r\n";
            $this->data['mail_template_data']['enquiry'] = nl2br($post_data['enquiry']."\r\n");

            $form_fields = $this->form->getFields();
            $this->data['mail_template_data']['form_fields'] = array();
            foreach ($form_fields as $field_name => $field_info) {
                if (has_value($post_data[$field_name]) && !in_array($field_name, array('enquiry', 'captcha'))) {
                    $field_value = $post_data[$field_name];
                    if(is_array($field_value)){
                        $field_value = implode("; ",$field_value);;
                    }
                    $field_details = $this->form->getField($field_name);
                    $this->data['mail_plain_text'] .= "\r\n".rtrim($field_details['name'], ':').":\t".$field_value;
                    $this->data['mail_template_data']['form_fields'][rtrim($field_details['name'], ':')] = $field_value;
                    $this->data['mail_template_data']['tpl_form_fields'][] = [
                        'name' => rtrim($field_details['name'], ':'),
                        'value' => $field_value
                    ];
                }
            }
            $this->data['mail_template_data']['first_name'] = strip_tags($post_data['first_name']);

            $mail = new AMail($this->config);
            if ($file_paths) {
                $this->data['mail_plain_text'] .= "\r\n".$this->language->get('entry_attached').": \r\n";
                foreach ($file_paths as $file_info) {
                    $basename = pathinfo(str_replace(' ', '_', $file_info['path']), PATHINFO_BASENAME);
                    $this->data['mail_plain_text'] .= "\t".$file_info['display_name'].': '.$basename." (".round(filesize($file_info['path']) / 1024, 2)."Kb)\r\n";
                    $mail->addAttachment($file_info['path'], $basename);
                    $this->data['mail_template_data']['form_fields'][$file_info['display_name']] = $basename." (".round(filesize($file_info['path']) / 1024, 2)."Kb)";
                }
            }

            $this->data['mail_template'] = 'mail/contact.tpl';

            //allow to change email data from extensions
            $this->extensions->hk_ProcessData($this, 'sf_contact_us_mail');

            $text_body = strip_tags(html_entity_decode($this->data['mail_plain_text'], ENT_QUOTES, 'UTF-8'));
            if ($this->config->get('config_duplicate_contact_us_to_message')) {
                $this->messages->saveNotice(
                    sprintf($this->language->get('entry_duplicate_message_subject'), $post_data['first_name'], $post_data['email']),
                    $text_body,
                    false
                );
            }

            $view = new AView($this->registry, 0);
            $view->batchAssign($this->data['mail_template_data']);
            $html_body = $view->fetch($this->data['mail_template']);

            $mail->setTo($this->config->get('store_main_email'));
            $mail->setFrom($this->config->get('store_main_email'));
            $mail->setReplyTo($post_data['email']);
            $mail->setSender($post_data['first_name']);
            $mail->setTemplate('storefront_contact_us_mail', $this->data['mail_template_data']);
            if (is_file(DIR_RESOURCE.$config_mail_logo)) {
                $mail->addAttachment(DIR_RESOURCE.$config_mail_logo,
                    md5(pathinfo($config_mail_logo, PATHINFO_FILENAME))
                    .'.'.pathinfo($config_mail_logo, PATHINFO_EXTENSION));
            }
            $mail->send();

            //get success_page
            if ($form['success_page']) {
                $success_url = $this->html->getSecureURL($form['success_page']);
            } else {
                $success_url = $this->html->getSecureURL('content/contact/success');
            }

            //notify admin
            $this->loadLanguage('common/im');
            $message_arr = array(
                1 => array(
                    'message' => sprintf(
                        $this->language->get('im_customer_contact_admin_text'),
                        $post_data['email'],
                        $post_data['first_name']
                    ),
                ),
            );
            $this->im->send('customer_contact', $message_arr, 'storefront_contact_us_mail_admin_notify', $post_data);

            $this->extensions->hk_ProcessData($this);
            redirect($success_url);
        }

        if ($this->request->is_POST()) {
            foreach ($this->request->post as $name => $value) {
                $this->form->assign($name, $value);
            }
        }

        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ));
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getURL('content/contact'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ));
        //if no fields - show nothing
        if ($this->form->getFields()) {
            $this->view->assign('form_output', $this->form->getFormHtml());
        }

        $this->view->assign('action', $this->html->getURL('content/contact'));
        $this->view->assign('store', $this->config->get('store_name'));

        $address_data = array();
        if($this->config->get('config_address')){
            $address_data['address_1'] = nl2br($this->config->get('config_address'));
        }
        if($this->config->get('config_postcode')){
            $address_data['postcode'] = $this->config->get('config_postcode');
        }
        if($this->config->get('config_city')){
            $address_data['city'] = $this->config->get('config_city');
        }
        if($this->config->get('config_zone_id')){
            $this->loadModel( 'localisation/zone' );
            $zone = $this->model_localisation_zone->getZone( $this->config->get('config_zone_id') );
            if($zone) {
                $address_data['zone'] = $zone['name'];
            }
        }
        $address_format = '';
        if($this->config->get('config_country_id')){
            $this->loadModel( 'localisation/country' );
            $country = $this->model_localisation_country->getCountry( $this->config->get('config_country_id') );
            if($country) {
                $address_data['country'] = $country['name'];
                $address_format = $country['address_format'];
            }
        }

        $address = $this->customer->getFormattedAddress( $address_data , $address_format);

        $this->view->assign('address_data', $address_data);
        $this->view->assign('address', $address);
        $this->view->assign('telephone', $this->config->get('config_telephone'));
        $this->view->assign('fax', $this->config->get('config_fax'));

        $this->processTemplate('pages/content/contact.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function success()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
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
                'href'      => $this->html->getURL('content/contact'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ));

        if ($this->config->get('embed_mode') == true) {
            $continue_url = $this->html->getNonSecureURL('product/category');
        } else {
            $continue_url = $this->html->getHomeURL();
        }

        $this->view->assign('continue', $continue_url);

        $continue = $this->html->buildElement(
            array(
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
            ));
        $this->view->assign('continue_button', $continue);

        if ($this->config->get('embed_mode') == true) {
            //load special headers
            $this->addChild('responses/embed/head', 'head');
            $this->addChild('responses/embed/footer', 'footer');
            $this->processTemplate('embed/common/success.tpl');
        } else {
            $this->processTemplate('common/success.tpl');
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * @return bool
     */
    private function _validate()
    {
        $this->error = array_merge($this->form->validateFormData($this->request->post), $this->error);

        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            $this->form->setErrors($this->error);
            return false;
        }
    }
}
