<div class="buttons">
    <table>
        <tr>
            <!-- agree -->
            <td align="center" style="font-size:14px; color:#cc0000 !important; padding:7px;">
                <b style="color:#cc0000 !important;">Please provide a Credit Card to complete the order. There will be
                    no charge to your card unless it is necessary to initiate our Market Loss Policy in the event of you
                    canceling or defaulting on payment.</b>
            </td>
            <!-- agree -->
        </tr>
    </table>
</div>

<?php if ($saved_cc_list) { ?>
<div class="saved_cards" style="padding:0 10px 0 10px;">
    <form id="authorizenet_saved_cc" class="validate-creditcard">
        <h4 class="heading4"><?php echo $text_saved_credit_card; ?></h4>

        <table width="100%">
            <tr>
                <td><?php echo $entry_billing_address; ?>: <?php echo $payment_address; ?>...
                    <a href="<?php echo $edit_address; ?>" class="btn_standard">
                        <i class="fa fa-edit fa-lg"></i> <?php echo $entry_edit; ?>
                    </a>
                </td>
            </tr>
        </table>
        <br>
        <table width="100%">
            <tr>
                <td>
                    <?php echo $saved_cc_list; ?>
                    &nbsp;
                    <a id="delete_card" class="btn_standard" title="<?php echo $text_delete_saved_credit_card; ?>">
                        <i class="fa fa-trash-o fa-lg"></i> <?php echo $text_delete_saved_credit_card; ?>
                    </a>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0 10px 0;">
                    <a id="new_card" class="btn_standard" title="<?php echo $text_new_credit_card; ?>">
                        <i class="fa fa-plus fa-lg"></i>
                        <?php echo $text_new_credit_card; ?>
                    </a>
                </td>
            </tr>
        </table>
        <br>

    </form>
</div>

<div class="enter_card" style="display:none;" style="padding:0 10px 0 10px;">
    <?php } else { ?>

    <div class="enter_card" style="padding:0 10px 0 10px;">
        <?php } ?>
        <?php echo $form['form_open']; ?>
        <b style="margin-bottom: 3px; display: block;"><?php echo $text_credit_card; ?></b>
        <div id="authorizenet"
             style="background: #F7F7F7; border: 1px solid #DDDDDD; padding: 10px; margin-bottom: 10px;">
            <div id="cc-logo"><img alt="" src="image/creditcards.jpg"></div>
            <table style="width: 100%;">
                <tr>
                    <td><?php echo $entry_cc_owner; ?></td>
                    <td>
                        <?php echo $cc_owner_firstname; ?>
                        <?php echo $cc_owner_lastname; ?>
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td><?php echo $entry_cc_number; ?></td>
                    <td><?php echo $cc_number; ?></td>
                </tr>
                <tr>
                    <td><?php echo $entry_cc_expire_date; ?></td>
                    <td><?php echo $cc_expire_date_month; ?> / <?php echo $cc_expire_date_year; ?></td>
                </tr>
                <tr>
                    <td><?php echo $entry_cc_cvv2; ?></td>
                    <td><?php echo $cc_cvv2; ?></td>
                </tr>
            </table>
        </div>

        </form>
    </div>

    <div class="buttons">
        <table>
            <tr>
                <!-- agree -->
                <td align="center" style="font-size:14px; color:#cc0000 !important; padding:7px;">
                    <b style="color:#cc0000 !important;">Again there will be no charge to your card unless it is
                        necessary to initiate our Market Loss Policy.</b>
                </td>
                <!-- agree -->
            </tr>
        </table>
    </div>

<script type="text/javascript"><!--
    $('#new_card').click(function () {
        //important to remove saved CCed for validation pages
        $('.saved_cards').remove();
        $('.enter_card').show();
        $('#save_cc').change();

    });
    $('#enter_card').hover(function () {
        $(this).tooltip('show');
    });
//--></script>