<?php
$total_items = sizeof((array)$products);
//To remove limit set $cart_view_limit = $total_items;
//To enable scroll for all products look for #top_cart_product_list .products in styles.css
$cart_view_limit = 5;
if ($total_items > 0) {
?>
<div class="products">
<table>
	<tbody>
	<?php echo $this->getHookVar('cart_top_pre_list_hook'); ?>
	<?php 
	for ($i = 0; $i < $cart_view_limit && $i < $total_items; $i++) {
	$product = $products[$i];
	?>
		<tr>
			<td class="image">
				<?php if($product['href']){ ?>
				<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']['thumb_url']; ?>"></a>
				<?php }else{ ?>
					<img src="<?php echo $product['thumb']['thumb_url']; ?>">
				<?php }?>
			</td>
			<td class="name">
				<?php if($product['href']){ ?>
					<a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
				<?php }else{
					echo $product['name'];
				}?>
				<div>
					<?php foreach ($product['option'] as $option) { ?>
						-
						<small title="<?php echo $option['title']?>"><?php echo $option['name']; ?> <?php echo $option['value']; ?></small><br/>
					<?php } ?>
				</div>
			</td>
			<td class="total"><?php echo $product['price']; ?></td>
			<td class="times"><i class="fa fa-times fa-fw"></i></td>
			<td class="quantity"><?php echo $product['quantity']; ?></td>
		</tr>
	<?php echo $this->getHookVar('cart_details_'.$product['key'].'_additional_info_1'); ?>
	<?php } ?>

	<?php echo $this->getHookVar('cart_top_post_list_hook'); ?>
	<?php if ($total_items > $cart_view_limit) {  ?>
		<tr>
			<td colspan="4" align="center"><a href="<?php echo $view; ?>">
			<i class="fa fa-chevron-down"></i>
			</a></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
</div>
<table class="totals">
	<tbody>
	<?php foreach ($totals as $total) { ?>
		<tr>
			<td><span class="cart_block_total"><b><?php echo $total['title']; ?></b></span></td>
			<td><span class="cart_block_total"><?php echo $total['text']; ?></span></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php } else { ?>
	<div class="empty_cart text-center">
		<i class="fa fa-shopping-cart"></i>
	</div>
<?php } ?>