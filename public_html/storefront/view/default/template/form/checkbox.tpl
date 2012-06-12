<span class="checkbox_element">
    <input type="checkbox" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo ($checked ? 'checked="checked"':'') ?> <?php echo $attr ?> />
<?php if ( $label_text ){ ?>
	<label for="<?php echo $id ?>"><?php echo $label_text; ?></label>
<?php } ?>
</span>
<?php if ( $required == 'Y' ){ ?>
<span class="required">*</span>
<?php } ?>