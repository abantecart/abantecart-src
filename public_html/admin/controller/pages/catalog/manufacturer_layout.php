<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

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
class ControllerPagesCatalogManufacturerLayout extends AController {
	private $error = array();
	public $data = array();

	public function main() {
		$page_controller = 'pages/product/manufacturer';
		$page_key_param = 'manufacturer_id';
		$manufacturer_id = (int)$this->request->get['manufacturer_id'];

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('catalog/manufacturer');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/manufacturer');
		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer( $manufacturer_id );

		if (has_value($manufacturer_id) && ($this->request->server[ 'REQUEST_METHOD' ] != 'POST')) {
			if (!$manufacturer_info) {
				$this->session->data[ 'warning' ] = $this->language->get('error_manufacturer_not_found');
				$this->redirect($this->html->getSecureURL('catalog/manufacturer'));
			}
		}


		$this->data[ 'heading_title' ] = $this->language->get('text_edit') . $this->language->get('text_manufacturer') . ' - ' . $manufacturer_info[ 'name' ];
		$this->data[ 'manufacturer_edit' ] = $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=' . $manufacturer_id);
		$this->data[ 'tab_edit' ] = $this->language->get('entry_edit');
		$this->data[ 'tab_layout' ] = $this->language->get('entry_layout');
		$this->data[ 'manufacturer_layout' ] = $this->html->getSecureURL('catalog/manufacturer_layout', '&manufacturer_id=' . $manufacturer_id);

		$this->view->assign('error_warning', $this->error[ 'warning' ]);
		$this->view->assign('success', $this->session->data[ 'success' ]);
		if (isset($this->session->data[ 'success' ])) {
			unset($this->session->data[ 'success' ]);
		}

		$this->document->initBreadcrumb(array(
		                                     'href' => $this->html->getSecureURL('index/home'),
		                                     'text' => $this->language->get('text_home'),
		                                     'separator' => FALSE
		                                ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/manufacturer'),
		                                    'text' => $this->language->get('heading_title'),
		                                    'separator' => ' :: '
		                               ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=' . $manufacturer_id),
		                                    'text' => $this->language->get('text_edit') . $this->language->get('text_manufacturer') . ' - ' . $manufacturer_info[ 'name' ],
		                                    'separator' => ' :: '
		                               ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/manufacturer_layout', '&manufacturer_id=' . $manufacturer_id),
		                                    'text' => $this->language->get('entry_layout'),
		                                    'separator' => ' :: '
		                               ));


		$this->data[ 'active' ] = 'layout';
		$this->view->batchAssign($this->data);

		$layout = new ALayoutManager();
		//get existing page layout or generic
		$page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $manufacturer_id);
		$page_id = $page_layout['page_id'];
		$layout_id = $page_layout['layout_id'];
		$tmpl_id = $this->config->get('config_storefront_template');

		// insert external form of layout
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
		$settings = array();
		$settings[ 'action' ] = $this->html->getSecureURL('catalog/manufacturer_layout/save');
		// hidden fields of layout form
		$settings[ 'hidden' ][ 'page_id' ] = $page_id;
		$settings[ 'hidden' ][ 'layout_id' ] = $layout_id;
		$settings[ 'hidden' ][ 'manufacturer_id' ] = $manufacturer_id;
		$settings['allow_clone'] = true;

		$layoutform = $this->dispatch('common/page_layout', array( $settings, $layout ));

		$this->view->assign('help_url', $this->gen_help_url('manufacturer_layout') );
		$this->view->assign('layoutform', $layoutform->dispatchGetOutput());
		$this->processTemplate('pages/catalog/manufacturer_layout.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function save() {
		if (($this->request->server[ 'REQUEST_METHOD' ] != 'POST')) {
			$this->redirect($this->html->getSecureURL('catalog/manufacturer_layout'));
		}

		$page_controller = 'pages/product/manufacturer';
		$page_key_param = 'manufacturer_id';
		$manufacturer_id = (int)$this->request->post['manufacturer_id'];

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('catalog/manufacturer');

		if (!has_value($manufacturer_id)) {
			$this->session->data[ 'error' ] = $this->language->get('error_product_not_found');
			$this->redirect($this->html->getSecureURL('catalog/manufacturer/update'));
		}

		$tmpl_id = $this->config->get('config_storefront_template');
		$post_data = $this->request->post;
		// need to know unique page existing
		$layout = new ALayoutManager();
		$pages = $layout->getPages($page_controller, $page_key_param, $manufacturer_id);
		if ( count($pages) ) {
			$page_id = $pages[0]['page_id'];
			$layout_id = $pages[0]['layout_id'];
		} else {

			$page_info = array( 'controller' => $page_controller,
			                    'key_param' => $page_key_param,
			                    'key_value' => $manufacturer_id );

            $languages = $this->language->getAvailableLanguages();
			$this->loadModel('catalog/manufacturer');
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
			if($manufacturer_info){
                foreach ( $languages as $l ) {
                    $page_info['page_descriptions'][ $l['language_id'] ] = $manufacturer_info;
                }
			}
			$page_id = $layout->savePage($page_info);
            $layout_id = '';

			// need to generate layout name
			$post_data['layout_name'] = 'Manufacturer: ' . $manufacturer_info['name'];
		}

		//create new instance with specific template/page/layout data
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
		if (has_value($post_data['layout_change'])) {	
			//update layout request. Clone source layout
			$layout->clonePageLayout($post_data['layout_change'], $layout_id, $post_data['layout_name']);
		} else {
			//save new layout
			$post_data['controller'] = $page_controller;
			$layout->savePageLayout($post_data);
		}

		$this->session->data[ 'success' ] = $this->language->get('text_success_layout');
		$this->redirect($this->html->getSecureURL('catalog/manufacturer_layout', '&manufacturer_id=' . $manufacturer_id));
	}

}