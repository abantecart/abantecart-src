<select name="<?php echo $name ?>" id="<?php echo $id ?>" class="form-control aselect <?php echo $style; ?>" data-placeholder="<?php echo $placeholder ?>" <?php echo $attr ?>>
	<?php foreach ( $options as $v => $text ) { ?>
		<option value="<?php echo $v ?>"
			<?php echo (in_array($v, $value) ? ' selected="selected" ':'') ?>
			<?php echo (in_array((string)$v, (array)$disabled, true) ? ' disabled="disabled" ':'') ?>
			data-orgvalue="<?php echo (in_array($v, $value) ? 'true':'false') ?>"><?php echo $text ?></option>
	<?php } ?>
</select>

<?php if ( $required == 'Y' ){ ?>
	<span class="input-group-addon"><span class="required">*</span></span>
<?php } else { ?>
	<span class="input-group-addon"></span>
<?php } ?>
<select name="<?php echo $zone_field_name; ?>" id="<?php echo $id ?>_zones" class="form-control aselect ml10 <?php echo $style; ?>"></select>
<?php if ( $required == 'Y' ){ ?>
	<span class="input-group-addon"><span class="required">*</span></span>
<?php } else { ?>
	<span class="input-group-addon"></span>
<?php } ?>

<script type="text/javascript">	
	<?php 
		if ($submit_mode == 'id') {
			$selector = "&country_id=";
		} else {
			$selector = "&country_name=";		
		}
	?>

	$(document).on("change", "#<?php echo $id ?>", function(){
		var def_contry = '<?php echo key($value); ?>';
		var def_zone_value = '<?php echo '&zone_id='.key($zone_value); ?>';
		var sel_country = $('#<?php echo $id ?>').val();
		if($('#<?php echo $id ?>_zones > option').length == 0){
			var zone_id_val = def_zone_value;
		} else if (sel_country == def_contry) {
			//if original country selected use original zone (reset)
			var zone_id_val = def_zone_value;
		} else {
			var zone_id_val =  '&'+$('#<?php echo $id ?>_zones').serialize();
		}
		//reload zones
		$.getJSON('<?php echo $url; ?><?php echo $selector ?>'+sel_country+zone_id_val,
			function(response){
				buildZones(response.options);
			}
		);
	});

	//fire event on load
	$('#<?php echo $id ?>').change();

	var buildZones = function(options){
		$('#<?php echo $id ?>_zones').html('');
		for(var k in options){
			var selected = 'data-orgvalue="false"';
			if (options[k].hasOwnProperty('selected')) {
				selected = 'selected="selected" data-orgvalue="true"';
			}
			$('#<?php echo $id ?>_zones').append('<option value="'+k+'" '+selected+'>'+options[k]['value']+'</option>')
		}
	}

</script>