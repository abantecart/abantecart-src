<?php
if ( !defined ( 'DIR_CORE' ) ) {
        header ( 'Location: static_pages/' );
}

class ExtensionDefaultPayza extends Extension {
	
	protected $registry;
	protected $r_data;
	
	public function  __construct() {
		$this->registry = Registry::getInstance();
	}
	
	//Hook to extension edit in the admin 
	public function onControllerPagesExtensionExtensions_UpdateData() {
		$that = $this->baseObject;
		
		$current_ext_id = $that->request->get['extension'];
	    if ( IS_ADMIN && $current_ext_id == 'default_payza' && $this->baseObject_method == 'edit' ) {
	    	$html = '<a class="btn btn-white tooltips" target="_blank" href="https://secure.payza.com/?8dYDuHG0dFxqAh3PEXvZiu%2fblIIczM5Th75CF6Csm0Q%3d" title="Visit Payza">
	    				<i class="fa fa-external-link fa-lg"></i>
	    			</a>';
	    
	    	$that->view->addHookVar('extension_toolbar_buttons', $html);
		}
	}
       
}