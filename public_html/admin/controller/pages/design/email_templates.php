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
class ControllerPagesDesignEmailTemplates extends AController
{
    public $error = array();
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->buildHeader();

        $grid_settings = [
            'table_id'       => 'email_templates_grid',
            'url'            => $this->html->getSecureURL('listing_grid/email_templates'),
            'editurl'        => $this->html->getSecureURL('listing_grid/email_templates/update'),
            'update_field'   => $this->html->getSecureURL('listing_grid/email_templates/update_field'),
            'sortname'       => 'text_id',
            'sortorder'      => 'asc',
            'columns_search' => true,
            'actions'        => array(
                'edit'   => array(
                    'text' => $this->language->get('text_edit'),
                    'href' => $this->html->getSecureURL('design/email_templates/update', '&id=%ID%'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                ),
            ),
        ];

        $grid_settings['colNames'] = array(
            $this->language->get('column_text_id'),
            $this->language->get('column_language'),
            $this->language->get('column_status'),
            $this->language->get('column_subject'),
        );

        $grid_settings['colModel'] = array(
            array(
                'name'  => 'text_id',
                'index' => 'text_id',
                'width' => 150,
                'align' => 'left',
            ),
            array(
                'name'  => 'language',
                'index' => 'language',
                'width' => 100,
                'align' => 'left',
            ),
            array(
                'name'   => 'status',
                'index'  => 'status',
                'width'  => 100,
                'align'  => 'center',
                'search' => false,
            ),
            array(
                'name'  => 'subject',
                'index' => 'subject',
                'width' => 250,
                'align' => 'left',
            ),
        );

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

        $this->view->assign('insert', $this->html->getSecureURL('design/email_templates/insert'));
        $this->view->assign('help_url', $this->gen_help_url('email_templates'));

        $this->processTemplate('pages/design/email_templates_list.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

    }

    public function insert()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->buildHeader();

        $this->loadModel('design/email_template');
        $this->loadModel('localisation/language');

        if ($this->request->is_POST() && $this->validate($this->request->post)) {
            $data = $this->request->post;
            $data['store_id'] = (int)$this->config->get('config_store_id');
            $emailTemplate = $this->model_design_email_template->insert($data);
        }

        if ($this->session->data['success']) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if ($this->session->data['warning']) {
            $this->error['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!empty($this->error)) {
            $this->view->assign('error', $this->error);
        }

        if ($emailTemplate) {
            redirect($this->html->getSecureURL('design/email_templates/update', '&id='.$emailTemplate['id']));
        }

        $this->getForm();

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('email_templates'));
        $this->view->assign('list_url', $this->html->getSecureURL('design/email_templates'));
        $this->processTemplate('pages/design/email_templates_form.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function update()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->buildHeader();

        $this->loadModel('design/email_template');
        $this->loadModel('localisation/language');

        if ($this->request->is_POST()) {
            $emailTemplate = $this->model_design_email_template->getById((int)$this->request->get['id']);

            if ($emailTemplate && $this->validate($this->request->post)) {
                try {
                    $this->model_design_email_template->update((int)$this->request->get['id'], $this->request->post);
                    $this->session->data['success'] = $this->language->get('save_complete');
                    redirect($this->html->getSecureURL('design/email_templates/update', '&id='.$emailTemplate['id']));
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage());
                    $this->session->data['warning'] = $this->language->get('save_error');
                }
            }

            if (!$emailTemplate || !$this->validate($this->request->post)) {
                $this->session->data['warning'] = $this->language->get('save_error');
            }
        }

        if (!(int)$this->request->get['id']) {
            redirect($this->html->getSecureURL('design/email_templates'));
        }

        if ($this->session->data['success']) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        if ($this->session->data['warning']) {
            $this->error['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }

        if (!empty($this->error)) {
            $this->view->assign('error', $this->error);
        }

        $this->getForm();

        $this->view->batchAssign($this->data);
        $this->view->assign('help_url', $this->gen_help_url('email_templates'));
        $this->view->assign('list_url', $this->html->getSecureURL('design/email_templates'));
        $this->processTemplate('pages/design/email_templates_form.tpl');
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function getForm($args = [])
    {
        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('error_name', $this->error['name']);

        $this->view->assign('cancel', $this->html->getSecureURL('design/email_templates'));

        $languages = $this->model_localisation_language->getLanguages();
        $this->data['languages'] = [
            0 => '-- Please Select --',
        ];
        if ($languages) {
            foreach ($languages as $key => $language) {
                $this->data['languages'][$language['language_id']] = $language['name'];
            }
        }

        if ((int)$this->request->get['id']) {
            $emailTemplate = $this->model_design_email_template->getById((int)$this->request->get['id']);
            if ($emailTemplate['language_id'] != $this->language->getContentLanguageID() || $emailTemplate['store_id'] != $this->config->get('config_store_id')) {
                $existTemplate = $this->model_design_email_template->getByTextIdAndLanguageId($emailTemplate['text_id'], $this->language->getContentLanguageID());
                if (!$existTemplate) {
                    redirect($this->html->getSecureURL('design/email_templates/insert', '&text_id='.$emailTemplate['text_id']));
                    return;
                }
                redirect($this->html->getSecureURL('design/email_templates/update', '&id='.$existTemplate['id']));
                return;
            }
            if ($emailTemplate) {
                foreach ($emailTemplate as $key => $value) {
                    $this->data[$key] = $value;
                }
            }
        } elseif ($this->request->get['text_id']) {
            $this->data['text_id'] = $this->request->get['text_id'];
            $existTemplate = $this->model_design_email_template->getByTextIdAndLanguageId($this->request->get['text_id'], $this->language->getContentLanguageID());
            if ($existTemplate) {
                redirect($this->html->getSecureURL('design/email_templates/update', '&id='.$existTemplate['id']));
                return;
            }
            $templateDefaultLanguage = $this->model_design_email_template->getByTextIdAndLanguageId($this->request->get['text_id'], $this->language->getDefaultLanguageID());
            if ($templateDefaultLanguage) {
                foreach ($templateDefaultLanguage as $key => $value) {
                    if (in_array($key, ['id', 'language_id'])) {
                        continue;
                    }
                    $this->data[$key] = $value;
                }
            }
        }

        if ($this->request->is_POST()) {
            foreach ($this->request->post as $key => $value) {
                $this->data[$key] = $value;
            }
        }

        $form = new AForm ('ST');
        $form->setForm(['form_name' => 'emailTemplateFrm']);

        $this->data['form']['id'] = 'emailTemplateFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'emailTemplateFrm',
                'attr'   => 'data-confirm-exit="true" class="aform form-horizontal"',
                'action' => $this->data['action'],
            ]);

        $this->data['form']['submit'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'submit',
                'text'  => $this->language->get('button_save'),
                'style' => 'button1',
            ]);

        $this->data['form']['cancel'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'cancel',
                'text'  => $this->language->get('button_cancel'),
                'style' => 'button2',
            ]);

        $this->data['form']['fields']['status'] = $form->getFieldHtml(
            [
                'type'  => 'checkbox',
                'name'  => 'status',
                'value' => isset($this->data['status']) ? $this->data['status'] : 1,
                'style' => 'btn_switch',
            ]);

        $this->data['form']['fields']['text_id'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'text_id',
                'value'    => $this->data['text_id'],
                'required' => true,
                'attr'     => (int)$this->request->get['id'] ? 'disabled' : '',
            ]);

        $this->data['form']['fields']['language_id'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'language_id',
                'options'  => $this->data['languages'],
                'value'    => isset($this->data['language_id']) ? $this->data['language_id'] : $this->language->getContentLanguageID(),
                'required' => true,
                'attr'     => (int)$this->request->get['id'] ? 'disabled' : '',
            ]);

        $this->data['form']['fields']['headers'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'headers',
                'value' => $this->data['headers'],
            ]);

        $this->data['form']['fields']['subject'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'subject',
                'value'    => $this->data['subject'],
                'required' => true,
            ]);

        $this->data['form']['fields']['html_body'] = $form->getFieldHtml(
            [
                'type'     => 'texteditor',
                'name'     => 'html_body',
                'value'    => $this->data['html_body'],
                'required' => true,
            ]);

        $this->data['form']['fields']['text_body'] = $form->getFieldHtml(
            [
                'type'     => 'textarea',
                'name'     => 'text_body',
                'value'    => $this->data['text_body'],
                'attr'     => 'rows="16"',
                'required' => true,
            ]);
        $this->data['form']['fields']['allowed_placeholders'] = $form->getFieldHtml(
            [
                'type'  => 'textarea',
                'name'  => 'allowed_placeholders',
                'value' => $this->data['allowed_placeholders'],
            ]);

    }

    private function validate(array $data)
    {
        $this->loadModel('design/email_template');
        $this->loadLanguage('design/email_templates');

        if (isset($data['text_id'])) {
            if (strlen(trim($data['text_id'])) === 0 || strlen(trim($data['text_id'])) > 254 || preg_match('/(^[\\w\\d]+)$/i', $data['text_id']) === 0) {
                $this->error['text_id'] = $this->language->get('save_error_text_id');
            } else {
                if (!(int)$this->request->get['id'] && $this->model_design_email_template->getByTextIdAndLanguageId($data['text_id'], $data['language_id'])) {
                    $this->error['text_id'] = $this->language->get('save_error_text_id_unique');
                }
            }
        }

        if (isset($data['headers'])) {
            if (strlen(trim($data['headers'])) > 254) {
                $this->error['headers'] = $this->language->get('save_error_text_header');
            }
        }

        if (!isset($data['subject']) || strlen(trim($data['subject'])) === 0 || strlen(trim($data['subject'])) > 254) {
            $this->error['subject'] = $this->language->get('save_error_text_subject');
        }

        if (!isset($data['html_body']) || strlen(trim($data['html_body'])) === 0) {
            $this->error['html_body'] = $this->language->get('save_error_html_body');
        }

        if (!isset($data['text_body']) || strlen(trim($data['text_body'])) === 0) {
            $this->error['text_body'] = $this->language->get('save_error_text_body');
        }

        if (isset($data['language_id']) && (int)$data['language_id'] === 0) {
            $this->error['language_id'] = $this->language->get('save_error_language_id');
        }

        if (empty($this->error)) {
            return true;
        }
        return false;
    }

    private function buildHeader()
    {
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

        $this->document->initBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('index/home'),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ));
        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('design/email_templates'),
                'text'      => $this->language->get('heading_title'),
                'separator' => ' :: ',
                'current'   => true,
            ));

        $this->document->setTitle($this->language->get('heading_title'));
    }

}
