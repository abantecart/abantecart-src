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
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

final class AAPI {
	/**
	 * @var Registry
	 */
	protected $registry;
	/**
	 * @var array
	 */
	protected $pre_dispatch = array();
	protected $error;
	private $recursion_limit = 0;
	
	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __destruct() {
	}

    public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	/**
	 * @param string $dispatch_rt
	 */
	public function addPreDispatch($dispatch_rt) {
		$this->pre_dispatch[] = new ADispatcher($dispatch_rt, array("instance_id" => "0"));
	}

	/**
	 * @param string $dispatch_rt
	 */
  	public function build($dispatch_rt) {
		$dispatch = '';
 		$this->recursion_limit = 0;

		foreach ($this->pre_dispatch as $pre_dispatch) {
			/**
			 * @var ADispatcher $pre_dispatch
			 */
			$result = $pre_dispatch->dispatch();					
			if ($result) {
				//Something happened. Need to run different page
				$dispatch_rt = $result;
				break;
			}
		}

		//Process disparcher in while if we have new dispatch back
		while ($dispatch_rt){
			//Process main level controller
			//filter in case we have responses set already
			$dispatch_rt = preg_replace('/^(api)\//', '', $dispatch_rt);			
            $dispatch = new ADispatcher('api/'.$dispatch_rt, array("instance_id" => "0"));
			$dispatch_rt = $dispatch->dispatch();

		}	
			
		unset($dispatch); 
  	}
}
