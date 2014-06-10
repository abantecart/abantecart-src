<?php if( strpos($style,'btn_switch') !== false ) { 
	$switch = true;
?>
<div class="btn-group btn-toggle afield aswitcher <?php echo $style; ?>" <?php echo $attr ?>> 
	<?php if ($checked) { ?>
    <button class="btn btn-primary active">ON</button>
    <button class="btn btn-default">OFF</button>	
	<?php } else { ?>
    <button class="btn btn-default">ON</button>
    <button class="btn btn-primary btn-off active">OFF</button>		
	<?php } ?>
</div>
<input type="hidden"
           name="<?php echo $name ?>"
           id="<?php echo $id ?>"
           value="<?php echo $value ?>" 
           ovalue="<?php echo ($checked ? 'true':'false') ?>"
/>
<?php if ( !empty ($help_url) ) { ?>
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
<?php } ?>
<?php } else { ?>
<input type="checkbox"
           name="<?php echo $name ?>"
           id="<?php echo $id ?>"
           value="<?php echo $value ?>" 
           class="form-control afield acheckbox <?php echo $style; ?>"
		   <?php echo ( ($checked) ? 'checked="checked"':'' ) ?>
           ovalue="<?php echo ($checked ? 'true':'false') ?>"
		   <?php echo $attr ?>
           style="opacity: 0;" />
	<?php if ( $label_text ){ ?>
	<label for="<?php echo $id ?>"><?php echo $label_text; ?></label>
	<?php } ?>
	<?php if ( $required == 'Y' ) { ?>
	<span class="input-group-addon  required">*
	<?php if ( !empty ($help_url) ) { ?>
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
	<?php } ?>
	</span>
	<?php } ?>
<?php } ?>


