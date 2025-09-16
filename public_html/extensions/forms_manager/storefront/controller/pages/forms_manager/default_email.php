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
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesFormsManagerDefaultEmail extends AController
{
    public function main()
    {
        /** @var ModelToolFormsManager $mdl */
        $mdl = $this->loadModel('tool/forms_manager');
        $this->loadLanguage('forms_manager/forms_manager');
        $this->loadLanguage('forms_manager/default_email');

        if ($this->request->is_POST()) {
            $path = $_SERVER['HTTP_REFERER'];

            if (!isset($this->request->get['form_id'])) {
                redirect($path);
                exit;
            }

            $formId = (int)$this->request->get['form_id'];
            $form_data = $mdl->getForm($formId);
            $form = new AForm($form_data['form_name']);
            $form->loadFromDb($form_data['form_name']);
            $errors = $form->validateFormData($this->request->post);

            if ($errors) {
                //save error and data to session
                $this->session->data['custom_form_' . $formId] = $this->request->post;
                $this->session->data['custom_form_' . $formId]['errors'] = $errors;
                redirect($path);
            } else {
                $mailer = new AMail($this->config);
                $mailer->setTo($this->config->get('store_main_email'));

                $senderEmail = $this->request->post['email']
                    ?: $this->config->get('forms_manager_default_sender_email')
                    ?: $this->config->get('store_main_email');
                unset($this->request->post['email']);
                $mailer->setFrom($senderEmail);

                $senderName = $this->request->post['first_name']
                    ?: $this->config->get('forms_manager_default_sender_name')
                    ?: $this->config->get('store_name');
                unset($this->request->post['first_name']);
                $mailer->setSender($senderName);

                $subject = $this->request->post['email_subject'] ?? $form_data['form_name'];
                unset($this->request->post['email_subject']);
                $mailer->setSubject($subject);

                $msg = $this->config->get('store_name') . PHP_EOL
                    . $this->config->get('config_url')
                    . $this->config->get('seo_prefix') . PHP_EOL;

                $this->data['mail_template_data']['tpl_form_fields'] = [];
                $fields = $mdl->getFields($formId);
                foreach ($fields as $field) {
                    // skip files and captcha
                    if (in_array($field['element_type'], ['K', 'J', 'U'])) {
                        continue;
                    }

                    if (isset($this->request->post[$field['field_name']])) {
                        $val = $this->request->post[$field['field_name']];
                        $val = $this->_prepareValue($val);
                        //for zones
                        $msg .= $field['name'] . ': ' . $val . PHP_EOL;
                        if ($field['element_type'] == 'Z') {
                            $val = $this->request->post[$field['field_name'] . '_zones'];
                            $val = $this->_prepareValue($val);
                            $msg .= "\t" . $val . PHP_EOL;
                        }
                        $this->data['mail_template_data']['tpl_form_fields'][$field['name']] = $val;
                    }
                }

                // add attachments
                $file_paths = $form->processFileUploads($this->request->files);
                if ($file_paths) {
                    $msg .= PHP_EOL . $this->language->get('entry_attached') . ": " . PHP_EOL;
                    $this->data['mail_template_data']['tpl_form_fields'][$this->language->get('entry_attached')] = '';
                    foreach ($file_paths as $file_info) {
                        $basename = pathinfo(str_replace(' ', '_', $file_info['path']), PATHINFO_BASENAME);
                        $this->data['mail_template_data']['tpl_form_fields'][$this->language->get('entry_attached')] .=
                        $msg
                            .= "\t" . $file_info['display_name'] . ': ' . $basename
                            . " (" . round(filesize($file_info['path']) / 1024, 2) . "Kb)" . PHP_EOL;
                        $mailer->addAttachment($file_info['path'], $basename);
                    }
                }

                //$mailer->setTemplate('storefront_contact_us_mail', $this->data['mail_template_data'] );
                $mailer->setText(strip_tags(html_entity_decode($msg, ENT_QUOTES, 'UTF-8')));
                $mailer->send();

                if (!$mailer->error) {
                    $rt = $form_data['success_page'] ?: 'forms_manager/default_email/success';
                    $successUrl = $this->html->getSecureURL($rt);
                    //clear form session
                    unset($this->session->data['custom_form_' . $formId]);
                } else {
                    $this->session->data['warning'] = $mailer->error;
                    $successUrl = $this->html->getSecureURL('forms_manager/default_email', '&form_id=' . $formId);
                }
                redirect($successUrl);
            }
            exit;
        }

        $this->data['warning'] = $this->session->data['warning'];
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }

        $this->document->setTitle($this->language->get('text_default_email_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getURL('forms_manager/default_email'),
                'text'      => $this->language->get('text_default_email_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['continue'] = $_SERVER['HTTP_REFERER'];
        $continue = HtmlElementFactory::create(
            [
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
                'icon'  => 'icon-arrow-right',
            ]
        );
        $this->data['continue_button'] = $continue;

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/default_email.tpl');
    }

    protected function _prepareValue($val)
    {
        $str = '';
        if (is_array($val)) {
            if (sizeof($val) > 1) {
                $str = PHP_EOL;
            }
            foreach ($val as $k => $v) {
                $str .= "\t" . $k . ': ' . $v . PHP_EOL;
            }
            $val = $str;
        }
        return $val;
    }

    public function success()
    {
        $this->loadLanguage('forms_manager/default_email');

        $this->data['warning'] = $this->session->data['warning'];
        if (isset($this->session->data['warning'])) {
            unset($this->session->data['warning']);
        }

        $this->document->setTitle($this->language->get('text_default_email_title'));
        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getURL('forms_manager/default_email/success'),
                'text'      => $this->language->get('text_default_email_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->data['continue'] = $this->html->getURL('index/home');
        $continue = HtmlElementFactory::create(
            [
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
                'icon'  => 'icon-arrow-right',
            ]
        );
        $this->data['continue_button'] = $continue;

        $this->view->batchAssign($this->data);
        $this->processTemplate('pages/default_email_success.tpl');
    }
}