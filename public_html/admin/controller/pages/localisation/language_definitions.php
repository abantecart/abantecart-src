<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

class ControllerPagesLocalisationLanguageDefinitions extends AController {
	public $data = array();
	private $rt = 'localisation/language_definitions';

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->initBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL($this->rt),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current' => true
		));

		$this->request->get['language_id'] = !isset($this->request->get['language_id']) ? $this->config->get('storefront_language_id') : (int)$this->request->get['language_id'];
		$grid_settings = array(
				'table_id' => 'lang_definition_grid',
				'url' => $this->html->getSecureURL('listing_grid/language_definitions',
								'&language_id=' . $this->request->get['language_id']),
				'editurl' => $this->html->getSecureURL('listing_grid/language_definitions/update'),
				'update_field' => $this->html->getSecureURL('listing_grid/language_definitions/update_field'),
				'sortname' => 'date_modified',
				'actions' => array(
						'edit' => array(
								'text' => $this->language->get('text_edit'),
								'href' => $this->html->getSecureURL('localisation/language_definition_form/update',
												'&view_mode=all&language_definition_id=%ID%')
						),
						'save' => array(
								'text' => $this->language->get('button_save'),
						),
						'delete' => array(
								'text' => $this->language->get('button_delete'),
						)
				),
				'grid_ready' => 'grid_ready(data);'
		);

		$form = new AForm();
		$form->setForm(array(
				'form_name' => 'lang_definition_grid_search',
		));

		$grid_search_form = array();
		$grid_search_form['id'] = 'lang_definition_grid_search';
		$grid_search_form['form_open'] = $form->getFieldHtml(array(
				'type' => 'form',
				'name' => 'lang_definition_grid_search',
				'action' => '',
		));
		$grid_search_form['submit'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'submit',
				'text' => $this->language->get('button_go'),
				'style' => 'button1',
		));
		$grid_search_form['reset'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'reset',
				'text' => $this->language->get('button_reset'),
				'style' => 'button2',
		));

		$languages = $this->language->getAvailableLanguages();
		$options = array(-1 => $this->language->get('text_all_languages'));
		foreach ($languages as $lang) {
			$options[$lang['language_id']] = $lang['name'];
		}

		$grid_search_form['fields']['language_id'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'language_id',
				'options' => $options,
				'value' => $this->request->get['language_id']
		));

		$grid_search_form['fields']['section'] = $form->getFieldHtml(array(
				'type' => 'selectbox',
				'name' => 'section',
				'options' => array(
						'' => $this->language->get('text_all_section'),
						'admin' => $this->language->get('text_admin'),
						'storefront' => $this->language->get('text_storefront'),
				),
		));

		$grid_settings['search_form'] = true;

		$grid_settings['colNames'] = array(
				$this->language->get('column_block'),
				$this->language->get('column_key'),
				$this->language->get('column_translation'),
				$this->language->get('column_update_date'),
		);
		$grid_settings['colModel'] = array(
				array(
						'name' => 'block',
						'index' => 'block',
						'align' => 'left',
						'sorttype' => 'string',
						'width' => 200
				),
				array(
						'name' => 'language_key',
						'index' => 'language_key',
						'align' => 'left',
						'sorttype' => 'string',
						'width' => 200
				),
				array(
						'name' => 'language_value',
						'index' => 'language_value',
						'align' => 'left',
						'sorttype' => 'string',
						'sortable' => false,
						'width' => 260
				),
				array(
						'name' => 'date_modified',
						'index' => 'date_modified',
						'align' => 'center',
						'sorttype' => 'string',
						'search' => false,
						'width' => 90
				),
		);

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign('search_form', $grid_search_form);

		$this->document->setTitle($this->language->get('heading_title'));
		$this->view->assign('insert', $this->html->getSecureURL('localisation/language_definition_form/update', '&view_mode=all'));
		$this->view->assign('help_url', $this->gen_help_url('language_definitions_listing'));

		$this->processTemplate('pages/localisation/language_definitions_list.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}
}
