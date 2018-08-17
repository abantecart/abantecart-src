<?php
/*Neowize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class Controllerpagesneowizedashboard extends AController
{

    public $data = array();
    private $error = array();

    // generate guid for api key / secret key
    private function create_guid()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    // main action of this controller, eg render the dashboard page
    public function main()
    {

        // init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        // set page title
        $title = 'Neowize Insights';
        $this->document->setTitle($title);

        $this->document->initBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('neowize/dashboard'),
            'text'      => $title,
            'separator' => ' :: ',
            'current'   => true,
        ));

        // get Neowize settings
        $settings = NeowizeUtils::getConfig();

        // pass basic data to the template to be used as a basic authentication mechanism.
        $this->loadModel('setting/store');
        $store_info = $this->model_setting_store->getStore($this->request->get['store_id']);
        $this->view->assign('store_name', $store_info['store_name']);
        $this->view->assign('store_url', $store_info['config_url']);
        $this->view->assign('api_key', $settings['api_key']);
        $this->view->assign('secret_key', $settings['secret_key']);
        $this->view->assign('php_version', PHP_VERSION);

        // render the neowize insights dashboard template
        $this->processTemplate('pages/neowize/dashboard.tpl');

        // update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
