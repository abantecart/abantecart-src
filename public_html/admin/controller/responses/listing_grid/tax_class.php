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
class ControllerResponsesListingGridTaxClass extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('localisation/tax_class');
	    $this->loadModel('localisation/tax_class');

		//Prepare filter config
		$grid_filter_params = array('title');
	    $filter = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
	    $filter_data = $filter->getFilterData();
	    
	    $total = $this->model_localisation_tax_class->getTotalTaxClasses($filter_data);
	    $response = new stdClass();
		$response->page = $filter->getParam('page');
		$response->total = $filter->calcTotalPages( $total );
		$response->records = $total;
	    $response->userdata = (object)array('');
	    $results = $this->model_localisation_tax_class->getTaxClasses($filter_data);

	    $i = 0;
		foreach ($results as $result) {

            $response->rows[$i]['id'] = $result['tax_class_id'];
			$response->rows[$i]['cell'] = array(
				$this->html->buildInput(array(
                    'name'  => 'tax_class[' . $result[ 'tax_class_id' ] . '][' . $this->session->data[ 'content_language_id' ] . '][title]',                    					'value' => $result['title'],
                )),
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

		$this->loadModel('localisation/tax_class');
        $this->loadLanguage('localisation/tax_class');
		if (!$this->user->canModify('listing_grid/tax_class')) {
			        $error = new AError('');
			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/tax_class'),
			    	                                      'reset_value' => true
			    	                             ) );
	    }

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$err = $this->_validateDelete($id);
					if (!empty($err)) {
						$dd = new ADispatcher('responses/error/ajaxerror/validation',array('error_text'=>$err));
						return $dd->dispatch();
					}

					$this->model_localisation_tax_class->deleteTaxClass($id);
				}
				break;
			case 'save':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					if (isset($this->request->post[ 'tax_class' ][ $id ])) {
						foreach ($this->request->post[ 'tax_class' ][ $id ] as $lang => $value) {
							if ( isset($value['title']) ) {
		    					$err = $this->_validateField('title', $value['title']);
		    					if (!empty($err)) {							
									$this->response->setOutput($err);
									return;
								}
							}
						}
						$this->model_localisation_tax_class->editTaxClass($id, array( 'tax_class' => $this->request->post['tax_class'][ $id ] ));
					}						
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

        $this->loadLanguage('localisation/tax_class');
		if (!$this->user->canModify('listing_grid/tax_class')) {
			        $error = new AError('');
			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/tax_class'),
			    	                                      'reset_value' => true
			    	                             ) );
	    }
        $this->loadModel('localisation/tax_class');
		if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    foreach ($this->request->post as $key => $value ) {
				$err = '';
				if ( $key == 'tax_class' ) {
					foreach ($value as $lang => $dvalue) {	
						if ( isset($dvalue['title']) ) {	
		    				$err .= $this->_validateField('title', $dvalue['title']);
		    			}
		    		}				
				} else {
					$err = $this->_validateField($key, $value);			
				}
			    if ( !empty($err) ) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
			    }
			    $data = array( $key => $value );
				$this->model_localisation_tax_class->editTaxClass($this->request->get['id'], $data);
			}
		    return;
	    }

	    //request sent from jGrid. ID is key of array
		if (isset($this->request->post['tax_class'])) {
			foreach ($this->request->post['tax_class'] as $id => $v) {
				foreach ($v as $lang => $value) {
		    		$err = $this->_validateField('title', $value['title']);
		    		if (!empty($err)) {
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
					}
				}
				$this->model_localisation_tax_class->editTaxClass($id, array( 'tax_class' => $v ));
			}
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	/**
     * update only one field
     *
     * @return void
     */
	public function update_rate_field() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('localisation/tax_class');
		if (!$this->user->canModify('listing_grid/tax_class')) {
			        $error = new AError('');
			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/tax_class'),
			    	                                      'reset_value' => true
			    	                             ) );
	    }

        $this->loadModel('localisation/tax_class');
		if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    foreach ($this->request->post as $key => $value ) {
				$err = $this->_validateField($key, $value);
			    if ( !empty($err) ) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
			    }
			    $data = array( $key => $value );
				$this->model_localisation_tax_class->editTaxRate($this->request->get['id'], $data);
			}
		    return;
	    }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateField( $field, $value ) {
		$err = '';
		switch( $field ) {
			case 'title' :
				if ((strlen(utf8_decode($value)) < 2) || (strlen(utf8_decode($value)) > 128))  {
					$err = $this->language->get('error_tax_title');
				}
				break;
			case 'rate' :
				if (!$value) {
					$err = $this->language->get('error_rate');
				}
				break;
		}

		return $err;
	}

	private function _validateDelete($tax_class_id) {

		$this->loadModel('catalog/product');

		$product_total = $this->model_catalog_product->getTotalproductsByTaxClassId($tax_class_id);
		if ($product_total) {
			return sprintf($this->language->get('error_product'), $product_total);
		}
	}

}
?>