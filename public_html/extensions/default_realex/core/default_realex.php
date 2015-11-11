<?php
if ( !defined ( 'DIR_CORE' ) ) {
        header ( 'Location: static_pages/' );
}

class ExtensionDefaultRealex extends Extension {
	
	protected $registry;
	protected $r_data;
	
	public function  __construct() {
		$this->registry = Registry::getInstance();
	}
	
	//Hook to extension edit in the admin 
	public function onControllerPagesExtensionExtensions_UpdateData() {
		$that = $this->baseObject;
		$current_ext_id = $that->request->get['extension'];
	    if ( IS_ADMIN && $current_ext_id == 'default_realex' && $this->baseObject_method == 'edit' ) {
	    	$html = '<a class="btn btn-white tooltips" target="_blank" href="http://www.realexpayments.com/partner-referral?id=abantecart" title="Visit Realex">
	    				<i class="fa fa-external-link fa-lg"></i>
	    			</a>';
	    
	    	$that->view->addHookVar('extension_toolbar_buttons', $html);
		}
	}
	
	//Hook to enable payment details tab in admin 
	public function onControllerPagesSaleOrdertabs_UpdateData() {
		/**
		 * @var $that ControllerPagesSaleOrderTabs
		 */
		$that = $this->baseObject;
		$order_id = $that->data['order_id'];
		//are we logged in and in admin?
	    if ( IS_ADMIN && $that->user->isLogged() ) {
	    	//check if tab is not yet enabled. 
	    	if ( in_array('payment_details', $that->data['groups'])) {
	    		return null;
	    	} 
	    	//check if we this order is used realex payment 
	    	$this->_load_releax_order_data($order_id, $that);
	    	if ( !$this->r_data ) {
	    		return;
	    	}	   		
	    	$that->data['groups'][] = 'payment_details';
	    	$that->data['link_payment_details'] = $that->html->getSecureURL('sale/order/payment_details', '&order_id=' . $order_id.'&extension=default_realex');
			//reload main view data with updated tab
			$that->view->batchAssign( $that->data );
	    	
	    	//other approch to hook to tab variable. In this case more work required to handle new tab
	    	//$that->view->addHookVar('extension_tabs', '[tab HTML]');
	    }
	}
	
	//Hook to payment detilas page to show information  
	public function onControllerPagesSaleOrder_UpdateData() {

		$that = $this->baseObject;
		$order_id = $that->request->get['order_id'];
		//are we logged to admin and correct method called?
	    if ( IS_ADMIN && $that->user->isLogged() && $this->baseObject_method == 'payment_details' ) {
			//build HTML to show
			
			$that->loadLanguage('default_realex/default_realex');
			if ( !$this->r_data ) {
				//no realex data yet. load it. 
				$this->_load_releax_order_data($order_id, $that);
			}

		    if(!$this->r_data){
			    return null;
		    }

			$view = new AView($this->registry, 0);		
			$view->assign('order_id', $order_id);
			$view->assign('void_url', $that->html->getSecureURL('r/extension/default_realex/void'));
			$view->assign('capture_url', $that->html->getSecureURL('r/extension/default_realex/capture'));
			$view->assign('rebate_url', $that->html->getSecureURL('r/extension/default_realex/rebate'));
			$view->assign('realex_order', $this->r_data);
			$view->batchAssign($that->language->getASet('default_realex/default_realex'));
			$this->baseObject->view->addHookVar('extension_payment_details', $view->fetch('pages/sale/payment_details.tpl'));
		}
	}

	/**
	 * @param int $order_id
	 * @param AController $that
	 * @return null
	 */
	private function _load_releax_order_data($order_id, $that) {
		//data already loaded, return 
		if ( $this->r_data ) {
			return null;
		}

		//load realex data 		
		$that->loadModel('extension/default_realex');
		$this->r_data = $that->model_extension_default_realex->getRealexOrder($order_id);
		if (!empty($this->r_data)) {
				$this->r_data['total_captured'] = $that->model_extension_default_realex->getTotalCaptured($this->r_data['realex_order_id']);
				$this->r_data['total_formatted'] = $that->currency->format($this->r_data['total'], $this->r_data['currency_code'], 1);				
				$this->r_data['total_captured_formatted'] = $that->currency->format($this->r_data['total_captured'], $this->r_data['currency_code'], 1);				
		}		
	}
	                
}