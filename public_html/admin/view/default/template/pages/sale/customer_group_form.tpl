<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_total"><?php echo $heading_title; ?></div>

	<?php if ( !empty($main_tab) ): ?>
		<div class="heading-tabs">
			<a href="<?php echo $main_tab['href']; ?>" class="<?php echo $main_tab['class']; ?>"><span><?php echo $main_tab['text']; ?></span></a>
			<?php if ( !empty($tabs) ): ?>
				<?php foreach ( $tabs as $tab): ?>
					<a href="<?php echo $tab['href']; ?>" class="<?php echo $tab['class']; ?>"><span><?php echo $tab['text']; ?></span></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
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