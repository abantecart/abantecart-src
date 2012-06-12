<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_shipping"><?php echo $heading_title; ?></div>
	<div class="toolbar"><?php echo $form_language_switch; ?></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $form['form_open']; ?>
	<div class="fieldset">
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">
		<table>
			<tr>
				<td><?php echo $form['fields']['language_note']; ?></td>
				<td>
					<?php echo $form['fields']['language']; ?>
					<?php if (!empty($error)) { ?>
						<div class="field_err"><?php echo $error; ?></div>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $form['fields']['instruction_note']; ?></td>
				<td>
					<?php echo $form['fields']['instruction']; ?>
					<?php if (!empty($error)) { ?>
						<div class="field_err"><?php echo $error; ?></div>
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
<script type="text/javascript">
	$('#editFrm_default_bank_transfer_text_instruction').parents('.afield').removeClass('mask2, afield');
	CKEDITOR.replace('default_bank_transfer_text_instruction',{
		height: '400px',
		filebrowserBrowseUrl : false,
        filebrowserImageBrowseUrl : '<?php echo $rl; ?>',
        filebrowserWindowWidth : '910',
        filebrowserWindowHeight : '500',
		language: '<?php echo $language_code; ?>'
	});
</script>