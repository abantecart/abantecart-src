<?php if ( !empty($error['warning']) ) { ?>
<div class="warning"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>


<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_length"><?php echo $heading_title; ?></div>
	  <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
		<?php echo $form_language_switch; ?>
  	    <div class="buttons">
        <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
  		<span class="icon_add">&nbsp;</span>		
    	</div>
    	</a>
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
					<?php if (!is_array($field)) echo $field; ?>
					<?php if (!empty($error[$name])) { ?>
						<div class="field_err"><?php echo $error[$name]; ?></div>
					<?php } ?>
					<?php if ($name == 'element_type') { ?>
						<div id="values">
							<?php if( $child_count == 0 ) { ?>
							<div style="padding-left: 40px;">
							<span style="padding-right: 35px;"><b><?php echo $entry_element_values; ?></b></span>
							<span><b><?php echo $column_sort_order; ?></b></span></div>
							<?php foreach ($form['fields']['attribute_values'] as $atr_val_id => $atr_field) { ?>
								<div class="value">
								<?php echo $atr_field['attribute_value_ids']; ?>
								<?php echo $atr_field['values']; ?>&nbsp;
								<?php echo $atr_field['sort_order']; ?>
								<a class="remove"></a>
								</div>
							<?php } //foreach ?>
							<a class="add"></a>
							<?php } else { ?>
							<div style="padding-left: 10px;">
							<span><b><?php echo $entry_children_attributes; ?></b></span>
								<?php foreach ($children as $child) { ?>
								<div class="value">
									<a href="<?php echo $child['link']; ?>"><?php echo $child['name']; ?></a>
								</div>
								<?php } //foreach ?>
							<?php } //if ?>
						</div>
					<?php } ?>
				</td>
			</tr>
			<?php if ($name == 'attribute_parent') { ?>
				<tr><td colspan="2"><?php echo $text_parent_note; ?></td></tr>
			<?php } ?>

		<?php } //foreach  ?>
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
<script>
jQuery(function($){

	var elements_with_options = [];
	<?php
	foreach ($elements_with_options as $el) {
		echo "elements_with_options.push('$el');\r\n";
	}
	?>

	function addValue(val) {
		var add = $('#values a.add');
		$(add).before($(add).prev().clone());
		$('input', $(add).prev()).val(val);
	}

	$('#values .aform').show();
	$('#values a.remove').live('click', function(){
		var current = $(this);
		if ( $('#values div.value').length > 1 ) {
			if ( $(current).parent().find('input[name^=attribute_value_ids]').val() == 'new' ) {
				$(current).parent().remove();
			}
			else {
				$(current).parent().toggleClass('toDelete');	
			}
		}
	});
	$('#values a.add').live('click', function(){ 
		$(this).before($(this).prev().clone());
		$('input', $(this).prev()).val('');
		$('input[name^=attribute_value_ids]', $(this).prev()).val('new');
		$('input[name^=attribute_value_ids]', $(this).prev()).attr("name","attribute_value_ids[]");
		$('input[name^=values]', $(this).prev()).attr("name","values[]");
		$('input[name^=sort_orders]', $(this).prev()).attr("name","sort_orders[]");
		$('#values .value').last().removeClass('toDelete');
	});

	if ( $.inArray( $('#editFrm_element_type').val(), elements_with_options) > -1 ) {
		$('#values').show();
	}

	$('#editFrm_element_type').change(function(){
		if ( $.inArray( $(this).val(), elements_with_options) > -1 ) {
			$('#values').show();
		} else {
			$('#values').hide();
		}
	});

	$('#editFrm_attribute_parent_id').change(function(){
		var attribute_id = $(this).val();
		if ( attribute_id == '' ) {
			$('#editFrm_attribute_type_id')
				.val('')
				.change()
				.removeAttr('disabled');
			return false;
		}
		$.ajax({
	        url: '<?php echo $get_attribute_type; ?>' + '&attribute_id=' + attribute_id,
	        type: 'GET',
	        dataType: 'json',
	        success: function(json) {
				$('#editFrm_attribute_type_id')
					.val(json)
					.change()
					.attr('disabled', 'disabled');
	        },
	        error: function(jqXHR, textStatus, errorThrown){
	            $('#content').prepend('<div class="error" align="center"><b>'+textStatus+'</b>  '+errorThrown+'</div>');
	        }
	    });

	});
	if ( $('#editFrm_attribute_parent_id').val() != '' ) {
		$('#editFrm_attribute_parent_id').change();
	}

	$('#editFrm').submit(function(){
		$('#values .toDelete input[name^=attribute_value_ids]').val('delete');
		$(":disabled", this).removeAttr('disabled');
	});

});
</script>