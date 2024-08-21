<?php if(!$no_wrapper){?>
<div class="input-group h-100 form-check form-check-inline">
<?php } ?>
<div class="d-flex align-items-center">
    <input type="checkbox" class="form-check-input <?php echo $style; ?>"
           name="<?php echo $name ?>" id="<?php echo $id ?>"
           value="<?php echo $value ?>" <?php echo ($checked ? 'checked="checked"':'') ?> <?php echo $attr ?> <?php if ( $required ) { echo 'required'; }?>/>
<?php if ( $label_text ){ ?>
    <label class="mx-1" for="<?php echo $id;?>">
        <?php echo $label_text;?>
    </label>
    <?php } ?>
</div>
<?php if ( $required ) { ?>
    <span class="input-group-text border-0 text-danger">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php } ?>