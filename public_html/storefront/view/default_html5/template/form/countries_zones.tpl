<select name="<?php echo $name ?>[]" id="<?php echo $id ?>" <?php echo $attr ?> <?php echo ($style ? 'class="'.$style.'"':''); ?>>
	<?php foreach ( $options as $v => $text ) { ?>
            <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$value) ? ' selected="selected" ':'') ?> >
			<?php echo $text ?>
			</option>
	<?php } ?>
</select>
<?php if ( $required == 'Y' ) : ?>
<span class="required">*</span>
<?php endif; ?>
<select name="<?php echo $name ?>_zones[]" id="<?php echo $id ?>_zones" <?php echo ($style ? 'class="'.$style.'"':''); ?>>
	<?php foreach ( $zone_options as $v => $text ) { ?>
	            <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$zone_value) ? ' selected="selected" ':'') ?> >
				<?php echo $text ?>
				</option>
		<?php } ?>
</select>
<script type="text/javascript">
	<?php 
		if ($submit_mode == 'id') {
			$selector = "&country_id=";
		} else {
			$selector = "&country_name=";		
		}
	?>

	$('#<?php echo $id ?>').change( function(){
		$('#<?php echo $id ?>_zones').load('<?php echo $url; ?><?php echo $selector ?>' + encodeURIComponent($(this).val()) + '&'+encodeURIComponent('zone_name=<?php echo $zone_name; ?>'));
	});
</script>