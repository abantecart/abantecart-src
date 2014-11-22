<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="checkout">
  <input type="hidden" name="VPSProtocol" value="2.23" />
  <input type="hidden" name="TxType" value="<?php echo $transaction; ?>" />
  <input type="hidden" name="Vendor" value="<?php echo $vendor; ?>" />
  <input type="hidden" name="Crypt" value="<?php echo $crypt; ?>" />
  
	<div class="form-group action-buttons">
    	<div class="col-md-12">
	   	<button class="btn btn-orange pull-right" title="<?php echo $button_confirm; ?>" onclick="$('#checkout').submit();" type="submit">
	   	    <i class="fa fa-check"></i>
	   	    <?php echo $button_confirm; ?>
	   	</button>
	   	<a  href="<?php echo str_replace('&', '&amp;', $back); ?>" class="btn btn-default mr10" title="<?php echo $button_back; ?>">
	   	    <i class="fa fa-arrow-left"></i>
	   	    <?php echo $button_back; ?>
	   	</a>
	    </div>
	</div>
	  
</form>