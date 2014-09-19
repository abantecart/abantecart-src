
    <select name="<?php echo $name ?>"
			id="<?php echo $id ?>" <?php echo $attr ?>
			class="form-control aselect <?php echo $style; ?>"
			data-placeholder="<?php echo $placeholder ?>">
	<?php foreach ( $options as $v => $text ) { ?>
            <option value="<?php echo $v ?>"
					<?php echo (in_array($v, $value) ? ' selected="selected" ':'') ?>
					<?php echo (in_array((string)$v, (array)$disabled, true) ? ' disabled="disabled" ':'') ?>
					data-orgvalue="<?php echo (in_array($v, $value) ? 'true':'false') ?>"><?php echo $text ?></option>
	<?php } ?>
	</select>

<?php if ( $required == 'Y' ){ ?>
	<span class="input-group-addon"><span class="required">*</span></span>
<?php } ?>

	<select name="<?php echo $zone_field_name; ?>" id="<?php echo $id ?>_zones" class="form-control aselect <?php echo $style; ?>"></select>
	<?php if ( $required == 'Y' ){ ?>
		<span class="input-group-addon"><span class="required">*</span></span>
	<?php }?>

<script type="text/javascript">
	<?php 
		if ($submit_mode == 'id') {
			$selector = "&country_id=";
		} else {
			$selector = "&country_name=";		
		}
	?>

	$('#<?php echo $id ?>').change( function(){
		var def_value = '<?php echo '&zone_id='.key($zone_value); ?>';

		if($('#<?php echo $id ?>_zones > option').length==0){
			var zone_id_val = null
		}else{
			var zone_id_val =  '&'+$('#<?php echo $id ?>_zones').serialize();
		}

		$.getJSON('<?php echo $url; ?><?php echo $selector ?>' + $('#<?php echo $id ?>').val()+ (zone_id_val!=null ? zone_id_val : def_value),
			function(response){
				buildZones(response.options);
			}
		);
	});
	$('#<?php echo $id ?>').change();

	var buildZones = function(options){
		$('#<?php echo $id ?>_zones').html('');
		for(var k in options){
			$('#<?php echo $id ?>_zones').append('<option value="'+k+'" '+(options[k].hasOwnProperty('selected') ? 'selected="selected"':'')+'>'+options[k]['value']+'</option>')
		}
	}

</script>