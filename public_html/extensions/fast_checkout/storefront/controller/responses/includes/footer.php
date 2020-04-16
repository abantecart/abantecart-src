<?php
/*------------------------------------------------------------------------------
$Id$
  
This file and its content is copyright of AlgoZone Inc - ©AlgoZone Inc 2003-2016. All rights reserved.

You may not, except with our express written permission, modify, distribute or commercially exploit the content. Nor may you transmit it or store it in any other website or other form of electronic retrieval system.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesIncludesFooter extends AController
{
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('common/footer');
        $this->data['text_copy'] = $this->config->get('store_name').' &copy; '.date('Y', time());

        $this->data['text_project_label'] = $this->language->get('text_powered_by').' '.project_base();

        if ($this->config->get('config_google_analytics_code')) {
            $this->data['google_analytics'] = $this->config->get('config_google_analytics_code');
        } else {
            $this->data['google_analytics'] = '';
        }

        $this->view->assign('scripts_bottom', $this->document->getScriptsBottom());
        $this->data['text_project_label'] = $this->language->get('text_powered_by').' '.project_base();

        $this->view->batchAssign($this->data);
        if ($this->session->data['fast_checkout_view_mode'] != 'modal') {
            $tpl = 'responses/includes/page_footer.tpl';
        } else {
            $tpl = 'responses/includes/footer.tpl';
        }
        $this->processTemplate($tpl);
        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}