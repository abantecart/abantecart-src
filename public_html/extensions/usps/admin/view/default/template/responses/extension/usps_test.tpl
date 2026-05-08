<div class="col-xs-12">
	<div class="row col-xs-7 input-group afield">
<?php
$text_test = $this->language->get('usps_test_connection', 'usps/usps');
echo $this->html->buildElement(
	[
		'type'  => 'button',
		'name'  => 'usps_test_connection',
		'title' => $text_test,
		'text'  => $text_test,
		'style' => 'btn btn-info'
	]
); ?>
	</div>
</div>
<script type="text/javascript">
	$('#usps_test_connection').click(function() {
		$.ajax({
			url: '<?php echo $this->html->getSecureUrl('r/extension/usps/test'); ?>',
			type: 'POST',
			dataType: 'json',
			data: $('#editSettings').serialize(),
			beforeSend: function() {
				$('#usps_test_connection').button('loading');
			},
			success: function(response) {
				if (response['error_text']) {
					error_alert(response['error_text']);
				} else if (response['message']) {
					info_alert(response['message']);
				}
				$('#usps_test_connection').button('reset');
			},
			error: function(xhr) {
				var fallback = 'USPS connection test failed.';
				if (xhr && xhr.responseText) {
					fallback = xhr.responseText;
				}
				error_alert(fallback);
				$('#usps_test_connection').button('reset');
			},
			complete: function() {
				$('#usps_test_connection').button('reset');
			}
		});
		return false;
	});
</script>
