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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesFormsManagerGroups extends AController
{
    public $error = [];
    /** @var ModelToolFormsManager */
    public $mdl;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->loadLanguage('forms_manager/forms_manager');
        $this->mdl = $this->loadModel('tool/forms_manager');
    }

    public function addGroup()
    {
        $this->language->load('forms_manager/forms_manager');
        $post = $this->request->post;
        $post['form_id'] = (int)$this->request->get['form_id'];
        $post['group_id'] = (int)$post['group_id'];

        if (!$post['form_id'] || !$this->validateGroupForm($post)) {
            $error = new AError('');
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                ['error_text' => $this->error]);
            return;
        }
        if ($post['group_id']) {
            $this->mdl->assignGroupToForm($post['form_id'], (int)$post['group_id']);
        } else {
            $this->mdl->addFieldGroup($post['form_id'], $post);
        }
        $this->response->setOutput($this->language->get('text_success_added_group'));
    }

    public function assignFieldToGroup()
    {
        $formId = (int)$this->request->get['form_id'];
        $fieldId = (int)$this->request->get['field_id'];
        $groupId = (int)$this->request->get['group_id'];

        if (!$formId || !$fieldId) {
            $error = new AError('');
            $error->toJSONResponse(
                'VALIDATION_ERROR_406',
                ['error_text' => ['Invalid parameters']]);
            return;
        }

        $this->mdl->assignFieldToGroup($fieldId, $groupId);
        $this->response->setOutput($this->language->get('text_success_field_assigned'));
    }

    protected function validateGroupForm($data)
    {
        if (!$this->user->hasPermission('modify', 'tool/forms_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$data['group_id']) {
            if (!$data['group_name']) {
                $this->error['error_required'] = $this->language->get('error_group_name_required');
            }
        }

        $this->extensions->hk_ValidateData($this);
        return (!$this->error);
    }
}
