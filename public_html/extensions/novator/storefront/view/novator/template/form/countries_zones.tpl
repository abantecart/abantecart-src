<div class="input-group mb-3">
	<select name="<?php echo $name ?>[]"
            id="<?php echo $id ?>"
            class="form-select <?php echo $style; ?>"
            data-placeholder="<?php echo $placeholder ?>"
            <?php echo $attr ?>>
	<?php foreach ( $options as $v => $text ) { ?>
        <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$value) ? ' selected="selected" ':'') ?> >
            <?php echo $text ?>
        </option>
	<?php } ?>
	</select>
<?php if ( $required ){ ?>
    <span class="input-group-text text-danger">*</span>
<?php } ?>
</div>
<div class="input-group ">
	<select name="<?php echo $name ?>_zones[]"
            id="<?php echo $id ?>_zones"
            class="form-select <?php echo $style; ?>"
            data-placeholder="<?php echo $placeholder ?>">
<?php foreach ( $zone_options as $v => $text ) { ?>
        <option value="<?php echo $v ?>" <?php echo (in_array($v, (array)$zone_value) ? ' selected="selected" ':'') ?> >
            <?php echo $text ?>
        </option>
<?php } ?>
	</select>
<?php if ( $required ){ ?>
    <span class="input-group-text text-danger">*</span>
<?php } ?>
</div>
<script type="text/javascript">
<?php
	$selector = $submit_mode == 'id' ? "&country_id=" : "&country_name=";
?>

	$('#<?php echo $id ?>').change( function(){
		$('#<?php echo $id ?>_zones').load(
            '<?php echo $url; ?><?php echo $selector ?>'
            + encodeURIComponent($(this).val())
            + '&'+encodeURIComponent('zone_name=<?php echo $zone_name; ?>')
        );
	});
</script>