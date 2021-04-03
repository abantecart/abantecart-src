<?php // default addToCart button (for default checkout process) ?>
<a href="#"
   onclick="$(this).closest('form').submit(); return false;"
   class="cart">
    <i class="fa fa-cart-plus fa-fw"></i>
    <?php echo $button_add_to_cart; ?>
</a>
<a href="#"
   onclick="$(this).closest('form').attr('action', '<?php echo $buynow_url;?>').submit(); return false;"
   class="cart">
    <i class="fa fa-credit-card fa-fw"></i>
    <?php echo $text_buynow; ?>
</a>
