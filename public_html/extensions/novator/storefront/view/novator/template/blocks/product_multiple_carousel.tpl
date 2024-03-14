<?php
$products = array_values((array)$products);
if($products){ ?>
<section class="product-sec">
<?php
    $carouselId = $carouselId ?: 'ProductMultiCarousel'.$this->instance_id;

    if ( $block_framed ) { ?>
    <div id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>"
    class="container block_frame block_frame_<?php echo $block_details['block_txt_id']; ?>">
    <div class="row title justify-content-center sec-heading-block text-center">
        <div class="col-xl-8">
            <h2><?php echo $heading_title; ?></h2>
            <p><?php echo $heading_subtitle; ?></p>
        </div>
    </div>
    <?php } ?>
            <div class="row g-4 mb-5 product-multi-carousel">
                <div id="<?php echo $carouselId; ?>" class="carousel m-0 p-0" >
                    <div class="carousel-inner m-0 d-flex flex-nowrap justify-content-between row" role="listbox">
            <?php
                            $text_items_in_the_cart = $this->language->get('text_cart_items','checkout/confirm');
                            $cartProducts = $this->cart->getProducts();
                            $cartProductIds = $cartProducts ? array_column($cartProducts,'product_id') : [];
                            $cartProducts = array_column($cartProducts,'quantity','product_id');
                            $cartUrl = $this->html->getSecureURL( ($cart_rt ?:'checkout/cart'));
                            $imgW = $imgW ?? $this->config->get('config_image_product_width');
                            $imgH = $imgH ?? $this->config->get('config_image_product_height');
                            $wishlist = $wishlist ?? $this->customer->getWishlist();
                            $text_sale = $this->language->get('text_badge_sale','novator/novator');
                            $text_sale = $text_sale == 'text_badge_sale' ? 'SALE' : $text_sale;
                            $tax_exempt = $this->customer->isTaxExempt();
                            $config_tax = $this->config->get('config_tax');
                            $productImgCss = 'width: '.$imgW.'px;';
                            $productImgCss .= ' height: '.$imgH.'px;';
                            $noRating = noRatingStarsNv($button_write);

                            foreach ($products as $i => $product) {
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
                                <div class="px-0 mx-0 carousel-item <?php echo !$i ? 'active' : '';?>">
                                    <div class="product-card card p-0 border-0 col-6 col-lg-3 "
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
                                            <?php if ($product['track_stock'] && !$product['in_stock']) { ?>
                                                <span class="sale_<?php echo $product['product_id']; ?>
									prod-badge badge rounded-0 position-absolute text-uppercase bg-white text-dark">
										<?php echo $text_out_of_stock; ?>
									</span>
                                            <?php }?>
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
                                               title="<?php echo $product['blurb'] ?: $item['title'] ?>" class="link-dark">
                                                <?php echo $item['title'] . $this->getHookvar('product_listing_name_'.$product['product_id']); ?>
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
                            }
                        ?>

                    </div>
                    <a class="carousel-control-prev bg-transparent w-auto"
                       href="#<?php echo $carouselId; ?>" role="button" data-bs-slide="prev">
                        <div class="carousel-control-prev-icon-box" aria-hidden="true">
                            <i class="bi bi-arrow-left"></i>
                        </div>
                    </a>
                    <a class="carousel-control-next bg-transparent w-auto"
                       href="#<?php echo $carouselId; ?>" role="button" data-bs-slide="next">
                        <div class="carousel-control-next-icon-box" aria-hidden="true">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
    <?php
    if ( $block_framed ) { ?>
        </div>
    <?php } ?>
</section>
<?php } ?>
