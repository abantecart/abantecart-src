<?php
echo $head; ?>
    <link href="<?php
    echo $this->templateResource('/css/bootstrap-xxs.css'); ?>" rel="stylesheet" type='text/css'/>
    <link href="<?php
    echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>


    <script type="text/javascript"
            src="<?php
            echo $this->templateResource('/js/credit_card_validation.js'); ?>"></script>
    <script type="text/javascript"
            src="<?php
            echo $this->templateResource('/javascript/common.js'); ?>"></script>

    <div id="fast_checkout_cart"></div>

<script type="text/javascript">
    if ($('#fast_checkout_cart').html() === '') {
        $('.spinner-overlay').fadeIn(100);
    }
    <?php if ($cart_url) { ?>
    let loadPage = function () {
        $.ajax({
            url: '<?php echo $cart_url; ?>',
            type: 'GET',
            dataType: 'html',
            success: function (data) {
                $('.spinner-overlay').fadeOut(500);
                $('#fast_checkout_summary_block').trigger('reload');
                $('#fast_checkout_cart').hide().html(data).fadeIn(1000);
            },
            error: function () {
                $('.spinner-overlay').fadeOut(500);
            }
        });
    };
    $(document).ready(loadPage());
    <?php } ?>
</script>
<?php
echo $footer; ?>