<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_country"><?php echo $heading_title; ?></div>
	<div class="heading-tabs">
		<a href="<?php echo $details ?>" <?php echo ( $active == 'details' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_details ?></span></a>
		<?php if (!empty($locations)) { ?>
		  <a href="<?php echo $locations ?>" <?php echo ( $active == 'locations' ? 'class="active"' : '' ) ?> ><span><?php echo $tab_locations ?></span></a>
		<?php } ?>
	</div>
	<div class="toolbar">
		<?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
		<div class="buttons">
      <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert_location; ?>">
		<span class="icon_add">&nbsp;</span>
	  </a>
    </div></div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">
    <table id="zone_to_location" class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $entry_country; ?></td>
            <td class="left"><?php echo $entry_zone; ?></td>
            <td></td>
          </tr>
        </thead>
        <?php $zone_to_location_row = 0; ?>
        <?php foreach ($zone_to_locations as $zone_to_location) { ?>
        <tbody id="zone_to_location_row<?php echo $zone_to_location_row; ?>">
          <tr>
            <td class="left"><?php echo $zone_to_location['country'] ?></td>
            <td class="left"><?php echo $zone_to_location['zone'] ?></td>
            <td class="left">
	            <a class="btn_action btn_grid" title="<?php echo $button_edit; ?>" href="<?php echo str_replace('%ID%', $zone_to_location['zone_to_location_id'], $edit_location); ?>">
					<img border="0" alt="<?php echo $button_edit; ?>" src="admin/view/default/image/icons/icon_grid_edit.png">
				</a>

                <a title="<?php echo $button_remove; ?>" href="<?php echo str_replace('%ID%', $zone_to_location['zone_to_location_id'], $delete_location); ?>" class="btn_action btn_grid grid_action_delete">
	                <img border="0" alt="<?php echo $button_remove; ?>" src="admin/view/default/image/icons/icon_grid_delete.png">
	            </a>

            </td>
          </tr>
        </tbody>
        <?php $zone_to_location_row++; ?>
        <?php } ?>
        <tfoot>
          <tr>
            <td colspan="2"></td>
            <td class="left"><a href="<?php echo $insert_location; ?>" title="<?php echo $text_add;?>" class="add" style="height: 20px; width: 16px;"></a></td>
          </tr>
        </tfoot>
      </table>
  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>