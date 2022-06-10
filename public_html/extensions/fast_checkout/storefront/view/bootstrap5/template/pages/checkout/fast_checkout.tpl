<?php
if ($error) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
} ?>
<div class="spinner-overlay">
    <div class="text-center h-100 d-flex align-items-center justify-content-center">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>
<div id="fast_checkout_cart"></div>

<script type="application/javascript">

document.addEventListener('DOMContentLoaded', function load() {
    //waiting for jquery loaded!
    if (!window.jQuery) return setTimeout(load, 50);
    //jQuery-depended code

<?php if ($cart_url) { ?>
    var loadPage = function () {
        $.ajax({
            url: '<?php echo $cart_url; ?>',
            type: 'GET',
            dataType: 'html',
            beforeSend: function(){
                $('.spinner-overlay').fadeIn(100);
            },
            success: function (data) {
                $('#fast_checkout_summary_block').trigger('reload');
                $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
                $('.spinner-overlay').fadeOut(500);
                $('form.needs-validation').each(
                    function(){
                        $(this).unbind('submit');
                    }
                );
            },
            error: function () {
                $('.spinner-overlay').fadeOut(500);
                scrollOnTop();
            }
        });
    };

    $(document).ready(loadPage);
<?php }
    // set cart key into scratch data
?>
    $(document).ready( function(){
        let body = $('body');
        if(!body.data('cart_key')){
            <?php
            if($single_checkout){
                //we use this key to open product page when cart-key changed (another simple-checkout process)
                ?>
                body.data('product_key', '<?php echo $product_key; ?>');
            <?php } ?>
            body.data('cart_key', '<?php echo $cart_key; ?>');
        }
        checkCartKey();

        <?php //run onload validation only for registered customers
        if($this->customer->isLogged()){ ?>
            $('form#PayFrm, form#AddressFrm, form#Address2Frm').each( function(e){
               validateForm($(this));
            });
        <?php } ?>
    });

    <?php echo $this->getHookVar('fc_js_page'); ?>

}, false);
</script>
