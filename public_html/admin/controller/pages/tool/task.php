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

class ControllerPagesToolTask extends AController {
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
												'href' => $this->html->getSecureURL ( 'tool/task' ),
												'text' => $this->language->get ( 'heading_title' ), 
												'separator' => ' :: ',
												'current' => true) );
		
		$grid_settings = array (
								//id of grid
								'table_id' => 'tasks_grid',
								// url to load data from
								'url' => $this->html->getSecureURL ( 'listing_grid/task' ),
								// url to send data for edit / delete
								'editurl' => $this->html->getSecureURL('tool/task'),
								'multiselect'=>'false',
								// url to update one field
								'update_field' => '',
								// default sort column
								'sortname' => 'date_modified',
								// actions
								'actions' => array(
										'run' => array(
											'text' => $this->language->get('text_run'),
											'href' => $this->html->getSecureURL('listing_grid/task/run', '&task_id=%ID%')
										),
										'restart' => array(
											'text' => $this->language->get('text_restart'),
											'href' => $this->html->getSecureURL('listing_grid/task/restart', '&task_id=%ID%')
										),
										'delete' => array(
											'text' => $this->language->get('button_delete'),
											'href' => $this->html->getSecureURL('tool/task/delete', '&task_id=%ID%')
										)),
								'columns_search' => true,
								'sortable' => true,
								'grid_ready' => 'grid_ready();');
		
		$grid_settings ['colNames'] = array (
											$this->language->get ( 'column_id' ),
											$this->language->get ( 'column_name' ),
											$this->language->get ( 'column_status' ),
											$this->language->get ( 'column_start_time' ),
											$this->language->get ( 'column_date_created' ) );
		$grid_settings ['colModel'] = array (
											array (
													'name' => 'task_id',
													'index' => 'task_id',
													'width' => 40,
													'align' => 'center',
													'sortable' => true,
													'search' => false),
											array (
													'name' => 'name',
													'index' => 'name',
													'width' => 150,
													'align' => 'left',
													'sortable' => true,
													'search' => true),
											array (
													'name' => 'status',
													'index' => 'status',
													'width' => 150,
													'align' => 'center',
													'sortable' => true,
													'search' => false),
											array (
													'name' => 'start_time',
													'index' => 'start_time',
													'width' => 150,
													'align' => 'center',
													'sortable' => true,
													'search' => false),
											array (
													'name' => 'date_modified',
													'index' => 'date_modified',
													'width' => 150,
													'align' => 'center',
													'sortable' => true,
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

		$this->view->assign('run_task_url', $this->html->getSecureURL('listing_grid/task/run'));
		$this->view->assign('restart_task_url', $this->html->getSecureURL('listing_grid/task/restart'));

		$this->view->batchAssign (  $this->language->getASet () );
		$this->processTemplate ( 'pages/tool/task.tpl' );
		
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function delete(){
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$id = (int)$this->request->get_or_post('task_id');
		if($id){
			$tm = new ATaskManager();
			$tm->deleteTask($id);
		}

		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->redirect($this->html->getSecureURL('tool/task'));
	}

}