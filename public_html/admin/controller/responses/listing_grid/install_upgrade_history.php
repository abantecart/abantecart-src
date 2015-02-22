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
class ControllerResponsesListingGridInstallUpgradeHistory extends AController {
	private $error = array ();
	
	public function main() {
		//init controller data
		$this->extensions->hk_InitData($this,__FUNCTION__);
		
		$this->loadLanguage( 'tool/install_upgrade_history' );
		if (! $this->user->canAccess('tool/install_upgrade_history' )) {
			$response = new stdClass();
			$response->userdata->error = sprintf( $this->language->get( 'error_permission_access' ), 'tool/install_upgrade_history' );
			$this->load->library('json');
			$this->response->setOutput(AJson::encode($response));
			return null;
		}
		
		$this->loadModel ( 'tool/install_upgrade_history' );
		
		$page = $this->request->post ['page']; // get the requested page
		$limit = $this->request->post ['rows']; // get how many rows we want to have into the grid
		$sidx = $this->request->post ['sidx']; // get index row - i.e. user click to sort
		$sord = $this->request->post ['sord']; // get the direction

		$filter = array();
		//process custom search form
		//$allowedSearchFilter = array ('name', 'date_added', 'type','user' );

		if (isset ( $this->request->post ['filters'] ) && $this->request->post ['filters'] != '') {
				$this->request->post ['filters'] = json_decode(html_entity_decode($this->request->post ['filters']));
				$filter['value'] = $this->request->post ['filters']->rules[0]->data;
		}

		// process jGrid search parameter
		
		$data = array ('sort' => $sidx.":". $sord,
		               'offset' => ($page - 1) * $limit,
		               'limit' => $limit,
		               'filter' => $filter );
		$total = $this->model_tool_install_upgrade_history->getTotalRows ( $filter );
		if ($total > 0) {
			$total_pages = ceil ( $total / $limit );
		} else {
			$total_pages = 0;
		}
		
		$response = new stdClass ();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;
		$response->userdata = new stdClass();

			$results = $this->model_tool_install_upgrade_history->getLog ( $data );
			$i = 0;
			foreach ( $results as $k=>$result ) {
				$k++;
				$response->rows [$i] ['id'] = $k;

				switch($result['type']){
					case 'delete':
						$response->userdata->classes[ $k ] = 'warning';
						break;
					case 'upgrade':
					case 'install':
						$response->userdata->classes[ $k ] = 'success';
						break;
					case 'uninstall':
						$response->userdata->classes[ $k ] = 'attention';
						break;
				}

				if(is_file(DIR_BACKUP.$result ['backup_file'])){
					$link = '<a target="_blank" title="'.$this->language->get ( 'text_download' ).'" href="'.$this->html->getSecureUrl('tool/backup/download','&filename='.urlencode($result ['backup_file']) ).'">'.$result ['backup_file'].'</a>';
				}else{
					$link = $result ['backup_file'];
				}

				$response->rows [$i] ['cell'] = array (	$k,
														$result ['date_added'],
														$result ['type'],
														$result ['name'],
														$result ['version'],
														$result ['backup_date'],
														$link,
														$result ['user']);

				$i ++;
			}
	
		//update controller data
		$this->extensions->hk_UpdateData($this,__FUNCTION__);
		
		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
		
	}

}