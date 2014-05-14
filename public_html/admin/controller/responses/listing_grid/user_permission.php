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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesListingGridUserPermission extends AController {


    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('user/user_group');
	    $this->loadModel('user/user_group');

		$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

        // process jGrid search parameter
	    $allowedDirection = array('asc', 'desc');

	    if ( !in_array($sord, $allowedDirection) ) $sord = $allowedDirection[0];

	    $data = array(
			'order' => strtoupper($sord),
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		);


		$total = $this->model_user_user_group->getTotalUserGroups($data);
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		$response->userdata = new stdClass();

	    $results = $this->model_user_user_group->getUserGroups($data);
	    $i = 0;
		foreach ($results as $result) {
			$action = '';
			if($result['user_group_id']!=1){
				$action = '<a class="btn_action" href="'.$this->html->getSecureURL('user/user_permission/update', '&user_group_id='. $result['user_group_id']).'"
								title="'. $this->language->get('text_edit') . '">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_edit.png" alt="'. $this->language->get('text_edit') . '" />'.
				          '</a>'.
						  '<a class="btn_action" href="'.$this->html->getSecureURL('user/user_permission/delete', '&user_group_id='. $result['user_group_id']).'"
						  onclick="return confirm(\''.$this->language->get('text_delete_confirm').'\')" title="'. $this->language->get('text_delete') . '">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_delete.png" alt="'. $this->language->get('text_delete') . '" />'.
				          '</a>';
			}else{
				$response->userdata->hightligth[ $result['user_group_id'] ] = '';
			}
            $response->rows[$i]['id'] = $result['user_group_id'];
			$response->rows[$i]['cell'] = array(
				$result['name'],
				$action,
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

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
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('user/user_group');
	    if (!$this->user->canModify('listing_grid/user_permission')) {
	        $error = new AError('');
	    	return $error->toJSONResponse('NO_PERMISSIONS_402',
	    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/user_permission'),
	    	                                      'reset_value' => true
	    	                             ) );
	    }

        $this->loadModel('user/user_group');
		if ( isset( $this->request->get['user_group_id'] ) ) {
		    //request sent from edit form. ID in url
		  	$this->model_user_user_group->editUserGroup($this->request->get['user_group_id'], $this->request->post);
		 return;
	    }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function getPermissions(){

		$user_group_id = (int)$this->request->get['user_group_id'];
		$this->loadLanguage('user/user_group');
	    $this->loadModel('user/user_group');
		// check user_group_id for
		$result = $this->model_user_user_group->getUserGroup($user_group_id);
        $permissions = $result['permission'];
        if (empty($permissions) ) {
            $permissions = array(
                'access' => array(),
                'modify' => array(),
            );
        }


		$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

		// get controllers list
		$controllers = $this->model_user_user_group->getAllControllers($sord);

		$this->load->library('json');
		$searchData = json_decode(htmlspecialchars_decode($this->request->post['filters']), true);
		$search_str = $searchData['rules'][0]['data'];
		$access = $modify = array();
		foreach($controllers as $key=>$controller){
			$access[$key] = has_value($permissions['access'][$controller]) ? (int)$permissions['access'][$controller] : null;
			$modify[$key] = has_value($permissions['modify'][$controller]) ? (int)$permissions['modify'][$controller] : null;
		}

		//filter result by controller name (temporary solution). needs to improve.
		foreach($controllers as $key=>$controller){
			if($search_str){
				if(!is_int(strpos($controller,$search_str))){
					unset($controllers[$key]);
				}
			}
		}

        // process jGrid search parameter
	    $allowedDirection = array('asc', 'desc');

	    if (!in_array($sord, $allowedDirection)){
			$sord = $allowedDirection[0];
		}

		// resort by permissions
		if( $sidx=='access' ){
			array_multisort( $access, ($sord=='asc'? SORT_ASC : SORT_DESC), $controllers);
		}elseif( $sidx=='modify' ){
			array_multisort( $modify, ($sord=='asc'? SORT_ASC : SORT_DESC), $controllers);
		}

		$data = array(	'order' => strtoupper($sord),
						'start' => ($page - 1) * $limit,
						'limit' => $limit );

		$total = sizeof($controllers);
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;



	    $i = 0;
		$controllers = array_slice($controllers,$data['start'],$data['limit']);

		foreach ($controllers as $k=>$controller) {

			if(!in_array($controller, array_keys($permissions['access'])) && !in_array($controller, array_keys($permissions['modify']))){
					$response->userdata->classes[ $k ] = 'warning';
			}

            $response->rows[$i]['id'] = $k;
			$response->rows[$i]['cell'] = array( $k+$data['start']+1,
			                                     '<a style="padding-left: 10px;" href="'.$this->html->getSecureURL($controller).'" target="_blank" title="'.$this->language->get('text_go_to_page').'">'.$controller.'</a>',
			                                     $this->html->buildCheckbox(array(
												 								'name' => 'permission[access]['.$controller.']',
																				'value' => ($permissions['access'][$controller]?1:0),
			                                                                    'style' => 'btn_switch')),
												 $this->html->buildCheckbox(array(
												 								'name' => 'permission[modify]['.$controller.']',
																				'value' => ($permissions['modify'][$controller]?1:0),
			                                                                    'style' => 'btn_switch')));
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->response->setOutput(AJson::encode($response));
	}

}
