<h1 class="ms-3 my-2 heading-title ">
    <i class="fa fa-heart-pulse me-2"></i>
    <?php echo $heading_title; ?>
</h1>

<?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<?php if ($error) { ?>
    <div class="alert alert-error alert-danger alert-dismissible" role="alert">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

	<div class="container-fluid wishlist product-list">
		<table class="table table-hover table-bordered">
			<tr class="text-center">
				<th><?php echo $column_image; ?></th>
				<th><?php echo $column_name; ?></th>
				<th class="d-none d-sm-block"><?php echo $column_model; ?></th>
            <?php if ($display_price) { ?>
                <th class="d-none d-sm-block"><?php echo $column_price; ?></th>
            <?php } ?>
				<th class="d-none d-sm-block"><?php echo $column_added; ?></th>
				<th><?php echo $column_actions; ?></th>
			</tr>
			<?php
            foreach ($products as $product) { ?>
				<tr class="text-center align-middle wishlist_<?php echo $product['product_id'] ?>">
					<td>
						<a href="<?php echo $product['href']; ?>"><?php echo $product['thumb']['thumb_html']; ?></a>
					</td>
					<td class="text-start">
						<a class="btn mt-auto" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
					</td>
					<td class="d-none d-sm-block"><?php echo $product['model']; ?></td>
					<?php if ($display_price) { ?>
					<td class="d-none d-sm-block">
                        <div class="price d-flex justify-content-center align-items-center me-2">
                            <?php if ($product['special']) { ?>
                                <div class="me-2 align-center"><?php echo $product['special'] . $tax_message; ?></div>
                                <div class="text-decoration-line-through"><?php echo $product['price']; ?></div>
                            <?php } else { ?>
                                <div class=""><?php echo $product['price'] . $tax_message; ?></div>
                            <?php } ?>
                        </div>
					</td>
					<?php } ?>
					<td class="d-none d-sm-block"><?php echo $product['added']; ?></td>
					<td>

					<?php if ($display_price) { ?>
						<?php if($product['call_to_order']){ ?>
							<a data-id="<?php echo $product['product_id'] ?>"
                               href="#" class="btn btn-sm btn-outline-info call_to_order mb-1"
                               title="<?php echo $text_call_to_order?>">
								<i class="fa fa-phone fa-fw"></i>
							</a>
						<?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
							<span class="btn btn-sm disabled nostock bg-secondary text-light me-2"><?php echo $product['no_stock_text']; ?></span>
						<?php } else { ?>
							<a href="<?php echo $product['add']; ?>"
                               title="<?php echo $button_add_to_cart; ?>"
                               class="btn btn-sm btn-success mb-1">
								<i class="fa fa-cart-plus fa-fw"></i>
							</a>
						<?php } ?>
					<?php } ?>
						<a href="Javascript:void(0);)" title="<?php echo $button_remove_wishlist;?>"
                           data-product_id="<?php echo $product['product_id'] ?>"
                           class="remove-from-list btn btn-sm btn-danger bg-opacity-50 mb-1"><i class="text-light fa fa-trash fa-fw"></i></a>
					</td>
				</tr>
			<?php } ?>
			<?php echo $this->getHookVar('more_wishlist_products'); ?>
		</table>



        <div class="ps-4 p-3 col-12 d-flex flex-wrap justify-content-end">
            <?php echo $this->getHookVar('top_wishlist_buttons');
            $button_continue->style = 'btn btn-outline-secondary mx-2 mb-1';
            $button_continue->icon = 'fa fa-arrow-right';
            echo $button_continue;

            $button_cart->style = 'btn btn-success mx-2 mb-1';
            $button_cart->icon = 'fa fa-shopping-cart';
            echo $button_cart;
            echo $this->getHookVar('bottom_wishlist_buttons'); ?>
        </div>

    </div>

<script type="text/javascript">

    $(document).ready(function(){
        $('a.remove-from-list').on('click',
            function(e){
                e.preventDefault();
                let target = $(this);
                $.ajax({
                    type: 'POST',
                    url: '<?php echo $this->html->getURL('product/wishlist/remove');?>&product_id=' + target.attr('data-product_id'),
                    dataType: 'json',
                    beforeSend: function () {
                        target.hide();
                    },
                    error: function (jqXHR, exception) {
                        var text = jqXHR.statusText + ": " + jqXHR.responseText;
                        $('.alert').remove();
                        $('.wishlist').before('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
                        target.show();
                    },
                    success: function (data) {
                        if (data.error) {
                            $('.alert').remove();
                            $('.wishlist').before('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
                            target.show();
                        } else {
                            $('.wishlist .alert').remove();
                            target.parents('tr').remove();
                        }
                    }
                });
        });
    });

</script>