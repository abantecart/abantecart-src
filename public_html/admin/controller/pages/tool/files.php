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

class ControllerPagesToolFiles extends AController {
	public $data;

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('tool/files');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->initBreadcrumb();
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('index/home'),
				'text' => $this->language->get('text_home'),
				'separator' => FALSE));
		$this->document->addBreadcrumb(array(
				'href' => $this->html->getSecureURL('tool/files'),
				'text' => $this->language->get('heading_title'),
				'separator' => ' :: ',
				'current'	=> true

		));

		$grid_settings = array(
			//id of grid
				'table_id' => 'file_uploads',
			// url to load data from
				'url' => $this->html->getSecureURL('listing_grid/file_uploads'),
			// url to send data for edit / delete
				'editurl' => '',
				'multiselect' => 'false',
			// url to update one field
				'update_field' => '',
			// default sort column
				'sortname' => 'date_added',
			// actions
				'actions' => '',
				'columns_search' => false,
				'sortable' => true);

		$grid_settings ['colNames'] = array(
				'#',
				$this->language->get('column_date_added'),
				$this->language->get('column_section'),
				$this->language->get('column_path'),
		);
		$grid_settings ['colModel'] = array(
				array(
						'name' => 'row_id',
						'index' => 'row_id',
						'width' => 10,
						'align' => 'left',
						'sortable' => false,
						'search' => false
				),
				array(
						'name' => 'date_added',
						'index' => 'date_added',
						'width' => 50,
						'align' => 'center',
						'sortable' => false,
						'search' => false
				),
				array(
						'name' => 'section',
						'index' => 'section',
						'width' => 50,
						'align' => 'center',
						'sortable' => false
				),
				array(
						'name' => 'path',
						'index' => 'path',
						'width' => 20,
						'align' => 'center',
						'sortable' => false,
						'search' => false
				),
		);

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		$this->view->assign('help_url', $this->gen_help_url());

		if (isset($this->session->data['error'])) {
			$this->view->assign('error_warning', $this->session->data['error']);
			unset($this->session->data['error']);
		}
		if (isset($this->session->data['success'])) {
			$this->view->assign('success', $this->session->data['success']);
			unset($this->session->data['success']);
		}


		$this->view->batchAssign($this->language->getASet());

		$this->processTemplate('pages/tool/files.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function download() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if ($this->user->canAccess('tool/files')) {
			$filename = str_replace(array('../', '..\\', '\\', '/'), '', $this->request->get['filename']);

			if ($this->request->get['attribute_type'] == 'field') {
				$this->loadModel('tool/file_uploads');
				$attribute_data = $this->model_tool_file_uploads->getField($this->request->get['attribute_id']);
			} elseif (strpos($this->request->get['attribute_type'], 'AForm:') === 0) {
				// for aform fields
				$form_info = explode(':', $this->request->get['attribute_type']);
				$aform = new AForm('ST');
				$aform->loadFromDb($form_info[1]);
				$attribute_data = $aform->getField($form_info[2]);
			}
			// if request file from order details page, file is product option value
			elseif ($this->request->get['order_option_id']) {
				$this->loadModel('sale/order');
				$attribute_data = $this->model_sale_order->getOrderOption($this->request->get['order_option_id']);
				$attribute_data['settings'] = unserialize($attribute_data['settings']);
			} else {
				$am = new AAttribute($this->request->get['attribute_type']);
				$attribute_data = $am->getAttribute($this->request->get['attribute_id']);
			}

			if (has_value($attribute_data['settings']['directory'])) {
				$file = DIR_APP_SECTION . 'system/uploads/' . $attribute_data['settings']['directory'] . '/' . $filename;
			} else {
				$file = DIR_APP_SECTION . 'system/uploads/' . $filename;
			}

			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/x-gzip');
				header('Content-Disposition: attachment; filename=' . $filename);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_end_clean();
				flush();
				readfile($file);
				exit;
			} else {
				echo 'Error: File '.$file.' does not exists!';
				exit;
			}
		} else {
			return $this->dispatch('error/permission');
		}
	}
}