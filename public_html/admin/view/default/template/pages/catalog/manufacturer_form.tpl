<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_brand"><?php echo $heading_title; ?></div>
<?php if ($update) { ?>
	<div class="heading-tabs">
		<a href="<?php echo $manufacturer_edit ?>" class="active"><span><?php echo $tab_edit; ?></span></a>
		<a href="<?php echo $manufacturer_layout ?>"><span><?php echo $tab_layout; ?></span></a>
		<?php echo $this->getHookVar('extension_tabs'); ?>
	</div>
<?php } ?>
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
			<tr>
				<td><?php echo $entry_name; ?></td>
				<td><?php echo $form['fields']['name']; ?>
				<?php if ( !empty($error_name) ) { ?>
					<div class="error"><?php echo($error_name) ?></div>
				<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $entry_store; ?></td>
				<td><div class="scrollbox"><?php echo $form['fields']['manufacturer_store']; ?></div></td>
			</tr>
			<tr>
				<td><?php echo $entry_keyword; ?></td>
				<td><?php echo $form['fields']['keyword']; ?></td>
			</tr>
			<tr>
				<td><?php echo $entry_sort_order; ?></td>
				<td><?php echo $form['fields']['sort_order']; ?></td>
			</tr>

		</table>
	  </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
	</div><!-- <div class="fieldset"> -->


    <?php if ( !empty($update) ) { echo $resources_html; } ?>

	<div class="buttons align_center">
	  <button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button>
	  <a class="btn_standard" href="<?php echo $cancel; ?>" ><?php echo $form['cancel']; ?></a>
    </div>
	</form>

  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript"><!--

$('#editFrm_generate_seo_keyword').click(function(){
	var seo_name = $('#editFrm_name').val().replace('%','');
	$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
		$('#editFrm_keyword').val(data).change();
	});
});

//--></script>

<?php if ( !empty($update) ) { echo $resources_scripts; } ?>