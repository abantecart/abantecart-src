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

if (!ini_get('safe_mode')) {
	set_time_limit(0);
}

class ControllerResponsesCommonResourceLibrary extends AController {
	public $data = array();
	// TODO: need to find solution for this hardcoded preview sizes
	public $thumb_sizes = array(
								'width' => 100,
								'height' => 100
								);

	public function main() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
	
		//route to correct function
		$this->data['resource_id'] = (int)$this->request->get['resource_id'];
		$language_id = (int)$this->request->get['language_id'];
		if (!$language_id) {
			$language_id = $this->language->getContentLanguageID();
		}
		$this->data['language_id'] = $language_id;
		$this->data['mode'] = $this->request->get['mode'];

		$rm = new AResourceManager();
		$this->_common($rm);

		//quick action routing
		$this->data['action'] = $this->request->get['action'];
		if ($this->data['action'] == 'list_object' || $this->data['action'] == 'list_library' ) {
			return $this->list_library();
		}
		if ($this->data['action'] == 'add' || (!$this->data['resource_id'] && $this->data['action'] != 'multisave') ) {
			return $this->add();
		}
		if ($this->data['action'] == 'save' && $this->data['resource_id']) {
			return $this->update_resource_details();
		}

		if ($this->data['action'] == 'multisave') {
			return $this->_multiple_update();
		}
		//edit is default action. proceed

		$this->data['current_url'] = $this->html->getSecureURL('common/resource_library','','&encode');


		$this->data['add'] = isset($this->request->get['add']) ? $this->request->get['add'] : false;
		$this->data['update'] = isset($this->request->get['update']) ? $this->request->get['update'] : false;
		$this->data['rl_add'] = $this->html->getSecureURL('common/resource_library/add');
		$this->data['rl_resources'] = $this->html->getSecureURL('common/resource_library/resources');
		$this->data['rl_delete'] = $this->html->getSecureURL('common/resource_library/delete');
		$this->data['rl_get_resource'] = $this->html->getSecureURL('common/resource_library/get_resource_details');
		$this->data['rl_get_preview'] = $this->html->getSecureURL('common/resource_library/get_resource_preview');
		$this->data['rl_update_resource'] = $this->html->getSecureURL('common/resource_library/update_resource_details');
		$this->data['rl_update_sort_order'] = $this->html->getSecureURL('common/resource_library/update_sort_order');
		$this->data['rl_map'] = $this->html->getSecureURL('common/resource_library/map', '&object_name=' . $this->data['object_name'] . '&object_id=' . $this->data['object_id']);
		$this->data['rl_unmap'] = $this->html->getSecureURL('common/resource_library/unmap', '&object_name=' . $this->data['object_name'] . '&object_id=' . $this->data['object_id']);
		$this->data['rl_upload'] = $this->html->getSecureURL('common/resource_library/upload', '&mode='.$this->data['mode'].'&type='.$this->request->get['type'].'&object_name='.$this->request->get['object_name'].'&object_id=' . $this->request->get['object_id']);
		$this->data['rl_replace'] = $this->html->getSecureURL('common/resource_library/replace', '&resource_id=' . $this->data['resource_id']);
		$this->data['type'] = $this->request->get['type'];

		//load resource
		$resource = $rm->getResource($this->data['resource_id'], $language_id);		
		/*
		Array
		(
		    [resource_id] => 100009
		    [language_id] => 1
		    [name] => az_demo_product_14_1.jpg
		    [title] => 
		    [description] => 
		    [resource_path] => 18/6a/9.jpg
		    [resource_code] => 
		    [date_added] => 2014-06-05 16:23:12
		    [date_modified] => 2014-06-05 16:23:12
		    [type_name] => image
		    [default_icon] => icon_resource_image.png
		)
		*/		
		$this->data['button_go_actions'] = $this->html->buildButton(
			array(
				'name' => 'go',
				'text' => $this->language->get('button_go'),
				'style' => 'button5'
			));
		$this->data['button_done'] = $this->html->buildButton(
			array(
				'name' => 'done',
				'text' => $this->language->get('button_done'),
				'style' => 'button1'
			));
		$this->data['button_select_resources'] = $this->html->buildButton(
			array(
				'name' => 'select',
				'text' => $this->language->get('button_select'),
				'style' => 'button3'
			));
		$this->data['button_save_order'] = $this->html->buildButton(
			array(
				'name' => 'save_sort_order',
				'text' => $this->language->get('text_save_sort_order'),
				'style' => 'button1_small'
			));


		$rm->setType($resource['type_name']);
		$resource['thumbnail_url'] = $rm->getResourceThumb(
			$resource['resource_id'],
			$this->thumb_sizes['width'],
			$this->thumb_sizes['height']
		);
		$resource['url'] = $rm->buildResourceURL($resource['resource_path'], 'full');
		$resource['relative_url'] = $rm->buildResourceURL($resource['resource_path'], 'relative');
		
		$resource['resource_objects'] = $rm->getResourceObjects($resource['resource_id'], $language_id);

		//mark if this resource mapped to selected object


		if($this->data['mode']!='single'){
			$resource['mapped_to_current'] = $rm->isMapped(
							$resource['resource_id'],
							$this->data['object_name'],
							$this->data['object_id']);

			// also check is mapped at all
			//NOTE: we allow to delete resource that mappend ONLY to current object
			$is_mapped = $rm->isMapped($resource['resource_id']);
			if( ($is_mapped==1 && $resource['mapped_to_current']) || !$is_mapped){
				$resource['can_delete'] = true;
			}else{
				$resource['can_delete'] = false;
			}
		}else{
			//check is mapped at all
			$resource['can_delete'] = $rm->isMapped($resource['resource_id'])>0 ? false : true;
		}

		$this->_buildForm($resource);

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('resource_library'));

		$this->data['resource'] = $resource;

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_edit.tpl');
	}

	/**
	 * @return null | void
	 */
	public function add() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);


		if($this->request->is_POST()){
			return $this->add_code();
		}



		$this->data['languages'] = array();
		$result = $this->language->getAvailableLanguages();
		foreach ($result as $lang) {
			$this->data['languages'][$lang['language_id']] = $lang;
		}
		$rm = new AResourceManager();
		if($this->request->get['mode']=='single'){
			$this->data['types'] = array($rm->getResourceTypeByName($this->request->get['type']));
		}else{
			$this->data['types'] = $this->session->data['rl_types'] ? $this->session->data['rl_types'] : $rm->getResourceTypes();
		}

		if(!$this->data['types']){
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array('error_text' => 'Incorrect resource library type list!',
					'reset_value' => true
				));
		}

		$this->data['type'] = $this->request->get['type'];
		$this->data['wrapper_id'] = $this->request->get['wrapper_id'] ? $this->request->get['wrapper_id'] : false;
		$this->data['field_id'] = $this->request->get['field_id'] ? $this->request->get['field_id'] : false;

		$this->data['language_id'] = $this->config->get('storefront_language_id');

		$this->data['image_width'] = $this->config->get('config_image_grid_width');
		$this->data['image_height'] = $this->config->get('config_image_grid_height');

		$params = '&mode='.$this->request->get['mode'].'&type='.$this->request->get['type'].'&object_name='.$this->request->get['object_name'].'&object_id=' . $this->request->get['object_id'];
		$this->data['rl_add_code'] = $this->html->getSecureURL('common/resource_library/add_code', $params);
		$this->data['rl_get_info'] = $this->html->getSecureURL('common/resource_library/get_resource_details');
		$this->data['rl_upload'] = $this->html->getSecureURL('common/resource_library/upload', $params);

		if ((int)ini_get('post_max_size') <= 2) { // because 2Mb is default value for php
			$this->data['attention'] = sprintf($this->language->get('error_file size'), ini_get('post_max_size'));
		}

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('global_attributes_listing'));

		$this->_buildForm();

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_add.tpl');
	}

	private function _buildForm($resource=array()){
		//Resource edit form fields
		$form = new AForm('HT');
		$form->setForm(array('form_name' => 'RlFrm'));
		$this->data['form']['form_open' ] = $form->getFieldHtml(
														array(
		                                                    'type' => 'form',
		                                                    'name' => 'RlFrm',
		                                                    'action' => '',
		                                                ));

		$this->data['form']['field_resource_code'] = $form->getFieldHtml(
						array(  'type'=> 'textarea',
								'name'=> 'resource_code',
								'value'=> $resource['resource_code'],
								'placeholder' => $this->language->get('text_resource_code'),
								'attr' =>' rows="10" cols="50" style="resize: none;"',
								'required'=>true)
		);
		$this->data['form']['field_name'] = $form->getFieldHtml(
						array(  'type'=> 'input',
								'name'=> 'name',
								'value'=> $resource['name'],
								'placeholder' => $this->language->get('text_name'),
								'required'=>true)
		);
		$this->data['form']['field_resource_id'] .= $form->getFieldHtml(
						array(  'type'=>'hidden',
								'value'=> $resource['resource_id'],
								'name'=>'resource_id')
		);

		$this->data['form']['field_title'] = $form->getFieldHtml(
			array(  'type'=> 'input',
					'value'=> $resource['title'],
					'placeholder' => $this->language->get('text_title'),
					'name'=> 'title')
		);
		$this->data['form']['field_description'] = $form->getFieldHtml(
			array(  'type'=>'textarea',
					'name'=>'description',
					'placeholder' => $this->language->get('text_description'),
					'value'=> $resource['description']
				)
		);
	}

	/**
	 * @throws AException
	 */
	public function list_library() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$language_id = $this->language->getContentLanguageID();


		$this->data['sort'] = $this->request->get['sort'];
		$this->data['order'] = $this->request->get['order'];

		$rm = new AResourceManager();
		$rm->setType($this->data['type']);

		//Build request URI and filter params
		$uri = '&object_name='.$this->data['object_name'].'&object_id='.$this->data['object_id'];
		$uri .= '&type='.$this->data['type'].'&mode='.$this->data['mode'].'&language_id='.$language_id.'&action='.$this->data['action'];
		$filter_data = array(
			'type_id' => $rm->getTypeId(),
			'language_id' => $language_id,
			'limit' => 12,
		);
		if (!empty($this->request->get['keyword'])) {
			$filter_data['keyword'] = $this->request->get['keyword'];
			$uri .= '&keyword=' . $this->request->get['keyword'];
		}
		if (!empty($this->data['object_name']) && $this->data['action'] == 'list_object') {
			$filter_data['object_name'] = $this->data['object_name'];
		}
		if (!empty($this->data['object_id']) && $this->data['action'] == 'list_object' ) {
			$filter_data['object_id'] = $this->data['object_id'];
		}
		if($this->data['sort']){
			$filter_data['sort'] = $this->data['sort'];
		}
		if($this->data['order']){
			$filter_data['order'] = $this->data['order'];
		}elseif(!$this->data['sort'] && $this->data['action'] == 'list_object'){
			$filter_data['sort'] = 'sort_order';
		}else{
			$filter_data['sort'] = 'date_added';
			$filter_data['order'] = 'DESC';
		}
		$full_uri = $uri;
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			if ((int)$page < 1) {
				$page = 1;
			}
			$filter_data['start'] = (($page - 1) * $filter_data['limit']) ;
			$full_uri .= '&page=' . $page;
		} else {
			$full_uri .= '&page=0';
		}

		$resources_total = $rm->getTotalResources($filter_data);
		$result = $rm->getResourcesList($filter_data);

		foreach ($result as $key => $item) {
			if ($item['date_added']) {
				$result[$key]['date_added'] = dateISO2Display($item['date_added']);
			}
			$result[$key]['thumbnail_url'] = $rm->getResourceThumb(
				$item['resource_id'],
				$this->thumb_sizes['width'],
				$this->thumb_sizes['height'],
				$language_id
			);
			$result[$key]['url'] = $rm->buildResourceURL($item['resource_path'], 'full');
			$result[$key]['relative_url'] = $rm->buildResourceURL($item['resource_path'], 'relative');
			$result[$key]['mapped_to_current'] = $rm->isMapped($item['resource_id'], $this->data['object_name'], $this->data['object_id']);
		}

		$sort_order = '&sort='.$this->data['sort'].'&order='.$this->data['order'];
		$full_uri .= $sort_order;
		$this->data['current_url'] = $this->html->getSecureURL('common/resource_library',$uri.$sort_order.'&page={page}','&encode');
		$this->data['no_sort_url'] = $this->html->getSecureURL('common/resource_library',$uri,'&encode');
		$this->data['full_url'] = $this->html->getSecureURL('common/resource_library',$full_uri,'&encode');
				
		if ($resources_total > 12) {
		$this->data['pagination_bootstrap'] = HtmlElementFactory::create(array(
		    			'type' => 'Pagination',
		    			'name' => 'pagination',
		    			'text' => $this->language->get('text_pagination'),
		    			'text_limit' => $this->language->get('text_per_page'),
						'total' => $resources_total,
						'page' => $page,
		    			'limit' => 12,
		    			'url' => $this->data['current_url'],
		    			'size_class' => 'sm',
		    			'no_perpage' => true,
		    			'style' => 'pagination'));
		}

		$this->data['rls'] = $result;

		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('resource_library'));

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library.tpl');
	}

	private function _common() {

		$rm = new AResourceManager();
		if($this->request->get['mode']=='single'){
			$this->data['types'] = array($rm->getResourceTypeByName($this->request->get['type']));
		}else{
			$this->data['types'] = isset($this->session->data['rl_types']) ? $this->session->data['rl_types'] : $rm->getResourceTypes();
		}

		$this->data['type'] = $this->request->get['type'];
		if( ($this->data['type']=='undefined' || empty($this->data['type'])) && $this->request->post_or_get('resource_id')){
			$info = $rm->getResource($this->request->post_or_get('resource_id'), $this->language->getContentLanguageID());
			$this->data['type'] = $info['type_name'];
		}elseif( $this->data['type']=='undefined' || empty($this->data['type']) ){
			$this->data['type'] = is_array($this->data['types']) ? (string)$this->data['types'][0] : $this->data['types'];
		}

		$this->data['object_name'] = $this->data['name'] = (string)$this->request->get['object_name'];
		$this->data['object_id'] = $this->request->get['object_id'];
		$this->data['object_title'] = $this->request->get['object_title'];
		if ($this->data['object_title']) {
			$this->data['object_title'] = mb_substr($this->data['object_title'], 0, 60);
		} else {
			$this->data['object_title'] = mb_substr($this->_getObjectTitle($this->data['object_name'], $this->data['object_id']), 0, 60);
		}
	
		//search form
		$form = new AForm('HS');
		$this->data['search_form'] = $form->getFieldHtml(
						array(
						    'type' => 'form',
						    'name' => 'rlsearchform',
						    'action' => '',
						));
		$this->data['search_field_input'] = $form->getFieldHtml(
			array(  'type'=>'input',
					'name'=>'search',
					'value'=> $this->request->get['keyword'],
					'placeholder'=>$this->language->get('text_search'),
					'icon'=>'icon-search')
		);

		$rm->setType($this->data['type']);
		$options = array();
		foreach ($this->data['types'] as $type ) {
			$options[$type['type_name']] = $type['type_name'];
		}
		$this->data['rl_types'] = $form->getFieldHtml(array(
		    'type' => 'selectbox',
		    'name' => 'rl_types',
		    'placeholder' => $this->language->get('text_type'),
            'options' => $options,
            'value' => $this->data['type'],
	    ));

		$this->data['languages'] = array();
		$result = $this->language->getAvailableLanguages();
		foreach ($result as $lang) {
			$this->data['languages'][$lang['language_id']] = $lang;
			$languages[$lang['language_id']] = $lang['name'];
		}

		$this->data['language'] = $this->html->buildSelectbox(
					array(
						'id' => 'language_id',
						'name' => 'language_id',
						'options' => $languages,
						'value' => $language_id
					));
	}

	/**
	 * @return mixed
	 */
	public function upload() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$rm = new AResourceManager();

		$rm->setType($this->request->get['type']);

		$upload_handler = new ResourceUploadHandler(
			array(
				'script_url' => $this->html->getSecureURL('common/resource_library/delete', '&type=' . $this->request->get['type']),
				'max_file_size' => (int)$this->config->get('config_upload_max_size') * 1024,
				'upload_dir' => $rm->getTypeDir(),
				'upload_url' => '',
				'accept_file_types' => $rm->getTypeFileTypes(),
			)
		);

		$this->response->addHeader('Pragma: no-cache');
		$this->response->addHeader('Cache-Control: private, no-cache');
		$this->response->addHeader('Content-Disposition: inline; filename="files.json"');
		$this->response->addHeader('X-Content-Type-Options: nosniff');

		$result = null;
		switch ($this->request->server['REQUEST_METHOD']) {
			case 'HEAD':
			case 'GET':
				$result = $upload_handler->get();
				break;
			case 'POST':
				$result = $upload_handler->post();
				break;
			case 'DELETE':
			case 'OPTIONS':
			default:
				$this->response->addHeader('HTTP/1.0 405 Method Not Allowed');
		}

		foreach ($result as $k => $r) {
			if (!empty($r->error)){
				$result[$k]->error_text = $this->language->get('error_'.$r->error);
				continue;
			}
			$data = array(
				'resource_path' => $r->name,
				'resource_code' => '',
				'language_id' => $this->config->get('storefront_language_id')
			);

			$data['name'][$data['language_id']] = $r->name;
			$data['title'][$data['language_id']] = '';
			$data['description'][$data['language_id']] = '';

			$resource_id = $rm->addResource($data);

			if ($resource_id) {
				$info = $rm->getResource($resource_id, $data['language_id']);

				$result[$k]->resource_id = $resource_id;
				$result[$k]->type = $info['type_name'];
				$result[$k]->language_id = $data['language_id'];
				$result[$k]->resource_detail_url = $this->html->getSecureURL('common/resource_library/update_resource_details', '&resource_id=' . $resource_id);
				$result[$k]->resource_path = $info['resource_path'];
				$result[$k]->thumbnail_url = $rm->getResourceThumb(
					$resource_id,
					$this->config->get('config_image_grid_width'),
					$this->config->get('config_image_grid_height')
				);
				if (!empty($this->request->get['object_name']) && !empty($this->request->get['object_id'])) {
					$rm->mapResource($this->request->get['object_name'], $this->request->get['object_id'], $resource_id);
				}
			} else {
				$result[$k]->error = $this->language->get('error_not_added');
				$result[$k]->error_text = $result[$k]->error;
			}

		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	/**
	 * @return mixed
	 */
	public function replace() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$resource_id = (int)$this->request->get['resource_id'];
		if(!$resource_id){
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array('error_text' => $this->language->get('error_not_replaced'),
					'reset_value' => false
				));
		}

		$rm = new AResourceManager();
		$info = $rm->getResource($resource_id, $this->language->getContentLanguageID());
		if(!$info){
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array('error_text' => $this->language->get('error_not_exists'),
					'reset_value' => false
				));
		}

		$rm->setType($info['type_name']);

		$upload_handler = new ResourceUploadHandler(
			array(
				'script_url' => $this->html->getSecureURL('common/resource_library/delete', '&type=' . $this->request->get['type']),
				'max_file_size' => (int)$this->config->get('config_upload_max_size') * 1024,
				'upload_dir' => $rm->getTypeDir(),
				'upload_url' => '',
				'accept_file_types' => $rm->getTypeFileTypes(),
			)
		);

		$this->response->addHeader('Pragma: no-cache');
		$this->response->addHeader('Cache-Control: private, no-cache');
		$this->response->addHeader('Content-Disposition: inline; filename="files.json"');
		$this->response->addHeader('X-Content-Type-Options: nosniff');

		$result = null;
		switch ($this->request->server['REQUEST_METHOD']) {
			case 'HEAD':
			case 'GET':
				$result = $upload_handler->get();
				break;
			case 'POST':
				$result = $upload_handler->post();
				break;
			case 'DELETE':
			case 'OPTIONS':
			default:
				$this->response->addHeader('HTTP/1.0 405 Method Not Allowed');
		}

		foreach ($result as $k => $r) {
			if (!empty($r->error)){
				$result[$k]->error_text = $this->language->get('error_'.$r->error);
				continue;
			}

			$result[$k]->resource_id = $resource_id;
			$result[$k]->type = $info['type_name'];

			//resource_path
			$resource_path = $rm->buildResourcePath($resource_id, $r->name);

			if ( !rename(DIR_RESOURCE . $info['type_name'] .'/'. $r->name, DIR_RESOURCE . $info['type_name']. '/' . $resource_path ) ) {
					$message = sprintf($this->language->get('error_cannot_move'), $r->name);
					$error = new AError ( $message );
					$error->toLog()->toDebug();
					$result[$k]->error_text = $message;
					continue;
			}
			$rm->updateResource($resource_id, array('resource_path' => $resource_path));
			//remove old file of resource
			if ( $info['resource_path'] && is_file( DIR_RESOURCE . $info['type_name'] . '/' . $info['resource_path']) && $info['resource_path']!=$resource_path ) {
				unlink( DIR_RESOURCE.$info['type_name'].'/'.$info['resource_path'] );
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function add_code() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->request->post['add_code'] = true;
		$this->request->post['resource_code'] = html_entity_decode($this->request->post['resource_code'], ENT_COMPAT, 'UTF-8');

		$rm = new AResourceManager();
		$rm->setType($this->request->get['type']);
		$data = $this->request->post;

		$language_id = (int)$this->request->post['language_id'];
		$language_id = !$language_id ? $this->language->getContentLanguageID() : $language_id;

		$data['name'] = array($language_id => $this->request->post['name']);
		$data['title'] = array($language_id => $this->request->post['title']);
		$data['description'] = array($language_id => $this->request->post['description']);
		$resource_id = $rm->addResource($data);

		if ($resource_id) {
			$this->request->post['resource_id'] = $resource_id;
			$this->request->post['resource_detail_url'] = $this->html->getSecureURL('common/resource_library/update_resource_details', '&resource_id=' . $resource_id);
			$this->request->post['thumbnail_url'] = $rm->getResourceThumb(
																			$resource_id,
																			$this->config->get('config_image_grid_width'),
																			$this->config->get('config_image_grid_height'),
																			$this->request->post['language_id']
			);
			if (!empty($this->request->get['object_name']) && !empty($this->request->get['object_id'])) {
				$rm->mapResource($this->request->get['object_name'], $this->request->get['object_id'], $resource_id);
			}
		} else {
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array('error_text' => $this->language->get('error_not_added'),
					'reset_value' => false
				));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($resource_id));
	}

	public function resources() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$rm = new AResourceManager();
		$rm->setType($this->request->get['type']);

		$uri = '&type=' . $this->request->get['type'] . '&language_id=' . $this->request->get['language_id'];

		$filter_data = array(
			'type_id' => $rm->getTypeId(),
			'language_id' => $this->request->get['language_id'],
		);
		if (!empty($this->request->get['keyword'])) {
			$filter_data['keyword'] = $this->request->get['keyword'];
			$uri .= '&keyword=' . $this->request->get['keyword'];
		}
		if (!empty($this->request->get['object_name'])) {
			$filter_data['object_name'] = $this->request->get['object_name'];
			$uri .= '&object_name=' . $this->request->get['object_name'];
		}
		if (!empty($this->request->get['object_id'])) {
			$filter_data['object_id'] = $this->request->get['object_id'];
			$uri .= '&object_id=' . $this->request->get['object_id'];
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			if ((int)$page < 1) {
				$page = 1;
			}
			$filter_data['page'] = $page;
			$filter_data['limit'] = 12;
		}
		if (!empty($this->request->get['sort'])) {
			$filter_data['sort'] = $this->request->get['sort'];		
		} else {
			$filter_data['sort'] = 'sort_order';		
		}

		$result = array(
			'items' => $rm->getResourcesList($filter_data),
			'pagination' => '',
			'object_name' => $this->request->get['object_name'],
			'object_id' => $this->request->get['object_id']
		);

		foreach ($result['items'] as $key => $item) {
			$result['items'][$key]['thumbnail_url'] = $rm->getResourceThumb(
				$item['resource_id'],
				$this->thumb_sizes['width'],
				$this->thumb_sizes['height'],
				$item['language_id']
			);
			$result['items'][$key]['url'] = $rm->buildResourceURL($item['resource_path'], 'full');
			$result['items'][$key]['relative_url'] = $rm->buildResourceURL($item['resource_path'], 'relative');
			$result['items'][$key]['can_delete'] = $result['items'][$key]['mapped']==1 ? true : false;

		}

		if (isset($this->request->get['page'])) {

			$resources_total = $rm->getResourcesList($filter_data, true);
			if ($resources_total > 12) {
				$result['pagination'] = (string)HtmlElementFactory::create(array(
					'type' => 'Pagination',
					'name' => 'pagination',
					'text' => $this->language->get('text_pagination'),
					'text_limit' => $this->language->get('text_per_page'),
					'total' => $resources_total,
					'page' => $page,
					'limit' => 12,
					'url' => $this->html->getSecureURL('common/resource_library/resources', $uri . '&page={page}'),
					'style' => 'pagination'));
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function delete() {
		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$rm = new AResourceManager();
		$resource_id = (int)$this->request->get['resource_id'];
		if(has_value($this->request->get['object_name']) && has_value($this->request->get['object_id'])){
			$rm->unmapResource( $this->request->get['object_name'],$this->request->get['object_id'], $resource_id);
		}
		$result = $rm->deleteResource($resource_id);

		if(!$result){
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array('error_text' => $this->language->get('text_cant_delete')));
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode( $result ));
	}

	public function map() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$rm = new AResourceManager();
		if (!empty($this->request->get['resource_id'])) {
			$this->request->post['resources'] = array($this->request->get['resource_id']);
		}
		foreach ($this->request->post['resources'] as $resource_id) {
			$rm->mapResource(
				$this->request->get['object_name'],
				$this->request->get['object_id'],
				$resource_id
			);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode(true));
	}

	public function unmap() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}


		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if(!has_value($this->request->get['object_name']) || !has_value($this->request->get['object_id']) ){
			$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406',
							array('error_text' => $this->language->get('error_unmap')));
		}

		if (!empty($this->request->get['resource_id'])) {
			$this->request->post['resources'] = array($this->request->get['resource_id']);
		}
		$rm = new AResourceManager();
		foreach ($this->request->post['resources'] as $resource_id) {
			$rm->unmapResource(
				$this->request->get['object_name'],
				$this->request->get['object_id'],
				$resource_id
			);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode(true));
	}

	public function update_sort_order() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}


		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$rm = new AResourceManager();
		$rm->updateSortOrder($this->request->post['sort_order'],
			$this->request->get['object_name'],
			$this->request->get['object_id']
		);

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode(true));
	}

	public function get_resource_preview() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$rm = new AResourceManager();
		$result = $rm->getResource($this->request->get['resource_id'], $this->language->getContentLanguageID());
		if (!empty($result)) {
			$rm->setType($result['type_name']);
			if (!empty($result['resource_code'])) {
				if (strpos($result['resource_code'], "http") === 0) {
					$this->redirect($result['resource_code']);
				} else {
					$this->response->setOutput($result['resource_code']);
				}
			} else {
				$file_path = DIR_RESOURCE . $rm->getTypeDir() . $result['resource_path'];
				$result['name'] = pathinfo($result['name'], PATHINFO_FILENAME);
				if (file_exists($file_path) && ($fd = fopen($file_path, "r"))) {
					$fsize = filesize($file_path);
					$path_parts = pathinfo($file_path);
					$this->response->addHeader('Content-type: application/octet-stream');
					$this->response->addHeader("Content-Disposition: filename=\"" . $result['name'] . '.' . $path_parts["extension"] . "\"");
					$this->response->addHeader("Content-length: ".$fsize);
					$this->response->addHeader("Cache-control: private"); //use this to open files directly
					$buffer = '';
					while (!feof($fd)) {
						$buffer .= fread($fd, 32768);
					}
					$this->response->setOutput($buffer);
				} else
					$this->response->setOutput($this->language->get('text_no_resources'));
				fclose($fd);
			}
		} else
			$this->response->setOutput($this->language->get('text_no_resources'));
	}

	public function update_resource_details() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if(!$this->request->post){
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => 'Error: No data to save!',
					'reset_value' => true
				));
		}
		if(!$this->request->post['name']){
			$error = new AError('');
			return $error->toJSONResponse('VALIDATION_ERROR_406',
				array(  'error_text' => $this->language->get('error_name'),
						'reset_value' => true
				));
		}

		$this->request->post['resource_code'] = html_entity_decode($this->request->post['resource_code'], ENT_COMPAT, 'UTF-8');

		$rm = new AResourceManager();
		$language_id = (int)$this->request->post['language_id'];
		$language_id = !$language_id ? $this->language->getContentLanguageID() : $language_id;
		if(!is_array($this->request->post['name'])){
			$this->request->post['name'] = array($language_id=>$this->request->post['name']);
			$this->request->post['title'] = array($language_id=>$this->request->post['title']);
			$this->request->post['description'] = array($language_id=>$this->request->post['description']);
		}

		$result = $rm->updateResource($this->request->get['resource_id'], $this->request->post);

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function _multiple_update() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$object_name = $this->request->get['object_name'];
		$object_id = $this->request->get['object_id'];

		$result = false;

		$rm = new AResourceManager();
		if($this->request->post['sort_order']){
			$result = $rm->updateSortOrder($this->request->post['sort_order'], $object_name, $object_id);
		}

		// $this->request->post['map'] must be an array of resource ids
		if( $this->request->post['map'] ){
			$result = $rm->mapResources( $this->request->post['map'], $object_name, $object_id);
		}

		// $this->request->post['unmap'] must be an array of resource ids
		if( $this->request->post['unmap'] ){
			$result = $rm->unmapResources( $this->request->post['unmap'], $object_name, $object_id);
		}
		// $this->request->post['delete'] must be an array of resource ids
		if( $this->request->post['delete'] ){
			$result = $rm->deleteResources( $this->request->post['delete'], $object_name, $object_id);
			if($result===false){
				$error = new AError('');
				return $error->toJSONResponse('VALIDATION_ERROR_406',
					array('error_text' => $rm->error, //returns text array to show all resources which cannot be deleted
						  'reset_value' => true
					));
			}
		}

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function get_resources_html() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if($this->session->data['rl_types']){
			$this->data['types'] = $this->session->data['rl_types'];
		}else{
			$rm = new AResourceManager();
			$this->data['types'] = $rm->getResourceTypes();
		}

		$this->view->assign('current_url', $this->html->currentURL());

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_html.tpl');
	}


	public function get_resource_details() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$resource_id = (int)$this->request->get['resource_id'];
		$language_id = $this->language->getContentLanguageID();

		$rm = new AResourceManager();
		$info = $rm->getResource($resource_id, $language_id);
		if(!$info){
			$info = null;
		}else{
			$rm->setType($info['type_name']);
			$info['thumbnail_url'] = $rm->getResourceThumb(
					$resource_id,
				$this->thumb_sizes['width'],
				$this->thumb_sizes['height'],
				$language_id
			);
		}

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($info));
	}

	public function get_resources_scripts() {
		/**
		 * @var string $object_name - name of RL-object for assistance of resources, for ex. products, categories, etc
		 * @var int $object_id - id of object
		 * @var array $types - array with RL-types (image, audio,video,archive etc)
		 * @var bool $onload - sign of call function after js-script load
		 * @var string $mode - mode of RL
		 */
		list($object_name,$object_id,$types, $onload, $mode) = func_get_args();

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->data['onload'] = is_bool($onload) ? $onload : true; //sign of call js-function on page load. default true.

		$rm = new AResourceManager();
		$this->data['types'] = $rm->getResourceTypes();

		if ($types) {
			$types = (array)$types;
			foreach ($this->data['types'] as $key => $type) {
				if (!in_array($type['type_name'], $types)) {
					unset($this->data['types'][$key]);
				}
			}
		}

		$this->session->data['rl_types'] = $this->data['types'];
		$this->data['default_type'] = reset($this->data['types']);
		$this->data['object_name'] = $object_name;
		$this->data['object_id'] = $object_id;
		$this->data['mode'] = $mode;

		$params = '&mode='.$mode.'&object_name=' . $object_name . '&object_id=' . $object_id;
		$this->data['rl_resource_library'] = $this->html->getSecureURL('common/resource_library', $params);
		$this->data['rl_resources'] = $this->html->getSecureURL('common/resource_library/resources', $params);
		$this->data['rl_resource_single'] = $this->html->getSecureURL('common/resource_library/get_resource_details', $params);
		$this->data['rl_delete'] = $this->html->getSecureURL('common/resource_library/delete');
		$this->data['rl_unmap'] = $this->html->getSecureURL('common/resource_library/unmap', $params);
		$this->data['rl_map'] = $this->html->getSecureURL('common/resource_library/map', $params);
		$this->data['rl_download'] = $this->html->getSecureURL('common/resource_library/get_resource_preview');
		$this->data['rl_upload'] = $this->html->getSecureURL('common/resource_library/upload', $params);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_scripts.tpl');
	}

	/**
	 * @param string $object_name
	 * @param int $object_id
	 * @return string
	 */
	private function _getObjectTitle($object_name, $object_id) {
		if (is_callable(array($this, '_get' . $object_name . 'Title'))) {
			/**
			 * @see _getProductsTitle()
			 * @see _getCategoriesTitle()
			 * @see _getStoreTitle()
			 * @see _getManufacturersTitle()
			 * @see _getDownloadsTitle()
			 */
			return call_user_func_array(array($this, '_get' . $object_name . 'Title'), array($object_id));
		} else
			return 'Add/Edit';
	}

	/**
	 * @param int $object_id
	 * @return string
	 */
	private function _getProductsTitle($object_id) {
		$this->loadModel('catalog/product');
		$description = $this->model_catalog_product->getProductDescriptions($object_id);
		return $description[$this->config->get('storefront_language_id')]['name'];
	}

	/**
	 * @param int $object_id
	 * @return string
	 */
	private function _getCategoriesTitle($object_id) {
		$this->loadModel('catalog/category');
		$description = $this->model_catalog_category->getCategoryDescriptions($object_id);
		return $description[$this->config->get('storefront_language_id')]['name'];
	}

	/**
	 * @param int $object_id
	 * @return string
	 */
	private function _getStoreTitle($object_id) {
		if (!$object_id) {
			return $this->language->get('text_default');
		}
		$this->loadModel('setting/store');
		$store_info = $this->model_setting_store->getStore($object_id);
		return $store_info['config_title'];
	}

	/**
	 * @param int $object_id
	 * @return string
	 */
	private function _getManufacturersTitle($object_id) {
		$this->loadModel('catalog/manufacturer');
		$description = $this->model_catalog_manufacturer->getManufacturer($object_id);
		return $description['name'];
	}
	/**
	 * @param int $object_id
	 * @return string
	 */
	private function _getDownloadsTitle($object_id) {
		$this->loadModel('catalog/download');
		$description = $this->model_catalog_download->getDownload($object_id);
		return $description['name'] ? $description['name'] : $this->language->get('text_new_download');
	}
}