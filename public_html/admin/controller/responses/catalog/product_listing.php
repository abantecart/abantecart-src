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
class ControllerResponsesCatalogProductListing extends AController {
	public $data = array();

	/*
	 * response method, if response type is html - it send jqgrid, otherwise - json-data for grid
	 * */
	public function Main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->load->library('json');
		$response_type = $this->request->get[ 'response_type' ];

		$form_name = isset($this->request->get[ 'form_name' ]) ? $this->request->get[ 'form_name' ] : 'productFrm';
		$multivalue_hidden_id = isset($this->request->get[ 'multivalue_hidden_id' ]) ? $this->request->get[ 'multivalue_hidden_id' ] : 'popup';
		if ($response_type != 'json') {

			$this->loadLanguage('catalog/product');
			//remember selected rows for response
			if (isset($this->request->post[ 'selected' ])) {
				$this->session->data[ 'listing_selected' ] = AJson::decode(html_entity_decode($this->request->post[ 'selected' ]), true);
			}
			$grid_settings = array(
				'table_id' => 'product_grid',
				'url' => $this->html->getSecureURL('catalog/product_listing',
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

			$grid_settings[ 'colNames' ][ ] = $this->language->get('column_model');
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
					'name' => 'model',
					'index' => 'model',
					'align' => 'center',
					'width' => 60,
					'sortable' => false ),
				array(
					'name' => 'action',
					'index' => 'action',
					'align' => 'center',
					'width' => 30,
					'sortable' => false,
					'search' => false ) );

				$form = new AForm();
				$form->setForm(array(
					'form_name' => 'product_listing_search',
				));

				$grid_search_form = array();
				$grid_search_form['id'] = 'product_listing_search';
				$grid_search_form['form_open'] = $form->getFieldHtml(array(
					'type' => 'form',
					'name' => 'product_grid_search',
					'action' => '',
				));
				$grid_search_form['submit'] = $form->getFieldHtml(array(
					'type' => 'button',
					'name' => 'submit',
					'text' => $this->language->get('button_go'),
					'style' => 'button1',
				));
				$grid_search_form['reset'] = $form->getFieldHtml(array(
					'type' => 'button',
					'name' => 'reset',
					'text' => $this->language->get('button_reset'),
					'style' => 'button2',
				));

				$grid_search_form['fields']['keyword'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'keyword',
					'value' => ''
				));
				$grid_search_form['fields']['match'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'match',
					'options' => array(
						'any'	=> $this->language->get('filter_any_word'),
						'all'   => $this->language->get('filter_all_words'),
						'exact' => $this->language->get('filter_exact_match'),
					),
				));
				$grid_search_form['fields']['pfrom'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'pfrom',
					'value' => ''
				));
				$grid_search_form['fields']['pto'] = $form->getFieldHtml(array(
					'type' => 'input',
					'name' => 'pto',
					'value' => ''
				));

				$this->loadModel('catalog/category');
				$categories = array( '' => $this->language->get('text_select_category') );
				$results = $this->model_catalog_category->getCategories(0);
				foreach( $results as $r ) {
					$categories[ $r['category_id'] ] = $r['name'];
				}

				$grid_search_form['fields']['category'] = $form->getFieldHtml(array(
					'type' => 'selectbox',
					'name' => 'category',
					'options' => $categories,
					'style' => 'medium-field'
				));


			$grid_settings[ 'search_form' ] = true;


			$grid = $this->dispatch('common/listing_grid', array( $grid_settings ));
			$listing_grid = $grid->dispatchGetOutput();
			unset($grid);
			

			// add js-scripts for grid rows selecting (redeclare onSelectRow event for grid)
			$view = new AView($this->registry, 0);
			$view->batchAssign(array(   'id' => $multivalue_hidden_id,
										'form_name' => $form_name . '_' . $multivalue_hidden_id,
										'table_id' => $grid_settings[ 'table_id' ],
				                        'search_form' => $grid_search_form,
			                            'listing_grid' => $listing_grid,
										'filter_product' => $this->language->get('filter_product'),
										'filter_price_max' => $this->language->get('filter_price_max'),
			                   ));
			$this->data[ 'response' ] = $view->fetch('responses/catalog/product_listing.tpl');

		} else {
			// json-response for jqgrid
			$this->loadLanguage('catalog/product');
			$this->loadModel('catalog/product');
			$this->loadModel('tool/image');

			//Prepare filter config
			//Clean up parametres if needed
		    if ( isset($this->request->get['keyword']) && $this->request->get['keyword'] == $this->language->get('filter_product') ) {
				unset($this->request->get['keyword']);
			}
		    if ( isset($this->request->get['pfrom']) && $this->request->get['pfrom'] == 0 ) {
				unset($this->request->get['pfrom']);
			}
		    if ( isset($this->request->get['pto']) && $this->request->get['pto'] == $this->language->get('filter_price_max') ) {
				unset($this->request->get['pto']);
			}
		
			//Prepare filter config
			$filter_params = array('category', 'status', 'keyword', 'match', 'pfrom', 'pto');
 			$grid_filter_params = array( 'name', 'sort_order', 'model' );
		
			$filter_form = new AFilter( array( 'method' => 'get', 'filter_params' => $filter_params ) );    
		    $filter_grid = new AFilter( array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ) );   
		    
			$total = $this->model_catalog_product->getTotalProducts( array_merge( $filter_form->getFilterData(), $filter_grid->getFilterData() ) );
		    $response = new stdClass();
			$response->page = $filter_grid->getParam('page');
			$response->total = $filter_grid->calcTotalPages( $total );
			$response->records = $total;
		
		    $results = $this->model_catalog_product->getProducts( array_merge( $filter_form->getFilterData(), $filter_grid->getFilterData() ) );
			$i = 0;
			$resource = new AResource('image');
			$response->userdata = (object)array('page'=>$page);
			$data_type = 'product_id';

			$list = $this->session->data[ 'listing_selected' ];

			$id_list = array();
			foreach ($list as $id => $row) {
				if($id == $this->session->data['multivalue_excludes']['product_id'] ){ continue; }
				if ($row[ 'status' ]) {
					$id_list[ ] = $id;
				}
			}

			if ($results) {
				foreach ($results as $result) {
					if($result[ $data_type ] == $this->session->data['multivalue_excludes']['product_id'] ){ continue; }

					if (in_array($result[ $data_type ], $id_list)) {
						$response->userdata->selId[ ] = $result[ $data_type ];
					}
					$thumbnail = $resource->getMainThumb('products',
			                                     $result[ $data_type ],
			                                     27,
			                                     27,true);
					$response->rows[ $i ][ 'id' ] = $result[ $data_type ];
					$response->rows[ $i ][ 'cell' ] = array(
						$thumbnail['thumb_html'],
						$result[ 'name' ],
						$result[ 'model' ],
						'<a class="btn_action" href="JavaScript:void(0);"
							onclick="showPopup(\'' . $this->html->getSecureURL('catalog/product/update', '&' . $data_type . '=' . $result[ $data_type ]) . '\')" title="' . $this->language->get('text_view') . '">' .
						'<img height="27" src="' . RDIR_TEMPLATE . 'image/icons/icon_grid_view.png" alt="' . $this->language->get('text_edit') . '" /></a>' );
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

}