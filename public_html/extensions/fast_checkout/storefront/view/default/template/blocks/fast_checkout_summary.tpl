<div id="fast_checkout_summary_block"></div>

<script>
	showLoading = function (modal_body) {
		modal_body.html('<div class="modal_loader" style="text-align: center"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
	}

    <?php if ($summaryUrl) { ?>
	let loadBlockContent = function () {
		if ($('#fast_checkout_summary_block').html() == '') {
			//showLoading($('#fast_checkout_summary_block'))
		}
		$.ajax({
			url: '<?php echo $summaryUrl; ?>',
			type: 'GET',
			dataType: 'html',
			success: function (data) {
				$('#fast_checkout_summary_block').hide().html(data).fadeIn(1000)
			}
		});
	}
    <?php } ?>

	$('#fast_checkout_summary_block').on('reload', function () {
		loadBlockContent()
	});
</script>
