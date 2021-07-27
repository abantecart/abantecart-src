<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $params['language']; ?>"
      xml:lang="<?php echo $params['language']; ?>">
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php $options = [
    'image'          => '<span class="abantecart_image"></span>',
    'name'           => '<h3 class="abantecart_name"></h3>',
    'products_count' => '<p class="abantecart_products_count"></p>',
];
$html = '';
$common_params = '';
foreach ($params as $param => $v) {
    if (isset($options[$param])) {
        $html .= $options[$param]."\n";
    } else {
        $common_params .= '&'.$param.'='.$v;
    }
} ?>

<script src="<?php echo $sf_js_embed_url.$common_params; ?>" type="text/javascript"></script>
<ul style="display:none;"
    class="abantecart-widget-container"
    data-url="<?php echo $sf_base_url; ?>"
    data-css-url="<?php echo $sf_css_embed_url; ?>"
    data-language="<?php echo $params['language'] ?>"
    data-currency="<?php echo $params['currency'] ?>">
    <?php foreach ($params['manufacturer_id'] as $id) { ?>
        <li id="abc_man_<?php echo $id; ?>"
            class="abantecart_manufacturer"
            data-manufacturer-id="<?php echo $id; ?>"
            data-language="<?php echo $params['language']; ?>"
            data-currency="<?php echo $params['currency']; ?>">
            <?php echo $html; ?>
        </li>
    <?php } ?>
</ul>

</body>
</html>