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

class ControllerPagesToolInstallUpgradeHistory extends AController {
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
												'href' => $this->html->getSecureURL ( 'tool/install_upgrade_history' ),
												'text' => $this->language->get ( 'heading_title' ), 
												'separator' => ' :: ',
												'current' => true) );
		
		$grid_settings = array (
								//id of grid
								'table_id' => 'install_upgrade_history',
								// url to load data from
								'url' => $this->html->getSecureURL ( 'listing_grid/install_upgrade_history' ),
								// url to send data for edit / delete
								'editurl' => '',
								'multiselect'=>'false',
								// url to update one field
								'update_field' => '',
								// default sort column
								'sortname' => 'date_added',
								// actions
								'actions' => '',
								'columns_search' => false,
								'sortable' => true );
		
		$grid_settings ['colNames'] = array (
											'#',
											$this->language->get ( 'column_date_added' ),
											$this->language->get ( 'column_type' ),
											$this->language->get ( 'column_name' ),
											$this->language->get ( 'column_version' ),
											$this->language->get ( 'column_backup_date' ),
											$this->language->get ( 'column_backup_file' ),
											$this->language->get ( 'column_user' ) );
		$grid_settings ['colModel'] = array (
											array (
													'name' => 'row_id',
													'index' => 'row_id',
													'width' => 10,
													'align' => 'center',
													'sortable' => false,
													'search' => false),
											array (
													'name' => 'date_added',
													'index' => 'date_added',
													'width' => 50,
													'align' => 'center',
													'sortable' => false,
													'search' => false),
											array (
													'name' => 'type',
													'index' => 'type',
													'width' => 50,
													'align' => 'center',
													'sortable' => false,
													'search' => false),
											array (
													'name' => 'name',
													'index' => 'name',
													'width' => 50,
													'align' => 'center',
													'sortable' => false),
											array (
													'name' => 'version',
													'index' => 'version',
													'width' => 20,
													'align' => 'center',
													'sortable' => false,
													'search' => false),
											array (
													'name' => 'backup_date',
													'index' => 'backup_date',
													'width' => 50,
													'align' => 'center',
													'sortable' => false,
													'search' => false),
											array (
													'name' => 'backup_file',
													'index' => 'backup_file',
													'width' => 70,
													'align' => 'center',
													'sortable' => false,
													'search' => false),
											array (
													'name' => 'user',
													'index' => 'user',
													'width' => 40,
													'align' => 'center',
													'sortable' => false,
													'search' => false) );
		
		$grid = $this->dispatch ( 'common/listing_grid', array ( $grid_settings ) );
		$this->view->assign ( 'listing_grid', $grid->dispatchGetOutput () );
		$this->view->assign('help_url', $this->gen_help_url() );

		if (isset($this->session->data['error'])) {
			$this->view->assign('error_warning', $this->session->data['error']);
			unset($this->session->data['error']);
		}
		if (isset($this->session->data['success'])) {
			$this->view->assign('success', $this->session->data['success']);
			unset($this->session->data['success']);
		}

		$this->processTemplate ( 'pages/tool/install_upgrade_history.tpl' );
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

}