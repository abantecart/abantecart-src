<?php if ($minimum_notmet) { ?>
<div class="alert alert-error alert-danger">
  <strong><?php echo $minimum_notmet; ?></strong>
</div>
<?php } ?>

<div class="checkout_details mb-4"><?php echo $text_instructions; ?><br />
  <?php echo $instructions; ?>
  <br />
  <br />
  <?php echo $text_payment; ?>
</div>
<div class="form-group action-buttons ">
    <div class="text-center">
<?php if (!$minimum_notmet) { ?>
        <a id="checkout_btn" onclick="confirmOrder();" class="btn btn-primary lock-on-click checkout_btn">
            <i class="fa fa-check"></i>
            <?php echo $button_confirm; ?>
        </a>
<?php } ?>
    </div>
</div>

<script type="text/javascript">
function confirmOrder() {
    $('body').css('cursor','wait');
    $.ajax({
        type: 'GET',
        url: '<?php echo $this->html->getURL('extension/default_banktransfer/confirm');?>',
        global: false,
        beforeSend: function () {
            $('.spinner-overlay').fadeIn(100);
            $('.alert').remove();
            $('.action-buttons')
                .hide()
                .before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
        },
        success: function() {
            goTo('<?php echo $continue; ?>');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('.spinner-overlay').fadeOut(500);
            alert(textStatus + ' ' + errorThrown);
            $('.wait').remove();
            $('.action-buttons').show();
            try { resetLockBtn(); } catch (e){}
        }
    });
}
</script>
