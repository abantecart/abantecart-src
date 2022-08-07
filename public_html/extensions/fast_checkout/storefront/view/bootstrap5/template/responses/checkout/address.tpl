<?php
$form['form_open']->style .= ' needs-validation';
//block native browser validation messages
$form['form_open']->attr .= ' novalidate ';
echo $form['form_open']; ?>
<div id="pay_error_container">
    <?php
    include($this->templateResource('/template/responses/checkout/alerts.tpl'));
    ?>
</div>
<fieldset>
    <?php echo $this->getHookVar('address_form_top'); ?>
    <label class="visible-xs text-center text-uppercase"><?php echo $type.' '.$fast_checkout_text_address; ?></label>
    <div class="row mb-3">
        <div class="form-group col-12 col-sm-6 mb-3 mb-sm-0">
            <div class="input-group">
                <div class="input-group-text">
                    <i class="fa fa-user"></i>
                </div>
                <input aria-label="firstname"
                       class="form-control form-control-lg <?php if (isset($errors['firstname'])) { echo 'is-invalid'; } ?>"
                       placeholder="<?php echo_html2view($entry_firstname); ?>"
                       name="firstname"
                       type="text"
                       maxlength="32"
                       required
                       value="<?php echo $form['firstname']->value; ?>">
            </div>
        </div>
        <div class="form-group col-12 col-sm-6">
            <div class="input-group">
                <div class="input-group-text">
                    <i class="fa fa-user"></i>
                </div>
                <input aria-label="lastname"
                       class="form-control form-control-lg <?php if (isset($errors['lastname'])) { echo 'is-invalid'; } ?>"
                       placeholder="<?php echo_html2view($entry_lastname); ?>"
                       name="lastname"
                       type="text"
                       maxlength="32"
                       required
                       value="<?php echo $form['lastname']->value; ?>">
            </div>
        </div>
    </div>

    <div class="form-group col-12 mb-3">
        <div class="input-group">
            <div class="input-group-text">
                <i class="fa fa-building"></i>
            </div>
            <input id="cc_address_1"
                   aria-label="cc_address_1"
                   class="form-control form-control-lg <?php if (isset($errors['address_1'])) { echo 'is-invalid'; } ?>"
                   placeholder="<?php echo_html2view($entry_address_1); ?>"
                   name="address_1"
                   type="text"
                   required
                   maxlength="64"
                   value="<?php echo $form['address_1']->value; ?>">
        </div>
    </div>

    <div class="form-group col-12 mb-3">
        <div class="input-group">
            <div class="input-group-text">
                <i class="fa fa-hotel"></i>
            </div>
            <input id="cc_address_2"
                   aria-label="cc_address_2"
                   class="form-control form-control-lg <?php if (isset($errors['address_2'])) { echo 'is-invalid'; } ?>"
                   placeholder="<?php echo_html2view($entry_address_2); ?>"
                   name="address_2"
                   type="text"
                   maxlength="64"
                   value="<?php echo $form['address_2']->value; ?>">
        </div>
    </div>

    <div class="row mb-3 ">
        <div class="form-group col-12 col-sm-6 mb-3 mb-sm-0">
            <div class="input-group">
                <div class="input-group-text">
                <i class="fa fa-city"></i>
                </div>
                <input aria-label="city"
                       class="form-control form-control-lg <?php if (isset($errors['city'])) { echo 'is-invalid'; } ?>"
                       placeholder="<?php echo_html2view($entry_city); ?>"
                       name="city"
                       type="text"
                       maxlength="32"
                       required
                       value="<?php echo $form['city']->value; ?>">
            </div>
        </div>
        <div class="form-group col-12 col-sm-6">
            <div class="input-group">
                <div class="input-group-text">
                <i class="fa fa-bars"></i>
                </div>
                <input aria-label="postcode"
                       class="form-control form-control-lg <?php if (isset($errors['postcode'])) { echo 'is-invalid'; } ?>"
                       placeholder="<?php echo_html2view($entry_postcode); ?>"
                       name="postcode"
                       type="text"
                       maxlength="10"
                       minlength="3"
                       required
                       value="<?php echo $form['postcode']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="form-group col-12 col-sm-6 mb-3 mb-sm-0">
            <div class="input-group">
                <div class="input-group-text">
                    <i class="fa fa-bars"></i>
                </div>
                <select aria-label="zone" required class="form-select form-select-lg <?php if (isset($errors['zone'])) { echo 'is-invalid'; } ?>"
                        id="zone_id" name="zone_id"></select>
            </div>
        </div>
        <div class="form-group col-12 col-sm-6">
            <div class="input-group">
                <div class="input-group-text">
                    <i class="fa fa-map"></i>
                </div>
                <select required aria-label="country" class="form-select form-select-lg  <?php if (isset($errors['country'])) { echo 'is-invalid';} ?>"
                        id="country_id" name="country_id">
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
            </div>
        </div>
    </div>

    <div class="form-group col-12 mb-3">
        <div class="input-group has-validation">
            <div class="input-group-text">
                <i class="fa fa-envelope"></i>
            </div>
            <input aria-label="email"
                   class="form-control form-control-lg"
                   placeholder="<?php echo_html2view($this->language->get('fast_checkout_email_placeholder')); ?>"
                   id="cc_email"
                   name="cc_email"
                   type="email"
                   maxlength="96"
                   value="<?php echo $customer_email; ?>"
                   required>
        </div>
    </div>

    <div class="form-group col-12 mb-3">
        <div class="input-group">
            <div class="input-group-text">
                <i class="fa fa-phone"></i>
            </div>
            <input aria-label="phone"
                   class="form-control form-control-lg"
                   placeholder="<?php echo_html2view($this->language->get('fast_checkout_telephone_placeholder')); ?>"
                   id="telephone"
                   name="telephone"
                   type="text"
                   <?php
                        echo $this->config->get('config_phone_validation_pattern')
                            ? 'pattern="'.trim(trim(trim($this->config->get('config_phone_validation_pattern'),'/'),'^'),'$').'"'
                            : 'maxlength="32"'; ?>
                   <?php echo $require_telephone ? 'required' : ''; ?>
                   value="<?php echo $customer_telephone; ?>" <?php echo $loggedin ? 'readonly' : ''; ?>>
        </div>
    </div>

    <?php echo $this->getHookVar('address_attributes'); ?>
    <?php if ($type == 'payment' && $this->cart->hasShipping()) { ?>
        <div class="form-group d-flex justify-content-end">
            <div class="form-check mb-3">
              <input name="same_as_shipping" class="form-control-sm form-check-input me-2 px-3" type="checkbox" checked="checked" value="1" id="same_as_shipping">
              <label class="form-check-label fs-4" for="same_as_shipping">
                  <?php echo $fast_checkout_text_same_as_shipping_address; ?>
              </label>
            </div>
        </div>
    <?php } ?>


    <button type="submit" class="col-12 btn btn-primary btn-lg btn-block btn-pay">
        <?php echo $fast_checkout_text_enter_address.' '.$text_mmmm; ?>
    </button>
    <?php echo $this->getHookVar('address_form_bottom'); ?>
</fieldset>

</form>
<script type="text/javascript">
    <?php $cz_url = $this->html->getSecureURL('common/zone', '&zone_id='.$zone_id); ?>
    var cntry = $('#country_id');
    cntry.change(function () {
        $('select[name=\'zone_id\']').load(
            '<?php echo $cz_url;?>&country_id=' + $(this).val(),
            function(){
                let opts = $('select[name=\'zone_id\']>option[value]');
                if(opts.length === 1 ){
                    $(this).val(opts.first().attr('value'));
                }
            });
    });
    cntry.change();
</script>

