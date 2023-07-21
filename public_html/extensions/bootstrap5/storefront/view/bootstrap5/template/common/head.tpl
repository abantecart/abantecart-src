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
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css" integrity="sha512-ltIFivbYEeV9dNzcYLxBKC2hPQ0l9K2/Ws8R5GsMkxANKtMigmsjzTUUej7iH5NwGNnD070lrycDq5OJlDyb1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-grid.rtl.min.css" integrity="sha512-3oruZFd8e/wrfr2pTP6LpO4lR0exB870UcnrVa0u3TTqbQ5ULfSsv25uG4NdN5mOgES3zvEzuLQq4EqaX8yVqA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-reboot.rtl.min.css" integrity="sha512-oOQddPMv4zGW7uB4CCwYq6inlgc5ur0QpM63U80cqrYMJXRWdVe4+vikoUqJQ9csSguOSUd2SUvXxJ6KIzhnjQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-utilities.rtl.min.css" integrity="sha512-Jx83h6vz654R02peFNTa/9Xeqy//qpF6meM5bnhXD9uD9aMIV8JqYBumRIeAUnx5gQSojnN+FrvZoyvfEmu8OA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } else {?>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" integrity="sha512-t4GWSVZO1eC8BM339Xd7Uphw5s17a86tIZIj8qRxhnKub6WoyhnrxeCIMeAqBPgdZGlCcG2PrZjMc+Wr78+5Xg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-grid.min.css" integrity="sha512-EAgFb1TGFSRh1CCsDotrqJMqB2D+FLCOXAJTE16Ajphi73gQmfJS/LNl6AsjDqDht6Ls7Qr1KWsrJxyttEkxIA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-reboot.min.css" integrity="sha512-allly0sW31f5fb2FdiSuezu/pLSoV8hN0liPTS5PAeB39hxh5S6yDf6ak7ge77JkfnOkiFNfjdXDyx6sEzz08A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap-utilities.min.css" integrity="sha512-K4XWKeYNHW67orY92NwVkgHAShgq/TowE5Sx9O4imSO1YM3ll+6pLLwcSJvr3IwDIWCnSDhkuxxqJEbY8+iGzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php } ?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="<?php echo $this->templateResource('/css/style.css'); ?>" rel="stylesheet" type='text/css' />

<?php if ( $template_debug_mode ) {  ?>
<link href="<?php echo $this->templateResource('/css/template_debug.css'); ?>" rel="stylesheet" />
<?php } ?>

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js" integrity="sha512-VK2zcvntEufaimc+efOYi622VN5ZacdnufnmX7zIhCPmjhKnOi9ZDMtg1/ug5l183f19gG1/cBstPO4D8N/Img==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
/** @see public_html/extensions/bootstrap5/storefront/view/bootstrap5/js/main.js */
echo $this->templateResource('/js/main.js'); ?>"></script>
<?php
foreach ($scripts as $script) { ?>
    <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php
} ?>