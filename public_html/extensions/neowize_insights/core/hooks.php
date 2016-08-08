<?php
/*
NeoWize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

/**
 * This class listen to some useful hooks and send data to NeoWize
 */
class ExtensionNeowizeInsights extends Extension {

	// HOOK FUNCTIONS

	// called on checkout complete and send order data to NeoWize.
	public function onControllerPagesCheckoutSuccess_InitData()
	{
		try
		{
			$this->addCheckoutDataToPage();
		}
		catch (Exception $e)
		{
			NeowizeUtils::reportException(__FUNCTION__, $e);
		}
	}

	// INTERNAL FUNCTIONS

	// add checkout / order data to page (via cookie), so NeoWize can parse it and use it.
	protected function addCheckoutDataToPage()
	{
		// get session data
		$session_data = $this->baseObject->session->data;

		// this controller is called twice - once with data once without it. skip the run without data.
		if (!isset($session_data['order_id'])) {
			return;
		}

		// get registry
		$registry = Registry::getInstance();

		// get order data
		$order = new AOrder( $registry );
		$order_data = $order->buildOrderData( $session_data );

		// calc grand total and other price components
		$order_tax = $order_total = $order_shipping = 0.0;
		foreach($order_data['totals'] as $total ){
			if($total['total_type']=='total'){
				$order_total += $total['value'];
			}elseif($total['total_type']=='tax'){
				$order_tax += $total['value'];
			}elseif($total['total_type']=='shipping'){
				$order_shipping += $total['value'];
			}
		}

		// convert to a dictionary
		$order_data_dict = array('order_id' => (int)$session_data['order_id'],
								 'currency_code' => $order_data['currency'],
								 'grand_total' => $order_total,
								 'subtotal' => $order_total - $order_tax - $order_shipping,
								 'tax_amount' => $order_tax,
								 'shipping_amount'=> $order_shipping,
								 'city'=>$order_data['shipping_city'],
								 'state'=>$order_data['shipping_zone'],
								 'country'=> $order_data['shipping_country']);

		// set order data into cookie. this cookie will be read from the javascript side and parsed into events.
		setcookie ('neowize_order_data', json_encode($order_data_dict), 0, '/');
	}
}