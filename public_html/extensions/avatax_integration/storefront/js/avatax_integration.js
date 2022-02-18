$('#test_connection').click(function() {		
		$.ajax({
			url: '<?php echo $this->html->getSecureURL(\'r/extension/avatax_integration/test_address\');; ?>',
			type: 'GET',
			dataType: 'json',
			beforeSend: function() {
				$('#test_connection').button('loading');
			},
			success: function( response ) { 
				if ( !response ) {
					error_alert( '<?php js_echo($error_turn_extension_on); ?>' );
					return false;
				}
				info_alert( response['message'] );
				$('#test_connection').button('reset');
			}
		});
		return false;
	});