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
class ControllerPagesToolGlobalSearch extends AController {

	private $error = array ();
	public $data;

	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('tool/global_search');

		$this->document->setTitle ( $this->language->get ( 'heading_title' ) );

		$this->request->post ['search'] = $this->request->post ['search'] ? $this->request->post ['search'] : $this->request->get ['search'];

		$this->data ['heading_title'] = $this->language->get ( 'heading_title').':&nbsp;&nbsp;&nbsp;&nbsp;'. htmlentities($this->request->post ['search'],ENT_QUOTES,'UTF-8');
		
		$this->data ['text_select_all'] = $this->language->get ( 'text_select_all' );
		$this->data ['text_unselect_all'] = $this->language->get ( 'text_unselect_all' );
		$this->data ['button_backup'] = $this->language->get ( 'button_backup' );
		$this->data ['button_restore'] = $this->language->get ( 'button_restore' );
		
		$this->data ['tab_general'] = $this->language->get ( 'tab_general' );
		
		if (isset ( $this->error ['warning'] )) {
			$this->data ['error_warning'] = $this->error ['warning'];
		} else {
			$this->data ['error_warning'] = '';
		}
		
		if (isset ( $this->session->data ['success'] )) {
			$this->data ['success'] = $this->session->data ['success'];
			
			unset ( $this->session->data ['success'] );
		} else {
			$this->data ['success'] = '';
		}
		
		$this->document->resetBreadcrumbs ();
		$this->document->addBreadcrumb ( array (
												'href' => $this->html->getSecureURL ( 'index/home' ), 
												'text' => $this->language->get ( 'text_home' ), 
												'separator' => FALSE ) );
		$this->document->addBreadcrumb ( array (
												'href' => $this->html->getSecureURL ( 'tool/global_search', '&search='. $this->request->post ['search'] ),
												'text' => $this->language->get ( 'heading_title' ), 
												'separator' => ' :: ' ) );
		
		$this->view->assign ( 'search_url', $this->html->getSecureURL ( 'listing_grid/global_search_result' ) );
		$this->view->assign ( 'search_keyword', $this->request->post ['search'] );

		$form = new AForm ();
		$search_form = $form->getFieldHtml ( array (
			'type' => 'form',
			'name' => 'search_form',
			'action' => $this->html->getSecureURL ( 'tool/global_search' ) ) );
		$this->view->assign ( 'search_form', $search_form );
		$search_form_input = $this->request->post ['search'];

		$search_form_button = $form->getFieldHtml ( array (
			'type' => 'button',
			'name' => 'submit',
			'style' => 'button1',
			'text' => $this->language->get ( 'button_go' )
		) );
		$this->view->assign ( 'search_form_input', $search_form_input );
		$this->view->assign ( 'search_form_button',  $search_form_button);

		$this->data['grid_inits'] = array(); // list of js-functions names for initialization all jqgrids
		if ($this->_validate ()) {
			$search_categories_icons = $this->model_tool_global_search->getSearchSources ( $this->request->post ['search'] );
			$search_categories  = array_keys($search_categories_icons);
			if ($search_categories) {
				


				$this->view->assign ( 'no_results_message', '' );

				$i = 0;
				foreach ( $search_categories as $search_category ) {
					
					$total = $this->model_tool_global_search->getTotal ( $search_category, $this->request->post ['search'] );
					if ($total < 1) {
						continue;
					} else {
						$this->session->data ['search_totals'] [$search_category] = $total;
						$search_categories_names[$search_category] = $this->language->get ( "text_".$search_category );
					}
					

					// we need to set grids fro each search category as child
					$grid_settings = array (
											'table_id' => $search_category . '_grid',
											'url' => $this->html->getSecureURL ( 'listing_grid/global_search_result', "&search_category=" . $search_category . "&keyword=" . $this->request->post ['search'] ),
											'editurl' => null,
											'columns_search' => false,
											'sortable' => false,
											'hidden_head' => true);
					
					$grid_settings ['colNames'] = array (	'#',
															$this->language->get ( "text_".$search_category ) );
					$grid_settings ['colModel'] = array (
															array (
																				'name' => 'num', 
																				'index' => 'num',
																				'width' => 40,
																				'align' => 'center',
																				'classes' => 'search_num',
																				'sortable' => false ), 
															array (
																				'name' => 'search_result', 
																				'index' => 'search_result',
																				'width' => 830,
																				'align' => 'left',
																				'sortable' => false ) );
					$grid_settings ['multiselect'] = "false";
					$grid_settings ['hoverrows'] = "false";
					$grid_settings ['altRows'] = "true";
					$grid_settings ['ajaxsync'] = "false";
					$grid_settings ['history_mode'] = false;
					// need to disable initializations all grid on page load because it high load cpu in damn IE8-9
					$grid_settings ['init_onload'] = false;
					$this->data['grid_inits'][] = 'initGrid_'.$grid_settings ['table_id'];

					$grid = $this->dispatch ( 'common/listing_grid', array ($grid_settings ) );
					$this->view->assign ( 'listing_grid_' . $search_category, $grid->dispatchGetOutput () );
					$i++;
				}
				$this->view->assign ( 'search_categories', array_keys((array)$this->session->data ['search_totals']) );
				$this->view->assign ( 'search_categories_icons', $search_categories_icons );
				$this->view->assign ( 'search_categories_names', $search_categories_names );

				if (! $i) {
					$this->view->assign ( 'no_results_message', $this->language->get ( 'no_results_message' ) );
					$this->view->assign ( 'search_categories', array () );
				
				}
			} else {
				
				$this->view->assign ( 'no_results_message', $this->language->get ( 'no_results_message' ) );
				$this->view->assign ( 'search_categories', array () );
			}
		
		}
		
		$this->view->batchAssign ( $this->data );
		$this->processTemplate ( 'pages/tool/global_search.tpl' );
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	/**
	 * function check access rights to search results
	 * @param string $permissions
	 * @return boolean
	 */
	private function _validate($permissions = null) {
		// check access to global search
		if (! $this->user->canAccess( 'tool/global_search' )) {
			$this->error ['warning'] = $this->language->get ( 'error_permission' );
		}
		return ! $this->error ? true : false;
	}
}
?>
