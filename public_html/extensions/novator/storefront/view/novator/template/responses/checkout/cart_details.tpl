<?php
$total_items = sizeof((array)$products);
//To remove limit set $cart_view_limit = $total_items;
//To enable scroll for all products look for #top_cart_product_list .products in styles.css
$cart_view_limit = 5;
if ($total_items > 0) {
?>
<div class="products">
    <?php echo $this->getHookVar('cart_top_pre_list_hook'); ?>
    <?php
    for ($i = 0; $i < $cart_view_limit && $i < $total_items; $i++) {
        $product = $products[$i];
        ?>
    <div class="d-flex">
        <div class="flex-shrink-0"><img src="<?php echo $product['thumb']['thumb_url']; ?>" alt="image" class="img-fluid h-auto"
                                        width="90" height="90"></div>
        <div class="flex-grow-1 ms-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p class="mb-0"><?php echo $product['name']; ?></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="btn-group btn-group-sm mb-0" role="group" aria-label="button groups sm">
                    <button type="button"
                            id="decrease" onclick="decreaseValue('number')" class="btn border-0 shadow-none">-</button> <input
                            class="text-center border-0 shadow-none" type="text" id="number" value="<?php echo $product['quantity']; ?>"> <button
                            type="button" id="increase" onclick="increaseValue('number')"
                            class="btn border-0 shadow-none">+</button>
                </div>
                <h5 class="mb-0"><?php echo $product['price']; ?></h5>
            </div>
        </div>
    </div>
        <?php echo $this->getHookVar('cart_details_'.$product['key'].'_additional_info_1'); ?>
    <?php } ?>
</div>
    <div class="d-flex flex-column justify-content-center align-items-end vh-100">
         <div class="table-container w-100">
<table class="table">
	<tbody>
	<?php foreach ($totals as $total) { ?>
		<tr>
			<th><?php echo $total['title']; ?></th>
			<td><span class="float-end"><?php echo $total['text']; ?></span></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
         </div>
    </div>
<?php } else { ?>
	<div class="empty_cart text-center">
		<i class="bi bi-shopping-cart"></i>
	</div>
<?php } ?>