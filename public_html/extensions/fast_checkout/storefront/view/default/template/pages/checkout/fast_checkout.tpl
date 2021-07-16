<div class="spinner-overlay">
    <div class="spinner"></div>
</div>
<div id="fast_checkout_cart"></div>

<script type="application/javascript">
    if ($('#fast_checkout_cart').html() === '') {
        $('.spinner-overlay').fadeIn(100);
    }
    <?php if ($cart_url) { ?>
    var loadPage = function () {
        $.ajax({
            url: '<?php echo $cart_url; ?>',
            type: 'GET',
            dataType: 'html',
            success: function (data) {
                $('#fast_checkout_summary_block').trigger('reload');
                $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
                $('.spinner-overlay').fadeOut(500);
            },
            error: function () {
                $('.spinner-overlay').fadeOut(500);
            }
        });
    };

    $(document).ready(loadPage);
    <?php }
    // set cart key into scratch data
    ?>
    $(document).ready( function(){
        if(!$('body').data('cart_key')){
            <?php
            if($single_checkout){
                //we use this key to open product page when cart-key changed (another simple-checkout process)
                ?>
                $('body').data('product_key', '<?php echo $product_key; ?>');
            <?php } ?>
            $('body').data('cart_key', '<?php echo $cart_key; ?>');
        }
        checkCartKey();
    });

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function checkCartKey(){
        if($('body').data('cart_key') && $('body').data('cart_key') !== readCookie('fc_cart_key') ){
            var pKey = $('body').data('product_key');
            if(pKey){
                location = '<?php echo $this->html->getSecureUrl('product/product')?>' + '&key=' + pKey;
            }
        }
    }
    <?php echo $this->getHookVar('fc_js_page'); ?>
</script>
