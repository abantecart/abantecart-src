<h1 class="heading1">
  <span class="maintext"><i class="fa fa-money"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">
	<table class="table table-striped transaction-table">
	<thead>
	<tr>
		<th><?php echo $text_transaction_id; ?></th>
		<th><?php echo $text_date_added; ?></th>
		<th><?php echo $text_transaction_type; ?></th>
		<th><?php echo $text_debit; ?></th>
		<th><?php echo $text_credit; ?></th>
		<th ><?php echo $text_transaction_description; ?></th>
	</tr>
	</thead>
	<?php if( count($transactions) ) { foreach ($transactions as $trn) { ?>
		<tr>
			<td><?php echo $trn['customer_transaction_id']; ?></td>
			<td><?php echo $trn['date_added']; ?></td>
			<td><?php echo $trn['transaction_type']; ?></td>
			<td><?php echo $trn['debit']; ?></td>
			<td><?php echo $trn['credit']; ?></td>
			<td class="col-md-4"><?php echo $trn['description']; ?></td>
		</tr>
	<?php } } ?>
	</table>

	<?php
	if ( count($transactions) <=0 ) {
	?>
		<div><?php echo $text_error; ?></div>
	<?php	
	}
	?>

	<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
	
</div>


<div class="container-fluid cart_total">
	<div class="cart-info totals pull-right">
	    <table class="table table-striped table-bordered">
	    	<tr>
	    		<td>
	    			<span class="extra bold totalamout"><?php echo $text_total; ?></span>
	    		</td>
	    		<td>
	    			<span class="bold totalamout"><?php echo $balance_amount; ?></span>
	    		</td>
	    	</tr>
	    </table>

	    <a href="<?php echo $continue; ?>" class="btn btn-default mr10 pull-right" title="<?php echo $button_continue->text ?>">
	    	    <i class="fa fa-arrow-right"></i>
	    	    <?php echo $button_continue->text ?>
	    </a>
	</div>
</div>
