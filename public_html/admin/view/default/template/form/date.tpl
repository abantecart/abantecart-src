<input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value; ?>" data-orgvalue="<?php echo $value; ?>" <?php echo $attr; ?> class="form-control adate <?php echo $style; ?>" placeholder="<?php echo $placeholder ?>" />

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

<script type="text/javascript">
	$('#<?php echo $id ?>').datepicker({dateFormat:'<?php echo $dateformat ?>'});
<?php if ( $highlight == 'pased' ) : ?>
	var startdate = $('#<?php echo $id ?>').val();
	if( (new Date(startdate).getTime() < new Date().getTime())) {
		$('#<?php echo $id ?>').closest('.afield').addClass('focus');	
	}
<?php endif; ?>
<?php if ( $highlight == 'future' ) : ?>
	var startdate = $('#<?php echo $id ?>').val();
	if( (new Date(startdate).getTime() > new Date().getTime())) {
		$('#<?php echo $id ?>').closest('.afield').addClass('focus');	
	}
<?php endif; ?>
</script>