<?php if (!empty($error['warning'])) { ?>
	<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_order"><?php echo $heading_title; ?></div>
      <div class="heading-tabs">
             <?php
             foreach ($tabs as $tab) {
                 echo '<a href="'.$tab['href'].'" '.($tab['active'] ? 'class="active"' : '').'><span>'.$tab['text'].'</span></a>';
             }
             ?>
     	</div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
	<div class="buttons">
		<a href="<?php echo $invoice?>" class="btn_standard" target="_invoice"><?php echo $button_invoice?></a>
	</div>
	</div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $summary_form; ?>
	<?php echo $form['form_open']; ?>
	<div class="fieldset" id="tab_history">
	  <div class="heading"><?php echo $form_title; ?></div>
	  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
	  <div class="cont_left"><div class="cont_right"><div class="cont_mid">

        <?php foreach ($histories as $history) { ?>
        <table class="list">
          <thead>
            <tr>
              <td class="left" width="33.3%"><b><?php echo $column_date_added; ?></b></td>
              <td class="left" width="33.3%"><b><?php echo $column_status; ?></b></td>
              <td class="left" width="33.3%"><b><?php echo $column_notify; ?></b></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="left"><?php echo $history['date_added']; ?></td>
              <td class="left"><?php echo $history['status']; ?></td>
              <td class="left"><?php echo $history['notify']; ?></td>
            </tr>
          </tbody>
          <?php if ($history['comment']) { ?>
          <thead>
            <tr>
              <td class="left" colspan="3"><b><?php echo $column_comment; ?></b></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="left" colspan="3"><?php echo $history['comment']; ?></td>
            </tr>
          </tbody>
          <?php } ?>
        </table>
        <?php } ?>
        <table class="form">
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><?php echo $form['order_status_id']; ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_notify; ?></td>
            <td><?php echo $form['notify']; ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_append; ?></td>
            <td><?php echo $form['append']; ?></td>
          </tr>
		  <?php echo $this->getHookVar('hk_order_comment_pre'); ?>
          <tr>
            <td><?php echo $entry_comment; ?></td>
            <td><?php echo $form['comment']; ?></td>
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