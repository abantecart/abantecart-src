<?php if($products){
    $text_items_in_the_cart = $this->language->get('text_cart_items','checkout/confirm');

    $cartProducts = $this->cart->getProducts();
    $cartProductIds = $cartProducts ? array_column($cartProducts,'product_id') : [];
    $cartProducts = array_column($cartProducts,'quantity','product_id');

    $imgW = $imgW ?? $this->config->get('config_image_product_width');
    $imgH = $imgH ?? $this->config->get('config_image_product_height');
?>
<section id="<?php echo $homeBlockId;?>">
    <div class="container-fluid product-flex">
        <?php if ( $block_framed ) { ?>
        <div id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>"
             class="mt-5 block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>">
            <h2 class="ps-3 pe-2"><?php echo $heading_title; ?></h2>
            <h6 class="ps-3 pe-2"><?php echo $heading_subtitle; ?></h6>
        <?php } ?>

            <div class="d-flex flex-wrap block-product-items justify-content-evenly">
            <?php
                    $text_sale = $this->language->get('text_badge_sale','bootstrap5/bootstrap5');
                    $text_sale = $text_sale == 'text_badge_sale' ? 'SALE' : $text_sale;
                    $tax_exempt = $this->customer->isTaxExempt();
                    $config_tax = $this->config->get('config_tax');
                    $productImgCss = 'width: '.$imgW.'px;';
                    $productImgCss .= ' height: '.$imgH.'px;';

                    foreach ($products as $product) {
                        $tax_message = '';
                        if ($config_tax && !$tax_exempt && $product['tax_class_id']) {
                                $tax_message = '&nbsp;&nbsp;' . $price_with_tax;
                        }

                        $product['thumb'] = $product['thumb'] ?? $product['image'];
                        $item = [];
                        $item['image'] = '<img class="m-3" src="'.$product['thumb']['thumb_url'].'" style="'.$productImgCss.'">';
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
                        <div class="card m-3 col-4 shadow ">
                            <a class="link-light card-link text-decoration-none"
                                    href="<?php echo $item['info_url'] ?>"
                                    title="<?php echo $item['title'] ?>">
                                <div class="card-title bg-gradient bg-secondary p-3 text-center">
                                    <?php echo $item['title']
                                        . $this->getHookvar('product_listing_name_'.$product['product_id']);
                                    ?>
                                </div>
                            </a>
                            <div class="card-img-top text-center">
                                <?php if ($product['special']) { ?>
                                    <span class="position-absolute mt-5 fs-4 ms-2 translate-middle badge bg-danger">
                                        <?php echo $text_sale; ?>
                                      </span>
                                <?php }
                                if ($product['new_product']) { ?>
                                <span class="new new_<?php echo $product['product_id']; ?>"></span>
                                <?php } ?>
                                <a href="<?php echo $item['info_url'] ?>"><?php echo $item['image'] ?></a>
                                <div class="d-flex justify-content-center p-1 mt-2 align-items-center">
                                    <a class="shadow btn btn-light" href="<?php echo $item['info_url'] ?>">
                                        <i class="fa fa-eye"></i>
                                        <?php echo $button_view ?>
                                    </a>
                                    <?php if ($review_status) { ?>
                                        <a class="shadow btn btn-light ms-2" href="<?php echo $item['info_url'] ?>#review">
                                            <?php echo $review ?>
                                        </a>
                                    <?php }
                                    echo $product['buttons']; ?>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="card-text blurb" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo_html2view($product['blurb']); ?>">
                                    <?php echo $product['blurb'] ?>
                                </div>
                                <?php echo $this->getHookvar('product_listing_details0_'.$product['product_id']);?>
                                <div class="d-flex justify-content-between p-2 mt-auto align-items-center bg-light">
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
                                               href="<?php echo $this->html->getSeoUrl('content/contact');?>"
                                               class="btn btn-success call_to_order"
                                               title="<?php echo_html2view($text_call_to_order); ?>">
                                               <i class="fa fa-phone"></i>
                                            </a>
                                        <?php } else if ($product['track_stock'] && !$product['in_stock']) { ?>
                                            <a class="btn btn-secondary disabled"><?php echo $text_out_of_stock; ?></a>
                                        <?php } elseif ($this->getHookVar('product_add_to_cart_html_'.$product['product_id'])) {
                                            echo $this->getHookVar('product_add_to_cart_html_'.$product['product_id']);
                                            }else{ ?>
                                            <div class="position-relative btn btn-sm btn-success">
                                                <a class="text-decoration-none text-white"
                                                   href="<?php echo $this->html->getSecureURL('checkout/cart'); ?>">
                                                    <i title="<?php echo_html2view($text_add_cart_confirm); ?>"
                                                       class="<?php echo !in_array((int)$product['product_id'], $cartProductIds) ? 'visually-hidden ' : '';?>fa fa-check fa-xl me-2 text-warning"></i>
                                                </a>
                                                <span class="visually-hidden spinner-border spinner-border-sm" aria-hidden="true"></span>
                                                <a class="text-decoration-none text-white add-to-cart"
                                                   data-id="<?php echo $product['product_id']; ?>"
                                                   href="<?php echo $item['buy_url'] ?>">
                                                <i title="<?php echo_html2view($button_add_to_cart); ?>"
                                                   class="fa fa-cart-plus fa-xl"></i>
                                                </a>
                                            <?php if($cartProducts[(int)$product['product_id']]){?>
                                                <span title="<?php echo_html2view($text_items_in_the_cart); ?>"
                                                        class="item-qty-badge position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark border border-2 border-success">
                                                    <?php echo $cartProducts[(int)$product['product_id']];?>
                                                </span>
                                            <?php }?>
                                            </div>
                                        <?php
                                            }
                                        ?>
                                    </div>
                                    <?php
                                    }
                                    echo $this->getHookVar('product_price_hook_var_' . $product['product_id']); ?>
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
    </div>
</section>
<?php } ?>