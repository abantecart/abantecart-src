<?php
?>
<select name="<?php echo $name ?>" id="<?php echo $id ?>" class="form-control <?php echo $style; ?>" data-placeholder="<?php echo $placeholder ?>" <?php echo $attr ?>>
	<?php
	if(!current($value) && $placeholder){ ?>
		<option value=""><?php echo $placeholder; ?></option>
	<?php
	}
	foreach ( $options as $v => $text ) { ?>
	    <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$value) ? ' selected="selected" ':'') ?>>
	        <?php echo $text ?>
	    </option>
	<?php } ?>
</select>
<?php if ( $required == 'Y' ) { ?>
<span class="input-group-addon"><span class="required">*</span></span>
<?php } ?>