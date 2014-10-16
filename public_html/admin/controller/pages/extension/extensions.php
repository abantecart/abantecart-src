<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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

/**
 * Class ControllerPagesExtensionExtensions
 * @property ModelToolMPApi $model_tool_mp_api
 */
class ControllerPagesExtensionExtensions extends AController {

	public $data;
	private $error;

	public function main() {

		if (!in_array($this->session->data['extension_filter'], array('extensions', 'payment', 'shipping', 'template', 'language'))) {
			$this->session->data['extension_filter'] = 'extensions';
		}
		unset($this->session->data['package_info']);

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		//put extension_list for remote install into session to prevent multiple requests for grid

		$this->loadModel('tool/mp_api');
		$this->session->data['ready_to_install'] = $this->model_tool_mp_api->getExtensions();


		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->initBreadcrumb(array(
			'href' => $this->html->getSecureURL('index/home'),
			'text' => $this->language->get('text_home'),
			'separator' => FALSE
		));
		$this->document->addBreadcrumb(array(
			'href' => $this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']),
			'text' => $this->language->get('heading_title'),
			'separator' => ' :: ',
			'current'   => true
		));

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}

		//set store id based on param or session.
		$store_id = (int)$this->config->get('config_store_id');
		if ( has_value($this->request->get_or_post('store_id')) ) {
			$store_id = (int)$this->request->get_or_post('store_id');
			$this->session->data['current_store_id'] = (int)$this->request->get_or_post('store_id');
		} else if ($this->session->data['current_store_id']) {
			$store_id = (int)$this->session->data['current_store_id'];
		}

		$grid_settings = array(
			'table_id' => 'extension_grid',
			'url' => $this->html->getSecureURL('listing_grid/extension', '&store_id=' . $store_id),
			'editurl' => $this->html->getSecureURL('listing_grid/extension/update'),
			'update_field' => $this->html->getSecureURL('listing_grid/extension/update'),
			'sortname' => 'date_modified',
			'sortorder' => 'desc',
			'multiselect' => 'false',
			'actions' => array(
							'edit' => array(
								'text' => $this->language->get('text_edit'),
								'href' => $this->html->getSecureURL('extension/extensions/edit', '&store_id='.$store_id)
							),
							'remote_install' => array(
								'text' => $this->language->get('text_install'),
								'href'=> $this->html->getSecureURL( 'tool/package_installer/download')
							),
							'install' => array(
								'text' => $this->language->get('text_install'),
								'href' => $this->html->getSecureURL('extension/extensions/install')
							),
							'uninstall' => array(
								'text' => $this->language->get('text_uninstall'),
								'href' => $this->html->getSecureURL('extension/extensions/uninstall')
							),
							'delete' => array(
								'text' => $this->language->get('button_delete'),
								'href' => $this->html->getSecureURL('extension/extensions/delete')
							)
						),
			'grid_ready' => 'extension_grid_ready(data);'
		);

		$grid_settings['colNames'] = array('',
			$this->language->get('column_id'),
			$this->language->get('column_name'),
			$this->language->get('column_category'),
			$this->language->get('column_update_date'));
		if (!$this->config->get('config_store_id')) {
			$grid_settings['colNames'][] = $this->language->get('tab_store');
		}
		$grid_settings['colNames'][] = $this->language->get('column_status');


		$grid_settings['colModel'] = array(
			array('name' => 'icon',
				'index' => 'icon',
				'width' => 90,
				'align' => 'center',
				'sortable' => false,
				'search' => false),
			array('name' => 'key',
				'index' => 'key',
				'width' => 130,
				'align' => 'center',
				'search' => true),
			array('name' => 'name',
				'index' => 'name',
				'width' => 200,
				'align' => 'center',
				'search' => false),
			array('name' => 'category',
				'index' => 'category',
				'width' => 80,
				'align' => 'center',
				'search' => false),
			array('name' => 'date_modified',
				'index' => 'date_modified',
				'width' => 110,
				'align' => 'center',
				'search' => false));
		if (!$this->config->get('config_store_id')) {
			$grid_settings['colModel'][] = array('name' => 'store_name',
				'index' => 'store_name',
				'width' => 70,
				'align' => 'center',
				'search' => false);
		}
		$grid_settings['colModel'][] = array('name' => 'status',
			'index' => 'status',
			'width' => 120,
			'align' => 'center',
			'search' => false);

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->data['btn_extensions_store'] = $this->html->buildElement(
																	array(
																		'type' => 'button',
																		'name' => 'btn_ext_store',
																		'text' => $this->language->get('text_extensions_store'),
																		'href' => $this->html->getSecureURL('extension/extensions_store')
																		));

		$this->data['btn_add_new'] = $this->html->buildElement(
															array(
																'type' => 'button',
																'name' => 'text_add_new',
																'text' => $this->language->get('text_add_new'),
																'href' => $this->html->getSecureURL('tool/package_installer')
															));


		$this->data['license_url'] = $this->html->getSecureURL('listing_grid/extension/license');
		$this->data['dependants_url'] = $this->html->getSecureURL('listing_grid/extension/dependants');

		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());
		$this->view->assign('extension_edit_url', $this->html->getSecureURL('listing_grid/extension/license')) ;
		$this->view->assign('help_url', $this->gen_help_url('extension_listing'));

		$this->view->batchAssign($this->data);

		$this->processTemplate('pages/extension/extensions.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * method set in session extension type for extension list filtering
	 * @return void
	 */
	public function extensions() {
		$this->session->data['extension_filter'] = 'extensions';
		$this->main();
	}

	public function payment() {
		$this->session->data['extension_filter'] = 'payment';
		$this->main();
	}

	public function shipping() {
		$this->session->data['extension_filter'] = 'shipping';
		$this->main();
	}

	public function template() {
		$this->session->data['extension_filter'] = 'template';
		$this->main();
	}

	public function language() {
		$this->session->data['extension_filter'] = 'language';
		$this->main();
	}

	public function edit() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb(array('href' => $this->html->getSecureURL('index/home'),
			'text' => $this->language->get('text_home'),
			'separator' => FALSE));
		$this->document->addBreadcrumb(array('href' => $this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']),
			'text' => $this->language->get('heading_title'),
			'separator' => ' :: '));

		$extension = $this->request->get['extension'];
		$this->loadLanguage('extension/extensions');
		$this->loadLanguage($extension . '/' . $extension);

		$store_id = (int)$this->config->get('config_store_id');
		if ($this->request->get_or_post('store_id')) {
			$store_id = $this->request->get_or_post('store_id');
		}

		$ext = new ExtensionUtils($extension, $store_id);
		$settings = $ext->getSettings();
		$extension_info = $this->extensions->getExtensionInfo($extension);
		if (!$extension_info) { // if extension is not installed yet - redirect to list
			$this->redirect($this->html->getSecureURL('extension/extensions'));
		}

		/** build aform with settings**/

		$form = new AForm('HS');
		$form->setForm(
			array(
				'form_name' => 'editSettings',
				'update'	=> $this->html->getSecureURL('listing_grid/extension/update', '&id=' . $extension . '&store_id=' . $store_id)
			)
		);

		$this->data['form']['form_open'] = $form->getFieldHtml(
			array(
				'type' => 'form',
				'name' => 'editSettings',
				'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
				'action' => $this->html->getSecureURL('extension/extensions/edit/', '&action=save&extension=' . $extension . '&store_id=' . $store_id)
			)
		);

		$result = array('resource_field_list' => array());

		// store switcher for default store Control Panel only
		if (!$this->config->get('config_store_id')) {
			$stores = array();

			$stores[0] = $this->language->get('text_default');
			$this->loadModel('setting/store');
			$stores_arr = $this->model_setting_store->getStores();
			if (count($stores_arr) > 1) {
				foreach ($stores_arr as $res) {
					$stores[$res['store_id']] = $res['alias'];
				}
				$switcher = array(
						'name' => 'store_id',
						'type' => 'selectbox',
						'options' => $stores,
						'value' => $store_id,
						'note' => $this->language->get('tab_store'),
						'style' => 'no-save');
			} else {
				$switcher = array(
						'type' => 'hidden',
						'name' => 'store_id',
						'note' => ' ',
						'value' => 0);
			}
		} else {
			$switcher = array(
					'type' => 'hidden',
					'name' => 'store_id',
					'note' => ' ',
					'value' => $store_id);
		}

		array_unshift($settings, $switcher);
		foreach ($settings as $item) {
			$data = array();
			if ($item['name'] == $extension . '_status') {
				$data['attr'] = ' reload_on_save="true"';
				$status = $item['value'];
				//set sign for confirmation modal about dependendants for disable action
				if($item['value']==1){
					$children = $this->extension_manager->getChildrenExtensions($extension);
					if ($children) {
						foreach($children as $child){
							if ($this->config->get($child['key'] . '_status')) {
								$this->data['has_dependants'] = true;
								break;
							}
						}
					}
				}

			}
			$data['name'] = $item['name'];
			$data['type'] = $item['type'];
			$data['value'] = $item['value'];
			$data['required'] = (bool)$item['required'];

			if($item[ 'note' ]){
				$data[ 'note' ] = $item[ 'note' ];
			} else {
				$note_text = $this->language->get($data[ 'name' ]);
				// if text definition not found - seek it in default settings definitions
				if ($note_text == $data[ 'name' ]) {
					$new_text_key = str_replace($extension . '_','text_',$data[ 'name' ]);
					$note_text = $this->language->get($new_text_key);
					if ($note_text == $new_text_key) {
						$note_text = $this->language->get($new_text_key.'_'.$extension_info['type']);
					}
				}
				$data[ 'note' ] = $note_text;
			}

			if ($item['style']) {
				$data['style'] = $item['style'];
			}
			if ($item['attr']) {
				$data['attr'] = $item['attr'];
			}
			if ($item['readonly']) {
				$data['readonly'] = $item['readonly'];
			}

			switch ($data['type']) {
				case 'selectbox':
				case 'multiselectbox':
				case 'checkboxgroup':
					// if options need to extract from db
					$data['options'] = $item['options'];
					if ($item['model_rt'] != '') {
						//force to load models even before extension is enabled
						$this->loadModel($item['model_rt'], 'force');
						$model = $this->{'model_' . str_replace("/", "_", $item['model_rt'])};
						$method_name = $item['method'];
						if (method_exists($model, $method_name)) {
							$res = call_user_func(array($model, $method_name));
							if ($res) {
								$field1 = $item['field1'];
								$field2 = $item['field2'];
								foreach ($res as $opt) {
									$data['options'][$opt[$field1]] = $opt[$field2];
								}
							}
						}
					}
				    if($data['type']=='checkboxgroup' || $data['type']=='multiselectbox'){
						#custom settings for multivalue
						$data[ 'scrollbox' ] = 'true';
						if(substr($item['name'],-2)!='[]'){
							$data['name'] = $item['name']."[]";
						}
						$data['style'] = "chosen";
					}
				    break;
				case 'checkbox':
					$data['style'] = "btn_switch";
					break;

				case 'resource':
					$item['resource_type'] = (string)$item['resource_type'];
					$data['rl_types'] = array($item['resource_type']);
					$data['rl_type'] = $item['resource_type'];
					if (!$result['rl_scripts']) {
						$scripts = $this->dispatch('responses/common/resource_library/get_resources_scripts',
												array(
													'object_name' => '',
													'object_id' => '',
													'types' => $item['resource_type'],
													'mode' => 'single'
												));
						$result['rl_scripts'] = $scripts->dispatchGetOutput();
						unset($scripts);
					}
					$data['resource_path'] = $item['value'];
					break;
				default:
			}
			/**
			 * @var HtmlElementFactory
			 * */
			$field = $form->getFieldHtml($data);
			$result[ 'html' ][ $data[ 'name' ] ] = array(
										'note'  => $data[ 'note' ],
										'value' => $field);
		}


		// end building aform
		$this->data['settings'] = $result['html'];

		$this->data['resources_scripts'] = $result['rl_scripts'];
		$this->data['target_url'] = $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension . '&store_id=' . $store_id);

		if (isset($this->request->get['restore']) && $this->request->get['restore']) {
			$this->extension_manager->editSetting($extension, $ext->getDefaultSettings());
			$this->cache->delete('settings.extension');
			$this->session->data['success'] = $this->language->get('text_restore_success');
			$this->redirect($this->data['target_url']);
		}

		if ($this->request->is_POST() && $this->_validateSettings($extension,$store_id)) {
			foreach ($settings as $item) {
				if (!isset($this->request->post[$item['name']])) {
					$this->request->post[$item['name']] = 0;
				}
			}
			
			$this->extension_manager->editSetting($extension, $this->request->post);
			$this->cache->delete('settings.extension');
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->data['target_url']);
		}

		$conflict_resources = $ext->validateResources();
		if (!empty($conflict_resources)) {
			ob_start();
			print_r($conflict_resources);
			$err = ob_get_clean();
			ADebug::warning('resources conflict', AC_ERR_USER_WARNING, $extension . ' Extension resources conflict detected.<br/><pre>' . $err . '</pre>');
		}

		$this->document->setTitle($this->language->get($extension . '_name'));

		$this->document->addBreadcrumb(array(
			'href' => $this->data['target_url'],
			'text' => $this->language->get($extension . '_name'),
			'separator' => ' :: ',
			'current' => true
		));

		$this->data['heading_title'] = $this->language->get($extension . '_name');
		$this->data['text_version'] = $this->language->get('text_version');
		$this->data['text_installed_on'] = $this->language->get('text_installed_on');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_license'] = $this->language->get('text_license');
		$this->data['text_dependency'] = $this->language->get('text_dependency');
		$this->data['text_configuration_settings'] = $this->language->get('text_configuration_settings');

		$this->data['button_back'] = $this->html->buildElement(
				array(  'type' => 'button',
						'name' => 'btn_back',
						'text' => $this->language->get('text_back')
				)
		);
		$this->data['button_reload'] = $this->html->buildElement(
				array(  'type' => 'button',
						'name' => 'btn_reload',
						'text' => $this->language->get('text_reload')
				));
		$this->data['button_restore_defaults'] = $this->html->buildElement(
				array(  'type' => 'button',
						'name' => 'button_restore_defaults',
						'text' => $this->language->get('button_restore_defaults'),
						'href' => $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension.'&reload=1')
				));
		$this->data['button_save'] = $this->html->buildElement(
				array(  'type' => 'button',
						'name' => 'btn_save',
						'text' => $this->language->get('button_save')
				));
		$this->data['button_save_green'] = $this->html->buildElement(
				array(  'type' => 'button',
						'name' => 'btn_save',
						'text' => $this->language->get('button_save')
				));
		$this->data['button_reset'] = $this->html->buildElement(
				array(  'type' => 'button',
						'name' => 'btn_reset',
						'text' => $this->language->get('text_reset')
				));
		$this->data['reload'] = $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension);
		$this->data['back'] = $this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']);
		$this->data['update'] = $this->html->getSecureURL('listing_grid/extension/update', '&id=' . $extension . '&store_id=' . $store_id);
		$this->data['dependants_url'] = $this->html->getSecureURL('listing_grid/extension/dependants', '&extension='.$extension);

		if(!$this->extension_manager->validateDependencies($extension,getExtensionConfigXml($extension))){
			$this->error['warning'] = $this->language->get('error_dependencies');
		}


		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$icon_ext_img_url = HTTP_CATALOG . 'extensions/' . $extension . '/image/icon.png';
		$icon_ext_dir = DIR_EXT . $extension . '/image/icon.png';
		$icon = (is_file($icon_ext_dir) ? $icon_ext_img_url : RDIR_TEMPLATE . 'image/default_extension.png');
		$extension_data = array('id' => $extension);

		$missing_extensions = $this->extensions->getMissingExtensions();
		if (!in_array($extension, $missing_extensions)) {
			$extension_data['icon'] = $icon;
			$extension_data['name'] = $this->language->get($extension . '_name');
			$extension_data['version'] = $extension_info['version'];

			$long_datetime_format = $this->language->get('date_format_long').' '.$this->language->get('time_format');
			if($extension_info['date_installed']){
				$extension_data['installed'] = dateISO2Display($extension_info['date_installed'], $long_datetime_format );
			}
			if($extension_info['date_added']){
				$extension_data['date_added'] =  dateISO2Display($extension_info['date_added'], $long_datetime_format );
			}

			$extension_data['license'] = $extension_info['license_key'];
			$extension_data['note'] = $ext->getConfig('note') ? $this->html->convertLinks($this->language->get($extension . '_note')) : '';

			$config = $ext->getConfig();
			if (!empty($config->preview->item)) {
				foreach ($config->preview->item as $item) {
					if (!is_file(DIR_EXT . $extension . DIR_EXT_IMAGE . (string)$item))
						continue;
					$extension_data['preview'][] = HTTPS_EXT . $extension . DIR_EXT_IMAGE . (string)$item;
				}
				//image gallery scripts and css for previews
				$this->document->addStyle(array(
				        'href' => RDIR_TEMPLATE . 'javascript/blueimp-gallery/css/bootstrap-image-gallery.css',
				        'rel' => 'stylesheet'
				));
				$this->document->addStyle(array(
				        'href' => RDIR_TEMPLATE . 'javascript/blueimp-gallery/css/blueimp-gallery.min.css',
				        'rel' => 'stylesheet'
				));
				$this->document->addScript(RDIR_TEMPLATE.'javascript/blueimp-gallery/jquery.blueimp-gallery.min.js');
				$this->document->addScript(RDIR_TEMPLATE.'javascript/blueimp-gallery/bootstrap-image-gallery.js');
			}

			if (isset($this->session->data['extension_updates'][$extension])) {

				$this->data['upgrade_button'] = $this->html->buildElement(
									array(  'type'=> 'button',
											'name' => 'btn_upgrade',
											'id' => 'upgradenow',
											'href' => AEncryption::addEncoded_stid($this->session->data['extension_updates'][$extension]['url']),
											'text' => $this->language->get('button_upgrade')
									));
			}
			if($ext->getConfig('help_link')){
				$extension_data['help'] = array(
											'ext_link' => array(
																'text' => $this->language->get('text_developer_site'),
																'link' => $ext->getConfig('help_link'))
				);
			}
			if ($ext->getConfig('help_file')) {
				$extension_data['help']['file'] = array(
											'link' => $this->html->getSecureURL('extension/extension/help', '&extension=' . $this->request->get['extension']),
											'text' => $this->language->get('button_howto'));
			}

			$extension_data['dependencies'] = array();
			$extension_data['extensions'] = $this->extensions->getEnabledExtensions();
			$missing_extensions = $this->extensions->getMissingExtensions();
			$db_extensions = $this->extensions->getDbExtensions();

			if (isset($config->dependencies->item)) {
				foreach ($config->dependencies->item as $item) {
					$id = (string)$item;
					$actions = array();

					if($this->config->has($id . '_status')){
						$status = $this->language->get('text_installed') .' ('.$this->language->get('text_enabled').')';
					}else{
						$status =  $this->language->get('text_not_installed').' ('.$this->language->get('text_disabled').')';
					}

					if (in_array($id, $db_extensions)) {
						if (in_array($id, $missing_extensions)) {
							$class = 'warning';
							$status = sprintf( $this->language->get('text_missing_extension'), $id );
							$actions['delete'] = $this->html->buildElement(
								array(
									'type'=>'button',
									'href'=> $this->html->getSecureURL('extension/extensions/delete', '&extension=' . $id),
									'target' => '_blank',
									'style'  => 'btn_delete',
									'icon'  => 'fa fa-trash-o',
									'title'  => $this->language->get('text_delete')
								)
							);
						} else {

							if (!$this->config->has($id . '_status')) {
								$actions['install'] = $this->html->buildElement(
																array(
																	'type'=>'button',
																	'href'=> $this->html->getSecureURL('extension/extensions/install', '&extension=' . $id),
																	'target' => '_blank',
																	'style'  => 'btn_install',
																	'icon'  => 'fa fa-play',
																	'title'=> $this->language->get('text_install')
																)
								);
								$actions['delete'] = $this->html->buildElement(
																array(
																	'type'=>'button',
																	'href'=> $this->html->getSecureURL('extension/extensions/delete', '&extension=' . $id),
																	'target' => '_blank',
																	'style'  => 'btn_delete',
																	'icon'  => 'fa fa-trash-o',
																	'title'=> $this->language->get('text_delete')
																)
								);
							} else {
								$actions['edit'] = $this->html->buildElement(
																array(
																	'type'=>'button',
																	'href'=> $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $id),
																	'target' => '_blank',
																	'style'=> 'btn_edit',
																	'icon'  => 'fa fa-edit',
																	'title'=> $this->language->get('text_edit')
																)
								);
								if (!(boolean)$item['required']) {
									$actions['uninstall'] = $this->html->buildElement(
																	array(
																		'type'=>'button',
																		'href'=> $this->html->getSecureURL('extension/extensions/uninstall', '&extension=' . $id),
																		'target' => '_blank',
																		'style'  => 'btn_uninstall',
																		'icon'  => 'fa fa-times',
																		'title'=> $this->language->get('text_uninstall')
																	)
									);
								}
							}
						}
					} else {
						$actions['mp'] = $this->html->buildElement(
														array(
															'type'=>'button',
															'href'=> $this->html->getSecureURL('extension/extensions_store', '&extension=' . $id),
															'target' => '_blank',
															'style'  => 'btn_mp',
															'icon'  => 'fa fa-play',
															'title'=> $this->language->get('text_visit_repository')
														)
						);
					}

					$extension_data['dependencies'][] = array(
						'required' => (boolean)$item['required'],
						'id' => $id,
						'status' => $status,
						'actions' => $actions,
						'class' => $class,
					);
					unset($class);

				}
			}

		} else { // if extension missing
			$extension_data['icon'] = $icon;
			$extension_data['name'] = sprintf($this->language->get('text_missing_extension'), $extension);
		}
		// additional settings page

		if ($ext->getConfig('additional_settings') && $status) {
			$btn_param = array(
					'type' => 'button',
					'name' => 'btn_addsett',
					'href' => $this->html->getSecureURL($ext->getConfig('additional_settings')),
					'text' => $this->language->get('text_additional_settings'),
					'style' => 'button1');


			if ($store_id) {
				$this->loadModel('setting/store');
				$store_info = $this->model_setting_store->getStore($store_id);
				$btn_param['link'] = $store_info['config_url'] . '?s=' . ADMIN_PATH . '&rt=' . $ext->getConfig('additional_settings');
				$btn_param['target'] = '_blank';
				$btn_param['onclick'] = 'onclick="return confirm(\'' . $this->language->get('additional_settings_confirm') . '\');"';
			}
			$this->data['add_sett'] = $this->html->buildElement($btn_param);
		}
		$this->data['extension'] = $extension_data;
		$this->data['target_url'] = $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension);
		$this->view->assign('help_url', $this->gen_help_url('extension_edit'));

		$template = 'pages/extension/extensions_edit.tpl';
		//#PR set custom templates for extension settings page.  
		if ( has_value( (string)$config->custom_settings_template ) ) {
		    //build path to template directory.
			$dir_template = DIR_EXT . $extension . DIR_EXT_ADMIN . DIR_EXT_TEMPLATE . $this->config->get('admin_template') . "/template/";
			$dir_template .= (string)$config->custom_settings_template;
			//validate template and report issue
			if (!file_exists( $dir_template )) {
            	$warning = new AWarning("Cannot load override template $dir_template in extension $extension!" );
            	$warning->toLog()->toDebug();
			} else {
				$template = $dir_template;
			}			
		}

		$this->view->batchAssign($this->data);
		$this->processTemplate($template);

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * @param string $extension
	 * @param int $store_id
	 * @return bool
	 */
	private function _validateSettings($extension,$store_id) {
		if (!$this->user->canModify('extension/extensions')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			//then check required fields and validate it
			$ext = new ExtensionUtils($extension, $store_id);
			$validate = $ext->validateSettings($this->request->post);
			if(!$validate['result']){
				if(!isset($validate['errors'])){
					$this->error['warning'] = $this->language->get('error_required_field');
				}else{
					$this->error['warning'] = array();
					foreach($validate['errors'] as $field_id => $error_text){
						$error = $error_text ? $error_text : $this->language->get($field_id.'_validation_error') ;
						$this->error['warning'][] = $error;
					}
					$this->error['warning'] = implode('<br>',$this->error['warning']);
				}
				return false;
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function install() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('extension/extensions')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
		} else {
			$validate = $this->extension_manager->validate($this->request->get['extension']);
			if (!$validate) {
				$this->session->data['error'] = implode('<br>', $this->extension_manager->errors);
				$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
			}
			$config = getExtensionConfigXml($this->request->get['extension']);
			if ($config === false) {
				$filename = DIR_EXT . str_replace('../', '', $this->request->get['extension']) . '/config.xml';
				$err = sprintf('Error: Could not load config for <b>%s</b> ( ' . $filename . ')!', $this->request->get['extension']);
				$this->session->data['error'] = $err;
			} else {
				$this->extension_manager->install($this->request->get['extension'], $config);
			}
			$this->redirect($this->html->getSecureURL('extension/extensions/edit', '&extension=' . $this->request->get['extension']));
		}
	}

	public function uninstall() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('extension/extensions')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
		} else {
			$ext = new ExtensionUtils($this->request->get['extension']);
			$this->extension_manager->uninstall($this->request->get['extension'], $ext->getConfig());
			$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function delete() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canModify('extension/extensions')) {
			$this->session->data['error'] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
		} else {
			//extensions that has record in DB but missing files
			$missing_extensions = $this->extensions->getMissingExtensions();

			if ((!in_array($this->request->get['extension'], $missing_extensions)) && $this->config->has($this->request->get['extension'] . '_status')) {
				$this->session->data['error'] = $this->language->get('error_uninstall');
				$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
			}
			$ext = new ExtensionUtils($this->request->get['extension']);
			if (in_array($this->request->get['extension'], $missing_extensions)) {
				$this->extension_manager->uninstall($this->request->get['extension'], $ext->getConfig());
			}

			$this->extension_manager->delete($this->request->get['extension']);
			$this->redirect($this->html->getSecureURL('extension/extensions/' . $this->session->data['extension_filter']));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

	}

}
