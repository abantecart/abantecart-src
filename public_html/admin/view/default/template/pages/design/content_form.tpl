<?php if ($error_warning) { ?>
<div class="warning alert alert-error alert-danger"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading"><?php echo $heading_title; ?></div>
	  <div class="heading-tabs">
		<a href="<?php echo $content_form ?>" class="active"><span><?php echo $tab_form; ?></span></a>
        <?php if ( isset($content_layout) ) { ?>
		<a href="<?php echo $content_layout ?>"><span><?php echo $tab_layout; ?></span></a>
        <?php } ?>
	  </div>
	  <div><a href="<?php echo $layout_edit_href; ?>"></a></div>
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
			<td><?php echo $entry_status; ?></td>
			<td>
				<?php echo $form['fields']['status']; ?>
				<?php if (!empty($error['status'])) { ?>
					<div class="field_err"><?php echo $error['status']; ?></div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $entry_parent; ?></td>
			<td class="ml_field">
				<?php echo $form['fields']['parent']; ?>
				<?php if (!empty($error['parent'])) { ?>
					<div class="field_err"><?php echo $error['parent']; ?></div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $entry_title; ?></td>
			<td class="ml_field">
				<?php echo $form['fields']['title']; ?>
				<?php if (!empty($error['title'])) { ?>
					<div class="field_err"><?php echo $error['title']; ?></div>
				<?php } ?>
			</td>
		</tr>
		<tr style="vertical-align:top;">
			<td><?php echo $entry_description; ?></td>
			<td class="ml_field">
				<?php echo $form['fields']['description']; ?>
				<?php if (!empty($error['description'])) { ?>
					<div class="field_err"><?php echo $error['description']; ?></div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td><?php echo $entry_keyword; ?></td>
			<td class="ml_field">
				<?php echo $form['fields']['keyword']; ?>
				<?php if (!empty($error['keyword'])) { ?>
					<div class="field_err"><?php echo $error['keyword']; ?></div>
				<?php } ?>
			</td>
		</tr>
		<tr style="vertical-align:top;">
			<td><?php echo $entry_content; ?></td>
			<td class="ml_ckeditor">
				<?php echo $form['fields']['content']; ?>
				<?php if (!empty($error['content'])) { ?>
					<div class="field_err"><?php echo $error['content']; ?></div>
				<?php } ?>
			</td>
		</tr>
<?php
if($form['fields']['store_id']){
?>
		<tr style="vertical-align:top;">
			<td><?php echo $entry_store; ?></td>
			<td>
				<?php echo $form['fields']['store_id']; ?>
				<?php if (!empty($error['store_id'])) { ?>
					<div class="field_err"><?php echo $error['store_id']; ?></div>
				<?php } ?>
			</td>
		</tr>
<?php } ?>

		<tr>
			<td><?php echo $entry_sort_order; ?></td>
			<td id="sort_orders">
				<?php echo $form['fields']['sort_order']; ?>
				<?php if (!empty($error['sort_order'])) { ?>
					<div class="field_err"><?php echo $error['sort_order']; ?></div>
				<?php } ?>
			</td>
		</tr>
		</table>
	  </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	</div>
	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>
  </div>
</div></div>
<?php echo $prototype_sort_order; ?>
<div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
<script type="text/javascript">
	$('#contentFrm_content').parents('.afield').removeClass('mask2');
	CKEDITOR.replace('contentFrm_content',{
		height: '400px',
		filebrowserBrowseUrl : false,
        filebrowserImageBrowseUrl : '<?php echo $rl; ?>',
        filebrowserWindowWidth : '920',
        filebrowserWindowHeight : '520',
		language: '<?php echo $language_code; ?>'
	});

	$('#contentFrm_generate_seo_keyword').click(function(){
		var seo_name = $('#contentFrm_title').val().replace('%','');
		$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
			$('#contentFrm_keyword').val(data).change();
		});
	});
	var sort_order_clone = $('#sort_orders').find('span.text_element').clone();

	$('#contentFrm_parent_content_id').change(function(){
		var old_values = {};
		var that = this;
		var old_keys = $('#sort_orders').find('input[name^=sort_order]').map(function(){
				    return this.name.replace('sort_order[','').replace(']','')
				}).get();
		var old_vals = $('#sort_orders').find('input[name^=sort_order]').map(function(){
						    return this.value;
						}).get();
		for(var k in old_keys){
			var name = old_keys[k];
			old_values[name] =  old_vals[k];
		}

		var values = $(that).val();
		var html = '';

		$('#sort_orders').html('');
		for(var k in values){
			var temp_clone = sort_order_clone;
			temp_clone.find('input').attr('name','sort_order\['+values[k]+'\]').attr('id','contentFrm_sort_order\['+values[k]+'\]');

			if(old_values[values[k]]){
				temp_clone.find('input').attr('value',old_values[values[k]].replace(/[^0-9]/g,''));
			}

			html += $(this).find('option:selected[value='+values[k]+']').text() + ': <span class="text_element">'+temp_clone.html()+'</span>';
			$('#sort_orders').html(html);

		}
		$('#sort_orders').find('input').aform({triggerChanged: true,
										buttons: {
													save: '<span id="btn_save" class="button3"><span><?php echo $button_save ?></span></span>',
													reset: '<span id="btn_reset" class="button2"><span><?php echo $button_reset ?></span></span>'
												},
										showButtons: <?php echo $content_id ? 'true' : 'false';?>,
										save_url: '<?php echo $update; ?>'}).change();
	});

    $('#sort_orders').find('input[name^=sort_order]').keyup(function(){
        $(this).val($(this).val().replace(/[^0-9]/g,''));
    });



</script>