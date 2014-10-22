<div class="col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $text_payment_status; ?></label>
			<div class="input-group afield col-sm-7">
				<p class="form-control-static"><?php echo $payment_status; ?></p>
			</div>
		</div>
	<?php if ( has_value($refunded_amount) ){ ?>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $text_already_refunded; ?></label>
			<div id="refunded_amount" class="input-group afield col-sm-7"><?php echo $refunded_amount; ?></div>
		</div>
	<?php } ?>
</div>
<?php if ( $pp_refund_amount ){ ?>
<div class="col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $text_do_paypal_refund; ?></label>
			<div class="input-group afield col-sm-7">
				<div class="col-sm-4 col-xs-12"><?php echo $pp_refund_amount; ?></div>
				<?php $pp_refund_submit->style = 'btn btn-info'; echo $pp_refund_submit; ?>
				<div id="pp_refund_loading" class="ajax_loading" style="display: none;"></div>
				<input type="hidden" name="pp_order_id">
			</div>
		</div>
</div>
<?php } ?>


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

					if ( result ) {
						goTo( result.href );
					} else {
						$('#pp_refund_loading').hide();
						$('#pp_refund_submit').show();
						error_alert('<?php echo $error_service_unavailable; ?>');
					}
				}
			});
		} else {
			error_alert('<?php echo $error_wrong_amount; ?>');
		}
	return false;
	});

</script>