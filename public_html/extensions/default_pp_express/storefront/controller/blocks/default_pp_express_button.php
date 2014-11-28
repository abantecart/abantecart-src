<?php
	if (! defined ( 'DIR_CORE' )) {
		header ( 'Location: static_pages/' );
	}

class ControllerBlocksDefaultPPExpressButton extends AController {

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

		$language = $this->language->getCurrentLanguage();
		$locale = explode(',',$language['locale']);


		$this->data['image_src'] = 'https://www.paypal.com/'.$locale[1].'/i/btn/btn_xpressCheckout.gif';
		$this->data['href'] = $this->html->getSecureURL('r/extension/default_pp_express/set_pp');

		if ( $this->config->get('default_pp_express_billmelater') ) {
			$this->data['billmelater'] = array(
				'image_src' => '//www.paypalobjects.com/webstatic/'.$locale[1].'/btn/btn_bml_SM.png',
				'href' => $this->html->getSecureURL('r/extension/default_pp_express/set_pp', '&fundsource=bml'),
			);
			$this->data['billmelater_txt'] = array(
				'image_src' => 'https://www.paypalobjects.com/webstatic/'.$locale[1].'/btn/btn_bml_text.png',
			);
		}


		$this->view->batchAssign( $this->data );
		$this->processTemplate();

		//init controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}
}