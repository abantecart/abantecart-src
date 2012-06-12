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
class ControllerResponsesListingGridStore extends AController {
    /**
     * update only one field
     *
     * @return void
     */
	public function update_field() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->loadLanguage('setting/store');
        if (!$this->user->hasPermission('modify', 'setting/store')) {
			$this->response->setOutput( sprintf($this->language->get('error_permission_modify'), 'setting/store') );
            return;
		}

        $this->loadModel('setting/store');
		if ( isset( $this->request->get['id'] ) ) {
		    //request sent from edit form. ID in url
            foreach ($this->request->post as $key => $value ) {
				$err = $this->_validateField($key, $value);
                if ( !empty($err) ) {
				    $this->response->setOutput( $err );
				    return;
			    }
			    $data = array( $key => $value );
	            $this->model_setting_store->editStore($this->request->get['id'], $data);
			}
		    return;
	    }

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _validateField( $field, $value ) {
		$err = '';

		switch( $field ) {
			case 'config_name' :
				if (!$value) {
					$err = $this->language->get('error_name');
				}
				break;
			case 'config_url' :
				if (!$value) {
					$err = $this->language->get('error_url');
				}
				break;
			case 'config_title' :
				if (!$value) {
					$err = $this->language->get('error_title');
				}
				break;
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
			case 'config_image_category_height' :
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
			case 'config_catalog_limit' :
				if (!$value) {
					$err = $this->language->get('error_limit');
				}
				break;

		}

		return $err;
	}

	private function _validateDelete($id) {
        return ;
	}

}
?>