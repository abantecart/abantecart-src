<?php if ($error_warning) { ?>
<div class="warning alert alert-error"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_tax"><?php echo $heading_title; ?></div>
	  <div class="heading-tabs">
		<a href="<?php echo $action ?>" <?php echo ( $active == 'details' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_details ?></span></a>
		<?php if (!empty($rates)) { ?>
		  <a href="<?php echo $rates ?>" <?php echo ( $active == 'rates' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_rates ?></span></a>
		<?php } ?>
	</div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
	    <?php echo $form_language_switch; ?>
		<div class="buttons">
      <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert_rate; ?>">
		<span class="icon_add">&nbsp;</span>
	  </a>
    </div></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <table id="tax_rate" class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $entry_location; ?></td>
            <td class="left"><?php echo $entry_zone; ?></td>
            <td class="left"><?php echo $entry_description; ?></td>
            <td class="left"><?php echo $entry_rate; ?></td>
            <td class="left"><?php echo $entry_priority; ?></td>
            <td></td>
          </tr>
        </thead>
        <?php $tax_rate_row = 0; ?>
        <?php foreach ($tax_rates as $tax_rate) { ?>
        <tbody id="tax_rate_row<?php echo $tax_rate_row; ?>">
          <tr>
            <td class="left"><?php echo $locations[ $tax_rate['location_id'] ] ?></td>
            <td class="left"><?php echo $zones[  $tax_rate['zone_id'] ]; ?></td>
            <td class="left"><?php echo $tax_rate['description']; ?></td>
            <td class="left"><?php echo $tax_rate['rate_prefix'] . $tax_rate['rate']; ?></td>
            <td class="left"><?php echo $tax_rate['priority']; ?></td>
            <td class="left">
	            <a class="btn_action btn_grid" title="<?php echo $button_edit; ?>" href="<?php echo str_replace('%ID%', $tax_rate['tax_rate_id'], $update_rate); ?>">
					<img border="0" alt="<?php echo $button_edit; ?>" src="admin/view/default/image/icons/icon_grid_edit.png">
				</a>

                <a title="<?php echo $button_remove; ?>" href="<?php echo str_replace('%ID%', $tax_rate['tax_rate_id'], $delete_rate); ?>" class="btn_action btn_grid grid_action_delete">
	                <img border="0" alt="<?php echo $button_remove; ?>" src="admin/view/default/image/icons/icon_grid_delete.png">
	            </a>
            </td>
          </tr>
        </tbody>
        <?php $tax_rate_row++; ?>
        <?php } ?>
        <tfoot>
          <tr>
            <td colspan="5"></td>
            <td class="left"><a href="<?php echo $insert_rate; ?>" class="add" title="<?php echo $text_add;?>" style="height: 20px; width: 16px;"></a></td>
          </tr>
        </tfoot>
      </table>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>