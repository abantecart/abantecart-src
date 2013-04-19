<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2013 Belavier Commerce LLC

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
class ControllerResponsesListingGridTransactions extends AController {
	private $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/customer');
		$this->loadModel('sale/customer');
		$this->load->library('json');


		$page = $this->request->post[ 'page' ]; // get the requested page
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction

		$data = array(
			'sort' => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
			'customer_id' => (int)$this->request->get['customer_id']
		);

		if ( has_value($this->request->get[ 'user' ]) )
			$data['filter']['user'] = $this->request->get[ 'user' ];
		if ( has_value($this->request->get['credit']) )
			$data['filter']['credit'] = $this->request->get[ 'credit' ];
		if ( has_value($this->request->get['debit']) )
			$data['filter']['debit'] = $this->request->get[ 'debit' ];
		if ( has_value($this->request->get['type']) )
			$data['filter']['type'] = $this->request->get[ 'type' ];
		if ( has_value($this->request->get['date_start']) )
			$data['filter']['date_start'] = dateDisplay2ISO($this->request->get[ 'date_start' ]);
		if ( has_value($this->request->get['date_end']) )
			$data['filter']['date_end'] = dateDisplay2ISO($this->request->get[ 'date_end' ]);

		$allowedFields = array( 'user', 'credit', 'debit', 'type', 'date_start', 'date_end' );
		if ( isset($this->request->post[ '_search' ]) && $this->request->post[ '_search' ] == 'true') {
			$searchData = AJson::decode(htmlspecialchars_decode($this->request->post[ 'filters' ]), true);

			foreach ($searchData[ 'rules' ] as $rule) {
				if (!in_array($rule[ 'field' ], $allowedFields)) continue;
				$data['filter'][ $rule[ 'field' ] ] = $rule[ 'data' ];
			}
		}

		$total = $this->model_sale_customer->getTotalTransactions($data);

		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = $this->model_sale_customer->getTransactions($data);
		$i = 0;
		foreach ($results as $result) {
			$response->rows[ $i ][ 'id' ] = $result[ 'transaction_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$result[ 'create_date' ],
				$result[ 'user' ],
				$result[ 'credit' ],
				$result[ 'debit' ],
				$result[ 'type' ],
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);


		$this->response->setOutput(AJson::encode($response));
	}


	private function _validateForm($field, $value, $customer_id = '') {

		$err = false;
		switch ($field) {
			case 'loginname' :
				$login_name_pattern = '/^[\w._-]+$/i';
				$value = preg_replace('/\s+/', '', $value);
   		 		if ( strlen(utf8_decode($value)) < 5 || strlen(utf8_decode($value)) > 64
   		 			|| (!preg_match($login_name_pattern, $value) && $this->config->get('prevent_email_as_login')) ) {	
   		   			$err = $this->language->get('error_loginname');
   				//check uniqunes of loginname
   		 		} else if ( !$this->model_sale_customer->is_unique_loginname($value, $customer_id) ) {
   		   			$err = $this->language->get('error_loginname_notunique');
   		 		}			
				break;
			case 'firstname' :
				if ((strlen(utf8_decode($value)) < 1) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_firstname');
				}
				break;
			case 'lastname':
				if ((strlen(utf8_decode($value)) < 1) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_lastname');
				}
				break;
			case 'email':
				$pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';
				if ((strlen(utf8_decode($value)) > 96) || (!preg_match($pattern, $value))) {
					$err = $this->language->get('error_email');
				}
				break;
			case 'telephone':
				if ((strlen(utf8_decode($value)) < 3) || (strlen(utf8_decode($value)) > 32)) {
					$err = $this->language->get('error_telephone');
				}
				break;
		}

		return $err;
	}

	public function getTransactionInfo($transaction_id){

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		if (!$this->user->canAccess('listing_grid/transactions')) {
					$error = new AError('');
					return $error->toJSONResponse('NO_PERMISSIONS_402',
						array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/transactions'),
							'reset_value' => true
						));
				}

		$this->loadModel('sale/customer');
		$info = $this->model_sale_customer->getTransaction($this->request->get['transaction_id']);

		$form = new AForm();
		$form->setForm(array(
			'form_name' => 'transaction_form',
		));
		$response['form_open'] = $form->getFieldHtml(array(
				    'type' => 'form',
				    'name' => 'transaction_form',
				    'action' => '',
			    ));


		$response['fields'][] = array('text' => $this->language->get('column_create_date'),
									'field' => $form->getFieldHtml(array(
																		'type' => 'input',
																		'name' => 'create_date',
																		'value' => dateISO2Display($info['create_date'])
																	)));
		$response['fields'][] = array('text' => $this->language->get('column_created_by'),
									'field' => $form->getFieldHtml(array(
																		'type' => 'input',
																		'name' => 'created_by',
																		'value' => $info['user']
																	)));
		$selected = $info['credit'] ? 'credit' : 'debit';
		$response['fields'][] = array('text' => $this->language->get('column_created_by'),
									'field' => $form->getFieldHtml(array(
																		'type' => 'selectbox',
																		'name' => 'balance',
																		'options' => array('credit'=>$this->language->get('text_option_credit'),
																							'debit'=>$this->language->get('text_option_debit')),
																		'value' => $selected
																	)));

		$response['fields'][] = array('text' => $this->language->get('column_created_by'),
									'field' => $form->getFieldHtml(array(
																		'type' => 'selectbox',
																		'name' => 'balance',
																		'options' => array('credit'=>$this->language->get('text_option_credit'),
																							'debit'=>$this->language->get('text_option_debit')),
																		'value' => $selected
																	)));





		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

}