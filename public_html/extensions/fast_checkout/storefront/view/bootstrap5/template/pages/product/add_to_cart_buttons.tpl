<a id="product_add_to_cart" href="Javascript:void(0)" class="shadow cart btn btn-success btn-lg mx-2 mb-3">
    <i class="fa-solid fa-cart-plus fa-fw"></i>
    <?php echo $button_add_to_cart; ?>
</a>
<a id="product_buy_now_btn" href="#"
   onclick="$(this).closest('form').attr('action', '<?php echo $buynow_url;?>').submit(); return false;"
   class="shadow cart btn btn-success btn-lg mx-2 mb-3">
    <i class="fa-solid fa-credit-card fa-fw"></i>
    <?php echo $text_buynow; ?>
</a>
