<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute');
echo $_COOKIE['data-bs-theme'] == 'dark' ? 'data-bs-theme="dark"' : ''; ?>>
<head><?php	echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'index-home'; ?>">
<?php echo $this->getHookVar('top_page'); ?>
<header>
	<div class="nav-wrapper">
	<?php echo ${$header}; ?>
		<!-- header-bottom-section Starts -->
		<div class="header-bottom-section">
			<?php if ( !empty( $$header_bottom ) ) { ?>
				<?php echo $$header_bottom; ?>
			<?php } ?>
		</div>
		<!-- header-bottom-section Ends -->
	</div>
</header>
<!-- header section Ends -->
<div id="maincontainer" class="top-section mt-4 mb-3">
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
	<div class="container">
	    <div class="row align-items-stretch align-self-stretch justify-content-center">
            <?php if ( !empty($$column_left ) ) { ?>
                <!-- column_left-section Starts -->
                    <div class="column_left-section col-12 col-xl-3">
                        <?php echo $$column_left; ?>
                    </div>
                <!-- column_left-section Ends (EOF) -->
            <?php }
            $span = 12 - 3 * $present_columns; ?>
            <div class="page-main-content flex-grow-1 col-<?php echo $span ?> col-lg-<?php echo $span ?>">
                <?php if ( !empty( $$content_top ) ) { ?>
                    <!-- content-top-section Starts -->
                    <?php echo $$content_top; ?>
                    <!-- content-top-section Ends (EOF) -->
                <?php } ?>
                <!-- content-main-section Starts -->
                <div class="content-main-section">
                    <?php echo $content; ?>
                </div>
                <!-- content-main-section Ends (EOF) -->
                <?php if ( !empty( $$content_bottom ) ) { ?>
                    <!-- content-bottom-section Starts -->
                    <div class="content-bottom-section">
                        <?php echo $$content_bottom; ?>
                    </div>
                    <!-- content-bottom-section Ends (EOF) -->
                <?php } ?>
            </div>
            <?php if ( !empty($$column_right ) ) { ?>
                <!-- column_right-section Starts -->
                <div class="column_right-section col-12 col-lg-3">
                    <?php echo $$column_right; ?>
                </div>
                <!-- column_right-section Ends (EOF) -->
            <?php } ?>
    	</div>
	</div>
</div>
<?php if ( $$footer_top || $$footer) { ?>
<div class="container-fluid">
	<!-- footer top section Starts -->
	<?php if ( $$footer_top ) { ?>
		<div class="footer-top mt-5">
		<?php echo $$footer_top; ?>
		</div>
	<?php } ?>
	<!-- footer top section Ends -->
	<!-- footer bottom section Starts -->
		<div class="footer-bottom">
			<?php echo $$footer; ?>
		</div>
	<!-- footer bottom section Ends -->
</div>
<?php } ?>

<a id="gotop" class="fs-6 go-top" href="#" title="<?php echo_html2view($this->language->get('text_back_on_top'));?>"><i class="fa-solid fa-circle-chevron-up fa-3x"></i></a>
<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
Please donate https://www.abantecart.com/donate
//-->
<?php
/*
	Placed at the end of the document so the pages load faster

	For better rendering minify all JavaScripts and merge all JavaScript files in to one singe file
	Example: <script type="text/javascript" src=".../javascript/footer.all.min.js" defer async></script>

Check Dan Riti's blog for more fine tunning suggestion:
https://www.appneta.com/blog/bootstrap-pagespeed/
		*/
?>
<script src="<?php echo $this->templateResource('/js/fast_checkout.js'); ?>" defer></script>
<?php
if ($scripts_bottom && is_array($scripts_bottom)) {
	foreach ($scripts_bottom as $script) { ?>
		<script type="text/javascript" src="<?php echo $script; ?>" defer></script>
<?php
	}
}
if (trim($this->config->get('config_google_analytics_code'))) { ?>
    <script type="text/javascript">
    <?php
        //get ecommerce tracking data from checkout page
        /**
         * @see AOrder::getGoogleAnalyticsOrderData()
         */
        //when checkout begin
        $gaCheckoutData = $this->session->data['google_analytics_begin_checkout_data'];
        unset($this->session->data['google_analytics_begin_checkout_data']);
        if ($gaCheckoutData) { ?>
            gtag(
                "event",
                "begin_checkout",
                {
                    currency: <?php js_echo($gaCheckoutData['currency_code']); ?>,
                    value: <?php js_echo($gaCheckoutData['total']); ?>
        <?php if ($gaCheckoutData['items']) { ?>
        , items: <?php js_echo($gaCheckoutData['items']); ?>
    <?php } ?>
                }
            );
        <?php } ?>
    </script>
<?php } ?>
    </body>
</html>