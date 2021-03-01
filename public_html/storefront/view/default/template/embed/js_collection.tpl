(function(){
var html = '';

$('#<?php echo $target; ?>.abantecart_collection').append(
    '<style>ul.abantecart_products li {float: none;} '
    + ' .priceold { text-decoration: line-through; }</style><ul class="abantecart_products"></ul>'
);
<?php
foreach ((array) $products as $product) { ?>
    if($('#<?php echo $target; ?> .abantecart_collection ul')) {
        html = '<li>';
<?php
if($product['thumb']){ ?>
        html += '<a data-href="<?php echo $product['product_details_url']; ?>" data-id="<?php echo $product['product_id']; ?>" '
                + 'data-html="true" data-target="#abc_embed_modal" data-backdrop="static" data-keyboard="false" '
                + 'data-toggle="abcmodal" href="#" class="product_thumb" data-original-title=""> '
                + <?php js_echo($product['thumb']['thumb_html']); ?>
                + '</a>';
<?php
}
if($product['name']){ ?>
        html += '<div><h3 class="abantecart_product_name">'
                + '<a data-href="<?php echo $product['product_details_url']; ?>" '
                + ' data-id="<?php echo $product['product_id']; ?>"'
                + ' data-backdrop="static" data-keyboard="false" data-html="true"'
                + ' data-target="#abc_embed_modal" data-toggle="abcmodal" href="#"'
                + ' class="collection_thumb">'
                + <?php js_echo($product['name']); ?>
                +'</a></h3></div>';
<?php
}
if ($product['price'] && $display_price) {
        if ($product['special']) { ?>
            html += '<div class="priceold">' + <?php js_echo($product['price']) ?> + '</div>'
                + '<div class="pricenew">' + <?php js_echo($product['special']) ?> + '</div>';
<?php
       } else { ?>
            html += '<div class="oneprice">' + <?php js_echo($product['price']) ?> + '</div>'
<?php  }
}
if ($product['rating']) { ?>
            html += '<div class="rating">'
                + '<img src="<?php echo AUTO_SERVER.$this->templateResource('/image/stars_'.(int) $product['rating'].'.png'); ?>" '
                + 'alt="<?php echo $product['stars']; ?>"/></div>';
<?php } ?>
            html += '</li>';
            $('#<?php echo $target; ?>.abantecart_collection ul').append(html);
    }
<?php } ?>
})();
