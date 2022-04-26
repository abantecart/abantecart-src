<?php /** @var Aview $this */ ?>
<meta charset="UTF-8">
<!--[if IE]>
	<meta http-equiv="x-ua-compatible" content="IE=Edge" />
<![endif]-->
<title><?php echo $title; ?></title>
<?php
foreach($meta as $item){
    if(!$item['content']){ continue;} ?>
<meta <?php foreach($item as $n=>$v){ echo $n.'="'.$v.'" '; }?>/>
<?php } ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<base href="<?php echo $base; ?>" />
<?php
if ($google_tag_manager) { ?>
<!-- Google Tag Manager -->
<script type="application/javascript">
    (
        function(w,d,s,l,i){
            w[l]=w[l]||[];
            w[l].push(
                {
                    'gtm.start': new Date().getTime(),
                    event:'gtm.js'
                }
            );
            let f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l !== 'dataLayer' ? '&l=' + l : '';
            j.async=true;
            j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;
            f.parentNode.insertBefore(j,f);
        }
    )(window,document,'script','dataLayer','<?php echo trim($google_tag_manager); ?>');
</script>
<!-- End Google Tag Manager -->
<?php } ?>

<?php if ( is_file( DIR_RESOURCE . $icon ) ) {  ?>
<link href="resources/<?php echo $icon; ?>" type="<?php echo mime_content_type(DIR_RESOURCE . $icon)?>" rel="icon" />
<?php } ?>

<link href="<?php echo $this->templateResource('/image/apple-touch-icon.png');?>" rel="apple-touch-icon" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-76x76.png');?>" rel="apple-touch-icon" sizes="76x76" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-120x120.png');?>" rel="apple-touch-icon" sizes="120x120" />
<link href="<?php echo $this->templateResource('/image/apple-touch-icon-152x152.png');?>" rel="apple-touch-icon" sizes="152x152" />
<link href="<?php echo $this->templateResource('/image/icon-192x192.png');?>" rel="apple-touch-icon" sizes="192x192" />

<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

<link href="<?php echo $this->templateResource('/css/bootstrap.min.css'); ?>" rel="stylesheet" type='text/css' />
<link href="<?php echo $this->templateResource('/fontawesome/css/all.min.css'); ?>" rel="stylesheet" type='text/css' />
<link href="<?php echo $this->templateResource('/css/style.css'); ?>" rel="stylesheet" type='text/css' />



<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/css/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script type="text/javascript" src="<?php echo $this->templateResource('/js/jquery-3.6.0.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/js/bootstrap.bundle.min.js'); ?>"></script>
<script type="text/javascript">
    let baseUrl = '<?php echo $base; ?>';
    let samesite = '<?php echo((defined('HTTPS') && HTTPS) ? 'None; secure=1;' : 'lax; secure=0;'); ?>';
    let is_retina = <?php echo $retina ? 'true' : 'false'; ?>;
    let cart_url = '<?php echo $cart_url; ?>';
    let call_to_order_url = '<?php echo $call_to_order_url;?>';
    let search_url = '<?php echo $search_url;?>';
    let text_add_cart_confirm = <?php js_echo($text_add_cart_confirm); ?>;

    <?php
    if($cart_ajax){ ?>
    let cart_ajax_url = '<?php echo $cart_ajax_url; ?>';
    <?php } ?>
</script>
<script type="text/javascript" src="<?php echo $this->templateResource('/js/custom.js'); ?>"></script>
<?php
foreach ($scripts as $script) { ?>
    <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php
} ?>

