<?php //NOTE: For maltivalue, need to pass attribute multiple="multiple" ?>
<select id="<?php echo $id ?>" name="<?php echo $name ?>" data-placeholder="<?php echo $placeholder; ?>" class="chosen-select form-control aselect <?php echo ($style ? $style:''); ?>" style="display: none;" <?php echo $attr; ?>>
<?php 
	foreach ( $options as $v => $text ) { 	
	$check_id = preg_replace('/[^a-zA-Z0-9_]/', '', $id . $v);
?>
<option id="<?php echo $check_id ?>" value="<?php echo $v ?>" <?php echo (in_array($v, $value) ? ' selected="selected" ':'') ?> data-orgvalue="<?php echo (in_array($v, $value) ? 'true':'false') ?>"><?php echo $text ?></option>
<?php } ?>
</select>

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