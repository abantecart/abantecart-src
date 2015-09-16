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
class ControllerPagesCatalogManufacturer extends AController {
	public $error = array();
	public $data = array();
	private $fields = array('name', 'manufacturer_store', 'keyword', 'sort_order');
  
  	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/manufacturer'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

        $grid_settings = array(
			'table_id' => 'manufacturer_grid',
			'url' => $this->html->getSecureURL('listing_grid/manufacturer'),
			'editurl' => $this->html->getSecureURL('listing_grid/manufacturer/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/manufacturer/update_field'),
			'sortname' => 'sort_order',
			'sortorder' => 'asc',
			'drag_sort_column' => 'sort_order',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=%ID%'),
				    'children' => array_merge(array(
                                'general' => array(
                                            'text' => $this->language->get('entry_edit'),
                                            'href' => $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=%ID%'),
                                            ),
                                'layout' => array(
                                            'text' => $this->language->get('entry_layout'),
                                            'href' => $this->html->getSecureURL('catalog/manufacturer_layout', '&manufacturer_id=%ID%'),
                                            ),
                    ),(array)$this->data['grid_edit_expand'])
                ),
	            'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                )
            ),
            'grid_ready' => 'grid_ready(data);'
		);

        $grid_settings['colNames'] = array(
            '',
            $this->language->get('column_name'),
			$this->language->get('column_sort_order'),
		);
		$grid_settings['colModel'] = array(
            array(
				'name' => 'image',
				'index' => 'image',
                'align' => 'center',
				'width' => 50,
				'sortable' => false,
				'search' => false,
			),
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 600,
                'align' => 'center',
			),
			array(
				'name' => 'sort_order',
				'index' => 'sort_order',
				'width' => 100,
                'align' => 'center',
				'search' => false,
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		if( $this->config->get('config_embed_status') ){
		    $this->view->assign('embed_url', $this->html->getSecureURL('common/do_embed/manufacturers'));
	    }

		$this->document->setTitle( $this->language->get('heading_title') );
		$this->view->assign('insert', $this->html->getSecureURL('catalog/manufacturer/insert') );
		$this->view->assign('help_url', $this->gen_help_url('manufacturer_listing') );
		$this->view->assign('form_store_switch', $this->html->getStoreSwitcher());

		$this->processTemplate('pages/catalog/manufacturer_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

	}
  
  	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );
			
		if (($this->request->is_POST()) && $this->_validateForm()) {
			$manufacturer_id = $this->model_catalog_manufacturer->addManufacturer($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=' . $manufacturer_id ));
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	} 
   
  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

        $this->view->assign('error_warning', $this->error['warning']);
        $this->view->assign('success', $this->session->data['success']);
		$this->view->assign('insert', $this->html->getSecureURL('catalog/manufacturer/insert') );
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
		
    	if (($this->request->is_POST()) && $this->_validateForm()) {
			$this->model_catalog_manufacturer->editManufacturer($this->request->get['manufacturer_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=' . $this->request->get['manufacturer_id'] ));
		}

		if( $this->config->get('config_embed_status')){
			$this->view->assign('embed_url', $this->html->getSecureURL('common/do_embed/manufacturers', '&manufacturer_id=' . $this->request->get['manufacturer_id']));
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

	private function _getForm() {

		$this->view->assign('token', $this->session->data['token']);
 		$this->view->assign('error_warning', $this->error['warning']);
 		$this->view->assign('error_name', $this->error['name']);


		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('catalog/manufacturer'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
							
		$this->view->assign('cancel', $this->html->getSecureURL('catalog/manufacturer'));

		if (isset($this->request->get['manufacturer_id']) && $this->request->is_GET()) {
      		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
    	}

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($manufacturer_info) && isset($manufacturer_info[$f]) ) {
				$this->data[$f] = $manufacturer_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}
		
		$this->loadModel('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();
		if (isset($this->request->post['manufacturer_store'])) {
			$this->data['manufacturer_store'] = $this->request->post['manufacturer_store'];
		} elseif (isset($manufacturer_info)) {
			$this->data['manufacturer_store'] = $this->model_catalog_manufacturer->getManufacturerStores($this->request->get['manufacturer_id']);
		} else {
			$this->data['manufacturer_store'] = array(0);
		}

		$stores = array( 0 => $this->language->get('text_default'));
		foreach ($this->data['stores'] as $s) {
			$stores[ $s['store_id'] ] = $s['name'];
		}

        if (!isset($this->request->get['manufacturer_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/manufacturer/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_manufacturer');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=' . $this->request->get['manufacturer_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_manufacturer') . ' - ' . $this->data['name'];
			$this->data['update'] = $this->html->getSecureURL('listing_grid/manufacturer/update_field','&id='.$this->request->get['manufacturer_id']);
			$form = new AForm('HS');

            $this->data['manufacturer_edit'] = $this->html->getSecureURL('catalog/manufacturer/update', '&manufacturer_id=' . $this->request->get['manufacturer_id'] );
            $this->data['tab_edit'] = $this->language->get('entry_edit');
            $this->data['tab_layout'] = $this->language->get('entry_layout');
            $this->data['manufacturer_layout'] = $this->html->getSecureURL('catalog/manufacturer_layout', '&manufacturer_id=' . $this->request->get['manufacturer_id'] );
		}

		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'   => true
   		 ));

		$form->setForm(array(
		    'form_name' => 'editFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'editFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'editFrm',
		    'attr' => 'data-confirm-exit="true" class="aform form-horizontal"',
		    'action' => $this->data['action'],
	    ));
        $this->data['form']['submit'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'submit',
		    'text' => $this->language->get('button_save'),
		    'style' => 'button1',
	    ));
		$this->data['form']['cancel'] = $form->getFieldHtml(array(
		    'type' => 'button',
		    'name' => 'cancel',
		    'text' => $this->language->get('button_cancel'),
		    'style' => 'button2',
	    ));

		$this->data['form']['fields']['general']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'name',
			'value' => $this->data['name'],
			'required' => true,
			'style' => 'large-field',
		));
		$this->data['form']['fields']['general']['manufacturer_store'] = $form->getFieldHtml(array(
			'type' => 'checkboxgroup',
			'name' => 'manufacturer_store[]',
			'value' => $this->data['manufacturer_store'],
			'options' => $stores,
			'style' => 'chosen',
		));

		$this->data['keyword_button'] = $form->getFieldHtml(array(
				'type' => 'button',
				'name' => 'generate_seo_keyword',
				'text' => $this->language->get('button_generate'),
				'attr' => 'type="button"',
				'style' => 'btn btn-info',
		));
		$this->data['generate_seo_url'] = $this->html->getSecureURL('common/common/getseokeyword', '&object_key_name=category_id&id=' . $category_id);

		$this->data['form']['fields']['general']['keyword'] = $form->getFieldHtml(array(
				'type' => 'input',
				'name' => 'keyword',
				'value' => $this->data['keyword'],
				'attr' => ' gen-value="' . SEOEncode($this->data['category_description']['name']) . '" ',
				'help_url' => $this->gen_help_url('seo_keyword')
		));

		$this->data['form']['fields']['general']['sort_order'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'sort_order',
			'value' => $this->data['sort_order'],
			'style' => 'small-field'
		));

		$this->view->assign('help_url', $this->gen_help_url('manufacturer_edit') );
		$this->view->batchAssign( $this->data );

        $this->addChild('responses/common/resource_library/get_resources_html', 'resources_html', 'responses/common/resource_library_scripts.tpl');
        $resources_scripts = $this->dispatch(
            'responses/common/resource_library/get_resources_scripts',
            array(
                'object_name' => 'manufacturers',
                'object_id' => $this->request->get['manufacturer_id'],
				'types' => array('image','audio','video','pdf')
            )
        );
		$this->view->assign('resources_scripts', $resources_scripts->dispatchGetOutput());

		$this->processTemplate('pages/catalog/manufacturer_form.tpl' );
	}  
	 
  	private function _validateForm() {
    	if (!$this->user->canModify('catalog/manufacturer')) {
      		$this->error['warning'][] = $this->language->get('error_permission');
    	}

    	if ( mb_strlen($this->request->post['name']) < 2 || mb_strlen($this->request->post['name']) > 64 ) {
      		$this->error['warning'][] = $this->language->get('error_name');
		}
		if (($error_text = $this->html->isSEOkeywordExists('manufacturer_id='.$this->request->get['manufacturer_id'], $this->request->post['keyword']))) {
			$this->error['warning'][] = $error_text;
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
	  		return TRUE;
		} else {
			$this->error['warning'] = implode('<br>',$this->error['warning']);
	  		return FALSE;
		}
  	}    

}
