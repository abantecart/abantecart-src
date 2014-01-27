<title><?php echo $title; ?></title>
<meta http-equiv="x-ua-compatible" content="IE=Edge" />
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>

<meta name="generator" content="AbanteCart v<?php echo VERSION; ?> - Open Source eCommerce solution" />

<base href="<?php echo $base; ?>" />

<?php if ( is_file( DIR_RESOURCE . $icon ) ) {  ?>
<link href="resources/<?php echo $icon; ?>" type="image/png" rel="icon" />
<?php } ?>

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->templateResource('/stylesheet/stylesheet.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->templateResource('/stylesheet/boxes.css'); ?>" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="<?php echo $this->templateResource('/stylesheet/stylesheet-ie7.css'); ?>" />
<![endif]-->

<script type="text/javascript" src="<?php echo $ssl ? 'https': 'http'?>://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript">
if (typeof jQuery == 'undefined') {
   var include = '\x3Cscript type="text/javascript" src="<?php echo $this->templateResource("/javascript/jquery/jquery-1.6.4.min.js"); ?>">\x3C/script>';
   document.write(include);
}
</script>

<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery/thickbox/thickbox-compressed.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->templateResource('/javascript/jquery/thickbox/thickbox.css'); ?>" />
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery/tab.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>


<script type="text/javascript">
<?php if($cart_ajax){ //event for adding product to cart by ajax ?>
    $('a[href=\\#].buy').live('click',function(){
        var item = $(this);
        if(item.attr('id')){
            $.ajax({
                    url:'<?php echo $cart_ajax_url; ?>',
                    type:'GET',
                    dataType:'json',
                    data: {product_id:  item.attr('id') },
                    success:function (data) {
                        item.removeAttr('href').addClass('added').removeClass('buy');
	                    if($('#item_count').length>0){
		                    $('#item_count').html(data.item_count);
	                    }
						if($('#cart_total').length>0 && data.total.length>0){
		                    $('#cart_total').html(data.total);
	                    }

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

