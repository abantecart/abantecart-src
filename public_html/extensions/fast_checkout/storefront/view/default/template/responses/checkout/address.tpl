<?php echo $form['form_open']; ?>
<fieldset>
    <?php echo $this->getHookVar('address_form_top'); ?>
    <label class="visible-xs text-center text-uppercase"><?php echo $type.' '.$fast_checkout_text_address; ?></label>
    <div class="row">
        <div class="form-group col-xxs-12 col-xs-6 <?php if (isset($errors['firstname'])) { echo 'has-error'; } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-user"></i>
                <input aria-label="firstname"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($entry_firstname); ?>"
                       name="firstname"
                       type="text"
                       value="<?php echo $form['firstname']->value; ?>">
            </div>
        </div>
        <div class="form-group col-xxs-12 col-xs-6 <?php if (isset($errors['lastname'])) { echo 'has-error'; } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-user"></i>
                <input aria-label="lastname"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($entry_lastname); ?>"
                       name="lastname"
                       type="text"
                       value="<?php echo $form['lastname']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 <?php if (isset($errors['address_1'])) { echo 'has-error'; } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-building"></i>
                <input id="cc_address_1"
                       aria-label="cc_address_1"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($entry_address_1); ?>"
                       name="address_1"
                       type="text"
                       value="<?php echo $form['address_1']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 <?php if (isset($errors['address_2'])) { echo 'has-error'; } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-building-o"></i>
                <input id="cc_address_2"
                       aria-label="cc_address_2"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($entry_address_2); ?>"
                       name="address_2"
                       type="text"
                       value="<?php echo $form['address_2']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 col-xs-6 <?php if (isset($errors['city'])) { echo 'has-error'; } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-institution"></i>
                <input aria-label="city"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($entry_city); ?>"
                       name="city"
                       type="text"
                       value="<?php echo $form['city']->value; ?>">
            </div>
        </div>
        <div class="form-group col-xxs-12 col-xs-6 <?php if (isset($errors['postcode'])) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-bars"></i>
                <input aria-label="postcode"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($entry_postcode); ?>"
                       name="postcode"
                       type="text" value="<?php echo $form['postcode']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 col-xs-6 <?php if (isset($errors['zone'])) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-bars"></i>
                <select aria-label="zone" class="form-control input-lg" id="zone_id" name="zone_id"></select>
                <div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
            </div>
        </div>
        <div class="form-group col-xxs-12 col-xs-6 <?php if (isset($errors['country'])) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-map"></i>
                <select aria-label="country" class="form-control input-lg" id="country_id" name="country_id">
                <?php
                    if ($form['country_id']->options) {
                        foreach ($form['country_id']->options as $id => $name) {
                            $current = '';
                            if ($id == $form['country_id']->value) {
                                $current = ' selected ';
                            }
                            echo '<option value="'.$id.'" '.$current.'>'.$name.'</options>';
                        }
                    } ?>
                </select>
                <div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12">
            <div class="left-inner-addon">
                <i class="fa fa-envelope"></i>
                <input aria-label="email"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($this->language->get('fast_checkout_email_placeholder')); ?>"
                       id="cc_email"
                       name="cc_email"
                       type="text"
                       value="<?php echo $customer_email; ?>">
            </div>
        </div>
    </div>
    <?php
    if ($require_telephone) { ?>
    <div class="row">
        <div class="form-group col-xxs-12">
            <div class="left-inner-addon">
                <i class="fa fa-phone"></i>
                <input aria-label="phone"
                       class="form-control input-lg"
                       placeholder="<?php echo_html2view($this->language->get('fast_checkout_telephone_placeholder')); ?>"
                       id="telephone"
                       name="telephone"
                       type="text"
                       value="<?php echo $customer_telephone; ?>" <?php echo $loggedin ? 'readonly' : ''; ?>>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php echo $this->getHookVar('address_attributes'); ?>
    <?php if ($type == 'payment' && $this->cart->hasShipping()) { ?>
        <div class="row">
            <div class="form-group col-xs-2">
                <hr>
            </div>
            <div class="form-group col-xs-10">
                <div class="input-group pull-right">
                <span class="button-checkbox">
                    <button type="button" class="btn"
                            data-color="primary"> <?php echo $fast_checkout_text_same_as_shipping_address; ?></button>
                    <input aria-label="same-as" type="checkbox" name="same_as_shipping" class="hidden" checked="checked"/>
                </span>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <hr>
    <?php } ?>


    <button type="submit" class="btn btn-primary btn-lg btn-block btn-pay">
        <?php echo $fast_checkout_text_enter_address.' '.$text_mmmm; ?>
    </button>
    <?php echo $this->getHookVar('address_form_bottom'); ?>
</fieldset>

</form>
<script type="text/javascript">
    <?php $cz_url = $this->html->getURL('common/zone', '&zone_id='.$zone_id); ?>
    var cntry = $('#country_id');
    cntry.change(function () {
        $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
    });
    cntry.change();
</script>
