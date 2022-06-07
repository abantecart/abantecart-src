<?php
$total_payment = count((array)$payment_methods);
$no_payment_required = ($this->data['payment_method'] == 'no_payment_required');
if($total_payment || $balance>0 || $no_payment_required){ ?>
<h5 class="text-center text-uppercase mb-3"><?php echo $fast_checkout_text_select_payment; ?></h5>
<div class="d-flex flex-wrap justify-content-evenly payment_items ">
<?php
    if ($total_payment) {
        $paymentCover = '<img style="height: 100px;" src="%s">';
        $defaultPaymentCover = sprintf(
                $paymentCover,
                'extensions/fast_checkout/storefront/view/default/images/payment.png'
        );

        foreach ($payment_methods as $id => $payment) {
            $current = ($id == $payment_method) ? ' bg-success bg-opacity-25 ' : ''; ?>
            <div class="card payment_item border col-11 col-sm-10 col-md-5 col-lg-5 m-2"
                 data-payment-id="<?php echo $id; ?>"
                 data-payment-available="<?php echo (!$csession['used_balance_full'] ? 'true': 'false'); ?>">
                <div class="card-header d-none d-md-block text-start text-md-center fw-bold bg-gradient <?php echo $current; ?>">
                    <?php echo ($id == $payment_method ? '<i class="fa fa-check me-2"></i>' : '') . $payment['title']; ?>
                </div>
                <div class="card-body d-flex flex-wrap flex-sm-nowrap thumbnail justify-content-center text-md-center payment-option <?php echo $current; ?>">
                    <div class="">
                        <?php
                        if ($payment['icon']) {
                            $icon = $payment['icon'];
                            if (is_file(DIR_RESOURCE.$icon['image'])) {
                                echo sprintf( $paymentCover, 'resources/'.$icon['image']);
                            } else {
                                echo $icon['resource_code'] ?: $defaultPaymentCover;
                            }
                        } else {
                            echo $defaultPaymentCover;
                        } ?>
                    </div>
                    <div class="card-text fw-bold mt-3 ms-0 ms-sm-4 w-100 d-md-none text-center">
                        <?php echo ($id == $payment_method ? '<i class="fa fa-check me-2"></i>' : '') . $payment['title']; ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
    if ($balance > 0 && !$no_payment_required) {
        $css = $csession['used_balance'] ? 'balance_applied' : '';
        $css .= $csession['used_balance_full'] ? ' balance_applied_full' : '';
        $current = $csession['used_balance_full'] ? ' bg-success bg-opacity-25 ' : '';
    ?>
    <div class="card payment_item border col-11 col-sm-6 col-md-4 col-lg-5 m-2 <?php echo $css; ?>">
        <div class="card-header text-center fw-bold bg-gradient <?php echo $current; ?>">
            <?php echo ($csession['used_balance_full'] ? '<i class="fa fa-check me-2"></i>' : '')
                    . sprintf( $fast_checkout_text_account_credit, $balance_value );
            ?>
        </div>
        <div class="card-body thumbnail payment-option <?php echo $current; ?> d-flex flex-column align-items-center justify-content-center"
             data-payment-id="account_balance">
                <i class="fa fa-money-bill-transfer fa-fw fa-3x my-4"></i>
                <div class="mb-3">
                    <?php if ($csession['used_balance']) { ?>
                        <button class="btn btn-outline-secondary btn-md btn-remove-balance" type="button">
                        <i class="fa fa-trash fa-fw"></i>
                        <span class="hidden-xxs">
                            <?php echo $fast_checkout_text_remove; ?>
                        </span>
                      </button>
                    <?php } else { ?>
                        <button class="btn btn-outline-secondary btn-md btn-apply-balance" type="button">
                        <i class="fa fa-check fa-fw"></i>
                        <span class="hidden-xxs">
                            <?php echo $fast_checkout_text_apply; ?>
                        </span>
                      </button>
                    <?php } ?>
                </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php } ?>