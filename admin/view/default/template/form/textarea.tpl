<span class="textarea_element" >
	<div class="aform"><div class="afield mask2"><div class="tl"><div class="tr"><div class="tc"></div></div></div><div class="cl"><div class="cr"><div class="cc">
    	<textarea name="<?php echo $name ?>" id="<?php echo $id ?>" class="atext <?php echo $style ?>" ovalue="<?php echo $ovalue ?>" <?php echo $attr ?> ><?php echo $value ?></textarea>
    </div></div></div><div class="bl"><div class="br"><div class="bc"></div></div></div></div></div>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>
