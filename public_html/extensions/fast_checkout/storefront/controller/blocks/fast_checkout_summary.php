<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerBlocksFastCheckoutSummary extends AController
{

    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['summaryUrl'] = $this->html->getSecureURL('r/checkout/fast_checkout_summary');
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
