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
class ControllerResponsesListingGridContent extends AController {
	/**
	 * @var AContentManager
	 */
	private $acm;

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('design/content');
		$this->acm = new AContentManager();

		//Prepare filter config
		$grid_filter_params = array( 'sort_order', 'id.title', 'status', 'nodeid' );
		//Build advanced filter
		$filter_data = array( 'method' => 'post',
							  'grid_filter_params' => $grid_filter_params);
		$filter_grid = new AFilter($filter_data);
		$filter_array = $filter_grid->getFilterData();
		if ($this->request->post['nodeid'] ) {
            list($void,$parent_id) = explode('_',$this->request->post['nodeid']);
            $filter_array['parent_id'] = $parent_id;
			if($filter_array['subsql_filter']){
				$filter_array['subsql_filter'] .= " AND i.parent_content_id='".(int)$filter_array['parent_id']."' ";
			}else{
				$filter_array['subsql_filter'] = " i.parent_content_id='".(int)$filter_array['parent_id']."' ";
			}
			$new_level = (integer)$this->request->post["n_level"] + 1;
		}else{
			//Add custom params
            $filter_array['parent_id'] = $new_level = 0;
			if ( $this->config->get('config_show_tree_data') ) {
				if($filter_array['subsql_filter']){
					$filter_array['subsql_filter'] .= " AND i.parent_content_id='0' ";
				}else{
					$filter_array['subsql_filter'] = " i.parent_content_id='0' ";
				}
			}
		}


		$leafnodes = $this->config->get('config_show_tree_data') ? $this->acm->getLeafContents() : array();

		$total = $this->acm->getTotalContents($filter_array);
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;
        $response->userdata = (object)array('');
		$results = $this->acm->getContents($filter_array);
		$results = !$results ? array() : $results;
		$i = 0;


		foreach ($results as $result) {

			if ( $this->config->get('config_show_tree_data') ) {
				$title_lable = '<label style="white-space: nowrap;">'.$result['title'].'</label>';
			} else {
				$title_lable = $result['title'];
			}
            $parent_content_id = current($result[ 'parent_content_id' ]);
			$response->rows[ $i ][ 'id' ] = $parent_content_id.'_'.$result[ 'content_id' ];
			$response->rows[ $i ][ 'cell' ] = array(

				$title_lable,
				$result[ 'parent_name' ],
				$this->html->buildCheckbox(array(
					'name' => 'status[' . $parent_content_id.'_'. $result[ 'content_id' ].']',
					'value' => $result[ 'status' ],
					'style' => 'btn_switch',
				)),
				$this->html->buildInput(array(
					'name' => 'sort_order[' . $parent_content_id.'_'. $result[ 'content_id' ].']',
					'value' => $result[ 'sort_order' ][$parent_content_id],
				)),
				'action',
				 $new_level,
				 ( $this->request->post['nodeid'] ? $this->request->post['nodeid'] : null ),
				 ( $result['content_id'] == $leafnodes[$result['content_id']] ? true : false ),
				false
			);
			$i++;
		}


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('design/content');
		$this->acm = new AContentManager();
		if (!$this->user->canModify('listing_grid/content')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/content'),
					'reset_value' => true
				));
		}

		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						if(is_int(strpos($id,'_'))){
							list($parent_content_id,$content_id) = explode('_',$id);
						}else{
							$content_id = $id;
						}

						if ($this->config->get('config_account_id') == $content_id) {
							$this->response->setOutput($this->language->get('error_account'));
							return null;
						}

						if ($this->config->get('config_checkout_id') == $content_id) {
							$this->response->setOutput($this->language->get('error_checkout'));
							return null;
						}


						$this->acm->deleteContent($content_id);
					}
				break;
			case 'save':
				$allowedFields = array( 'sort_order', 'status');
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					//resort required. 
					if(  $this->request->post['resort'] == 'yes' ) {
						//get only ids we need
						foreach($ids as $id){
							$array[$id] = $this->request->post['sort_order'][$id];
						}
						$new_sort = build_sort_order($ids, min($array), max($array), $this->request->post['sort_direction']);
	 					$this->request->post['sort_order'] = $new_sort;
					}
					foreach ($ids as $id) {
						$parent_content_id = null;
						if(is_int(strpos($id,'_'))){
							list($parent_content_id,$content_id) = explode('_',$id);
						}else{
							$content_id = $id;
						}
						foreach ($allowedFields as $field) {
							$this->acm->editContentField($content_id, $field, $this->request->post[ $field ][ $id ],$parent_content_id);
						}
					}
				break;

			default:
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);
		$this->loadLanguage('design/content');
		$this->acm = new AContentManager();
		if (!$this->user->canModify('listing_grid/content')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/content'),
					   'reset_value' => true
				));
		}
		$allowedFields = array( 'title', 'description', 'keyword', 'store_id', 'sort_order', 'status', 'parent_content_id' );

		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $field => $value) {
				if (!in_array($field, $allowedFields)) {
					continue;
				}
				if($field=='keyword'){
					if($err = $this->html->isSEOkeywordExists('content_id='.$this->request->get['id'], $value)){
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
					}
				}
				if($field=='sort_order'){
					// NOTE: grid quicksave ids are not the same as id from form quick save request!
					list($void,$parent_content_id) = explode('_',key($value));
					$value = current($value);
				}

				$this->acm->editContentField($this->request->get[ 'id' ], $field, $value, $parent_content_id);
			}
			return null;
		}

		//request sent from jGrid. ID is key of array
		foreach ($this->request->post as $field => $value) {
			if (!in_array($field, $allowedFields)) continue;
			// NOTE: grid quicksave ids are not the same as id from form quick save request!
			list($parent_content_id,$content_id) = explode('_',key($value));
			$this->acm->editContentField($content_id, $field, current($value),$parent_content_id);
		}
		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}
