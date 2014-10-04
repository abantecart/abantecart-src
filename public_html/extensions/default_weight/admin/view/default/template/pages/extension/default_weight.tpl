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

      <div style="display: inline-block; width: 100%;">
        <div id="tabs" class="vtabs">
            <a tab="#tab_general"><?php echo $tab_general; ?></a>
          <?php foreach ($locations as $location) { ?>
          <a tab="#tab_location_<?php echo $location['location_id']; ?>"><?php echo $location['name']; ?></a>
          <?php } ?>
        </div>
        <div id="tab_general" class="vtabs_page">
          <table class="form">
            <tr>
              <td><?php echo $entry_tax; ?></td>
              <td><?php echo $form['fields']['tax']; ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_sort_order; ?></td>
              <td><?php echo $form['fields']['sort_order']; ?></td>
            </tr>
          </table>
        </div>
        <?php foreach ($locations as $location) { ?>
        <div id="tab_location_<?php echo $location['location_id']; ?>" class="vtabs_page">
          <table class="form">
            <tr>
              <td><?php echo $entry_rate; ?></td>
              <td><?php echo $form['fields']['default_weight_' . $location['location_id'] . '_rate']; ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><?php echo $form['fields']['default_weight_' . $location['location_id'] . '_status']; ?></td>
            </tr>
          </table>
        </div>
        <?php } ?>
      </div>
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
jQuery(function(){
$.tabs('#tabs a');
//$('.aform').show();
});
//--></script>
