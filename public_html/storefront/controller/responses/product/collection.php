<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesProductCollection extends AController
{
    private $error = array();
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        try {
            $this->config->set('embed_mode', true);
            $cntr = $this->dispatch('pages/product/collection');
            $html_out = $cntr->dispatchGetOutput();
        } catch (AException $e) {
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->response->setOutput($html_out);
    }

}
