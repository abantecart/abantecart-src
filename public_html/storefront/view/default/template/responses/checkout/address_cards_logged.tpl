<?php
/** @var AView|AController $this */
$readonly = count($all_addresses) == 1 ? ' readonly ' : '';
$addAddress = '
<div class="input-group-text">
    <a target="_blank" href="'. $this->html->getSecureUrl('account/address/insert').'" title="'.html2view($this->language->get('button_add_address')).'">
        <i class="fa fa-plus-circle fs-3"></i>
    </a>
</div>';
if ($this->cart->hasShipping()) {  ?>
        <div class="flex-item flex-fill pe-sm-0 pe-md-1 pb-1">
            <h4 class="shipping_address_label"><?php echo $fast_checkout_text_shipping_address; ?></h4>
            <div class="input-group">
                <div class="input-group-text">
                    <i class="fa fa-home" id="delivery_icon"></i>
                </div>
                <select id="shipping_address_id"
                        aria-label="shipping_address_id"
                        class="form-select form-select-lg"
                        name="shipping_address_id" <?php echo $readonly; ?>>
                    <option disabled><?php echo $fast_checkout_text_shipping_address; ?>:</option>
                    <option disabled></option>
                    <?php
                    if ($all_addresses) {
                        $addressId = null;
                        foreach ($all_addresses as $addr) {
                            $current = ($addr['address_id'] == $csession['shipping_address_id']) ? ' selected ' : '';
                            $address = $this->customer->getFormattedAddress($addr, $addr['format']);
                            if(!$formattedShippingAddress){
                                $formattedShippingAddress = $address;
                            }
                            if($current == ' selected '){
                                $formattedShippingAddress = $address;
                                $addressId = $addr['address_id'];
                            }
                            $lines = explode("<br />", $address);
                            echo '<option value="'.$addr['address_id'].'" '.$current.'>'
                                    .$addr['firstname'].' '.$addr['lastname'].' 
                                    '.$addr['address_1'].' '.$addr['address_2'].'</option>';
                            foreach($lines as $line) {
                                echo '<option disabled>'.str_repeat('&nbsp;',3).trim($line).'</option>';
                            }
                        }
                    } ?>
                </select>
                <?php echo $addAddress;?>
            </div>
            <div class="d-flex justify-content-between col-12 bg-light border shipping_address_details">
                <div class="card border-0 bg-light-primary ms-3">
                    <div class="card-body">
                        <?php echo $formattedShippingAddress; ?>
                    </div>
                </div>
                <?php if($addressId){?>
                    <a href="<?php echo $this->html->getSecureUrl('account/address/update','&address_id='.$addressId); ?>"
                       title="<?php echo_html2view($this->language->get('text_edit_address','account/address')); ?>"
                       class="position-relative top-0 end-0 mt-3 me-3">
                        <i class="fa fa-2x fa-pencil-square"></i>
                    </a>
                <?php } ?>
            </div>
        </div>
<?php } //eof if product has shipping

if ($show_payment == true) {
    $readonly = count((array)$csession['shipping_methods']) == 1 ? ' readonly ' : '' ;
?>
    <div class="flex-item flex-fill ps-md-1 ps-0 pt-0 pb-1">
        <h4 class="shipping_address_label"><?php echo $fast_checkout_text_payment_address; ?></h4>
        <div class="input-group">
            <div class="input-group-text">
                <i class="fa fa-bank" id="delivery_icon"></i>
            </div>
            <select aria-label="payment_address_id"
                    class="form-select form-select-lg"
                    id="payment_address_id"
                    name="payment_address_id" <?php echo $readonly .' '.($payment_equal_shipping_address ? 'disabled' : ''); ?>>
                <option disabled><?php echo $fast_checkout_text_payment_address; ?>:</option>
                <option disabled></option>
                <?php
                if ($all_addresses) {
                    $addressId = null;
                    foreach ($all_addresses as $addr) {
                        $current = ($addr['address_id'] == $csession['payment_address_id']) ? ' selected ' : '';
                        $address = $this->customer->getFormattedAddress($addr, $addr['format']);
                        if(!$formattedPaymentAddress){
                            $formattedPaymentAddress = $address;
                        }
                        if($current == ' selected '){
                            $formattedPaymentAddress = $address;
                            $addressId = $addr['address_id'];
                        }
                        $lines = explode("<br />", $address);
                        echo '<option value="'.$addr['address_id'].'" '.$current.'>
                        '.$addr['firstname'].' '.$addr['lastname'].' 
                        '.$addr['address_1'].' '.$addr['address_2']
                                .'</option>';
                        for ($i = 0; $i <= count($lines); $i++) {
                            echo '<option disabled>&nbsp;&nbsp;&nbsp;'.$lines[$i].'</option>';
                        }
                    }
                } ?>
            </select>
            <?php echo $addAddress;?>
        </div>
        <div class="d-flex justify-content-between px-2 col-12 bg-light border payment_address_details">
            <div class="card border-0 bg-light-primary ms-3">
                <div class="card-body">
                    <?php echo $formattedPaymentAddress; ?>
                </div>
            </div>
            <?php if($addressId){?>
                <a href="<?php echo $this->html->getSecureUrl('account/address/update','&address_id='.$addressId); ?>"
                   title="<?php echo_html2view($this->language->get('text_edit_address','account/address')); ?>"
                   class="position-relative top-0 end-0 mt-3 me-3">
                    <i class="fa fa-2x fa-pencil-square"></i>
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<script type="application/javascript">
    $(document).ready(function () {
        $("#payment_address_id").change(function () {
            let url = '<?php echo $main_url ?>&' + getUrlParams('payment_address_id', $(this).val());
            pageRequest(url);
        });

        $("#shipping_address_id").change(function () {
            let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_address_id', $(this).val());
            pageRequest(url);
        });
    });
</script>
