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
class ControllerResponsesListingGridCustomer extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('sale/customer');
		$this->loadModel('sale/customer');
		$this->load->library('json');

	    $approved = array(
			1 => $this->language->get('text_yes'),
			0 => $this->language->get('text_no'),
		);

		$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction

	    $data = array(
			'sort'  => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		);
	    if ( isset( $this->request->get['customer_group'] ) && $this->request->get['customer_group'] != '' )
		    $data['filter_customer_group_id'] = $this->request->get['customer_group'];
	    if ( isset( $this->request->get['status'] ) && $this->request->get['status'] != '' )
		    $data['filter_status'] = $this->request->get['status'];
	    if ( isset( $this->request->get['approved'] ) && $this->request->get['approved'] != '' )
		    $data['filter_approved'] = $this->request->get['approved'];
		$allowedFields = array( 'name', 'c.email');
	    if (isset($this->request->post['_search']) && $this->request->post['_search'] == 'true') {
		    $searchData = AJson::decode(htmlspecialchars_decode($this->request->post['filters']), true);

		    foreach ( $searchData['rules'] as $rule ) {
			    if ( !in_array($rule['field'], $allowedFields) ) continue;
			    $data['filter_'.$rule['field']] = $rule['data'];
		    }
	    }

	    $total = $this->model_sale_customer->getTotalCustomers($data);
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

	    $results = $this->model_sale_customer->getCustomers($data);
	    $i = 0;
		foreach ($results as $result) {

            $response->rows[$i]['id'] = $result['customer_id'];
			$response->rows[$i]['cell'] = array(
				$result['name'],
				$result['email'],
				$result['customer_group'],
				$this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['customer_id'].']',
                    'value' => $result['status'],
                    'style'  => 'btn_switch',
                )),
				$this->html->buildSelectbox(array(
                    'name'  => 'approved['.$result['customer_id'].']',
                    'value' => $result['approved'],
                    'options'  => $approved,
                )),
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);


		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $this->loadModel('sale/customer');
        $this->loadLanguage('sale/customer');
        if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'sale/customer') );
            return;
		}

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$this->model_sale_customer->deleteCustomer($id);
				}
				break;
			case 'save':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {

					$err = $this->_validateForm('status', $this->request->post['status'][$id] );
					if ( !$err ) {
						$this->model_sale_customer->editCustomerField($id, 'status', $this->request->post['status'][$id]);
					} else {
						$this->response->setOutput( $err );
						return;
					}
					$err = $this->_validateForm('approved', $this->request->post['approved'][$id] );
					if ( !$err ) {
						$this->model_sale_customer->editCustomerField($id, 'approved', $this->request->post['approved'][$id]);
					} else {
						$this->response->setOutput( $err );
						return;
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

        $this->loadLanguage('sale/customer');
		$this->loadModel('sale/customer');

        if (!$this->user->hasPermission('modify', 'sale/customer')) {
			$this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'sale/customer') );
            return;
		}

	    if ( isset( $this->request->get['id'] ) ) {
		    foreach( $this->request->post as $field => $value ) {
		        $err = $this->_validateForm($field, $value );
			    if ( !$err ) {
			        $this->model_sale_customer->editCustomerField($this->request->get['id'], $field, $value);
			    } else {
				    $this->response->setOutput( $err );
				    return;
			    }
		    }
		    //update controller data
        	$this->extensions->hk_UpdateData($this,__FUNCTION__);
		    return;
	    }

	    //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value ) {
            foreach ( $value as $k => $v ) {
                $err = $this->_validateForm($field, $v );
			    if ( !$err ) {
			        $this->model_sale_customer->editCustomerField($k, $field, $v);
			    } else {
				    $this->response->setOutput( $err );
				    return;
			    }
            }
        }


		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateForm( $field, $value) {

		$err = false;
		switch( $field ) {
			case 'firstname' :
				if ((strlen(utf8_decode($value)) < 1) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_firstname');
				}
				break;
			case 'lastname':
				if ((strlen(utf8_decode($value)) < 1) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_lastname');
				}
				break;
			case 'email':
				$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';
				if ((strlen(utf8_decode($value)) > 96) || (!preg_match($pattern, $value))) {
					$err = $this->language->get('error_email');
				}
				break;
			case 'telephone':
				if ((strlen(utf8_decode($value)) < 3) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_telephone');
				}
				break;
		}

		return $err;
  	}

}