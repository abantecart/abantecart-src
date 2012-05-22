<span class="passwordset_element">

<span class="text_element">
<div class="aform"><div class="afield mask1"><div class="cl"><div class="cr"><div class="cc">
    <input class="atext <?php echo $style; ?>" type="password" name="<?php echo $name ?>" id="<?php echo $id ?>"
           value="<?php echo $value ?>" ovalue="<?php echo $value ?>" <?php echo $attr; ?> autocomplete="off" />
</div></div></div></div></div>           
	<?php if ( $required == 'Y' ) : ?>
	<span class="required">*</span>
	<?php endif; ?>
</span>

<span id="<?php echo $id ?>_strength" class="password_strength"></span>

<span class="text_element">
	<span id="<?php echo $id ?>_confirm_default" class="confirm_default"><?php echo $text_confirm_password; ?></span>
<div class="aform"><div class="afield mask1"><div class="cl"><div class="cr"><div class="cc">
    <input class="atext <?php echo $style; ?>" type="password" name="<?php echo $name ?>_confirm" id="<?php echo $id ?>_confirm"
           value="" <?php echo $attr; ?> />
</div></div></div></div></div> 
	<?php if ( $required == 'Y' ) : ?>
	<span class="required">*</span>
	<?php endif; ?>
</span>

</span>
<br class="clr_both" />