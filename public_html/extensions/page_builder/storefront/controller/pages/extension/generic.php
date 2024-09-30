<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerPagesExtensionGeneric extends AController
{

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->response->setOutput('<div>Some Page Content</div>');

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
