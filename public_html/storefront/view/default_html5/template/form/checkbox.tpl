<?php if ( $label_text ){ ?>
	<label class="checkbox" for="<?php echo $id ?>">
<?php } ?>
<input type="checkbox" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>" <?php echo ($checked ? 'checked="checked"':'') ?> <?php echo ($style ? 'class="'.$style.'"':'') ?> <?php echo $attr ?> />
<?php if ( $label_text ){ ?>
	<?php echo $label_text; ?></label>
<?php } ?>
<?php if ( $required == 'Y' ){ ?>
<span class="add-on required">*</span>
<?php } ?>
