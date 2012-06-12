<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesExtensionExtensions extends AController {

	public $data;
	private $error;
	public function main() {

		if(!in_array($this->session->data['extension_filter'], array('extensions', 'payment', 'shipping','template'))){
			$this->session->data['extension_filter'] = 'extensions';
		}
		unset($this->session->data['package_info']);

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->initBreadcrumb(array(
		                                     'href' => $this->html->getSecureURL('index/home'),
		                                     'text' => $this->language->get('text_home'),
		                                     'separator' => FALSE
		                                ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']),
		                                    'text' => $this->language->get('heading_title'),
		                                    'separator' => ' :: '
		                               ));

		if (isset($this->session->data[ 'success' ])) {
			$this->data[ 'success' ] = $this->session->data[ 'success' ];
			unset($this->session->data[ 'success' ]);
		} else {
			$this->data[ 'success' ] = '';
		}

		if (isset($this->session->data[ 'error' ])) {
			$this->data[ 'error_warning' ] = $this->session->data[ 'error' ];
			unset($this->session->data[ 'error' ]);
		} else {
			$this->data[ 'error_warning' ] = '';
		}

		$grid_settings = array(
							'table_id' => 'extension_grid',
							'url' => $this->html->getSecureURL('listing_grid/extension'),
							'editurl' => $this->html->getSecureURL('listing_grid/extension/update'),
							'update_field' => $this->html->getSecureURL('listing_grid/extension/update'),
							'sortname' => 'update_date',
							'sortorder' => 'desc',
							'multiselect' => 'false',
		);

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'extension_grid_search' ));

		$grid_search_form = array();
		$grid_search_form[ 'id' ] = 'extension_grid_search';
		$grid_search_form[ 'form_open' ] = $form->getFieldHtml(array( 'type' => 'form',
		                                                              'name' => 'extension_grid_search',
		                                                              'action' => '' ));
		$grid_search_form[ 'submit' ] = $form->getFieldHtml(array( 'type' => 'button',
		                                                           'name' => 'submit',
		                                                           'text' => $this->language->get('button_go'),
		                                                           'style' => 'button1' ));
		$grid_search_form[ 'reset' ] = $form->getFieldHtml(array( 'type' => 'button',
		                                                          'name' => 'reset',
		                                                          'text' => $this->language->get('button_reset'),
		                                                          'style' => 'button2' ));

		$grid_settings[ 'search_form' ] = true;

		$grid_settings[ 'colNames' ] = array( '',
		                                      $this->language->get('column_id'),
		                                      $this->language->get('column_name'),
		                                      $this->language->get('column_category'),
		                                      $this->language->get('column_update_date'));
		if(!$this->config->get('config_store_id')){
			$grid_settings[ 'colNames' ][] = $this->language->get('tab_store');
		}
		$grid_settings[ 'colNames' ][] = $this->language->get('column_status');
		$grid_settings[ 'colNames' ][] = $this->language->get('column_action');


		$grid_settings[ 'colModel' ] = array(
											array( 'name' => 'icon',
											       'index' => 'icon',
											       'width' => 50,
											       'align' => 'center',
											       'sortable' => false,
											       'search' => false ),
											array( 'name' => 'id',
											       'index' => 'id',
											       'width' => 120,
											       'align' => 'center',
											       'search' => true ),
											array( 'name' => 'name',
											       'index' => 'name',
											       'width' => 200,
											       'align' => 'center',
											       'search' => false ),
											array( 'name' => 'category',
											       'index' => 'category',
											       'width' => 90,
											       'align' => 'center',
											       'search' => false ),
											array( 'name' => 'update_date',
											       'index' => 'update_date',
											       'width' => 110,
											       'align' => 'center',
											       'search' => false ));
		if(!$this->config->get('config_store_id')){
			$grid_settings[ 'colModel' ][] = array( 'name' => 'store',
											       'index' => 'store',
											       'width' => 70,
											       'align' => 'center',
											       'search' => false );
		}
		$grid_settings[ 'colModel' ][] =	array( 'name' => 'status',
											       'index' => 'status',
											       'width' => 120,
											       'align' => 'center',
											       'search' => false );
		$grid_settings[ 'colModel' ][] =	array( 'name' => 'action',
											       'index' => 'action',
											       'width' => 100,
											       'align' => 'center',
											       'sortable' => false,
											       'search' => false );

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->data[ 'btn_extensions_store' ] = $this->html->buildButton(array( 'name' => 'btn_ext_store',
		                                                                        'text' => $this->language->get('text_extensions_store'),
		                                                                        'style' => 'button1' ));

		$this->data[ 'btn_add_new' ] = $this->html->buildButton(array( 'name' => 'text_add_new',
		                                                               'text' => $this->language->get('text_add_new'),
		                                                               'style' => 'button1' ));
		$this->data[ 'extensions_store' ] = $this->html->getSecureURL('extension/extensions_store');
		$this->data[ 'install_new' ] = $this->html->getSecureURL('tool/package_installer');
		$this->data[ 'license_url' ] = $this->html->getSecureURL('listing_grid/extension/license');
		$this->data[ 'close' ] = $this->html->buildButton(array('name' => 'close',
			                                                             'text' => $this->language->get('button_close'),
			                                                             'attr' => ' onclick=" $aPopup.dialog(\'destroy\');" ',
																		 'style' => 'button2'
																	));
		$this->data[ 'cancel_install' ] = $this->html->buildButton(array('name' => 'cancel',
			                                                             'text' => $this->language->get('button_cancel'),
			                                                             'attr' => ' onclick=" $aPopup.dialog(\'destroy\');" ',
																		 'style' => 'button2'
																	));
		$this->data[ 'agree_install' ] = $this->html->buildButton(array('name' => 'agree',
			                                                             'text' => $this->language->get('button_agree'),
																		 'style' => 'button1'
																	));


		$this->view->assign('help_url', $this->gen_help_url('extension_listing') );
		$this->view->batchAssign($this->data);

		$this->processTemplate('pages/extension/extensions.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	/**
	 * method set in session extension type for extension list filtering
	 * @return void
	 */
	public function extensions(){ 
		$this->session->data['extension_filter'] = 'extensions';
		$this->main();
	}
	public function payment(){
		$this->session->data['extension_filter'] = 'payment';
		$this->main();
	}
	public function shipping(){
		$this->session->data['extension_filter'] = 'shipping';
		$this->main();
	}
	public function template(){
		$this->session->data['extension_filter'] = 'template';
		$this->main();
	}

	public function edit() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->addScript( $this->view->templateResource('/javascript/jquery/thickbox/thickbox-compressed.js') );
		$this->document->addStyle(
			array(
				 'href' => $this->view->templateResource('/javascript/jquery/thickbox/thickbox.css'),
				 'rel' => 'stylesheet',
				 'media' => 'screen',
			)
		);


		$this->document->resetBreadcrumbs();
		$this->document->addBreadcrumb( array( 'href' => $this->html->getSecureURL('index/home'),
		                                       'text' => $this->language->get('text_home'),
		                                       'separator' => FALSE ) );
		$this->document->addBreadcrumb( array( 'href' => $this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']),
		                                       'text' => $this->language->get('heading_title'),
		                                       'separator' => ' :: ' ) );

		$extension = $this->request->get[ 'extension' ];
		$this->loadLanguage($extension . '/' . $extension);

		$store_id = (int)$this->config->get('config_store_id');
		if(!$this->config->get('config_store_id')){ // if store_id is default - take store_id from get. Else - do not permit switch store on edit page
			$store_id = isset($this->request->post['store_id']) ? (int)$this->request->post['store_id'] : (int)$this->request->get['store_id'];
		}
        unset($this->request->get['store_id']);

		$ext = new ExtensionUtils($extension, $store_id );
		$settings = $ext->getSettings();
		$extension_info = $this->extensions->getExtensionInfo($extension);
		if(!$extension_info){ // if extension is not installed yet - redirect to list
			$this->redirect($this->html->getSecureURL('extension/extensions'));
		}

		/** build aform **/
		$this->session->data[ 'extension_required_fields' ] = array();
		$result = array('resource_field_list'=>array());

		// store switcher for default store Cntrol Panel only
		if(!$this->config->get('config_store_id')){
			$stores = array();

			$stores[0] = $this->language->get('text_default');
			$this->loadModel('setting/store');
			$results = $this->model_setting_store->getStores();
			foreach ($results as $res) {
				$stores[$res['store_id']] = $res['name'];
			}
			$switcher = array( 'name' => 'store_id',
			                   'type' => 'selectbox',
							   'options'=> $stores,
			                   'value' => $store_id,
							   'note' => $this->language->get('tab_store'),
							   'style' => 'no-save' );
		}else{
			$switcher = array( 'type' => 'hidden',
			                     'name' => 'store_id',
			                     'value' => $store_id);
		}
		array_unshift($settings,$switcher);
		foreach($settings as $item){
				$data = array();
                if($item['name'] == $extension.'_status'){
                    $status = $item['value'];
                }
				$data['name'] = $item['name'];
				$data['type'] = $item['type'];
				$data['value'] = $item['value'];


				if($item[ 'note' ]){
					$data[ 'note' ] = $item[ 'note' ];
				}
				if($item[ 'style' ]){
					$data[ 'style' ] = $item[ 'style' ];
				}
				if($item[ 'attr' ]){
					$data[ 'attr' ] = $item[ 'attr' ];
				}
				if($item[ 'readonly' ]){
					$data[ 'readonly' ] = $item[ 'readonly' ];
				}

				switch ($data[ 'type' ]) {
					case 'selectbox':
						// if options need to extract from db
						$data[ 'options' ] =  $item['options'];
						if ($item['model_rt'] != '') {
							$this->loadModel($item['model_rt']);
							$model = $this->{'model_' . str_replace("/", "_",$item['model_rt'])};
							$method_name = $item['method'];
							if (method_exists($model, $method_name)) {
								$res = call_user_func(array( $model, $method_name ));
								if ($res) {
									$field1 = $item['field1'];
									$field2 = $item['field2'];
									foreach ($res as $opt) {
										$data[ 'options' ][ $opt[ $field1 ] ] = $opt[ $field2 ];
									}
								}
							}
						}

						break;
					case 'resource':
						$item['resource_type'] = (string)$item['resource_type'];
						if(!$result['rl_scripts']){
							$scripts = $this->dispatch( 'responses/common/resource_library/get_resources_scripts',
														array('object_name' => '',
														  	  'object_id' => '',
															  'types' => $item['resource_type'],
															  'mode' => 'url'
														));
							$result['rl_scripts'] = $scripts->dispatchGetOutput();
							unset($scripts);
						}
						//preview of resource
						$resource = new AResource($item['resource_type']);
						$resource_id = $resource->getIdFromHexPath(str_replace($item['resource_type'].'/','',$item[ 'value' ]));
						$preview = $this->dispatch(
							'responses/common/resource_library/get_resource_html_single',
							array('type'=>'image',
								  'wrapper_id'=>$item[ 'name' ],
								  'resource_id'=> $resource_id,
								  'field' => $item[ 'name' ]));
						$item[ 'value' ] = $preview->dispatchGetOutput();
						if ($data['value']) {
							$data = array( 'name' => $item[ 'name' ],
										   'type' => 'hidden' );
							if($resource_id){
								$resource_info = $resource->getResource($resource_id);
								$data['value'] = $item['resource_type'].'/'.$resource_info['resource_path'];
							}
						}
						$result['resource_field_list'][$item[ 'name' ]]['value'] = $item[ 'value' ];
						$result['resource_field_list'][$item[ 'name' ]]['resource_type'] = $item[ 'resource_type' ];
						$result['resource_field_list'][$item[ 'name' ]]['resource_id'] = $resource_id;

						break;
					default:
				}

				$item = HtmlElementFactory::create($data);
				$result[ 'html' ][ $data[ 'name' ] ] = array('note'  => ($data[ 'note' ] ? $data[ 'note' ] : $this->language->get($data[ 'name' ])),
				                                             'value' => $item->getHtml());
		}


		// end building aform
        $this->data[ 'settings' ] = $result[ 'html' ];
        $this->data[ 'resource_field_list' ] = $result[ 'resource_field_list' ];
		$this->data[ 'resource_edit_link' ] =
        $this->data['resources_scripts'] = $result['rl_scripts'];
		$this->data['target_url'] = $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension.'&store_id='.$store_id);

		if ( isset($this->request->get['restore']) && $this->request->get['restore']  ) {
			$this->extension_manager->editSetting($extension, $ext->getDefaultSettings() );
			$this->cache->delete('settings.extension');
			$this->session->data[ 'success' ] = $this->language->get('text_restore_success');
			$this->redirect($this->data['target_url']);
		}

		if (($this->request->server[ 'REQUEST_METHOD' ] == 'POST') && ($this->_validateSettings()) && $this->_checkRequiredSettings()) {
			foreach ($settings as $item) {
				if (!isset($this->request->post[ $item['name'] ])){
					$this->request->post[ $item['name'] ] = 0;
				}

			}
			$this->extension_manager->editSetting($extension, $this->request->post);
			$this->cache->delete('settings.extension');
			$this->session->data[ 'success' ] = $this->language->get('text_success');
			$this->redirect($this->data['target_url']);
		}

        $conflict_resources = $ext->validateResources();
        if ( !empty( $conflict_resources ) ) {
            ob_start();
            print_r($conflict_resources);
            $err = ob_get_clean();
            ADebug::warning('resources conflict', AC_ERR_USER_WARNING, $extension.' Extension resources conflict detected.<br/><pre>'. $err.'</pre>');
        }

		$this->document->setTitle($this->language->get($extension . '_name'));

		$this->document->addBreadcrumb(array(
		                                    'href' => $this->data['target_url'],
		                                    'text' => $this->language->get($extension . '_name'),
		                                    'separator' => ' :: '
		                               ));

		$this->data[ 'heading_title' ] = $this->language->get($extension . '_name');
		$this->data[ 'text_version' ] = $this->language->get('text_version');
		$this->data[ 'text_installed_on' ] = $this->language->get('text_installed_on');
		$this->data[ 'text_date_added' ] = $this->language->get('text_date_added');
		$this->data[ 'text_license' ] = $this->language->get('text_license');
		$this->data[ 'text_dependency' ] = $this->language->get('text_dependency');
		$this->data[ 'text_configuration_settings' ] = $this->language->get('text_configuration_settings');

		$this->data[ 'button_back' ] = $this->html->buildButton(array( 'name' => 'btn_back', 'text' => $this->language->get('text_back'), 'style' => 'button2' ));
		$this->data[ 'button_reload' ] = $this->html->buildButton(array( 'name' => 'btn_reload', 'text' => $this->language->get('text_reload'), 'style' => 'button2' ));
		$this->data[ 'button_restore_defaults' ] = $this->html->buildButton(array( 'name' => 'button_restore_defaults', 'text' => $this->language->get('button_restore_defaults'), 'style' => 'button2' ));
		$this->data[ 'button_save' ] = $this->html->buildButton(array( 'name' => 'btn_save', 'text' => $this->language->get('button_save'), 'style' => 'button1' ));
		$this->data[ 'button_save_green' ] = $this->html->buildButton(array( 'name' => 'btn_save', 'text' => $this->language->get('button_save'), 'style' => 'button3' ));
		$this->data[ 'button_reset' ] = $this->html->buildButton(array( 'name' => 'btn_reset', 'text' => $this->language->get('text_reset'), 'style' => 'button2' ));
		$this->data[ 'reload' ] = $this->html->getSecureURL('extension/extensions/edit/', '&extension=' . $extension);
		$this->data[ 'back' ] = $this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']);
		$this->data[ 'update' ] = $this->html->getSecureURL('listing_grid/extension/update', '&id=' . $extension.'&store_id='.$store_id);

		$form = new AForm();
		$form->setForm(
			array(
		        'form_name' => 'editSettings',
		    )
		);

		$this->data[ 'form' ][ 'form_open' ] = $form->getFieldHtml(
			array(
				'type' => 'form',
				'name' => 'editSettings',
				'action' => $this->html->getSecureURL('extension/extensions/edit/','&action=save&extension=' . $extension.'&store_id='.$store_id)
		    )
		);


		if (isset($this->error[ 'warning' ])) {
			$this->data[ 'error_warning' ] = $this->error[ 'warning' ];
		} else {
			$this->data[ 'error_warning' ] = '';
		}

		if (isset($this->session->data[ 'success' ])) {
			$this->data[ 'success' ] = $this->session->data[ 'success' ];
			unset($this->session->data[ 'success' ]);
		} else {
			$this->data[ 'success' ] = '';
		}

		if (isset($this->session->data[ 'error' ])) {
			$this->data[ 'error_warning' ] = $this->session->data[ 'error' ];
			unset($this->session->data[ 'error' ]);
		} else {
			$this->data[ 'error' ] = '';
		}

		$icon_ext_img_url = HTTP_CATALOG . 'extensions/' . $extension . '/image/icon.png';
		$icon_ext_dir = DIR_EXT . $extension . '/image/icon.png';
		$icon = ( is_file ( $icon_ext_dir ) ? $icon_ext_img_url : RDIR_TEMPLATE . 'image/default_extension.png');
		$extension_data = array();

		$missing_extensions = $this->extensions->getMissingExtensions();
		if (!in_array($extension, $missing_extensions)) {
			$extension_data[ 'icon' ] = $icon;
			$extension_data[ 'name' ] = $this->language->get($extension . '_name');
			$extension_data[ 'version' ] = $extension_info['version'];
			$extension_data[ 'installed' ] = (strtotime($extension_info['date_installed']) ? date('F, d Y h:iA', strtotime($extension_info['date_installed'])) : '');
			$extension_data[ 'create_date' ] = (strtotime($extension_info['create_date']) ? date('F, d Y h:iA', strtotime($extension_info['create_date'])) : '');
			$extension_data[ 'license' ] = $extension_info['license_key'];
			$extension_data[ 'note' ] = $ext->getConfig('note') ? $this->html->convertLinks($this->language->get($extension . '_note')) : '';

			$config = $ext->getConfig();
			if ( !empty($config->preview->item) ) {
				foreach ($config->preview->item as $item) {
					if ( !is_file(DIR_EXT . $extension . DIR_EXT_IMAGE . (string)$item))
						continue;
					$extension_data[ 'preview' ][] = HTTPS_EXT . $extension . DIR_EXT_IMAGE . (string)$item;
				}
			}

			if(isset($this->session->data['extension_updates'][$extension])){

				$extension_data[ 'upgrade' ] = array(
					'text' => $this->html->buildButton( array ( 'id' => 'upgradenow',
					                                            'name' => 'btn_upgrade',
					                                            'text' => $this->language->get('button_upgrade'),
					                                            'style' => 'button1' )),
					'link' => AEncryption::addEncoded_stid($this->session->data['extension_updates'][$extension]['url']));
			}

            if ( $status ) {
                $extension_data[ 'help' ] = array(
                    'text' => $this->html->buildButton(
                        array( 'name' => 'btn_help', 'text' => $this->language->get('text_help'), 'style' => 'button2' )
                    ),
                    'ext_link' => $ext->getConfig('help_link'),
                );
                if ( $ext->getConfig('help_file') ) {
                	$extension_data[ 'help' ]['file'] = true;
                	$extension_data[ 'help' ]['file_link'] = $this->html->getSecureURL('extension/extension/help', '&extension='.$this->request->get['extension']);
                	$this->data['text_more_help'] = $this->language->get('text_more_help');
                }
            }

			$extension_data[ 'dependencies' ] = array();
			$extension_data[ 'extensions' ] = $this->extensions->getEnabledExtensions();
			$missing_extensions = $this->extensions->getMissingExtensions();
			$db_extensions= $this->extensions->getDbExtensions();

			if ( isset($config->dependencies->item) ) {
				foreach( $config->dependencies->item as $item )
				{
					$id = (string)$item;
					if (in_array( $id, $db_extensions)) {
						if (in_array($id, $missing_extensions)) {
							$class = 'warning';
							$action = str_replace('%EXT%', $id, $this->language->get('text_missing_extension')) .
							          '<a class="btn_action" target="_blank" href="'.$this->html->getSecureURL('extension/extensions/delete', '&extension=' . $id).'"
										onclick="return confirm(\''.$this->language->get('text_delete_confirm').'\')" title="'. $this->language->get('text_delete') . '">'.
									  '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_delete.png" alt="'. $this->language->get('text_delete') . '" />'.
									  '</a>';
						} else {

							if ( !$this->config->has($id . '_status')) {
								$class = 'attention';
								$action = '<a class="btn_action" target="_blank" href="'.$this->html->getSecureURL('extension/extensions/install', '&extension=' . $id).'"
								title="'. $this->language->get('text_install') . '">'.
									  '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_install.png" alt="'. $this->language->get('text_install') . '" />'.
									  '</a>'.
									  '<a class="btn_action" target="_blank" href="'.$this->html->getSecureURL('extension/extensions/delete', '&extension=' . $id).'"
									  onclick="return confirm(\''.$this->language->get('text_delete_confirm').'\')" title="'. $this->language->get('text_delete') . '">'.
									  '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_delete.png" alt="'. $this->language->get('text_delete') . '" />'.
									  '</a>';
							} else {
								$action = '<a id="action_edit_'.$id.'" target="_blank" class="btn_action"
												href="'.$this->html->getSecureURL('extension/extensions/edit', '&extension=' . $id).'"
												title="'. $this->language->get('text_edit') . '">'.
									        '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_edit.png" alt="'. $this->language->get('text_edit') . '" /></a>';
								if(!(boolean)$item['required']){
								$action .=  '<a class="btn_action" target="_blank" href="'.$this->html->getSecureURL('extension/extensions/uninstall', '&extension=' . $id).'"
									  onclick="return confirm(\''.str_replace('%extension%', $id,$this->language->get('text_uninstall_confirm')).'\')"
									  title="'. $this->language->get('text_uninstall') . '">'.
									  '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_uninstall.png" alt="'. $this->language->get('text_uninstall') . '" />'.
									  '</a>';
								}
							}
						}
					} else {
						$action = $this->language->get('text_visit_repository');
					}

					$extension_data[ 'dependencies' ][] = array(
						'required' => (boolean)$item['required'],
						'id' => $id,
						'status' => ($this->config->has($id . '_status') ? $this->language->get('text_installed') : $this->language->get('text_not_installed')) .' ('.($this->config->get($id . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled')).' )'  ,
						'action' => $action,
						'class' => $class,
					);
					unset($class);

				}
			}

		}else{ // if extension missing
			$extension_data[ 'icon' ] = $icon;
			$extension_data[ 'name' ] = str_replace('%EXT%', $extension, $this->language->get('text_missing_extension'));
		}
		// additional settings page

		if($ext->getConfig('additional_settings') && $status ){
			$btn_param = array( 'name' => 'btn_addsett',
				                'text' => $this->language->get('text_additional_settings'),
			                    'style' => 'button1' );

			$this->data[ 'add_sett' ]['link'] = $this->html->getSecureURL($ext->getConfig('additional_settings'));
            if($store_id){
                $this->loadModel('setting/store');
                $store_info = $this->model_setting_store->getStore($store_id);
                $this->data[ 'add_sett' ]['link'] = $store_info['config_url'].'?s='.ADMIN_PATH.'&rt='.$ext->getConfig('additional_settings');
                $this->data[ 'add_sett' ]['onclick'] = 'onclick="return confirm(\''.$this->language->get('additional_settings_confirm').'\');"';
            }
            $this->data[ 'add_sett' ]['text'] = $this->html->buildButton( $btn_param );
		}
		$this->data[ 'extension' ] = $extension_data;
        $this->data['target_url'] = $this->html->getSecureURL('extension/extensions/edit', '&extension=' . $extension);
        $this->view->assign('help_url', $this->gen_help_url('extension_edit') );
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/extension/extensions_edit.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateSettings() {
		if (!$this->user->hasPermission('modify', 'extension/extensions')) {
			$this->error[ 'warning' ] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function _checkRequiredSettings() {
		if($this->session->data['extension_required_fields']){
			foreach($this->session->data['extension_required_fields'] as $field_name){
				if(!isset($this->request->post[$field_name]) || empty($this->request->post[$field_name])){
					$this->error[ 'warning' ] = $this->language->get('error_required_field');
					return false;
				}
			}
		}

		return true;
	}

	public function install() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->user->hasPermission('modify', 'extension/extensions')) {
			$this->session->data[ 'error' ] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
		} else {
			$ext = new ExtensionUtils($this->request->get[ 'extension' ]);
			$ext->validate();
			$validateErrors = $ext->getError();
			if (!empty($validateErrors)) {
				$this->session->data[ 'error' ] = implode('<br>', $validateErrors);
				$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
			}
			$config = $ext->getConfig();
			if($config===false){
				$this->session->data[ 'error' ] = implode('<br>', $ext->getError());
			}else{
                $this->extension_manager->install( $this->request->get[ 'extension' ], $config );
			}
			$this->redirect($this->html->getSecureURL('extension/extensions/edit', '&extension=' . $this->request->get[ 'extension' ]));
		}
	}

	public function uninstall() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->user->hasPermission('modify', 'extension/extensions')) {
			$this->session->data[ 'error' ] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
		} else {
			$ext = new ExtensionUtils($this->request->get[ 'extension' ]);
			$this->extension_manager->uninstall( $this->request->get[ 'extension' ], $ext->getConfig() );
			$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function delete() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->user->hasPermission('modify', 'extension/extensions')) {
			$this->session->data[ 'error' ] = $this->language->get('error_permission');
			$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
		} else {
			//extensions that has record in DB but missing files
			$missing_extensions = $this->extensions->getMissingExtensions();

			if ((!in_array($this->request->get[ 'extension' ], $missing_extensions)) && $this->config->has($this->request->get[ 'extension' ] . '_status')) {
				$this->session->data[ 'error' ] = $this->language->get('error_uninstall');
				$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
			}
			$ext = new ExtensionUtils($this->request->get[ 'extension' ]);
			if (in_array($this->request->get[ 'extension' ], $missing_extensions)) {
				$this->extension_manager->uninstall( $this->request->get[ 'extension' ], $ext->getConfig() );
			}
			$this->extension_manager->delete( $this->request->get[ 'extension' ] );
			$this->redirect($this->html->getSecureURL('extension/extensions/'.$this->session->data['extension_filter']));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

	}

}

?>