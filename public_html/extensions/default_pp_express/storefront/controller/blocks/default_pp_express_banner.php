<?php
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerBlocksDefaultPPExpressBanner extends AController {

	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

        $min = $this->config->get("default_pp_express_payment_minimum_total");
        $max = $this->config->get("default_pp_express_payment_maximum_total");
        $amount = $this->cart->getFinalTotal();
        if ( 	(has_value( $min ) && $amount < $min )
            ||  (has_value( $max ) && $amount > $max )  ) {
            return null;
        }

        if(!$this->config->get('default_pp_express_billmelater')){
            return null;
        }



		$this->view->assign('pp_publisher_id', $this->config->get('default_pp_express_billmelater_publisher_id') );
		$this->processTemplate();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}