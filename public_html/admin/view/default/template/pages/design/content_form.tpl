<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
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
			<td><?php echo $entry_name; ?></td>
			<td class="ml_field">
				<?php echo $form['fields']['name']; ?>
				<?php if (!empty($error['name'])) { ?>
					<div class="field_err"><?php echo $error['name']; ?></div>
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
		<tr valign="top">
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
		<tr valign="top">
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
		<tr valign="top">
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
			<td>
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
</script>