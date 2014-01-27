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
class ControllerPagesToolMessageManager extends AController {
	
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
												'href' => $this->html->getSecureURL ( 'tool/message_manager' ), 
												'text' => $this->language->get ( 'heading_title' ), 
												'separator' => ' :: ' ) );
		
		$grid_settings = array (
								//id of grid
								'table_id' => 'message_grid', 
								// url to load data from
								'url' => $this->html->getSecureURL ( 'listing_grid/message_grid' ), 
								// url to send data for edit / delete
								'editurl' => $this->html->getSecureURL ( 'listing_grid/message_grid/update' ), 
								// url to update one field
								'update_field' => $this->html->getSecureURL ( 'listing_grid/message_grid/update_field' ), 
								// default sort column
								'sortname' => 'status', 
								// actions
								'actions' => array (),
								'columns_search' => false, 
								'sortable' => true,
								'multiaction_options' => array('delete'=>$this->language->get('text_delete_selected')));
		
		$grid_settings ['colNames'] = array (
											$this->language->get ( 'column_status' ), 
											$this->language->get ( 'column_title' ), 
											$this->language->get ( 'column_create_date' ),
											$this->language->get ( 'column_action' ) );
		$grid_settings ['colModel'] = array (
											array (
													'name' => 'status',
													'index' => 'status',
													'width' => 50,
													'align' => 'center',
													'sorttype' => 'string' ),
											array (
													'name' => 'title',
													'index' => 'title',
													'width' => 250,
													'align' => 'left',
													'sorttype' => 'string' ),
											array (
													'name' => 'create_date',
													'index' => 'create_date',
													'width' => 70,
													'align' => 'center',
													'sorttype' => 'string' ),
											array (
													'name' => 'action',
													'index' => 'action',
													'width' => 70,
													'align' => 'center',
													'sorttype' => 'string' ) );
		
		$form = new AForm ( 'ff' );
		$grid = $this->dispatch ( 'common/listing_grid', array (
																$grid_settings ) );
		$this->view->assign ( 'listing_grid', $grid->dispatchGetOutput () );
		$this->view->assign ( 'popup_action', $this->html->getSecureURL ( 'listing_grid/message_grid/update' ) );
		$this->view->assign ( 'notifier', $this->html->getSecureURL ( 'listing_grid/message_grid/getNotify' ) );
		$this->view->assign ( 'status', $this->language->get ( 'text_status' ) );
		$this->view->assign ( 'status_field', $form->getFieldHtml ( Array (
																			'type' => 'input', 
																			'name' => 'msg_status', 
																			'id' => 'msg_status', 
																			'attr' => 'disabled readonly '
																			 ) ) );
		
		$this->view->assign ( 'create_date', $this->language->get ( 'text_date' ) );
		$this->view->assign ( 'create_date_field', $form->getFieldHtml ( Array (
																				'type' => 'input', 
																				'name' => 'msg_create_date', 
																				'id' => 'msg_create_date', 
																				'attr' => 'disabled readonly ' ) ) );
		
		$this->view->assign ( 'repeats', $this->language->get ( 'text_repeats' ) );
		$this->view->assign ( 'repeat_field', $form->getFieldHtml ( Array (
																				'type' => 'input', 
																				'name' => 'msg_repeat', 
																				'id' => 'msg_repeat', 
																				'attr' => 'disabled readonly ' ) ) );
		
		
		$this->view->assign ( 'delete', $this->language->get ( 'text_delete' ) );
		$this->view->assign ( 'close', $this->language->get ( 'text_close' ) );
		$this->view->assign ( 'confirm', $this->language->get ( 'text_confirm' ) );
		$this->view->batchAssign (  $this->language->getASet () );
		$this->view->assign('help_url', $this->gen_help_url() );

		$this->processTemplate ( 'pages/tool/message_manager.tpl' );
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}