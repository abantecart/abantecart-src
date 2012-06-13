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
class ControllerPagesCatalogProductLayout extends AController {
	private $error = array();
	public $data = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');

		if (isset($this->request->get[ 'product_id' ]) && ($this->request->server[ 'REQUEST_METHOD' ] != 'POST')) {
			$product_id = (int)$this->request->get[ 'product_id' ];
			$product_info = $this->model_catalog_product->getProduct($this->request->get[ 'product_id' ]);
			if (!$product_info) {
				$this->session->data[ 'warning' ] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}
		}

		$this->data[ 'product_description' ] = $this->model_catalog_product->getProductDescriptions($this->request->get[ 'product_id' ]);
		$this->data[ 'heading_title' ] = $this->language->get('text_edit') .'&nbsp;'. $this->language->get('text_product');

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
		                                    'href' => $this->html->getSecureURL('catalog/product'),
		                                    'text' => $this->language->get('heading_title'),
		                                    'separator' => ' :: '
		                               ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get[ 'product_id' ]),
		                                    'text' => $this->language->get('text_edit') . $this->language->get('text_product') . ' - ' . $this->data[ 'product_description' ][ $this->session->data[ 'content_language_id' ] ][ 'name' ],
		                                    'separator' => ' :: '
		                               ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('catalog/product_layout', '&product_id=' . $this->request->get[ 'product_id' ]),
		                                    'text' => $this->language->get('tab_layout'),
		                                    'separator' => ' :: '
		                               ));


		$this->data[ 'link_general' ] = $this->html->getSecureURL('catalog/product/update', '&product_id=' . $this->request->get[ 'product_id' ]);
		$this->data[ 'link_images' ] = $this->html->getSecureURL('catalog/product_images', '&product_id=' . $this->request->get[ 'product_id' ]);
		$this->data[ 'link_relations' ] = $this->html->getSecureURL('catalog/product_relations', '&product_id=' . $this->request->get[ 'product_id' ]);
		$this->data[ 'link_options' ] = $this->html->getSecureURL('catalog/product_options', '&product_id=' . $this->request->get[ 'product_id' ]);
		$this->data[ 'link_promotions' ] = $this->html->getSecureURL('catalog/product_promotions', '&product_id=' . $this->request->get[ 'product_id' ]);
		$this->data[ 'link_extensions' ] = $this->html->getSecureURL('catalog/product_extensions', '&product_id=' . $this->request->get[ 'product_id' ]);
		$this->data[ 'link_layout' ] = $this->html->getSecureURL('catalog/product_layout', '&product_id=' . $this->request->get[ 'product_id' ]);

		$this->data[ 'active' ] = 'layout';
		$this->view->batchAssign($this->data);
		$this->data[ 'product_tabs' ] = $this->view->fetch('pages/catalog/product_tabs.tpl');

		$this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$layout = new ALayoutManager();
		$page = $layout->getPage('pages/product/product', 'product_id', $this->request->get[ 'product_id' ]);

		if ($page) {
			$page_id = $page[ 0 ][ 'page_id' ];
			$layout_id = $page[ 0 ][ 'layout_id' ];
		} else {
			$page = $layout->getPage('pages/product/product');
			if ($page && !$page[0]['key_param']) {
				$page_id = $page[ 0 ][ 'page_id' ];
				$layout_id = $page[ 0 ][ 'layout_id' ];
			}else{
				$page = $layout->getPage('generic');
				$page_id = $page[0]['page_id'];
				$layout_id = $page[0]['layout_id'];
			}
		}
		$tmpl_id = $this->config->get('config_storefront_template');
		// insert external form of layout
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
		$settings[ 'page' ] = $layout->getPageData();
		$settings[ 'layout' ] = $layout->getActiveLayout();
		$settings[ 'layout_drafts' ] = $layout->getLayoutDrafts();
		$settings[ 'layout_templates' ] = $layout->getLayoutTemplates();
		$settings[ '_blocks' ] = $layout->getInstalledBlocks();
		$settings[ 'blocks' ] = $layout->getLayoutBlocks();
		$settings[ 'action' ] = $this->html->getSecureURL('catalog/product_layout/save');

		$settings[ 'hidden' ][ 'product_id' ] = $product_id;

		$layoutform = $this->dispatch('common/page_layout', array( $settings ));

		$this->view->assign('heading_title', $this->language->get('text_edit') );
		$this->view->assign('layoutform', $layoutform->dispatchGetOutput());
		$this->view->assign('help_url', $this->gen_help_url('product_layout') );
        $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/catalog/product_layout.tpl');

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function save() {
		if (($this->request->server[ 'REQUEST_METHOD' ] != 'POST')) {
			$this->redirect($this->html->getSecureURL('catalog/product_layout'));
		}

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('catalog/product');


		$this->request->post[ 'product_id' ] = (int)$this->request->post[ 'product_id' ];
		$product_id = $this->request->post[ 'product_id' ];


		if (!$product_id) {
			$this->session->data[ 'error' ] = $this->language->get('error_product_not_found');
			$this->redirect($this->html->getSecureURL('catalog/product/update'));
		}

		$tmpl_id = $this->config->get('config_storefront_template');

		// need to know unique page existing
		$layout = new ALayoutManager();
		$page = $layout->getPage('pages/product/product', 'product_id', $product_id);

		if ($page) {
			$page_id = $page[ 0 ][ 'page_id' ];
			$layout_id = $page[ 0 ][ 'layout_id' ];
		} else {
			$page_info = array( 'controller' => 'pages/product/product',
			                    'key_param' => 'product_id',
			                    'key_value' => $product_id );

			$this->loadModel('catalog/product');
			$product_info = $this->model_catalog_product->getProductDescriptions($product_id);
			if($product_info){
				foreach($product_info as $language_id=>$description){
					if(!(int)$language_id){ continue;}
					$page_info['page_descriptions'][$language_id]['name'] = $description['name'];
				}
			}
			$this->request->post['controller'] = 'pages/content/content';
			$page_id = $layout->savePage($page_info);
            $layout_id = '';
			// need to generate layout name
			$this->request->post[ 'layout_name' ] = 'Product: ' . $product_info[ 1 ][ 'name' ].' (product_id='.$product_id.')';
		}
		$this->request->post['controller'] = 'pages/product/product';
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
		$layout->savePageLayout($this->request->post);
		$this->session->data[ 'success' ] = $this->language->get('text_success_layout');
		$this->redirect($this->html->getSecureURL('catalog/product_layout', '&product_id=' . $product_id));
	}


}