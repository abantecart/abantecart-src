<?php echo $head; ?>
<div class="pay-form container">
    <div class="text-center order-success">
        <div class="order-success d-flex flex-wrap flex-column p-5">
            <h3 class="text-success mt-4"><?php echo $fast_checkout_text_order_is_completed; ?></h3>
            <div class="text-success py-5">
                <i class="fa fa-check fa-fw fa-5x"></i>
            </div>
            <br/>
            <?php
            //if we have custom message
            if ($order_finished_message) {
                echo '<h5 class="lh-lg">'.$order_finished_message.'<br>'
                    .$fast_checkout_text_thank_you.'</h5>';
            }
            //if guest wish to be registered - show message
            if ($text_account_created) {
                echo '<h5 class="lh-lg">'.$text_account_created.'</h5>';
            }
            ?>

            <?php
            //if we have download and it pending - show message
            if ($text_order_download_pending) {
                echo "<p>".$text_order_download_pending."</p><br/>";
            } ?>
            <div class="order_completed_buttons">
                <?php echo $this->getHookVar('order_completed_buttons'); ?>
                <?php
                //if we have download
                if ($download_url) { ?>
                    <a href="<?php echo $download_url; ?>" class="btn btn-primary btn-xl" target="_new">
                        <i class="fa fa-download fa-fw"></i>
                        <?php echo $fast_checkout_button_start_download; ?>
                    </a>
                <?php } else {
                    if ($order_details_url) { ?>
                        <a href="#" data-href="<?php echo $order_details_url; ?>"
                           class="btn btn-outline-secondary btn-xl reload_parent">
                            <i class="fa fa-download fa-fw"></i>
                            <?php echo $fast_checkout_button_start_download; ?>
                        </a>
                    <?php }
                } ?>
                <a href="#" data-href="<?php echo $button_order_details->href; ?>"
                   class="btn btn-info btn-xl reload_parent">
                    <i class="fa fa-archive fa-fw"></i>
                    <?php echo $fast_checkout_button_order_details; ?>
                </a>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(".reload_parent").on("click", function (e) {
        var url = $(this).attr("data-href");
        if (window.parentIFrame) {
            window.parentIFrame.sendMessage({reload: true, url: url});
        } else {
            location = url;
        }
        return false;
    });
    <?php
    if (trim($this->config->get('config_google_tag_manager_id'))) {
    //get ecommerce tracking data from checkout page
    /**
     * @see ControllerPagesCheckoutSuccess::_google_analytics()
     * @see ControllerResponsesCheckoutPay::_save_google_analytics()
     */
    $gaOrderData = $this->session->data['google_analytics_order_data'];
    unset($this->session->data['google_analytics_order_data']);
    if ($gaOrderData) { ?>
        dataLayer.push({ecommerce: null});
        dataLayer.push({
        event: "purchase",
        ecommerce: {
            transaction_id: <?php js_echo($gaOrderData['transaction_id']);?>,
            affiliation: <?php js_echo($gaOrderData['store_name']);?>,
            value: <?php js_echo($gaOrderData['total']); ?>,
            tax: <?php js_echo($gaOrderData['tax']); ?>,
            shipping: <?php js_echo($gaOrderData['shipping']); ?>,
            currency: <?php js_echo($gaOrderData['currency_code']); ?>,
            coupon: <?php js_echo($gaOrderData['coupon']); ?>,
            city: <?php js_echo($gaOrderData['city']); ?>,
            state: <?php js_echo($gaOrderData['state']);?>,
            country: <?php js_echo($gaOrderData['country']);?>
<?php if ($gaOrderData['items']) { ?>
,
            items: <?php js_echo($gaOrderData['items']); ?>
<?php } ?>
        }
    });
<?php }
} ?>
</script>
<?php echo $footer; ?>