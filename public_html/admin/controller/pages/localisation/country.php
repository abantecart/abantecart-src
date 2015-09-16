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

class ControllerPagesLocalisationCountry extends AController {
	public $data = array();
	public $error = array();
	private $fields = array('status', 'iso_code_2', 'iso_code_3', 'address_format');
 
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
       		'href'      => $this->html->getSecureURL('localisation/country'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$grid_settings = array(
			'table_id' => 'country_grid',
			'url' => $this->html->getSecureURL('listing_grid/country'),
			'editurl' => $this->html->getSecureURL('listing_grid/country/update'),
            'update_field' => $this->html->getSecureURL('listing_grid/country/update_field'),
			'sortname' => 'name',
			'sortorder' => 'asc',
            'actions' => array(
                'edit' => array(
                    'text' => $this->language->get('text_edit'),
				    'href' => $this->html->getSecureURL('localisation/country/update', '&country_id=%ID%')
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
            $this->language->get('column_iso_code_2'),
            $this->language->get('column_iso_code_3'),
            $this->language->get('column_status'),
		);
		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 250,
                'align' => 'left',
			),
			array(
				'name' => 'iso_code_2',
				'index' => 'iso_code_2',
				'width' => 120,
                'align' => 'center',
			),
			array(
				'name' => 'iso_code_3',
				'index' => 'iso_code_3',
				'width' => 120,
                'align' => 'center',
			),
			array(
				'name' => 'status',
				'index' => 'status',
				'width' => 130,
                'align' => 'center',
				'search' => false,
			)
		);

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign( 'insert', $this->html->getSecureURL('localisation/country/insert') );
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('help_url', $this->gen_help_url('country_listing') );
		$this->processTemplate('pages/localisation/country_list.tpl' );

          //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function insert() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		if ( $this->request->is_POST() && $this->_validateForm()) {
			$country_id = $this->model_localisation_country->addCountry($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('localisation/country/update', '&country_id=' . $country_id ) );
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
		if ( $this->request->is_POST() && $this->_validateForm() ) {
			$this->model_localisation_country->editCountry($this->request->get['country_id'], $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect( $this->html->getSecureURL('localisation/country/update', '&country_id=' . $this->request->get['country_id'] ) );
		}
		$this->_getForm();

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	private function _getForm() {
		$this->data = array();
		$this->data['error'] = $this->error;
		$this->data['cancel'] = $this->html->getSecureURL('localisation/country');

  		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('localisation/country'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));


		if (isset($this->request->get['country_id']) && $this->request->is_GET() ) {
			$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
		}

		foreach ( $this->fields as $f ) {
			if (isset ( $this->request->post [$f] )) {
				$this->data [$f] = $this->request->post [$f];
			} elseif (isset($country_info)) {
				$this->data[$f] = $country_info[$f];
			} else {
				$this->data[$f] = '';
			}
		}
		
		//set multilingual fields
		$this->data['country_name'] = array();
		if ( $country_info['country_name'] ) {
			$this->data['country_name'] = $country_info['country_name'];
		}

		$country_name = '';
		if (!isset($this->request->get['country_id'])) {
			$this->data['action'] = $this->html->getSecureURL('localisation/country/insert');
			$this->data['heading_title'] = $this->language->get('text_insert') .' '. $this->language->get('heading_title');
			$this->data['update'] = '';
			$form = new AForm('ST');
		} else {
			$country_name = $this->data['country_name'][$this->session->data['content_language_id']]['name'];
			$this->data['action'] = $this->html->getSecureURL('localisation/country/update', '&country_id=' . $this->request->get['country_id'] );
			$this->data['heading_title'] = $this->language->get('text_edit') .' '. $this->language->get('text_country') . ' - ' . $country_name;
			$this->data['update'] = $this->html->getSecureURL('listing_grid/country/update_field','&id='.$this->request->get['country_id']);
			$form = new AForm('HS');
		}
		
		$this->document->addBreadcrumb( array (
       		'href'      => $this->data['action'],
       		'text'      => $this->data['heading_title'],
      		'separator' => ' :: ',
			'current'	=> true
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
			'attr' => 'data-confirm-exit="true" class="aform form-horizontal"'
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

		$this->data['form']['fields']['status'] = $form->getFieldHtml(array(
		    'type' => 'checkbox',
		    'name' => 'status',
		    'value' => $this->data['status'],
			'style'  => 'btn_switch',
	    ));

		$this->data['form']['fields']['name'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'country_name['.$this->session->data['content_language_id'].'][name]',
			'value' => $country_name,
			'required' => true,
			'multilingual' => true,
		));
		$this->data['form']['fields']['iso_code_2'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'iso_code_2',
			'value' => $this->data['iso_code_2'],
		));
		$this->data['form']['fields']['iso_code_3'] = $form->getFieldHtml(array(
			'type' => 'input',
			'name' => 'iso_code_3',
			'value' => $this->data['iso_code_3'],
		));
		$this->data['form']['fields']['address_format'] = $form->getFieldHtml(array(
			'type' => 'textarea',
			'name' => 'address_format',
			'value' => $this->data['address_format'],
			'style' => 'large-field',
		));
		$this->view->assign('form_language_switch', $this->html->getContentLanguageSwitcher());
		$this->view->assign('language_id', $this->session->data['content_language_id']);
		$this->view->assign('help_url', $this->gen_help_url('country_edit') );

		$this->view->batchAssign( $this->data );
        $this->processTemplate('pages/localisation/country_form.tpl' );
	}

	private function _validateForm() {
		if (!$this->user->canModify('localisation/country')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

    	foreach ($this->request->post['country_name'] as $language_id => $value) {
      		if ( mb_strlen($value['name']) < 2 || mb_strlen($value['name']) > 128 ) {
        		$this->error['name'] = $this->language->get('error_name');
      		}
    	}

		$this->extensions->hk_ValidateData($this);
    	
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function validateDelete() {
		if (!$this->user->canModify('localisation/country')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$this->loadModel('setting/store');
		$this->loadModel('sale/customer');
		$this->loadModel('localisation/zone');
		$this->loadModel('localisation/location');
		
		foreach ($this->request->post['selected'] as $country_id) {
			if ($this->config->get('config_country_id') == $country_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}
			
			$store_total = $this->model_setting_store->getTotalStoresByCountryId($country_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
			
			$address_total = $this->model_sale_customer->getTotalAddressesByCountryId($country_id);
	
			if ($address_total) {
				$this->error['warning'] = sprintf($this->language->get('error_address'), $address_total);
			}
				
			$zone_total = $this->model_localisation_zone->getTotalZonesByCountryId($country_id);
		
			if ($zone_total) {
				$this->error['warning'] = sprintf($this->language->get('error_zone'), $zone_total);
			}
		
			$zone_to_location_total = $this->model_localisation_location->getTotalZoneToLocationByCountryID($country_id);
		
			if ($zone_to_location_total) {
				$this->error['warning'] = sprintf($this->language->get('error_zone_to_location'), $zone_to_location_total);
			}
		}
		$this->extensions->hk_ValidateData($this);

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}


	}
}
