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
class ControllerResponsesListingGridGlobalSearchResult extends AController {
	/**
	 * @var array
	 */
	private $error = array ();
	
	public function main() {
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
        $this->loadModel ( 'tool/global_search' );
        
        $page = $this->request->post['page']; // get the requested page
		$limit = $this->request->post['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post['sord']; // get the direction
        
        $search_str = '';
	    $allowedFields = array('search_result');
	    $allowedSortFields = array();

        $allowedDirection = array('asc', 'desc');
        

	    if ( !in_array($sidx, $allowedSortFields) ) $sidx = $allowedSortFields[0];
	    if ( !in_array($sord, $allowedDirection) ) $sord = $allowedDirection[0];

	    $data = array(
			'sort'  => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		    'search' => $search_str,
		);
        
		
		$results = $this->model_tool_global_search->getResult($this->request->get['search_category'],$this->request->get['keyword']);
		// preverse repeat request to db for total
		if(!isset($this->session->data['search_totals'][$this->request->get['search_category']])){
			$total = $this->model_tool_global_search->getTotal($this->request->get['search_category'],$this->request->get['keyword']);
		} else {
			$total =  $this->session->data['search_totals'][$this->request->get['search_category']];
			unset($this->session->data['search_totals'][$this->request->get['search_category']]);
		}	
		
	    if( $total > 0 ) {
			$total_pages = ceil($total/$limit);
		} else {
			$total_pages = 0;
		}

	    $response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		
	//	$response->search_str = $search_str;
          
		
		
		$i = 0;
		foreach ($results['result'] as $result) {

			$response->rows[$i]['id'] = $i+1;
			$response->rows[$i]['cell'] = array($i+1,
												$result['text']
												);
			$i++;
		}
		
		
		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
		
	}
	
	/**
	 * function check access rights to search results
	 * @param string $permissions
	 * @return boolean
	 */
	private function validate($permissions = null) {
		// check access to global search
		if (! $this->user->hasPermission ( 'access', 'tool/global_search' )) {
			$this->error ['warning'] = $this->language->get ( 'error_permission' );
		}
		return ! $this->error ? true : false;
	}

}
?>