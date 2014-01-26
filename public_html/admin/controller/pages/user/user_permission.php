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
class ControllerPagesUserUserPermission extends AController {
	public $data = array();
	private $error = array();
	private $fields = array('name', 'permission');
 
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('user/user_group');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('user/user_group');

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
       		'href'      => $this->html->getSecureURL('user/user_permission'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		));

		$grid_settings = array(
			'table_id' => 'group_grid',
			'url' => $this->html->getSecureURL('listing_grid/user_permission'),
			'editurl' => '',
            'update_field' => $this->html->getSecureURL('listing_grid/user_permission/update_field'),
			'sortname' => 'name',
			'columns_search' => false,
            'actions' => array(),
			'multiselect'=>'false'
		);

        $grid_settings['colNames'] = array(
            $this->language->get('column_name'),
            $this->language->get('column_action'),
		);
		$grid_settings['colModel'] = array( array(
													'name' => 'name',
													'index' => 'name',
													'width' => 600,
													'align' => 'left',
													'sortable' => false ),
											array( 'name' => 'action',
												   'index' => 'action',
												   'width' => 100,
												   'align' => 'center',
												   'sortable' => false,
												   'search' => false ),
		);

        $grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign( 'insert', $this->html->getSecureURL('user/user_permission/insert') );
		$this->view->assign('help_url', $this->gen_help_url('permission_listing') );

		$this->processTemplate('pages/user/user_group_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function delete() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

	    $this->loadModel('user/user_group');
		$this->loadModel('user/user');
        $this->loadLanguage('user/user_group');
        if (!$this->user->canModify('user/user_permission')) {
			echo sprintf($this->language->get('error_permission_modify'), 'user/user_permission');
            return;
		}


		$ids = explode(',', $this->request->get['user_group_id']);
		if ( !empty($ids) ){
			foreach( $ids as $id ) {
				if($id==1){ continue;}
				$user_total = $this->model_user_user->getTotalUsersByGroupId($id);
				if ($user_total) {
					echo sprintf($this->language->get('error_user'), $user_total);
					return;
				}
				$this->model_user_user_group->deleteUserGroup($id);
			}
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->redirect( $this->html->getSecureURL('user/user_permission' ) );
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('user/user_group');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('user/user_group');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$id = $this->model_user_user_group->addUserGroup($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_group_added');
			$this->redirect( $this->html->getSecureURL('user/user_permission/update', '&user_group_id=' . $id ) );
		}
		$this->_getForm();

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('user/user_group');
		$this->document->setTitle( $this->language->get('heading_title') );
		$this->loadModel('user/user_group');

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_user_user_group->editUserGroup($this->request->get['user_group_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('user/user_permission/update', '&user_group_id=' . $this->request->get['user_group_id'] ) );
		}
		$this->_getForm();

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {

 		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('error_name', $this->error['name']);

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('user/user_permission'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
			
		$this->data['cancel'] = $this->html->getSecureURL('user/user_permission');

		if (isset($this->request->get['user_group_id']) && $this->request->server['REQUEST_METHOD'] != 'POST') {
			$user_group_info = $this->model_user_user_group->getUserGroup($this->request->get['user_group_id']);
		}


		if (!isset($this->request->get['user_group_id'])) {
			$this->data['action'] = $this->html->getSecureURL('user/user_permission/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .' '. $this->language->get('text_group');
			$this->data['update'] = '';
			$form = new AForm('ST');
			$this->data['form']['submit'] = $form->getFieldHtml(array(	'type' => 'button',
																		'name' => 'submit',
																		'text' => $this->language->get('button_save'),
																		'style' => 'button1' ));

		} else {
			$this->data['action'] = $this->html->getSecureURL('user/user_permission/update', '&user_group_id=' . $this->request->get['user_group_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_group');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/user_permission/update_field','&user_group_id='.$this->request->get['user_group_id']);
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
		    'attr' => 'confirm-exit="true"',
		    'action' => $this->data['action'],
	    ));

		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'name',
			'value' => $user_group_info['name'],
			'required' => true,
			'style' => 'large-field',
		));
		
	    if (isset($this->request->get['user_group_id'])) {
			$grid_settings = array(
				'table_id' => 'permission_grid',
				'url' => $this->html->getSecureURL('listing_grid/user_permission/getpermissions','&user_group_id='.$this->request->get['user_group_id']),
				'editurl' => $this->html->getSecureURL('listing_grid/user_permission/update_field','&user_group_id='.$this->request->get['user_group_id']),
				'update_field' => $this->html->getSecureURL('listing_grid/user_permission/update_field','&user_group_id='.$this->request->get['user_group_id']),
				'sorting' => false,
				'columns_search' => true,
				'actions' => array(),
				'multiaction' => 'false',
				'multiaction_options' => array( 'save'=> $this->language->get('text_save_selected')),
				'button_go' => $this->language->get('button_save'),
				//'multiselect' => 'false',
			);

			$grid_settings['colNames'] = array( '#',
												$this->language->get('column_controller'),
												$this->language->get('column_access'),
												$this->language->get('column_modify') );

			$grid_settings['colModel'] = array(
				array(
					'name' => '#',
					'index' => 'id',
					'width' => 20,
					'align' => 'center',
					'search' => false
				),
				array(
					'name' => 'controller',
					'index' => 'controller',
					'width' => 300,
					'align' => 'left',
					'search' => true
				),
				array(
					'name' => 'access',
					'index' => 'access',
					'width' => 50,
					'align' => 'center',
					'search' => false
				),
				array(
					'name' => 'modify',
					'index' => 'modify',
					'width' => 50,
					'align' => 'center',
					'search' => false
				),
			);

			$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
			$this->view->assign('listing_grid', $grid->dispatchGetOutput());
		}
		$this->view->assign('help_url', $this->gen_help_url('permission_edit') );
		$this->view->batchAssign($this->data);
		$this->processTemplate('/pages/user/user_group_form.tpl');
	}

	private function _validateForm() {
		if (!$this->user->canModify('user/user_permission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((strlen(utf8_decode($this->request->post['name'])) < 2) || (strlen(utf8_decode($this->request->post['name'])) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
?>