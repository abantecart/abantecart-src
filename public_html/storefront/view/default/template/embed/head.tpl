<head>
<meta http-equiv="cache-control" content="max-age=0">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="-1">
<meta http-equiv="expires" content="Tue, 01 Jan 1980 11:00:00 GMT">
<meta http-equiv="pragma" content="no-cache">

<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<base href="<?php echo $base; ?>" />

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<link href="<?php echo $this->templateResource('/stylesheet/style.response.css'); ?>" rel="stylesheet" type='text/css' />
<link href="<?php echo $this->templateResource('/javascript/intl-tel-input/css/intlTelInput.css'); ?>" rel="stylesheet" type='text/css' />

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script type="text/javascript" src="<?php echo $ssl ? 'https': 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript">
if (typeof jQuery == 'undefined') {
   var include = '\x3Cscript type="text/javascript" src="<?php echo $this->templateResource("/javascript/jquery-1.12.4.min.js"); ?>">\x3C/script>';
   document.write(include);
}
</script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery-migrate-1.2.1.min.js');?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/intl-tel-input/js/intlTelInput.min.js'); ?>"></script>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>

<script type="text/javascript">
	$(document).on('click', 'a.productcart', function () {
		var item = $(this);
		//check if href provided for product details access
		if (item.attr('href') && item.attr('href') != '#') {
			return true;
		}

		if (item.attr('data-id')) {
			$.ajax({
				url: '<?php echo $cart_ajax_url; ?>',
				type: 'GET',
				dataType: 'json',
				data: {product_id: item.attr('data-id')},
				success: function (data) {
					var alert_msg = '<div class="quick_basket">'
							+ '<a href="<?php echo $cart_url ?>" title="<?php echo $text_add_cart_confirm; ?>">'
							+ '<i class="fa fa-shopping-cart fa-fw"></i></a></div>';
					item.closest('.thumbnail .pricetag').addClass('added_to_cart').prepend(alert_msg);
				}
			});
		}
		return false;
	});

	$(document).on('click', 'a.call_to_order', function () {
		goTo('<?php echo $call_to_order_url;?>');
		return false;
	});
</script>

</head>
<body>
<?php if($maintenance_warning){ ?>
	<div class="alert alert-warning">
	 	<button type="button" class="close" data-dismiss="alert">&times;</button>
 		<strong><?php echo $maintenance_warning;?></strong>
 	</div>
<?php } ?>