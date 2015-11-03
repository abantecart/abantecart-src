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
class ControllerResponsesListingGridMessageGrid extends AController {
	private $error = array();
	public $data = array();

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('tool/message_manager');
		if (!$this->user->canAccess('tool/message_manager')) {
			$response = new stdClass ();
			$response->userdata->error = sprintf($this->language->get('error_permission_access'), 'tool/message_manager');
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($response));
			return null;
		}

		$this->loadModel('tool/message_manager');

		//Prepare filter config
		$grid_filter_params = array('title', 'date_added', 'status');
		$filter = new AFilter(array('method' => 'post', 'grid_filter_params' => $grid_filter_params));

		$total = $this->model_tool_message_manager->getTotalMessages();
		$response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages($total);
		$response->records = $total;
		$response->userdata = new stdClass();

		$sort_array = $filter->getFilterData();
		if ($sort_array['sort'] == 'sort_order') {
			$sort_array['sort'] = 'viewed';
		}
		$results = $this->model_tool_message_manager->getMessages($sort_array);

		$i = 0;
		foreach ($results as $result) {
			$response->rows [$i] ['id'] = $result ['msg_id'];
			switch ($result['status']) {
				case 'E':
					$status = $this->language->get('entry_error');
					$response->userdata->classes[$result ['msg_id']] = 'warning';
					break;
				case 'W':
					$status = $this->language->get('entry_warning');
					$response->userdata->classes[$result ['msg_id']] = 'attention';
					break;
				case 'N':
				default:
					$status = $this->language->get('entry_notice');
					$response->userdata->classes[$result ['msg_id']] = 'success';
					break;
			}

			$response->userdata->classes[$result ['msg_id']] .= !$result['viewed'] ? ' new_message' : '';

			$response->rows [$i] ['cell'] = array($status,
					$result ['title'],
					dateISO2Display($result ['date_added'], $this->language->get('date_format_short') . ' H:s'),
			);

			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));

	}

	/**
	 * @return mixed
	 */
	public function update() {

		if (!$this->user->canModify('listing_grid/message_grid')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
					array('error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/message_grid'),
						  'reset_value' => true
					));
		}

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('tool/message_manager');

		if ($this->request->post ['oper'] == 'del') {
			$ids = explode(',', $this->request->post ['id']);
			if ($ids) {
				foreach ($ids as $msg_id) {
					$this->model_tool_message_manager->deleteMessage($msg_id);
				}
			}
		} elseif ($this->request->get ['oper'] == 'show') {
			$msg_id = $this->request->get ['id'];
			if ($msg_id) {
				$this->data['message'] = $this->model_tool_message_manager->getMessage($msg_id);
				if ($this->data['message']) {
					$this->loadLanguage('tool/message_manager');
					$this->data['message']["message"] = str_replace("#link-text#", $this->language->get('text_linktext'), $this->data['message'] ["message"]);
					switch ($this->data['message'] ['status']) {
						case 'W' :
							$this->data['message'] ['status'] = $this->language->get('text_warning');
							break;
						case 'E' :
							$this->data['message'] ['status'] = $this->language->get('text_error');
							break;
						default :
							$this->data['message'] ['status'] = $this->language->get('text_notice');
							break;
					}
					$this->data['message'] ['date_formatted'] = dateISO2Display($this->data['message'] ['date_modified'], $this->language->get('date_format_short').' '.$this->language->get('time_format'));
				} else {
					$this->data['message'] ["message"] = $this->language->get('text_not_found');
				}
				$this->messages->markAsRead($msg_id);
			}

			$this->view->assign('delete_url', $this->html->getSecureURL('listing_grid/message_grid/update'));
			$this->view->assign('msg_id', $msg_id);
			$this->view->assign('readonly', $this->request->get['readonly']);
			$this->view->batchAssign($this->language->getASet('tool/message_manager'));
			$this->view->batchAssign($this->data);
			$this->response->setOutput($this->view->fetch('responses/tool/message_info.tpl'));

			//update controller data
			$this->extensions->hk_UpdateData($this, __FUNCTION__);
		}
	}

	public function getNotifies() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$ret = array();

		$this->loadLanguage('tool/message_manager');
		$this->data['shortlist'] = $this->messages->getShortList();
		if($this->data['shortlist']['total']) {
			$ret = $this->data['shortlist'];
			$ret['total_title'] = sprintf($this->language->get('text_notifier_title'),$ret['total']);
			foreach($ret['shortlist'] as &$m){
				$m['message'] = mb_substr($m['message'],0, 30).'...';
				$m['href']	= $this->html->getSecureURL ( 'listing_grid/message_grid/update','&oper=show&readonly=1&id='.$m['msg_id']);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($ret));
	}
}