<?php echo $head; ?>
<link href="<?php echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>


<div id="fast_checkout_success"></div>

<script>
    <?php if ($success_url) { ?>
	let loadPage = function () {
		$('.spinner-overlay').fadeIn(100);
		$.ajax({
			url: '<?php echo $success_url; ?>',
			type: 'POST',
			dataType: 'html',
			success: function (data) {
				$('#fast_checkout_success').hide().html(data).fadeIn(1000)
				$('.spinner-overlay').fadeOut(500);
			}
		});
	}
    <?php } ?>

	$(document).ready(() => {
		$('body').append('<div class=\'spinner-overlay\'><div class="spinner"></div><div>')
		loadPage()
	})
</script>
<?php echo $footer; ?>
