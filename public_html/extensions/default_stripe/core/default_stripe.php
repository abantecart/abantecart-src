<?php
if ( !defined ( 'DIR_CORE' ) ) {
        header ( 'Location: static_pages/' );
}

class ExtensionStripe extends Extension {
	
	protected $registry;
	protected $r_data;
	
	public function  __construct() {
		$this->registry = Registry::getInstance();
	}
	
	//Hook to extension edit in the admin 
	public function onControllerPagesExtensionExtensions_UpdateData() {
		$that = $this->baseObject;
		$current_ext_id = $that->request->get['extension'];
	    if ( IS_ADMIN && $current_ext_id == 'default_stripe' && $this->baseObject_method == 'edit' ) {
	    	$html = '<a class="btn btn-white tooltips" target="_blank" href="https://www.stripe.com" title="Visit stripe">
	    				<i class="fa fa-external-link fa-lg"></i>
	    			</a>';
	    
	    	$that->view->addHookVar('extension_toolbar_buttons', $html);
		}
	}
	                
}