<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php	echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'home'; ?>">

<?php echo $this->getHookVar('top_page'); ?>

<div class="container-fixed" style="max-width: <?php echo $layout_width; ?>">

    <?php if ($maintenance_warning) { ?>
        <div class="alert alert-warning alert-dismissible">
           <i class="fa-solid fa-circle-exclamation me-2">
           <strong><?php echo $maintenance_warning;?></strong>
           <?php if($act_on_behalf_warning){ ?>
              <br/><strong><?php echo $act_on_behalf_warning;?></strong>
           <?php } ?>
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php
    }
    if ($act_on_behalf_warning && !$maintenance_warning) { ?>
        <div class="alert alert-warning alert-dismissible">
            <i class="fa-solid fa-circle-exclamation me-2"></i><strong><?php echo $act_on_behalf_warning;?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php }
echo ${$header}; ?>

    <?php if ( !empty( $$header_bottom ) ) { ?>
    <!-- header_bottom blocks placeholder -->
        <div class="container-fluid">
            <?php echo $$header_bottom; ?>
        </div>
    <!-- header_bottom blocks placeholder -->
    <?php } ?>

	<div id="maincontainer">

        <?php
            //check layout dynamically
            $present_columns = 0;
            if (!empty($$column_left)) {
                $present_columns++;
            }
            if (!empty($$column_right)) {
                $present_columns++;
            }
        ?>

        <div class="d-flex flex-wrap align-items-stretch align-self-stretch justify-content-center">
            <?php if ( !empty($$column_left ) ) { ?>
            <div class="ms-3 col-12 col-sm-9 col-md-8 col-lg-2 col-xl-3">
            <?php echo $$column_left; ?>
            </div>
            <?php } ?>

            <?php $span = 12 - 4 * $present_columns; ?>
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
            <div class="me-3 col-11 col-sm-9 col-md-8 col-lg-2 col-xl-3">
            <?php echo $$column_right; ?>
            </div>
            <?php } ?>
        </div>

    </div>

    <?php if ( !empty( $$footer_top ) ) { ?>
    <!-- footer top blocks placeholder -->
        <div class="container-fluid">
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

<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
Please donate http://www.abantecart.com/donate
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
<script type="text/javascript" src="<?php echo $this->templateResource('/js/jquery-3.6.0.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/js/bootstrap.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/js/custom.js'); ?>"></script>
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