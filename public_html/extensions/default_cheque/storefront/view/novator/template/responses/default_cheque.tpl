<div class="container-fluid px-0">
    <div class="card checkout_details col-12 mx-auto mb-5 bg-light">
        <h5 class="card-title bg-secondary bg-opacity-10 p-2"><?php echo $text_payment; ?></h5>
        <div class="card-body mx-auto text-start" style="width: 90%;">
            <p class="card-text"><?php echo $text_payable; ?> <b class="ms-4"><?php echo $payable; ?></b></p>
            <p class="card-text"><?php echo $text_address; ?> <b class="ms-4"><?php echo $address; ?></b></p>
        </div>
    </div>

    <form id="CqFrm" class="mx-auto text-center">
        <div class="form-group">
            <button id="checkout_btn" type="submit" class="btn btn-primary btn-lg lock-on-click" title="<?php echo $button_confirm->text ?>">
                <i class="fa fa-check"></i>
                <?php echo $button_confirm->text; ?>
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">
$('#CqFrm').on('submit',function(e) {
        e.preventDefault();
        $('body').css('cursor','wait');
        $.ajax(
            {
                type: 'GET',
                url: '<?php echo $this->html->getURL('extension/default_cheque/confirm'); ?>',
                beforeSend: function() {
                    $('.alert').remove();
                    $('.action-buttons')
                        .hide()
                        .before('<div class="wait alert alert-info text-center"><i class="bi bi-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
                },
                success: function() {
                    location = '<?php echo $continue; ?>';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus + ' ' + errorThrown);
                    $('.wait').remove();
                    $('.action-buttons').show();
                    try { resetLockBtn(); } catch (e){}
                }
            }
        );
    }
);
</script>