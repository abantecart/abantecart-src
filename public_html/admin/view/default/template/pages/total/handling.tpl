<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_total"><?php echo $heading_title; ?></div>
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
		<?php foreach ($form['fields'] as $name => $field) {
			if(is_array($field)){
				foreach($field as $i=>$fld){ ?>
				<tr class="per_payment">
					<td><?php echo ${'entry_'.$name}; ?></td>
					<td>
						<?php echo $fld; ?>
					</td>
				</tr>
				<?php if($i+1==sizeof($field)){?>
				<tr>
					<td></td>
					<td><a style="margin-left: 400px;" class="add">&nbsp;</a></td>
				</tr>
				<?php } ?>
			<?php }?>

			<?php
			}else{ ?>
			<tr>
				<td><?php echo ${'entry_'.$name}; ?></td>
				<td>
					<?php echo $field; ?>
					<?php if (!empty($error[$name])) { ?>
						<div class="field_err"><?php echo $error[$name]; ?></div>
					<?php } ?>
				</td>
			</tr>
		<?php } } ?>
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
<script language="javascript" type="text/javascript">
	$('a.add').click(function(){
		var new_row = $('tr.per_payment').last().clone();
		$('tr.per_payment').last().after(new_row);
		$("input, select", new_row).aform({triggerChanged: true, showButtons: false });
	});

	</script>