<span class="checkbox_element">
<div class="aform"><div class="afield acheckbox <?php if($style == 'btn_switch') { ?>aswitcher<?php } ?> <?php echo ($checked ? 'checked':'') ?>"><span>
    <input type="checkbox"
           name="<?php echo $name ?>"
           id="<?php echo $id ?>"
           value="<?php echo $value ?>" <?php echo ($style ? 'class="'.$style.'"':''); ?>
		   <?php echo (($checked || $style == 'btn_switch') ? 'checked="checked"':'') ?>
           ovalue="<?php echo ($checked ? 'true':'false') ?>"
		   <?php echo $attr ?>
           style="opacity: 0;" />
    </span></div></div>
</span>
<?php if ( $label_text ){ ?>
<label for="<?php echo $id ?>"><?php echo $label_text; ?></label>
<?php }
 if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<?php if ( !empty ($help_url) ) : ?>
<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></span>
<?php endif; ?>