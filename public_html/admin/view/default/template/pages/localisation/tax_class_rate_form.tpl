<?php if ( !empty($error['warning']) ) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
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
	    <?php echo $form_language_switch; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $form['form_open']; ?>
	<div class="fieldset">
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">
		<table class="form">
			<tr>
				<td><?php echo ${'entry_location'}; ?></td>
				<td>
					<?php echo $form['fields']['location']; ?>
					<?php if (!empty($error['location'])) { ?>
						<div class="field_err"><?php echo $error['location']; ?></div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php echo ${'entry_zone'}; ?></td>
				<td>
					<?php echo $form['fields']['zone']; ?>
					<?php if (!empty($error['zone'])) { ?>
						<div class="field_err"><?php echo $error['zone']; ?></div>
					<?php } ?>
				</td>
			</tr>		
			<tr>
				<td><?php echo ${'entry_rate'}; ?></td>
				<td>
					<?php echo $form['fields']['rate_prefix']; ?>
					<?php echo $form['fields']['rate']; ?>
					<?php if (!empty($error['rate'])) { ?>
						<div class="field_err"><?php echo $error['rate']; ?></div>
					<?php } ?>
				</td>
			</tr>	
			<tr>
				<td><?php echo ${'entry_tax_rate_threshold'}; ?></td>
				<td>
					<?php echo $form['fields']['threshold_condition']; ?>
					<?php echo $form['fields']['threshold']; ?>
					<?php if (!empty($error['threshold'])) { ?>
						<div class="field_err"><?php echo $error['threshold']; ?></div>
					<?php } ?>
				</td>
			</tr>	
			<tr>
				<td><?php echo ${'entry_description'}; ?></td>
				<td>
					<?php echo $form['fields']['description']; ?>
					<?php if (!empty($error['description'])) { ?>
						<div class="field_err"><?php echo $error['description']; ?></div>
					<?php } ?>
				</td>
			</tr>	
			<tr>
				<td><?php echo ${'entry_priority'}; ?></td>
				<td>
					<?php echo $form['fields']['priority']; ?>
					<?php if (!empty($error['priority'])) { ?>
						<div class="field_err"><?php echo $error['priority']; ?></div>
					<?php } ?>
				</td>
			</tr>	
				
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