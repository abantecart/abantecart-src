<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="checkout">
  <input type="hidden" name="instId" value="<?php echo $merchant; ?>" />
  <input type="hidden" name="cartId" value="<?php echo $order_id; ?>" />
  <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
  <input type="hidden" name="currency" value="<?php echo $currency; ?>" />
  <input type="hidden" name="desc" value="<?php echo $description; ?>" />
  <input type="hidden" name="name" value="<?php echo $name; ?>" />
  <input type="hidden" name="address" value="<?php echo $address; ?>" />
  <input type="hidden" name="postcode" value="<?php echo $postcode; ?>" />
  <input type="hidden" name="country" value="<?php echo $country; ?>" />
  <input type="hidden" name="tel" value="<?php echo $telephone; ?>" />
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="testMode" value="<?php echo $test; ?>" />

	<div class="control-group">
	   <div class="controls">
	   	<button class="btn btn-orange pull-right" onclick="$('#checkout').submit();" title="<?php echo $button_confirm; ?>" type="submit">
	   	    <i class="icon-ok icon-white"></i>
	   	    <?php echo $button_confirm; ?>
	   	</button>
	   	<a href="<?php echo str_replace('&', '&amp;', $back); ?>" class="btn mr10" title="<?php echo $button_back; ?>">
	   	    <i class="icon-arrow-left"></i>
	   	    <?php echo $button_back; ?>
	   	</a>
	    </div>
	</div>

</form>