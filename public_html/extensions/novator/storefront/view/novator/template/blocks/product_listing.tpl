<?php if($products){
    $wishlist = $wishlist ?? $this->customer->getWishlist();
    $cartUrl = $this->html->getSecureURL( ($cart_rt ?:'checkout/cart'));

    ?>
    <div class="px-0">
<?php
        $cartProducts = $this->cart->getProducts();
        $cartProductIds = $cartProducts ? array_column($cartProducts,'product_id') : [];
        $cartProducts = array_column($cartProducts,'quantity','product_id');

        $text_items_in_the_cart = $this->language->get('text_cart_items','checkout/confirm');

        $tax_exempt = $this->customer->isTaxExempt();
        $config_tax = $this->config->get('config_tax');
        $text_sale = $this->language->get('text_badge_sale','novator/novator');
        $text_sale = $text_sale == 'text_badge_sale' ? 'SALE' : $text_sale;

        foreach ($products as $product) {
        $tax_message = '';
        if ($config_tax && !$tax_exempt && $product['tax_class_id']) {
                $tax_message = '&nbsp;&nbsp;' . $price_with_tax;
        }

        $product['thumb'] = $product['thumb'] ?? $product['image'];
        $item = [];
        $item['image'] = '<img class="img-fluid" src="'.$product['thumb']['thumb_url'].'" width="120" height="150">';
        $item['title'] = $product['name'];
        $item['description'] = $product['model'];
        $item['rating'] = renderRatingStarsNv($product['rating'], $product['stars']);

        $item['info_url'] = $product['href'];
        $item['buy_url'] = $product['add'];

        if (!$display_price) {
            $item['price'] = '';
        }

        $review = $button_write;
        if ($item['rating']) {
            $review = $item['rating'];
        }
        $inCart = in_array((int)$product['product_id'], $cartProductIds);
        ?>
                <div class="card prod-list-card position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <?php
                                if ($product['new_product']) { ?>
                                <span class="new new_<?php echo $product['product_id']; ?>"></span>
                                <?php } ?>
                                <a href="<?php echo $item['info_url'] ?>"><?php echo $item['image'] ?></a>
                            </div>

                            <div class="flex-grow-1 ms-3">
                                <div class="row align-items-center">
                                    <div class="col-sm-6">
                                        <?php if ($product['special']) { ?>
                                            <span class="special-badge sale_<?php echo $product['product_id']; ?> badge prod-badge text-white bg-warning">
                                                <?php echo $text_sale; ?>
                                            </span>
                                        <?php } ?>
                                        <h2 class="h6 mb-3">
                                            <a class="text-decoration-none text-secondary card-title" href="<?php echo $item['info_url'] ?>">
                                                <?php echo $item['title'].' '. ($product['model'] ? "(".$product['model'].")" :''); ?>
                                            </a>
                                        </h2>
                                      
                                        <ul class="list-inline mb-0">
                                            <?php if($this->customer->isLogged()){ ?>
                                            <li class="list-inline-item ">
                                                <a class="wish" href="javascript:void(0)">
                                                    <i class="bi <?php
                                                    echo isset($wishlist[$product['product_id']])
                                                        ? 'bi-heart-fill'
                                                        : 'bi-heart'; ?>"></i>
                                                </a>
                                            </li>
                                            <?php } ?>
                                            <li class="list-inline-item">
                                                <a href="<?php echo $item['info_url'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            </li>
                                            <li class="list-inline-item"><a href="#"><i class="bi bi-shuffle"></i></a></li>
                                        </ul>

                                        <div class="d-flex justify-content-center p-1 mt-2 align-items-center">
                                            
                                            <?php if ($review_status) { ?>
                                                <!-- <a class="btn btn-outline-secondary ms-2" href="<?php echo $item['info_url'] ?>#review">
                                                    <?php echo $review ?>
                                                </a> --> 
                                            <?php }
                                                echo $product['buttons']; 
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                        <?php if ($display_price) { ?>
                                            <div class="mb-2 prod-price price  me-2">
                                                     
                                                    <?php if ($product['special']) { ?>
                                                        <p class="price text-muted d-flex align-items-center justify-content-end">
                                                            <span class="pricenew text-danger prod-price mb-0 fw-semibold me-2"><?php echo $product['special'] . $tax_message; ?></span>
                                                            <span class="priceold text-muted prod-price mb-0 text-decoration-line-through"><?php echo $product['price']; ?></span>
                                                        </p>
                                                    <?php } else { ?>
                                                        <p class="mb-2 prod-price">
                                                            <span class="text-muted"><?php echo $product['price'] . $tax_message; ?></span>
                                                        </p>
                                                    <?php } ?>
                                            </div>
                                            <?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']); ?>
                                                <div class="pricetag flex-item ">
                                                    <?php if($product['call_to_order']){ ?>
                                                        <a data-id="<?php echo $product['product_id'] ?>"
                                                        href="<?php echo $this->html->getSeoUrl(
                                                            'content/contact',
                                                            '&product_id='.$product['product_id'].'&product_name='.$product['name']); ?>"
                                                        class="call_to_order btn btn-primary btn-sm text-white">
                                                        <i class="bi bi-telephone-fill"></i> <?php echo $text_call_to_order ?>
                                                        </a>
                                                    <?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
                                                        <a class="btn btn-outline-warning btn-sm mb-0 disabled"><?php echo $text_out_of_stock; ?></a>
                                                    <?php } elseif ($this->getHookVar('product_add_to_cart_html_'.$product['product_id'])) {
                                                        echo $this->getHookVar('product_add_to_cart_html_'.$product['product_id']);
                                                        }else{ ?>

                                                        <div class="position-relative btn btn-dark btn-sm mt-2">
                                                            <a class="add-to-cart text-white"
                                                               title="<?php $inCart ? echo_html2view($text_items_in_the_cart) : echo_html2view($button_add_to_cart); ?>"
                                                               data-id="<?php echo $product['product_id']; ?>"
                                                               href="<?php echo $inCart ? $cartUrl : $item['buy_url']; ?>">
                                                                <i class="bi <?php echo $inCart ? 'bi-bag-check-fill text-success' :'bi-bag-fill';?>"></i>
                                                                <?php echo_html2view($button_add_to_cart); ?>
                                                            </a>
                                                        </div>
                                                <?php } ?>
                                                </div>

                                            <?php
                                                }
                                            echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
                                        ?>
                                    </div>
                                    <div class="product-description-list my-2"><?php echo $product['description'] ?></div>
                                    <?php echo $this->getHookvar('product_listing_details00_'.$product['product_id']);?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php   } ?>
    </div>
<?php } ?>