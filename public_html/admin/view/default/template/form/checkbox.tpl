<?php
 $is_switch = is_int(strpos($style,'btn_switch')) ? true : false;
?>
<input type="checkbox"
           name="<?php echo $name ?>"
           id="<?php echo $id ?>"
           value="<?php echo $value ?>" 
           class="acheckbox <?php echo $style; ?>"
		   <?php echo ( ($checked) ? 'checked="checked"':'' ) ?>
           data-orgvalue="<?php echo ($checked ? 'true':'false') ?>"
		   <?php echo $attr ?>
           <?php echo ($is_switch ? 'style="opacity: 0;"' : ''); ?>
/>
<?php if ( $label_text ){ ?>
<label class="pull-left" for="<?php echo $id ?>"><?php echo $label_text; ?></label>
<?php } ?>

<?php if ( $required == 'Y' || !empty ($help_url) ) { ?>
	<span class="input-group-addon">
	<?php if ( $required == 'Y') { ?> 
		<span class="required">*</span>
	<?php } ?>	

	<?php if ( !empty ($help_url) ) { ?>
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
	<?php } ?>	
	</span>
<?php } ?>