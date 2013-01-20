<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011, 2012 Belavier Commerce LLC

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

/**
 * Load form data
 * render output
 */
class AForm {
	/**
	 * @var registry - access to application registry
	 */
	protected $registry;
	/**
	 * @var $form - form data array
	 * Array (
	 *      [form_id]
	 *      [form_name]
	 *      [controller]
	 *      [success_page]
	 *      [status]
	 *      [description]
	 * )
	 */
	private $form;
	/**
	 * @var $fields - array of form fields
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
	private $fields;
	/**
	 * @var $groups - fields groups
	 */
	private $groups;
	/**
	 * @var $page_id - current page id
	 */
	public $page_id;
	/**
	 * @var $errors - field_name -=> error
	 */
	protected $errors;
	/**
	 * @var $form_edit_action - ( ST = standard,  HS = highlight save,  HT = highlight )
	 */
	private $form_edit_action;

	/**
	 * @param  $form_edit_action
	 */
	public function __construct($form_edit_action = '') {
		$this->registry = Registry::getInstance();
		$this->page_id = $this->layout->page_id;
		$this->errors = array();
		$this->form_edit_action = $form_edit_action;
	}

	/**
	 * @param  $key - key to load data from registry
	 */
	public function __get($key) {
		return $this->registry->get($key);
	}

	/**
	 * @param  $key - key to save data in registry
	 */
	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param  $errors - array of validation errors - field_name -=> error
	 * @return void
	 */
	public function setErrors($errors) {
		$this->errors = $errors;
	}

	/**
	 * Sets the form and loads the data for the form from the database
	 *
	 * @param  $fname - unique form name
	 * @return void
	 */
	public function loadFromDb($name) {

		$this->_loadForm($name);
		// if no form return
		if (empty($this->form)) return;

		$this->_loadFields();
		// if no fields no need to get groups
		if (empty($this->fields)) return;

		$this->_loadGroups();
	}

	/**
	 * load form data into this->form variable
	 *
	 * @param  $name - unique form name
	 * @return void
	 */
	private function _loadForm($name) {

		$cache_name = 'forms.' . $name;
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$form = $this->cache->get($cache_name, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
		if (isset($form)) {
			$this->form = $form;
			return;
		}

		$query = $this->db->query("SELECT f.*, fd.description
                                    FROM `" . DB_PREFIX . "forms` f
                                    LEFT JOIN `" . DB_PREFIX . "form_descriptions` fd
                                        ON ( f.form_id = fd.form_id AND fd.language_id = '" . (int)$this->config->get('storefront_language_id') . "' )
                                    WHERE f.form_name = '" . $this->db->escape($name) . "'
                                            AND f.status = 1 "
		);

		if (!$query->num_rows) {
			$err = new AError('NOT EXIST Form with name ' . $name);
			$err->toDebug()->toLog();
			return;
		}
		$this->cache->set($cache_name, $query->row, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
		$this->form = $query->row;
	}

	/**
	 * load form fields data into this->fields variable
	 *
	 * @return void
	 */
	private function _loadFields() {

		$cache_name = 'forms.' . $this->form[ 'form_name' ] . '.fields';
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$fields = $this->cache->get($cache_name, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
		if (isset($fields)) {
			$this->fields = $fields;
			return;
		}

		$query = $this->db->query("
            SELECT f.*, fd.name, fd.description
            FROM `" . DB_PREFIX . "fields` f
                LEFT JOIN `" . DB_PREFIX . "field_descriptions` fd ON ( f.field_id = fd.field_id AND fd.language_id = '" . (int)$this->config->get('storefront_language_id') . "' )
            WHERE f.form_id = '" . $this->form[ 'form_id' ] . "'
                AND f.status = 1
            ORDER BY f.sort_order"
		);
		$this->fields = array();
		if ($query->num_rows) {
			foreach ($query->rows as $row) {
				$this->fields[ $row[ 'field_id' ] ] = $row;
				$query = $this->db->query("
					SELECT *
					FROM `" . DB_PREFIX . "field_values`
					WHERE field_id = '" . $row[ 'field_id' ] . "'
					AND language_id = '" . (int)$this->config->get('storefront_language_id') . "'"
				);
				if ($query->num_rows) {

					$values = unserialize($query->row[ 'value' ]);
					usort($values, array( 'self', '_sort_by_sort_order' ));
					foreach ($values as $value) {
						$this->fields[ $row[ 'field_id' ] ][ 'options' ][ $value[ 'name' ] ] = $value[ 'name' ];
					}
					$this->fields[ $row[ 'field_id' ] ][ 'value' ] = $values[ 0 ][ 'name' ];

				}
			}
		}
		$this->cache->set($cache_name, $this->fields, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
	}

	private function _sort_by_sort_order($a, $b) {
		if ($a[ 'sort_order' ] == $b[ 'sort_order' ]) {
			return 0;
		}
		return ($a[ 'sort_order' ] < $b[ 'sort_order' ]) ? -1 : 1;
	}

	/**
	 * load form fields groups data into this->groups variable
	 *
	 * @return void
	 */
	private function _loadGroups() {

		$cache_name = 'forms.' . $this->form[ 'form_name' ] . '.groups';
		$cache_name = preg_replace('/[^a-zA-Z0-9\.]/', '', $cache_name);
		$groups = $this->cache->get($cache_name, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
		if (isset($groups)) {
			$this->groups = $groups;
			return;
		}

		$query = $this->db->query("
            SELECT fg.*, fgd.name, fgd.description
            FROM `" . DB_PREFIX . "form_groups` g
                LEFT JOIN `" . DB_PREFIX . "fields_groups` fg ON ( g.group_id = fg.group_id)
                LEFT JOIN `" . DB_PREFIX . "fields_group_descriptions` fgd ON ( fg.group_id = fgd.group_id AND fgd.language_id = '" . (int)$this->config->get('storefront_language_id') . "' )
            WHERE g.form_id = '" . $this->form[ 'form_id' ] . "'
                AND g.status = 1
            ORDER BY g.sort_order, fg.sort_order"
		);
		$this->groups = array();
		if ($query->num_rows)
			foreach ($query->rows as $row) {
				if (empty($this->groups[ $row[ 'group_id' ] ])) {
					$this->groups[ $row[ 'group_id' ] ] = $row;
				}
				$this->groups[ $row[ 'group_id' ] ][ 'fields' ][ ] = $row[ 'field_id' ];
			}

		$this->cache->set($cache_name, $this->groups, (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
	}

	/**
	 * return form data
	 *
	 * @return form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * set form data
	 *
	 * @return void
	 */
	public function setForm($form) {
		$this->form = $form;
	}

	/**
	 * get form fields array
	 *
	 * @return array of fields => value
	 */
	public function getFields() {

		$fields = array();

		foreach ($this->fields as $field) {
			$fields[ $field[ 'field_name' ] ] = array(
				'value' => $field[ 'value' ],
				'required' => $field[ 'required' ],
			);
		}

		return $fields;
	}

	/**
	 * Get given field, type, values and selected/default
	 *
	 * @param  $fname
	 * @return array with field data
	 */
	public function getField($fname) {
		foreach ($this->fields as $field) {
			if ($field[ 'field_name' ] == $fname) {
				return array(
					'field_name' => $field[ 'field_name' ],
					'element_type' => $field[ 'element_type' ],
					'required' => $field[ 'required' ],
					'name' => $field[ 'name' ],
					'value' => $field[ 'value' ],
					'description' => $field[ 'description' ],
				);
			}
		}

		$err = new AWarning('NOT EXIST Form field with name ' . $fname);
		$err->toDebug()->toLog();
		return null;
	}

	/**
	 * assign value(s) to given field name
	 *
	 * @param  $fname
	 * @param string $value
	 * @return void
	 */
	public function assign($fname, $value = '') {
		foreach ($this->fields as $key => $field) {
			if ($field[ 'field_name' ] == $fname) {
				$this->fields[ $key ][ 'value' ] = $value;
				break;
			}
		}
	}

	/**
	 * assign array of field with values.
	 *
	 * @param  $values - array of field name -> value
	 * @return void
	 */
	public function batchAssign($values) {
		foreach ($values as $name => $value) {
			$this->assign($name, $value);
		}
	}

	/**
	 * load values to select, multiselect, checkbox group etc
	 * @return void
	 */
	public function loadFieldOptions($fname, $values) {
		foreach ($this->fields as $key => $field) {
			if ($field[ 'field_name' ] == $fname) {
				$this->fields[ $key ][ 'options' ] = $values;
				break;
			}
		}
	}

	/**
	 * return field html
	 *
	 * @param  $data - array with field data
	 * @return field html
	 */
	public function getFieldHtml($data) {
		$data[ 'form' ] = $this->form[ 'form_name' ];
		$item = HtmlElementFactory::create($data);

		$js = '';
		if ($data[ 'type' ] == 'form') {
			$js = $this->addFormJs();
		}

		return $js . $item->getHtml();
	}

	/**
	 * Render the form elements only
	 *
	 * @return form html
	 */
	public function loadExtendedFields() {
		return $this->getFormHtml(true);
	}

	/**
	 * add javascript to implement form behaviour based on form_edit_action parameter
	 *
	 * @return void
	 */
	protected function addFormJs() {
		$html = $this->registry->get('html');
		$language = $this->registry->get('language');
		$view = new AView($this->registry, 0);

		switch ($this->form_edit_action) {
			case 'ST': //standards
				$view->batchAssign(
					array(
						'id' => $this->form[ 'form_name' ],
					)
				);
				$output = $view->fetch('form/form_js_st.tpl');
				break;
			case 'HS': //highlight on change and show save button
				$view->batchAssign(
					array(
						'id' => $this->form[ 'form_name' ],
						'button_save' => str_replace("\r\n", "", $html->buildButton(array( 'name' => 'btn_save', 'text' => $language->get('button_save'), 'style' => 'button3' ))),
						'button_reset' => str_replace("\r\n", "", $html->buildButton(array( 'name' => 'btn_reset', 'text' => $language->get('button_reset'), 'style' => 'button2' ))),
						'update' => $this->form[ 'update' ],
					)
				);
				$output = $view->fetch('form/form_js_hs.tpl');
				break;
			case 'HT': //highlight on change
				$view->batchAssign(
					array(
						'id' => $this->form[ 'form_name' ],
					)
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
	 * @return form html
	 */
	public function getFormHtml($fieldsOnly = false) {

		// if no form was loaded return empty string
		if (empty($this->form)) return '';

		$fields_html = array();
		$view = new AView($this->registry, 0);

		foreach ($this->fields as $field) {

			$data = array(
				'type' => HtmlElementFactory::getElementType($field[ 'element_type' ]),
				'name' => $field[ 'field_name' ],
				'form' => $this->form[ 'form_name' ],
				'attr' => $field[ 'attributes' ],
				'required' => $field[ 'required' ],
				'value' => $field[ 'value' ],
				'options' => $field[ 'options' ],
			);

			switch ($data[ 'type' ]) {
				case 'multiselectbox' :
					$data[ 'name' ] .= '[]';
					break;
				case 'checkboxgroup' :
					$data[ 'name' ] .= '[]';
					break;
				case 'captcha' :
					$data[ 'captcha_url' ] = $this->html->getURL('common/captcha');
					break;
			}
			$item = HtmlElementFactory::create($data);

			switch ($data[ 'type' ]) {
				case 'IPaddress' :
				case 'hidden' :
					$fields_html[ $field[ 'field_id' ] ] = $item->getHtml();
					break;
				default:
					$view->batchAssign(
						array(
							'element_id' => $item->element_id,
							'title' => $field[ 'name' ],
							'description' => (!empty($this->form[ 'description' ]) ? $field[ 'description' ] : ''),
							'error' => (!empty($this->errors[ $field[ 'field_name' ] ]) ? $this->errors[ $field[ 'field_name' ] ] : ''),
							'item_html' => $item->getHtml(),
						)
					);
					$fields_html[ $field[ 'field_id' ] ] = $view->fetch('form/form_field.tpl');
			}
		}

		$output = '';
		if (!empty($this->groups)) {
			foreach ($this->groups as $group) {
				$view->batchAssign(
					array(
						'group' => $group,
						'fields_html' => $fields_html,
					)
				);
				$output .= $view->fetch('form/form_group.tpl');
			}
		} else {
			$view->batchAssign(array( 'fields_html' => $fields_html ));
			$output .= $view->fetch('form/form_no_group.tpl');
		}

		// add submit button and form open/close tag
		if (!$fieldsOnly) {
			$data = array(
				'type' => 'submit',
				'form' => $this->form[ 'form_name' ],
				'name' => $this->language->get('button_continue'),
			);
			$submit = HtmlElementFactory::create($data);

			$data = array(
				'type' => 'form',
				'name' => $this->form[ 'form_name' ],
				'attr' => ' class="form" ',
				'action' => str_replace('&', '&amp;', $this->html->getSecureURL($this->form[ 'controller' ])),
			);
			$form_open = HtmlElementFactory::create($data);
			$form_close = $view->fetch('form/form_close.tpl');

			$js = $this->addFormJs();

			$view->batchAssign(
				array(
					'description' => $this->form[ 'description' ],
					'form' => $output,
					'form_open' => $js . $form_open->getHtml(),
					'form_close' => $form_close,
					'submit' => $submit->getHtml(),
				)
			);
			$output = $view->fetch('form/form.tpl');
		}

		return $output;
	}

	private function _getCountries() {
		$countries = array();
		$this->load->model('localisation/country', '');
		$results = $this->model_localisation_country->getCountries();
		$data[ 'options' ] = array();
		foreach ($results as $c) {
			$countries[ $c[ 'name' ] ] = $c[ 'name' ];
		}
		return $countries;
	}

}