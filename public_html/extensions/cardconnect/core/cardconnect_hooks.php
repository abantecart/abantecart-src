<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

class ExtensionCardconnect extends Extension {

	protected $r_data;
	protected function _is_enabled(){
		return $this->baseObject->config->get('cardconnect_status');
	}

	//Hook to extension edit in the admin
	public function onControllerPagesExtensionExtensions_UpdateData() {
		if (!$this->_is_enabled()){
			return null;
		}
		$that = $this->baseObject;
		$current_ext_id = $that->request->get['extension'];
		if ( IS_ADMIN && $current_ext_id == 'cardconnect' && $this->baseObject_method == 'edit' ) {
			$html = '<a class="btn btn-white tooltips" target="_blank" href="https://www.cardconnect.com" title="Visit cardconnect">
						<i class="fa fa-external-link fa-lg"></i>
					</a>';
			$that->view->addHookVar('extension_toolbar_buttons', $html);
		}
	}

	//Hook to enable payment details tab in admin
	public function onControllerPagesSaleOrderTabs_UpdateData() {
		if (!$this->_is_enabled()){
			return null;
		}
		$that = $this->baseObject;
		$order_id = $that->data['order_id'];
		//are we logged in and in admin?
		if ( IS_ADMIN && $that->user->isLogged() ) {
			//check if tab is not yet enabled.
			if ( in_array('payment_details', $that->data['groups'])) {
				return null;
			}
			//check if we this order is used cardconnect payment
			$that->loadModel('extension/cardconnect');
			$this->_load_cardconnect_order_data($order_id, $that);
			if ( !$this->r_data ) {
				return;
			}
			$that->data['groups'][] = 'payment_details';
			$that->data['link_payment_details'] = $that->html->getSecureURL('sale/order/payment_details', '&order_id=' . $order_id.'&extension=cardconnect');
			//reload main view data with updated tab
			$that->view->batchAssign( $that->data );
		}
	}

	//Hook to payment details page to show information
	public function onControllerPagesSaleOrder_UpdateData() {
		if (!$this->_is_enabled()){
			return null;
		}
		$that = $this->baseObject;
		if ( IS_ADMIN !== true
			|| !$that->user->isLogged()
			|| $this->baseObject_method != 'payment_details'
		) {
			return null;
		}

		$order_id = $that->request->get['order_id'];
		//are we logged to admin and correct method called?
		//build HTML to show

		$that->loadLanguage('cardconnect/cardconnect');
		$that->loadModel('extension/cardconnect');
		if ( !$this->r_data ) {
			//no local cardconnect order data yet. load it.
			$this->_load_cardconnect_order_data($order_id, $that);
		}

		if(!$this->r_data){
			return null;
		}
		$registry = Registry::getInstance();
		$view = new AView($registry, 0);
		//get remote charge data
		$ch_data = $that->model_extension_cardconnect->getCardconnectCharge($this->r_data['retref']);

		if (!$ch_data) {
			$view->assign('error_warning', "Some error happened!. Check the error log for more details.");
		} else {
			$ch_data['settlement_status'] = $ch_data['setlstat'];
			$ch_data['amount'] = number_format($this->r_data['total'], 2);
			$ch_data['amount_refunded'] = number_format($ch_data['refunded'], 2);
			$ch_data['refunded_formatted'] = $that->currency->format($ch_data['refunded'], strtoupper($ch_data['currency_code']), 1);

			$ch_data['amount_formatted'] = $that->currency->format($ch_data['amount'], strtoupper($ch_data['currency_code']), 1);
			$ch_data['captured_formatted'] = $that->currency->format($ch_data['captured'], strtoupper($ch_data['currency_code']), 1);

			//check a void status.
			//Not captured and refunded
			if ($ch_data['refunded'] && !$ch_data['captured']) {
				$ch_data['void_status'] = 1;
			}
			if($ch_data['refunds']->total_count > 0) {
				//get all refund transactions
				foreach ($ch_data['refunds']->data as $refund) {
					$amount = number_format($refund['amount'], 2);
					$refunds[] = array(
						'id' => $refund['id'],
						'amount' => $amount,
						'amount_formatted' => $that->currency->format($amount, strtoupper($refund['currency']), 1),
						'currency' => $refund['currency'],
						'reason' => $refund['reason'],
						'date_added' => (string)date('m/d/Y H:i:s', $refund['created']),
						'receipt_number' => $refund['receipt_number'],
					);
				}
			}
			$ch_data['balance'] = $ch_data['amount'] + $ch_data['refunded'];
			$ch_data['balance_formatted'] = $that->currency->format($ch_data['balance'], strtoupper($ch_data['currency']), 1);
		}

		$view->assign('order_id', $order_id);
		$view->assign('test_mode', $this->r_data['cardconnect_test_mode']);
		$port = $that->config->get('cardconnect_test_mode') ? 6443 : 8443;
		$view->assign('external_url', 'https://'.$that->config->get('cardconnect_site').'.cardconnect.com:'.$port.'/ui/findauth.jsf');
		$view->assign('void_url', $that->html->getSecureURL('r/extension/cardconnect/void'));
		$view->assign('capture_url', $that->html->getSecureURL('r/extension/cardconnect/capture'));
		$view->assign('refund_url', $that->html->getSecureURL('r/extension/cardconnect/refund'));
		$view->assign('cardconnect_order', $ch_data);
		$view->assign('refund', $refunds);
		$view->batchAssign($that->language->getASet('cardconnect/cardconnect'));
		$that->document->addStyle(
							array(
								'href' => $that->view->templateResource('/stylesheet/cardconnect.css'),
								'rel' => 'stylesheet',
								'media' => 'screen'
							)
					);
		$that->view->addHookVar('extension_payment_details', $view->fetch('pages/sale/cardconnect_payment_details.tpl'));
	}

	private function _load_cardconnect_order_data($order_id, $that) {
		//data already loaded, return
		if ( $this->r_data ) {
			return null;
		}
		//load local cardconnect data
		$this->r_data = $that->model_extension_cardconnect->getcardconnectOrder($order_id);
	}

	/*
	 *
	 * custom tpl for product edit page
	 *
	 *
	 * */


	public function onControllerPagesCatalogProduct_InitData(){
		if (!$this->_is_enabled()){
			return null;
		}
		$that = $this->baseObject;
		if(!$this->_is_enabled($that)){ return null; }
		if(IS_ADMIN !== true){
			return;
		}

		$product_id = (int)$that->request->get['product_id'];
		$cardconnect_plan = $that->request->get['cardconnect_plan'];
		$that->load->language('cardconnect/cardconnect');
		$that->load->model('extension/cardconnect');
		if($product_id && has_value($cardconnect_plan) ){
			if($cardconnect_plan) {
				//Set up product for subscription
				//update product price with plan price
				//update cardconnect metadata for description
				$ret = $that->model_extension_cardconnect->setProductAsSubscription($product_id, $cardconnect_plan);
				if( array($ret) && $ret['error'] ) {
					$that->session->data['warning'] = implode("\n",$ret['error']);
					header('Location: '. $that->html->getSecureURL('catalog/product/update','&product_id='.$product_id));
					exit;
				}
			} else {
				//reset to no plan
				$ret = $that->model_extension_cardconnect->removeProductAsSubscription($product_id);
				if( array($ret) && $ret['error'] ) {
					$that->session->data['warning'] = implode("\n",$ret['error']);
					header('Location: '. $that->html->getSecureURL('catalog/product/update','&product_id='.$product_id));
					exit;
				}
			}
		}
	}

/*
	public function onControllerPagesCatalogProduct_UpdateData(){
		if (!$this->_is_enabled()){
			return null;
		}
		$that = $this->baseObject;
		if(IS_ADMIN !== true){
			return;
		}

		if(!in_array($this->baseObject_method, array('update'))){
			return;
		}

		$product_id = (int)$that->request->get['product_id'];
		$product_info = $that->view->getData('product_info');

		//add switcher to donation product
		$data = $that->view->getData('form');
return;
		$that->load->language('cardconnect/cardconnect');
		$that->load->model('extension/cardconnect');
		$cardconnect_plan = $that->model_extension_cardconnect->getProductSubscription($product_id);
		$cardconnect_plan_alive = false;

		//get available plans and check if plan is selected
		$that->loadModel('extension/cardconnect');
		$all_cardconnect_plans = $that->model_extension_cardconnect->getcardconnectPlans();

		$plans_list = array();
		$plans_list[0] = 'Select cardconnect plan for this product! ????';
		foreach($all_cardconnect_plans->data as $plan) {
			$plans_list[$plan['id']] = $plan['name'];
			//check if existing plan is still alive in cardconnect.
			if($plan['id'] == $cardconnect_plan){
				$cardconnect_plan_alive = true;
			}
		}
		unset($all_cardconnect_plans);
		if(!$cardconnect_plan_alive) {
			$cardconnect_plan = '';
		}
		if($that->config->get('cardconnect_test_mode')) {
			$external_url = 'https://dashboard.cardconnect.com/test/plans/';
		} else {
			$external_url = 'https://dashboard.cardconnect.com/plans/';
		}
		if($cardconnect_plan) {
			$external_url = $external_url.$cardconnect_plan;
		}
		$append_html = '<span class="input-group-addon"><span class="help_element"><a href="'.$external_url.'" target="new"><i class="fa fa-cc-cardconnect fa-lg"></i></a></span></span>';

		$href = $that->html->getSecureURL('catalog/product/' . $this->baseObject_method, ($product_id ? '&product_id=' . $product_id : ''));
		$js = '<script type="application/javascript">
			$("#cardconnect_plan").on("change", function(){
				var that = $(this);
				var old_value = that.find("option[data-orgvalue=true]").attr("value");
				if(old_value == $(this).val()){
					return false; 
				} else {
					that.attr("disabled", "disabled");   
				}

				var action = \'' . $href . '\' + \'&cardconnect_plan=\' + $(this).val();
				location = action;
			});
		</script>';

		$cardconnect_plan = $that->html->buildElement(
					array(
						'type' => 'selectbox',
						'name' => 'cardconnect_plan',
						'options' => $plans_list,
						'value' => $cardconnect_plan,
						'style' => 'medium-field',
					)
		).$append_html.$js;

		//push switch after status
		$data['fields']['general'] = array_slice($data['fields']['general'], 0, 1, true) +
				array (
						"cardconnect_plan" => $cardconnect_plan
				) +
				array_slice($data['fields']['general'], 1, count($data['fields']['general']) - 1, true);

		$that->view->assign('form', $data);

	}
*/
	public function onControllerPagesCheckoutConfirm_InitData(){
		if (!$this->_is_enabled()){
			return null;
		}
		$that = $this->baseObject;
		if( $that->session->data['payment_method']['id'] == 'cardconnect' ){
			$that->document->addStyle(
					array(
						'href' => $that->view->templateResource('/stylesheet/cardconnect.css'),
						'rel' => 'stylesheet',
						'media' => 'screen'
					)
			);
		}
	}
}