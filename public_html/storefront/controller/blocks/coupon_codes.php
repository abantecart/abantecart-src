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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerBlocksCouponCodes extends AController {
	public $data = array();
	
	public function main() {

		$action = func_get_arg(0);
        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('checkout/payment');

		if (!$this->config->get('coupon_status')) {
			return null;
		}

		$this->data['coupon_status'] = $this->config->get('coupon_status');

		$entereted_cpn = ( isset($this->request->post[ 'coupon' ]) ? $this->request->post[ 'coupon' ] : $this->session->data[ 'coupon' ] );

		$form = new AForm();
		$form->setForm(array( 'form_name' => 'coupon' ));
		$this->data[ 'form_open' ] = $form->getFieldHtml(
                                array( 'type' => 'form',
                                       'name' => 'coupon',
                                       'action' => $action ));
		$this->data[ 'coupon' ] = $form->getFieldHtml( array(
                                       'type' => 'input',
		                               'name' => 'coupon',
		                               'value' => $entereted_cpn,
		                        ));
		$this->data[ 'submit' ] = $form->getFieldHtml( array(
                             'type' => 'submit',
		                     'name' => $this->language->get('button_coupon') ));

		$this->view->batchAssign($this->data);
		$this->processTemplate('blocks/coupon_form.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
?>