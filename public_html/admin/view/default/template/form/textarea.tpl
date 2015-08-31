<textarea 
	class="form-control atext <?php echo $style ?>" 
	name="<?php echo $name ?>" 
	placeholder="<?php echo $placeholder; ?>" 
	id="<?php echo $id ?>" 
	data-orgvalue="<?php echo $ovalue ?>" 
	<?php echo $attr ?> 
>
<?php echo $value ?>
</textarea>

<?php if ( $required == 'Y' || $multilingual  || !empty ($help_url) ) { ?>
	<span class="input-group-addon">
	<?php if ( $required == 'Y') { ?> 
		<span class="required">*</span>
	<?php } ?>	

	<?php if ( $multilingual ) { ?>
	<span class="multilingual"><i class="fa fa-flag"></i></span>	
	<?php } ?>	

	<?php if ( !empty ($help_url) ) { ?>
	<span class="help_element"><a href="<?php echo $help_url; ?>" target="new"><i class="fa fa-question-circle fa-lg"></i></a></span>
	<?php } ?>	
	</span>
<?php } ?>