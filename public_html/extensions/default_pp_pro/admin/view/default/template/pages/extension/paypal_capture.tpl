<div class="col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $text_payment_status; ?></label>
			<div class="input-group afield col-sm-7">
				<p class="form-control-static"><?php echo $payment_status; ?><?php echo  $pending_reason ? '('.$pending_reason.')':''; ?></p>
			</div>
		</div>
	<?php if ( has_value($refunded_amount) ){ ?>
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $text_already_refunded; ?></label>
			<div id="refunded_amount" class="input-group afield col-sm-7"><?php echo $refunded_amount; ?></div>
		</div>
	<?php } ?>
</div>
<?php if ( has_value($pp_capture_amount) ){ ?>
<div class="col-sm-6 col-xs-12">
		<div class="form-group">
			<label class="control-label col-sm-5"><?php echo $text_capture_funds; ?></label>
			<div class="input-group afield col-sm-7">
				<div class="col-sm-4 col-xs-12"><?php echo $pp_capture_amount; ?></div>
				<?php $pp_capture_submit->style = 'btn btn-info'; echo $pp_capture_submit; ?>
				<div id="pp_capture_loading" class="ajax_loading" style="display: none;"></div>
				<input type="hidden" name="pp_order_id">
			</div>
		</div>
</div>
<?php } ?>

<script type="text/javascript">

	$('#pp_capture_submit').click(function() {


		$('#pp_capture_submit').hide();
		$('#pp_capture_loading').show();

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
						$('#pp_capture_loading').hide();
						$('#pp_capture_submit').show();
						error_alert('<?php echo $error_service_unavailable; ?>');
					}
				}
			});
		}


	});

</script>