<span class="text_element" >
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="" <?php echo $attr; ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>/>
</span>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<script>
	$('#<?php echo $id ?>').datepicker({dateFormat:'yy-mm-dd'});
</script>