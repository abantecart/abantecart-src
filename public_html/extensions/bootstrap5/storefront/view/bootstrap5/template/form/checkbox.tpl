<?php if(!$no_wrapper){?>
<div class="input-group h-100">
<?php } ?>
<div class="form-check-inline d-flex flex-wrap form-control me-0">
    <input type="checkbox" class="form-check <?php echo $style; ?>"
           name="<?php echo $name ?>" id="<?php echo $id ?>"
           value="<?php echo $value ?>" <?php echo ($checked ? 'checked="checked"':'') ?> <?php echo $attr ?> <?php if ( $required ) { echo 'required'; }?>/>
<?php if ( $label_text ){ ?>
    <label class="form-check-label ms-3" for="<?php echo $id;?>">
        <?php echo $label_text;?>
    </label>
    <?php } ?>
</div>
<?php if ( $required ) { ?>
    <span class="input-group-text text-danger">*</span>
<?php }
if(!$no_wrapper){?>
    </div>
<?php } ?>