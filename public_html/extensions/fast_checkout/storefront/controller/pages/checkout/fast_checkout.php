<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesCheckoutFastCheckout extends AController
{
    private $error = array();
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['cart_url'] = $this->html->getSecureURL('r/checkout/pay',
            '&order_id='.$this->session->data['fast_checkout']['cart_key'].'&viewport='.$this->request->get_or_post('viewport'));

        $this->view->batchAssign($this->data);

        $this->view->setTemplate('pages/checkout/fast_checkout.tpl');
        $this->processTemplate();
        //update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
