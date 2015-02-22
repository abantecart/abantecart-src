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
if (!defined('DIR_CORE') || !IS_ADMIN) {
	header('Location: static_pages/');
}
class ControllerResponsesCatalogDownloadListing extends AController {
	public $data = array();
	/**
	 * response method, if response type is html - it send jqgrid, otherwise - json-data for grid
	 *
	 */
	public function Main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		$this->load->library('json');

		if ($this->request->get[ 'response_type' ] != 'json') {
			$this->_html_response();
		} else {
			$this->_json_response();
		}
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		$this->response->setOutput($this->data[ 'response' ]);
	}


	private function _html_response(){

		$this->loadLanguage('catalog/download');
		$form_name = isset($this->request->get[ 'form_name' ]) ? $this->request->get[ 'form_name' ] : 'SharedFrm';
		$multivalue_hidden_id = isset($this->request->get[ 'multivalue_hidden_id' ]) ? $this->request->get[ 'multivalue_hidden_id' ] : 'popup';

		//remember selected rows for response
		if (isset($this->request->post[ 'selected' ])) {
			$this->session->data[ 'listing_selected' ] = AJson::decode(html_entity_decode($this->request->post[ 'selected' ]), true);
		}
		$grid_settings = array(
			'table_id' => 'download_grid_'.$multivalue_hidden_id,
			'url' => $this->html->getSecureURL('catalog/download_listing',
			                                   '&response_type=json&product_id='.$this->request->get['product_id']
											   .(isset($this->request->get['shared_only']) ? '&shared_only=1':'')),
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
		$listing_grid = $grid->dispatchGetOutput();
		unset($grid);

		// add js-scripts for grid rows selecting (redeclare onSelectRow event for grid)
		$view = new AView($this->registry, 0);
		$view->batchAssign(array(   'id' => $multivalue_hidden_id,
									'form_name' => $form_name . '_' . $multivalue_hidden_id,
									'table_id' => $grid_settings[ 'table_id' ],
		                            'listing_grid' => $listing_grid,
									'filter_product' => $this->language->get('filter_product'),
									'filter_price_max' => $this->language->get('filter_price_max'),
		                   ));
		$this->data[ 'response' ] = $view->fetch('responses/catalog/download_listing.tpl');
	}


	private function _json_response(){
		// json-response for jqgrid
		$this->loadLanguage('catalog/download');
		$this->loadModel('catalog/download');

		$excludes = $this->session->data['multivalue_excludes'];

		$grid_filter_params = array( 'name' );
		// if need to show all downloads of product
		if($this->request->get['product_id']){
			$additional_filter_string = (sizeof($excludes) ? "AND p2d.download_id NOT IN (".implode(', ',$excludes).")" : '' );
			if(isset($this->request->get['shared_only'])){
				$additional_filter_string .= 'AND shared=1';
			}

			$filter_grid = new AFilter( array( 'method' => 'post',
												'grid_filter_params' => $grid_filter_params ,
												'additional_filter_string' => $additional_filter_string)
			);
			$results = $this->model_catalog_download->getProductDownloadsDetails( $this->request->get['product_id'], $filter_grid->getFilterData() );
			$total = sizeof($results);
		}else{
			//Prepare filter config
			$additional_filter_string = sizeof($excludes) ? " d.download_id NOT IN (".implode(', ',$excludes).")" : '';
			if(isset($this->request->get['shared_only'])){
				$additional_filter_string .= 'AND shared=1';
			}
			$filter_grid = new AFilter( array( 'method' => 'post',
												'grid_filter_params' => $grid_filter_params,
												'additional_filter_string' => $additional_filter_string
			) );

			$total = $this->model_catalog_download->getTotalDownloads( $filter_grid->getFilterData() );
			$results = $this->model_catalog_download->getDownloads( $filter_grid->getFilterData() );
		}

		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages( $total );
		$response->records = $total;

		$i = 0;
		$response->userdata = (object)array('page'=>$page);
		$data_type = 'download_id';

		$rl = new AResource('download');
		$rl_dir = $rl->getTypeDir();

		if ($results) {
			foreach ($results as $result) {
				$resource_id = $rl->getIdFromHexPath(str_replace($rl_dir,'',$result['filename']));
				$resource_info = $rl->getResource($resource_id);
				$thumbnail = $rl->getResourceThumb($resource_id,27, 27);
				if($resource_info['resource_path']){
					$thumbnail = $this->html->buildResourceImage(
																array('url' => $thumbnail,
																	'width' => 27,
																	'height' => 27,
																	'attr' => 'alt="' . $resource_info['title'] . '"') );
				}else{
					$thumbnail = $resource_info['resource_code'];
				}

				$response->rows[ $i ][ 'id' ] = $result[ 'download_id' ];
				$response->rows[ $i ][ 'cell' ] = array(
					$thumbnail,
					$result[ 'name' ],
					'<a class="btn_action" href="JavaScript:void(0);"
						onclick="showPopup(\'' . $this->html->getSecureURL('catalog/download/update', '&' . $data_type . '=' . $result[ $data_type ]) . '\')" title="' . $this->language->get('text_view') . '">' .
					'<img height="27" src="' . RDIR_TEMPLATE . 'image/icons/icon_grid_view.png" alt="' . $this->language->get('text_edit') . '" /></a>' );
				$i++;
			}
		}

		$this->data[ 'response' ] = AJson::encode($response);
	}

}