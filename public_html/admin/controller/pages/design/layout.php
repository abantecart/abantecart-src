<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesDesignLayout extends AController {
	private $error = array(); 
     
  	public function main() {

		 //use to init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        $this->session->data['content_language_id'] = $this->config->get('storefront_language_id');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';
		if (isset($this->request->get['tmpl_id'])) {
			$url .= '&tmpl_id=' . $this->request->get['tmpl_id'];
			$tmpl_id = $this->request->get['tmpl_id'];
		} else {
			$tmpl_id = NULL;
		}
		if (isset($this->request->get['page_id'])) {
			$url .= '&page_id=' . $this->request->get['page_id'];
			$page_id = $this->request->get['page_id'];
		} else {
			$page_id = NULL;
		}
		if (isset($this->request->get['layout_id'])) {
			$url .= '&layout_id=' . $this->request->get['layout_id'];
			$layout_id = $this->request->get['layout_id'];
		} else {
			$layout_id = NULL;
		}
		
		$layout_data['templates'] = array();
		$directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
		foreach ($directories as $directory) {
			$layout_data['templates'][] = basename($directory);
		}
        $enabled_templates = $this->extensions->getExtensionsList(
            array(
              'filter' => 'template',
              'status' => 1,
            )
        );
        foreach ( $enabled_templates->rows as $template ) {
            $layout_data['templates'][] = $template['key'];
        }

		$layout_id = !$layout_id ? 1 : $layout_id;
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
		
		$layout_data['tmpl_id'] = $layout->getTemplateId();
		$layout_data['pages'] = $layout->getPages();		
		$layout_data['page'] = $settings['page'] = $layout->getPageData();
		$settings['layout'] = $layout->getActiveLayout();
		$settings['layout_drafts'] = $layout->getLayoutDrafts();
		$settings['layout_templates'] = $layout->getLayoutTemplates();
		$settings['_blocks'] = $layout->getInstalledBlocks();
		$settings['blocks'] = $layout->getLayoutBlocks();

		// hidden fields of layout form
		$settings['hidden']['page_id'] = $page_id;
		$settings['hidden']['layout_id'] = $layout_id;
		$settings['hidden']['tmpl_id'] = $layout_data['tmpl_id'];

        $this->document->initBreadcrumb( array (
            'href'      => $this->html->getSecureURL('index/home'),
            'text'      => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb( array (
            'href'      => $this->html->getSecureURL('design/layout', $url),
            'text'      => $this->language->get('heading_title') . ' - ' . $layout_data['tmpl_id'],
            'separator' => ' :: '
        ));

		$this->view->batchAssign($layout_data);
		$this->view->assign('page_url', $this->html->getSecureURL('design/layout'));
		$this->view->assign('insert', $this->html->getSecureURL('design/layout/insert', $url));
		$settings['action'] = $this->html->getSecureURL('design/layout/save', $url);

		$this->view->assign('error_warning', (isset($this->error['warning']) ? $this->error['warning'] : ''));
        $this->view->assign('success', (isset($this->session->data['success']) ? $this->session->data['success'] : ''));
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$layoutform = $this->dispatch('common/page_layout', array( $settings ) );
		$this->view->assign('layoutform', $layoutform->dispatchGetOutput());
		$this->view->assign('help_url', $this->gen_help_url('layout') );

		$this->processTemplate('pages/design/layout.tpl');
		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
	
	public function save() {

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		$url = '';
		if (isset($this->request->get['tmpl_id'])) {
			$url .= '&tmpl_id=' . $this->request->get['tmpl_id'];
			$tmpl_id = $this->request->get['tmpl_id'];
		} else {
			$tmpl_id = NULL;
		}
		if (isset($this->request->get['page_id'])) {
			$url .= '&page_id=' . $this->request->get['page_id'];
			$page_id = $this->request->get['page_id'];
		} else {
			$page_id = NULL;
		}
		if (isset($this->request->get['layout_id'])) {
			$url .= '&layout_id=' . $this->request->get['layout_id'];
			$layout_id = $this->request->get['layout_id'];
		} else {
			$layout_id = NULL;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
			$layout->savePageLayout($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->redirect($this->html->getSecureURL('design/layout', $url));
	}
}
?>