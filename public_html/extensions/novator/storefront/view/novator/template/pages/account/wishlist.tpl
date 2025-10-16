<div class="list-page-desc-block">
    <h1 class="h4 heading-title">
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
    <div id="product_cell_grid" class="product_cell_grid">
        <section class="product-sec wishlist product-list" >
    <?php if ( $block_framed ) { ?>

    <div id="block_frame_<?php echo $block_details['block_txt_id'] . '_' . $block_details['instance_id'] ?>"
         class="block_frame container block_frame_<?php echo $block_details['block_txt_id']; ?>">

        <?php } ?>
        <div class="row g-4">
            <?php
            $text_sale = $this->language->get('text_badge_sale');
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

                $item['image'] = '<img alt="'.html2view($product['thumb']['title'] ?: $product['name']).'" class="img-fluid h-auto" src="'.$product['thumb']['thumb_url'].'" style="'.$productImgCss.'">';
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
                $product['hide_wishlist'] = $product['hide_quickview'] = $product['hide_share'] = true;
                //hide text Add to cart
                $button_add_to_cart = '';

                $this->addHookVar(
                        'product_button_'.$product['product_id'],
                        '<li class="list-inline-item btn-compare">
                            <a href="Javascript:void(0);" title="'.html2view($button_remove_wishlist).'"
                            data-product_id="'.$product['product_id'].'"
                            class="remove-from-list"><i class="text-danger bi-trash"></i></a>
                        </li>'
                );
                ?>
                <div class="col-6 col-lg-3">
                    <?php
                    //render one card of product. It can be used by other tpls!
                    /** @see  product_card.tpl */
                    include($this->templateResource('/template/blocks/product_card.tpl')); ?>
                </div>
                <?php
            }
            echo $this->getHookVar('more_wishlist_products');
            ?>
        </div>
        <?php
        if ( $block_framed ) { ?>
    </div>
<?php } ?>
    <div class="py-3 col-12 d-flex flex-wrap justify-content-center">
        <?php echo $this->getHookVar('top_wishlist_buttons');
        $button_continue->style = 'btn btn-outline-secondary mx-2 mb-1';
        $button_continue->icon = 'fa fa-arrow-right';
        echo $button_continue;

        $button_cart->style = 'btn btn-success mx-2 mb-1';
        $button_cart->icon = 'fa fa-shopping-cart';
        echo $button_cart;
        echo $this->getHookVar('bottom_wishlist_buttons'); ?>
    </div>
</section>
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
                            target.parents('.product-card').fadeOut(500);
                        }
                    }
                });
        });
    });

</script>