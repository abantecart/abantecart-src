<div class="card checkout_details col-10 mx-auto mb-5 bg-light" >
    <h5 class="card-title bg-secondary bg-opacity-10 p-2"><?php echo $text_payment; ?></h5>
    <div class="card-body w-75 mx-auto text-start">
      <p class="card-text"><?php echo $text_payable; ?> <b class="ms-4"><?php echo $payable; ?></b></p>
      <p class="card-text"><?php echo $text_address; ?> <b class="ms-4"><?php echo $address; ?></b></p>
  </div>
</div>

<form id="CqFrm">
    <div class="form-group action-buttons">
        <div class="col-md-12">
            <button id="checkout_btn" type="submit" class="btn btn-orange pull-right lock-on-click" title="<?php echo $button_confirm->text ?>">
                <i class="fa fa-check"></i>
                <?php echo $button_confirm->text; ?>
            </button>
        </div>
    </div>
</form>
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
                        .before('<div class="wait alert alert-info text-center"><i class="fa fa-refresh fa-spin"></i> <?php echo $text_wait; ?></div>');
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