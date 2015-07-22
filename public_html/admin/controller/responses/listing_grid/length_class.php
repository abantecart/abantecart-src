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
class ControllerResponsesListingGridLengthClass extends AController {

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('localisation/length_class');
	    $this->loadModel('localisation/length_class');

		$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

	    // process jGrid search parameter
	    $allowedDirection = array('asc', 'desc');

	    if ( !in_array($sord, $allowedDirection) ) $sord = $allowedDirection[0];

	    $data = array(
			'sort' => $sidx,
			'order' => strtoupper($sord),
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		    'content_language_id' => $this->language->getContentLanguageID()
		);

		$total = $this->model_localisation_length_class->getTotalLengthClasses();
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    if($page > $total_pages){
            $page = $total_pages;
            $data['start'] = ($page - 1) * $limit;
        }


	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

	    $results = $this->model_localisation_length_class->getLengthClasses($data);
	    $i = 0;
		foreach ($results as $result) {
            $response->rows[$i]['id'] = $result['length_class_id'];
			$response->rows[$i]['cell'] = array(
				$this->html->buildInput(array(
                    'name'  => 'length_class_description['.$result['length_class_id'].']['.$this->session->data['content_language_id'].'][title]',
                    'value' => $result['title'],
                )),
				$this->html->buildInput(array(
                    'name'  => 'length_class_description['.$result['length_class_id'].']['.$this->session->data['content_language_id'].'][unit]',
                    'value' => $result['unit'],
                )),
				$this->html->buildInput(array(
                    'name'  => 'value['.$result['length_class_id'].']',
                    'value' => $result['value'],
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

	    $this->loadModel('localisation/length_class');
		$this->loadModel('catalog/product');
        $this->loadLanguage('localisation/length_class');
		if (!$this->user->canModify('listing_grid/length_class')) {
			        $error = new AError('');
			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/length_class'),
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
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
					}
					$this->model_localisation_length_class->deleteLengthClass($id);
				}
				break;
			case 'save':
				$fields = array('length_class_description', 'value');
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					foreach ( $fields as $f ) {
						if ( isset($this->request->post[$f][$id]) ) {
							$err = $this->_validateField($f, $this->request->post[$f][$id]);
							if ( !empty($err) ) {
								$error = new AError('');
								return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
							}
							$this->model_localisation_length_class->editLengthClass($id, array($f => $this->request->post[$f][$id]) );
						}
					}
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

        $this->loadLanguage('localisation/length_class');
	    if (!$this->user->canModify('listing_grid/length_class')) {
	  			        $error = new AError('');
	  			    	return $error->toJSONResponse('NO_PERMISSIONS_402',
	  			    	                               array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/length_class'),
	  			    	                                      'reset_value' => true
	  			    	                             ) );
	  	}

        $this->loadModel('localisation/length_class');
		if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
		    foreach ($this->request->post as $key => $value ) {
				$err = $this->_validateField($key, $value);
			    if ( !empty($err) ) {
				    $error = new AError('');
				    return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
			    }
			    $data = array( $key => $value );
				$this->model_localisation_length_class->editLengthClass($this->request->get['id'], $data);
			}
		    return null;
	    }

	    //request sent from jGrid. ID is key of array
	    $fields = array('length_class_description', 'value', );
	    foreach ( $fields as $f ) {
		    if ( isset($this->request->post[$f]) )
			foreach ( $this->request->post[$f] as $k => $v ) {
				$err = $this->_validateField($f, $v);
				if ( !empty($err) ) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
				}
				$this->model_localisation_length_class->editLengthClass($k, array($f => $v) );
			}
	    }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateField( $field, $value ) {
		$err = '';
		switch( $field ) {
			case 'length_class_description' :
				foreach ($value as $v) {
					if ( isset($v['title']) )
					if ( mb_strlen($v['title']) < 2 || mb_strlen($v['title']) > 32 ) {
						$err = $this->language->get('error_title');
					}

					if ( isset($v['unit']) )
					if ( !$v['unit'] || mb_strlen($v['unit']) > 4 ) {
						$err = $this->language->get('error_unit');
					}
				}
				break;
		}

		return $err;
	}

	private function _validateDelete( $length_class_id ) {
		$length_class_info = $this->model_localisation_length_class->getLengthClass($length_class_id);
		if ($length_class_info && ($this->config->get('config_length_class') == $length_class_info['unit'])) {
			return $this->language->get('error_default');
		}

		$product_total = $this->model_catalog_product->getTotalProductsByLengthClassId($length_class_id);
		if ($product_total) {
			return sprintf($this->language->get('error_product'), $product_total);
		}
	}

}
