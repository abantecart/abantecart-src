<div class="fieldset">
    <div class="heading"><?php echo $text_product_summary; ?></div>
    <div class="top_left"><div class="top_right"><div class="top_mid"></div></div></div>
    <div class="cont_left"><div class="cont_right"><div class="cont_mid">
    <table id="summary" class="summary" width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="summary_image" rowspan="3" align="center">
            <?php echo $product['image']['thumb_html']; ?>
            <br/>
            <a href="<?php echo $product['preview']; ?>" target="_blank"><?php echo $text_view; ?></a>
        </td>
        <td class="summary_label"><?php echo $entry_name; ?></td>
        <td class="summary_value"><?php echo $product['name']; ?></td>
        <td class="summary_label"><?php echo $entry_product_id; ?></td>
        <td class="summary_value"><?php echo $product['product_id']; ?></td>
      </tr>
      <tr>
        <td class="summary_label"><?php echo $entry_model; ?></td>
        <td class="summary_value"><?php echo $product['model']; ?></td>
        <td class="summary_label"><?php echo $entry_price; ?></td>
        <td class="summary_value"><?php echo $product['price']; ?></td>
      </tr>
      <tr>
        <td class="summary_label"><?php echo $text_product_condition; ?></td>
        <td class="summary_value"><?php
			if($product['condition']){
				echo  '<p class="warning">'.implode('</p><br><p class="error">',$product['condition']).'</p>';
			}else{
				echo $text_product_available;
			} ?></td>
        <td class="summary_label"></td>
        <td class="summary_value"></td>
      </tr>
    </table>
    </div></div></div>
    <div class="bottom_left"><div class="bottom_right"><div class="bottom_mid"></div></div></div>
</div><!-- <div class="fieldset"> -->