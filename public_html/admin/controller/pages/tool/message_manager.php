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
class ControllerPagesToolMessageManager extends AController {
	
	public function main() {
		
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('tool/message_manager');
		
		$this->document->setTitle ( $this->language->get ( 'heading_title' ) );
		
		$this->document->initBreadcrumb ();
		$this->document->addBreadcrumb ( array (
												'href' => $this->html->getSecureURL ( 'index/home' ), 
												'text' => $this->language->get ( 'text_home' ), 
												'separator' => FALSE ) );
		$this->document->addBreadcrumb ( array (
												'href' => $this->html->getSecureURL ( 'tool/message_manager' ), 
												'text' => $this->language->get ( 'heading_title' ), 
												'separator' => ' :: ',
												'current' => true ) );
		
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
								'sortname' => 'date_added',
								// actions
								'actions' => array(
									'view' => array(
										'text' => $this->language->get('text_view'),
										'href' => $this->html->getSecureURL ( 'listing_grid/message_grid/update','&oper=show&id=%ID%')
									),
									'delete' => array(
										'text' => $this->language->get('button_delete')
									)
								),
								'columns_search' => false, 
								'sortable' => true,
								'multiaction_options' => array('delete'=>$this->language->get('text_delete_selected')),
								'grid_ready' => 'grid_ready();');
		
		$grid_settings ['colNames'] = array (
											$this->language->get ( 'column_status' ), 
											$this->language->get ( 'column_title' ), 
											$this->language->get ( 'column_create_date' ));
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
													'name' => 'date_added',
													'index' => 'date_added',
													'width' => 70,
													'align' => 'center',
													'sorttype' => 'string' ) );
		

		$grid = $this->dispatch ( 'common/listing_grid', array (
																$grid_settings ) );
		$this->view->assign ( 'listing_grid', $grid->dispatchGetOutput () );

		$this->view->assign ( 'notifier', $this->html->getSecureURL ( 'listing_grid/message_grid/getNotify' ) );

		$this->view->batchAssign (  $this->language->getASet () );
		$this->view->assign('help_url', $this->gen_help_url() );

		$this->processTemplate ( 'pages/tool/message_manager.tpl' );
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}