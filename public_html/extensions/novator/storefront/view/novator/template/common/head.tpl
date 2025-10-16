<?php /** @var AView|AController $this */ ?>
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
if ($google_analytics_code) { ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $google_analytics_code; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        <?php echo  $this->getHookVar('ga_pre'); ?>
        gtag('js', new Date());
        gtag('config', <?php js_echo($google_analytics_code); ?>, {cookie_flags: 'SameSite=None;Secure'});
        <?php echo  $this->getHookVar('ga_post'); ?>
    </script>
    <?php
} ?>

<?php if ( is_file( DIR_RESOURCE . $icon ) ) {  ?>
<link href="resources/<?php echo $icon; ?>" type="<?php echo getMimeType(DIR_RESOURCE . $icon)?>" rel="icon" />
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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap.rtl.min.css" integrity="sha512-yZC61UaBAUGIbs/YDvp3XLZsh29taG1brQrFFJiCPHv+BoA6oETW0Zuc1xDKYHW/U3yZr/rqoiUZZG+iVOmB7w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap-grid.rtl.min.css" integrity="sha512-Rd2hI4hFIAuPtRF85GI8g2c2nu36l8yoE+wxe1lAeesMBG6qs4AxchEUigbJtakdlENmpI57ORsQL+Wb4sVmJA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap-reboot.rtl.min.css" integrity="sha512-7ymq9gDDGrC9TLgRhNtTz0EGxY5fPCDYCeo0NezM87/v6Aq8/zoGZFeo5Ty2jvnJbH+a0zW7IP2xua6WWecRPA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap-utilities.rtl.min.css" integrity="sha512-L/9ut+tWYwGahKxHv85Mcbo2QjLkWJRNVIQF4PlMTpWIXuhfG3gLvyq+H82fn5MtL5vNPp+4HRvdnSgdjc8omg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } else {?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap.min.css" integrity="sha512-fw7f+TcMjTb7bpbLJZlP8g2Y4XcCyFZW8uy8HsRZsH/SwbMw0plKHFHr99DN3l04VsYNwvzicUX/6qurvIxbxw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap-grid.min.css" integrity="sha512-79vX0oXpL1ee3k+V7jJxmmT+xdb7UrE7Fce5RYu3/l1oO/EWaMGEjDDObLXe2JSrDZtoRntVv0Iolv6i4TDWKw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap-reboot.min.css" integrity="sha512-jk0jBZf+2M/6V/Nql7QBoEB3bl+J9apM4VxB+UFTYTgxlO8Wxzb6nroBv+cXyXRjTHEY/HUZUynWqz1aY1/upQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/css/bootstrap-utilities.min.css" integrity="sha512-ooC60t939JQlDgMDZ4CoLIVrNvUu1XeA4p2o0sT1apgD+75ZAhuO0eMugE6nHUhr0MEz8UOXMvNGSvdVMsk0Kg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } ?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="//cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/js/all.min.js" integrity="sha512-gBYquPLlR76UWqCwD06/xwal4so02RjIR0oyG1TIhSGwmBTRrIkQbaPehPF8iwuY9jFikDHMGEelt0DtY7jtvQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css" integrity="sha512-t7Few9xlddEmgd3oKZQahkNI4dS6l80+eGEzFQiqtyVYdvcSG2D3Iub77R20BdotfRPA9caaRkg1tyaJiPmO0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="<?php echo $this->templateResource('/css/plugins/owl.carousel.min.css'); ?>" rel="stylesheet" type='text/css' />
<link href="<?php echo $this->templateResource('/css/tm_style.css'); ?>" rel="stylesheet" type='text/css' />

<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/css/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.7/js/bootstrap.bundle.min.js" integrity="sha512-Tc0i+vRogmX4NN7tuLbQfBxa8JkfUSAxSFVzmU31nVdHyiHElPPy2cWfFacmCJKw0VqovrzKhdd2TSTMdAxp2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="<?php echo $this->templateResource('/js/plugins/owl.carousel.js'); ?>" ></script>
<script defer src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-hover-dropdown/2.2.1/bootstrap-hover-dropdown.min.js"></script>
<script type="text/javascript">
    let storeName = <?php js_echo($this->config->get('config_title_'.$this->language->getLanguageID())); ?>;
    let baseUrl = '<?php echo $base; ?>';
    let samesite = '<?php echo((defined('HTTPS') && HTTPS) ? 'None; secure=1;' : 'lax; secure=0;'); ?>';
    let is_retina = <?php echo $retina ? 'true' : 'false'; ?>;
    let currency = '<?php echo $this->request->cookie['currency']; ?>';
    let default_currency = '<?php echo $this->config->get('config_currency'); ?>';
    let language = '<?php echo $this->request->cookie['language']; ?>';
    let cart_url = '<?php echo $cart_url; ?>';
    let call_to_order_url = '<?php echo $call_to_order_url;?>';
    let search_url = '<?php echo $search_url;?>';
    let text_add_cart_confirm = <?php js_echo($text_add_cart_confirm); ?>;
    let wishlist_add_url = '<?php echo $this->html->getSecureURL('product/wishlist/add'); ?>';
    let wishlist_remove_url = '<?php echo $this->html->getSecureURL('product/wishlist/remove'); ?>';
<?php
if($cart_ajax){ ?>
    let cart_ajax_url = '<?php echo $cart_ajax_url; ?>';
<?php } ?>
    let ga4_enabled = <?php echo $this->config->get('config_google_analytics_code') ? 'true' : 'false'; ?>;
    <?php
    //if you need to add some js variable from hook use $that->view->addHookVar() method;
    echo $this->getHookVar('head_js'); ?>
</script>
<script type="text/javascript" src="<?php
/** @see public_html/extensions/novator/storefront/view/novator/js/main.js */
echo $this->templateResource('/js/main.js'); ?>"></script>
<?php
foreach ($scripts as $script) { ?>
    <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php
} ?>