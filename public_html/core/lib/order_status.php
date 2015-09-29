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
/**
 * Class AOrderStatus
 */
class AOrderStatus {
	/**
	 * @var Registry
	 */
	protected $registry;
	/**
	 * @var array
	 */
	private $base_statuses = array (
			0  => 'incomplete',
			1  => 'pending',
			2  => 'processing',
			3  => 'shipped',
			7  => 'canceled',
			5  => 'completed',
			8  => 'denied',
			9  => 'canceled_reversal',
			10 => 'failed',
			11 => 'refunded',
			12 => 'reversed',
			13 => 'chargeback',
			14 => 'canceled_by_customer');
	/**
	 * @var array
	 */
	protected $statuses = array();

  	public function __construct() {
	    $this->statuses = $this->base_statuses;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function addStatus($order_status_id, $status_text_id){
		$order_status_id = (int)$order_status_id;
		//preformat text_id at first
		$status_text_id = preformatTextID($status_text_id);

		if(in_array($order_status_id, array_keys($this->statuses)) || in_array($status_text_id, $this->statuses)){
			$error_text = 'Error: Cannot add new order status with id '.$order_status_id.' and text id '. $status_text_id.' into AOrderStatus class.';
			$e =  new AError($error_text);
			$e->toLog()->toDebug();
			return false;
		}

		if(!$status_text_id){
			$error_text = 'Error: Cannot add new order status with id '.$order_status_id.' and empty text id' ;
			$e =  new AError($error_text);
			$e->toLog()->toDebug();
			return false;
		}

		$this->statuses[$order_status_id] = $status_text_id;
		return true;
	}

	
	public function getStatusByTextId( $order_status_text_id ) {
		$flipped = array_flip($this->statuses);
		return $flipped[$order_status_text_id];
	}

	public function getStatusById( $order_status_id ) {
		return $this->statuses[$order_status_id];
	}

	public function getStatuses(){
		return $this->statuses;
	}

	public function getBaseStatuses(){
		return $this->base_statuses;
	}
   	  	
}
