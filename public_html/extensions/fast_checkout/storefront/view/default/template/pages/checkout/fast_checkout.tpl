<div class="spinner-overlay">
    <div class="spinner"></div>
</div>
<div id="fast_checkout_cart"></div>

<script type="application/javascript">
    if ($('#fast_checkout_cart').html() === '') {
        $('.spinner-overlay').fadeIn(100);
    }
    <?php if ($cart_url) { ?>
    let loadPage = function(cart_key) {
        $.ajax({
            url: '<?php echo $cart_url; ?>' + '&cart_key=' + (cart_key || ''),
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
    $(document).ready( loadPage );
    <?php } ?>


</script>
