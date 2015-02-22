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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesListingGridUser extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('user/user');
	    $this->loadModel('user/user');

	    $this->loadModel('user/user_group');
    	$user_groups = array('' => $this->language->get('text_select_group'),);
		$results = $this->model_user_user_group->getUserGroups();
		foreach ($results as $r) {
			$user_groups[ $r['user_group_id'] ] = $r['name'];
		}

		//Prepare filter config
		$filter_params = array('status', 'user_group_id');
 		$grid_filter_params = array( 'username' );
 		//Build query string based on GET params first 
		$filter_form = new AFilter( array( 'method' => 'get', 'filter_params' => $filter_params ) );  
		//Build final filter
	    $filter_grid = new AFilter( array( 'method' => 'post', 
	    								   'grid_filter_params' => $grid_filter_params,
	    								   'additional_filter_string' => $filter_form->getFilterString()
	    								  ) );   	    
		$total = $this->model_user_user->getTotalUsers( $filter_grid->getFilterData() );
	    $response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages( $total );
		$response->records = $total;
	    $results = $this->model_user_user->getUsers( $filter_grid->getFilterData() );

	    $i = 0;
		foreach ($results as $result) {

            $response->rows[$i]['id'] = $result['user_id'];
			$response->rows[$i]['cell'] = array(
				$result['username'],
				$user_groups[$result['user_group_id']],
				$this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['user_id'].']',
                    'value' => $result['status'],
                    'style'  => 'btn_switch',
                )),
				dateISO2Display($result['date_added'], $this->language->get('date_format_short'))
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->user->canModify('listing_grid/user')) {
			        $error = new AError('');
			        return $error->toJSONResponse('NO_PERMISSIONS_402',
			                                      array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/user'),
			                                             'reset_value' => true
			                                           ) );
		}

		$this->loadModel('user/user');
        $this->loadLanguage('user/user');

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					if ($this->user->getId() == $id) {
						$this->response->setOutput( $this->language->get('error_account'));
						return null;
					}
					$this->model_user_user->deleteUser($id);
				}
				break;
			case 'save':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$this->model_user_user->editUser($id, array('status' => isset($this->request->post['status'][$id]) ? $this->request->post['status'][$id] : 0 ) );
				}

				break;

			default:


		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

    /**
     * update only one field
     *
     * @return void
     */
    public function update_field() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    if (!$this->user->canModify('listing_grid/user')) {
	  			        $error = new AError('');
	  			        return $error->toJSONResponse('NO_PERMISSIONS_402',
	  			                                      array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/user'),
	  			                                             'reset_value' => true
	  			                                           ) );
	  	}

        $this->loadLanguage('user/user');
        $this->loadModel('user/user');
		if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    foreach ($this->request->post as $key => $value ) {
				$data = array( $key => $value );
				$this->model_user_user->editUser($this->request->get['id'], $data);
			}
		    return null;
	    }

	    //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value ) {
            foreach ( $value as $k => $v ) {
				$this->model_user_user->editUser($k, array($field => $v) );
            }
        }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}
