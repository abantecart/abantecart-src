<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"
      dir="<?php echo $direction; ?>"
      lang="<?php echo $params['language']; ?>"
      xml:lang="<?php echo $params['language']; ?>">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<?php $options = [
    'image'     => '<div class="abantecart_image"></div>',
    'name'      => '<h3 class="abantecart_name"></h3>',
    'price'     => '<div class="abantecart_price"></div>',
    'limit'     => '<input class="abantecart_limit" type="hidden" name="limit" value="'.$params['limit'].'">',
];
$html = '';
$common_params = '';
foreach ($params as $param => $v) {
    if (isset($options[$param])) {
        $html .= $options[$param]."\n";
    }else{
        $common_params .= '&'.$param.'='.$v;
    }
}
?>

<script src="<?php echo $sf_js_embed_url.$common_params; ?>" type="text/javascript"></script>
<ul style="display:none;"
    class="abantecart-widget-container"
    data-url="<?php echo $sf_base_url; ?>"
    data-css-url="<?php echo $sf_css_embed_url; ?>"
    data-language="<?php echo $params['language'] ?>"
    data-currency="<?php echo $params['currency'] ?>">
    <li id="abc_<?php echo time() * 1000; ?>"
        class="abantecart_collection"
        data-collection-id="<?php echo $params['collection_id']; ?>"
        data-language="<?php echo $params['language'] ?>"
        data-currency="<?php echo $params['currency'] ?>">
        <?php echo $html; ?>
    </li>
</ul>
</body>
</html>