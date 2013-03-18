<h1 class="heading1">
  <span class="maintext"><?php echo $heading_title; ?></span>
  <?php if ($weight) { ?>
  <span class="subtext">(<?php echo $weight; ?>)</span>
  <?php } ?>
</h1>

<?php if ( count($error_warning) > 0 ) {
	foreach ($error_warning as $error) { ?>
<div class="alert alert-error">
  <strong><?php echo $error; ?></strong>
</div>
<?php } 
	}
echo $form['form_open'];
?>
<div class="cart-info">
	<table class="table table-striped table-bordered">
    <tr>
      <th align="center"><?php echo $column_remove; ?></th>
      <th align="center"><?php echo $column_image; ?></th>
      <th align="left"><?php echo $column_name; ?></th>
      <th align="left"><?php echo $column_model; ?></th>
      <th align="center"><?php echo $column_quantity; ?></th>
      <th align="right"><?php echo $column_price; ?></th>
      <th align="right"><?php echo $column_total; ?></th>
    </tr>
    <?php $class = 'odd'; ?>
    <?php foreach ($products as $product) { ?>
    <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
    <tr class="<?php echo $class; ?>">
      <td align="center"><?php echo $product['remove']; ?></td>
      <td align="center"><a href="<?php echo $product['href']; ?>"><?php echo $product['thumb']['thumb_html']; ?></a></td>
      <td align="left" ><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
        <?php if (!$product['stock']) { ?>
        <span style="color: #FF0000; font-weight: bold;">***</span>
        <?php } ?>
        <div>
          <?php foreach ($product['option'] as $option) { ?>
          - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br />
          <?php } ?>
        </div></td>
      <td align="left" ><?php echo $product['model']; ?></td>
      <td align="center"><?php echo $product['quantity']; ?></td>
      <td align="right"><?php echo $product['price']; ?></td>
      <td align="right"><?php echo $product['total']; ?></td>
    </tr>
    <?php } ?>
  </table>
</div>  

<div class="container">
<div class="pull-right">
    <div class="cart-info span4 pull-right">
      <table class="table table-striped table-bordered">
		<?php foreach ($totals as $total) { ?>
		<tr>
		  <td><span class="extra bold <?php if($total[id] == 'total') echo 'totalamout'; ?>"><?php echo $total['title']; ?></span></td>
		  <td><span class="bold <?php if($total[id] == 'total') echo 'totalamout'; ?>"><?php echo $total['text']; ?></span></td>
		</tr>
		<?php } ?>
      </table>
      <button title="<?php echo $button_checkout; ?>" class="btn btn-orange pull-right" id="cart_checkout" type="button"><?php echo $button_checkout; ?></button>
      <button title="<?php echo $button_update; ?>" class="btn btn-orange pull-right mr10" id="cart_update" type="submit"><?php echo $button_update; ?></button>
    </div>
</div>
</div>
 
</form>


<script type="text/javascript">
	$('#cart_checkout').click( function(){
		location = '<?php echo $checkout; ?>';
	})
</script>