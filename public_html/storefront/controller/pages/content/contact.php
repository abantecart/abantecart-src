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

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesContentContact extends AController
{
    const formTxtId = 'ContactUsFrm';
    public $error = [];
    /**
     * @var AForm
     */
    public $form;

    public function main()
    {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->form = new AForm(self::formTxtId);
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->form->loadFromDb(self::formTxtId);
        $form = $this->form->getForm();
        $languageId = $this->language->getContentLanguageID() ?? $this->language->getLanguageID();

        if ($this->request->is_POST() && $this->validate()) {
            $post = $this->request->post;
            // move all uploaded files to their directories
            $filePaths = $this->form->processFileUploads($this->request->files);
            $subject = $this->config->get('store_name')
                . ' '
                . $this->language->getAndReplace(
                    key: 'email_subject',
                    replaces: strip_tags($post['first_name'])
                );

            $this->data['mail_template_data']['subject'] = $subject;

            $mailLogo = $this->config->get('config_mail_logo_' . $languageId)
                ?: $this->config->get('config_mail_logo')
                    ?: $this->config->get('config_logo_' . $languageId)
                        ?: $this->config->get('config_logo');

            if ($mailLogo) {
                $result = getMailLogoDetails($mailLogo);
                $this->data['mail_template_data']['logo_uri'] = $result['uri'];
                $this->data['mail_template_data']['logo_html'] = $result['html'];
            }

            $this->data['mail_template_data']['store_name'] = $this->config->get('store_name');
            $this->data['mail_template_data']['store_url'] = $this->config->get('config_url') . $this->config->get('seo_prefix');
            $this->data['mail_template_data']['text_project_label'] = htmlspecialchars_decode(project_base());
            $this->data['mail_template_data']['entry_enquiry'] = $this->data['mail_plain_text']
                = $this->language->get('entry_enquiry');
            $this->data['mail_plain_text'] .= "\r\n" . $post['enquiry'] . "\r\n";
            $this->data['mail_template_data']['enquiry'] = nl2br($post['enquiry'] . "\r\n");

            $form_fields = $this->form->getFields();
            $this->data['mail_template_data']['form_fields'] = [];
            foreach ($form_fields as $elmName => $fieldInfo) {
                if (!$fieldInfo['status']
                    //skip captcha
                    || in_array($fieldInfo['element_type'], ['J', 'K'])
                    || $elmName == 'enquiry'
                    || !isset($post[$elmName])
                ) {
                    continue;
                }

                //country
                if ($fieldInfo['element_type'] == 'O') {
                    /** @var ModelLocalisationCountry $mdl */
                    $mdl = $this->load->model('localisation/country');
                    $country = $mdl->getCountry((int)$post[$elmName]);
                    $post[$elmName] = $country['name'];
                } elseif ($fieldInfo['element_type'] == 'Z') {
                    /** @var ModelLocalisationZone $mdl */
                    $mdl = $this->load->model('localisation/zone');
                    $zone = $mdl->getZone((int)$post[$elmName]);
                    $post[$elmName] = $zone['name'];
                }

                $fieldValue = implode("; ", (array)$post[$elmName]);
                $fieldTitle = rtrim($fieldInfo['title'], ':');

                $this->data['mail_plain_text'] .= "\r\n"
                    . $fieldTitle
                    . ":\t"
                    . $fieldValue;
                $this->data['mail_template_data']['form_fields'][$fieldTitle] = $fieldValue;
                $this->data['mail_template_data']['tpl_form_fields'][] = [
                    'name'  => $fieldTitle,
                    'value' => $fieldValue,
                ];
            }
            $this->data['mail_template_data']['first_name'] = strip_tags($post['first_name']);

            $mail = new AMail($this->config);
            if ($filePaths) {
                $this->data['mail_plain_text'] .= "\r\n" . $this->language->get('entry_attached') . ": \r\n";
                foreach ($filePaths as $file_info) {
                    $basename = pathinfo(str_replace(' ', '_', $file_info['path']), PATHINFO_BASENAME);
                    $size = " (" . round(filesize($file_info['path']) / 1024, 2) . "Kb)";
                    $this->data['mail_plain_text'] .= "\t"
                        . $file_info['display_name']
                        . ': '
                        . $basename . $size . "\r\n";
                    $mail->addAttachment($file_info['path'], $basename);
                    $this->data['mail_template_data']['form_fields'][$file_info['display_name']] = $basename . $size;
                }
            }

            $this->data['mail_template'] = 'mail/contact.tpl';

            //allow to change email data from extensions
            $this->extensions->hk_ProcessData($this, 'sf_contact_us_mail');

            $textBody = strip_tags(html_entity_decode($this->data['mail_plain_text'], ENT_QUOTES, 'UTF-8'));
            if ($this->config->get('config_duplicate_contact_us_to_message')) {
                $this->messages->saveNotice(
                    $this->language->getAndReplace(
                        key: 'entry_duplicate_message_subject',
                        replaces: [$post['first_name'], $post['email']]
                    ),
                    $textBody,
                    false
                );
            }

            $view = new AView($this->registry, 0);
            $view->batchAssign($this->data['mail_template_data']);
            $attachment = [];
            if ($post['first_name']) {
                $mail->setTo($this->config->get('store_main_email'));
                $mail->setFrom($this->config->get('store_main_email'));
                $mail->setReplyTo($post['email']);
                $mail->setSender($post['first_name']);
                $mail->setTemplate('storefront_contact_us_mail', $this->data['mail_template_data']);
                if (is_file(DIR_RESOURCE . str_replace('/', DS, $mailLogo))) {
                    $attachment = [
                        'file' => DIR_RESOURCE . str_replace('/', DS, $mailLogo),
                        'name' => md5(pathinfo($mailLogo, PATHINFO_FILENAME))
                            . '.'
                            . pathinfo($mailLogo, PATHINFO_EXTENSION),
                    ];
                    $mail->addAttachment($attachment['file'], $attachment['name']);
                }
                $mail->send();
            } else {
                $this->messages->saveError("Contact form Error", 'Sender name is empty. Please check form settings!');
            }

            //notify admin
            $this->loadLanguage('common/im');
            $messageArr = [
                1 => [
                    'message' => $this->language->getAndReplace(
                        'im_customer_contact_admin_text',
                        replaces: [$post['email'], $post['first_name']]
                    ),
                ],
            ];
            $this->im->send(
                'customer_contact',
                $messageArr,
                'storefront_contact_us_mail_admin_notify',
                $post,
                $attachment ? [$attachment] : []
            );

            $this->extensions->hk_ProcessData($this);
            redirect(
                $this->html->getSecureURL(
                    $form['success_page'] ?: 'content/contact/success'
                )
            );
        }

        if ($this->request->is_POST()) {
            $this->form->batchAssign($this->request->post);
        } else {
            if ($this->customer->isLogged()) {
                $this->form->assign(
                    'first_name',
                    $this->session->data['guest']['payment_firstname'] ?: $this->customer->getFirstName()
                );
                $this->form->assign(
                    'email',
                    $this->session->data['guest']['email'] ?: $this->customer->getEmail()
                );
            }
            if ($this->request->get['product_name']) {
                $this->form->assign(
                    'enquiry',
                    $this->request->get['product_name'] . ' (#' . $this->request->get['product_id'] . ')' . "\n\n"
                );
            }
        }

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
                'href'      => $this->html->getURL('content/contact'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );
        //if no fields - show nothing
        if ($this->form->getFields()) {
            $this->view->assign('form_output', $this->form->getFormHtml());
        }

        $this->view->assign('action', $this->html->getURL('content/contact'));
        $this->view->assign('store', $this->config->get('store_name'));

        $addressData = [];
        if ($this->config->get('config_address')) {
            $addressData['address_1'] = nl2br($this->config->get('config_address'));
        }
        if ($this->config->get('config_postcode')) {
            $addressData['postcode'] = $this->config->get('config_postcode');
        }
        if ($this->config->get('config_city')) {
            $addressData['city'] = $this->config->get('config_city');
        }
        if ($this->config->get('config_zone_id')) {
            $this->loadModel('localisation/zone');
            $zone = $this->model_localisation_zone->getZone($this->config->get('config_zone_id'));
            if ($zone) {
                $addressData['zone'] = $zone['name'];
            }
        }
        $addressFormat = '';
        if ($this->config->get('config_country_id')) {
            /** @var ModelLocalisationCountry $mdl */
            $mdl = $this->loadModel('localisation/country');
            $country = $mdl->getCountry($this->config->get('config_country_id'));
            if ($country) {
                $addressData['country'] = $country['name'];
                $addressFormat = $country['address_format'];
            }
        }

        $address = $this->customer->getFormattedAddress($addressData, $addressFormat);
        $this->view->assign('address_data', $addressData);
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
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );
        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getURL('content/contact'),
                'text'      => $this->language->get('heading_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        if ($this->config->get('embed_mode')) {
            $continueUrl = $this->html->getNonSecureURL('product/category');
        } else {
            $continueUrl = $this->html->getHomeURL();
        }

        $this->view->assign('continue', $continueUrl);
        $this->view->assign(
            'continue_button',
            $this->html->buildElement(
                [
                    'type'  => 'button',
                    'name'  => 'continue_button',
                    'text'  => $this->language->get('button_continue'),
                    'style' => 'button',
                ]
            )
        );

        if ($this->config->get('embed_mode')) {
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
     * @throws AException
     */
    protected function validate()
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
