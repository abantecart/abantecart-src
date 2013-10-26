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
    <?php echo $form['form_open']; ?>
      <table class="list">
        <thead>
          <tr>
            <td><div class="center checkbox all">
	            <input type='checkbox' onclick="checkAll('selected', $(this).prop('checked'))" />
            </div></td>
            <td><?php echo $column_type; ?></td>
            <td class="left"><?php echo $column_description; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($sections) { ?>
          <?php foreach ($sections as $section) { ?>
          <tr>
            <td class="center"><div class="checkbox"><input type='checkbox' name="selected[]" value="<?php echo $section['keywords']; ?>" /></div></td>
            <td class="left"><?php echo $section['text']; ?></td>
            <td class="left"><?php echo $section['description']; ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="3"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
	  <div class="align_center" style="margin-top: 10px;">
		  <button class="btn_standard button_loader" type="submit"><?php echo $form['submit']; ?></button>
	  </div>
    </form>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>