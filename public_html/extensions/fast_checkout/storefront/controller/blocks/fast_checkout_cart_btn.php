<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerBlocksFastCheckoutCartBtn extends AController
{

    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('fast_checkout/fast_checkout');

        $this->view->assign('cartUrl', $this->html->getSecureURL('checkout/cart'));
        $this->view->assign('cartAlt', $this->language->get('fast_checkout_back_to_cart'));
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
