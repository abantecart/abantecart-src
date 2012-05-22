<?php if ( !empty($error['warning']) ) { ?>
<div class="warning"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_tax"><?php echo $heading_title; ?></div>
	  <div class="heading-tabs">
		<a href="<?php echo $action ?>" <?php echo ( $active == 'details' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_details ?></span></a>
		<?php if (!empty($rates)) { ?>
		  <a href="<?php echo $rates ?>" <?php echo ( $active == 'rates' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_rates ?></span></a>
		<?php } ?>
	</div>
	  <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $form['form_open']; ?>
	<div class="fieldset">
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">
		<table class="form">
		<?php foreach ($form['fields'] as $name => $field) { ?>
			<tr>
				<td><?php echo ${'entry_'.$name}; ?></td>
				<td>
					<?php echo $field; ?>
					<?php if (!empty($error[$name])) { ?>
						<div class="field_err"><?php echo $error[$name]; ?></div>
					<?php } ?>
				</td>
			</tr>
		<?php } //foreach ($form['fields'] as $name => $field)  ?>
		</table>
	  </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	</div><!-- <div class="fieldset"> -->
	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>
<?php if (isset($zone_id)) { ?>
<script type="text/javascript"><!--
var zone_id = '<?php echo $zone_id; ?>';
jQuery(function($){

	toggleZonesSelectbox = function()
	{
		if( !$('#cgFrm_all_zones').is(':checked')) {
			$('#cgFrm_zone_id').show().parents('.aform').show();
		}else{
			$('#cgFrm_zone_id').show().parents('.aform').hide();
		}
	}
	toggleZonesSelectbox();
	$('#cgFrm_zone_id').parents('.aform').css('margin-top',10);

	getZones = function(id, location_id)
	{

		$.ajax(
		{
			url: '<?php echo $common_zone; ?>&location_id=' + location_id +'&zone_id='+ zone_id,
			type: 'GET',
			dataType: 'json',
			success: function(data)
			{
				result = data;
				showZones(id, data);
			}
		});
	}

	showZones = function(id, data)
	{
		var options = '';

		$.each(data['options'], function(i, opt)
		{
			options += '<option value="'+ i +'"';
			if ( opt.selected )
			{
				options += 'selected="selected"';
			}
			options += '>'+ opt.value +'</option>'
		});

		var selectObj = $('#'+ id);

		selectObj.html(options);
		var selected_name = $('#'+ id +' :selected').text();

		selectObj.parent().find('span').text(selected_name);

	}

	getZones('cgFrm_zone_id', $('#cgFrm_location_id').val());


	$('#cgFrm_location_id').change(function(){
		getZones('cgFrm_zone_id', $(this).val());
		$('#cgFrm_zone_id').val('').change();
		
	});

	$('#cgFrm_all_zones').click(function(){
		toggleZonesSelectbox();
	});

});
//--></script>
<?php } ?>