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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesCatalogManufacturerListing extends AController {

	public $data = array();

	/*
	 * response method, if response type is html - it send jqgrid, otherwise - json response for grid
	 * */
	public function Main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);

		$this->load->library('json');
		$response_type = $this->request->get[ 'response_type' ];

		$form_name = isset($this->request->get[ 'form_name' ]) ? $this->request->get[ 'form_name' ] : 'productFrm';
		$multivalue_hidden_id = isset($this->request->get[ 'multivalue_hidden_id' ]) ? $this->request->get[ 'multivalue_hidden_id' ] : 'popup';

		if ($response_type != 'json') {

			$this->loadLanguage('catalog/manufacturer');
			//remember selected rows for response
			if (isset($this->request->post[ 'selected' ])) {
				$this->session->data[ 'listing_selected' ] = AJson::decode(html_entity_decode($this->request->post[ 'selected' ]), true);
			}
			$grid_settings = array(
				'table_id' => 'manufacturer_grid',
				'url' => $this->html->getSecureURL('catalog/manufacturer_listing',
				                                   '&response_type=json'),
				'editurl' => '',
				'sortname' => 'name',
				'sortorder' => 'asc',
				'actions' => array(),
				'multiselect_noselectbox' => true,
			);

			$grid_settings[ 'colNames' ] = array(
				$this->language->get('column_image'),
				$this->language->get('column_name') );

			$grid_settings[ 'colNames' ][ ] = $this->language->get('column_action');

			$grid_settings[ 'colModel' ] = array(
				array(
					'name' => 'image',
					'index' => 'image',
					'align' => 'center',
					'width' => 50,
					'sortable' => false,
					'search' => false ),
				array(
					'name' => 'name',
					'index' => 'name',
					'align' => 'left',
					'width' => 200 ),
				array(
					'name' => 'action',
					'index' => 'action',
					'align' => 'center',
					'width' => 30,
					'sortable' => false,
					'search' => false ) );

			$grid_settings[ 'search_form' ] = true;

			$grid = $this->dispatch('common/listing_grid', array( $grid_settings ));
			$this->data[ 'response' ] = $grid->dispatchGetOutput();
			unset($grid);
			

			// add js-scripts for grid rows selecting (redeclare onSelectRow event for grid)
			$view = new AView($this->registry, 0);
			$view->batchAssign(array(   'id' => $multivalue_hidden_id,
										'form_name' => $form_name . '_' . $multivalue_hidden_id,
										'table_id' => $grid_settings[ 'table_id' ] ));
			$this->data[ 'response' ] .= $view->fetch('responses/catalog/manufacturer_listing.tpl');

		} else {
			// json-response for jqgrid
			$this->loadLanguage('catalog/manufacturer');
			$this->loadModel('catalog/manufacturer');
			$this->loadModel('tool/image');
			
			//Prepare filter config
			$grid_filter_params = array('name', 'sort_order');
	    	$filter = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
	    	$filter_data = $filter->getFilterData();
	    	
	    	$total = $this->model_catalog_manufacturer->getTotalManufacturers($filter_data);
	    	$response = new stdClass();
			$response->page = $filter->getParam('page');
			$response->total = $filter->calcTotalPages( $total );
			$response->records = $total;
	    	$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);
	    
			$resource = new AResource('image');

			$i = 0;
			$response->userdata = (object)array('page'=>$page,'selId'=>array() );
			$data_type = 'manufacturer_id';

			$list = $this->session->data[ 'listing_selected' ];

			$id_list = array();
			foreach ($list as $id => $row) {
				if ($row[ 'status' ]) {
					$id_list[ ] = $id;
				}
			}

			if ($results) {
				foreach ($results as $result) {
					if (in_array($result[ $data_type ], $id_list)) {

						$response->userdata->selId[ ] = $result[ $data_type ];
					}
					$thumbnail = $resource->getMainThumb('manufacturers',
			                                     $result[ $data_type ],
			                                     36,
			                                     36,true);

					$response->rows[ $i ][ 'id' ] = $result[ $data_type ];
					$response->rows[ $i ][ 'cell' ] = array(
						$thumbnail['thumb_html'],
						$result[ 'name' ],
						'<a class="btn_action" href="JavaScript:void(0);"
							onclick="showPopup(\'' . $this->html->getSecureURL('catalog/manufacturer/update', '&' . $data_type . '=' . $result[ $data_type ]) . '\')" title="' . $this->language->get('text_view') . '">' .
						'<img src="' . RDIR_TEMPLATE . 'image/icons/icon_grid_view.png" alt="' . $this->language->get('text_edit') . '" /></a>' );
					$i++;
				}
			}

			$this->data[ 'response' ] = $response;
		}


		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);

		if ($response_type == 'json') {
			$this->data[ 'response' ] = AJson::encode($this->data[ 'response' ]);
		}

		$this->response->setOutput($this->data[ 'response' ]);

	}


	public function getManufacturers(){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('catalog/manufacturer');

		if (isset($this->request->post[ 'id' ])) { // variant for popup listing
			$manufacturers = $this->request->post[ 'id' ];
		} else {
			$manufacturers = array();
		}
		$manufacturer_data = array();

		foreach ($manufacturers as &$manufacturer_id) {

			$manufacturer_id = (int)$manufacturer_id;
		} unset($manufacturer_id);


		$manufacturers = array_unique($manufacturers);
		if($manufacturers){
			$filter['subsql_filter'] .= 'manufacturer_id IN  ('.implode(',',$manufacturers).') ';
			$manufacturers_data = $this->model_catalog_manufacturer->getManufacturers($filter);
		}

		if ($manufacturers_data) {
			foreach ($manufacturers_data as $manufacturer) {
				$manufacturer_data[ ] = array(
					'id' => $manufacturer['manufacturer_id'],
					'name' => $manufacturer['name'],
					'sort_order' => 0
				);
			}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->addJSONHeader();
		$this->response->setOutput(AJson::encode($manufacturer_data));
	}

}