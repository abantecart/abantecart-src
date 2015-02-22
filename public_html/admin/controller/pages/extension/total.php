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
class ControllerPagesExtensionTotal extends AController {

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
       		'href'      => $this->html->getSecureURL('extension/total'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$template_data['success'] = '';
		if (isset($this->session->data['success'])) {
			$template_data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		$template_data['error'] = '';
		if (isset($this->session->data['error'])) {
			$template_data['error'] = $this->session->data['error'];
			unset($this->session->data['success']);
		}

		$grid_settings = array (
			'table_id' => 'total_grid',
			'url' => $this->html->getSecureURL ( 'listing_grid/total' ),
			'update_field' => $this->html->getSecureURL ( 'listing_grid/total/update_field' ),
			'sortname' => 'sort_order',
			'sortorder' => 'asc',
			'multiselect' => 'false',
			'columns_search' => false,
			'actions' => array(
						'edit' => array(
							'text' => $this->language->get('text_edit'),
							'href' => $this->html->getSecureURL('')
						)
			),
			'grid_ready' => 'grid_ready(data);' // run custom js-trigger with userdata from json-response as parameter
		);

		$grid_settings['colNames'] = array (
			$this->language->get ( 'column_name' ),
			$this->language->get ( 'column_status' ),
			$this->language->get ( 'column_sort_order' ),
			$this->language->get ( 'column_calculation_order' )
		);
		$grid_settings['colModel'] = array (
			array ('name' => 'name', 'index' => 'name', 'width' => 320, 'align' => 'left', 'search' => false ),
			array ('name' => 'status', 'index' => 'status', 'align' => 'center', 'search' => false ),
			array ('name' => 'sort_order', 'index' => 'sort_order', 'align' => 'center', 'search' => false ),
			array ('name' => 'calculation_order', 'index' => 'calculation_order', 'align' => 'center', 'search' => false )
		);

		$grid = $this->dispatch ( 'common/listing_grid', array ($grid_settings ) );
		$this->view->assign ( 'listing_grid', $grid->dispatchGetOutput () );
		$this->view->assign('help_url', $this->gen_help_url('total') );

		$this->processTemplate('pages/extension/total.tpl' );

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function install() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		if (!$this->user->canModify('extension/total')) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->html->getSecureURL('extension/total'));
		} else {				
			$this->loadModel('setting/extension');
		
			$this->model_setting_extension->install('total', $this->request->get['extension']);

			$this->loadModel('user/user_group');
		
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/' . $this->request->get['extension']);

			$this->redirect($this->html->getSecureURL('extension/total'));
		}

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}
