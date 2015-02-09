<?php
	echo $form['form_open'];
	echo $form['pay_to_email'].
		 $form['recipient_description'].
		 $form['transaction_id'].
		 $form['return_url'].
		 $form['cancel_url'].
		 $form['status_url'].
		 $form['language'].
		 $form['logo_url'].
		 $form['pay_from_email'].
		 $form['firstname'].
		 $form['lastname'].
		 $form['address'].
		 $form['address2'].
		 $form['phone_number'].
		 $form['postal_code'].
		 $form['city'].
		 $form['state'].
		 $form['country'].
		 $form['amount'].
		 $form['currency'].
		 $form['detail1_text'].
		 $form['merchant_fields'].
		 $form['order_id'];
	?>
	
	<div class="form-group action-buttons">
	    <div class="col-md-12">
	   	<button class="btn btn-orange pull-right" title="<?php echo $form['submit']->name ?>" type="submit">
	   	    <i class="fa fa-check"></i>
	   	    <?php echo $form['submit']->name; ?>
	   	</button>
	   	<a id="<?php echo $form['back']->name ?>" href="<?php echo $form['back']->href; ?>" class="btn btn-default mr10" title="<?php echo $form['back']->text ?>">
	   	    <i class="fa fa-arrow-left"></i>
	   	    <?php echo $form['back']->text ?>
	   	</a>
	    </div>
	</div>
	
</form>