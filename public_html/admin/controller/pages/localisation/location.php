<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

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
class ControllerPagesLocalisationLocation extends AController {
	public $data = array();
	private $error = array();
 
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
       		'href'      => $this->html->getSecureURL('localisation/location'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));

		$grid_settings = array(
			'table_id' => 'location_grid',
			'url' => $this->html->getSecureURL('listing_grid/location'),
			'editurl' => $this->html->getSecureURL('listing_grid/location/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/location/update_field'),
			'sortname' => 'name',
			'sortorder' => 'asc',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/location/update', '&location_id=%ID%')
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
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 600,
                'align' => 'center',
			),
		);

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/location/insert') );
		$this->view->assign('help_url', $this->gen_help_url('location_listing') );

		$this->processTemplate('pages/localisation/location_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$location_id = $this->model_localisation_location->addLocation($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/location/locations', '&location_id=' . $location_id ));
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('common_zone', $this->html->getSecureURL('common/zone'));
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validateForm()) {
			$this->model_localisation_location->editLocation($this->request->get['location_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] ));
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function locations() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('error_warning', $this->error['warning']);
		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->setTitle( $this->language->get('heading_title') );

		$location_info = $this->model_localisation_location->getLocation($this->request->get['location_id']);

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/location'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/location/update', '&location_id=' . $this->request->get['location_id'] ),
       		'text'      => $this->language->get('text_edit') .' '. $this->language->get('text_location') . ' - ' . $location_info['name'],
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] ),
       		'text'      => $this->language->get('tab_locations'),
      		'separator' => ' :: '
   		 ));

		$this->data = array();
		$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_location') . ' - ' . $location_info['name'];
		$this->data['error'] = $this->error;
		$this->data['zone_to_locations'] = $this->model_localisation_location->getZoneToLocations($this->request->get['location_id']);
		$this->data['insert_location'] = $this->html->getSecureURL('localisation/location/insert_locations', '&location_id=' . $this->request->get['location_id']);
		$this->data['delete_location'] = $this->html->getSecureURL('localisation/location/delete_locations', '&location_id=' . $this->request->get['location_id']. '&zone_to_location_id=%ID%');
		$this->data['edit_location'] = $this->html->getSecureURL('localisation/location/update_locations', '&location_id=' . $this->request->get['location_id']. '&zone_to_location_id=%ID%');

		$this->data['locations'] = $this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] );
		$this->data['details'] = $this->html->getSecureURL('localisation/location/update', '&location_id=' . $this->request->get['location_id'] );
		$this->data['active'] = 'locations';

		$this->loadModel('localisation/zone');
		$this->loadModel('localisation/country');
		$results = $this->model_localisation_country->getCountries();
		$this->data['countries'] = array();
		foreach ( $results as $c ) {
			$this->data['countries'][ $c['country_id'] ] = $c['name'];
		}

		foreach ($this->data['zone_to_locations'] as $key => $value) {
			$this->data['zone_to_locations'][$key]['country'] = $this->data['countries'][ $value['country_id'] ];
			$zone = $this->model_localisation_zone->getZone( $value['zone_id'] );
			$this->data['zone_to_locations'][$key]['zone'] = $zone['name'];
		}

		$this->view->assign('help_url', $this->gen_help_url('location_listing') );
		$this->view->batchAssign( $this->data );
		$this->processTemplate('pages/localisation/location_data_list.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert_locations() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$zone_to_location_id = $this->model_localisation_location->addLocationZone($this->request->get['location_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id']. '&zone_to_location_id='. $zone_to_location_id ));
		}
		$this->_getLocationsForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function update_locations() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->view->assign('success', $this->session->data['success']);
		if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->document->setTitle( $this->language->get('heading_title') );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
			$this->model_localisation_location->editLocationZone($this->request->get['zone_to_location_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id']. '&zone_to_location_id='. $this->request->get['zone_to_location_id']));
		}
		$this->_getLocationsForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function delete_locations() {

		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->model_localisation_location->deleteLocationZone($this->request->get['zone_to_location_id']);
		$this->session->data['success'] = $this->language->get('text_success');
		$this->redirect($this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] ));

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getLocationsForm() {
		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] );

		$_info = $this->model_localisation_location->getLocation($this->request->get['location_id']);
		$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_location') . ' - ' . $_info['name'];

 		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/location'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/location/update', '&location_id=' . $this->request->get['location_id'] ),
       		'text'      => $this->language->get('text_edit') .' '. $this->language->get('text_location') . ' - ' . $_info['name'],
      		'separator' => ' :: '
   		 ));
		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] ),
       		'text'      => $this->language->get('tab_locations'),
      		'separator' => ' :: '
   		 ));

		if (isset($this->request->get['zone_to_location_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$location_info = $this->model_localisation_location->getLocationZone($this->request->get['zone_to_location_id']);
		}else{ // if new location's zone insert form - get country
			$location_zones = $this->model_localisation_location->getZoneToLocations( $this->request->get['location_id'] );
			if($location_zones){
				end($location_zones);
				$location_zones = current($location_zones);
				$location_info['country_id'] = $location_zones['country_id'];
			}
		}

		$fields = array('country_id', 'zone_id');
		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($location_info)) {
				$this->data[$f] = $location_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		$this->loadModel('localisation/country');
		$results = $this->model_localisation_country->getCountries();
		$this->data['countries'] = array();
		foreach ( $results as $c ) {
			$this->data['countries'][ $c['country_id'] ] = $c['name'];
		}

		$this->loadModel('localisation/zone');
		$results = $this->model_localisation_zone->getZonesByCountryId( $this->data['country_id'] );
		$this->data['zones'] = array();
		foreach ( $results as $c ) {
			$this->data['zones'][ $c['zone_id'] ] = $c['name'];
		}

		$this->data['active'] = 'locations';
		$this->data['locations'] = $this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] );
        $this->data['details'] = $this->html->getSecureURL('localisation/location/update', '&location_id=' . $this->request->get['location_id'] );
		$this->data['common_zone'] = $this->html->getSecureURL('common/zone');

		if (!isset($this->request->get['zone_to_location_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/location/insert_locations', '&location_id=' . $this->request->get['location_id']);
			$this->data['form_title'] = $this->language->get('text_insert') . $this->language->get('text_location_zone');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['action'] = $this->html->getSecureURL('localisation/location/update_locations', '&location_id=' . $this->request->get['location_id']. '&zone_to_location_id='. $this->request->get['zone_to_location_id']);
			$this->data['form_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_location_zone');
			$this->data['update'] = $this->html->getSecureURL('listing_grid/location/update_location_field','&id='.$this->request->get['zone_to_location_id']);
			$form = new AForm('ST');
		}

		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['form_title'],
      		'separator' => ' :: '
   		 ));

		$form->setForm(array(
		    'form_name' => 'cgFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'cgFrm',
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

		$this->data['form']['fields']['country'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'country_id',
			'value' => $this->data['country_id'],
			'options' => $this->data['countries'],
		));

		$this->data['form']['fields']['zone'] = $form->getFieldHtml(array(
			'type' => 'selectbox',
			'name' => 'zone_id',
			'value' => $this->data['zone_id'],
			'options' => $this->data['zones'],
			'style' => 'medium-field'
		));

		$this->view->assign('help_url', $this->gen_help_url('location_edit') );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/localisation/location_form.tpl' );
	}

	private function _getForm() {
		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/location');

        $this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));

		if (isset($this->request->get['location_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$location_info = $this->model_localisation_location->getLocation($this->request->get['location_id']);
		}

		$fields = array('name', 'description');
		foreach ( $fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($location_info)) {
				$this->data[$f] = $location_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}

		$this->data['active'] = 'details';
		if (!isset($this->request->get['location_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/location/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') . $this->language->get('text_location');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$this->data['locations'] = $this->html->getSecureURL('localisation/location/locations', '&location_id=' . $this->request->get['location_id'] );
			$this->data['action'] = $this->html->getSecureURL('localisation/location/update', '&location_id=' . $this->request->get['location_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_location') . ' - ' . $this->data['name'];
			$this->data['update'] = $this->html->getSecureURL('listing_grid/location/update_field','&id='.$this->request->get['location_id']);
			$form = new AForm('HS');
		}
        $this->data['details'] = $this->data['action'];


        $this->document->addBreadcrumb( array (
            'href'      => $this->html->getSecureURL('localisation/location'),
            'text'      => $this->data['heading_title'],
            'separator' => ' :: '
        ));

		$form->setForm(array(
		    'form_name' => 'cgFrm',
			'update' => $this->data['update'],
	    ));

        $this->data['form']['id'] = 'cgFrm';
        $this->data['form']['form_open'] = $form->getFieldHtml(array(
		    'type' => 'form',
		    'name' => 'cgFrm',
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
			'help_url' => $this->gen_help_url('name'),
		));
		$this->data['form']['fields']['description'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'description',
			'value' => $this->data['description'],
			'required' => true,
			'style' => 'large-field',
			'help_url' => $this->gen_help_url('description'),
		));
		$this->view->assign('help_url', $this->gen_help_url('location_edit') );
		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/localisation/location_form.tpl' );
	}
	
	private function _validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/location')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((strlen(utf8_decode($this->request->post['name'])) < 2) || (strlen(utf8_decode($this->request->post['name'])) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((strlen(utf8_decode($this->request->post['description'])) < 2) || (strlen(utf8_decode($this->request->post['description'])) > 255)) {
			$this->error['description'] = $this->language->get('error_description');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}