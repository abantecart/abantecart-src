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
class ControllerResponsesListingGridReportPurchased extends AController {

    public function main() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('report/purchased');
		$this->loadModel('report/purchased');

	    //Prepare filter config
        $filter_params =  array('date_start', 'date_end');

	    if(!$this->request->get['date_start']){
		    $this->request->get['date_start'] = dateInt2Display(strtotime('-30 day'));
	    }
	    if(!$this->request->get['date_end']){
		    $this->request->get['date_end'] = dateInt2Display(time());
	    }

        $filter_form = new AFilter(array( 'method' => 'get', 'filter_params' => $filter_params ));
        $filter_grid = new AFilter(array( 'method' => 'post' ));
        $data = array_merge($filter_form->getFilterData(), $filter_grid->getFilterData());

		$total = $this->model_report_purchased->getTotalOrderedProducts($data);

	    $response = new stdClass();
		$response->page = $filter_grid->getParam('page');;
		$response->total = $filter_grid->calcTotalPages($total);;
		$response->records = $total;

	    $results = $this->model_report_purchased->getProductPurchasedReport($data );
	    $i = 0;
		foreach ($results as $result) {
            $response->rows[$i]['id'] = $i;
			$response->rows[$i]['cell'] = array(
				$result['name'],
				$result['model'],
				$result['quantity'],
				$this->currency->format($result['total'], $this->config->get('config_currency'))
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}
}