<?php //populate product div on client side ?>
(function(){
    var html = '';
    if($('#<?php echo $target; ?> .abantecart_name')){
    $('#<?php echo $target; ?> .abantecart_name').html(<?php js_echo($product['name']) ?>);
}

if($('#<?php echo $target; ?> .abantecart_image')){
    html = '<a data-href="<?php echo $product_details_url; ?>" data-id="<?php echo $product['product_id']; ?>" '
        + ' data-html="true" data-target="#abc_embed_modal" data-backdrop="static" data-keyboard="false" '
        + ' data-toggle="abcmodal" href="#" class="product_thumb" data-original-title=""> '
        + <?php js_echo(nl2br($product['thumbnail']['thumb_html'])); ?>
        +'</a>';
    $('#<?php echo $target; ?> .abantecart_image').html(html);
}

<?php
if ($product['price'] && $display_price) { ?>
    html ='';
    if($('<?php echo $target; ?> .abantecart_price')){
<?php
    if ($product['special']) { ?>
        html = '<div class="priceold">' + <?php js_echo($product['price']) ?> +'</div>'
            + '<div class="pricenew">' + <?php js_echo($product['special']) ?> +'</div>';
<?php
    } else { ?>
        html = '<div class="oneprice">' + <?php js_echo($product['price']); ?> +'</div>';
<?php
    } ?>
        $('#<?php echo $target; ?> .abantecart_price').html(html);
    }
    <?php
}

if($product['rating']) { ?>
    html ='';
    if($('<?php echo $target; ?> .abantecart_rating')){
        html = '<?php echo '<img src="'.AUTO_SERVER
            .$this->templateResource('/image/stars_'.(int) $product['rating'].'.png')
            .'" alt="'.$product['stars'].'" />' ?>';
        $('#<?php echo $target; ?> .abantecart_rating').html(html);
    }
    <?php
}

if($product['quantity'] && !($product['track_stock'] && !$product['in_stock']) && !$product['call_to_order']) { ?>
    html ='';
    if($('<?php echo $target; ?> .abantecart_quantity')){
        html = '<span class="abantecart_quantity_text"><?php js_echo($text_qty); ?></span>'
            + '&nbsp;<input type="text" size="3" class="abantecart_quantity_field" '
            + 'placeholder="<?php js_echo($text_qty); ?>" value="<?php echo $product['quantity']->value ?>" '
            + 'id="product_quantity" name="' +<?php js_echo($product['quantity']->name) ?> +'"></div>';
        $('#<?php echo $target; ?> .abantecart_quantity').html(html);
    }
<?php }

if($product['button_addtocart']){ ?>
    html ='';
    if($('<?php echo $target;?> .abantecart_addtocart')){
        <?php if($product['call_to_order'] || ($product['track_stock'] && !$product['in_stock']) ) { ?>
        html ='';
        <?php }else{ ?>
        html ='<button <?php echo $product['button_addtocart']->attr; ?> '
            + 'title="' + <?php js_echo($product['button_addtocart']->text); ?> + '" '
            + 'class="abantecart_button" id="<?php echo $product['button_addtocart']->id; ?>" '
            + 'type="button">' + <?php js_echo($product['button_addtocart']->text); ?> + '</button>';
    <?php } ?>
        $('#<?php echo $target;?> .abantecart_addtocart').html(html);
    }
<?php }

if($product['blurb']){?>
    if($('#<?php echo $target;?> .abantecart_blurb')){
        $('#<?php echo $target;?> .abantecart_blurb').html( <?php js_echo($product['blurb'])?> );
    }
<?php }
echo $this->getHookVar('embed_product_js');
?>
})();
