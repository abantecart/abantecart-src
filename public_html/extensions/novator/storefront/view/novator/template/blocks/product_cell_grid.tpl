<?php if($products){
    $text_items_in_the_cart = $this->language->get('text_cart_items','checkout/confirm');

    $cartProducts = $this->cart->getProducts();
    $cartProductIds = $cartProducts ? array_column($cartProducts,'product_id') : [];
    $cartProducts = array_column($cartProducts,'quantity','product_id');

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
					?>
					<div class="col-6 col-lg-3">
						<div class="product-card card p-0 border-0"
                             data-raw-price="<?php echo round($product['raw_price'],2)?>"
                             data-product-id="<?php echo round($product['product_id'],2)?>">
							<div class="prod-img position-relative overflow-hidden">
								<a href="<?php echo $item['info_url'] ?>">
									<?php echo $item['image'] ?>
									<?php echo $item['image1'] ?>
								</a>
								<?php if ($product['special']) { ?>
									<span class="sale_<?php echo $product['product_id']; ?>
									prod-badge bg-warning badge rounded-0 position-absolute text-uppercase text-white">
										<?php echo $text_sale; ?>
									</span>
								<?php } ?>
								<div class="overlay d-flex align-items-end end-0 start-0 bottom-0 position-absolute">
									<?php
									if ($product['new_product']) { ?>
										<span class="new new_<?php echo $product['product_id']; ?> prod-badge bg-warning badge rounded-0 position-absolute text-uppercase text-white"></span>
									<?php } ?>

									<div class="bottom-bar">
										<div class="row g-2 align-items-center justify-content-between">
											<div class="col-auto">
												<ul class="list-inline mb-0">
                                                    <?php if($this->customer->isLogged()){ ?>
													<li class="list-inline-item btn-wishlist">
														<a class="wish" href="javascript:void(0)">
                                                            <i class="bi <?php
                                                                echo isset($wishlist[$product['product_id']])
                                                                    ? 'bi-heart-fill'
                                                                    : 'bi-heart';
                                                                ?>"></i>
                                                        </a>
													</li>
                                                    <?php } ?>
													<li class="list-inline-item btn-quickview">
														<a href="<?php echo $item['info_url'] ?>" data-bs-toggle="tooltip" title="<?php echo $button_view ?>">
															<i class="bi bi-eye" ></i>
														</a>
													</li>
													<li class="list-inline-item btn-compare">
														<a href="#">
														<i class="bi bi-shuffle"></i>
														</a>
													</li>
												</ul>
											</div>
											<div class="col-auto">

												<!-- Hello Abentacart team you need to check here starts -->

												<div class="add-to-cart-block">
													<div class="qty-status-block">
														<?php if ($display_price) { ?>
															<?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']); ?>
																	<?php if($product['call_to_order']){ ?>
																		<p class="mb-0 text-capitalize">
																			<a data-id="<?php echo $product['product_id'] ?>"
																			href="<?php echo $this->html->getSeoUrl('content/contact');?>"
																			class="call_to_order badge text-bg-primary"
																			title="<?php echo_html2view($text_call_to_order); ?>">
																			<i class="bi bi-telephone-fill"></i>
                                        <?php echo $text_call_to_order; ?>
																			</a>
																		</p>
																	<?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
																		<p class="badge text-bg-warning mb-0 disabled"><?php echo $text_out_of_stock; ?></p>
																	<?php } elseif ($this->getHookVar('product_add_to_cart_html_'.$product['product_id'])) {
																		echo $this->getHookVar('product_add_to_cart_html_'.$product['product_id']);
																		}else{ ?>
																	<?php
																		}
																	?>
														<?php }
															echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
														?>
													</div>
													<?php if ($display_price) { ?>
														<?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']); ?>
																<?php if($product['call_to_order']){ ?>
																<?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
																<?php } elseif ($this->getHookVar('product_add_to_cart_html_'.$product['product_id'])) {
																	echo $this->getHookVar('product_add_to_cart_html_'.$product['product_id']);
																	}else{ ?>
																		<span class="visually-hidden spinner-border spinner-border-sm" aria-hidden="true"></span>
																		<a data-id="<?php echo $product['product_id']; ?>" href="<?php echo $this->html->getSecureURL( ($cart_rt ?:'checkout/cart')); ?>">
																			<i title="<?php echo_html2view($button_add_to_cart); ?>" class="bi bi-handbag-fill"></i>
                                                                            <?php echo_html2view($button_add_to_cart); ?>
																		</a>
																<?php
																	}
																?>
													<?php }
														echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
													?>
												</div>

												<!-- Hello Abentacart team you need to check here end -->
												<!--
												<div class="d-flex two">
													<?php if ($display_price) { ?>
														<?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']); ?>
															<div class="pricetag flex-item">

																<?php if($product['call_to_order']){ ?>

																<?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>

																<?php } elseif ($this->getHookVar('product_add_to_cart_html_'.$product['product_id'])) {
																	echo $this->getHookVar('product_add_to_cart_html_'.$product['product_id']);
																	}else{ ?>

																		<a class="text-decoration-none text-white"
																		href="<?php echo $this->html->getSecureURL( ($cart_rt ?:'checkout/cart')); ?>">

																			<i title="<?php echo_html2view($text_add_cart_confirm); ?>"
																			class="<?php echo !in_array((int)$product['product_id'], $cartProductIds) ? 'visually-hidden ' : '';?>fa fa-check me-2 text-warning">
																			</i>
																		</a>

																		<?php if($cartProducts[(int)$product['product_id']]){?>
																			<span title="<?php echo_html2view($text_items_in_the_cart); ?>"
																					class="item-qty-badge">
																				<?php echo $cartProducts[(int)$product['product_id']];?>
																			</span>
																		<?php }?>

																<?php
																	}
																?>
															</div>
													<?php }
														echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
													?>
												</div>
												-->

											</div>
										</div>
									</div>
								</div>
							</div>

							<h6 class="my-3">
								<a href="<?php echo $item['info_url'] ?>"
									title="<?php echo $product['blurb'] ?: $item['title'] ?>" class="link-dark">
									<?php echo $item['title']
										. $this->getHookvar('product_listing_name_'.$product['product_id']);
									?>
									</a>
							</h6>

							<div class="card-body p-0">
								<div class="row align-items-center justify-content-between">
									<div class="col-auto">
                                    <?php if ($review_status) { ?>
                                        <a class="text-warning rating-stars text-sm-end" href="<?php echo $item['info_url'] ?>#review">
                                            <?php echo $review ?>
                                        </a>
                                    <?php } ?>
									</div>
									<div class="col-auto">
										<?php if ($display_price) { ?>

											<div class="price text-muted d-flex align-items-center me-2">
												<?php if ($product['special']) { ?>
													<div class="pricenew text-danger prod-price mb-0 fw-semibold me-2"><?php echo $product['special'] . $tax_message; ?></div>
													<div class="priceold text-muted prod-price mb-0 text-decoration-line-through"><?php echo $product['price']; ?></div>
												<?php } else { ?>
													<div class="mb-0 prod-price text-muted fw-semibold"><?php echo $product['price'] . $tax_message; ?></div>
												<?php } ?>
											</div>

										<?php }
											echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
										?>
									</div>


									<?php echo $this->getHookvar('product_listing_details0_'.$product['product_id']);?>


								</div>
							</div>
						</div>
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