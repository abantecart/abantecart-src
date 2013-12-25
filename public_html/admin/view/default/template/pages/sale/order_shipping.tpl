<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_order"><?php echo $heading_title; ?></div>
      <div class="heading-tabs">
             <?php
             foreach ($tabs as $tab) {
                 echo '<a href="'.$tab['href'].'" '.($tab['active'] ? 'class="active"' : '').'><span>'.$tab['text'].'</span></a>';
             }
             ?>
     	</div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
	<div class="buttons">
		<a href="<?php echo $invoice?>" class="btn_standard" target="_invoice"><?php echo $button_invoice?></a>
	</div>
	<?php echo $form_language_switch; ?>
	</div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $summary_form; ?>
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
					<?php } //if (!empty($error[$name])) { ?>
				</td>
			</tr>
		<?php } //foreach ($form['fields'] as $name => $field)  ?>
		  <tr>
            <td><?php echo $entry_country; ?></td>
            <td>
              <?php echo $form['country_select']; ?>
              <input type="hidden" name="shipping_country" value="<?php echo $shipping_country; ?>" />
            </td>
          </tr>
          <tr>
            <td><?php echo $entry_zone; ?></td>
            <td id="shipping_zone">
            	<?php echo $form['zone_select']; ?>
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
<script type="text/javascript"><!--
jQuery(function($){

	getZones = function(country_id)
	{
		if ( !country_id )
		{
			country_id = '<?php echo $shipping_country_id; ?>';
		}

		$.ajax(
		{
			url: '<?php echo $common_zone; ?>&country_id='+ country_id +'&zone_id=<?php echo $shipping_zone_id; ?>&type=shipping_zone',
			type: 'GET',
			dataType: 'json',
			success: function(data)
			{
				result = data;
				showZones(data);
			}
		});
	}

	showZones = function(data)
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

		var selectObj = $('#orderFrm_shipping_zone_id');

		selectObj.html(options);
		var selected_name = $('#orderFrm_shipping_zone_id :selected').text();
		selectObj.parent().find('span').text(selected_name);
		selectObj.after('<input id="shipping_zone_name" name="shipping_zone" value="' + selected_name + '" type="hidden" />');

	}

	getZones();

	$('#orderFrm_shipping_zone_id').live('change', function() {
		$('#shipping_zone_name').val($('#shipping_zone select :selected').text());
	});

	$('#orderFrm_shipping_country_id').change(function()
	{
		getZones($(this).val());
		$('#shipping_zone select').aform({triggerChanged: false})

	});


    $('#orderFrm').submit(function(){
        $('input[name="shipping_country"]', this).val($('#shipping_country option:selected').text());
        $('input[name="shipping_zone"]', this).val($('#shipping_zone select option:selected').text());
    });
});
-->
</script>