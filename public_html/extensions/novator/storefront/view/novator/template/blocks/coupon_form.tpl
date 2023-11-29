<p><?php echo $text_coupon; ?></p>
<div id="couponForm">
    <?php echo $form_open; ?>
        <div class="mb-3 row">
            <label for="<?php echo $coupon->element_id?>" class="text-nowrap col-sm-2 col-form-label"><?php echo $entry_coupon; ?></label>
            <div class="col-sm-10 h-100">
                <?php echo $coupon; ?>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-end">
            <?php if($coupon_code) { ?>
                <a id="remove_coupon_btn" href="Javascript:void(0);"
                   title="<?php echo_html2view($remove); ?>"
                   class="btn btn-outline-danger m-2"><i class="fa fa-trash fa-fw"></i></a>
            <?php } ?>
            <button id="apply_coupon_btn" title="<?php echo_html2view($submit->name); ?>"
                    class="btn btn-success" type="submit">
            <i class="fa fa-check"></i>
            <?php echo $submit->name; ?>
            </button>
        </div>
    </div>
    </form>
</div>
