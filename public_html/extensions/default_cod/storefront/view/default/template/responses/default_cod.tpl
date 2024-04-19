<form id="CODFrm" action="<?php echo $this->html->getURL('extension/default_cod/confirm');?>" method="get">
    <div class="form-group action-buttons">
        <div class="col-md-12">
            <button type="submit" id="checkout_btn" class="btn btn-primary" title="<?php echo $button_confirm->text ?>">
                <i class="fa fa-check"></i>
                <?php echo $button_confirm->text; ?>
            </button>
        </div>
    </div>
</form>
<script type="text/javascript">
    $('#CODFrm').on('submit',function(e) {
        e.preventDefault();
        $('body').css('cursor', 'wait');
        $.ajax({
            type: 'GET',
            url: '<?php echo $this->html->getSecureURL('r/extension/default_cod/confirm');?>',
            beforeSend: function () {
                $('.alert').remove();
                $('.action-buttons')
                    .hide()
                    .before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
            },
            success: function () {
                location = '<?php echo $continue; ?>';
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus + ' ' + errorThrown);
                $('.wait').remove();
                $('.action-buttons').show();
                try {
                    resetLockBtn();
                } catch (e) {
                }
            }
        });
    });
</script>
