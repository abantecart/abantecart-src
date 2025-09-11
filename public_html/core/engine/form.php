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

use ReCaptcha\ReCaptcha;

/**
 * Load form data, render output
 *
 * Class AForm
 *
 * @property ALayout $layout
 * @property ACache $cache
 * @property ADB $db
 * @property AConfig $config
 * @property AHtml $html
 * @property ASession $session
 * @property ARequest $request
 * @property ALoader $load
 * @property ALanguageManager $language
 *
 */
class AForm
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var array $form - form data array
     * Array (
     *      [form_id]
     *      [form_name]
     *      [controller]
     *      [success_page]
     *      [status]
     *      [description] )
     */
    protected $form;
    /**
     * @var array $fields - array of form fields
     * Array
     *   (
     *       [field_id]
     *       [form_id]
     *       [field_name]
     *       [element_type]
     *       [sort_order]
     *       [required]
     *       [status]
     *       [name]
     *       [description]
     *       [value]
     *   )
     */
    protected $fields;
    /**
     * @var $groups - fields groups
     */
    protected $groups;
    /** @var $page_id - current page id */
    public $page_id;
    /** @var $errors - field_name -=> error */
    protected $errors;
    /** @var $form_edit_action - ( ST = standard,  HS = highlight save,  HT = highlight ) */
    protected $form_edit_action;

    /** @param string $form_edit_action ( ST = standard,  HS = highlight save,  HT = highlight ) */
    public function __construct(string $form_edit_action = '')
    {
        $this->registry = Registry::getInstance();
        if ($this->layout) {
            $this->page_id = $this->layout->page_id;
        }
        $this->errors = [];
        $this->form_edit_action = $form_edit_action;
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @param array|null $errors - array of validation errors - field_name -=> error
     *
     * @void
     */
    public function setErrors(?array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Sets the form and loads the data for the form from the database
     *
     * @param string $name
     *
     * @throws AException
     */
    public function loadFromDb($name)
    {
        $this->_loadForm($name);
        // if no form return
        if (empty($this->form)) {
            return;
        }

        $this->_loadFields();
        // if no fields no need to get groups
        if (empty($this->fields)) {
            return;
        }

        $this->_loadGroups();
    }

    /**
     * load form data into this->form variable
     *
     * @param string $name - unique form name
     *
     * @throws AException
     */
    protected function _loadForm($name)
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'forms.' . $name;
        $cache_key = preg_replace('/[^a-zA-Z0-9.]/', '', $cache_key)
            . '.store_' . $store_id
            . '_lang_' . $language_id;
        $form = $this->cache->pull($cache_key);
        if ($form !== false) {
            $this->form = $form;
            return;
        }

        $query = $this->db->query(
            "SELECT f.*, fd.description
            FROM " . $this->db->table("forms") . " f
            LEFT JOIN " . $this->db->table("form_descriptions") . " fd
                ON ( f.form_id = fd.form_id AND fd.language_id = '" . $language_id . "' )
            WHERE f.form_name = '" . $this->db->escape($name) . "'
                    AND f.status = 1 "
        );

        if (!$query->num_rows) {
            $err = new AError('NOT EXIST Form with name ' . $name);
            $err->toDebug()->toLog();
            return;
        }
        $this->cache->push($cache_key, $query->row);
        $this->form = $query->row;
    }

    /**
     * load form fields data into this->fields variable
     *
     * @return void
     * @throws AException
     */
    protected function _loadFields(array $data = [])
    {
        $languageId = (int)$data['language_id'] ?: (int)$this->config->get('storefront_language_id');
        $storeId = (int)($data['store_id'] ?? (int)$this->config->get('config_store_id'));
        $cache_key = 'forms.' . $this->form['form_name'] . '.fields';
        $cache_key = preg_replace('/[^a-zA-Z0-9.]/', '', $cache_key)
            . '.store_' . $storeId
            . '_lang_' . $languageId;
        $fields = $this->cache->pull($cache_key);
        if ($fields !== false) {
            $this->fields = $fields;
            return;
        }

        $fieldsResult = $this->db->query(
            "SELECT f.*, 
                fd.name, 
                fd.description, 
                fd.error_text, 
                fg.group_id, 
                fg2f.sort_order as group_sort_order, 
                fg.group_txt_id, 
                fgd.name as group_name,  
                fgd.description as group_description  
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_descriptions") . " fd
                ON ( f.field_id = fd.field_id AND fd.language_id = '" . $languageId . "' )
            LEFT JOIN " . $this->db->table("field_groups") . " fg
                ON f.group_id = fg.group_id
            LEFT JOIN " . $this->db->table("field_group_descriptions") . " fgd
                ON (fg.group_id = fgd.group_id AND fgd.language_id = '" . $languageId . "' )
            LEFT JOIN " . $this->db->table("field_group_to_form") . " fg2f
                ON fg2f.group_id = fg.group_id AND fg2f.form_id = '" . (int)$this->form['form_id'] . "'    
            WHERE f.form_id = '" . (int)$this->form['form_id'] . "'
                AND f.status = 1
            ORDER BY f.sort_order"
        );
        $this->fields = [];
        foreach ($fieldsResult->rows as $row) {
            $row['group_txt_id'] = $row['group_txt_id'] ?: 'general';
            $fieldId = (int)$row['field_id'];
            $row['settings'] = $row['settings'] ? unserialize($row['settings']) : [];
            $this->fields[$fieldId] = $row;
            $valuesResult = $this->db->query(
                "SELECT *
                FROM " . $this->db->table("field_values") . " 
                WHERE field_id = '" . $fieldId . "'
                AND language_id = '" . $languageId . "'"
            );
            if ($valuesResult->num_rows) {
                $values = unserialize($valuesResult->row['value']);
                if (is_array($values)) {
                    usort($values, self::_sort_by_sort_order(...));
                    foreach ($values as $value) {
                        $this->fields[$fieldId]['options'][$value['name']] = $value['name'];
                    }
                    $this->fields[$fieldId]['value'] = $values[0]['name'];
                }
            }
        }
        $this->cache->push($cache_key, $this->fields);
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    protected function _sort_by_sort_order($a, $b)
    {
        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }
        return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
    }

    /**
     * load form fields groups data into this->groups variable
     *
     * @return void
     * @throws AException
     */
    protected function _loadGroups()
    {
        $language_id = (int)$this->config->get('storefront_language_id');
        $store_id = (int)$this->config->get('config_store_id');
        $cache_key = 'forms.' . $this->form['form_name'] . '.groups';
        $cache_key = preg_replace('/[^a-zA-Z0-9.]/', '', $cache_key)
            . '.store_' . $store_id
            . '_lang_' . $language_id;
        $groups = $this->cache->pull($cache_key);
        if ($groups !== false) {
            $this->groups = $groups;
            return null;
        }

        $query = $this->db->query(
            "SELECT f.field_id,
                    f.field_name,
                    f.group_id, 
                    COALESCE(fg.group_txt_id,'general') as group_txt_id, 
                    fgd.name, 
                    fgd.description
            FROM " . $this->db->table("fields") . " f
            LEFT JOIN " . $this->db->table("field_groups") . " fg 
                ON ( f.group_id = fg.group_id)
            LEFT JOIN " . $this->db->table("field_group_to_form") . " fg2f 
                ON ( fg2f.group_id = fg.group_id AND fg2f.form_id = " . (int)$this->form['form_id'] . ")
            LEFT JOIN " . $this->db->table("field_group_descriptions") . " fgd
                ON ( fg.group_id = fgd.group_id AND fgd.language_id = '" . $language_id . "' )
            WHERE f.form_id = " . (int)$this->form['form_id'] . "
                AND f.status = 1
            ORDER BY fg2f.sort_order, f.sort_order"
        );
        $this->groups = [];
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                if (empty($this->groups[(int)$row['group_id']])) {
                    $this->groups[(int)$row['group_id']] = $row;
                }
                $this->groups[(int)$row['group_id']]['fields'][] = $row['field_name'];
            }
        }

        $this->cache->push($cache_key, $this->groups);
    }

    /**
     * return form data
     *
     * @return array
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * set form data
     *
     * @param array $form - [ 'form_name' => 'YOUR-FORM-TEXT-ID', 'update'    => URL-FOR-INLINE-UPDATE ]
     *
     * @return void
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * get form fields array
     *
     * @return array of fields => value
     */
    public function getFields()
    {
        $fields = [];

        foreach ($this->fields as $field) {
            $fields[$field['field_name']] = [
                'status'       => $field['status'],
                'value'        => $field['value'],
                'required'     => $field['required'] == 'Y',
                'element_type' => $field['element_type'],
                'title'        => $field['name'],
                'id'           => $field['field_id'],
            ];
        }

        return $fields;
    }

    /**
     * Get given field, type, values and selected/default
     *
     * @param string $fieldName
     *
     * @return array with field data
     * @throws AException
     */
    public function getField($fieldName)
    {
        foreach ($this->fields as $field) {
            if ($field['field_name'] == $fieldName) {
                return [
                    'field_name'   => $field['field_name'],
                    'element_type' => $field['element_type'],
                    'required'     => $field['required'] == 'Y',
                    'name'         => $field['name'],
                    'value'        => $field['value'],
                    'settings'     => $field['settings'],
                    'description'  => $field['description'],
                ];
            }
        }

        $err = new AError('NOT EXIST Form field with name ' . $fieldName);
        $err->toDebug()->toLog();
        return null;
    }

    /**
     * assign value(s) to given field name
     *
     * @param string $fieldName
     * @param string $value
     *
     * @return void
     */
    public function assign($fieldName, $value = '')
    {
        foreach ($this->fields as $key => $field) {
            if ($field['field_name'] == $fieldName) {
                $this->fields[$key]['value'] = $value;
                break;
            }
        }
    }

    /**
     * assign array of field with values.
     *
     * @param array $values - array of field name -> value
     *
     * @void
     */
    public function batchAssign($values)
    {
        foreach ($values as $name => $value) {
            $this->assign($name, $value);
        }
    }

    /**
     * load values to select, multiselect, checkbox group etc
     *
     * @param string $fieldName
     * @param array $values
     *
     * @void
     */
    public function loadFieldOptions($fieldName, $values)
    {
        foreach ($this->fields as $key => $field) {
            if ($field['field_name'] == $fieldName) {
                $this->fields[$key]['options'] = $values;
                break;
            }
        }
    }

    /**
     * return field html
     *
     * @param array $data - array with field data
     *
     * @return object  - AHtml form element
     * @throws AException
     */
    public function getFieldHtml($data)
    {
        $data['form'] = $this->form['form_name'] ?? '';

        if ($data['type'] == 'form') {
            $data['javascript'] = $this->addFormJs();
        }

        return HtmlElementFactory::create($data);
    }

    /**
     * Render the form elements only
     *
     * @return string html
     * @throws AException
     */
    public function loadExtendedFields()
    {
        return $this->getFormHtml(true);
    }

    /**
     * add javascript to implement form behaviour based on form_edit_action parameter
     *
     * @return string
     * @throws AException
     */
    protected function addFormJs()
    {
        /** @var ALanguageManager $language */
        $language = $this->registry->get('language');
        $view = new AView($this->registry, 0);

        switch ($this->form_edit_action) {
            case 'ST': //standards
                $view->batchAssign(
                    [
                        'id' => $this->form['form_name'] ?: $this->form['id'],
                    ]
                );
                $output = $view->fetch('form/form_js_st.tpl');
                break;
            case 'HS': //highlight on change and show save button
                $view->batchAssign(
                    [
                        'id'              => $this->form['form_name'] ?: $this->form['id'],
                        'button_save'     => $language->get('button_save'),
                        'button_reset'    => $language->get('button_reset'),
                        'update'          => $this->form['update'] ?? '',
                        'text_processing' => $language->get('text_processing'),
                        'text_saved'      => $language->get('text_saved'),
                    ]
                );
                $output = $view->fetch('form/form_js_hs.tpl');
                break;
            case 'HT': //highlight on change
                $view->batchAssign(
                    [
                        'id' => $this->form['form_name'],
                    ]
                );
                $output = $view->fetch('form/form_js_ht.tpl');
                break;
            default:
                $output = '';
        }

        return $output;
    }

    /**
     * Process and render the form with HTML output.
     *
     * @param bool $fieldsOnly
     *
     * @return string html
     * @throws AException
     */
    public function getFormHtml($fieldsOnly = false)
    {
        // if no form was loaded return empty string
        if (empty($this->form)) {
            return '';
        }

        $fields_html = [];
        $view = new AView($this->registry, 0);

        $containFiles = false;
        $formElements = $this->getFormElements();
        foreach ($formElements as $group => $elements) {
            foreach ($elements as $fName => $elm) {
                if ($elm->type == 'file') {
                    $containFiles = true;
                }

                switch ($elm->type) {
                    case 'IPaddress' :
                    case 'hidden' :
                        $fields_html[$group][$fName] = $elm->getHtml();
                        break;
                    default:
                        $view->batchAssign(
                            [
                                'element_id'  => $elm->element_id,
                                'type'        => $elm->type,
                                'title'       => $elm->title ?: $elm->display_name,
                                'description' => (string)$elm->description,
                                'error'       => (string)$this->errors[$elm->name],
                                'item_html'   => $elm->getHtml(),
                            ]
                        );
                        $fields_html[$group][$fName] = $view->fetch('form/form_field.tpl');
                }
            }
        }

        $output = '';
        if ($this->groups) {
            foreach ($this->groups as $group) {
                $view->batchAssign(
                    [
                        'group'       => $group,
                        'fields_html' => $fields_html[$group['group_txt_id']],
                    ]
                );
                $output .= $view->fetch('form/form_group.tpl');
            }
        } else {
            $view->batchAssign(['fields_html' => $fields_html]);
            $output .= $view->fetch('form/form_no_group.tpl');
        }

        // add submit button and form open/close tag
        if (!$fieldsOnly) {
            $data = [
                'type' => 'submit',
                'form' => $this->form['form_name'],
                'name' => $this->language->get('button_submit'),
            ];
            $submit = HtmlElementFactory::create($data);

            $data = [
                'type'   => 'form',
                'name'   => $this->form['form_name'],
                'attr'   => ' class="form" ',
                'action' => $this->html->getSecureURL(
                    $this->form['controller'],
                    '&form_id=' . $this->form['form_id'],
                    true
                ),
            ];
            if ($containFiles) {
                $data['enctype'] = 'multipart/form-data';
            }

            $form_open = HtmlElementFactory::create($data);
            $form_close = $view->fetch('form/form_close.tpl');

            $js = $this->addFormJs();

            $view->batchAssign(
                [
                    'description'  => $this->form['description'],
                    'form'         => $output,
                    'form_open'    => $js . $form_open->getHtml(),
                    'form_close'   => $form_close,
                    'submit'       => $submit,
                    'button_reset' => $this->language->get('button_reset'),
                ]
            );
            $output = $view->fetch('form/form.tpl');
        }

        return $output;
    }

    public function getFormElements(string $formAlias = '')
    {
        // if no form was loaded return empty string
        if (!$this->form) {
            return [];
        }

        $formAlias = $formAlias ?: $this->form['form_name'];
        $output = [];
        foreach ($this->fields as $field) {
            $field['attributes'] = html_entity_decode($field['attributes']);
            $field['regexp_pattern'] = html_entity_decode($field['regexp_pattern']);
            //check for enabled recaptcha instead of default captcha
            if ($this->config->get('config_recaptcha_site_key') && $field['element_type'] == 'K') {
                $field['element_type'] = 'J';
            }
            //build data array for each field HTML template
            $data = [
                'type'                    => HtmlElementFactory::getElementType($field['element_type']),
                'name'                    => $field['field_name'],
                'form'                    => $formAlias,
                'attr'                    => $field['attributes']
                    . ($field['regexp_pattern'] ? ' pattern="' . regexForHtmlPattern($field['regexp_pattern']) . '"' : ''),
                'required'                => $field['required'],
                'value'                   => $field['value'],
                'options'                 => $field['options'],
                'display_name'            => $field['name'],
                'group_id'                => $field['group_id'],
                'group_txt_id'            => $field['group_txt_id'],
                'field_group_name'        => $field['group_name'],
                'field_group_description' => $field['group_description'],
                'field_group_sort_order'  => $field['group_sort_order'],
            ];
            $data['value'] = $data['type'] == 'checkbox' && !$data['value'] ? 1 : $data['value'];
            $data['text'] = $data['type'] == 'label' && !$data['text'] ? $data['value'] : $data['text'];

            if ($field['resource_id']) {
                $resource = new AResource('image');
                $iconData = $resource->getResource($field['resource_id']);
                $img_sub_path = $iconData['type_name'] . '/' . $iconData['resource_path'];
                if (is_file(DIR_RESOURCE . $img_sub_path)) {
                    $logo_path = DIR_RESOURCE . $img_sub_path;
                    $info = get_image_size($logo_path);
                    $data['icon'] = HtmlElementFactory::create(
                        [
                            'type'   => 'resourceImage',
                            'url'    => HTTPS_DIR_RESOURCE . $img_sub_path,
                            'width'  => $info['width'],
                            'height' => $info['height'],
                        ]
                    )->getHtml();
                } else {
                    $data['icon'] = $iconData['resource_code'];
                }
            }

            //settings for country
            if ($field['element_type'] == 'O') {
                if (preg_match('/data-submit-mode="([^"]+)"/', $field['attributes'], $matches)) {
                    $submitMode = $matches[1] ?: 'id';
                } else {
                    $submitMode = 'id';
                }
                $data['submit_mode'] = $submitMode;
            }
            //zones
            if ($field['element_type'] == 'Z') {
                if (preg_match('/data-submit-mode="([^"]+)"/', $field['attributes'], $matches)) {
                    $submitMode = $matches[1] ?: 'id';
                } else {
                    $submitMode = 'id';
                }
                $data['submit_mode'] = $submitMode;
                $data['zone_only'] = true;
            }

            //populate customer entered values from session (if present)
            if (is_array($this->session->data['custom_form_' . $formAlias])) {
                $data['value'] = $this->session->data['custom_form_' . $formAlias][$field['field_name']];
            }

            //custom data based on the HTML element type
            switch ($data['type']) {
                case 'multiselectbox' :
                case 'checkboxgroup' :
                    $data['name'] .= '[]';
                    break;
                case 'captcha' :
                    $data['captcha_url'] = $this->html->getURL('common/captcha');
                    break;
                case 'recaptcha' :
                    $data['recaptcha_site_key'] = $this->config->get('config_recaptcha_site_key');
                    $data['language_code'] = $this->language->getLanguageCode();
                    break;
            }
            $sidx[$field['group_txt_id']] = (int)$field['group_sort_order'];
            $output[$field['group_txt_id']][$field['field_name']] = HtmlElementFactory::create($data);
        }

        if (count($output) > 1) {
            array_multisort($sidx, SORT_ASC, $output);
        }

        return $output;
    }

    /**
     * method for validation of data based on form fields requirements
     *
     * @param array $data - usually it's a $_POST
     *
     * @return array - array with error text for each of invalid field data
     * @throws AException
     */
    public function validateFormData($data = [])
    {
        $errors = [];
        $this->_loadFields();
        // load language for file upload text errors
        $this->load->language('checkout/cart');
        $errorRequiredText = $this->language->get('text_field_required');

        foreach ($this->fields as $field) {
            $fieldName = $field['field_name'];
            $fieldTitle = $field['name'];

            if (str_starts_with($fieldName, 'zone_') && $data[$fieldName] == '-1') {
                continue;
            }

            $isRequired = in_array($field['required'], [1, 'Y', '1']);
            // for multi-value required fields
            if (in_array($field['element_type'], HtmlElementFactory::getMultivalueElements())
                && !$data[$fieldName] && $isRequired
            ) {
                $errors[$fieldName] = $fieldTitle . ' ' . $errorRequiredText;
            }
            // for required string values
            if ($isRequired && !in_array($field['element_type'], ['K', 'J', 'U'])) {
                if (!is_array($data[$fieldName])) {
                    $data[$fieldName] = trim($data[$fieldName]);
                    //if empty string
                    if ($data[$fieldName] == '') {
                        $errors[$fieldName] = $fieldTitle . ' ' . $errorRequiredText;
                    }
                } // if empty array
                else if (!$data[$fieldName]) {
                    $errors[$fieldName] = $fieldTitle . ' ' . $errorRequiredText;
                }
            }
            //email regexp pattern
            if ($field['element_type'] == 'E') {
                $field['regexp_pattern'] = $field['regexp_pattern'] ?: EMAIL_REGEX_PATTERN;
            }

            // check by regexp
            if ($field['regexp_pattern']) {
                $dataArr = is_array($data[$fieldName]) ? $data[$fieldName] : [(string)$data[$fieldName]];
                foreach ($dataArr as $value) {
                    if (!preg_match($field['regexp_pattern'], $value)) {
                        if ($isRequired || $value) {
                            $errors[$fieldName] .= ' ' . $field['error_text'];
                        }
                        break;
                    }
                }
            }

            //for captcha or recaptcha
            if ($field['element_type'] == 'K' || $field['element_type'] == 'J') {
                if ($this->config->get('config_recaptcha_secret_key')) {
                    $recaptcha = new ReCaptcha($this->config->get('config_recaptcha_secret_key'));
                    $resp = $recaptcha->verify($data['g-recaptcha-response'] ?: $data['captcha'], $this->request->getRemoteIP());
                    if (!$resp->isSuccess() && $resp->getErrorCodes()) {
                        $errors[$fieldName] = $this->language->get('error_captcha');
                    }
                } else {
                    if (!isset($this->session->data['captcha']) || ($this->session->data['captcha'] != $data[$fieldName])
                    ) {
                        $errors[$fieldName] = $this->language->get('error_captcha');
                    }
                }
            }

            // for file
            if ($field['element_type'] == 'U' && ($this->request->files[$fieldName]['tmp_name'] || $isRequired)) {
                $fm = new AFile();
                $file_path_info = $fm->getUploadFilePath(
                    $data['settings']['directory'],
                    $this->request->files[$fieldName]['name']
                );
                $file_data = [
                    'name'     => $file_path_info['name'],
                    'path'     => $file_path_info['path'],
                    'type'     => $this->request->files[$fieldName]['type'],
                    'tmp_name' => $this->request->files[$fieldName]['tmp_name'],
                    'error'    => $this->request->files[$fieldName]['error'],
                    'size'     => $this->request->files[$fieldName]['size'],
                ];

                $file_errors = $fm->validateFileOption($field['settings'], $file_data);

                if ($file_errors) {
                    $errors[$fieldName] .= implode(' ', $file_errors);
                }
            }
        }

        return $errors;
    }

    /**
     * process uploads of files from form file element
     *
     * @param array $files - usually it's a $_FILES array
     *
     * @return array - list of absolute paths of moved files
     * @throws AException
     */
    public function processFileUploads($files = [])
    {
        if (!$files) {
            return [];
        }
        if ($this->fields) {
            $this->_loadFields();
        }

        $output = [];
        foreach ($this->fields as $field) {
            if ($field['element_type'] != 'U' || !$files[$field['field_name']]['tmp_name']) {
                continue;
            }

            $fm = new AFile();
            $file_path_info = $fm->getUploadFilePath(
                $field['settings']['directory'],
                $files[$field['field_name']]['name']
            );

            $result = move_uploaded_file($files[$field['field_name']]['tmp_name'], $file_path_info['path']);

            if ($result) {
                $output[$field['field_name']] = [
                    'display_name' => $field['name'],
                    'path'         => $file_path_info['path'],
                ];
            } else {
                $err = new AError(
                    "AForm error: can't to move uploaded file "
                    . $files[$field['field_name']]['tmp_name'] . " to " . $file_path_info['path']
                );
                $err->toLog()->toDebug();
            }

            $dataset = new ADataset('file_uploads', 'admin');
            $dataset->addRows(
                [
                    'date_added' => date("Y-m-d H:i:s", time()),
                    'name'       => $file_path_info['name'],
                    'type'       => $files[$field['field_name']]['type'],
                    'section'    => 'AForm:' . $this->form['form_name'] . ":" . $field['field_name'],
                    'section_id' => '',
                    'path'       => $file_path_info['path'],
                ]
            );
        }
        return $output;
    }
}