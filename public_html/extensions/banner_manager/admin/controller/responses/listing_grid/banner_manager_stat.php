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
class ControllerResponsesListingGridBannerManagerStat extends AController {
	private $error = array();

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('banner_manager/banner_manager');
		$this->loadModel('extension/banner_manager');

	    $filter_params = array('name', 'banner_group_name', 'type', 'cnt');
	    $filter_grid = new AFilter( array( 'method' => 'post',
	                                       'grid_filter_params' => $filter_params,
	                                       'additional_filter_string' => '') );

		/*$page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction



	    $data = array(
			'sort'  => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);*/

		$total = $this->model_extension_banner_manager->getBannersStat($filter_grid->getFilterData(),'total_only');
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;


	    $results = $this->model_extension_banner_manager->getBannersStat($filter_grid->getFilterData());

	    $i = 0;
		foreach ($results as $result) {

            $response->rows[$i]['id'] = $i;
			$response->rows[$i]['cell'] = array(
												$result['name'],
												$result['banner_group_name'],
												$result['clicked'],
												$result['viewed'],
												$result['percent'],
												'<a class="btn_action btn_grid grid_action_expand"
												href="'.$this->html->getSecureURL('extension/banner_manager_stat/details','&banner_id='.$result['banner_id']).'"
												title="'. $this->language->get('text_view') . '">'.
												'<img src="'.RDIR_TEMPLATE.'image/icons/icon_grid_view.png" alt="'. $this->language->get('text_view') . '" /></a>'

												);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

}