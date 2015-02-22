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

class ControllerPagesToolDatasetsManager extends AController {
	private $error = array ();
	public $data;
	public function main() {
		
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
				
		$this->document->setTitle ( $this->language->get ( 'heading_title' ) );
		
		$this->document->initBreadcrumb ();
		$this->document->addBreadcrumb ( array (
												'href' => $this->html->getSecureURL ( 'index/home' ), 
												'text' => $this->language->get ( 'text_home' ), 
												'separator' => FALSE ) );
		$this->document->addBreadcrumb ( array (
												'href' => $this->html->getSecureURL ( 'tool/datasets_manager' ), 
												'text' => $this->language->get ( 'heading_title' ), 
												'separator' => ' :: ',
												'current'   => true
		) );
		
		$grid_settings = array (
								//id of grid
								'table_id' => 'dataset_grid', 
								// url to load data from
								'url' => $this->html->getSecureURL ( 'listing_grid/datasets_grid' ), 
								// url to send data for edit / delete
								'editurl' => $this->html->getSecureURL ( 'listing_grid/datasets_grid/update' ),
								// url to update one field
								'update_field' => $this->html->getSecureURL ( 'listing_grid/datasets_grid/update_field' ), 
								// default sort column
								'sortname' => 'dataset_id',
								// actions
								'actions' => array (
										'view' =>
												array (
													'text' => $this->language->get ( 'text_show' ), 
													'href' => $this->html->getSecureURL ( 'listing_grid/datasets_grid/info', '&dataset_id=%ID%' )
												)),
								'columns_search' => false, 
								'sortable' => true,
								'multiselect' => 'false',
								'grid_ready' => 'grid_ready();'
		);
		
		$grid_settings ['colNames'] = array (
											$this->language->get ( 'column_id' ), 
											$this->language->get ( 'column_name' ), 
											$this->language->get ( 'column_key' ));
		$grid_settings ['colModel'] = array (
											array (
															'name' => 'dataset_id',
															'index' => 'dataset_id',
															'width' => 30,
															'align' => 'center', 
															'sorttype' => 'string' ), 
											array (
															'name' => 'dataset_name',
															'index' => 'dataset_name',
															'width' => 450,
															'align' => 'left', 
															'sorttype' => 'string' ), 
											array (
															'name' => 'dataset_key',
															'index' => 'dataset_key',
															'width' => 150,
															'align' => 'left',
															'sorttype' => 'string' ) );
		
		$form = new AForm ( 'ds' );
		$grid = $this->dispatch ( 'common/listing_grid', array (
																$grid_settings ) );
		$this->view->assign ( 'listing_grid', $grid->dispatchGetOutput () );
		$this->view->assign ( 'popup_action', $this->html->getSecureURL ( 'listing_grid/datasets_grid/info' ) );
		$this->view->assign ( 'popup_title', $this->language->get ( 'text_popup_title' ) );

		$this->view->assign ( 'date_added', $this->language->get ( 'text_date' ) );
		$this->view->assign ( 'create_date_field', $form->getFieldHtml ( Array (
																				'type' => 'input', 
																				'name' => 'msg_create_date', 
																				'id' => 'msg_create_date', 
																				'attr' => 'readonly=true' ) ) );
		
		$this->view->assign ( 'repeats', $this->language->get ( 'text_repeats' ) );
		$this->view->assign ( 'repeat_field', $form->getFieldHtml ( Array (
																				'type' => 'input', 
																				'name' => 'msg_repeat', 
																				'id' => 'msg_repeat', 
																				'attr' => 'readonly=true' ) ) );
		
		
		//$this->view->assign ( 'delete', $this->language->get ( 'text_delete' ) );
		$this->view->assign ( 'confirm', $this->language->get ( 'text_confirm' ) );
		$this->view->batchAssign (  $this->language->getASet () );
		$this->view->assign('help_url', $this->gen_help_url() );
		$this->processTemplate ( 'pages/tool/datasets_manager.tpl' );
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}