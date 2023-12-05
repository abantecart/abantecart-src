<?php if ($this->cart->hasShipping()) {
    $address = $this->customer->getFormattedAddress(
            $guest_data['shipping'],
            $guest_data['shipping']['address_format']
    ); ?>
    <div class="flex-item flex-fill pe-sm-0 pe-md-1 pb-1">
        <div class="d-flex justify-content-between col-12 bg-light border">
            <div class="card bg-light border-0 ms-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fa fa-home" id="delivery_icon"></i> <?php echo $fast_checkout_text_shipping_address; ?></h5>
                    <?php echo $address; ?>
                </div>
            </div>
            <a href="<?php echo $edit_address_url; ?>&type=shipping"
               class="address_edit text-end p-3">
                <i class="fa fa-edit fa-xl"></i>
            </a>
        </div>
    </div>
<?php }

if ($show_payment == true) {
    $readonly = count((array)$csession['shipping_methods']) == 1 ? ' readonly ' : '' ;
    $address = $this->customer->getFormattedAddress(
                        $guest_data,
                        $guest_data['address_format']
                    ); ?>
    <div class="flex-item flex-fill ps-md-1 ps-0 pt-0 pb-1">
        <div class="d-flex justify-content-between col-12 bg-light border">
            <div class="card border-0 bg-light ms-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fa fa-bank" id="bank_icon"></i> <?php echo $fast_checkout_text_payment_address; ?></h5>
                    <?php echo $address; ?>
                </div>
            </div>

            <?php if(!$payment_equal_shipping_address){ ?>
                <a href="<?php echo $edit_address_url; ?>&type=payment"
                   class="address_edit text-end p-3 "
                   id="payment_address_edit">
                    <i class="fa fa-edit fa-xl"></i>
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>