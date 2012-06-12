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
class ControllerResponsesListingGridLanguageDefinitions extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('localisation/language_definitions');
	    $this->loadModel('localisation/language_definitions');

		//Prepare filter config
		$filter_params = array( 'language_key', 'section');
 		$grid_filter_params = array('name', 'language_key', 'language_value', 'block' );

		$filter_form = new AFilter( array( 'method' => 'get', 'filter_params' => $filter_params ) );    
	    $filter_grid = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
	    
		$total = $this->model_localisation_language_definitions->getTotalDefinitions( array_merge(	$filter_form->getFilterData(), 
																									$filter_grid->getFilterData() ) );
	    $response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages( $total );
		$response->records = $total;
		$results = $this->model_localisation_language_definitions->getLanguageDefinitions( array_merge(	$filter_form->getFilterData(), 
																									$filter_grid->getFilterData() ) );

	    $i = 0;
		foreach ($results as $result) {
			if($result['error']){
				$response->userdata->classes[ $result['language_definition_id'] ] = 'warning';
			}

            $response->rows[$i]['id'] = $result['language_definition_id'];
			$response->rows[$i]['cell'] = array(
				$result['language_name'],
				$this->html->buildInput(array(
                    'name'  => 'block['.$result['language_definition_id'].']',
                    'value' => $result['block'],
                )),
                $this->html->buildInput(array(
                    'name'  => 'language_key['.$result['language_definition_id'].']',
                    'value' => $result['language_key'],
                )),
				$this->html->buildInput(array(
                    'name'  => 'language_value['.$result['language_definition_id'].']',
                    'value' => $result['language_value'],
                )),
				(strtotime($result['update_date']) ? date('Y/m/d', strtotime($result['update_date'])) : ''),
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

	    $this->loadModel('localisation/language_definitions');
        $this->loadLanguage('localisation/language_definitions');
        if (!$this->user->hasPermission('modify', 'localisation/language_definitions')) {
			$this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'localisation/language_definitions') );
            return;
		}

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$this->model_localisation_language_definitions->deleteLanguageDefinition($id);
				}
				break;
			case 'save':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$data = array(
						'block' => $this->request->post['block'][$id],
						'language_key' => $this->request->post['language_key'][$id],
						'language_value' => $this->request->post['language_value'][$id],
					);
					$this->model_localisation_language_definitions->editLanguageDefinition($id, $data);
				}
				break;

			default:
				//print_r($this->request->post);

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

        $this->loadLanguage('localisation/language_definitions');
        if (!$this->user->hasPermission('modify', 'localisation/language_definitions')) {
			$this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'localisation/language_definitions') );
            return;
		}

        $this->loadModel('localisation/language_definitions');
		$allowedFields = array('block', 'language_key', 'language_value', 'section');

	    if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    foreach ($this->request->post as $key => $value ) {
				if ( !in_array($key, $allowedFields) ) continue;
				$data = array( $key => $value );
				$this->model_localisation_language_definitions->editLanguageDefinition($this->request->get['id'], $data);
			}
		    return;
	    }

	    //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $key => $value ) {
            if ( !in_array($key, $allowedFields) ) continue;
            foreach ( $value as $k => $v ) {
                $data = array( $key => $v );
                $this->model_localisation_language_definitions->editLanguageDefinition($k, $data);
            }
        }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}
?>