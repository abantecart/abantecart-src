<?php
    if (! defined ( 'DIR_CORE' )) {
        header ( 'Location: static_pages/' );
    }


/**
* NeoWize footer block, include the JavaScript, init, and put some metadata in page.
*/
class ControllerBlocksNeowizeInsights extends AController {

    public $data = array();

    public function main() {

        try
        {
        	// make sure Neowize should run on this system
        	if (!NeowizeUtils::shouldRun())
        	{
        		return;
        	}

            // we try to put this block in several places because some templates don't have all layout blocks
            // so this mechanism is to make sure if we succeed to be in more than one place we'll ignore the redundant occurrences.
            if ($this->registry->get('neowize_already_init')) {return;}
            $this->registry->set('neowize_already_init', true);

            // init controller data
            $this->extensions->hk_InitData($this, __FUNCTION__);

            // get neowize api key
            $this->data['api_key'] = NeowizeUtils::getApiKey();

            // get current product id (relevant for product pages)
            $this->data['product_id'] = $this->request->get['product_id'];

            // get current cart data
            $this->data['current_cart'] = json_encode($this->getCurrentCartData());

            // set data and process template
            $this->view->batchAssign( $this->data );
            $this->processTemplate('blocks/neowize_insights.tpl');

            // init controller data
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
        }
        catch (Exception $e)
		{
			NeowizeUtils::reportException(__FUNCTION__, $e);
		}
    }

    // get current cart data
  	protected function getCurrentCartData()
  	{
		// get current cart data
		$registry = Registry::getInstance();
		$products = $registry->get('cart')->getProducts();

		// convert cart to neowize format with needed data
		$curr_cart = array();
		foreach ( $products as $key => $product ) {

			// get product id
			$product_id = $product['product_id'];

			// get main image url
			$main_image = NeowizeUtils::getProductImage($product_id, $this->config);

			// get product options data
			$options = array();
			if (isset($product['option']))
			{
				foreach ($product['option'] as $option)
				{
					array_push($options, array(
						'option_value_id' => $option['product_option_value_id'],
						'option_id' => $option['product_option_id'],
						'value' => $option['value'],
						'price_mod' => $option['price'],
						'price_mod_by_percent' => $option['prefix'] == '%',
					));
				}
			}

			// set product data
			$product_data = array("image_url" => $main_image,
								  "unique_id" => $product_id,
								  "quantity" => $product['quantity'],
								  "unit_price" => $product['price'],
								  "item_name" => $product['name'],
								  "options" => $options,
								  );

			// add product data to cart
			array_push($curr_cart, $product_data);
	 	}

		// add data to page
		return $curr_cart;
  	}
}