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
                    <div class="thumbnail payment-option <?php echo $current; ?>" data-payment-id="<?php echo $id; ?>" paiment-awailable="<?php if(!$csession['used_balance_full']) { echo 'true'; } else { echo 'false'; } ?>">
                        <div class="caption">
                            <p class="text-center"><?php if ($id == $payment_method) {  echo '<i class="fa fa-check"></i>'; } echo $payment['title']; ?></p>
                        </div>
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
                    </div>
                </div>
                <?php
            }
        }
        ?>
        <?php if ($balance > 0) { ?>
        <div class="payment_item <?php if ($csession['used_balance']) { ?>balance_applied<?php } ?> <?php if ($csession['used_balance_full']) { ?>balance_applied_full<?php } ?>">
            <div class="thumbnail payment-option <?php if ($csession['used_balance_full']) { ?>current<?php } ?>" data-payment-id="account_balance">
                <div class="caption">
                    <p class="text-center"><?php if ($csession['used_balance_full']) {  echo '<i class="fa fa-check"></i>'; } ?>
                        <span class="hidden-xxs"><?php echo sprintf($fast_checkout_text_account_credit,
                            $balance_value) ?></span>
                        <span class="visible-xxs"><?php echo $balance_value; ?></span>
                    </p>
                </div>
				<div style="min-height: 66px;"><i class="fa fa-money fa-fw fa-3x"></i></div>
                <div>
                    <div class="input-group">
                        <span class="input-group-btn">
                        <?php if ($csession['used_balance']) { ?>
                            <button class="btn btn-default btn-md btn-remove-balance" type="button">
                            <i class="fa fa-trash fa-fw"></i>
                            <span class="hidden-xxs">
                                <?php echo $fast_checkout_text_remove; ?>
                            </span>
                          </button>
                        <?php } else { ?>
                            <button class="btn btn-default btn-md btn-apply-balance" type="button">
                            <i class="fa fa-check fa-fw"></i>
                            <span class="hidden-xxs">
                                <?php echo $fast_checkout_text_apply; ?>
                            </span>
                          </button>
                        <?php } ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
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

