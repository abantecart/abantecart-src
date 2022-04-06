<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easyzoom@2.5.3/css/easyzoom.css" />
<script src="https://cdn.jsdelivr.net/npm/easyzoom@2.5.3/src/easyzoom.js"></script>

<?php
$tax_exempt = $this->customer->isTaxExempt();
$config_tax = $this->config->get('config_tax');
$tax_message = '';

$add_w = $this->config->get('config_image_additional_width');
$add_h = $this->config->get('config_image_additional_height');

$thmb_w = $this->config->get('config_image_thumb_width');
$thmb_h = $this->config->get('config_image_thumb_height');


if ($error){ ?>
    <div class="alert alert-error alert-danger">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php echo is_array($error) ? implode('<br>', $error) : $error; ?></strong>
    </div>
<?php } ?>

<div id="product_details">
    <div class="d-flex flex-wrap align-content-center">
        <!-- Left Image-->
        <div class="text-center d-flex flex-nowrap mt-lg-4 mt-5">
            <ul class="thumbnails mainimage smallimage list-unstyled overflow-auto"
                style="max-height: <?php echo $thmb_w?>px">
        <?php
            if (sizeof((array)$images) > 1){
                foreach ($images as $image){ ?>
                    <li class="mb-3 pe-4 producthtumb"><?php
                        if ($image['origin'] != 'external'){?>
                            <a href="Javascript:void(0);" data-href="<?php echo $image['main_url']; ?>"
                               data-standard="<?php echo $image['thumb2_url']; ?>">
                                <img class="border" style="width: <?php echo $add_w; ?>px; height: <?php echo $add_h; ?>px;"
                                    src="<?php echo $image['thumb_url']; ?>" alt="<?php echo $image['title']; ?>"
                                    title="<?php echo $image['title']; ?>"/></a>
                        <?php }
                    ?></li>
            <?php
                }
            } ?>
            </ul>
            <div class="ps-1 pe-4 d-none d-md-block mainimage bigimage easyzoom easyzoom--overlay easyzoom--with-thumbnails">
                <?php
                if ($image_main){
                    //NOTE: ZOOM is not supported for embed image tags
                    if ($image_main['origin'] == 'external'){ ?>
                        <a class="html_with_image">
                            <?php echo $image_main['main_html']; ?>
                        </a>
                <?php
                    } else {
                        $image_url = $image_main['main_url'];
                        $thumb_url = $image_main['thumb_url'];
                ?>
                    <a class="local_image"
                       href="<?php echo $image_url; ?>"
                       target="_blank"
                       title="<?php echo $image_main['title']; ?>">
                           <img class="border"
                                style="width: <?php echo $thmb_w; ?>px; height: <?php echo $thmb_h; ?>px;"
                                src="<?php echo $thumb_url; ?>"
                                alt="<?php echo $image_main['title']; ?>"
                                title="<?php echo $image_main['title']; ?>"/>
                    </a>
                    <?php
                    }
                } ?>
            </div>
            <!-- for mobile devices-->
            <div class="mainimage bigimage d-md-none">
                <?php
                if ($image_main){
                    //NOTE: ZOOM is not supported for embed image tags
                    if ($image_main['origin'] == 'external'){
                        ?>
                        <a class="html_with_image">
                            <?php echo $image_main['main_html']; ?>
                        </a>
                <?php
                    } else{
                        $image_url = $image_main['main_url'];
                        $thumb_url = $image_main['thumb_url'];
                ?>
                        <a class="local_image">
                            <img class="border"
                                 style="width: <?php echo $thmb_w; ?>px; height: <?php echo $thmb_h; ?>px;"
                                 src="<?php echo $thumb_url; ?>"
                                 alt="<?php echo_html2view($image['title']); ?>"
                                 title="<?php echo_html2view($image['title']); ?>"/>
                        </a>
                    <?php }
                } ?>
            </div>

        </div>
        <!-- Right Details-->
        <div class="product-page-preset-box mt-lg-3 mt-md-4 mt-4 mt-sm-4 me-4 col-md-6 ms-lg-2">
            <div class="col-md-12 d-flex flex-column">
                    <h1><?php echo $heading_title; ?></h1>
                    <div class="blurb"><?php echo $product_info['blurb'] ?></div>
                    <div class="d-flex flex-column product-price mb-4">
                        <?php
                        if ($display_price){
                            $tax_message = '';
                            if($config_tax && !$tax_exempt && $tax_class_id){
                                $tax_message = '&nbsp;&nbsp;<span class="productpricesmall">'.$price_with_tax.'</span>';
                            }?>
                        <div class="price text-muted d-flex align-items-center">
                            <?php if ($special){ ?>
                                <div class="fs-3 text-black me-2 product-price">
                                    <?php echo $special . $tax_message; ?>
                                </div>
                                <span class="fs-4 text-decoration-line-through me-2 product-old-price">
                                    <?php echo $price; ?>
                                </span>
                            <?php } else { ?>
                                <div class="fs-2 text-black me-2 product-price">
                                    <?php echo $price . $tax_message; ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php }
                        if ($average){ ?>
                            <div class="text-warning fs-4 rating-stars"><?php echo renderRatingStars($average,''); ?></div>
                        <?php } ?>


                        <?php if($product_info['free_shipping'] && $product_info['shipping_price'] <= 0 && $can_buy) { ?>
                        <div class="alert alert-warning my-2 opacity-75 free-shipping-holder">
                            <i class="fa-solid fa-people-carry-box fa-xl me-2"></i><?php echo $text_free_shipping; ?>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="quantitybox">
                        <?php if ($display_price) {
                            echo $form['form_open']; ?>
                                <fieldset>
                                    <?php if ($options) {
                                            foreach ($options as $option) {
                                                if ( $option['html']->type == 'hidden') {
                                                    echo $option['html'];
                                                    continue;
                                                }
                                                ?>
                                            <div class="form-group mb-3">
                                                <?php
                                                    echo $this->getHookVar('product_option_'.$option['name'].'_additional_info');
                                                ?>
                                                <label class="control-label fw-bold"><?php echo $option['name']; ?></label>
                                                <div class="input-group">
                                                    <?php echo $option['html'];	?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php echo $this->getHookVar('extended_product_options'); ?>

                                    <?php if ($discounts) { ?>
                                        <div class="form-group">
                                            <label class="control-label"><?php echo $text_discount; ?></label>
                                            <table class="table table-striped">
                                                <thead>
                                                    <th><?php echo $text_order_quantity; ?></th>
                                                    <th><?php echo $text_price_per_item; ?></th>
                                                </thead>
                                            <?php foreach ($discounts as $discount) { ?>
                                                <tr>
                                                    <td><?php echo $discount['quantity']; ?></td>
                                                    <td><?php echo $discount['price']; ?></td>
                                                </tr>
                                            <?php } ?>
                                            </table>
                                        </div>
                                    <?php } ?>

                                    <?php if(!$product_info['call_to_order']){ ?>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text fs-3"><?php echo $text_qty; ?></span>
                                            <?php if ($minimum > 1) { ?>
                                                <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo_html2view($text_minimum);?>">&gt;= <?php echo $minimum; ?></span>
                                            <?php } ?>
                                            <?php echo $form['minimum'];
                                            if ($maximum > 0) { ?>
                                            <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo_html2view($text_maximum);?>">&lt;= <?php echo $maximum; ?></span>
                                            <?php } ?>
                                        </div>
                                        <div class="m-4 total-price-holder d-flex">
                                            <div class="fs-5 fw-3 me-2 mt-auto"><?php echo $text_total_price; ?></div>
                                            <div class="fs-3 fw-3 total-price mt-auto"><i class="ms-2 fa-solid fa-spinner fa-spin"></i></div>
                                            <div class="fs-3 fw-3 mt-auto"><?php echo $tax_message; ?></div>
                                        </div>
                                    <?php }?>

                                    <div>
                                        <?php echo $form['product_id'] . $form['redirect']; ?>
                                    </div>

                                    <div class="mt-2 d-flex flex-column">
                                        <?php
                                        if(!$product_info['call_to_order']){
                                            if (!$can_buy) { ?>
                                                <div class="alert alert-warning my-2 no-stock-alert">
                                                    <label class="control-label"><?php echo $stock; ?></label>
                                                </div>
                                        <?php
                                            } else { ?>
                                                <div class="product-page-add2cart mt-3">
                                                    <?php if(!$this->getHookVar('product_add_to_cart_html')) { ?>
                                                        <a class="shadow cart btn btn-success btn-lg w-100"
                                                           href="#" onclick="$(this).closest('form').submit(); return false;" >
                                                            <i class="fa-solid fa-cart-plus fa-fw"></i>
                                                            <?php echo $button_add_to_cart; ?>
                                                        </a>
                                                        <?php } else { ?>
                                                            <?php echo $this->getHookVar('product_add_to_cart_html'); ?>
                                                        <?php } ?>
                                                </div>
                                        <?php
                                            }
                                        } else {
                                            if($this->getHookVar('product_call_to_order_html')) {
                                                echo $this->getHookVar('product_call_to_order_html');
                                            } else { ?>
                                            <div class="product-page-call_to_order mt-3">
                                                <a href="#" class="shadow cart btn btn-success btn-lg w-100">
                                                    <i class="fa-solid fa-phone fa-fw"></i>&nbsp;&nbsp;
                                                    <?php echo $text_call_to_order; ?>
                                                </a>
                                            </div>
                                            <?php }
                                        } ?>
                                        <div class="d-flex flex-wrap align-items-start justify-content-evenly mt-3 ">
                                            <a class="border product-page-print btn btn-outline-secondary mb-2" href="javascript:window.print();">
                                                <i class="fa-solid fa-print fa-xl"></i>
                                                <?php echo $button_print; ?>
                                            </a>
                                            <?php echo $this->getHookVar('buttons');
                                        if ($is_customer) { ?>
                                            <div class="wishlist mb-2">
                                                <a id="wishlist_remove"
                                                   class="border btn btn-outline-secondary <?php echo $in_wishlist ? 'd-block': 'd-none';?>"
                                                   href="Javascript:void(0);"
                                                   onclick="wishlist_remove(); return false;">
                                                    <i class="fa-solid fa-heart-crack fa-xl"></i>
                                                    <?php echo $button_remove_wishlist; ?>
                                                </a>
                                                <a id="wishlist_add"
                                                   class="border btn btn-outline-secondary <?php echo $in_wishlist ? 'd-none': 'd-block';?>"
                                                   href="Javascript:void(0);"
                                                   onclick="wishlist_add(); return false;">
                                                    <i class="fa fa-solid fa-heart-circle-plus fa-xl"></i>
                                                    <?php echo $button_add_wishlist; ?>
                                                </a>
                                        </div>
                                    <?php } ?>
                                        </div>
                                    </div>
                                </fieldset>
                                </form>
                            <?php } elseif(!$product_info['call_to_order']) { ?>
                                <div class="alert alert-warning mt-5">
                                        <?php echo $text_login_view_price; ?>
                                </div>
                            <?php } ?>

                    </div>
                </div>
        </div>
    </div>
</div>

<!-- Product Description tab & comments-->
<div id="productdesc">
    <div class="row">
        <div class="col-md-12 productdesc">
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a href="#description"><?php echo $tab_description; ?></a></li>
                <?php if ($display_reviews || $review_form_status){ ?>
                    <li><a href="#review"><?php echo $tab_review; ?></a></li>
                <?php } ?>
                <?php if ($tags){ ?>
                    <li><a href="#producttag"><?php echo $text_tags; ?></a></li>
                <?php } ?>
                <?php if ($related_products){ ?>
                    <li><a href="#relatedproducts"><?php echo $tab_related; ?> (<?php echo sizeof((array)$related_products); ?>)</a></li>
                <?php } ?>
                <?php if ($downloads){ ?>
                    <li><a href="#productdownloads"><?php echo $tab_downloads; ?></a></li>
                <?php } ?>
                <?php echo $this->getHookVar('product_features_tab'); ?>
            </ul>
            <div class="tab-content">

                <div class="tab-pane active" id="description">
                    <?php echo $description; ?>

                    <ul class="productinfo">
                        <?php if ($stock){ ?>
                            <li>
                                <span class="productinfoleft"><?php echo $text_availability; ?></span> <?php echo $stock; ?>
                            </li>
                        <?php } ?>
                        <?php if ($model){ ?>
                            <li><span class="productinfoleft"><?php echo $text_model; ?></span> <?php echo $model; ?>
                            </li>
                        <?php } ?>
                        <?php if ($manufacturer){ ?>
                            <li>
                                <span class="productinfoleft"><?php echo $text_manufacturer; ?></span>
                                <a href="<?php echo $manufacturers; ?>">
                                    <?php if ($manufacturer_icon){ ?>
                                        <img alt="<?php echo $manufacturer; ?>"
                                             src="<?php echo $manufacturer_icon; ?>"
                                             title="<?php echo $manufacturer; ?>"
                                             style="width: <?php echo $this->config->get('config_image_grid_width'); ?>px;"/>
                                        <?php
                                    } else{
                                        echo $manufacturer;
                                    } ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                </div>

                <?php if (($display_reviews || $review_form_status)){ ?>
                    <div class="tab-pane" id="review">
                        <div id="current_reviews" class="mb20"></div>
                        <?php if($review_form_status){ ?>
                        <div class="heading" id="review_title"><h4><?php echo $text_write; ?></h4></div>
                        <div class="content">
                            <fieldset>
                                <div class="form-group">
                                    <div class="form-inline">
                                        <label class="control-label col-md-3 pull-left"><?php echo $entry_rating; ?>
                                            <span
                                                    class="red">*</span></label>
                                        <?php echo $rating_element; ?>
                                    </div>
                                </div>
                                <div class="form-group mt40">
                                    <div class="form-inline">
                                        <label class="control-label col-md-3"><?php echo $entry_name; ?> <span
                                                    class="red">*</span></label>
                                        <?php echo $review_name; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-inline">
                                        <label class="control-label col-md-3"><?php echo $entry_review; ?> <span
                                                    class="red">*</span></label>
                                        <?php echo $review_text; ?>
                                    </div>
                                    <div class="input-group"><?php echo $text_note; ?></div>
                                </div>
                                <?php if ($review_recaptcha){ ?>
                                    <div class="clear form-group">
                                        <div class="form-inline col-md-6 col-md-offset-1 col-sm-6">
                                            <?php echo $review_recaptcha; ?>
                                        </div>
                                        <div class="form-inline col-md-5 col-sm-6">
                                            <?php echo $review_button; ?>
                                        </div>
                                    </div>
                                <?php } else{ ?>
                                    <div class="clear form-group">
                                        <label class="control-label"><?php echo $entry_captcha; ?> <span
                                                    class="red">*</span></label>

                                        <div class="form-inline">
                                            <label class="control-label col-md-3">
                                                <img src="<?php echo $captcha_url; ?>" id="captcha_img" alt=""/>
                                            </label>
                                            <?php
                                            echo $review_captcha;
                                            echo $review_button; ?>
                                        </div>
                                    </div>
                                <?php } ?>

                            </fieldset>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($tags){ ?>
                    <div class="tab-pane" id="producttag">
                        <ul class="tags">
                            <?php foreach ($tags as $tag){ ?>
                                <li><a href="<?php echo $tag['href']; ?>"><i
                                                class="fa fa-tag"></i><?php echo $tag['tag']; ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <?php if ($related_products){ ?>
                    <div class="tab-pane" id="relatedproducts">
                        <ul class="row side_prd_list">
                            <?php foreach ($related_products as $related_product){
                                $tax_message = '';
                                $item['rating'] = ($related_product['rating']) ? "<img src='" . $this->templateResource('/image/stars_' . $related_product['rating'] . '.png') . "' class='rating' alt='" . $related_product['stars'] . "' width='64' height='12' />" : '';
                                if (!$display_price){
                                    $related_product['price'] = $related_product['special'] = '';
                                } else {
                                    if($config_tax && !$tax_exempt && $related_product['tax_class_id']){
                                        $tax_message = '&nbsp;&nbsp;'.$price_with_tax;
                                    }
                                }
                            ?>
                                <li class="col-md-3 col-sm-5 col-xs-6 related_product">
                                    <a href="<?php echo $related_product['href']; ?>"><?php echo $related_product['image']['thumb_html'] ?></a>
                                    <a class="productname" title="<?php echo $related_product['name']; ?>"
                                       href="<?php echo $related_product['href']; ?>"><?php echo $related_product['name']; ?></a>
                                    <span class="procategory"><?php echo $item['rating'] ?></span>

                                    <div class="price">
                                        <?php if ($related_product['special']){ ?>
                                            <span class="pricenew"><?php echo $related_product['special'] . $tax_message ?></span>
                                            <span class="priceold"><?php echo $related_product['price'] ?></span>
                                        <?php } else{ ?>
                                            <span class="oneprice"><?php echo $related_product['price'] . $tax_message ?></span>
                                        <?php } ?>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <?php if ($downloads){ ?>
                    <div class="tab-pane" id="productdownloads">
                        <ul class="list-group">
                            <?php foreach ($downloads as $download){ ?>
                                <li class="list-group-item">
                                    <a class="pull-right btn btn-default"
                                       href="<?php echo $download['button']->href; ?>"><i
                                                class="fa fa-download"></i> <?php echo $download['button']->text; ?></a>

                                    <div><?php echo $download['name']; ?>
                                        <div class="download-list-attributes">
                                            <?php foreach ($download['attributes'] as $name => $value){
                                                echo '<small>- ' . $name . ': ' . (is_array($value) ? implode(' ', $value) : $value) . '</small>';
                                            } ?></div>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <?php echo $this->getHookVar('product_features'); ?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function load() {
        //waiting for jquery loaded!
        if (!window.jQuery) return setTimeout(load, 50);
        //jQuery-depended code

        var orig_imgs = $('div.bigimage').html();
        var orig_thumbs = $('ul.smallimage').html();

        start_easyzoom();
        display_total_price();

        $('#current_reviews .pagination a').on('click', function () {
            $('#current_reviews').slideUp('slow')
                .load(this.href)
                .slideDown('slow');
            return false;
        });

        reload_review('<?php echo $product_review_url; ?>');


        $('#product_add_to_cart').click(function () {
            $('#product').submit();
        });
        $('#review_submit').click(function () {
            review();
        });

        //process clicks in review pagination
        $('#current_reviews').on('click', '.pagination a', function () {
            reload_review($(this).attr('href'));
            return false;
        });

        /* Process images for product options */
        var $select = $('input[name^=\'option\'], select[name^=\'option\']');
        $select.change(function () {
            var valId = $(this).val();
            valId = this.type === 'checkbox' && $(this).attr('data-attribute-value-id') ? $(this).attr('data-attribute-value-id') : valId;
            //skip not selected radio
            if ((this.type === 'radio' || this.type === 'checkbox') && $(this).prop('checked') === false) {
                return false;
            }
            load_option_images(valId, '<?php echo $product_id; ?>');
            display_total_price();
        });

        $('input[name=quantity]').on(
            'change keyup',
            function () {
                display_total_price();
            }
        );


        $.ajax({
            url: '<?php echo $update_view_count_url; ?>',
            type: 'GET',
            dataType: 'json'
        });

        $select.change();

        function start_easyzoom() {
            // Instantiate EasyZoom instances
            var $easyzoom = $('.easyzoom').easyZoom();

            // Get an instance API
            var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');
            //clean and reload existing events
            api1.teardown();
            api1._init();

            // Setup thumbnails
            $('.thumbnails .producthtumb').on('click', 'a', function (e) {
                var $this = $(this);
                e.preventDefault();
                // Use EasyZoom's `swap` method
                api1.swap($this.data('standard'), $this.attr('data-href'));
                $('.mainimage.bigimage.hidden-lg').find('img').attr('src', $this.attr('data-href'));
            });
        }

        function load_option_images(attribute_value_id, product_id) {
            var selected = {};
            var k = 0;
            $('[name^=\'option\']').each(function () {
                var valId = $(this).val();
                valId = this.type === 'checkbox' && $(this).attr('data-attribute-value-id') ? $(this).attr('data-attribute-value-id') : valId;
                //skip not selected radio
                if ((this.type === 'radio' || this.type === 'checkbox') && $(this).prop('checked') === false) {
                    return;
                }
                //exclude just clicked option
                if (valId === attribute_value_id) {
                    return;
                }
                selected[k] = valId;
                k++;
            });

            var data = {
                attribute_value_id: attribute_value_id,
                product_id: product_id,
                selected_options: selected
            };

            $.ajax({
                type: 'POST',
                url: '<?php echo $option_resources_url; ?>',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.length === 0) {
                        return false;
                    }
                    var html1 = '',
                        html2 = '',
                        main_image = data.main;

                    if (main_image) {
                        if (main_image.origin === 'external') {
                            html1 = '<a class="html_with_image">';
                            html1 += main_image.main_html + '</a>';
                        } else {
                            html1 = '<a style="width:' + main_image.thumb_width + 'px; height:' + main_image.thumb_height + 'px;" class="local_image" href="' + main_image.main_url + '">';
                            html1 += '<img style="width:' + main_image.thumb_width + 'px; height:' + main_image.thumb_height + 'px;" src="' + main_image.thumb_url + '" />';
                            html1 += '<i class="fa fa-arrows  hidden-xs hidden-sm"></i></a>';
                        }
                    }
                    if (data.images.length > 0) {
                        for (var img in data.images) {
                            var image = data.images[img];
                            html2 += '<li class="producthtumb">';
                            var img_url = image.main_url;
                            var tmb_url = image.thumb_url;
                            var tmb2_url = image.thumb2_url;
                            if (image.origin !== 'external') {
                                html2 += '<a data-href="' + image.main_url + '" href="' + img_url + '" data-standard="' + tmb2_url + '"><img style="width:' + image.thumb_width + 'px; height:' + image.thumb_height + 'px;" src="' + tmb_url + '" alt="' + image.title + '" title="' + image.title + '" /></a>';
                            }
                            html2 += '</li>';
                        }
                    } else {
                        //no images - no action
                        return false;
                    }
                    $('div.bigimage').each(function () {
                        $(this).html(html1)
                    });
                    $('ul.smallimage').each(function () {
                        $(this).html(html2);
                    });
                    start_easyzoom();
                }
            });
        }

        function display_total_price() {

            $.ajax({
                type: 'POST',
                url: '<?php echo $calc_total_url;?>',
                dataType: 'json',
                data: $("#product").serialize(),

                success: function (data) {
                    if (data && data.total) {
                        $('.total-price-holder').show()
                            .css('visibility', 'visible');
                        $('.total-price').html(data.total);
                    }
                }
            });

        }

        function reload_review(url) {
            $('#current_reviews').load(url);
        }

        function review() {
            var dismiss = '<button type="button" class="close" data-dismiss="alert">&times;</button>';

            <?php if ($review_recaptcha) { ?>
            var captcha = '&g-recaptcha-response=' + encodeURIComponent($('[name=\'g-recaptcha-response\']').val());
            <?php } else { ?>
            var captcha = '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val());
            <?php } ?>

            $.ajax({
                type: 'POST',
                url: '<?php echo $product_review_write_url;?>',
                dataType: 'json',
                data: 'name='
                    + encodeURIComponent($('input[name=\'name\']').val())
                    + '&text='
                    + encodeURIComponent($('textarea[name=\'text\']').val())
                    + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + captcha,
                beforeSend: function () {
                    $('.success, .warning').remove();
                    $('#review_button').attr('disabled', 'disabled');
                    $('#review_title').after('<div class="wait"><i class="fa-solid fa-spinner fa-spin"></i> <?php echo $text_wait; ?></div>');
                },
                complete: function () {
                    $('#review_button').attr('disabled', '');
                    $('.wait').remove();
                    <?php if ($review_recaptcha) { ?>
                    try {
                        grecaptcha.reset();
                    } catch (e) {
                    }
                    try {
                        ReCaptchaCallbackV3();
                    } catch (e) {
                    }

                    <?php } ?>
                    try {
                        resetLockBtn();
                    } catch (e) {
                    }
                },
                error: function (jqXHR, exception) {
                    var text = jqXHR.statusText + ": " + jqXHR.responseText;
                    $('#review .alert').remove();
                    $('#review_title').after('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
                },
                success: function (data) {
                    if (data.error) {
                        $('#review .alert').remove();
                        $('#review_title').after('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
                    } else {
                        $('#review .alert').remove();
                        $('#review_title').after('<div class="alert alert-success">' + dismiss + data.success + '</div>');

                        $('input[name=\'name\']').val('');
                        $('textarea[name=\'text\']').val('');
                        $('input[name=\'rating\']:checked').attr('checked', '');
                        $('input[name=\'captcha\']').val('');
                    }
                    $('img#captcha_img').attr('src', $('img#captcha_img').attr('src') + '&' + Math.random());
                }
            });
        }





    });

    function wishlist_add() {
        var dismiss = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        $.ajax({
            type: 'POST',
            url: '<?php echo $product_wishlist_add_url; ?>',
            dataType: 'json',
            beforeSend: function () {
                $('#wishlist_add').removeClass('d-block').addClass('d-none')
                    .after('<div class="wait alert alert-secondary p-1 mb-0"><i class="fa-solid fa-spinner fa-spin"></i> <?php echo $text_wait; ?></div>');
            },
            complete: function () {
                $('.wishlist .wait').remove();
            },
            error: function (jqXHR, exception) {
                var text = jqXHR.statusText + ": " + jqXHR.responseText;
                $('.wishlist .alert').remove();
                $('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
                $('#wishlist_add').removeClass('d-none').addClass('d-block');
            },
            success: function (data) {
                if (data.error) {
                    $('.wishlist .alert').remove();
                    $('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
                    $('#wishlist_add').removeClass('d-none').addClass('d-block');
                } else {
                    $('.wishlist .alert').remove();
                    $('#wishlist_remove').removeClass('d-none').addClass('d-block');
                }
            }
        });
    }

    function wishlist_remove() {
        var dismiss = '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        $.ajax({
            type: 'POST',
            url: '<?php echo $product_wishlist_remove_url; ?>',
            dataType: 'json',
            beforeSend: function () {
                $('#wishlist_remove').removeClass('d-block').addClass('d-none')
                    .after('<div class="wait alert alert-secondary p-1 mb-0"><i class="fa-solid fa-spinner fa-spin"></i> <?php echo $text_wait; ?></div>');
            },
            complete: function () {
                $('.wishlist .wait').remove();
            },
            error: function (jqXHR, exception) {
                var text = jqXHR.statusText + ": " + jqXHR.responseText;
                $('.wishlist .alert').remove();
                $('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + text + '</div>');
                $('#wishlist_remove').removeClass('d-none').addClass('d-block');
            },
            success: function (data) {
                if (data.error) {
                    $('.wishlist .alert').remove();
                    $('.wishlist').after('<div class="alert alert-error alert-danger">' + dismiss + data.error + '</div>');
                    $('#wishlist_remove').removeClass('d-none').addClass('d-block');
                } else {
                    $('.wishlist .alert').remove();
                    $('#wishlist_add').removeClass('d-none').addClass('d-block');
                }
            }
        });
    }
</script>
