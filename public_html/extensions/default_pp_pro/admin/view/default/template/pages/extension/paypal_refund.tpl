<tr>
	<td><?php echo $text_payment_status; ?></td>
	<td><?php echo $payment_status; ?></td>
</tr>

<?php if ( has_value($refunded_amount) ): ?>
<tr>
	<td><?php echo $text_already_refunded; ?></td>
	<td id="refunded_amount"><?php echo $refunded_amount; ?></td>
</tr>
<?php endif; ?>

<?php if ( $pp_refund_amount ): ?>
<tr>
	<td colspan="2" style="display: none;" id="pp_refund_message_td"><div id="pp_refund_message"></div></td>
</tr>
<tr>
	<td><?php echo $text_do_paypal_refund; ?></td>
	<td>
		<?php echo $pp_refund_amount; ?>
		<span class="abuttons_grp" style="display: inline-block;">
			<a class="btn_standard"><?php echo $pp_refund_submit; ?></a>
			<div id="pp_refund_loading" class="ajax_loading" style="display: none;"></div>
		</span>

		<input type="hidden" name="pp_order_id" />
	</td>
</tr>
<?php endif; ?>

<script type="text/javascript">

	$('#pp_refund_submit').click(function() {

		$('#pp_refund_message_td').hide();

		var amount = $('#pp_refund_amount').val();

		if ( amount > 0 ) {
			$('#pp_refund_submit').hide();
			$('#pp_refund_loading').show();
			$.ajax({
				url: '<?php echo $pp_refund_action; ?>' + '&amount=' + amount,
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					//$('#pp_refund_loading').hide();
					//$('#pp_refund_submit').show();
					if ( result ) {
						/*
						$('#pp_refund_message').html(result.message);
						var div_class = 'warning';

						if ( !result.error ) {
							div_class = 'success';
							$('#refunded_amount').text(result.refunded_amount);
						}

						$('#pp_refund_message').removeAttr('class').addClass(div_class);
						$('#pp_refund_message_td').show();
						*/
						window.location.href = result.href;
					} else {
						$('#pp_refund_loading').hide();
						$('#pp_refund_submit').show();
						$('#pp_refund_message').html('<?php echo $error_service_unavailable; ?>');
						$('#pp_refund_message').removeAttr('class').addClass('warning');
						$('#pp_refund_message_td').show();
					}
				}
			});
		} else {
			$('#pp_refund_message').html('<?php echo $error_wrong_amount; ?>');
			$('#pp_refund_message').removeAttr('class').addClass('warning');
			$('#pp_refund_message_td').show();
		}


	});

</script>