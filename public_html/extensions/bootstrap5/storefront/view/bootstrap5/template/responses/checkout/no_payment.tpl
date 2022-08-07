<div class="form-group action-buttons">
    <div class="col-md-12">
        <button id="<?php echo $button_confirm->name ?>" class="btn btn-primary lock-on-click " title="<?php echo $button_confirm->name ?>" type="submit">
            <i class="fa fa-check"></i>
            <?php echo $button_confirm->text; ?>
        </button>
    </div>
</div>

<script type="text/javascript">
$('#checkout').click(function(e) {
    e.preventDefault();
    let btn = $(this);
    $.ajax(
        {
            type: 'GET',
            url: '<?php echo $this->html->getURL('r/checkout/no_payment/confirm');?>',
            success: function() {
                location = '<?php echo $continue; ?>';
            },
            error: function(){
                resetLockedButton(btn);
                return false;
            }
        }
    );
});
</script>
