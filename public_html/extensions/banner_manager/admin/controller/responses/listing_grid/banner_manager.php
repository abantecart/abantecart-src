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
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

/**
 * Class ControllerResponsesListingGridBannerManager
 * @property ModelExtensionBannerManager model_extension_banner_manager
 */
class ControllerResponsesListingGridBannerManager extends AController {
	public $data;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('banner_manager/banner_manager');

		$page = $this->request->post['page']; // get the requested page
		if ((int)$page < 0) $page = 0;
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid

		//sort
		$filter_params = array('name', 'banner_group_name', 'banner_type', 'status', 'date_modified');
		$filter_grid = new AFilter(array('method' => 'post',
			'grid_filter_params' => $filter_params,
			'additional_filter_string' => ''));

		$this->loadModel('extension/banner_manager');
		$total = $this->model_extension_banner_manager->getBanners($filter_grid->getFilterData(), 'total_only');

		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$results = $this->model_extension_banner_manager->getBanners($filter_grid->getFilterData());

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		$resource = new AResource('image');
		$i = 0;
		foreach ($results as $result) {

			$response->rows[$i]['id'] = $result['banner_id'];

			$thumbnail = $resource->getMainThumb('banners',
				$result['banner_id'],
				$this->config->get('config_image_grid_width'),
				$this->config->get('config_image_grid_height'),
				true);
			$thumbnail = $thumbnail['thumb_html'];

			//check if banner is active based on dates and update status
			$now = time();
			if (dateISO2Int($result['start_date']) > $now ) {
				$result['status'] = 0;
			}
			$stop =  dateISO2Int($result['end_date']);
			if($stop>0 && $stop<$now){
				$result['status'] = 0;
			}

			$response->rows[$i]['cell'] = array(
				$result['banner_id'],
				$thumbnail,
				$result['name'],
				$result['banner_group_name'],
				($result['banner_type'] == 1 ? $this->language->get('text_graphic_banner') : $this->language->get('text_text_banner')),
				$this->html->buildCheckbox(array(
					'name' => 'status[' . $result['banner_id'] . ']',
					'value' => $result['status'],
					'style' => 'btn_switch'
				)),
				$result['date_modified']
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if ($this->request->is_POST()) {
			$this->loadModel('extension/banner_manager');

			if (isset($this->request->post['start_date']) && $this->request->post['start_date']) {
				$this->request->post['start_date'] = dateDisplay2ISO($this->request->post['start_date']);
			}
			if (isset($this->request->post['end_date']) && $this->request->post['end_date']) {
				$this->request->post['end_date'] = dateDisplay2ISO($this->request->post['end_date']);
			}


			//request sent from edit form. ID in url
			foreach ($this->request->post as $field => $value) {
				if ($field == 'banner_group_name') {
					if (isset($value[0]) && !in_array($value[0], array('0', 'new'))) {
						$tmp = array('banner_group_name' => trim($value[0]));
					}
					if (isset($value[1])) {
						$tmp = array('banner_group_name' => trim($value[1]));
					}
					$id = (int)$this->request->get['banner_id'];
					$this->model_extension_banner_manager->editBanner($id, $tmp);

				} elseif (is_array($value)) {
					foreach ($value as $id => $val) {
						$tmp[$field] = (int)$val;
						$this->model_extension_banner_manager->editBanner($id, $tmp);
					}
				} else {
					if ((int)$this->request->get['banner_id']) {
						$this->model_extension_banner_manager->editBanner($this->request->get['banner_id'], array($field => $value));
					}
				}
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}


	public function edit() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('extension/banner_manager');
		$this->loadLanguage('banner_manager/banner_manager');
		if (!$this->user->canModify('extension/banner_manager')) {
			$this->response->setOutput(sprintf($this->language->get('error_permission_modify'), 'extension/banner_manager'));
			return null;
		}

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids))
					foreach ($ids as $id) {
						$this->model_extension_banner_manager->deleteBanner($id);
					}
				break;
			case 'save':
				$allowedFields = array('status');
				$ids = explode(',', $this->request->post['id']);
				if (!empty($ids))
					foreach ($ids as $id) {
						if (!isset($this->request->post['status'][$id])) $this->request->post['status'][$id] = 0;
						foreach ($allowedFields as $field) {
							$this->model_extension_banner_manager->editBanner($id, array($field => $this->request->post[$field][$id]));
						}
					}
				break;

			default:
				//print_r($this->request->post);

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/*
	 * response method, if response type is html - it send jqgrid, otherwise - json-data for grid
	 * */
	public function getListing() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->load->library('json');
		$form_name = 'BannerBlockFrm';
		$multivalue_hidden_id = isset($this->request->get['multivalue_hidden_id']) ? $this->request->get['multivalue_hidden_id'] : 'popup';


		$this->loadLanguage('banner_manager/banner_manager');
		//remember selected rows for response
		if (isset($this->request->post['selected'])) {
			$this->session->data['listing_selected'] = AJson::decode(html_entity_decode($this->request->post['selected']), true);
		}
		$grid_settings = array('table_id' => 'banner_grid',
			'url' => $this->html->getSecureURL('listing_grid/banner_manager/getBannerListData'),
			'editurl' => '',
			'update_field' => '',
			'sortname' => 'name',
			'sortorder' => 'asc',
			'columns_search' => true,
			'actions' => array(),
			'multiselect_noselectbox' => true,);

		$form = new AForm ();
		$form->setForm(array('form_name' => 'banner_grid_search'));

		$grid_search_form = array();
		$grid_search_form['id'] = 'banner_grid_search';
		$grid_search_form['form_open'] = $form->getFieldHtml(array('type' => 'form',
			'name' => 'banner_grid_search',
			'action' => ''));
		$grid_search_form['submit'] = $form->getFieldHtml(array('type' => 'button',
			'name' => 'submit',
			'text' => $this->language->get('button_go'), 'style' => 'button1'));
		$grid_search_form['reset'] = $form->getFieldHtml(array('type' => 'button',
			'name' => 'reset',
			'text' => $this->language->get('button_reset'), 'style' => 'button2'));

		$grid_settings['colNames'] = array('',
			$this->language->get('column_banner_name'),
			$this->language->get('column_banner_group'),
			$this->language->get('column_banner_type'),
			$this->language->get('column_action'));

		$grid_settings['colModel'] = array(
			array('name' => 'banner_icon',
				'index' => 'icon',
				'width' => 40,
				'align' => 'center',
				'search' => false
			),
			array('name' => 'banner_name',
				'index' => 'name',
				'width' => 100,
				'align' => 'left',
			),
			array('name' => 'banner_group',
				'index' => 'banner_group_name',
				'width' => 80,
				'align' => 'left',
			),
			array('name' => 'banner_type',
				'index' => 'banner_type',
				'width' => 60,
				'align' => 'center',
				'search' => false),

			array('name' => 'action',
				'index' => 'action',
				'align' => 'center',
				'sortable' => false,
				'search' => false));

		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$this->data['listing_grid'] = $grid->dispatchGetOutput();
		$this->data['search_form'] = $grid_search_form;


		$grid = $this->dispatch('common/listing_grid', array($grid_settings));
		$listing_grid = $grid->dispatchGetOutput();
		unset($grid);


		// add js-scripts for grid rows selecting (redeclare onSelectRow event for grid)
		$view = new AView($this->registry, 0);
		$view->batchAssign(array('id' => $multivalue_hidden_id,
			'form_name' => $form_name . '_' . $multivalue_hidden_id,
			'table_id' => $grid_settings['table_id'],
			'listing_grid' => $listing_grid,
			'heading_title' => $this->language->get('text_select_banners')));

		$this->data['response'] = $view->fetch('responses/extension/banner_listing.tpl');


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->response->setOutput($this->data['response']);
	}


	public function getBannerListData() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->load->library('json');

		// json-response for jqgrid
		$this->loadLanguage('banner_manager/banner_manager');
		$this->loadModel('tool/image');

		$page = $this->request->post['page']; // get the requested page
		if ((int)$page < 0) $page = 0;
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid

		//sort
		$filter_params = array('name', 'banner_group_name');
		$filter_grid = new AFilter(array('method' => 'post',
			'grid_filter_params' => $filter_params,
			'additional_filter_string' => ''));

		$this->loadModel('extension/banner_manager');
		$total = $this->model_extension_banner_manager->getBanners($filter_grid->getFilterData(), 'total_only');

		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$results = $this->model_extension_banner_manager->getBanners($filter_grid->getFilterData());

		$list = $this->session->data['listing_selected'];

		$id_list = array();
		foreach ($list as $id => $row) {
			if ($row['status']) {
				$id_list[] = $id;
			}
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$i = 0;
		$resource = new AResource('image');
		foreach ($results as $result) {

			if (in_array($result['banner_id'], $id_list)) {
				$response->userdata->selId[] = $result['banner_id'];
			}

			$action = '<a class="btn_action" href="JavaScript:void(0);"
						onclick="showPopup(\'' . $this->html->getSecureURL('extension/banner_manager/edit', '&banner_id=' . $result['banner_id']) . '\')" title="' . $this->language->get('text_view') . '">' .
					'<img height="27" src="' . RDIR_TEMPLATE . 'image/icons/icon_grid_view.png" alt="' . $this->language->get('text_edit') . '" /></a>';


			$response->rows[$i]['id'] = $result['banner_id'];
			if ($result['banner_type'] == 1) {
				$thumbnail = $resource->getMainThumb('banners',
					$result['banner_id'],
					27,
					27,
					true);
				$thumbnail = $thumbnail['thumb_html'];
			} else {
				$thumbnail = '';
			}
			$response->rows[$i]['cell'] = array($thumbnail,
				$result['name'],
				$result['banner_group_name'],
				($result['banner_type'] == 1 ? $this->language->get('text_graphic_banner') : $this->language->get('text_text_banner')),
				$action,
			);
			$i++;
		}

		$this->data['response'] = $response;


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($this->data['response']));
	}

	public function banners() {

		//$products = array();
		$banners_data = array();

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadModel('extension/banner_manager');
		if (isset($this->request->post['term'])) {

			$rm = new AResourceManager();
			$rm->setType('image');

			$filter = array( 'subsql_filter' => "b.target_url LIKE '%".$this->db->escape($this->request->post['term'])."%'
												OR bd.name LIKE '%".$this->db->escape($this->request->post['term'])."%'
												OR bd.description LIKE '%".$this->db->escape($this->request->post['term'])."%'
												OR bd.meta LIKE '%".$this->db->escape($this->request->post['term'])."%'",
							'limit' => 20 );
			$banners = $this->model_extension_banner_manager->getBanners($filter);


			foreach ($banners as $banner) {
				$thumbnail = $rm->getMainThumb('banners',
												$banner['banner_id'],
												(int)$this->config->get('config_image_grid_width'),
												(int)$this->config->get('config_image_grid_height'),
												false);
				$icon = $thumbnail['thumb_html'] ? $thumbnail['thumb_html'] : '<i class="fa fa-code fa-4x"></i>&nbsp;';

				$banners_data[ ] = array(
					'image' => $icon,
					'id' => $banner['banner_id'],
					'name' => $banner['name'],
					'sort_order' => (int)$banner['sort_order'],
				);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($banners_data));
	}

}