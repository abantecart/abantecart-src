<link href="<?php echo $this->templateResource('/css/bootstrap-xxs.css'); ?>" rel="stylesheet" type='text/css'/>
<link href="<?php echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>

<script type="text/javascript"
		src="<?php echo $this->templateResource('/js/credit_card_validation.js'); ?>"></script>
<script type="text/javascript"
		src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>

<div id="fast_checkout_cart"></div>

<script>
	showLoading = function (modal_body) {
		modal_body.html('<div class="modal_loader" style="text-align: center"><i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i></div>');
	}

    <?php if ($cart_url) { ?>
	let loadPage = function () {
		if ($('#fast_checkout_cart').html() == '') {
			showLoading($('#fast_checkout_cart'))
		}
		$.ajax({
			url: '<?php echo $cart_url; ?>',
			type: 'GET',
			dataType: 'html',
			success: function (data) {
				$('#fast_checkout_cart').hide().html(data).fadeIn(1000)
			}
		});
	}
    <?php } ?>

	$(document).ready(() => {
		loadPage()
	})
</script>
