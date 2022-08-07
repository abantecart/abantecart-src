
<?php if ($connected) { ?>
    <a class="inblock btn btn-default mr10" href="<?php echo $disconnect_url; ?>">
        <i class="fa fa-chain-broken fa-fw"></i> <?php echo $text_disconnect; ?>
    </a>
    <?php if ($test_mode) { ?>
        <a href="<?php echo $connect_url; ?>" class="pp-live-btn inblock mr10">
            <span><?php echo $text_connect; ?> (live) </span>
        </a>
    <?php } else { ?>
        <a href="<?php echo $connect_url; ?>&mode=test" class="pp-test-btn inblock mr10">
            <span><?php echo $text_connect; ?> (test) </span>
        </a>
    <?php } ?>
<?php } else { ?>
    <a href="<?php echo $connect_url; ?>" class="pp-live-btn inblock mr10 ml30">
        <i class="fa fa-paypal mr10"></i>
        <?php echo $text_connect; ?> (live)
    </a>
    <a href="<?php echo $connect_url; ?>&mode=test" class="pp-test-btn inblock mr10">
        <i class="fa fa-paypal mr10"></i>
        <?php echo $text_connect; ?> (test)
    </a>
    <a class="inblock mr10 pp-skip-connect" href="Javascript:void(0)">
        <?php echo $text_skip_connect; ?>
    </a>
    <script type="application/javascript">
        $('.pp-skip-connect').on('click', function(){
            $('.pp-manual-connect-settings').slideDown();
            $(this).hide();
        });
    </script>
<?php } ?>