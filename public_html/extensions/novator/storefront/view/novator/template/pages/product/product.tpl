<?php
$tax_exempt = $this->customer->isTaxExempt();
$config_tax = $this->config->get('config_tax');
$tax_message = '';

$add_w = $this->config->get('config_image_additional_width');
$add_h = $this->config->get('config_image_additional_height');

$thmb_w = $this->config->get('config_image_thumb_width');
$thmb_h = $this->config->get('config_image_thumb_height');

if ($error){ ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <?php echo is_array($error) ? implode('<br>', $error) : $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div id="product_details" class="mt-4">
    <div class="row justify-content-between">
        <!-- Left Image-->
        <div class="col-md-12 col-lg-6 col-xxl-5 text-center">
            <div class="w-auto sticky-md-top product-sticky">
                <div class="zoom-pane position-absolute col-12"></div>
                <div id="carouselProductImages" class="carousel slide mx-auto" data-bs-ride="carousel" style="max-width: <?php echo $thmb_w;?>px !important;">
                    <div class="carousel-inner bg-light rounded position-relative">
                        <!-- Main Image -->
                        <?php foreach ($images as $index => $image) {
                            $image['title'] = $image['title'] ? : $heading_title;
                            $image['description'] = $image['description'] ? : $heading_title; ?>
                            <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>" >
                            <?php
                                if ($image['origin'] == 'external') {
                                    echo $image['main_html'];
                                } else { ?>
                                        <style>
                                            @media (min-width: 577px) {
                                                .img<?php echo $index?> {
                                                    width: <?php echo $image['thumb2_width'];?>px;
                                                    height: <?php echo $image['thumb2_height'];?>px;
                                                }
                                            }
                                        </style>
                                    <img class="zoom-trigger d-block mx-auto w-auto product-main-img img<?php echo $index?>"
                                         src="<?php echo $image['thumb2_url']; ?>"
                                         data-zoom="<?php echo $image['main_url']; ?>"
                                         alt="<?php echo_html2view($image['title']); ?>"
                                         title="<?php echo_html2view($image['description']); ?>" />
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <ul class="thumbnails carousel-indicators position-relative product-carousel-indicators my-sm-3 mx-0 col-12 justify-content-left">
                        <?php
                        if (sizeof((array)$images) > 1) {
                            $imageCount = sizeof($images);
                            foreach ($images as $i => $image) {
                                if ($image['origin'] != 'external') {
                                    ?>
                                    <li data-bs-target="#carouselProductImages" data-bs-slide-to="<?php echo $i; ?>"
                                        class="product-thumb w-auto h-auto <?php echo ($i === 0) ? 'active' : ''; ?> ">
                                            <img class="d-block wid-100 rounded "
                                                 src="<?php echo $image['thumb_url']; ?>"
                                                 style="max-width: <?php echo $image['thumb_width'];?>px; max-height: <?php echo $image['thumb_height'];?>px;"
                                                 alt="<?php echo_html2view($image['title']); ?>"
                                                 title="<?php echo_html2view($image['description']); ?>">
                                    </li>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Right Details-->
        <div class="col-md-12 col-lg-6  col-xxl-7 position-relative product-page-preset-box mt-4 mt-md-0">
            <div class="row g-1">
                <div class="col-sm-9" >
                    <h1 class="h3" style="width: 100%;"><?php echo $heading_title; ?></h1>
                    <?php if($manufacturer){?>
                        <h6 class="my-2 text-warning"><u><a class="my-2 text-warning" href="<?php echo $manufacturers;  ?>"><?php echo $manufacturer; ?></a></u></h6>
                    <?php }?>
                    <?php if($blurb){?>
                    <p class="text-muted"><?php echo html_entity_decode($blurb); ?></p>
                    <?php }?>
                </div>
                <?php echo $this->getHookVar('after_product_blurb');?>
                <div class="col-sm-3">
                    <div class="d-flex justify-content-sm-end gap-2">
                        <?php
                        if ($is_customer) { ?>
                            <div class="wishlist d-flex align-items-center justify-content-between">
                                <a id="wishlist_remove" class="bg-light-danger badge fs-6 <?php echo $in_wishlist ? 'd-block': 'd-none';?>" href="Javascript:void(0);">
                                    <i class="bi bi-heart-fill"></i>
                                </a>
                                <a id="wishlist_add"
                                class="bg-light-danger badge fs-6 <?php echo $in_wishlist ? 'd-none': 'd-block';?>" href="Javascript:void(0);">
                                    <i class="bi bi-heart"></i>
                                </a>
                            </div>
                        <?php } ?>
                        <a href="javascript:void(0)"
                           class="share bg-light-secondary badge fs-6"
                           data-title="<?php echo_html2view($heading_title);?>"
                           data-url="<?php echo_html2view( $product_url);?>">
                        <i class="bi bi-share"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row g-1">
                <div class="col-sm-6">
                    <div class="d-flex flex-column product-price mb-0">
                        <?php
                        if ($display_price){ ?>
                            <input id="product_price_num" type="hidden" disabled value="<?php echo round(($special_num ?: $price_num), 2);?>">
                            <input id="product_total_num" type="hidden" disabled value="">
                        <?php
                            $tax_message = '';
                            if($config_tax && !$tax_exempt && $tax_class_id){
                                $tax_message = '&nbsp;&nbsp;<span class="productpricesmall">'.$price_with_tax.'</span>';
                            }?>

                            <div class="col-sm-6">
                                <?php if ($special) { ?>
                                    <h2 class="mb-0 text-danger"><?php echo $special . $tax_message; ?></h2>
                                    <h5 class="my-2 text-muted fw-normal">
                                        <del><?php echo $price; ?></del>
                                    </h5>
                                <?php } else { ?>
                                    <h2 class="mb-0 text-primary"><?php echo $price . $tax_message; ?></h2>
                                <?php } ?>
                            </div>

                        <?php } ?>
                    </div>
                </div>
                <div class="col-sm-6 d-flex justify-content-end align-items-center">
                   <?php if ($display_price){ ?>
                            
                        <?php
                            $tax_message = '';
                            if($config_tax && !$tax_exempt && $tax_class_id){
                                $tax_message = '&nbsp;&nbsp;<span class="productpricesmall">'.$price_with_tax.'</span>';
                            }?>

                        <?php }

                        if ((float)$average  && $display_reviews){ ?>
                            <div class="rounded-pill bg-light-warning badge fs-6"><i class="bi bi-star"></i> <?php echo $average;?></div>
                        <?php }?>
                    <?php if($tab_review && $display_reviews ){?>
                        <div class="rounded-pill bg-light-secondary badge fs-6">
                            <i class="bi bi-chat-left-dots"></i>
                            <a class="bg-light-secondary fs-6" href="javascript:void(0);" onclick="scrollToTab('review')"><?php echo $tab_review;?></a>
                        </div>
                    <?php }?>
                </div>
                <?php if($review_percentage && $display_reviews ){?>
                <p class="text-muted text-start mb-0 text-sm-end"><b class="text-success"><?php echo $review_percentage?>% </b><?php echo $review_percentage_translate; ?></p>
                <?php }?>
            </div>
            <hr class="my-4">
            <div class="col-md-12 d-flex flex-column">
                    <div class="quantitybox">
                        <?php if ($display_price) { echo $form['form_open']; ?>
                                <fieldset>
                                    <?php if ($options) {
                                            foreach ($options as $option) {
                                                $fldType = $option['html']->type;
                                                if ( $fldType == 'hidden') {
                                                    echo $option['html'];
                                                    continue;
                                                }?>
                                            <div class="d-block">
                                                <h5 class="text-muted control-label fw-bold mb-0">
                                                    <?php echo $option['name']; ?>
                                                </h5>
                                            </div>
                                            <div class="form-group mb-3 d-flex align-items-center">
                                                <?php
                                                    echo $this->getHookVar('product_option_'.$option['name'].'_additional_info');
                                                ?>
                                                <div class="prod-option <?php echo $fldType .' '.(in_array($fldType, ['input', 'textarea']) ? 'w-100' : '')?>">
                                                    <?php echo $option['html'];	?>
                                                </div>
                                            </div>
                                            <hr class="my-4">
                                        <?php } ?>
                                    <?php } ?>

                                    <?php echo $this->getHookVar('extended_product_options'); ?>

                                    <?php if ($discounts) { ?>
                                        <div class="form-group">
                                            <h5 class="text-muted"><?php echo $text_quantity_discount; ?></h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="h6">
                                                        <tr>
                                                            <th><?php echo $text_order_quantity; ?></th>
                                                            <th><?php echo $text_price_per_item; ?></th>
                                                        </tr>
                                                    </thead>
                                                <?php foreach ($discounts as $discount) { ?>
                                                    <tr>
                                                        <td><?php echo $discount['quantity']; ?></td>
                                                        <td class="text-primary fw-medium"><?php echo $discount['price']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                    <?php } ?>
                                        <div class="row align-items-center g-2 my-3">
                                            <div class="col-auto">
                                                <?php if(!$product_info['call_to_order']){ ?>
                                                    <div class="form-group d-inline-flex">
                                                        <h5 class="text-muted d-none"><?php echo $text_qty; ?></h5>
                                                        <div class="input-group d-flex flex-nowrap">
                                                        <button class="input-group-text minus-qnty btn btn-outline-danger fs-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="<?php $minimum > 1 ? echo_html2view($text_minimum) : '';?>">&minus;</button>
                                                        <?php
                                                        $form['minimum']->style .= " text-center fs-4 ";
                                                         ?>
                                                        <input type="text"
                                                               name="<?php echo $form['minimum']->name ?>"
                                                               id="<?php echo $form['minimum']->element_id ?>"
                                                               value="<?php echo $form['minimum']->value ?>"
                                                               inputmode="numeric"
                                                               placeholder="<?php echo $form['minimum']->placeholder ?>"
                                                               class="form-control text-center fw-bold <?php echo $form['minimum']->style; ?>"
                                                               min="<?php echo $minimum?:1; ?>"
                                                            <?php echo $maximum ? 'max="'.$maximum.'"' : '' ?>
                                                            <?php echo $form['minimum']->attr; ?>/>
                                                        <button class="input-group-text plus-qnty input-group-text btn btn-outline-success fs-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="<?php $maximum > 0 ? echo_html2view($text_maximum) : '';?>">&plus;</button>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                            </div>
                                            <div class="col-auto">
                                                <?php if(!$product_info['call_to_order']){ ?>
                                                    <h3 class="text-primary">
                                                        <small class="text-muted fw-normal"><?php echo $text_total_price; ?></small>
                                                        <span class="total-price mt-auto"><i class="ms-2 fa-solid fa-spinner fa-spin"></i></span>
                                                    </h3>
                                                    <div class="mt-auto"><?php echo $tax_message; ?></div>
                                                <?php }?>
                                            </div>
                                        </div>
                                    <div>
                                        <?php echo $form['product_id'] . $form['redirect']; ?>
                                    </div>

                                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between">
                                        <?php
                                        if(!$product_info['call_to_order']){
                                            if (!$can_buy) { ?>
                                                <div class="alert alert-warning my-2 no-stock-alert">
                                                    <label class="control-label"><?php echo $stock; ?></label>
                                                </div>
                                                <div class="col-9 "><?php echo $this->getHookVar('buttons'); ?></div>
                                        <?php
                                            } else { ?>
                                                <div class="product-page-add2cart">
                                                    <?php if(!$this->getHookVar('product_add_to_cart_html')) { ?>
                                                        <a id="product_add_to_cart" class="btn btn-outline-primary cart"
                                                        href="Javascript:void(0);">
                                                            <i class="bi bi-handbag"></i>
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
                                                <a href="<?php echo $this->html->getSeoUrl('content/contact');?>" class="cart btn btn-success">
                                                    <i class="bi bi-telephone"></i>&nbsp;&nbsp;
                                                    <?php echo $text_call_to_order; ?>
                                                </a>
                                            </div>
                                            <?php }
                                        } ?>
                                    </div>
                                    <?php
                                     if($product_info['free_shipping'] && $product_info['shipping_price'] <= 0) { ?>
                                         <div class="mt-3 mb-0">
                                             <div class="card-body p-0">
                                                 <ul class="list-group list-group-flush">
                                                     <li class="list-group-item border-0 px-0 pt-0">
                                                         <div class="d-flex align-items-center">
                                                             <div class="flex-shrink-0">
                                                                 <i class="bi bi-truck fs-4 text-danger me-2"></i>
                                                             </div>
                                                             <div class="flex-grow-1">
                                                                 <h5 class="m-0"><?php echo $text_free_shipping; ?></h5>
                                                             </div>
                                                         </div>
                                                     </li>
                                                 </ul>
                                             </div>
                                         </div>


                                     <?php } ?>
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
<section class="prod-desc mt-3 mt-lg-0">
    <ul id="productTabs" class="nav nav-tabs profile-tabs mb-4 border-bottom" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="description" data-bs-toggle="tab" href="#collapseDescription" role="tab" aria-controls="collapseDescription" aria-selected="true">
                <?php echo $tab_description; ?>
            </a>
        </li>
        <?php if ($display_reviews || $review_form_status){ ?>
                <?php if($review_form_status or $total_reviews>0){?>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="review" data-bs-toggle="tab" href="#collapseReview" role="tab" aria-controls="collapseReview" aria-selected="false" tabindex="-1" aria-selected="true">
                    <?php echo $tab_review; ?>
                </a>
            </li>
            <?php }
        }
        if ($tags){ ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tags" data-bs-toggle="tab" href="#collapseTags" role="tab" aria-controls="collapseTags" aria-selected="false" tabindex="-1">
                    <?php echo $text_tags; ?>
                </a>
            
            </li>
        <?php }
        if ($downloads){ ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="downloads" data-bs-toggle="tab" href="#collapseDownloads" role="tab" aria-controls="collapseDownloads" aria-selected="false" tabindex="-1">
                <?php echo $tab_downloads; ?>
            </a>
        </li>
        <?php }
        if ($this->getHookVar('product_features')){ ?>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="features" data-bs-toggle="tab" href="#collapseFeatures" role="tab" aria-controls="collapseFeatures" aria-selected="false" tabindex="-1">
                <?php echo $this->getHookVar('product_features_tab'); ?>
            </a>
        </li>
        <?php }
        $hookVarArray = $this->getHookVar('product_description_array');
        if( $hookVarArray ){
            foreach($hookVarArray as $key=>$hkVar){ ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="ecomtab-tab-4" data-bs-toggle="tab" href="#collapse<?php echo $key; ?>" role="tab" aria-controls="collapse<?php echo $key; ?>" aria-selected="false" tabindex="-1">  
                    <?php echo $hkVar['title']; ?>
                </a>
            </li>
        <?php }
        } ?>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active show" id="collapseDescription" role="tabpanel" aria-labelledby="description">
                <div class="tab-pane-body">
                    <?php echo $description; ?>
                    <ul class="productinfo list-unstyled">
                        <?php if ($stock){ ?>
                            <li>
                                <span class="fw-bold me-2"><?php echo $text_availability; ?></span> <?php echo $stock; ?>
                            </li>
                        <?php }
                        if ($model){ ?>
                            <li><span class="fw-bold me-2"><?php echo $text_model; ?></span> <?php echo $model; ?>
                            </li>
                        <?php }
                        if ($sku){ ?>
                            <li><span class="fw-bold me-2"><?php echo $text_sku; ?></span> <?php echo $sku; ?>
                            </li>
                        <?php }
                        if ($manufacturer){ ?>
                            <li class="d-flex align-items-center">
                                <span class="fw-bold me-2"><?php echo $text_manufacturer; ?></span>
                                <a href="<?php echo $manufacturers; ?>" class="d-inline-flex align-items-center" style="height: 17px;">
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
            </div>
        <!-- Description Tab Content Ends -->

        <?php if ($display_reviews || $review_form_status){ ?>
        <!-- Review Tab Content Starts -->
            <div class="tab-pane" id="collapseReview" role="tabpanel" aria-labelledby="review">
                <div class="tab-pane-body">
                    <div class="row">
                        <div class="col-xxl-8 col-md-10">
                            <?php if((float)$average) {?>
                                <?php if($display_reviews){?>
                                    <h4 class="fw-normal"><?php echo $feedback_customer_title;?></h4>
                                    <div class="row g-4 mb-4 justify-content-between align-items-stretch">
                                        <div class="col-xxl-4 col-xl-5">
                                            <div class="card h-100 text-center">
                                                <div class="card-body"><h2 class="mb-0"><b><?php echo $average; ?></b></h2>
                                                        <div class="d-flex align-items-center justify-content-center gap-2 text-warning my-3">
                                                        <div class="text-warning rating-stars text-sm-end">
                                                            <?php echo renderRatingStarsNv($average,''); ?>
                                                        </div></div>
                                                    <p class="mb-0 text-muted"><?php echo $product_rate_title; ?></p></div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-8 col-xl-7">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="w-100">
                                                            <?php echo renderProductRatingStars((int)$product_id);?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            <?php }?>
                            <h4 id="headingReview"><?php echo $review_title; ?></h4>

                            <div class="list-group list-group-flush">
                                <div id="current_reviews" class="mb-2"><?php echo $current_reviews;?></div>
                            </div>

                            <?php if($review_form_status){ ?>
                            <div class="heading" id="review_title"><h4><?php echo $write_review_title; ?></h4></div>
                            <fieldset>
                                <div class="mb-3">
                                    <div class="mb-3"><label class="form-label"><?php echo $entry_rating; ?></label>
                                        <div class="d-flex align-items-center gap-1 text-warning"><?php
                                            $rating_element->required = true;
                                            echo $rating_element; ?></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label"><?php echo $entry_name; ?></label>
                                        <?php $review_name->required = true;
                                        echo $review_name; ?>
                                    </div>
                                    <div class="mb-3"><label class="form-label"><?php echo $entry_review; ?></label>
                                        <div class="form-text mb-2"><?php echo $text_note; ?></div>
                                        <?php
                                        $review_text->required = true;
                                        echo $review_text; ?></div>
                                </div>
                                <?php
                                $review_button->style .= ' ms-auto text-nowrap mt-4';
                                if ($review_recaptcha){ ?>
                                    <div class="form-group mb-3 d-flex flex-wrap">
                                        <?php
                                        echo $review_recaptcha;
                                        echo $review_button;
                                        ?>
                                    </div>
                                <?php } else{ ?>
                                        <div class="form-group mb-3 d-flex flex-wrap">
                                            <?php
                                            echo $this->html->buildCaptcha(
                                                [
                                                    'name'        => 'captcha',
                                                    'required'    => true,
                                                    'captcha_url' => $captcha_url,
                                                    'placeholder' => $entry_captcha
                                                ]
                                            );
                                            echo $review_button; ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Review Tab Content Ends -->
        <?php }
        if ($tags){ ?>
        <!-- tags Tab Content Starts -->
            <div class="tab-pane" id="collapseTags" role="tabpanel" aria-labelledby="tags">
                <div class="tab-pane-body">
                <?php
                foreach ($tags as $tag){ ?>
                    <a class="badge bg-secondary" href="<?php echo $tag['href']; ?>">
                        <i class="fa-solid fa-hashtag"></i><?php echo $tag['tag']; ?>
                    </a>
                <?php } ?>
                </div>
            </div>
        <!-- tags Tab Content Ends -->
        <?php }
        if ($downloads){ ?>
        <!-- downloads Tab Content Starts -->
        <div class="tab-pane" id="collapseDownloads" role="tabpanel" aria-labelledby="downloads">
            <div class="tab-pane-body">
            <?php foreach ($downloads as $download){ ?>
                <li class="list-group-item d-flex justify-content-between align-items-center col-12">
                    <div class="fs-5 fw-bolder"><?php echo $download['name'];
                        if($download['attributes']){ ?>
                            <dl class="fs-6 fw-normal download-list-attributes ms-3 mt-2">
                                <?php foreach ($download['attributes'] as $name => $value){  ?>
                                    <dt class="fw-bold me-2"><?php echo $name; ?>:</dt>
                                    <dd class=" ms-5 text-secondary"><?php echo (is_array($value) ? implode(' ', $value) : $value); ?></dd>
                            <?php } ?>
                            </dl>
                        <?php } ?>
                    </div>
                    <a class="ms-auto text-nowrap btn btn-outline-dark"
                        href="<?php echo $download['button']->href; ?>">
                            <i class="fa-solid fa-download"></i> <?php echo $download['button']->text; ?>
                    </a>
                </li>
            <?php } ?>
            </div>
        </div>
        <!-- downloads Tab Content Ends -->
        <?php }
        if ($this->getHookVar('product_features')){ ?>
        <!-- features Products Tab Content Starts -->
            <div class="tab-pane" id="collapseFeatures" role="tabpanel" aria-labelledby="features">
                <div class="tab-pane-body">
                    <?php echo $this->getHookVar('product_features'); ?>
                </div>
            </div>
        <!-- features Products Tab Content Ends -->
        <?php }
        $hookVarArray = $this->getHookVar('product_description_array');
        if( $hookVarArray ){
            foreach($hookVarArray as $key=>$hkVar){ ?>
                <div class="tab-content">
                    <div class="tab-pane" id="ecomtab-3" role="tabpanel" aria-labelledby="ecomtab-tab-4">
                        <div class="tab-pane-body">
                            <?php echo $hkVar['html']; ?>
                        </div>
                    </div>
                </div>
        <?php }
        } ?>
    </div>
</section>

<?php if ($related_products){ ?>
<!-- Related Products Section Content Starts -->
    <div class="related_products-block">
        <div class="row title justify-content-center sec-heading-block text-center">
            <div class="col-xl-8">
                <h4><?php echo $tab_related; ?> (<?php echo sizeof((array)$related_products); ?>)</h4>
            </div>
        </div>
        <?php
            $products = $related_products;
            $imgW = $this->config->get('config_image_related_width');
            $imgH = $this->config->get('config_image_related_height');
            //use common template for all product grids
            include($this->templateResource('/template/blocks/product_cell_grid.tpl'));
        ?>

    </div>
<!-- Related Products Section Content Ends -->
<?php }?>

<script src="//cdn.jsdelivr.net/npm/drift-zoom@1.5.1/dist/Drift.min.js "></script>
<link href="//cdn.jsdelivr.net/npm/drift-zoom@1.5.1/dist/drift-basic.min.css " rel="stylesheet">
<script type="text/javascript">
    <?php if($this->config->get('config_google_analytics_code')){ ?>
    try {
        gtag("event", "view_item",
            {
                items: [{
                    item_name: <?php js_echo($heading_title);?>,
                    item_id: <?php echo (int)$product_info['product_id']; ?>,
                    price: $('#product_price_num') ? $('#product_price_num').val() :  0 ,
                    item_brand: <?php js_echo($manufacturer);?>,
                    quantity: <?php echo (int)$form['minimum']->value;?>
                }]
            }
        );
    } catch (e) {
    }
    <?php } ?>
    document.addEventListener('DOMContentLoaded', function load() {
        //waiting for jquery loaded!
        if (!window.jQuery) return setTimeout(load, 50);
        //jQuery-depended code
        $(document).ready(
            function(){
                let hash = location.hash;
                if(hash){
                    scrollToTab(hash.replace('#','').toLowerCase());
                }

                //pause carousel of main image
                $('#carouselProductImages').on(
                    'mouseover mouseout',
                    '.carousel-item.mainimage, .carousel-item.mainimage img',
                    function(e){
                        $('#carouselProductImages').carousel(
                            {
                                pause: e.type==='mouseover' ? "false" : "true"
                            }
                        );
                    }
                );
            }
        );

        initZoom();
        display_total_price();

        $('#product_add_to_cart').click(function (e) {
            e.preventDefault();
            ga_event_fire('add_to_cart');
            $('#product').submit();
        });
        $('#review_submit').click(function () {
            review();
        });

        //process clicks in review pagination
        $('#current_reviews').on('click', '.pagination a', function (e) {
            e.preventDefault();
<?php // note: if direct_url property is set in the pagination show it but works with ajax-url (data-url attribute) ?>
            const url = $(this).attr('data-url') ? $(this).attr('data-url') : $(this).attr('href');
            reload_review( url );
            $([document.documentElement, document.body]).animate(
                {
                    scrollTop: $("#headingReview").offset().top - 100
                },
                1000
            );
            return false;
        });

        /* Process images for product options */
        var $select = $('input[name^=\'option\'], select[name^=\'option\']');
        $select.on('change',function () {
            var valId = $(this).val();
            valId = this.type === 'checkbox' && $(this).attr('data-attribute-value-id') ? $(this).attr('data-attribute-value-id') : valId;
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
        // call change event for first option value
        // to refresh pictures for preselected options
        $select.first().change();

        function initZoom() {
            try {
                var paneContainer = document.querySelector('.zoom-pane');
                var elem = document.querySelectorAll('.zoom-trigger');
                for (var j = 0; j < elem.length; j++) {
                    new Drift(elem[j], {
                        paneContainer: paneContainer,
                        inlinePane: false,
                    });
                }
            }catch(e){
                console.log(e);
            }
        }

        function load_option_images(attribute_value_id, product_id) {
            let selected = {};
            let k = 0;
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

            const data = {
                attribute_value_id: attribute_value_id,
                product_id: product_id,
                selected_options: selected
            };
            const thumbnails = $('#carouselProductImages ul.thumbnails img');
            $.ajax({
                type: 'POST',
                url: '<?php echo $option_resources_url; ?>',
                data: data,
                dataType: 'json',
                beforeSend: function(){
                    thumbnails.addClass('spinner-grow text-light');
                },
                success: function (data) {
                    if (data.length === 0) {
                        thumbnails.removeClass('spinner-grow text-light');
                        return false;
                    }

                    let mainPicHtml = '', smallPicsHtml = '';
                    for (let key in data.images) {
                        let image = data.images[key];
                        if(image.length == 0 ){ continue; }
                        let active = parseInt(key) === 0 ? 'active' : '';
                        mainPicHtml += '<div class="carousel-item ' + active + '">';
                        smallPicsHtml += '<li data-bs-target="#carouselProductImages" data-bs-slide-to="' + key +
                            '" class="w-25 h-auto ' + active + '">';

                        if (image.origin === 'external') {
                            mainPicHtml += '<div class="html_with_image">' + image.main_html + '</div>';
                            smallPicsHtml += '<div class="html_with_image">' + image.main_html + '</div>';
                        } else {

                            mainPicHtml += '<style>' +
                            '@media (min-width: 577px) {'+
                            '.imgc'+key+' {'+
                                    'width: '+image.thumb2_width+'px;'+
                                    'height: '+image.thumb2_height+'px;'+
                                '}'+
                            '}'+
                            '</style>';
                            mainPicHtml += '<img class="zoom-trigger d-block w-auto mx-auto product-main-img imgc'+key+'" ' +
                                'data-zoom="'+image.main_url+'" ' +
                                'alt="'+escapeHtml(image.title)+'" ' +
                                'title="'+escapeHtml(image.description)+'" ' +
                                'src="' + image.thumb2_url + '"/>';

                            smallPicsHtml += '<img class="d-block wid-100 rounded" ' +
                                'style="width: '+image.thumb_width+'px; height: '+image.thumb_height+'px;" ' +
                                'src="' + image.thumb_url + '" ' +
                                'alt="'+escapeHtml(image.title)+'" ' +
                                'title="'+escapeHtml(image.description)+'"/>';
                        }
                        mainPicHtml += '</div>';
                        smallPicsHtml += '</li>';
                    }

                    $('#carouselProductImages .carousel-inner').html(mainPicHtml)
                    $('#carouselProductImages ul.thumbnails').html(smallPicsHtml);
                    initZoom();
                    thumbnails.removeClass('spinner-grow text-light');
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
                        if( $('product_price_num') ){
                            $('#product_price_num').val( data.raw_price_num);
                            $('#product_total_num').val( data.raw_total_num);
                        }
                    }
                }
            });
        }

        function reload_review(url) {
            $('#current_reviews').load(url);
        }

        function review() {
            var dismiss = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

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
                    + '&rating=' + encodeURIComponent($('input[name=\'rating\']').val() ? $('input[name=\'rating\']').val() : '') + captcha,
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
                    resetLockedButton( $('button#review_submit') );
                },
                error: function (jqXHR, exception) {
                    var text = jqXHR.statusText + ": " + jqXHR.responseText;
                    $('#review .alert').remove();
                    $('#review_title').after('<div class="alert alert-danger alert-dismissible">' + text + dismiss +'</div>');
                },
                success: function (data) {
                    if (data.error) {
                        $('#review .alert').remove();
                        $('#review_title').after('<div class="alert alert-danger alert-dismissible">' + data.error + dismiss + '</div>');
                    } else {
                        $('#review .alert').remove();
                        $('#review_title').after('<div class="alert alert-success alert-dismissible">' + data.success + dismiss + '</div>');

                        $('input[name=\'name\']').val('');
                        $('textarea[name=\'text\']').val('');
                        $('input[name=\'rating\']:checked').attr('checked', '');
                    }
                    $('input[name=\'captcha\']').val('');
                    $('img[alt=captcha]').attr('src', $('img[alt=captcha]').attr('src') + '&' + Math.random());
                }
            });
        }

        $(document).on('click','#wishlist_add', function(e) {
            e.preventDefault();
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
                        ga_event_fire("add_to_wishlist");
                    }
                }
            });
        });

        $(document).on('click','#wishlist_remove', function(e) {
            e.preventDefault();
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
        });

        //Google Analytics 4
        function ga_event_fire(evtName){
            if(!ga4_enabled){
                console.log('google analytics data collection is disabled')
                return;
            }

            let card = $('.product-page-preset-box');
            let prodName = card.find('h1').text();
            gtag("event", evtName, {
                currency: default_currency,
                value: $('#product_total_num') ? $('#product_total_num').val() :  0 ,
                items: [
                    {
                        item_id: <?php echo (int)$product_info['product_id']; ?>,
                        item_name: prodName.trim(),
                        affiliation: storeName,
                        price: $('#product_price_num') ? $('#product_price_num').val() :  0 ,
                        quantity: $('#product_quantity').val()
                    }
                ]
            });
        }
    });
</script>