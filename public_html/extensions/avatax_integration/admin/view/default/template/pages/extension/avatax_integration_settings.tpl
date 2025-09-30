<?php
$test_connection_url = $this->html->getSecureURL('r/extension/avatax_integration/test');
//TEST CONNECTION BUTTON ?>
    <div class="input-group afield <?php echo $widthcasses; ?>">
        <?php
        echo $this->html->buildElement([
            'type'  => 'button',
            'name'  => 'test_connection',
            'title' => $text_test,
            'text'  => $text_test,
            'style' => 'btn btn-info',
        ]); ?>
    </div>
<script type="text/javascript">
	$('#test_connection').click(function () {
		$.ajax({
			url: '<?php echo $test_connection_url; ?>',
			type: 'GET',
			dataType: 'json',
			beforeSend: function () {
				$('#test_connection').button('loading');
			},
			success: function (response) {
				if (!response) {
					error_alert( <?php js_echo($error_turn_extension_on); ?> );
					return false;
				}
				if (response['error'] == true) {
					error_alert(response['message']);
				}
				else {
					info_alert(response['message']);
				}
				$('#test_connection').button('reset');
			}
		});
		return false;
	});
</script>