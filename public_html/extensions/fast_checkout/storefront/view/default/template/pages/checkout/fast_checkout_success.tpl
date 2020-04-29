<link href="<?php echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>


<div id="fast_checkout_success"></div>

<script>
	showLoading = function (modal_body) {
		modal_body.html('<div class="modal_loader" style="text-align: center"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
	}

    <?php if ($success_url) { ?>
	let loadPage = function () {
		if ($('#fast_checkout_success').html() == '') {
			showLoading($('#fast_checkout_success'))
		}
		console.log('<?php echo $success_url; ?>')
		$.ajax({
			url: '<?php echo $success_url; ?>',
			type: 'POST',
			dataType: 'html',
			success: function (data) {
				$('#fast_checkout_success').hide().html(data).fadeIn(1000)
			}
		});
	}
    <?php } ?>

	$(document).ready(() => {
		loadPage()
	})
</script>
