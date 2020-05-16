<div class="row local_delivery_fields">
    <div class="form-group col-xxs-12">
        <div class="left-inner-addon">
            <i class="fa fa-phone"></i>
            <div class="input-group">
            <input id="telephone"
                   class="form-control input-lg"
                   placeholder="<?php echo $fast_checkout_text_telephone_placeholder; ?>"
                   name="telephone"
                   type="text"
                   value="<?php echo $customer_telephone; ?>" >
            <span class="input-group-btn">
                <button class="btn btn-default btn-lg btn-telephone" type="button">
                <i class="fa fa-check fa-fw"></i>
                    <span class="hidden-xxs"><?php echo $fast_checkout_text_apply; ?></span>
              </button>
            </span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function () {

    $(".local_delivery_fields .btn-telephone").on('click', function () {
        var tel = $('[name=telephone]').val();
        if ( validateForm( $(this).closest('form')) ) {
            $.ajax({
                type: "POST",
                url: '<?php echo $this->html->getSecureUrl('r/checkout/pay/updateOrderData') ?>',
                data: { cc_telephone: tel, telephone: tel },
            });
        }else{
            $.aCCValidator.show_error($(this), '.form-group');
        }
    });

});
</script>