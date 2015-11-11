<div class="table-responsive">
	<table class="table table-striped">
		<tr>
			<td class="col-sm-3"><?php echo $text_payment_status; ?></td>
			<td class="col-sm-6"><?php echo $payment_status; ?><?php echo  $pending_reason ? '('.$pending_reason.')':''; ?></td>
		</tr>
<?php

if ( has_value($refunded_amount) ){ ?>
		<tr>
			<td class="col-sm-3"><?php echo $text_already_refunded; ?></td>
			<td class="col-sm-6">
				<div id="refunded_amount" class="input-group afield col-sm-7"><?php echo $refunded_amount; ?></div>
			</td>
		</tr>
<?php }

// REFUND FORM

if ( $pp_refund_amount ){ ?>
	<tr>
		<td class="col-sm-3"><label><?php echo $text_do_paypal_refund; ?></label></td>
		<td class="col-sm-6">
				<div class="col-sm-2 col-xs-12"><?php echo $pp_refund_amount; ?></div>
		<?php $pp_refund_submit->style = 'btn btn-info lock-on-click';
				echo $pp_refund_submit; ?>
				<input type="hidden" name="pp_order_id">
		</td>
	</tr>
<?php }

// CAPTURE FORM
if ( has_value($pp_capture_amount) ){ ?>
	<tr>
		<td class="col-sm-3"><?php echo $text_capture_funds; ?></td>
		<td class="col-sm-6">
			<div class="col-sm-2 col-xs-12"><?php echo $pp_capture_amount; ?></div>
			<?php $pp_capture_submit->style = 'btn btn-info lock-on-click'; echo $pp_capture_submit; ?>
			<input type="hidden" name="pp_order_id">
		</td>
	</tr>
<?php } ?>
	</table>
</div>

<script type="text/javascript">
	$('#pp_refund_submit').click(function() {

		$('#pp_refund_message_td').hide();

		var amount = $('#pp_refund_amount').val();
		if ( amount > 0 ) {
			$.ajax({
				url: '<?php echo $pp_refund_action; ?>' + '&amount=' + amount,
				type: 'GET',
				dataType: 'json',
				success: function(result) {

					if ( result ) {
						goTo( result.href );
					} else {
						error_alert(<?php js_echo($error_service_unavailable); ?>);
					}
				}
			});
		} else {
			error_alert(<?php js_echo($error_wrong_amount); ?>);
		}
		return false;
	});

	$('#pp_capture_submit').click(function() {

		var amount = $('#pp_capture_amount').val();

		if ( amount > 0 ) {

			$.ajax({
				url: '<?php echo $pp_capture_action; ?>',
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					if ( result ) {
						goTo( result.href );
					} else {
						error_alert(<?php js_echo($error_service_unavailable); ?>);
					}
				}
			});
		}

	});
</script>