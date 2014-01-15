<tr>
	<td><?php echo $text_payment_status; ?></td>
	<td>
		<?php echo $payment_status; ?>
		<?php if ( $pending_reason ): ?>
			&nbsp;
			<?php echo $pending_reason; ?>
		<?php endif; ?>
	</td>
</tr>

<?php if ( has_value($pp_capture_amount) ): ?>
	<tr>
		<td colspan="2" style="display: none;" id="pp_capture_message_td"><div id="pp_capture_message"></div></td>
	</tr>
	<tr>
		<td><?php echo $text_capture_funds; ?></td>
		<td>
			<?php echo $pp_capture_amount; ?>
			<span class="abuttons_grp" style="display: inline-block;">
				<a class="btn_standard"><?php echo $pp_capture_submit; ?></a>
				<div id="pp_capture_loading" class="ajax_loading" style="display: none;"></div>
			</span>

			<input type="hidden" name="pp_order_id" />
		</td>
	</tr>
<?php endif; ?>

<script type="text/javascript">

	$('#pp_capture_submit').click(function() {

		$('#pp_capture_message_td').hide();
		$('#pp_capture_submit').hide();
		$('#pp_capture_loading').show();

		var amount = $('#pp_capture_amount').val();

		if ( amount > 0 ) {

			$.ajax({
				url: '<?php echo $pp_capture_action; ?>',
				type: 'GET',
				dataType: 'json',
				success: function(result) {
					//$('#pp_capture_loading').hide();
					//$('#pp_capture_submit').show();
					if ( result ) {
						/*
						$('#pp_capture_message').html(result.message);
						var div_class = result.error ? 'warning' : 'success';
						$('#pp_capture_message').removeAttr('class').addClass(div_class);
						$('#pp_capture_message_td').show();
						*/
						window.location.href = result.href;
					} else {
						$('#pp_capture_loading').hide();
						$('#pp_capture_submit').show();
						$('#pp_capture_message').html('<?php echo $error_service_unavailable; ?>');
						$('#pp_capture_message').removeAttr('class').addClass('warning');
						$('#pp_capture_message_td').show();
					}
				}
			});
		}


	});

</script>