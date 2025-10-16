<?php
$address_form['form_open']->style .= ' needs-validation';
//block native browser validation messages
$address_form['form_open']->attr .= ' novalidate ';
echo $address_form['form_open'];
$afUsed = $pairFields = $addressFormFields = [];
//build field pairs based on html-attribute data-pair-with
foreach($address_form['fields'] as $fieldKey => $field){
    if(in_array($fieldKey, $afUsed)){ continue; }
    if (preg_match('/data-pair-with="([^"]+)"/', $field->attr, $matches)) {
        $pairFields[$fieldKey.'~'.$matches[1]] = [
                $fieldKey,
                $matches[1]
        ];
        $afUsed[] = $fieldKey;
        $afUsed[] = $matches[1];
    }
}

$afUsed = [];
//convert field array based on pair fields.
// If array value is array - a "pair" fields, otherwise  - one field per line
foreach($address_form['fields'] as $fieldKey => $field){
    if(in_array($fieldKey, $afUsed)){ continue; }
    $isPairFields = [];
    foreach($pairFields as $pair){
        if(in_array($fieldKey, $pair)){
            $isPairFields = $pair;
            break;
        }
    }
    if($isPairFields) {
        foreach ($isPairFields as $fKey) {
            $addressFormFields[$fieldKey][$fKey] = $address_form['fields'][$fKey];
            $afUsed[] = $fKey;
        }
    }else{
        $addressFormFields[$fieldKey] = $field;
        $afUsed[] = $fieldKey;
    }
}
unset($afUsed,$isPairFields,$pairFields);
?>
<div id="pay_error_container">
    <?php
    include($this->templateResource('/template/responses/checkout/alerts.tpl'));
    ?>
</div>
<fieldset>
    <?php echo $this->getHookVar('address_form_top'); ?>
    <label class="visible-xs text-center text-uppercase mb-2"><?php echo $this->language->get('text_'.$type.'_address'); ?></label>
    <?php foreach($addressFormFields as $fieldKey => $field){
        if(is_array($field)){ ?>
            <div class="row mb-0 mb-sm-3">
            <?php foreach($field as $fKey => $fld){ ?>
                <div class="form-group col-12 mb-3 col-sm-6 mb-sm-0">
                    <?php
                    $fld->style .= ' form-control-lg form-select-lg  '.($errors[$fKey] ? 'is-invalid' : '');
                    $fld->placeholder = html2view($fld->display_name);
                    if($errors[$fKey]){
                        $fld->error_text .= $errors[$fKey];
                    }
                    echo $fld; ?>
                </div>
            <?php }
        }else{ ?>
        <div class="row mb-3">
            <div class="form-group col-12">
                <?php
                $field->style .= ' form-control-lg '.($errors[$fieldKey] ? 'is-invalid' : '');
                $field->placeholder = html2view($field->display_name);
                if($errors[$fieldKey]){
                    $field->error_text .= $errors[$fieldKey];
                }
                echo $field; ?>
            </div>
            <?php }?>
        </div>
    <?php }
    echo $this->getHookVar('address_attributes');
    if (
            $type == 'payment'
            && $this->cart->hasShipping()
            && !$this->config->get('fast_checkout_payment_address_equal_shipping')
    ){ ?>
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
        <?php echo $fast_checkout_text_enter_address; ?>
    </button>
    <?php echo $this->getHookVar('address_form_bottom'); ?>
</fieldset>
</form>