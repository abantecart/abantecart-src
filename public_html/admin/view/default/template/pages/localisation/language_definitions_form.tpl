<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_language"><?php echo $heading_title; ?></div>
	  <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
		<?php echo $form_language_switch; ?>
		<?php if ($view_mode) { ?> 
		<div class="help_element"><a href="<?php echo $view_mode; ?>"><img src="<?php echo $template_dir; ?>image/language.png"/></a></div>
		<?php } ?> 
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
		<?php if ( $name == 'language_definition_id' ) { ?>

		<?php } else if (is_array($field)) { ?>
            <?php foreach ($field as $lang_id => $f) { ?>
            <tr>
				<td>
                    <img src="<?php echo $languages[$lang_id]['image']; ?>" alt="<?php echo $languages[$lang_id]['name']; ?>" />
                    <?php echo ${'entry_'.$name}; ?>
                </td>
				<td class="ml_field">
					<?php echo $f; ?>
					<?php echo $form['fields']['language_definition_id'][$lang_id]; ?>
					<?php if (!empty($error[$name][$lang_id])) { ?>
						<div class="field_err"><?php echo $error[$name][$lang_id]; ?></div>
					<?php } ?>
				</td>
			</tr>
            <?php } ?>
		<?php } else { ?>
			<tr>
				<td><?php echo ${'entry_'.$name}; ?></td>
				<td class="ml_field">
					<?php echo $field; ?>
					<?php if (!empty($error[$name])) { ?>
						<div class="field_err"><?php echo $error[$name]; ?></div>
					<?php } ?>
				</td>
			</tr>
        <?php } ?>
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

<?php
if(!$language_definition_id){?>
<div id="dialog"></div>
<script type="text/javascript">
	$('#definitionFrm_block, #definitionFrm_language_key').blur(function(){
		if($('#definitionFrm_language_key').val()==''){
			return false;
		}

		$.ajax(
			{
				url:'<?php echo $check_url; ?>',
				type:'POST',
				data: $('#definitionFrm').serializeArray(),
				dataType:'json',
				success:function (data) {
					if(data.error.length>0){
						$('#dialog').html(data.error);
						$('#dialog').dialog({
							title: 'Error!',
							width: 300,
							height: 200,
							resizable: false,
							modal: false,
							buttons: { "close": function() { $(this).dialog("destroy"); }	}
						});

					}
				},
				error:function (req, status, msg) {
				}
			});

	});
</script>

<?php } ?>