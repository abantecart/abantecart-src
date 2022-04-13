<div class="container">
<?php

$tax_exempt = $this->customer->isTaxExempt();
$config_tax = $this->config->get('config_tax');
$text_sale = $this->language->get('text_badge_sale') != 'text_badge_sale'
            ? $this->language->get('text_badge_sale')
            : 'SALE';

foreach ($products as $product) {
    $tax_message = '';
    if ($config_tax && !$tax_exempt && $product['tax_class_id']) {
            $tax_message = '&nbsp;&nbsp;' . $price_with_tax;
    }

    $product['thumb'] = $product['thumb'] ?? $product['image'];
    $item = [];
    $item['image'] = '<img class="m-3" src="'.$product['thumb']['thumb_url'].'">';
    $item['title'] = $product['name'];
    $item['description'] = $product['model'];
    $item['rating'] = renderRatingStars($product['rating'], $product['stars']);

    $item['info_url'] = $product['href'];
    $item['buy_url'] = $product['add'];

    if (!$display_price) {
        $item['price'] = '';
    }

    $review = $button_write;
    if ($item['rating']) {
        $review = $item['rating'];
    }
    ?>
            <div class="d-flex flex-wrap pb-3 mb-3 border-bottom border-3">
                <div class="img-fluid col-3">
                    <?php if ($product['special']) { ?>
                        <span class="position-absolute mt-5 fs-4 ms-2 translate-middle badge bg-danger">
                            <?php echo $text_sale; ?>
                          </span>
                    <?php }
                    if ($product['new_product']) { ?>
                    <span class="new new_<?php echo $product['product_id']; ?>"></span>
                    <?php } ?>
                    <a href="<?php echo $item['info_url'] ?>"><?php echo $item['image'] ?></a>
                </div>

                <div class="col-9 d-flex flex-column">
                    <a class="text-decoration-none text-secondary card-title" href="<?php echo $item['info_url'] ?>">
                        <h2><?php echo $item['title'].' '. ($product['model'] ? "(".$product['model'].")" :''); ?></h2>
                    </a>
                    <div class="product-description-list mb-2"><?php echo $product['description'] ?></div>

                    <div class="blurb mb-2"><?php echo $product['blurb'] ?></div>
                    <?php echo $this->getHookvar('product_listing_details00_'.$product['product_id']);?>

                    <div class="d-flex justify-content-center p-1 mt-2 align-items-center">
                        <a class="btn btn-outline-secondary" href="<?php echo $item['info_url'] ?>">
                            <i class="fa fa-eye"></i>
                            <?php echo $button_view ?>
                        </a>
                        <?php if ($review_status) { ?>
                            <a class="btn btn-outline-secondary ms-2" href="<?php echo $item['info_url'] ?>#review">
                                <?php echo $review ?>
                            </a>
                        <?php }
                        echo $product['buttons']; ?>
                    </div>

                    <div class="d-flex p-2 mt-auto align-items-center">
                    <?php if ($display_price) { ?>
                        <div class="price text-muted d-flex align-items-center me-2">
                                <?php if ($product['special']) { ?>
                                    <div class="fs-4 text-black me-2"><?php echo $product['special'] . $tax_message; ?></div>
                                    <div class="fs-6 text-decoration-line-through"><?php echo $product['price']; ?></div>
                                <?php } else { ?>
                                    <div class="fs-4 text-black"><?php echo $product['price'] . $tax_message; ?></div>
                                <?php } ?>
                            </div>
                            <?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']); ?>
                        <div class="pricetag flex-item">
                            <?php if($product['call_to_order']){ ?>
                                <a data-id="<?php echo $product['product_id'] ?>"
                                   href="#"
                                   class="btn btn-success call_to_order"
                                   title="<?php echo $text_call_to_order ?>">
                                   <i class="fa fa-phone"></i>
                                </a>
                            <?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
                                <a class="btn btn-secondary disabled"><?php echo $text_out_of_stock; ?></a>
                            <?php } elseif ($this->getHookVar('product_add_to_cart_html_'.$product['product_id'])) {
                                echo $this->getHookVar('product_add_to_cart_html_'.$product['product_id']);
                                }else{ ?>
                                <a data-id="<?php echo $product['product_id'] ?>"
                                   href="<?php echo $item['buy_url'] ?>"
                                   class="btn btn-sm btn-success productcart"
                                   title="<?php echo_html2view($button_add_to_cart); ?>" >
                                    <i class="fa fa-cart-plus fa-xl"></i>
                                </a>
                            <?php
                                }
                            ?>
                        </div>
                        <?php
                        }
                        echo $this->getHookVar('product_price_hook_var_' . $product['product_id']); ?>
                    </div>
                </div>


<!--				<div class="row">-->
<!--					<div class="col-md-4">-->
<!--						--><?php //if ($product['special']) { ?>
<!--							<span class="sale sale_--><?php //echo $product['product_id']; ?><!--"></span>-->
<!--						--><?php //} ?>
<!--						--><?php //if ($product['new_product']) { ?>
<!--							<span class="new new_--><?php //echo $product['product_id']; ?><!--"></span>-->
<!--						--><?php //} ?>
<!--						--><?php //echo $this->getHookvar('product_listing_label_'.$product['product_id']);?>
<!--						<a href="--><?php //echo $item['info_url'] ?><!--">--><?php //echo $item['image'] ?><!--</a>-->
<!--					</div>-->
<!--					<div class="col-md-8">-->
<!--						<a class="prdocutname" href="--><?php //echo $item['info_url'] ?><!--">--><?php //echo $item['title'] ?>
<!--							--><?php //echo $product['model'] ? "(".$product['model'].")" :''; ?><!--</a>-->
<!--						<div class="productdiscrption">--><?php //echo $product['description'] ?><!--</div>-->
<!--						<div class="shortlinks">-->
<!--							<a class="details" href="--><?php //echo $item['info_url'] ?><!--">--><?php //echo $button_view ?><!--</a>-->
<!--							--><?php //if ($review_status) { ?>
<!--								<a class="compare"-->
<!--								   href="--><?php //echo $item['info_url'] ?><!--#review">--><?php //echo $review ?><!--</a>-->
<!--							--><?php //} ?>
<!--							--><?php //echo $product['buttons'];?>
<!--						</div>-->
<!--						<div class="blurb">--><?php //echo $product['blurb'] ?><!--</div>-->
<!--						--><?php //echo $this->getHookvar('product_listing_details00_'.$product['product_id']);?>
<!--						--><?php //if ($display_price) { ?>
<!--						<div class="pricetag pricetag_wide pull-right">-->
<!--							<span class="spiral"></span>-->
<!---->
<!--							--><?php //if($product['call_to_order']){ ?>
<!--								<a data-id="--><?php //echo $product['product_id'] ?><!--" href="#"-->
<!--									   class="btn call_to_order" title="--><?php //echo $text_call_to_order?><!--">-->
<!--									<i class="fa fa-phone fa-fw"></i>-->
<!--								</a>-->
<!--							--><?php //} else if ($product['track_stock'] && !$product['in_stock']) { ?>
<!--								<span class="nostock">--><?php //echo $product['no_stock_text']; ?><!--</span>-->
<!--							--><?php //} else { ?>
<!--								<a data-id="--><?php //echo $product['product_id'] ?><!--"-->
<!--								   href="--><?php //echo $item['buy_url'] ?><!--"-->
<!--								   class="productcart"-->
<!--								   title="--><?php //echo $button_add_to_cart ?><!--"-->
<!--								>-->
<!--									<i class="fa fa-cart-plus fa-fw"></i>-->
<!--								</a>-->
<!--							--><?php //} ?>
<!---->
<!---->
<!--							<div class="price">-->
<!--								--><?php //if ($product['special']) { ?>
<!--									<div class="pricenew">--><?php //echo $product['special'] . $tax_message; ?><!--</div>-->
<!--									<div class="priceold">--><?php //echo $product['price'] ?><!--</div>-->
<!--								--><?php //} else { ?>
<!--									<div class="oneprice">--><?php //echo $product['price'] . $tax_message; ?><!--</div>-->
<!--								--><?php //} ?>
<!--							</div>-->
<!--						</div>-->
<!--						--><?php //} ?>
<!--						--><?php //echo $this->getHookvar('product_listing_details11_'.$product['product_id']);?>
<!--					</div>-->
<!---->
<!--				</div>-->
			</div>
	<?php
	}
	?>
</div>