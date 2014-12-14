<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_product"><?php echo $heading_title; ?></div>
	<div class="heading-tabs">
        <a href="<?php echo $link_general; ?>"><span><?php echo $tab_general; ?></span></a>
        <a href="<?php echo $link_links; ?>"><span><?php echo $tab_links; ?></span></a>
        <a href="<?php echo $link_options; ?>"><span><?php echo $tab_option; ?></span></a>
        <a href="<?php echo $link_discount; ?>" class="active"><span><?php echo $tab_discount; ?></span></a>
        <a href="<?php echo $link_special; ?>"><span><?php echo $tab_special; ?></span></a>
        <a href="<?php echo $link_images; ?>"><span><?php echo $tab_image; ?></span></a>
	</div>
	<div class="toolbar">
		<div class="buttons">
		  <a class="btn_toolbar" title="<?php echo $button_insert; ?>" href="<?php echo $insert; ?>">
			<span class="icon_add">&nbsp;</span>
		  </a>
        </div>
	</div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	  <h2><?php echo $form_title; ?></h2>

	  <table id="discount" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_customer_group; ?></td>
              <td class="left"><?php echo $entry_quantity; ?></td>
              <td class="left"><?php echo $entry_priority; ?></td>
              <td class="left"><?php echo $entry_price; ?></td>
              <td class="left"><?php echo $entry_date_start; ?></td>
              <td class="left"><?php echo $entry_date_end; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $discount_row = 0; ?>
          <?php foreach ($product_discounts as $product_discount) { ?>
          <tbody id="discount_row<?php echo $discount_row; ?>">
            <tr>
              <td class="left"><?php echo $customer_groups[ $product_discount['customer_group_id'] ]; ?></td>
              <td class="left"><?php echo $product_discount['quantity']; ?></td>
              <td class="left"><?php echo $product_discount['priority']; ?></td>
              <td class="left"><?php echo $product_discount['price']; ?></td>
              <td class="left"><?php echo $product_discount['date_start']; ?></td>
              <td class="left"><?php echo $product_discount['date_end']; ?></td>
              <td class="left">
                <a href="<?php echo str_replace('%ID%', $product_discount['product_discount_id'], $update); ?>" class="btn_standard"><?php echo $button_edit; ?></a>
                <a href="<?php echo str_replace('%ID%', $product_discount['product_discount_id'], $delete); ?>" class="btn_standard"><?php echo $button_remove; ?></a>
              </td>
            </tr>
          </tbody>
          <?php $discount_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="6"></td>
              <td class="left"><a href="<?php echo $insert ?>" class="btn_standard"><?php echo $button_add_discount; ?></a></td>
            </tr>
          </tfoot>
        </table>


  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>