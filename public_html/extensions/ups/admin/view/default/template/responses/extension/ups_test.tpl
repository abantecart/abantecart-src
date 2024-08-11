<div class="col-xs-12">
	<div class="row col-xs-7 input-group afield">
<?php
$text_test = $this->language->get('ups_test_connection','ups/ups');
	echo $this->html->buildElement(
            [
                'type' => 'button',
                'name' => 'test_connection',
                'title' => $text_test,
                'text' => $text_test,
                'style' => 'btn btn-info'
            ]
    ); ?>
	</div>
</div>
<script type="text/javascript">

	<?php if ( $this->config->get('ups_test')){ ?>
		$('.panel-body.panel-body-nopadding.tab-content').addClass('status_test');
	<?php }else{ ?>
		$('.panel-body.panel-body-nopadding.tab-content').removeClass('status_test');
	<?php }?>
	$('#test_connection').click(function() {

		$.ajax({
			url: '<?php echo $this->html->getSecureUrl('r/extension/ups/test'); ?>',
			type: 'POST',
			dataType: 'json',
			data: $('#editSettings').serialize(),
			beforeSend: function() {
				$('#test_connection').button('loading');
			},
			success: function( response ) {
				info_alert( response['message'] );
				$('#test_connection').button('reset');
			},
			complete: function(){
				$('#test_connection').button('reset');
			}
		});
		return false;
	});

</script>
