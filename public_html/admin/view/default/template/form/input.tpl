<?php if ( $type == 'password' && $has_value == 'Y' ) : ?>
	<span id="<?php echo $id ?>_confirm_default" class="confirm_default">***********</span>
<?php endif; ?>	
    <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="form-control atext <?php echo $style; ?>" value="<?php echo $value ?>" ovalue="<?php echo $value ?>" <?php echo $attr; ?> placeholder="<?php echo $placeholder ?>" />
<?php if ( $required == 'Y' ) : ?>
	<span class="input-group-addon required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
	<span class="help_block"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>