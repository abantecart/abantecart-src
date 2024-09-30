<?php

class ControllerResponsesDesignEditBlock extends AController
{

    public function main()
    {
        //use to init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        try {
            $cntr = $this->dispatch('pages/design/blocks/edit');
            $html_out = $cntr->dispatchGetOutput();
        } catch (AException $e) {
        }

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->response->setOutput($html_out);

    }

}