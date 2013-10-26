<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading"><?php echo $heading_title; ?></div>
	  <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	<div id="anchor_links">
        <a href="<?php echo $current_url; ?>#tab_backup"><?php echo $tab_backup; ?></a>
        <a href="<?php echo $current_url; ?>#tab_restore"><?php echo $tab_restore; ?></a>
        <a href="<?php echo $current_url; ?>#tab_loadxml"><?php echo $tab_loadxml; ?></a>
    </div>

	<div class="fieldset">
      <div class="heading"><a id="tab_backup"><?php echo $tab_backup; ?></a></div>
      <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
      <div class="cont_left"><div class="cont_right"><div class="cont_mid">
        <div >
			<?php echo $form['form_open']; ?>
			  <table class="form">
				<tr>
				  <td><?php echo $entry_backup; ?></td>
				  <td>
					  <?php $class = 'odd'; ?>
					<?php foreach($form['fields'] as $key => $field): ?>
						<span id="<?php echo $key; ?>"><?php echo $field; ?></span>
					<?php endforeach; ?>
					<div class="clr_both"></div>
					<br/>
					<a onclick="selectAll();"><?php echo $text_select_all; ?></a> /
					<a onclick="unselectAll();"><?php echo $text_unselect_all; ?></a>
					<br/><br/>
					<div>
						<?php echo $form['backup_rl']; ?>
					</div>
					<div>
						<?php echo $form['backup_config']; ?>
					</div>
				</td></tr>
				<tr><td></td>
				  <td><button type="submit" class="btn_standard button_loader"><?php echo $form['submit']; ?></button></td>
				</tr>
			  </table>
			</form>
		</div>
      </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
    </div>
	<div class="fieldset">
      <div class="heading"><a id="tab_restore"><?php echo $tab_restore; ?></a></div>
      <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
      <div class="cont_left"><div class="cont_right"><div class="cont_mid">
        <div ><?php echo $restoreform['form_open']; ?>
			  <table class="form">
				<tr>
				  <td><?php echo $entry_restore; ?></td>
				  <td><?php echo $restoreform['file']; ?></td>
			      <td width="100%"><a class="btn_standard" onclick="document.forms['restore_form'].submit();">
						    <?php echo $restoreform['submit']; ?></a></td>
				</tr>
			  </table>
		</form>
     </div>
      </div></div></div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
    </div>
	<div class="fieldset">
      <div class="heading"><a id="tab_loadxml"><?php echo $tab_loadxml; ?></a></div>
      <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
      <div class="cont_left"><div class="cont_right"><div class="cont_mid">
        <div ><?php echo $xmlform['form_open']; ?>
			  <table class="form">
				<tr>
				  <td><?php echo $entry_loadxml; ?></td>
				  <td><?php echo $xmlform['file']; ?></td>
				  <td width="100%"><a class="btn_standard" onclick="document.forms['loadxml_form'].submit();">
						    <?php echo $xmlform['submit']; ?></a></td>
				</tr>
			  </table>
			</form>
		</div>
      </div>
      </div>
      </div>
      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
    </div>
  </div>
<div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>

<script type="text/javascript">

	function selectAll()
	{
		$('input[name*=\'backup\[\]\']').attr('checked', 'checked');
		$('#tables').find('.afield').addClass('checked');
	}

	function unselectAll()
	{
		$('input[name*=\'backup\[\]\']').removeAttr('checked');
		$('#tables').find('.afield').removeClass('checked');
	}

</script>