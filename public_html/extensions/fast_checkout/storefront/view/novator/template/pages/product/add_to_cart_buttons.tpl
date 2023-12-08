<a id="product_add_to_cart" href="Javascript:void(0)" class="btn btn-outline-primary">
    <i class="bi bi-handbag"></i>
    <?php echo $button_add_to_cart; ?>
</a>
<a id="product_buy_now_btn" href="#" onclick="$(this).closest('form').attr('action', '<?php echo $buynow_url;?>').submit(); return false;"
    class="btn btn-primary">
    <i class="bi bi-handbag"></i>
    <?php echo $text_buynow; ?>
</a>
