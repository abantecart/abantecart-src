<meta charset="UTF-8">
<!--[if IE]>
	<meta http-equiv="x-ua-compatible" content="IE=Edge" />
<![endif]-->
<title><?php echo $title; ?></title>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<meta name="generator" content="AbanteCart v<?php echo VERSION; ?> - Open Source eCommerce solution" />

<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<base href="<?php echo $base; ?>" />

<?php
if ($google_tag_manager) {
    ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','<?php echo trim($google_tag_manager); ?>');</script>
	<!-- End Google Tag Manager -->
    <?php
}
?>

<?php if ( is_file( DIR_RESOURCE . $icon ) ) {  ?>
<link href="resources/<?php echo $icon; ?>" type="image/png" rel="icon" />
<?php } ?>

<link href="<?php echo $this->templateResource('/image/apple-touch-icon.png');?>" rel="apple-touch-icon" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-76x76.png');?>" rel="apple-touch-icon" sizes="76x76" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-120x120.png');?>" rel="apple-touch-icon" sizes="120x120" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-152x152.png');?>" rel="apple-touch-icon" sizes="152x152" />
<link href="<?php echo $this->templateResource('/image/icon-192x192.png');?>" rel="apple-touch-icon" sizes="192x192" />

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<?php
/*
	Set $faster_browser_rendering == true; for loading tuning. For better rendering minify and include inline css.
    Note: This will increase page size, but will improve HTML rendering.
    As alternative, you can merge all CSS files in to one singe file and minify
    Example: <link href=".../stylesheet/all.min.css" rel="stylesheet" type='text/css' />

    Check Dan Riti's blog for more fine tuning suggestions:
    https://www.appneta.com/blog/bootstrap-pagespeed/
*/
$faster_browser_rendering = false;

if($faster_browser_rendering == true) {
?>
	<style><?php echo $this->LoadMinifyCSS('/stylesheet/bootstrap.min.css'); ?></style>
	<style><?php echo $this->LoadMinifyCSS('/stylesheet/flexslider.css'); ?></style>
	<style><?php echo $this->LoadMinifyCSS('/stylesheet/onebyone.css'); ?></style>
	<style><?php echo $this->LoadMinifyCSS('/stylesheet/font-awesome.min.css'); ?></style>
	<style><?php echo $this->LoadMinifyCSS('/stylesheet/style.css'); ?></style>
<?php } else { ?>
	<link href="<?php echo $this->templateResource('/stylesheet/bootstrap.min.css'); ?>" rel="stylesheet" type='text/css' />
	<link href="<?php echo $this->templateResource('/stylesheet/flexslider.css'); ?>" rel="stylesheet" type='text/css' />
	<link href="<?php echo $this->templateResource('/stylesheet/onebyone.css'); ?>" rel="stylesheet" type='text/css' />
	<link href="<?php echo $this->templateResource('/stylesheet/font-awesome.min.css'); ?>" rel="stylesheet" type='text/css' />
	<link href="<?php echo $this->templateResource('/stylesheet/style.css'); ?>" rel="stylesheet" type='text/css' />
<?php } ?>

<?php
/* Basic print styles */
?>
<style>
.visible-print  { display: inherit !important; }
.hidden-print   { display: none !important; }

a[href]:after {
	content: none !important;
}
</style>

<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/stylesheet/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<?php
if($faster_browser_rendering == true) {
?>
	<script type="text/javascript"><?php echo $this->PreloadJS('/javascript/jquery-1.12.4.min.js'); ?></script>
	<script type="text/javascript"><?php echo $this->PreloadJS('/javascript/jquery-migrate-1.2.1.min.js'); ?></script>
<?php } else { ?>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery-1.12.4.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery-migrate-1.2.1.min.js');?>"></script>
<?php } ?>

<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php } ?>

<script type="text/javascript">
	var baseUrl = '<?php echo $base; ?>';
<?php if($retina){?>
	if((window.devicePixelRatio===undefined?1:window.devicePixelRatio)>1) {
		document.cookie = 'HTTP_IS_RETINA=1;path=/';
	}
<?php } ?>
<?php if($cart_ajax){ ?>

	function update_cart(product_id){

		var senddata = {},
			result = false;
		if(product_id){
			senddata['product_id'] = product_id;
		}
		$.ajax({
                url:'<?php echo $cart_ajax_url; ?>',
                type:'GET',
                dataType:'json',
                data: senddata,
				async: false,
                success:function (data) {
					//top cart
					$('.nav.topcart .dropdown-toggle span').first().html(data.item_count);
					$('.nav.topcart .dropdown-toggle .cart_total').html(data.total);
					if($('#top_cart_product_list')){
						$('#top_cart_product_list').html(data.cart_details);
					};
	                result = true;
                }
        });
		return result;
	}

	//event for adding product to cart by ajax
	$(document).on('click', 'a.productcart', function() {
        var item = $(this);
        //check if href provided for product details access
        if ( item.attr('href') && item.attr('href') != '#') {
        	return true;
        }
        if(item.attr('data-id')){
	        if( update_cart(item.attr('data-id')) == true ) {
		        var alert_msg = '<div class="quick_basket">'
				        + '<a href="<?php echo $cart_url ?>" title="<?php echo $text_add_cart_confirm; ?>">'
				        + '<i class="fa fa-shopping-cart fa-fw"></i></a></div>';
				item.closest('.thumbnail .pricetag').addClass('added_to_cart').prepend(alert_msg);
	        }
        }
    return false;
});
$(window).on('load', function(){
	update_cart();
});
<?php }?>
$(document).on('click','a.call_to_order',function(){
	goTo('<?php echo $call_to_order_url;?>');
	return false;
});

<?php
//search block form function ?>
function search_submit () {
    var url = '<?php echo $search_url;?>';
	var filter_keyword = $('#filter_keyword').val();
	if (filter_keyword) {
	    url += '&keyword=' + encodeURIComponent(filter_keyword);
	}
	var filter_category_id = $('#filter_category_id').attr('value');
	if (filter_category_id) {
	    url += '&category_id=' + filter_category_id;
	}
	location = url;
	return false;
}
</script>