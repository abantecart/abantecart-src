<link href="<?php echo $this->templateResource('/css/pay.css'); ?>" rel="stylesheet" type='text/css'/>
<div id="fast_checkout_success"></div>
<script type="application/javascript">
    <?php
    if (trim($this->config->get('config_google_analytics_code'))) {
    //get ecommerce tracking data from checkout page
    /**
     * @see AOrder::getGoogleAnalyticsOrderData()
     */

if ($gaOrderData) { ?>
    let ga_ecommerce = {
        transaction_id: <?php js_echo($gaOrderData['transaction_id']);?>,
        affiliation: <?php js_echo($gaOrderData['store_name']);?>,
        value: <?php js_echo($gaOrderData['total']); ?>,
        tax: <?php js_echo($gaOrderData['tax']); ?>,
        shipping: <?php js_echo($gaOrderData['shipping']); ?>,
        currency: <?php js_echo($gaOrderData['currency_code']); ?>,
        city: <?php js_echo($gaOrderData['city']); ?>,
        state: <?php js_echo($gaOrderData['state']);?>,
        country: <?php js_echo($gaOrderData['country']);?>
<?php
        if($gaOrderData['coupon']){ ?>
,
        coupon: <?php js_echo($gaOrderData['coupon']); ?>
<?php }
        if ($gaOrderData['items']) { ?>
,
        items: <?php js_echo($gaOrderData['items']); ?>
        <?php } ?>
    };
    gtag("event", "purchase", ga_ecommerce );
<?php }
} ?>

    document.addEventListener('DOMContentLoaded', function load() {
        //waiting for jquery loaded!
        if (!window.jQuery) return setTimeout(load, 50);
        //jQuery-depended code
        <?php if ($success_url) { ?>
        let loadPage = function () {
            $.ajax({
                url: '<?php echo $success_url; ?>',
                type: 'POST',
                dataType: 'html',
                beforeSend: function () {
                    $('.spinner-overlay').fadeIn(100);
                },
                success: function (data) {
                    $('#fast_checkout_success').hide().html(data).fadeIn(1000)
                    $('.spinner-overlay').fadeOut(500);
                }
            });
        }
        <?php } ?>

        $(document).ready(() => {
            $('body').append(
                '<div class="spinner-overlay"><div class="text-center">'
                    +'<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div><div>'
            );
            loadPage();
        });
    }, false);
</script>
