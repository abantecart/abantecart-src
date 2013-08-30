<span class="text_element">
<?php if ( $type == 'password' && $has_value == 'Y' ) : ?>
	<span id="<?php echo $id ?>_confirm_default" class="confirm_default">***********</span>
<?php endif; ?>	
	<div class="aform"><div class="afield mask1"><div class="cl"><div class="cr"><div class="cc">
    <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="atext <?php echo $style; ?>" value="<?php echo $value ?>" ovalue="<?php echo $value ?>" <?php echo $attr; ?> placeholder="<?php echo $placeholder ?>" />
    </div></div></div></div></div>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>