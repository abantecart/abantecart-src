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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerResponsesListingGridTotal extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $this->loadLanguage('extension/total');

	    $page = $this->request->post['page']; // get the requested page
	    if ( (int)$page < 0 ) $page = 0;
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

	    $this->loadModel('setting/extension');
		$ext = $this->extensions->getExtensionsList(array('filter'=>'total'));
	    $extensions = array();
	    if($ext->rows){
		    foreach($ext->rows as $row){
			    $extensions[] = $row['key'];
		    }
	    }
	    $items = array();
		$files = glob(DIR_APP_SECTION . 'controller/pages/total/*.php');

		if ($files) {
			foreach ($files as $file) {

				$extension = basename($file, '.php');
				$this->loadLanguage('total/' . $extension);


				if (!in_array($extension, $extensions)) {
					$action = '<a class="btn_action" href="'.$this->html->getSecureURL('extension/total/install', '&extension=' . $extension).'">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_install.png" alt="'. $this->language->get('text_install') . '" />'.
				          '</a>';
				} else {

					$action = '<a class="btn_action" href="'.$this->html->getSecureURL('total/' . $extension).'">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_edit.png" alt="'. $this->language->get('text_edit') . '" />'.
				          '</a>'.
						  '<a class="btn_action" href="'.$this->html->getSecureURL('extension/total/uninstall', '&extension=' . $extension).'">'.
				          '<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_uninstall.png" alt="'. $this->language->get('text_uninstall') . '" />'.
				          '</a>';
				}

				$items[] = array(
					'id'         => $extension,
					'name'       => $this->language->get('heading_title'),
					'status'     => $this->config->get($extension . '_status'),
					'sort_order' => $this->config->get($extension . '_sort_order'),
					'action'     => $action
				);
			}
		}


	    //sort
	    $allowedSort = array('name', 'status', 'sort_order');
	    $allowedDirection = array(SORT_ASC => 'asc', SORT_DESC => 'desc');
	    if ( !in_array($sidx, $allowedSort) ) $sidx = $allowedSort[0];
	    if ( !in_array($sord, $allowedDirection) ) {
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
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

	    $results = array_slice($items, ($page-1)*-$limit, $limit);

	    $i = 0;
		foreach ($results as $result) {


			if (!in_array($result['id'], $extensions)) {
				$status = '';
				$sort = '';
			} else {
				$status = $this->html->buildCheckbox(array(
					'name' => $result['id'].'['.$result['id'].'_status]',
					'value' => $result['status'],
					'style' => 'btn_switch',
				));
				$sort = $this->html->buildInput(array(
                    'name'  => $result['id'].'['.$result['id'].'_sort_order]',
                    'value' => $result['sort_order'],
                ));

			}

			$response->rows[$i]['id'] = $result['id'];
			$response->rows[$i]['cell'] = array(
				$result['name'],
                $status,
				$sort,
				$result['action'],
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

	    $this->loadLanguage('extension/total');
	    $ids = array();
	    if ( isset( $this->request->get['id'] ) ) {
		    $ids[] = $this->request->get['id'];
	    } else {
		    $ids = array_keys($this->request->post);
	    }

	    if (!$this->user->canModify('listing_grid/total')) {
	  			        $error = new AError('');
	  			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
	  			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/total'),
	  			    	                                      'reset_value' => true
	  			    	                             ) );
	  	}
	    foreach ( $ids as $id) {
		    if (!$this->user->canModify('total/'.$id)) {
		  			        $error = new AError('');
		  			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
		  			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'total/'.$id),
		  			    	                                      'reset_value' => true
		  			    	                             ) );
		  	}
	    }

		$this->loadModel('setting/setting');

        if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    $this->model_setting_setting->editSetting( $this->request->get['id'], $this->request->post);
		    return;
	    }

	    //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $group => $values ) {
	        $this->model_setting_setting->editSetting( $group, $values);
        }


		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}
?>