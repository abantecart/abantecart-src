<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerPagesToolRLManager extends AController
{
    public $data;

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('tool/error_log'),
            'text'      => $this->language->get('heading_title'),
            'separator' => ' :: ',
            'current'   => true,
        ));

        $this->view->assign('current_url', $this->html->currentURL());

        //load all RL types to be listed
        $rm = new AResourceManager();
        $this->view->assign('types', $rm->getResourceTypes());

        $latest_limit = 13;
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            array(
                null,
                null,
                null,
                null,
                'mode'  => 'list_all',
                'page'  => 1,
                'limit' => $latest_limit,
                'sort'  => 'date_added',
                'order' => 'DESC',
            )
        );
        $this->view->assign('latest_limit', $latest_limit);
        $this->view->assign('rl_types_url', $this->html->getSecureURL('r/tool/rl_manager/update'));
        $this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());
        $this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
        $this->processTemplate('pages/tool/rl_manager.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
