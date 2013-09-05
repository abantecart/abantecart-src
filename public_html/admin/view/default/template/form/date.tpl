<span class="text_element" >
	<div class="aform"><div class="afield mask1"><div class="cl"><div class="cr"><div class="cc">
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value; ?>" <?php echo $attr; ?> class="atext <?php echo $style; ?>" />
    </div></div></div></div></div>    
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
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