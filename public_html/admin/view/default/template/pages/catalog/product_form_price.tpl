<table class="border-0">
    <tr>
        <td>
            <div class="input-group afield">
                <div class="input-group-addon"><?php echo $currency['symbol_left']; ?></div>
                <?php echo $field; ?>
                <div class="input-group-addon"><?php echo $currency['symbol_right']; ?></div>
            </div>
        </td>
        <td>
            <b class="fa-2x ml10 mr10">&plus;</b>
        </td>
        <td>
            <div class="input-group">
                <div class="input-group-addon">
                    <?php echo $entry_tax_rule; ?>
                </div>
                <?php echo $form['tax_selector']; ?>
            </div>
        </td>
        <td><b class="fa-2x ml10 mr10">&equals;</b></td>
        <td>
            <div class="input-group">
                <div class="input-group-addon"><?php echo $entry_price_with_tax; ?></div>
                <div class="input-group-addon"><?php echo $currency['symbol_left']; ?></div>
                <?php echo $form['price_with_tax']; ?>
                <div class="input-group-addon"><?php echo $currency['symbol_right']; ?></div>
            </div>
        </td>
    </tr>
</table>

<script type="text/javascript">
    let priorElm;
    $(document).on(
        'keyup change blur drop focus',
        'select[name="tax_selector"], input[name="price"], input[name="price_with_tax"]',
        function (e) {
            priorElm = e.type === 'keyup' || e.type === 'drop' ? $(this) : priorElm;
            getTaxedPrice($(this), priorElm);
        }
    );

    function getTaxedPrice(initiator) {
        let priceElm = $('input[name="price"]');
        let priceWithTaxElm = $('input[name="price_with_tax"]');
        let precision = 0;
        if (initiator.attr('name') === 'tax_selector') {
            initiator = priceElm;
        }
        if (initiator.val().length > 0) {
            let end = initiator.val().split('.').slice(-1)[0];
            precision = end.length > precision ? end.length : precision;
        }
        numberSeparators = {precision: precision, dec_point: '.', thousands_point: null};

        formatPrice(priceElm.get(0));
        formatPrice(priceWithTaxElm.get(0));

        let value = '&' + initiator.attr('name') + '=' + initiator.val();
        if (initiator.val() !== null && initiator.val() !== '') {
            $.get(
                '<?php echo $price_calc_url?>' + value + '&tax_class_id=' + $('select[name="tax_selector"]').val(),
                function (res) {
                    if (initiator.attr('name') === 'price') {
                        priceWithTaxElm.val(res);
                        formatPrice(priceWithTaxElm.get(0));
                    } else {
                        priceElm.val(res);
                        formatPrice(priceElm.get(0));
                        priceElm.aform().change();
                    }
                }
            );
        }
    }

    $(document).ready(function () {
        let price = $('input[name="price"]');
        let p = price.val();
        if (p !== '' && p !== '0.0' && p !== '0.00') {
            getTaxedPrice(price, price);
        }
    });
</script>