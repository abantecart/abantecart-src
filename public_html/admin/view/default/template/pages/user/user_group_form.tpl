<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_user_group"><?php echo $heading_title; ?></div>
	  <div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
        <?php echo $form_language_switch; ?>
    </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
	<?php echo $form['form_open']; ?>
      <table>
	    <tr>
		  <td><?php echo $entry_name; ?></td>
          <td><?php echo $form['fields']['name']; ?>
            <?php if ($error_name) { ?>
            <span class="error"><?php echo $error_name; ?></span>
            <?php  }
	          if($form['submit']){
	        ?>
	        </td><td><button type="submit" class="btn_standard"><?php echo $form['submit']; ?></button></td><td>
		    <?php } ?>
          </td>
        </tr>

		</table></form>
		 <?php echo $listing_grid; ?>

      <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>