<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesCheckoutFastCheckoutSuccess extends AController
{
    private $error = array();
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['success_url'] = $this->html->getSecureURL('r/checkout/pay/success', '&viewport=window&order_id='.$this->request->get['order_id']);

        $this->view->batchAssign($this->data);

        $this->view->setTemplate('pages/checkout/fast_checkout_success.tpl');
        $this->processTemplate();
        //update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
