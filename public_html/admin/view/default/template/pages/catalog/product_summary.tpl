<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-btns">
            <a class="panel-close" href="">×</a>
            <a class="minimize" href="">−</a>
		</div>
          <h4 class="panel-title"><?php echo $text_product_summary; ?></h4>
	</div>
	<div class="panel-body panel-body-nopadding table-responsive" style="display: block;">
    <table id="summary" class="table summary">
      <tr>
        <td class="summary_image" rowspan="3" align="center">
            <?php echo $product['image']['thumb_html']; ?>
            <br/>
            <a href="<?php echo $product['preview']; ?>" class="btn btn-small btn-default mt10" target="_new"><i class="fa fa-external-link"></i> <?php echo $text_view; ?></a>
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
				echo  '<div class="alert-danger col-sm-4">'.implode('</p><br><p class="error">',$product['condition']).'</div>';
			}else{
				echo $text_product_available;
			} ?></td>
        <td class="summary_label"><?php echo $text_total_orders; ?></td>
        <td class="summary_value"> <?php echo $product['orders']; ?>
        <?php if( $product['orders'] > 0) { ?>
	        &nbsp;&nbsp;<a href="<?php echo $product['orders_url']; ?>" class="btn btn-small btn-default" target="_new"><i class="fa fa-external-link"></i> <?php echo $text_view; ?></a>
        <?php } ?>
        </td>
      </tr>
    </table>
	</div>
</div>
