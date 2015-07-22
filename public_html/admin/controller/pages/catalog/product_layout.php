<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

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
		$page_controller = 'pages/product/product';
		$page_key_param = 'product_id';
		$product_id = (int)$this->request->get['product_id'];
		$page_url = $this->html->getSecureURL('catalog/product_layout', '&product_id=' . $product_id);

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('catalog/product');
		$this->loadLanguage('design/layout');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->loadModel('catalog/product');

		if (has_value($product_id) && $this->request->is_GET()) {
			$product_info = $this->model_catalog_product->getProduct( $product_id );
			if (!$product_info) {
				unset($this->session->data['success']);
				$this->session->data['warning'] = $this->language->get('error_product_not_found');
				$this->redirect($this->html->getSecureURL('catalog/product'));
			}

		}

		$this->data['help_url'] = $this->gen_help_url('product_layout');
		$this->data['product_description'] = $this->model_catalog_product->getProductDescriptions( $product_id );
		$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_product') . ' - ' . $this->data['product_description'][ $this->session->data['content_language_id'] ]['name'];

		$this->document->setTitle($this->data['heading_title']);

	    // Alert messages
	    if (isset($this->session->data['warning'])) {
	      $this->data['error_warning'] = $this->session->data['warning'];
	      unset($this->session->data['warning']);
	    }
	    if (isset($this->session->data['success'])) {
	      $this->data['success'] = $this->session->data['success'];
	      unset($this->session->data['success']);
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
		                                    'href' => $this->html->getSecureURL('catalog/product/update', '&product_id=' . $product_id),
		                                    'text' => $this->data['heading_title'],
		                                    'separator' => ' :: '
		                               ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $page_url,
		                                    'text' => $this->language->get('tab_layout'),
		                                    'separator' => ' :: ',
		                                    'current' => true
		                               ));
		//active tab
		$this->data['active'] = 'layout';
		//load tabs controller
		$tabs_obj = $this->dispatch('pages/catalog/product_tabs', array( $this->data ) );
		$this->data['product_tabs'] = $tabs_obj->dispatchGetOutput();
		unset($tabs_obj);

		$this->addChild('pages/catalog/product_summary', 'summary_form', 'pages/catalog/product_summary.tpl');

		$layout = new ALayoutManager();
		//get existing page layout or generic
		$page_layout = $layout->getPageLayoutIDs($page_controller, $page_key_param, $product_id);
		$page_id = $page_layout['page_id'];
		$layout_id = $page_layout['layout_id'];
		if (isset($this->request->get['tmpl_id'])) {
			$tmpl_id = $this->request->get['tmpl_id'];
		} else {
			$tmpl_id = $this->config->get('config_storefront_template');
		}
	    $params = array(
	      'product_id' => $product_id,
	      'page_id' => $page_id,
	      'layout_id' => $layout_id,
	      'tmpl_id' => $tmpl_id,
	    );
	    $url = '&'.$this->html->buildURI($params);
	
		// get templates
		$this->data['templates'] = array();
		$directories = glob(DIR_STOREFRONT . 'view/*', GLOB_ONLYDIR);
		foreach ($directories as $directory) {
		  $this->data['templates'][] = basename($directory);
		}
		$enabled_templates = $this->extensions->getExtensionsList(array(
		  'filter' => 'template',
		  'status' => 1,
		));
		foreach ($enabled_templates->rows as $template) {
		  $this->data['templates'][] = $template['key'];
		}

		$action = $this->html->getSecureURL('catalog/product_layout/save');
	    // Layout form data
	    $form = new AForm('HT');
	    $form->setForm(array(
	      'form_name' => 'layout_form',
	    ));
	
	    $this->data['form_begin'] = $form->getFieldHtml(array(
	      'type' => 'form',
	      'name' => 'layout_form',
	      'attr' => 'data-confirm-exit="true"',
	      'action' => $action
	    ));
	
	    $this->data['hidden_fields'] = '';
	    foreach ($params as $name => $value) {
	      $this->data[$name] = $value;
	      $this->data['hidden_fields'] .= $form->getFieldHtml(array(
	        'type' => 'hidden',
	        'name' => $name,
	        'value' => $value
	      ));
	    }
	
	    $this->data['page_url'] = $page_url;
	    $this->data['current_url'] = $this->html->getSecureURL('catalog/product_layout', $url);
	
		// insert external form of layout
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
	
	    $layoutform = $this->dispatch('common/page_layout', array($layout));
	    $this->data['layoutform'] = $layoutform->dispatchGetOutput();
		
		//build pages and available layouts for clonning
		$this->data['pages'] = $layout->getAllPages();
		$av_layouts = array( "0" => $this->language->get('text_select_copy_layout'));
		foreach($this->data['pages'] as $page){
			if ( $page['layout_id'] != $layout_id ) {
				$av_layouts[$page['layout_id']] = $page['layout_name'];
			}
		}

		$form = new AForm('HT');
		$form->setForm(array(
		    'form_name' => 'cp_layout_frm',
	    ));
	    
		$this->data['cp_layout_select'] = $form->getFieldHtml(array('type' => 'selectbox',
													'name' => 'layout_change',
													'value' => '',
													'options' => $av_layouts ));

		$this->data['cp_layout_frm'] = $form->getFieldHtml(array('type' => 'form',
		                                        'name' => 'cp_layout_frm',
		                                        'attr' => 'class="aform form-inline"',
			                                    'action' => $action));
		if($this->config->get('config_embed_status')){
			$this->data['embed_url'] = $this->html->getSecureURL('common/do_embed/product', '&product_id=' . $this->request->get['product_id']);
		}
		
        $this->view->batchAssign( $this->data );
		$this->processTemplate('pages/catalog/product_layout.tpl');
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function save() {
		if ($this->request->is_GET()) {
			$this->redirect($this->html->getSecureURL('catalog/product_layout'));
		}

		$page_controller = 'pages/product/product';
		$page_key_param = 'product_id';
		$product_id = $this->request->post['product_id'];

		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('catalog/product');

		if (!has_value($product_id)) {
			unset($this->session->data['success']);
			$this->session->data['warning'] = $this->language->get('error_product_not_found');
			$this->redirect($this->html->getSecureURL('catalog/product/update'));
		}

		// need to know if unique page existing
		$post_data = $this->request->post;
		$tmpl_id = $post_data['tmpl_id'];
		$layout = new ALayoutManager();
		$pages = $layout->getPages($page_controller, $page_key_param, $product_id);
		if ( count($pages) ) {
			$page_id = $pages[0]['page_id'];
			$layout_id = $pages[0]['layout_id'];
		} else {
			$page_info = array( 'controller' => $page_controller,
			                    'key_param' => $page_key_param,
			                    'key_value' => $product_id );

			$this->loadModel('catalog/product');
			$product_info = $this->model_catalog_product->getProductDescriptions($product_id);
			if($product_info){
				foreach($product_info as $language_id=>$description){
					if(!(int)$language_id){ continue;}
					$page_info['page_descriptions'][$language_id] = $description;
				}
			}
			$page_id = $layout->savePage($page_info);
            $layout_id = '';
			// need to generate layout name
			$default_language_id = $this->language->getDefaultLanguageID();
			$post_data['layout_name'] = 'Product: ' . $product_info[$default_language_id]['name'];
		}

		//create new instance with specific template/page/layout data
		$layout = new ALayoutManager($tmpl_id, $page_id, $layout_id);
		if (has_value($post_data['layout_change'])) {	
			//update layout request. Clone source layout
			$layout->clonePageLayout($post_data['layout_change'], $layout_id, $post_data['layout_name']);
			$this->session->data[ 'success' ] = $this->language->get('text_success_layout');
		} else {
			//save new layout
      		$layout_data = $layout->prepareInput($post_data);
      		if ($layout_data) {
      			$layout->savePageLayout($layout_data);
      			$this->session->data[ 'success' ] = $this->language->get('text_success_layout');
      		} 
		}

		$this->redirect($this->html->getSecureURL('catalog/product_layout', '&product_id=' . $product_id));
	}

}