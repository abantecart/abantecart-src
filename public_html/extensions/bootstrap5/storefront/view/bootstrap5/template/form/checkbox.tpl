<div class="input-group">
    <input type="checkbox" class="form-check <?php echo $style; ?>"
           name="<?php echo $name ?>" id="<?php echo $id ?>"
           value="<?php echo $value ?>" <?php echo ($checked ? 'checked="checked"':'') ?> <?php echo $attr ?> <?php if ( $required ) { echo 'required'; }?>/>

    <?php if ( $label_text ){ ?>
        <label class="form-check-label ms-3" for="<?php echo $id;?>">
            <?php echo $label_text;?>
          </label>
    <?php }
    if ( $required ){ ?>
        <span class="ms-3 text-danger">*</span>
    <?php } ?>
</div>