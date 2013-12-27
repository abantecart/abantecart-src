<?php if ( !empty($error['warning']) ) { ?>
<div class="warning alert alert-error"><?php echo $error['warning']; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<a name="top"></a>

<div class="contentBox">
  <div class="cbox_tl"><div class="cbox_tr"><div class="cbox_tc">
    <div class="heading icon_title_product"><?php echo $form_title; ?></div>
	<?php echo $product_tabs ?>
	<div class="toolbar">
	    <?php if ( !empty ($help_url) ) : ?>
	        <div class="help_element"><a href="<?php echo $help_url; ?>" target="new"><img src="<?php echo $template_dir; ?>image/icons/help.png"/></a></div>
	    <?php endif; ?>
      </div>
  </div></div></div>
  <div class="cbox_cl"><div class="cbox_cr"><div class="cbox_cc">

	<?php echo $summary_form; ?>

	<div class="fieldset">
		  <div class="heading"><a><?php echo $tab_discount; ?></a></div>
		  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
		  <div class="cont_left"><div class="cont_right"><div class="cont_mid">

	  <table class="list">
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
              <td class="left"><?php echo moneyDisplayFormat( $product_discount['price'] ); ?></td>
              <td class="left"><?php echo $product_discount['date_start']; ?></td>
              <td class="left"><?php echo $product_discount['date_end']; ?></td>
              <td class="left">
                <a href="<?php echo str_replace('%ID%', $product_discount['product_discount_id'], $update_discount); ?>" class="btn_standard"><?php echo $button_edit; ?></a>
                <a href="<?php echo str_replace('%ID%', $product_discount['product_discount_id'], $delete_discount); ?>" class="btn_standard"><?php echo $button_remove; ?></a>
              </td>
            </tr>
          </tbody>
          <?php $discount_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="6"></td>
              <td class="left"><a href="<?php echo $insert_discount ?>" class="btn_standard"><?php echo $button_add_discount; ?></a></td>
            </tr>
          </tfoot>
        </table>

		</div></div></div>
		  <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
		</div>

	<div class="fieldset">
		  <div class="heading"><a><?php echo $tab_special; ?></a></div>
		  <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
		  <div class="cont_left"><div class="cont_right"><div class="cont_mid">

		  <table class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_customer_group; ?></td>
              <td class="left"><?php echo $entry_priority; ?></td>
              <td class="left"><?php echo $entry_price; ?></td>
              <td class="left"><?php echo $entry_date_start; ?></td>
              <td class="left"><?php echo $entry_date_end; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $discount_row = 0; ?>
          <?php foreach ($product_specials as $item) { ?>
          <tbody>
            <tr>
              <td class="left"><?php echo $customer_groups[ $item['customer_group_id'] ]; ?></td>
              <td class="left"><?php echo $item['priority']; ?></td>
              <td class="left"><?php echo moneyDisplayFormat( $item['price'] ); ?></td>
              <td class="left"><?php echo $item['date_start']; ?></td>
              <td class="left"><?php echo $item['date_end']; ?></td>
              <td class="left">
                <a href="<?php echo str_replace('%ID%', $item['product_special_id'], $update_special); ?>" class="btn_standard"><?php echo $button_edit; ?></a>
                <a href="<?php echo str_replace('%ID%', $item['product_special_id'], $delete_special); ?>" class="btn_standard"><?php echo $button_remove; ?></a>
              </td>
            </tr>
          </tbody>
          <?php $discount_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="5"></td>
              <td class="left"><a href="<?php echo $insert_special ?>" class="btn_standard"><?php echo $button_add_special; ?></a></td>
            </tr>
          </tfoot>
        </table>

			</div></div></div>
		  <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
		</div>


  </div></div></div>
  <div class="cbox_bl"><div class="cbox_br"><div class="cbox_bc"></div></div></div>
</div>