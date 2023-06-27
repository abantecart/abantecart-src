<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'home'; ?>">
<?php echo $this->getHookVar('top_page'); ?>

<div class="container-fixed" style="max-width: <?php echo $layout_width; ?>">

<?php if($maintenance_warning){ ?>
	<div class="alert alert-warning alert-dismissible mb-0">
        <i class="fa-solid fa-circle-exclamation fa-xl me-2"></i>
        <b><?php echo $maintenance_warning;?></b>
        <?php if($act_on_behalf_warning){ ?>
           <b><?php echo $act_on_behalf_warning;?></b>
        <?php } ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
 	</div>
<?php
}
if($act_on_behalf_warning && !$maintenance_warning){ ?>
	<div class="alert alert-warning alert-dismissible mb-0">
        <i class="fa-solid fa-circle-exclamation me-2"></i>
        <b><?php echo $act_on_behalf_warning;?></b>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php }
echo $$header; ?>

<?php if ( !empty( $$header_bottom ) ) { ?>
<!-- header_bottom blocks placeholder -->
	<div class="container-fluid">
	    <?php echo $$header_bottom; ?>
	</div>
<!-- header_bottom blocks placeholder -->
<?php } ?>

<div id="maincontainer" class="mb-3">

<?php
    //check layout dynamically
    $present_columns = 0;
    $center_padding = '';
    if (!empty($$column_left)) {
        $present_columns++;
        $center_padding .= ' ms-1 ';
    }
    if (!empty($$column_right)) {
        $present_columns++;
        $center_padding .= ' me-1 ';
    }
?>

        <div class="d-flex flex-wrap align-items-stretch align-self-stretch justify-content-center">
		<?php if ( !empty($$column_left ) ) { ?>
		<div class="ms-3 col-12 col-sm-9 col-md-8 col-lg-3 col-xl-3">
		<?php echo $$column_left; ?>
		</div>
		<?php } ?>

		<?php $span = 12 - 6 * $present_columns; ?>
            <div class="flex-grow-1 col-12 col-lg-<?php echo $span ?>  mt-2">
            <?php if ( !empty( $$content_top ) ) { ?>
            <!-- content top blocks placeholder -->
            <?php echo $$content_top; ?>
            <!-- content top blocks placeholder (EOF) -->
            <?php } ?>

            <div class="container-fluid">
            <?php echo $content; ?>
            </div>

            <?php if ( !empty( $$content_bottom ) ) { ?>
            <!-- content bottom blocks placeholder -->
            <?php echo $$content_bottom; ?>
            <!-- content bottom blocks placeholder (EOF) -->
            <?php } ?>
		</div>

		<?php if ( !empty($$column_right ) ) { ?>
            <div class="me-3 col-11 col-sm-9 col-md-8 col-lg-3 col-xl-3">
		<?php echo $$column_right; ?>
		</div>
		<?php } ?>
	</div>

</div>

<?php if ( !empty( $$footer_top ) ) { ?>
<!-- footer top blocks placeholder -->
	<div class="d-flex w-100 justify-content-evenly flex-wrap px-0 mx-0 border">
		<div class="col-md-12">
	    <?php echo $$footer_top; ?>
	  	</div>
	</div>
<!-- footer top blocks placeholder -->
<?php } ?>

<!-- footer blocks placeholder -->
<div id="footer">
	<?php echo $$footer; ?>
</div>

</div>
<a id="gotop" class="fs-6" href="#" title="<?php echo_html2view($this->language->get('text_back_on_top'));?>"><i class="fa-solid fa-circle-chevron-up fa-3x"></i></a>
<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
Please donate http://www.abantecart.com/donate
//-->
<?php
if($scripts_bottom && is_array($scripts_bottom)) {
	foreach ($scripts_bottom as $script){ ?>
        <script type="text/javascript" src="<?php echo $script; ?>" defer></script>
    <?php }
}

if (trim($this->config->get('config_google_analytics_code'))) {
    //get ecommerce tracking data from checkout page
    /**
     * @see AOrder::getGoogleAnalyticsOrderData()
     */
    $gaOrderData = $this->session->data['google_analytics_order_data'];
    unset($this->session->data['google_analytics_order_data']);
    if ($gaOrderData) { ?>
<script type="application/javascript">
    gtag("event", "purchase",
        {
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
            , items: <?php js_echo($gaOrderData['items']); ?>
            <?php } ?>
        }
    );
</script>
<?php }
} ?>
</body></html>