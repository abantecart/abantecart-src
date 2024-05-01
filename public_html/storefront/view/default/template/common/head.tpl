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
if ($google_analytics_code) { ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $google_analytics_code; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', <?php js_echo($google_analytics_code); ?>);
    </script>
    <?php
} ?>

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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.rtl.min.css" integrity="sha512-VNBisELNHh6+nfDjsFXDA6WgXEZm8cfTEcMtfOZdx0XTRoRbr/6Eqb2BjqxF4sNFzdvGIt+WqxKgn0DSfh2kcA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-grid.rtl.min.css" integrity="sha512-V7mESobi1wvYdh9ghD/BDbehOyEDUwB4c4IVp97uL0QSka0OXjBrFrQVAHii6PNt/Zc1LwX6ISWhgw1jbxQqGg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-reboot.rtl.min.css" integrity="sha512-3rUQrzowNf614w5xf4fiZKVHGYQdstwW8pyN3ib/eHeOC3k0kd+T9nVZA+Mmsx2yFayVdC0QYMaUVZVCnE0YXQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-utilities.rtl.min.css" integrity="sha512-zsjfQ5vXJw9hZA67bkzXV2lIQrSLhF5gC5TZT/BE6DdrCoUdtOhPrpD0v6p4nZhZOYZENusWWSgqDpl0F8SbfA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } else {?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-grid.min.css" integrity="sha512-i1b/nzkVo97VN5WbEtaPebBG8REvjWeqNclJ6AItj7msdVcaveKrlIIByDpvjk5nwHjXkIqGZscVxOrTb9tsMA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-reboot.min.css" integrity="sha512-HJaQ4y3YcUGCWikWDn8bFeGTy3Z/3IbxFYQ9G3UAWx16PyTL6Nu5P/BDDV9s0WhK3Sq27Wtbk/6IcwGmGSMXYg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap-utilities.min.css" integrity="sha512-4ocAKAxnrkSm7MvkkF1D435kko3/HWWvoi/U9+7+ln94B/U01Mggca05Pm3W59BIv3abl0U3MPdygAPLo5aeqg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } ?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"  crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="<?php echo $this->templateResource('/css/style.css'); ?>" rel="stylesheet" type='text/css' />

<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/css/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>    </script><script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js" integrity="sha512-VK2zcvntEufaimc+efOYi622VN5ZacdnufnmX7zIhCPmjhKnOi9ZDMtg1/ug5l183f19gG1/cBstPO4D8N/Img==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    let storeName = '<?php echo $this->config->get('config_title_'.$this->language->getLanguageID()); ?>';
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
<?php
if($cart_ajax){ ?>
    let cart_ajax_url = '<?php echo $cart_ajax_url; ?>';
<?php } ?>
    let ga4_enabled = <?php echo $this->config->get('config_google_analytics_code') ? 'true' : 'false'; ?>;
</script>
<script type="text/javascript" src="<?php
/** @see public_html/storefront/view/bootstrap5/js/main.js */
echo $this->templateResource('/js/main.js'); ?>"></script>
<?php
foreach ($scripts as $script) { ?>
    <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php
} ?>