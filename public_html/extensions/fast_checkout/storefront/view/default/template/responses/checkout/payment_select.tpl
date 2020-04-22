<?php if ($allow_account_creation || $support_recurring_billing) { ?>
    <div class="row">
        <div class="form-group col-xxs-4 col-xs-4">
            <hr>
        </div>
        <?php if ($support_recurring_billing) { ?>
            <div class="form-group col-xxs-8 col-xs-4 pull-right">
                <div class="input-group pull-right">
            <span class="button-checkbox">
                <button type="button" class="btn"
                        data-color="primary"> <?php echo $fast_checkout_text_bill_me_monthly; ?></button>
                <input type="checkbox" name="cc_bill_monthly" class="hidden"/>
            </span>
                </div>
            </div>
        <?php } ?>
        <?php if ($allow_account_creation) { ?>
            <div class="form-group col-xxs-8 col-xs-4 pull-right">
                <div class="input-group pull-right">
            <span class="button-checkbox">
                <button type="button" class="btn"
                        data-color="primary"> <?php echo $fast_checkout_text_create_account; ?></button>
                <input type="checkbox" name="create_account" class="hidden" checked="checked"/>
            </span>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <hr>
<?php } ?>

<div class="row">
    <div class="form-group col-xxs-12 payment_items">
        <?php
        if ($total_payment = count($payment_methods)) {
            foreach ($payment_methods as $id => $payment) {
                $current = '';
                if ($id == $payment_method) {
                    $current = ' selected ';
                }
                ?>
                <div class="payment_item">
                    <div class="thumbnail payment-option <?php echo $current; ?>" data-payment-id="<?php echo $id; ?>">
                        <?php if ($payment['icon']) {
                            $icon = $payment['icon'];
                            ?>
                            <?php if (is_file(DIR_RESOURCE.$icon['image'])) { ?>

								<div style="height: 100px; background-image: url('resources/<?php echo $icon['image']; ?>');
										background-position: center; background-size: contain; background-repeat: no-repeat;" >
								</div>
                            <?php } else {
                                if (!empty($icon['resource_code'])) { ?>
                                    <?php echo $icon['resource_code']; ?>
                                <?php } else { ?>
									<div style="height: 100px; background-image: url('extensions/fast_checkout/storefront/view/default/images/payment.png');
											background-position: center; background-size: contain; background-repeat: no-repeat;" >
									</div>
                                <?php }
                            } ?>
                        <?php } else { ?>
                            <div style="height: 100px; background-image: url('extensions/fast_checkout/storefront/view/default/images/payment.png');
											background-position: center; background-size: contain; background-repeat: no-repeat;" >
                            </div>
                        <?php } ?>
                        <div class="caption">
                            <p class="text-center"><?php if ($id == $payment_method) {  echo '<i class="fa fa-check"></i>'; } echo $payment['title']; ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

<?php
if ($payment_form) {
    ?>
    <div id="payment_details" class="row">
        <?php include($this->templateResource('/template/responses/checkout/payment_form.tpl')); ?>
    </div>
    <?php
}
?>

