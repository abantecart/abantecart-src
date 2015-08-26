<?php include($tpl_common_dir . 'action_confirm.tpl');?>

<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-btns">
			<a class="panel-close" href="">×</a>
			<a class="minimize" href="">−</a>
		</div>
		<h4 class="panel-title"><?php echo $text_order_summary; ?></h4>
	</div>
	<div class="panel-body panel-body-nopadding table-responsive" style="display: block;">
		<table id="summary" class="table table-condensed summary" >
			<tr>
				<td class="summary_label"><?php echo $entry_order_id; ?></td>
				<td class="summary_value"><?php echo $order['order_id']; ?></td>
				<td class="summary_label"><?php echo $entry_order_status; ?></td>
				<td class="summary_value"><?php echo $order['order_status']; ?></td>
			</tr>
			<tr>
				<td class="summary_label"><?php echo $entry_customer; ?></td>
				<td class="summary_value"><?php echo $order['name']; ?></td>
				<td class="summary_label"><?php echo $entry_email; ?></td>
				<td class="summary_value"><?php echo $order['email']; ?></td>
			</tr>
			<tr>
				<td class="summary_label"><?php echo $entry_date_added; ?></td>
				<td class="summary_value"><?php echo $order['date_added']; ?></td>
				<td class="summary_label"><?php echo $entry_total; ?></td>
				<td class="summary_value"><?php echo $order['total']; ?></td>
			</tr>
			<tr>
				<td class="summary_label"><?php echo $entry_shipping_method; ?></td>
				<td class="summary_value"><?php echo $order['shipping_method']; ?></td>
				<td class="summary_label"><?php echo $entry_payment_method; ?></td>
				<td class="summary_value"><?php echo $order['payment_method']; ?></td>
			</tr>
			<?php echo $this->getHookVar('order_summary_hook_var'); ?>
		</table>
	</div>
</div>