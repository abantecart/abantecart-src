<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/easyzoom.js'); ?>" defer></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.validate.js'); ?>" defer></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/custom.response.js'); ?>" defer></script>

<?php
if (trim($this->config->get('config_google_tag_manager_id'))) {
    //get ecommerce tracking data from checkout page
    /**
     * @see ControllerPagesCheckoutSuccess::_google_analytics()
     */
    $registry = Registry::getInstance();
    $ga_data = $registry->get('google_analytics_data');
    if ($ga_data) { ?>
        <script type="application/javascript">
            dataLayer.push({ecommerce: null});
            dataLayer.push({
                event: "purchase",
                ecommerce: {
                    transaction_id: <?php js_echo($ga_data['transaction_id']);?>,
                    affiliation: <?php js_echo($ga_data['store_name']);?>,
                    value: <?php js_echo($ga_data['total']); ?>,
                    tax: <?php js_echo($ga_data['tax']); ?>,
                    shipping: <?php js_echo($ga_data['shipping']); ?>,
                    currency: <?php js_echo($ga_data['currency_code']); ?>,
                    coupon: <?php js_echo($ga_data['coupon']); ?>,
                    city: <?php js_echo($ga_data['city']); ?>,
                    state: <?php js_echo($ga_data['state']);?>,
                    country: <?php js_echo($ga_data['country']);?>
                    <?php if ($ga_data['items']) { ?>
                    , items: <?php js_echo($ga_data['items']); ?>
                    <?php } ?>
                }
            });
        </script>
    <?php }
} ?>

<?php foreach ($scripts_bottom as $script) { ?>
	<script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php } ?>
</body>
</html>

