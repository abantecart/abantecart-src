<h1 class="heading1">
  <span class="maintext"><i class="icon-briefcase"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="container-fluid">

	<?php foreach ($orders as $order) { ?>
		
	<div style="display: inline-block; margin-bottom: 10px; width: 100%;">
	    <div style="width: 49%; float: left; margin-bottom: 2px;"><b><?php echo $text_order; ?></b>
	    	#<?php echo $order[ 'order_id' ]; ?></div>
	    <div style="width: 49%; float: right; margin-bottom: 2px; text-align: right;">
	    	<b><?php echo $text_status; ?></b> <?php echo $order[ 'status' ]; ?></div>
	    <div class="content" style="clear: both; padding: 5px;">
	    	<div style="padding: 5px;">
	    		<table width="100%">
	    			<tr>
	    				<td><?php echo $text_date_added; ?> <?php echo $order[ 'date_added' ]; ?></td>
	    				<td><?php echo $text_customer; ?> <?php echo $order[ 'name' ]; ?></td>
	    				<td rowspan="2" style="text-align: right;"><?php echo $order[ 'button' ];?></td>
	    			</tr>
	    			<tr>
	    				<td><?php echo $text_products; ?> <?php echo $order[ 'products' ]; ?></td>
	    				<td><?php echo $text_total; ?> <?php echo $order[ 'total' ]; ?></td>
	    			</tr>
	    		</table>
	    	</div>
	    </div>
	</div>
	<?php } ?>

	<div class="pagination"><?php echo $pagination_bootstrap; ?></div>
</div>

<div class="container-fluid cart_total">
	<div class="row-fluid">
			<a href="<?php echo $continue; ?>" class="btn mt10 pull-right" title="<?php echo $button_continue->text ?>">
	    		    <i class="icon-arrow-right"></i>
	    		    <?php echo $button_continue->text ?>
			</a>
	</div>
</div>

<script type="text/javascript">
	function viewOrder(order_id) {
		location = '<?php echo $order_url; ?>&order_id=' + order_id;
	}
</script>