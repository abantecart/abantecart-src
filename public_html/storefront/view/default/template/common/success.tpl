<h1 class="heading1">
  <span class="maintext"><i class="fa fa-thumbs-up"></i> <?php echo $heading_title; ?></span>
  <span class="subtext"></span>
</h1>

<div class="contentpanel">

<section class="mb40">
<h4 class="hidden">&nbsp;</h4>
	<p><?php echo $text_message; ?></p>
	
	<a href="<?php echo $continue; ?>" class="btn btn-default mr10" title="<?php echo $continue_button->text ?>">
	    <i class="fa fa-arrow-right"></i>
	    <?php echo $continue_button->text ?>
	</a>
</section>

</div>

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
        ,           coupon: <?php js_echo($gaOrderData['coupon']); ?>
        <?php }
        if ($gaOrderData['items']) { ?>
        ,
        items: <?php js_echo($gaOrderData['items']); ?>
        <?php } ?>
    };
    gtag("event", "purchase", ga_ecommerce );
<?php }
} ?>
</script>