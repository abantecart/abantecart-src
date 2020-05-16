<?php echo $form['form_open']; ?>
<div id="pay_error_container">
    <?php if ($info) { ?>
        <div class="info alert alert-info"><i class="fa fa fa-check fa-fw"></i> <?php echo $info; ?></div>
    <?php } ?>
    <?php if ($error) { ?>
        <div class="alert alert-danger" role="alert"><i class="fa fa-exclamation fa-fw"></i> <?php echo $error; ?></div>
    <?php } ?>
</div>
<fieldset>
    <?php echo $this->getHookVar('address_form_top'); ?>
    <label class="visible-xs text-center text-uppercase"><?php echo $type.' '.$fast_checkout_text_address; ?></label>
    <div class="row">
        <div class="form-group col-xxs-12 col-xs-6 <?php if ($errors['firstname']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-user"></i>
                <input class="form-control input-lg" placeholder="<?php echo $entry_firstname; ?>" name="firstname"
                       type="text" value="<?php echo $form['firstname']->value; ?>">
            </div>
        </div>
        <div class="form-group col-xxs-12 col-xs-6 <?php if ($errors['lastname']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-user"></i>
                <input class="form-control input-lg" placeholder="<?php echo $entry_lastname; ?>" name="lastname"
                       type="text" value="<?php echo $form['lastname']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 <?php if ($errors['address_1']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-building"></i>
                <input id="cc_address_1" class="form-control input-lg" placeholder="<?php echo $entry_address_1; ?>"
                       name="address_1" type="text" value="<?php echo $form['address_1']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 <?php if ($errors['address_2']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-building-o"></i>
                <input id="cc_address_2" class="form-control input-lg" placeholder="<?php echo $entry_address_2; ?>"
                       name="address_2" type="text" value="<?php echo $form['address_2']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 col-xs-6 <?php if ($errors['city']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-institution"></i>
                <input class="form-control input-lg" placeholder="<?php echo $entry_city; ?>" name="city" type="text"
                       value="<?php echo $form['city']->value; ?>">
            </div>
        </div>
        <div class="form-group col-xxs-12 col-xs-6 <?php if ($errors['postcode']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-bars"></i>
                <input class="form-control input-lg" placeholder="<?php echo $entry_postcode; ?>" name="postcode"
                       type="text" value="<?php echo $form['postcode']->value; ?>">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xxs-12 col-xs-6 <?php if ($errors['zone']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-bars"></i>
                <select class="form-control input-lg" id="zone_id" name="zone_id"></select>
                <div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
            </div>
        </div>
        <div class="form-group col-xxs-12 col-xs-6 <?php if ($errors['country']) {
            echo 'has-error';
        } ?>">
            <div class="left-inner-addon">
                <i class="fa fa-map"></i>
                <select class="form-control input-lg" id="country_id" name="country_id">
                    <?php
                    if (count($form['country_id']->options)) {
                        foreach ($form['country_id']->options as $id => $name) {
                            $current = '';
                            if ($id == $form['country_id']->value) {
                                $current = ' selected ';
                            }
                            echo '<option value="'.$id.'" '.$current.'>'
                                .$name
                                .'</options>';
                        }
                    }
                    ?>
                </select>
                <div class="select_arrow"><i class="fa fa-angle-double-down"></i></div>
            </div>
        </div>
    </div>

	<div class="row">
		<div class="form-group col-xxs-12">
			<div class="left-inner-addon">
				<i class="fa fa-envelope"></i>
				<input class="form-control input-lg"
					   placeholder="<?php echo $this->language->get('fast_checkout_email_placeholder'); ?>"
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
				<input class="form-control input-lg"
					   placeholder="<?php echo $this->language->get('fast_checkout_telephone_placeholder'); ?>"
					   id="telephone"
					   name="telephone"
					   type="text"
					   value="<?php echo $customer_telephone; ?>" <?php if ($loggedin) {
                    echo 'readonly';
                } ?>>
			</div>
		</div>
	</div>
    <?php } ?>

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
			        <input type="checkbox" name="same_as_shipping" class="hidden" checked="checked"/>
			    </span>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <hr>
    <?php } ?>


    <button type="submit" class="btn btn-primary btn-lg btn-block btn-pay"><?php echo $fast_checkout_text_enter_address
            .' '.$text_mmmm; ?></button>
    <?php echo $this->getHookVar('address_form_bottom'); ?>
</fieldset>


</form>

<script type="text/javascript">
    <?php $cz_url = $this->html->getURL('common/zone', '&zone_id='.$zone_id); ?>
    $('#country_id').change(function () {
        $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $(this).val());
    });
    $('select[name=\'zone_id\']').load('<?php echo $cz_url;?>&country_id=' + $('#country_id').val());
</script>
