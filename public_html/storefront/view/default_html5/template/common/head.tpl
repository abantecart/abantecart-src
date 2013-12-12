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

<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo $this->templateResource('/image/apple-icon-57x57-precomposed.png');?>" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $this->templateResource('/image/apple-icon-72x72-precomposed.png');?>" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $this->templateResource('/image/apple-icon-114x114-precomposed.png');?>" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $this->templateResource('/image/apple-icon-144x144-precomposed.png');?>" />

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300italic,400italic,600,600italic' rel='stylesheet' type='text/css' />
<link href='//fonts.googleapis.com/css?family=Crete+Round' rel='stylesheet' type='text/css' />
<link href="<?php echo $this->templateResource('/stylesheet/bootstrap.min.css'); ?>" rel="stylesheet" media="all" />
<link href="<?php echo $this->templateResource('/stylesheet/bootstrap-responsive.min.css'); ?>" rel="stylesheet" media="screen" />
<link href="<?php echo $this->templateResource('/stylesheet/style.css'); ?>" rel="stylesheet" />
<link href="<?php echo $this->templateResource('/stylesheet/flexslider.css'); ?>" type="text/css" media="screen" rel="stylesheet"  />
<link href="<?php echo $this->templateResource('/stylesheet/cloud-zoom.css'); ?>" rel="stylesheet" />
<link href="<?php echo $this->templateResource('/stylesheet/onebyone.css'); ?>" rel="stylesheet" />
<link href="<?php echo $this->templateResource('/stylesheet/print.css'); ?>" rel="stylesheet" type="text/css" media="print" />

<link href="<?php echo $this->templateResource('/stylesheet/font-awesome.min.css'); ?>" rel="stylesheet" media="all" />
<!--[if IE 7]>
	<link href="<?php echo $this->templateResource('/stylesheet/font-awesome-ie7.min.css'); ?>" rel="stylesheet" />
<![endif]-->

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

<script type="text/javascript" src="<?php echo $ssl ? 'https': 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
if (typeof jQuery == 'undefined') {
   var include = '\x3Cscript type="text/javascript" src="<?php echo $this->templateResource("/javascript/jquery-1.8.2.min.js"); ?>">\x3C/script>';
   document.write(include);
}
</script>

<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<script type="text/javascript">
<?php if($cart_ajax){ //event for adding product to cart by ajax ?>
    $('a.productcart').live('click',function(){
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
$('a.call_to_order').live('click',function(){
	location='<?php echo $call_to_order_url;?>';
	return false;
});
</script>

