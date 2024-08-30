<div class="product-card card p-0 border-0"
     data-raw-price="<?php echo round($product['raw_price'],2)?>"
     data-product-id="<?php echo round($product['product_id'],2)?>">
    <div class="prod-img position-relative overflow-hidden">
        <a href="<?php echo $item['info_url'] ?>">
            <?php echo $item['image'] ?>
        </a>
        <?php if ($product['special']) { ?>
            <span class="sale_<?php echo $product['product_id']; ?> prod-badge bg-warning badge rounded-0 position-absolute text-uppercase text-white">
                <?php echo $text_sale; ?>
            </span>
        <?php } ?>
        <?php echo $this->getHookvar('product_listing_badge_'.$product['product_id']);?>
        <?php if ($product['track_stock'] && !$product['in_stock']) { ?>
            <span class="sale_<?php echo $product['product_id']; ?>
									prod-badge badge rounded-0 position-absolute text-uppercase bg-white text-dark">
										<?php echo $text_out_of_stock; ?>
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
                                <a class="share"
                                   data-title="<?php echo_html2view($product['name']);?>"
                                   data-url="<?php echo $item['info_url'];?>">
                                    <i class="bi bi-shuffle"></i>
                                </a>
                            </li>
                            <?php echo $this->getHookvar('product_button_'.$product['product_id']); ?>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <div class="add-to-cart-block">
                            <div class="qty-status-block">
                                <?php if ($display_price) { ?>
                                    <?php echo $this->getHookvar('product_listing_details1_'.$product['product_id']); ?>
                                    <?php if($product['call_to_order']){ ?>
                                        <p class="mb-0 text-capitalize">
                                            <a data-id="<?php echo $product['product_id'] ?>"
                                               href="<?php
                                               echo $this->html->getSeoUrl(
                                                   'content/contact',
                                                   '&product_id='.$product['product_id'].'&product_name='.$product['name']);
                                               ?>"
                                               class="call_to_order"
                                               title="<?php echo_html2view($text_call_to_order); ?>">
                                                <i class="bi bi-telephone-fill"></i>
                                                <?php echo $text_call_to_order; ?>
                                            </a>
                                        </p>
                                    <?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
                                        <p class="text-warning mb-0 disabled"><?php echo $text_out_of_stock; ?></p>
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
                                    <a class="add-to-cart"
                                       title="<?php $inCart ? echo_html2view($text_items_in_the_cart) : echo_html2view($button_add_to_cart); ?>"
                                       data-id="<?php echo $product['product_id']; ?>"
                                       href="<?php echo $inCart ? $cartUrl : $item['buy_url']; ?>">
                                        <i class="bi <?php echo $inCart ? 'bi-bag-check-fill text-success' :'bi-bag-fill';?>"></i>
                                        <?php echo_html2view($button_add_to_cart); ?>
                                    </a>
                                    <?php
                                }
                                ?>
                            <?php }
                            echo $this->getHookVar('product_price_hook_var_' . $product['product_id']);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h6 class="my-3">
        <a href="<?php echo $item['info_url'] ?>"
           title="<?php echo $product['blurb'] ?: $item['title'] ?>" class="link-dark product-name">
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