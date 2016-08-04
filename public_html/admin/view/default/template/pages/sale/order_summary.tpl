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
				<td class="summary_value">
					<?php if($customer['href']){ ?>
					<a data-toggle="modal"
					   data-target="#viewport_modal"
					   href="<?php echo $customer['vhref'] ?>"
					   data-fullmode-href="<?php echo $customer['href'] ?>">
					<?php }
					echo $customer['name'];?>
					<?php echo $customer['href']? '</a>' : '';?>
				</td>
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
<?php echo $this->html->buildElement(
		array(
				'type' => 'modal',
				'id' => 'viewport_modal',
				'modal_type' => 'lg',
                'data_source' =>'ajax',
				'title' => '',
				//run script after modal content load. Test it on slow connections in chrome
				'js_onload' => "
								var url = $(this).data('bs.modal').options.fullmodeHref;
								$('#viewport_modal .modal-header a.btn').attr('href',url);
								"
		));
?>

<script language="JavaScript" type="application/javascript">
	$('#viewport_modal').on('shown.bs.modal', function(e){
		var target = $(e.relatedTarget);
		$(this).find('.modal-footer a.btn.expand').attr('href',target.attr('data-fullmode-href'));
		var title = 'Customer - '+ '<?php	echo $customer['name'];?>';
		$(this).find('.modal-title').html(title);
	})
</script>
