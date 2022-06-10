<?php
$readonly = count($all_addresses) == 1 ? ' readonly ' : '';

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
                        foreach ($all_addresses as $addr) {
                            $current = ($addr['address_id'] == $csession['shipping_address_id']) ? ' selected ' : '';
                            $address = $this->customer->getFormattedAddress($addr, $addr['address_format']);
                            $lines = explode("<br />", $address);
                            echo '<option value="'.$addr['address_id'].'" '.$current.'>'
                                    .$lines[0].', '.$lines[1].'...</option>';
                            for ($i = 0; $i <= count($lines); $i++) {
                                echo '<option disabled>&nbsp;&nbsp;&nbsp;'.$lines[$i].'</option>';
                            }
                        }
                    } ?>
                </select>
            </div>
            <?php // see JS?>
            <div class="d-flex justify-content-between col-12 bg-light border shipping_address_details"></div>
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
                    foreach ($all_addresses as $addr) {
                        $current = ($addr['address_id'] == $csession['payment_address_id']) ? ' selected ' : '';
                        $address = $this->customer->getFormattedAddress($addr, $addr['address_format']);
                        $lines = explode("<br />", $address);
                        echo '<option value="'.$addr['address_id'].'" '.$current.'>'
                                .$lines[0].', '.$lines[1] .'...</option>';
                        for ($i = 0; $i <= count($lines); $i++) {
                            echo '<option disabled>&nbsp;&nbsp;&nbsp;'.$lines[$i].'</option>';
                        }
                    }
                } ?>
            </select>
        </div>

        <?php // see JS?>
        <div class="d-flex justify-content-between px-2 col-12 bg-light border payment_address_details"></div>
    </div>
<?php } ?>
<script type="application/javascript">
    var getAddressHtml = function (address) {
        let html = '<div class="card border-0 bg-light ms-3">'
                    + '<div class="card-body">';

        if (typeof address != "undefined") {
            if (address.firstname || address.lasttname) {
                html += address.firstname + ' ' + address.lastname + ' <br/>'
            }
            if (address.company) {
                html += address.company + ' <br/>'
            }
            if (address.address_2) {
                html += address.address_2 + ' <br/>'
            }
            if (address.address_1) {
                html += address.address_1 + ' <br/>'
            }
            if (address.city || address.postcode) {
                html += address.city + ' ' + address.postcode + ' <br/>'
            }
            if (address.zone) {
                html += address.zone + ' <br/>'
            }
            if (address.country) {
                html += address.country
            }
            html += '</div></div>';
            <?php if ($address_edit_base_url) { ?>
                html += '<a class="address_edit_link text-end p-3 " href="<?php echo $address_edit_base_url; ?>' + address.address_id + '">' +
                    '<i class="fa fa-edit fa-xl"></i></a>';
                <?php } ?>
        }
        return html
    };

    var updateShippingAddressDisplay = function () {
        let addresses = JSON.parse(atob('<?php echo base64_encode(json_encode($all_addresses)); ?>'))
        let shipping_address_id = $("#shipping_address_id").val();
        let address = addresses.find((el) => el.address_id == shipping_address_id);

        if (typeof address != "undefined") {
            $('.shipping_address_details').hide().html(getAddressHtml(address)).fadeIn(1000);
        }
    };

    var updatePaymentAddressDisplay = function () {
        let addresses = JSON.parse(atob('<?php echo base64_encode(json_encode($all_addresses)); ?>'));
        let payment_address_id = $("#payment_address_id").val();
        let address = addresses.find((el) => el.address_id == payment_address_id);

        if (typeof address != "undefined") {
            $('.payment_address_details').hide().html(getAddressHtml(address)).fadeIn(1000);
        }
    };
    $(document).ready(function () {
        $("#payment_address_id").change(function () {
            let url = '<?php echo $main_url ?>&' + getUrlParams('payment_address_id', $(this).val());
            pageRequest(url);
        });

        $("#shipping_address_id").change(function () {
            let url = '<?php echo $main_url ?>&' + getUrlParams('shipping_address_id', $(this).val());
            pageRequest(url);
        });
        updateShippingAddressDisplay();
        updatePaymentAddressDisplay();
    });
</script>
