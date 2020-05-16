<?php echo $head; ?>
    <div class="pay-form">
        <div class="text-center order-success">
            <div class="order-success">
                <br/>
                <h3 class="text-success"><?php echo $fast_checkout_text_order_is_completed; ?></h3>
                <br/>
                <div class="text-success">
                    <i class="fa fa-check fa-fw"></i>
                </div>
                <br/>
                <?php
                //if we have custom message
                if ($order_finished_message) {
                    echo "<p>".$order_finished_message."</p>";
                    echo "<p>".$fast_checkout_text_thank_you."</p>";
                }
                //if guest wish to be registered - show message
                if ($text_account_created) {
                    echo "<p>".$text_account_created."</p>";
                }
                ?>
                <br/>
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
                               class="btn btn-primary btn-xl reload_parent">
                                <i class="fa fa-download fa-fw"></i>
                                <?php echo $fast_checkout_button_start_download; ?>
                            </a>
                        <?php }
                    } ?>
                    <a href="#" data-href="<?php echo $button_order_details->href; ?>"
                       class="btn btn-default btn-xl reload_parent">
                        <i class="fa fa-archive fa-fw"></i>
                        <?php echo $fast_checkout_button_order_details; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<script>
	$(".reload_parent").on("click", function (e) {
		var url = $(this).attr("data-href");
		if (window.parentIFrame) {
			window.parentIFrame.sendMessage({reload: true, url: url});
		} else {
			location = url;
		}
		return false;
	});
</script>
<?php echo $footer; ?>
