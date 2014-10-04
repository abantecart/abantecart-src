<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_shipping"><?php echo $heading_title; ?></div>
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
				<td id="ship_<?php echo $name; ?>">

					<?php if ( is_array($field) && $field['checkboxes'] ) {
						echo '<div class="scrollbox">';
						foreach ( $field['groups'] as $group => $items ) {
							echo '<div id="'.$group.'">';
							echo '<table class="checkboxes">';
							foreach ( $items as $label => $checkbox ) {
								echo '<div class="clr_both">'.$checkbox.${'text_'.$label}.'</div>';
							}
							echo '</table>';
							echo '</div>';
						}
						echo '</div>';
					} else { ?>
						<?php echo $field; ?>
						<?php if (!empty($error[$name])) { ?>
							<div class="field_err"><?php echo $error[$name]; ?></div>
						<?php } ?>
					<?php } ?>
				</td>
			</tr>
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

<script type="text/javascript"><!--
jQuery(function($){
	$('select[name="ups_origin"]').bind('change', function() {
		$('#ship_service div.scrollbox > div').hide();
		$('#' + this.value).show();
	});

	$('select[name=\'ups_origin\']').trigger('change');
});
//--></script>