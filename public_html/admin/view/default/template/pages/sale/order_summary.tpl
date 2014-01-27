<?php if ($error) {?>
    <div class="warning alert alert-error"><?php echo $error; ?></div>
<?php } else { ?>

<div class="fieldset">
    <div class="heading"><?php echo $text_order_summary; ?></div>
    <div class="top_left">
        <div class="top_right">
            <div class="top_mid"></div>
        </div>
    </div>
    <div class="cont_left">
        <div class="cont_right">
            <div class="cont_mid">
                <div class="summary_loading"></div>
                <table id="summary" class="summary" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="summary_label"><?php echo $entry_order_id; ?></td>
                        <td class="summary_value"><?php echo $order['order_id']; ?></td>
                        <td class="summary_label"></td>
                        <td class="summary_value"></td>
                        <td class="summary_label"></td>
                        <td class="summary_value"></td>
                    </tr>
                    <tr>
                        <td class="summary_label"><?php echo $entry_customer; ?></td>
                        <td class="summary_value"><?php echo $order['name']; ?></td>
                        <td class="summary_label"><?php echo $entry_email; ?></td>
                        <td class="summary_value"><?php echo $order['email']; ?></td>
                        <td class="summary_label"><?php echo $entry_telephone; ?></td>
                        <td class="summary_value"><?php echo $order['telephone']; ?></td>
                    </tr>
                    <tr>
                        <td class="summary_label"><?php echo $entry_date_added; ?></td>
                        <td class="summary_value"><?php echo $order['date_added']; ?></td>
                        <td class="summary_label"><?php echo $entry_total; ?></td>
                        <td class="summary_value"><?php echo $order['total']; ?></td>
                        <td class="summary_label"><?php echo $entry_order_status; ?></td>
                        <td class="summary_value"><?php echo $order['order_status']; ?></td>
                    </tr>
                    <tr>
                        <td class="summary_label"><?php echo $entry_shipping_method; ?></td>
                        <td class="summary_value"><?php echo $order['shipping_method']; ?></td>
                        <td class="summary_label"><?php echo $entry_payment_method; ?></td>
                        <td class="summary_value"><?php echo $order['payment_method']; ?></td>
                        <td class="summary_label"></td>
                        <td class="summary_value"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="bottom_left">
        <div class="bottom_right">
            <div class="bottom_mid"></div>
        </div>
    </div>
</div><!-- <div class="fieldset"> -->
<?php } ?>