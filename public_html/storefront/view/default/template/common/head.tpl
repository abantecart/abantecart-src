<title><?php echo $title; ?></title>
<meta charset="UTF-8">
<!--[if IE]>
	<meta http-equiv="x-ua-compatible" content="IE=Edge" />
<![endif]-->
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<meta name="generator" content="AbanteCart v<?php echo VERSION; ?> - Open Source eCommerce solution" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<base href="<?php echo $base; ?>" />

<?php if ( is_file( DIR_RESOURCE . $icon ) ) {  ?>
<link href="resources/<?php echo $icon; ?>" type="image/png" rel="icon" />
<?php } ?>

<link href="<?php echo $this->templateResource('/image/apple-touch-icon.png');?>" rel="apple-touch-icon" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-76x76.png');?>" rel="apple-touch-icon" sizes="76x76" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-120x120.png');?>" rel="apple-touch-icon" sizes="120x120" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-152x152.png');?>" rel="apple-touch-icon" sizes="152x152" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-precomposed.png');?>" rel="apple-touch-icon-precomposed" />

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300italic,400italic,600,600italic' rel='stylesheet' type='text/css' />
<link href='//fonts.googleapis.com/css?family=Crete+Round' rel='stylesheet' type='text/css' />
<link href="<?php echo $this->templateResource('/stylesheet/style.css'); ?>" rel="stylesheet" type='text/css' />

<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/stylesheet/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script type="text/javascript" src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<!-- fav -->

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script type="text/javascript"
        src="<?php echo $ssl ? 'https' : 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
    if (typeof jQuery == 'undefined') {
        var include = '<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery-1.11.0.min.js'); ?>"><\/script>';
        document.write(include);
    }
</script>
<script type="text/javascript" src="<?php echo $template_dir; ?>javascript/jquery-migrate-1.2.1.min.js"></script>

<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<script type="text/javascript">
<?php if($cart_ajax){ //event for adding product to cart by ajax ?>
	$(document).on('click', 'a.productcart', function() {
        var item = $(this);
        //check if href provided for product details access
        if ( item.attr('href') && item.attr('href') != '#') {
        	return true;
        }
        
        if(item.attr('data-id')){
            $.ajax({
                    url:'<?php echo $cart_ajax_url; ?>',
                    type:'GET',
                    dataType:'json',
                    data: {product_id:  item.attr('data-id') },
                    success:function (data) {
                    	var alert_msg = '<div class="alert alert-info added_to_cart"> \
                    		<button type="button" class="close" data-dismiss="alert">&times;</button> \
                    		&nbsp;&nbsp;<a href="<?php echo $cart_url ?>"><?php echo $text_add_cart_confirm; ?> \
                    		&nbsp;<img src="<?php echo $this->templateResource("/image/addcart.png"); ?>"></a> \
                    		</div>';
						item.closest('.thumbnail .pricetag').prepend(alert_msg);

						//topcart
						$('.nav.topcart .dropdown-toggle span').first().html(data.item_count);
						$('.nav.topcart .dropdown-toggle .cart_total').html(data.total);
						if($('#top_cart_product_list')){
							$('#top_cart_product_list').html(data.cart_details);
						};
                    }
            });
        }
    return false;
});
<?php }?>
$('a.call_to_order').on('click',function(){
	location='<?php echo $call_to_order_url;?>';
	return false;
});
</script>

