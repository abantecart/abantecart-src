<div class="col-xs-12">
	<div class="row col-xs-7 input-group afield">
<?php
$text_generate = $this->language->get('usps_generate_payment_token_button', 'usps/usps');
if ($text_generate === 'usps_generate_payment_token_button') {
	$text_generate = 'Test Label Generation';
}
echo $this->html->buildElement(
	[
		'type'  => 'button',
		'name'  => 'usps_generate_payment_token',
		'title' => $text_generate,
		'text'  => $text_generate,
		'style' => 'btn btn-default'
	]
); ?>
	</div>
</div>
<script type="text/javascript">
	$('#usps_generate_payment_token').click(function() {
		$.ajax({
			url: '<?php echo $this->html->getSecureUrl('r/extension/usps/payment_token'); ?>',
			type: 'POST',
			dataType: 'json',
			data: $('#editSettings').serialize(),
			beforeSend: function() {
				$('#usps_generate_payment_token').button('loading');
			},
			success: function(response) {
				if (response['error_text']) {
					error_alert(response['error_text']);
				} else {
					info_alert(response['message'] || 'Label generation test passed.');
				}
				$('#usps_generate_payment_token').button('reset');
			},
			error: function(xhr) {
				var fallback = 'USPS label generation test failed.';
				if (xhr && xhr.responseText) {
					fallback = xhr.responseText;
				}
				error_alert(fallback);
				$('#usps_generate_payment_token').button('reset');
			},
			complete: function() {
				$('#usps_generate_payment_token').button('reset');
			}
		});
		return false;
	});
</script>
