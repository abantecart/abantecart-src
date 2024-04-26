<?php if($products){
    $text_items_in_the_cart = $this->language->get('text_cart_items','checkout/fast_checkout');

    $cartProducts = $this->cart->getProducts();
    $cartProductIds = $cartProducts ? array_column($cartProducts,'product_id') : [];
    $cartProducts = array_column($cartProducts,'quantity','product_id');
    $cartUrl = $this->html->getSecureURL( ($cart_rt ?:'checkout/cart'));

    $imgW = $imgW ?? $this->config->get('config_image_product_width');
    $imgH = $imgH ?? $this->config->get('config_image_product_height');
    $wishlist = $wishlist ?? $this->customer->getWishlist();
?>

    <section class="product-sec" id="<?php echo $homeBlockId;?>">
		<?php if ( $block_framed ) { ?>

			<div id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>"
			class="block_frame container block_frame_<?php echo $block_details['block_txt_id']; ?>">

				<div class="row title justify-content-center sec-heading-block text-center">
					<div class="col-xl-8">
						<h2><?php echo $heading_title; ?></h2>
						<p><?php echo $heading_subtitle; ?></p>
					</div>
				</div>

		<?php } ?>
			<div class="row g-4">
				<?php
				$text_sale = $this->language->get('text_badge_sale','novator/novator');
				$text_sale = $text_sale == 'text_badge_sale' ? 'SALE' : $text_sale;
				$tax_exempt = $this->customer->isTaxExempt();
				$config_tax = $this->config->get('config_tax');
				$productImgCss = 'width: '.$imgW.'px;';
				$productImgCss .= ' height: '.$imgH.'px;';
				$noRating = noRatingStarsNv($button_write);

				foreach ($products as $product) {
					$tax_message = '';
					if ($config_tax && !$tax_exempt && $product['tax_class_id']) {
							$tax_message = '&nbsp;&nbsp;' . $price_with_tax;
					}

					$product['thumb'] = $product['thumb'] ?? $product['image'];
					$item = [];

					$item['image'] = '<img alt="'.html2view($product['name']).'" class="img-fluid h-auto" src="'.$product['thumb']['thumb_url'].'" style="'.$productImgCss.'">';
					$item['image1'] = '<img class="img-fluid h-auto img-overlay" src="'.$product['thumb']['thumb_url'].'" style="'.$productImgCss.'">';
					$item['title'] = $product['name'];
					$item['description'] = $product['model'];
					$item['rating'] = renderRatingStarsNv($product['rating'], $product['stars']);

					$item['info_url'] = $product['href'];
					$item['buy_url'] = $product['add'];

					if (!$display_price) {
						$item['price'] = '';
					}

                    $review = $noRating;
					if ($item['rating']) {
                        $review = $item['rating'];
                    }
                    $inCart = in_array((int)$product['product_id'], $cartProductIds);
					?>
					<div class="col-6 col-lg-3">
                        <?php
                        //render one card of product. It can be used by other tpls!
                        /** @see  product_card.tpl */
                        include($this->templateResource('/template/blocks/product_card.tpl')); ?>
					</div>
				<?php
				} ?>
			</div>
		<?php
		if ( $block_framed ) { ?>
			</div>
		<?php } ?>
    </section>
<?php } ?>