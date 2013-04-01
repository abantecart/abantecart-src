<div id="content">
	<div class="top">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center">
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="middle">
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
		<div class="buttons">
			<table>
				<tr>
					<td align="right"><?php echo $button_continue; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="bottom">
		<div class="left"></div>
		<div class="right"></div>
		<div class="center"></div>
	</div>
</div>
<script type="text/javascript">

	$('#continue_button').click(function() {
		location = '<?php echo $continue; ?>';
	})

	function viewOrder(order_id) {
		location = '<?php echo $order_url; ?>&order_id=' + order_id;
	}
</script>