<div class="row">
    <div class="form-group col-xxs-12">
        <div class="left-inner-addon">
            <i class="fa fa-credit-card"></i>
            <input id="cc_number" class="form-control input-lg"
                   placeholder="<?php echo $fast_checkout_text_card_number; ?>" name="cc_number" type="text"
                   maxlength="19" autocomplete="off">
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xxs-7 col-xs-5">
        <div class="left-inner-addon">
            <i class="fa fa-calendar"></i>
            <select class="form-control input-lg" id="cc_expire_date_month" name="cc_expire_date_month">
                <?php
                if (count($pay_form['cc_expire_date_month']->options)) {
                    foreach ($pay_form['cc_expire_date_month']->options as $id => $name) {
                        $current = '';
                        if ($id == $pay_form['cc_expire_date_month']->value) {
                            $current = ' selected ';
                        }
                        echo '<option value="'.$id.'" '.$current.'>'.$name.'</options>';
                    }
                }
                ?>
            </select>
            <div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
        </div>
    </div>
    <div class="form-group col-xxs-5 col-xs-3">
        <div class="left-inner-addon">
            <i class="fa fa-calendar"></i>
            <select class="form-control input-lg" id="cc_expire_date_year" name="cc_expire_date_year">
                <?php
                if (count($pay_form['cc_expire_date_year']->options)) {
                    foreach ($pay_form['cc_expire_date_year']->options as $id => $name) {
                        $current = '';
                        if ($id == $pay_form['cc_expire_date_year']->value) {
                            $current = ' selected ';
                        }
                        echo '<option value="'.$id.'" '.$current.'>'.$name.'</options>';
                    }
                }
                ?>
            </select>
            <div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
        </div>
    </div>
    <div class="form-group col-xxs-12 col-xs-4">
        <div class="left-inner-addon">
            <i class="fa fa-credit-card"></i>
            <input class="form-control input-lg" placeholder="CVV" id="cc_cvv2" name="cc_cvv2" type="text" maxlength="4"
                   autocomplete="off">
        </div>
    </div>
</div>
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
