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
<?php }

if($direction == 'rtl'){ ?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.rtl.min.css" integrity="sha512-LbSN0zUc47h+h7KpimHiKIjJWcf2lLP5+mtzJFXcklWfITRR1pv4+qWFkHt5qIAdy8RrX7ejRym0JxtKj6I45w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-grid.rtl.min.css" integrity="sha512-Fi60uaCdw/Je3IZVAMnSWxiUutPyzIrcYUpJr9Wsfys2QchkrnryEn1fUvoBXV78IjXaTfoe54rnwmOEb2sv0Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-reboot.rtl.min.css" integrity="sha512-zfWnyY3rdZPagSgccMN9/Z27ifU7ES8kz7nvZFWzOeooCR++dAe0a7DhNMroEK7dy9R29Xe4UTRnoepEXruenw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-utilities.rtl.min.css" integrity="sha512-PpaWZ+OBKIdjDPke7B1Zm41JCYTN8wJGGyrWqKXyIRNnG4LWqWPRGuI6hzd8ua7+Y3D2mev0aJrQctg56hPMtg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } else {?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" integrity="sha512-GQGU0fMMi238uA+a/bdWJfpUGKUkBdgfFdgBm72SUQ6BeyWjoY/ton0tEjH+OSH9iP4Dfh+7HM0I9f5eR0L/4w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-grid.min.css" integrity="sha512-q0LpKnEKG/pAf1qi1SAyX0lCNnrlJDjAvsyaygu07x8OF4CEOpQhBnYiFW6YDUnOOcyAEiEYlV4S9vEc6akTEw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-reboot.min.css" integrity="sha512-stCMej6DXfErBwD8bQrJsG2dFPHNW9y8cmccQhW0o+GeSxKzjABmIVdwOzQ5Vlw4HYoEwrECGDRQs8xF0f9ssg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap-utilities.min.css" integrity="sha512-kRvJclW5GazdbAJPE9vvs560XNBwTBGLg4RVHZALXHFnAWQeRbQ/jGcGDHcQXiiqRld4wEFs6Kpoa6Tm/wCrtg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } ?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="<?php echo $this->templateResource('/css/style.css'); ?>" rel="stylesheet" type='text/css' />

<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/css/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js" integrity="sha512-pax4MlgXjHEPfCwcJLQhigY7+N8rt6bVvWLFyUMuxShv170X53TRzGPmPkZmGBhk+jikR8WBM4yl7A9WMHHqvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
<script type="text/javascript" src="<?php echo $this->templateResource('/js/main.js'); ?>"></script>
<?php
foreach ($scripts as $script) { ?>
    <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php
} ?>
