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

if (!ini_get('safe_mode')) {
	set_time_limit(0);
}

class ControllerResponsesCommonResourceLibrary extends AController {
	public $data = array();
	// TODO: need to find solution for this hardcoded preview sizes
	public $thumb_sizes = array('width' => 100, 'height' => 100);

	public function main() {
		if (isset($this->session->data['rl_types'])) {
			// if types of resources was limited
			$this->data['types'] = $this->session->data['rl_types'];
		} else {
			$rm = new AResourceManager();
			$this->data['types'] = $rm->getResourceTypes();
		}

		if (isset($this->request->server['HTTPS'])
			&&
			(($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {

			$this->data['base'] = HTTPS_SERVER;
			$this->data['ssl'] = 1;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}

		$this->data['object_name'] = $this->data['name'] = (string)$this->request->get['object_name'];

		$this->data['object_id'] = $this->request->get['object_id'];
		if ($this->request->get['object_title']) {
			$this->data['object_title'] = mb_substr($this->request->get['object_title'], 0, 60);
		} else {
			$this->data['object_title'] = mb_substr($this->_getObjectTitle($this->request->get['object_name'], $this->request->get['object_id']), 0, 60);
		}

		$this->data['resource_id'] = $this->request->get['resource_id'];
		$this->data['mode'] = $this->request->get['mode'];
		$this->data['add'] = isset($this->request->get['add']) ? $this->request->get['add'] : false;
		$this->data['update'] = isset($this->request->get['update']) ? $this->request->get['update'] : false;
		$this->data['rl_add'] = $this->html->getSecureURL('common/resource_library/add');
		$this->data['rl_resources'] = $this->html->getSecureURL('common/resource_library/resources');
		$this->data['rl_delete'] = $this->html->getSecureURL('common/resource_library/delete');
		$this->data['rl_get_resource'] = $this->html->getSecureURL('common/resource_library/get_resource_details');
		$this->data['rl_get_preview'] = $this->html->getSecureURL('common/resource_library/get_resource_preview');
		$this->data['rl_update_resource'] = $this->html->getSecureURL('common/resource_library/update_resource_details');
		$this->data['rl_update_sort_order'] = $this->html->getSecureURL('common/resource_library/update_sort_order');
		$this->data['rl_map'] = $this->html->getSecureURL('common/resource_library/map', '&object_name=' . $this->request->get['object_name'] . '&object_id=' . $this->request->get['object_id']);
		$this->data['rl_unmap'] = $this->html->getSecureURL('common/resource_library/unmap', '&object_name=' . $this->request->get['object_name'] . '&object_id=' . $this->request->get['object_id']);
		$this->data['default_type'] = $this->request->get['type'];

		//search form
		$form = new AForm('ST');
		$this->data['search_form_open'] = $form->getFieldHtml(
															array(
																'type' => 'form',
																'name' => 'searchform',
																'action' => '',
															));
		$this->data['search_field_input'] = $form->getFieldHtml(
			array(  'type'=>'input',
					'name'=>'search',
					'placeholder'=>$this->language->get('text_search'),
					'icon'=>'icon-search')
		);

		$this->data['language_id'] = $language_id = (int)$this->config->get('storefront_language_id');

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

		$this->data['button_go'] = $this->html->buildButton(
			array(
				'name' => 'searchform_go',
				'text' => $this->language->get('button_go'),
				'style' => 'button5'
			));
		//end search form

		$this->data['button_go_actions'] = $this->html->buildButton(
			array(
				'name' => 'go',
				'text' => $this->language->get('button_go'),
				'style' => 'button5'
			));
		$this->data['button_add_resource'] = $this->html->buildButton(array(
			'name' => 'add',
			'text' => $this->language->get('button_add'),
			'style' => 'button'
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

		//Resource edit form fields
		$form = new AForm('ST');
		$this->data['edit_form_open' ] = $form->getFieldHtml(
														array(
		                                                    'type' => 'form',
		                                                    'name' => 'editRlFrm',
		                                                    'action' => '',
		                                                ));

		$this->data['field_resource_code'] = $form->getFieldHtml(
						array(  'type'=>'textarea',
								'name'=>'resource_code',
								'required'=>true)
		);
		$this->data['field_name'] = $form->getFieldHtml(
						array(  'type'=>'input',
								'name'=>'name',
								'required'=>true)
		);
		$this->data['field_name'] .= $form->getFieldHtml(
						array(  'type'=>'hidden',
								'name'=>'resource_id')
		);

		$this->data['field_title'] = $form->getFieldHtml(
			array(  'type'=>'input',
					'name'=>'title')
		);
		$this->data['field_description'] = $form->getFieldHtml(
			array(  'type'=>'textarea',
					'name'=>'description'
				)
		);
		$this->data['rl_get_info'] = $this->html->getSecureURL('common/resource_library/get_resource_details');

		$this->data['batch_actions'] = $this->html->buildSelectbox(
			array(
				'name' => 'actions',
				'value' => 'map',
				'options' => array(
					''=>$this->language->get('text_select'),
					'map'=>$this->language->get('text_map'),
					'unmap'=>$this->language->get('text_unmap'),
					'delete'=>$this->language->get('button_delete')
				)
			));

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library.tpl');
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

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}

		$this->data['languages'] = array();
		$result = $this->language->getAvailableLanguages();
		foreach ($result as $lang) {
			$this->data['languages'][$lang['language_id']] = $lang;
		}
		$rm = new AResourceManager();
		$this->data['types'] = $rm->getResourceTypes();
		$this->data['type'] = $this->request->get['type'];
		$this->data['language_id'] = $this->config->get('storefront_language_id');

		$this->data['image_width'] = $this->config->get('config_image_grid_width');
		$this->data['image_height'] = $this->config->get('config_image_grid_height');

		$params = '&type='.$this->request->get['type'].'&object_name='.$this->request->get['object_name'].'&object_id=' . $this->request->get['object_id'];
		$this->data['rl_add_code'] = $this->html->getSecureURL('common/resource_library/add_code', $params);
		$this->data['rl_get_info'] = $this->html->getSecureURL('common/resource_library/get_resource_details');
		$this->data['rl_upload'] = $this->html->getSecureURL('common/resource_library/upload', $params);
		if ((int)ini_get('post_max_size') <= 2) { // because 2Mb is default value for php
			$this->data['attention'] = sprintf($this->language->get('error_file size'), ini_get('post_max_size'));
		}

		$this->view->batchAssign($this->data);

		$this->processTemplate('responses/common/resource_library_add.tpl');
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
			if (!empty($r->error)) continue;
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
			}

		}

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

		$this->request->post['add_code'] = true;
		$this->request->post['resource_code'] = html_entity_decode($this->request->post['resource_code'], ENT_COMPAT, 'UTF-8');

		$rm = new AResourceManager();
		$rm->setType($this->request->get['type']);
		$data = $this->request->post;
		$data['name'] = array($this->request->post['language_id'] => $this->request->post['name']);
		$data['title'] = array($this->request->post['language_id'] => $this->request->post['title']);
		$data['description'] = array($this->request->post['language_id'] => $this->request->post['description']);
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
			$this->request->post['error'] = $this->language->get('error_not_added');
		}

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($this->request->post));
	}

	public function delete() {
		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$rm = new AResourceManager();
		if (!empty($this->request->get['resource_id'])) {
			$this->request->post['resources'] = array($this->request->get['resource_id']);
		}
		foreach ($this->request->post['resources'] as $resource_id) {
			$rm->deleteResource($resource_id);
		}

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode(true));
	}

	public function map() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

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

		$rm = new AResourceManager();
		$rm->updateSortOrder($this->request->post['sort_order'],
			$this->request->get['object_name'],
			$this->request->get['object_id']
		);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode(true));
	}

	public function resources() {

		$rm = new AResourceManager();
		$rm->setType($this->request->get['type']);

		$pagination_param = '&type=' . $this->request->get['type'] . '&language_id=' . $this->request->get['language_id'];

		$search_data = array(
			'type_id' => $rm->getTypeId(),
			'language_id' => $this->request->get['language_id'],
		);
		if (!empty($this->request->get['keyword'])) {
			$search_data['keyword'] = $this->request->get['keyword'];
			$pagination_param .= '&keyword=' . $this->request->get['keyword'];
		}
		if (!empty($this->request->get['object_name'])) {
			$search_data['object_name'] = $this->request->get['object_name'];
			$pagination_param .= '&object_name=' . $this->request->get['object_name'];
		}
		if (!empty($this->request->get['object_id'])) {
			$search_data['object_id'] = $this->request->get['object_id'];
			$pagination_param .= '&object_id=' . $this->request->get['object_id'];
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			if ((int)$page < 1) {
				$page = 1;
			}
			$search_data['page'] = $page;
			$search_data['limit'] = 12;
		}

		$result = array(
			'items' => $rm->getResourcesList($search_data),
			'pagination' => '',
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
		}

		if (isset($this->request->get['page'])) {

			$resources_total = $rm->getResourcesList($search_data, true);
			if ($resources_total > 12) {
				$result['pagination'] = (string)HtmlElementFactory::create(array(
					'type' => 'Pagination',
					'name' => 'pagination',
					'text' => $this->language->get('text_pagination'),
					'text_limit' => $this->language->get('text_per_page'),
					'total' => $resources_total,
					'page' => $page,
					'limit' => 12,
					'url' => $this->html->getSecureURL('common/resource_library/resources', $pagination_param . '&page={page}'),
					'style' => 'pagination'));
			}
		}

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}


	public function get_resource_details() {
		$rm = new AResourceManager();
		$language_id = (int)$this->request->get['language_id'];
		if (!$language_id) {
			$language_id = $this->config->get('storefront_language_id');
		}

		$result = $rm->getResource($this->request->get['resource_id'], $language_id);

		$rm->setType($result['type_name']);
		$result['thumbnail_url'] = $rm->getResourceThumb(
			$result['resource_id'],
			$this->thumb_sizes['width'],
			$this->thumb_sizes['height']
		);
		$result['url'] = $rm->buildResourceURL($result['resource_path'], 'full');
		$result['relative_url'] = $rm->buildResourceURL($result['resource_path'], 'relative');
		
		if (!empty($this->request->get['resource_objects'])) {
			$result['resource_objects'] = $rm->getResourceObjects($result['resource_id'], $this->request->get['language_id']);
			if (!$result['resource_objects']) {
				unset($result['resource_objects']);
			}
		}

		$result['language_id'] = $language_id;

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function get_resource_preview() {

		$rm = new AResourceManager();
		$result = $rm->getResource($this->request->get['resource_id'], $this->config->get('storefront_language_id'));
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
					$this->response->addHeader('Content-type: ' . mime_content_type($path_parts["basename"]));
					$this->response->addHeader("Content-Disposition: filename=\"" . $result['name'] . '.' . $path_parts["extension"] . "\"");
					$this->response->addHeader("Content-length: $fsize");
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

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($result));
	}

	public function get_resources_html() {
		$rm = new AResourceManager();
		$this->data['types'] = $rm->getResourceTypes();

		$this->view->assign('current_url', $this->html->currentURL());
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_html.tpl');
	}

	/**
	 * variant for single picture with url mode
	 * @param string $type
	 * @param string $wrapper_id
	 * @param int $resource_id
	 * @param string $field
	 */
	public function get_resource_html_single($type = 'image', $wrapper_id = '', $resource_id = 0, $field = '') {
		$this->data['type'] = $type;
		$wrapper_id = is_numeric($wrapper_id[0]) ? '_'.$wrapper_id : $wrapper_id; // id do not to start from number!!! jquery will not work
		$this->data['wrapper_id'] = $wrapper_id;
		$this->data['resource_id'] = $resource_id;
		$this->data['field'] = $field;
		$this->data['types'] = array($type);
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_html_single.tpl');
	}

	public function get_resources_scripts() {

		list($object_name,$object_id,$types,$mode) = func_get_args();

		$rm = new AResourceManager();
		$this->data['types'] = $rm->getResourceTypes();

		if (!empty($types)) {
			foreach ($this->data['types'] as $key => $type) {
				if (!in_array($type['type_name'], (array)$types)) {
					unset($this->data['types'][$key]);
				}
			}
		}
		$this->session->data['rl_types'] = $this->data['types'];
		$this->data['mode'] = preg_replace('/[^a-z]/', '', $mode);
		$this->data['default_type'] = reset($this->data['types']);
		$this->data['object_name'] = $object_name;
		$this->data['object_id'] = $object_id;

		$params = '&object_name=' . $object_name . '&object_id=' . $object_id . '&mode=' . $mode;
		$this->data['rl_resource_library'] = $this->html->getSecureURL('common/resource_library', $params);
		$this->data['rl_resources'] = $this->html->getSecureURL('common/resource_library/resources', $params);
		$this->data['rl_resource_single'] = $this->html->getSecureURL('common/resource_library/get_resource_details', $params);
		$this->data['rl_delete'] = $this->html->getSecureURL('common/resource_library/delete');
		$this->data['rl_unmap'] = $this->html->getSecureURL('common/resource_library/unmap', $params);

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