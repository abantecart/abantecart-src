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
class ControllerResponsesListingGridTotal extends AController {
	public $data;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('extension/total');

		$page = $this->request->post['page']; // get the requested page
		if ((int)$page < 0) $page = 0;
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

		$this->loadModel('setting/extension');
		$ext = $this->extensions->getExtensionsList(array('filter' => 'total'));
		$extensions = array();
		if ($ext->rows) {
			foreach ($ext->rows as $row) {
				$language_rt = $config_controller = '';
				// for total-extensions inside engine
				if (is_file(DIR_APP_SECTION . 'controller/pages/total/' . $row['key'] . '.php')) {
					$config_controller = $language_rt = 'total/' . $row['key'];
				} else {
					// looking for config controller into parent extension.
					//That Controller must to have filename equal child extension text id
					$parents = $this->extension_manager->getParentsExtensionTextId($row['key']);
					if ($parents) {
						foreach ($parents as $parent) {
							if (!$parent['status']) continue;
							if (is_file(DIR_EXT . $parent['key'] . '/admin/controller/pages/total/' . $row['key'] . '.php')) {
								$config_controller = 'total/' . $row['key'];
								$language_rt = $parent['key'] . '/' . $parent['key'];
								break;
							}
						}
					}
				}
				if ($config_controller) {
					$extensions[$row['key']] = array(
						'extension_txt_id' => $row['key'],
						'config_controller' => $config_controller,
						'language_rt' => $language_rt);
				}
			}
		}

		//looking for uninstalled engine's total-extensions
		$files = glob(DIR_APP_SECTION . 'controller/pages/total/*.php');
		if ($files) {
			foreach ($files as $file) {
				$id = basename($file, '.php');
				if (!array_key_exists($id, $extensions)) {
					$extensions[$id] = array(
						'extension_txt_id' => $id,
						'config_controller' => 'total/' . $id,
						'language_rt' => 'total/' . $id);
				}
			}
		}

		$items = array();
		if ($extensions) {
			foreach ($extensions as $extension) {
				$this->loadLanguage($extension['language_rt']);
				$items[] = array(
					'id' => $extension['extension_txt_id'],
					'name' => $this->language->get('total_name'),
					'status' => $this->config->get($extension['extension_txt_id'] . '_status'),
					'sort_order' => (int)$this->config->get($extension['extension_txt_id'] . '_sort_order'),
					'calculation_order' => (int)$this->config->get($extension['extension_txt_id'] . '_calculation_order'),
					'action' => $this->html->getSecureURL($extension['config_controller'])
				);
			}
		}

		//sort
		$allowedSort = array('name', 'status', 'sort_order', 'calculation_order');
		$allowedDirection = array(SORT_ASC => 'asc', SORT_DESC => 'desc');
		if (!in_array($sidx, $allowedSort)) $sidx = $allowedSort[0];
		if (!in_array($sord, $allowedDirection)) {
			$sord = SORT_ASC;
		} else {
			$sord = array_search($sord, $allowedDirection);
		}

		$sort = array();
		foreach ($items as $item) {
			$sort[] = $item[$sidx];
		}

		array_multisort($sort, $sord, $items);

		$total = count($items);
		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$response->userdata = new stdClass();
		$response->userdata->rt = array();
		$response->userdata->classes = array();

		$results = array_slice($items, ($page - 1) * -$limit, $limit);

		$i = 0;
		foreach ($results as $result) {
			$response->userdata->rt[ $result['id'] ] = $result['action'];
			$status = $this->html->buildCheckbox(array(
				'name' => $result['id'] . '[' . $result['id'] . '_status]',
				'value' => $result['status'],
				'style' => 'btn_switch',
			));
			$sort = $this->html->buildInput(array(
				'name' => $result['id'] . '[' . $result['id'] . '_sort_order]',
				'value' => $result['sort_order'],
			));

			$calc = $this->html->buildInput(array(
				'name' => $result['id'] . '[' . $result['id'] . '_calculation_order]',
				'value' => $result['calculation_order'],
			));

			$response->rows[$i]['id'] = $result['id'];
			$response->rows[$i]['cell'] = array(
				$result['name'],
				$status,
				($result['status'] ? $sort : ''),
				($result['status'] ? $calc : '')
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}


	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('extension/total');
		$ids = array();
		if (isset($this->request->get['id'])) {
			$ids[] = $this->request->get['id'];
		} else {
			$ids = array_keys($this->request->post);
		}

		if (!$this->user->canModify('listing_grid/total')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array('error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/total'),
					'reset_value' => true
				));
		}
		foreach ($ids as $id) {
			if (!$this->user->canModify('total/' . $id)) {
				$error = new AError('');
				return $error->toJSONResponse('NO_PERMISSIONS_402',
					array('error_text' => sprintf($this->language->get('error_permission_modify'), 'total/' . $id),
						'reset_value' => true
					));
			}
		}

		$this->loadModel('setting/setting');

		if (isset($this->request->get['id'])) {
			//request sent from edit form. ID in url
			$this->model_setting_setting->editSetting($this->request->get['id'], $this->request->post);
			return null;
		}

		//request sent from jGrid. ID is key of array
		foreach ($this->request->post as $group => $values) {
			$this->model_setting_setting->editSetting($group, $values);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}