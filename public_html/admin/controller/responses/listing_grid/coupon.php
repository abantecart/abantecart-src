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
class ControllerResponsesListingGridCoupon extends AController {

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('sale/coupon');
		$this->loadModel('sale/coupon');

		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid

	    $total = $this->model_sale_coupon->getTotalCoupons(array());
		if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

	    $results = $this->model_sale_coupon->getCoupons(array());
	    $i = 0;
		$now = time();
		foreach ($results as $result) {
			// check daterange
			if ( dateISO2Int($result[ 'date_start' ]) > $now || dateISO2Int($result[ 'date_end' ]) < $now ) {
				$result['status'] = 0;
			}

            $response->rows[$i]['id'] = $result['coupon_id'];
			$response->rows[$i]['cell'] = array(
				$result['name'],
				$result['code'],
				moneyDisplayFormat($result['discount']),
				dateISO2Display($result['date_start'], $this->language->get('date_format_short')),
				dateISO2Display($result['date_end'], $this->language->get('date_format_short')),
				$this->html->buildCheckbox(array(
                    'name'  => 'status['.$result['coupon_id'].']',
                    'value' => $result['status'],
                    'style'  => 'btn_switch',
                )),
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $this->loadModel('sale/coupon');
        $this->loadLanguage('sale/coupon');
		if (!$this->user->canModify('listing_grid/coupon')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/coupon'),
					   'reset_value' => true
				));
		}

		switch ($this->request->post['oper']) {
			case 'del':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$this->model_sale_coupon->deleteCoupon($id);
				}
				break;
			case 'save':
				$ids = explode(',', $this->request->post['id']);
				if ( !empty($ids) )
				foreach( $ids as $id ) {
					$s = isset($this->request->post['status'][$id]) ? $this->request->post['status'][$id] : 0;
					$this->model_sale_coupon->editCoupon($id, array('status' => $s) );
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

        $this->loadLanguage('sale/coupon');
		$this->loadModel('sale/coupon');

	    if (!$this->user->canModify('listing_grid/coupon')) {
	  			$error = new AError('');
	  			return $error->toJSONResponse('NO_PERMISSIONS_402',
	  				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/coupon'),
	  					'reset_value' => true
	  				));
	  	}

	    if ( isset( $this->request->get['id'] ) ) {
		    foreach( $this->request->post as $field => $value ) {
			    if (($field == 'uses_total' && $value == '') || ($field == 'uses_customer' && $value == '')) {
					$value = -1;
				}

		        $err = $this->_validateForm($field, $value );
				if(in_array($field,array('date_start', 'date_end'))){
					$value = dateDisplay2ISO($value);
				}

			    if( in_array($field, array('discount','total')) ){
					$value = preformatFloat($value, $this->language->get('decimal_point') );
				}

			    if ( !$err ) {
			        $this->model_sale_coupon->editCoupon($this->request->get['id'], array( $field => $value) );
			    } else {
				    $error = new AError('');
				    return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
			    }
			    //save products to coupon
			    if ($this->request->post['coupon_product']) {
					$this->model_sale_coupon->editCouponProducts($this->request->get['id'], $this->request->post);
			    }
		    }
		    return null;
	    }

	    //request sent from jGrid. ID is key of array
        foreach ($this->request->post as $field => $value ) {
            foreach ( $value as $k => $v ) {
                $err = $this->_validateForm($field, $v );
			    if ( !$err ) {
			        $this->model_sale_coupon->editCoupon($k, array($field => $v) );
			    } else {
				    $error = new AError('');
				    return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
			    }
            }
        }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateForm( $field, $value) {

		$err = false;
		switch( $field ) {
			case 'coupon_description' :
				foreach ($value as $language_id => $v) {
					if ( isset($v['name']) )
					if ( mb_strlen($v['name']) < 2 || mb_strlen( $v['name'] ) > 64 ) {
						$err = $this->language->get('error_name');
					}

					if ( isset($v['description']) )
					if ( mb_strlen($v['description']) < 2) {
						$err = $this->language->get('error_description');
					}
				}
				break;
			case 'code':
				if ( mb_strlen($value) < 2 || mb_strlen($value) > 10) {
					$err = $this->language->get('error_code');
				}
				break;
			case 'date_start':
			case 'date_end':
				if(!dateDisplay2ISO($value)){
					$err = $this->language->get('error_date');
				}
				break;
		}

		return $err;
  	}


	public function products() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('catalog/product');

		if (isset($this->request->post['id'])) { // variant for popup listing
			$products = $this->request->post['id'];
		}else {
			$products = array();
		}
		$product_data = array();

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$product_data[] = array(
					'id' => $product_info['product_id'],
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'model'      => $product_info['model']
				);
			}
		}

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($product_data));
	}
}