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
class ControllerResponsesListingGridSetting extends AController {
	private $error = array();
	public $groups = array('details', 'general', 'checkout', 'appearance', 'mail', 'api', 'system');

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('setting/setting');
	    $this->loadModel('setting/setting');

		//Prepare filter config
 		$grid_filter_params = array( 'group', 'key' );
	    $filter_grid = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
	    
		$total = $this->model_setting_setting->getTotalSettings( $filter_grid->getFilterData() );
	    $response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages( $total );
		$response->records = $total;

	    $resource = new AResource('image');
	    $results = $this->model_setting_setting->getAllSettings( $filter_grid->getFilterData() );

	    $i = 0;
		foreach ($results as $result) {

			$response->rows[$i]['id'] = $result['group'].'-'.$result['key'];
			$response->rows[$i]['cell'] = array(
				$result['group'],
				$result['key'],
				$result['value'],
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

        $this->loadLanguage('setting/setting');
        if (!$this->user->hasPermission('modify', 'setting/setting')) {
			$this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'setting/setting') );
            return;
		}

        $this->loadModel('setting/setting');
		if ( isset( $this->request->get['group'] ) ) {
		    //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value ) {
				$err = $this->_validateField( $this->request->get['group'], $key, $value);
                if ( !empty($err) ) {
				    $this->response->setOutput($err);
				    return;
			    }
			    $data = array( $key => $value );
			    			    
				$this->model_setting_setting->editSetting($this->request->get['group'], $data, $this->request->get['store_id']);
			}
		    return;
	    }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateField( $group, $field, $value ) {
		$err = '';

		switch ( $group ) {
			case 'general':
				switch( $field ) {
					case 'store_name' :
						if (!$value) {
							$err = $this->language->get('error_name');
						}
						break;
					case 'store_url' :
						if (!$value) {
							$err = $this->language->get('error_url');
						}
						break;
					case 'config_owner' :
						if ((strlen(utf8_decode($value)) < 2) || (strlen(utf8_decode($value)) > 64)) {
							$err = $this->language->get('error_owner');
						}
						break;
					case 'config_address' :
						if ((strlen(utf8_decode($value)) < 2) || (strlen(utf8_decode($value)) > 256)) {
							$err = $this->language->get('error_address');
						}
						break;
					case 'store_main_email' :
						$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';
						if ((strlen(utf8_decode($value)) > 96) || (!preg_match($pattern, $value))) {
							$this->error['email'] = $this->language->get('error_email');
						}
						break;
					case 'config_telephone' :
						if ((strlen(utf8_decode($value)) < 2) || (strlen(utf8_decode($value)) > 32)) {
							$err = $this->language->get('error_telephone');
						}
						break;
				}
				break;

			case 'store':
				switch( $field ) {
					case 'config_title' :
						if (!$value) {
							$err = $this->language->get('error_title');
						}
						break;
				}
				break;

			case 'local':
				break;

			case 'options':
				switch( $field ) {
					case 'config_admin_limit' :
					case 'config_catalog_limit' :
					case 'config_bestseller_limit' :
					case 'config_featured_limit' :
					case 'config_latest_limit' :
						if (!$value) {
							$err = $this->language->get('error_limit');
						}
						break;
				}
				break;

			case 'images':
				switch( $field ) {
					case 'config_image_thumb_width' :
					case 'config_image_thumb_height' :
						if (!$value) {
							$err = $this->language->get('error_image_thumb');
						}
						break;
					case 'config_image_popup_width' :
					case 'config_image_popup_height' :
						if (!$value) {
							$err = $this->language->get('error_image_popup');
						}
						break;
					case 'config_image_category_width' :
					case 'config_image_category_width' :
						if (!$value) {
							$err = $this->language->get('error_image_category');
						}
						break;
					case 'config_image_product_width' :
					case 'config_image_product_height' :
						if (!$value) {
							$err = $this->language->get('error_image_product');
						}
						break;
					case 'config_image_additional_width' :
					case 'config_image_additional_height' :
						if (!$value) {
							$err = $this->language->get('error_image_additional');
						}
						break;
					case 'config_image_related_width' :
					case 'config_image_related_height' :
						if (!$value) {
							$err = $this->language->get('error_image_related');
						}
						break;
                    case 'config_image_cart_width' :
					case 'config_image_cart_height' :
						if (!$value) {
							$err = $this->language->get('error_image_cart');
						}
						break;
                    case 'config_image_grid_width' :
					case 'config_image_grid_height' :
						if (!$value) {
							$err = $this->language->get('error_image_grid');
						}
						break;
				}
				break;

			case 'mail':
				break;

			case 'server':
				switch( $field ) {
					case 'config_error_filename' :
						if (!$value) {
							$err = $this->language->get('error_error_filename');
						}
						break;
					case 'config_upload_max_size':
						$this->request->post['config_upload_max_size'] = preformatInteger($this->request->post['config_upload_max_size']);
						break;
				}
				break;

			default:
		}


		return $err;
	}

	private function _validateDelete($id) {
        return ;
	}

}
?>