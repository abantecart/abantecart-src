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
class ControllerPagesCatalogAttributeGroups extends AController {
	public $data = array();
	public $error = array();
    private $attribute_manager;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '') {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->attribute_manager = new AAttribute_Manager();
    }
 
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

    	$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('catalog/attribute_groups'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		));

		$grid_settings = array(
			'table_id' => 'attribute_groups_grid',
			'url' => $this->html->getSecureURL('listing_grid/attribute_groups'),
			'editurl' => $this->html->getSecureURL('listing_grid/attribute_groups/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/attribute_groups/update_field'),
			'sortname' => 'ag.sort_order',
			'sortorder' => 'asc',
			'columns_search' => false,
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('catalog/attribute_groups/update', '&attribute_groups_id=%ID%')
                ),
	            'save' => array(
                    'text' => $this->language->get('button_save'),
                ),
                'delete' => array(
                    'text' => $this->language->get('button_delete'),
                )
            ),
		);

        $grid_settings['colNames'] = array(
            $this->language->get('column_name'),
            $this->language->get('column_sort_order'),
            $this->language->get('column_status'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'gagd.name',
                'align' => 'left',
			),
			array(
				'name' => 'sort_order',
				'index' => 'gag.sort_order',
                'align' => 'center',
			),
			array(
				'name' => 'status',
				'index' => 'gag.status',
                'align' => 'center',
			),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign( 'insert', $this->html->getSecureURL('catalog/attribute_groups/insert') );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('global_attribute_groups_listing') );

		$this->processTemplate('pages/catalog/attribute_groups_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );

		if ($this->request->is_POST() && $this->_validateForm()) {
			$attribute_groups_id = $this->attribute_manager->addAttributeGroup($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
      		$this->redirect($this->html->getSecureURL('catalog/attribute_groups/update', '&attribute_groups_id=' . $attribute_groups_id ));
		}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}

  	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		  
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}  

    	$this->document->setTitle( $this->language->get('heading_title') );

    	if ($this->request->is_POST() && $this->_validateForm()) {
	  		$this->attribute_manager->updateAttributeGroup($this->request->get['attribute_groups_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('catalog/attribute_groups/update', '&attribute_groups_id=' . $this->request->get['attribute_groups_id'] ));
    	}
    	$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
  
  	private function _getForm() {

		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('catalog/attribute_groups');

   		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('catalog/attribute_groups'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		if (isset($this->request->get['attribute_groups_id']) && $this->request->is_GET()) {
      		$attribute_groups_info = $this->attribute_manager->getAttributeGroup(
                  $this->request->get['attribute_groups_id'],
                  $this->session->data['content_language_id']
            );
    	}

        $fields = array('name', 'sort_order', 'status');
        foreach ( $fields as $f ) {
            if (isset($this->request->post[$f])) {
                $this->data[$f] = $this->request->post[$f];
            } elseif (isset($attribute_groups_info)) {
                $this->data[$f] = $attribute_groups_info[$f];
            } else {
                $this->data[$f] = '';
            }
        }

		if (!isset($this->request->get['attribute_groups_id'])) {
			$this->data['action'] = $this->html->getSecureURL('catalog/attribute_groups/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_group');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('catalog/attribute_groups/update', '&attribute_groups_id=' . $this->request->get['attribute_groups_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') . $this->language->get('text_group');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/attribute_groups/update_field','&id='.$this->request->get['attribute_groups_id']);
			$form = new AForm('HS');
		}
		  
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: '
   		 ));  

		$form->setForm(array(
		    'form_name' => 'editFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'editFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'editFrm',
		    'attr' => 'data-confirm-exit="true"',
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

		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'name',
			'value' => $this->data['name'],
			'required' => true,
			'style' => 'large-field',
		));
		$this->data['form']['fields']['sort_order'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'sort_order',
			'value' => $this->data['sort_order'],
            'style' => 'small-field'
		));
        $this->data[ 'form' ][ 'fields' ][ 'status' ] = $form->getFieldHtml(array(
            'type' => 'checkbox',
            'name' => 'status',
            'value' => $this->data['status'],
            'style' => 'btn_switch',
        ));

		$this->view->batchAssign( $this->data );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $this->session->data['content_language_id']);
		$this->view->assign('help_url', $this->gen_help_url('global_attribute_groups_edit') );
        $this->processTemplate('pages/catalog/attribute_groups_form.tpl' );
  	}

	private function _validateForm() {
		if (!$this->user->canModify('catalog/attribute_groups')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ( mb_strlen($this->request->post['name']) < 2 || mb_strlen($this->request->post['name']) > 32 ) {
		    $this->error['name'] = $this->language->get('error_name');
		}

		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
}
