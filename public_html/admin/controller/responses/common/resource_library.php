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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}

if (!ini_get('safe_mode')) {
	set_time_limit(0);
}

class ControllerResponsesCommonResourceLibrary extends AController {
	public $data = array();

	public function main() {

		if (isset($this->session->data[ 'rl_types' ])) {
			// if types of resources was limited
			$this->data[ 'types' ] = $this->session->data[ 'rl_types' ];
		} else {
			$rm = new AResourceManager();
			$this->data[ 'types' ] = $rm->getResourceTypes();
		}


		if (isset($this->request->server[ 'HTTPS' ]) && (($this->request->server[ 'HTTPS' ] == 'on') || ($this->request->server[ 'HTTPS' ] == '1'))) {
			$this->data[ 'base' ] = HTTPS_SERVER;
			$this->data[ 'ssl' ] = 1;
		} else {
			$this->data[ 'base' ] = HTTP_SERVER;
		}

		$this->data[ 'object_name' ] = $this->data[ 'name' ] = (string)$this->request->get[ 'object_name' ];

		$this->data[ 'object_id' ] = $this->request->get[ 'object_id' ];
		if ($this->request->get[ 'object_title' ]) {
			$this->data[ 'object_title' ] = mb_substr($this->request->get[ 'object_title' ], 0, 60);
		} else {
			$this->data[ 'object_title' ] = mb_substr($this->_getObjectTitle($this->request->get[ 'object_name' ], $this->request->get[ 'object_id' ]), 0, 60);
		}

		$this->data[ 'resource_id' ] = $this->request->get[ 'resource_id' ];
		$this->data[ 'mode' ] = $this->request->get[ 'mode' ];
		$this->data[ 'add' ] = isset($this->request->get[ 'add' ]) ? $this->request->get[ 'add' ] : false;
		$this->data[ 'update' ] = isset($this->request->get[ 'update' ]) ? $this->request->get[ 'update' ] : false;
		$this->data[ 'rl_add' ] = $this->html->getSecureURL('common/resource_library/add');
		$this->data[ 'rl_resources' ] = $this->html->getSecureURL('common/resource_library/resources');
		$this->data[ 'rl_delete' ] = $this->html->getSecureURL('common/resource_library/delete');
		$this->data[ 'rl_get_resource' ] = $this->html->getSecureURL('common/resource_library/get_resource_details');
		$this->data[ 'rl_get_preview' ] = $this->html->getSecureURL('common/resource_library/get_resource_preview');
		$this->data[ 'rl_update_resource' ] = $this->html->getSecureURL('common/resource_library/update_resource_details');
		$this->data[ 'rl_update_sort_order' ] = $this->html->getSecureURL('common/resource_library/update_sort_order');
		$this->data[ 'rl_map' ] = $this->html->getSecureURL('common/resource_library/map', '&object_name=' . $this->request->get[ 'object_name' ] . '&object_id=' . $this->request->get[ 'object_id' ]);
		$this->data[ 'rl_unmap' ] = $this->html->getSecureURL('common/resource_library/unmap', '&object_name=' . $this->request->get[ 'object_name' ] . '&object_id=' . $this->request->get[ 'object_id' ]);
		$this->data[ 'default_type' ] = $this->request->get[ 'type' ];
		$this->data[ 'language_id' ] = $this->config->get('storefront_language_id');

		$this->data[ 'languages' ] = array();
		$result = $this->language->getAvailableLanguages();
		foreach ($result as $lang) {
			$this->data[ 'languages' ][ $lang[ 'language_id' ] ] = $lang;
			$languages[ $lang[ 'language_id' ] ] = $lang[ 'name' ];
		}

		$this->data[ 'language' ] =
				$this->html->buildSelectbox(
					array(
						'id' => 'language_id',
						'name' => 'language_id',
						'options' => $languages,
						'value' => array( $this->config->get('storefront_language_id') => $this->config->get('storefront_language_id') ),
					));

		$this->data[ 'button_go' ] = $this->html->buildButton(
			array(
				'name' => 'searchform_go',
				'text' => $this->language->get('button_go'),
				'style' => 'button5'
			));
		$this->data[ 'button_go_actions' ] = $this->html->buildButton(
			array(
				'name' => 'go',
				'text' => $this->language->get('button_go'),
				'style' => 'button5'
			));
		$this->data[ 'button_add_resource' ] = $this->html->buildButton(array(
			'name' => 'add',
			'text' => $this->language->get('button_add'),
			'style' => 'button'
		));
		$this->data[ 'button_done' ] = $this->html->buildButton(
			array(
				'name' => 'done',
				'text' => $this->language->get('button_done'),
				'style' => 'button1'
			));
		$this->data[ 'button_select_resources' ] = $this->html->buildButton(
			array(
				'name' => 'select',
				'text' => $this->language->get('button_select'),
				'style' => 'button3'
			));
		$this->data[ 'button_save_order' ] = $this->html->buildButton(
			array(
				'name' => 'save_sort_order',
				'text' => $this->language->get('text_save_sort_order'),
				'style' => 'button1_small'
			));
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library.tpl');
	}

	public function add() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		if (isset($this->request->server[ 'HTTPS' ]) && (($this->request->server[ 'HTTPS' ] == 'on') || ($this->request->server[ 'HTTPS' ] == '1'))) {
			$this->data[ 'base' ] = HTTPS_SERVER;
		} else {
			$this->data[ 'base' ] = HTTP_SERVER;
		}

		$this->data[ 'languages' ] = array();
		$result = $this->language->getAvailableLanguages();
		foreach ($result as $lang) {
			$this->data[ 'languages' ][ $lang[ 'language_id' ] ] = $lang;
		}
		$rm = new AResourceManager();
		$this->data[ 'types' ] = $rm->getResourceTypes();
		$this->data[ 'type' ] = $this->request->get[ 'type' ];
		$this->data[ 'language_id' ] = $this->config->get('storefront_language_id');

		$this->data[ 'image_width' ] = $this->config->get('config_image_grid_width');
		$this->data[ 'image_height' ] = $this->config->get('config_image_grid_height');

		$this->data[ 'rl_add_code' ] = $this->html->getSecureURL('common/resource_library/add_code', '&type=' . $this->request->get[ 'type' ] . '&object_name=' . $this->request->get[ 'object_name' ] . '&object_id=' . $this->request->get[ 'object_id' ]);
		$this->data[ 'rl_upload' ] = $this->html->getSecureURL('common/resource_library/upload', '&type=' . $this->request->get[ 'type' ] . '&object_name=' . $this->request->get[ 'object_name' ] . '&object_id=' . $this->request->get[ 'object_id' ]);
		if ((int)ini_get('post_max_size') <= 2) { // because 2Mb is default value for php
			$this->data[ 'attention' ] = sprintf($this->language->get('error_file size'), ini_get('post_max_size'));
		}

		$this->view->batchAssign($this->data);

		$this->processTemplate('responses/common/resource_library_add.tpl');
	}


	public function upload() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}
		$rm = new AResourceManager();
		$rm->setType($this->request->get[ 'type' ]);

		$upload_handler = new ResourceUploadHandler(
			array(
				'script_url' => $this->html->getSecureURL('common/resource_library/delete', '&type=' . $this->request->get[ 'type' ]),
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
		switch ($this->request->server[ 'REQUEST_METHOD' ]) {
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

		$languages = $this->language->getAvailableLanguages();

		foreach ($result as $k => $r) {
			if (!empty($r->error)) continue;
			$data = array(
				'resource_path' => $r->name,
				'resource_code' => '',
				'language_id' => $this->config->get('storefront_language_id'),
			);
			foreach ($languages as $lang) {
				$data[ 'name' ][ $lang[ 'language_id' ] ] = $r->name;
				$data[ 'title' ][ $lang[ 'language_id' ] ] = '';
				$data[ 'description' ][ $lang[ 'language_id' ] ] = '';
			}
			$resource_id = $rm->addResource($data);
			if ($resource_id) {
				$info = $rm->getResource($resource_id, $this->config->get('storefront_language_id'));

				$result[ $k ]->resource_id = $resource_id;
				$result[ $k ]->language_id = $this->config->get('storefront_language_id');
				$result[ $k ]->resource_detail_url = $this->html->getSecureURL('common/resource_library/update_resource_details', '&resource_id=' . $resource_id);
				$result[ $k ]->resource_path = $info[ 'resource_path' ];
				$result[ $k ]->thumbnail_url = $rm->getResourceThumb(
					$resource_id,
					$this->config->get('config_image_grid_width'),
					$this->config->get('config_image_grid_height')
				);
				if (!empty($this->request->get[ 'object_name' ]) && !empty($this->request->get[ 'object_id' ])) {
					$rm->mapResource($this->request->get[ 'object_name' ], $this->request->get[ 'object_id' ], $resource_id);
				}
			} else {
				$result[ $k ]->error = $this->language->get('error_not_added');
			}

		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));
	}

	public function add_code() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$this->request->post[ 'add_code' ] = true;
		$this->request->post[ 'resource_code' ] = html_entity_decode($this->request->post[ 'resource_code' ], ENT_COMPAT, 'UTF-8');

		$rm = new AResourceManager();
		$rm->setType($this->request->get[ 'type' ]);
		$resource_id = $rm->addResource($this->request->post);

		if ($resource_id) {
			$this->request->post[ 'resource_id' ] = $resource_id;
			$this->request->post[ 'resource_detail_url' ] = $this->html->getSecureURL('common/resource_library/update_resource_details', '&resource_id=' . $resource_id);
			$this->request->post[ 'thumbnail_url' ] = $rm->getResourceThumb(
				$resource_id,
				$this->config->get('config_image_grid_width'),
				$this->config->get('config_image_grid_height'),
				$this->request->post[ 'language_id' ]
			);
			if (!empty($this->request->get[ 'object_name' ]) && !empty($this->request->get[ 'object_id' ])) {
				$rm->mapResource($this->request->get[ 'object_name' ], $this->request->get[ 'object_id' ], $resource_id);
			}
		} else {
			$this->request->post[ 'error' ] = $this->language->get('error_not_added');
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($this->request->post));
	}

	public function delete() {
		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$rm = new AResourceManager();
		if (!empty($this->request->get[ 'resource_id' ])) {
			$this->request->post[ 'resources' ] = array( $this->request->get[ 'resource_id' ] );
		}
		foreach ($this->request->post[ 'resources' ] as $resource_id) {
			$rm->deleteResource($resource_id);
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode(true));
	}

	public function map() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$rm = new AResourceManager();
		if (!empty($this->request->get[ 'resource_id' ])) {
			$this->request->post[ 'resources' ] = array( $this->request->get[ 'resource_id' ] );
		}
		foreach ($this->request->post[ 'resources' ] as $resource_id) {
			$rm->mapResource(
				$this->request->get[ 'object_name' ],
				$this->request->get[ 'object_id' ],
				$resource_id
			);
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode(true));
	}

	public function unmap() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		if (!empty($this->request->get[ 'resource_id' ])) {
			$this->request->post[ 'resources' ] = array( $this->request->get[ 'resource_id' ] );
		}
		$rm = new AResourceManager();
		foreach ($this->request->post[ 'resources' ] as $resource_id) {
			$rm->unmapResource(
				$this->request->get[ 'object_name' ],
				$this->request->get[ 'object_id' ],
				$resource_id
			);
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode(true));
	}

	public function update_sort_order() {

		if (!$this->user->canModify('common/resource_library')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$rm = new AResourceManager();
		$rm->updateSortOrder($this->request->post[ 'sort_order' ],
			$this->request->get[ 'object_name' ],
			$this->request->get[ 'object_id' ]
		);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode(true));
	}

	public function resources() {

		$rm = new AResourceManager();
		$rm->setType($this->request->get[ 'type' ]);

		$pagination_param = '&type=' . $this->request->get[ 'type' ] . '&language_id=' . $this->request->get[ 'language_id' ];

		$search_data = array(
			'type_id' => $rm->getTypeId(),
			'language_id' => $this->request->get[ 'language_id' ],
		);
		if (!empty($this->request->get[ 'keyword' ])) {
			$search_data[ 'keyword' ] = $this->request->get[ 'keyword' ];
			$pagination_param .= '&keyword=' . $this->request->get[ 'keyword' ];
		}
		if (!empty($this->request->get[ 'object_name' ])) {
			$search_data[ 'object_name' ] = $this->request->get[ 'object_name' ];
			$pagination_param .= '&object_name=' . $this->request->get[ 'object_name' ];
		}
		if (!empty($this->request->get[ 'object_id' ])) {
			$search_data[ 'object_id' ] = $this->request->get[ 'object_id' ];
			$pagination_param .= '&object_id=' . $this->request->get[ 'object_id' ];
		}
		if (isset($this->request->get[ 'page' ])) {
			$page = $this->request->get[ 'page' ];
			if ((int)$page < 1) {
				$page = 1;
			}
			$search_data[ 'page' ] = $page;
			$search_data[ 'limit' ] = 12;
		}

		$result = array(
			'items' => $rm->getResourcesList($search_data),
			'pagination' => '',
		);

		foreach ($result[ 'items' ] as $key => $item) {
			$result[ 'items' ][ $key ][ 'thumbnail_url' ] = $rm->getResourceThumb(
				$item[ 'resource_id' ],
				$this->config->get('config_image_product_width'),
				$this->config->get('config_image_product_height'),
				$item[ 'language_id' ]
			);
		}

		if (isset($this->request->get[ 'page' ])) {

			$resources_total = $rm->getResourcesList($search_data, true);

			$pagination = new APagination();
			$pagination->total = $resources_total;
			$pagination->page = $page;
			$pagination->limit = 12;
			$pagination->num_links = 5;
			$pagination->text = $this->language->get('text_pagination');
			$pagination->text_limit = $this->language->get('text_per_page');
			$pagination->url = $this->html->getSecureURL('common/resource_library/resources', $pagination_param . '&page={page}');
			if ($resources_total > $pagination->limit) {
				$result[ 'pagination' ] = $pagination->render();
			}
		}

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));
	}


	public function get_resource_details() {

		$rm = new AResourceManager();

		$result = $rm->getResource($this->request->get[ 'resource_id' ], $this->request->get[ 'language_id' ]);
		$rm->setType($result[ 'type_name' ]);
		$result[ 'thumbnail_url' ] = $rm->getResourceThumb(
			$result[ 'resource_id' ],
			$this->config->get('config_image_product_width'),
			$this->config->get('config_image_product_height')
		);

		if (!empty($this->request->get[ 'resource_objects' ])) {
			$result[ 'resource_objects' ] = $rm->getResourceObjects($result[ 'resource_id' ], $this->request->get[ 'language_id' ]);
			if (!$result[ 'resource_objects' ]) {
				unset($result[ 'resource_objects' ]);
			}
		}

		if (!$result[ 'language_id' ]) {
			$result[ 'language_id' ] = $this->config->get('storefront_language_id');
		}
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));
	}

	public function get_resource_preview() {

		$rm = new AResourceManager();
		$result = $rm->getResource($this->request->get[ 'resource_id' ], $this->config->get('storefront_language_id'));
		if (!empty($result)) {
			$rm->setType($result[ 'type_name' ]);
			if (!empty($result[ 'resource_code' ])) {
				if (strpos($result[ 'resource_code' ], "http") === 0) {
					$this->redirect($result[ 'resource_code' ]);
				} else {
					$this->response->setOutput($result[ 'resource_code' ]);
				}
			} else {
				$file_path = DIR_RESOURCE . $rm->getTypeDir() . $result[ 'resource_path' ];
				$result[ 'name' ] = pathinfo($result[ 'name' ], PATHINFO_FILENAME);
				if (file_exists($file_path) && ($fd = fopen($file_path, "r"))) {
					$fsize = filesize($file_path);
					$path_parts = pathinfo($file_path);
					$this->response->addHeader('Content-type: ' . mime_content_type($path_parts[ "basename" ]));
					$this->response->addHeader("Content-Disposition: filename=\"" . $result[ 'name' ] . '.' . $path_parts[ "extension" ] . "\"");
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
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'common/resource_library'),
					'reset_value' => true
				));
		}

		$this->request->post[ 'resource_code' ] = html_entity_decode($this->request->post[ 'resource_code' ], ENT_COMPAT, 'UTF-8');

		$rm = new AResourceManager();
		$result = $rm->updateResource($this->request->get[ 'resource_id' ], $this->request->post);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($result));
	}

	public function get_resources_html() {
		$rm = new AResourceManager();
		$this->data[ 'types' ] = $rm->getResourceTypes();

		$this->view->assign('current_url', $this->html->currentURL() );
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_html.tpl');
	}

	// variant for single picture with url mode
	public function get_resource_html_single($type = 'image', $wrapper_id = '', $resource_id = 0, $field = '') {
		$this->data[ 'type' ] = $type;
		$this->data[ 'wrapper_id' ] = $wrapper_id;
		$this->data[ 'resource_id' ] = $resource_id;
		$this->data[ 'field' ] = $field;
		$this->data[ 'types' ] = array( $type );
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_html_single.tpl');
	}

	public function get_resources_scripts() {

		$num_args = func_num_args();
		if ($num_args > 0) $object_name = func_get_arg(0);
		if ($num_args > 1) $object_id = func_get_arg(1);
		if ($num_args > 2) $types = func_get_arg(2);
		if ($num_args > 3) $mode = func_get_arg(3);

		$rm = new AResourceManager();
		$this->data[ 'types' ] = $rm->getResourceTypes();

		if (!empty($types)) {
			foreach ($this->data[ 'types' ] as $key => $type) {
				if (!in_array($type[ 'type_name' ], (array)$types)) {
					unset($this->data[ 'types' ][ $key ]);
				}
			}
		}
		$this->session->data[ 'rl_types' ] = $this->data[ 'types' ];
		$this->data[ 'mode' ] = preg_replace('/[^a-z]/', '', $mode);
		$this->data[ 'default_type' ] = reset($this->data[ 'types' ]);
		$this->data[ 'object_name' ] = $object_name;
		$this->data[ 'object_id' ] = $object_id;


		$this->data[ 'rl_resource_library' ] = $this->html->getSecureURL('common/resource_library', '&object_name=' . $object_name . '&object_id=' . $object_id . '&mode=' . $mode);
		$this->data[ 'rl_resources' ] = $this->html->getSecureURL('common/resource_library/resources', '&object_name=' . $object_name . '&object_id=' . $object_id . '&mode=' . $mode);
		$this->data[ 'rl_resource_single' ] = $this->html->getSecureURL('common/resource_library/get_resource_details', '&object_name=' . $object_name . '&object_id=' . $object_id . '&mode=' . $mode);
		$this->data[ 'rl_delete' ] = $this->html->getSecureURL('common/resource_library/delete');
		$this->data[ 'rl_unmap' ] = $this->html->getSecureURL('common/resource_library/unmap', '&object_name=' . $object_name . '&object_id=' . $object_id . '&mode=' . $mode);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/common/resource_library_scripts.tpl');
	}

	private function _getObjectTitle($object_name, $object_id) {
		if (is_callable(array( $this, '_get' . $object_name . 'Title' ))) {
			return call_user_func_array(array( $this, '_get' . $object_name . 'Title' ), array( $object_id ));
		} else
			return '';
	}

	private function _getProductsTitle($object_id) {
		$this->loadModel('catalog/product');
		$description = $this->model_catalog_product->getProductDescriptions($object_id);
		return $description[ $this->config->get('storefront_language_id') ][ 'name' ];
	}

	private function _getCategoriesTitle($object_id) {
		$this->loadModel('catalog/category');
		$description = $this->model_catalog_category->getCategoryDescriptions($object_id);
		return $description[ $this->config->get('storefront_language_id') ][ 'name' ];
	}

	private function _getStoreTitle($object_id) {
		if (!$object_id) {
			return $this->language->get('text_default');
		}
		$this->loadModel('setting/store');
		$store_info = $this->model_setting_store->getStore($object_id);
		return $store_info[ 'config_title' ];
	}

	private function _getManufacturersTitle($object_id) {
		$this->loadModel('catalog/manufacturer');
		$description = $this->model_catalog_manufacturer->getManufacturer($object_id);
		return $description[ 'name' ];
	}

}